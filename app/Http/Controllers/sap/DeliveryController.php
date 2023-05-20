<?php

namespace App\Http\Controllers\sap;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\sap\SAPB1Controller;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use League\Flysystem\Visibility;
class DeliveryController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:logistic-module');
         
    }
    public function index()
    {
        return view('delivery.index');
    }
    public function store(Request $request)
    { 
        $files = $request->file('file');
      
        $attachments = []; 
        if ($request->hasFile('file')) {

            foreach ($files as $file) {
             
                $namefile = pathinfo($file->getClientOriginalName(),PATHINFO_FILENAME);
               
                $extension = $file->getClientOriginalExtension();
                $fileName = $namefile ."_". time() . '.' . $extension;

                $destinationPath = 'C:\b1_shr\attachment'; // Specify the target folder

                // Replace '\' with '\\' in the target folder path to avoid any escape sequence issues
                $destinationPath = str_replace('\\', '\\\\', $destinationPath);

                $file->move($destinationPath, $fileName);           
                $attachment = [
                    "FileExtension" => $extension,
                    "FileName" => pathinfo($fileName,PATHINFO_FILENAME),
                    "SourcePath" => env('pathuploadSAP'),
                    "UserID" => "1"
                ];
                
                array_push($attachments, $attachment);
                
                $payload = [
                    "Attachments2_Lines" => $attachments
                ];
                
            }
            
        }
    
        $entry=$this->attachSAP($payload);
        $this->updatestatus($entry,$request->DocKey);
      
        
       return  redirect()->back()->with('message', 'update sucesss!');
    
    }
    function destroy (Request $request)
    {
        
        
        $destinationPath =base_path() . '/' .'uploads/delivery/';
       
        if(file_exists($destinationPath.$request->id))
        {
           unlink($destinationPath.$request->id);
        } 
           
            
    }
    function updatestatus(string $entry, string $so)
    {
        $serviceLayerUrl = "https://".env('SAP_SERVER').":".env('SAP_PORT');
        //$serviceLayerUrl = "https://crm-grantthornton.xyz".":".env('SAP_PORT');
        $headers = [
            'Content-Type' => 'text/plain',
            "Authorization" => "Basic " . env('BSHeader'),
           // "Authorization" => "Basic " . "eyJDb21wYW55REIiOiIwMV9CVEdfU0FQX0xJVkUiLCJVc2VyTmFtZSI6Im1hbmFnZXIifTptYW5hZ2Vy",
        ];

        $client = new \GuzzleHttp\Client([
            "base_uri" => $serviceLayerUrl,
            "headers" => $headers,
        ]);
        $payload = [
            "AttachmentEntry" =>$entry,
            "U_DeliveryStatus"=>'02'
        ];
        // Make a request to the service layer
        $response = $client->request("patch", "/b1s/v1/Orders(".$so.")", [
            'verify' => false,
            'json' => $payload
        ]);
    
   
    }

    public function attachSAP(array $payload)
    {
        $serviceLayerUrl = "https://".env('SAP_SERVER').":".env('SAP_PORT');
        //$serviceLayerUrl = "https://crm-grantthornton.xyz".":".env('SAP_PORT');
        $headers = [
            'Content-Type' => 'multipart/mixed',
            "Authorization" => "Basic " . env('BSHeader'),
            //"Authorization" => "Basic " . "eyJDb21wYW55REIiOiIwMV9CVEdfU0FQX0xJVkUiLCJVc2VyTmFtZSI6Im1hbmFnZXIifTptYW5hZ2Vy",
        ];

        $client = new \GuzzleHttp\Client([
            "base_uri" => $serviceLayerUrl,
            "headers" => $headers,
        ]);
        
        // Make a request to the service layer
        $response = $client->request("POST", "/b1s/v1/Attachments2", [
            'verify' => false,
            'json' => $payload
        ]);
    
        // Get the response body as a string
        $responseBody = $response->getBody()->getContents();
        $responseJson = json_decode($responseBody, true);
        $absoluteEntry = $responseJson["AbsoluteEntry"];
        
        return $absoluteEntry;
    }

    function TruckInfor(Request $request){
        $conDB = (new SAPB1Controller)->connect_sap();
        $sql='call "USP_BS_TRUCKINFOMATION_DOUPPLICATE" (?,?,?)';
        $stmt = odbc_prepare($conDB, $sql);
        odbc_execute($stmt,array($request->fromDate,$request->toDate,1));
        $results = array();
        while ($row = odbc_fetch_object($stmt)) {
            $results[] = $row;
        }
        odbc_close($conDB);
        return json_encode($results);

    }
    function truckview()
    {
        $conDB = (new SAPB1Controller)->connect_sap();
        $sql='select * from "@BS_TRUCKINFO" Where "U_Status"=?';
        $stmt = odbc_prepare($conDB, $sql);
        odbc_execute($stmt,array('A'));
        $results = array();
        while ($row = odbc_fetch_object($stmt)) {
            $results[] = $row;
        };
        odbc_close($conDB);
        return view ('logistic.truckinfo',compact('results'));
    }

    function TruckApply(Request $request){
       
        $conDB = (new SAPB1Controller)->connect_sap();
        if (!empty($request->No))
        {
            foreach($request->No as $no)
            {
               
               
                if( $no['DocType']=='IT')
                {
                    $sql='update OWTQ set "U_TruckInfo"=? Where "DocNum"=?';  
                }
                else {
                    $sql='update ORDR set "U_TruckInfo"=? Where "DocNum"=?';  
                }
                    
                $stmt = odbc_prepare($conDB, $sql);
                odbc_execute($stmt,array($request->TruckCode,$no['DocNum']));

            }
            odbc_close($conDB);
            
        }
       
        return response()->json(["success" => true,"data"=>"okay"]);

    }
    function TruckLockView()
    {
        $conDB = (new SAPB1Controller)->connect_sap();
        $sql='select *,Case when "U_Status"=\'A\' then \'UnLock\'
        else \'Lock\' end statusvh from "@BS_TRUCKINFO"';
        $stmt = odbc_prepare($conDB, $sql);
        odbc_execute($stmt);
        $results = array();
        while ($row = odbc_fetch_object($stmt)) {
            $results[] = $row;
        };
        $results=json_encode( $results);
        odbc_close($conDB);
        return view ('logistic.lock',compact('results'));

    }
    function TruckLock(Request $request){
        $conDB = (new SAPB1Controller)->connect_sap();
        if (!empty($request->TruckCode))
        {
            if($request->type=="L")
            {
                foreach($request->TruckCode as $no)
                {
                    $sql='update "@BS_TRUCKINFO" set "U_Status"=? Where "Code"=?';       
                    $stmt = odbc_prepare($conDB, $sql);
                    odbc_execute($stmt,array('L',$no));
    
                };
            }
           else
           {
                foreach($request->TruckCode as $no)
                {
                    $sql='update "@BS_TRUCKINFO" set "U_Status"=? Where "Code"=?';       
                    $stmt = odbc_prepare($conDB, $sql);
                    odbc_execute($stmt,array('A',$no));

                };
           }


            odbc_close($conDB);
            
        }
       
        return response()->json(["success" => true,"data"=>"okay"]);

    }
    function UpdateDriver(Request $request)
    {
        $conDB = (new SAPB1Controller)->connect_sap();
        if (!empty($request->No))
        {
                foreach($request->No as $no)
                {
                    $sql='update ORDR set "U_TruckInfo"=? Where "DocNum"=?';       
                    $stmt = odbc_prepare($conDB, $sql);
                    odbc_execute($stmt,array('L',$no));
    
                };
        }
        odbc_close($conDB);
        return response()->json(["success" => true,"data"=>"okay"]);
    }
    function SoNotPrint()
    {
        $conDB = (new SAPB1Controller)->connect_sap();
        $sql='call USP_BS_PrintInvoice_NotPrint';
        $stmt = odbc_prepare($conDB, $sql);
        odbc_execute($stmt);
        $results = array();
        while ($row = odbc_fetch_object($stmt)) {
            $results[] = $row;
        };
        $results=json_encode( $results);
        odbc_close($conDB);
        return view ('logistic.printDoNote',compact('results'));
    }
    function updatePrinted()
    {
        $conDB = (new SAPB1Controller)->connect_sap();
        if (!empty($request->No))
        {
                foreach($request->No as $no)
                {
                    $sql='update ORDR set "Printed"=? Where "DocNum"=?';       
                    $stmt = odbc_prepare($conDB, $sql);
                    odbc_execute($stmt,array('N',$no));
    
                };
        }
        odbc_close($conDB);
        return response()->json(["success" => true,"data"=>"okay"]);
    }
    function PrintLayoutDO(Request $request)
    {
        $conDB = (new SAPB1Controller)->connect_sap();
        $results = array();
        $layout=$request->layout;
        if ($layout=="vin") {
           
            
            $sql='call "usp_NNHD_PhieuGiaoHang_Delivery_VIN"(?)';
            $stmt = odbc_prepare($conDB, $sql);
            odbc_execute($stmt,array($request->so));
            
            while ($row = odbc_fetch_object($stmt)) {
                $results[] = $row;
            };
            sort($results);
        $groupedDocuments=collect($results)->groupBy('DocEntry');
        return view ('layoutsap.vin',compact('groupedDocuments'));
           
        }
        else if ($layout=="ck")
        {
            $sql='call "usp_NNHD_PhieuGiaoHang_Delivery"(?)';
            $stmt = odbc_prepare($conDB, $sql);
            odbc_execute($stmt,array($request->so));
            while ($row = odbc_fetch_object($stmt)) {
                $results[] = $row;
            };
            sort($results);
            $groupedDocuments=collect($results)->groupBy('DocEntry');
            return view ('layoutsap.ck',compact('groupedDocuments'));
        }
        else if ($layout=="aeon")
        {
            $sql='call "usp_NNHD_PhieuGiaoHang_Delivery"(?)';
            $stmt = odbc_prepare($conDB, $sql);
            odbc_execute($stmt,array($request->so));
            while ($row = odbc_fetch_object($stmt)) {
                $results[] = $row;
            };
            sort($results);
            $groupedDocuments=collect($results)->groupBy('DocEntry');
            return view ('layoutsap.aeon',compact('groupedDocuments'));
        }
        else if ($layout=="aeonkm")
        {
            $sql='call "usp_NNHD_PhieuGiaoHang_Delivery_DIS"(?)';
            $stmt = odbc_prepare($conDB, $sql);
            odbc_execute($stmt,array($request->so));
            while ($row = odbc_fetch_object($stmt)) {
                $results[] = $row;
            };
            sort($results);
            $groupedDocuments=collect($results)->groupBy('DocEntry');
            return view ('layoutsap.aeonkm',compact('groupedDocuments'));
        }
        else if ($layout=="lotte")
        {
            $sql='call "usp_NNHD_PhieuGiaoHang_Delivery"(?)';
            $stmt = odbc_prepare($conDB, $sql);
            odbc_execute($stmt,array($request->so));
            while ($row = odbc_fetch_object($stmt)) {
                $results[] = $row;
            };
            sort($results);
            $groupedDocuments=collect($results)->groupBy('DocEntry');
            return view ('layoutsap.lotte',compact('groupedDocuments'));
        }
        else if ($layout=="lottekm")
        {
            $sql='call "usp_NNHD_PhieuGiaoHang_Delivery_DIS"(?)';
            $stmt = odbc_prepare($conDB, $sql);
            odbc_execute($stmt,array($request->so));
            while ($row = odbc_fetch_object($stmt)) {
                $results[] = $row;
            };
            sort($results);
            $groupedDocuments=collect($results)->groupBy('DocEntry');
            return view ('layoutsap.lottekm',compact('groupedDocuments'));

        }
        else if ($layout=="metro")
        {
            $sql='call "usp_NNHD_PhieuGiaoHang_Delivery"(?)';
            $stmt = odbc_prepare($conDB, $sql);
            odbc_execute($stmt,array($request->so));
            while ($row = odbc_fetch_object($stmt)) {
                $results[] = $row;
            };
            sort($results);
            $groupedDocuments=collect($results)->groupBy('DocEntry');
            return view ('layoutsap.metro',compact('groupedDocuments'));
        }
        else if ($layout=="metrokm")
        {
            $sql='call "usp_NNHD_PhieuGiaoHang_Delivery_DIS"(?)';
            $stmt = odbc_prepare($conDB, $sql);
            odbc_execute($stmt,array($request->so));
            while ($row = odbc_fetch_object($stmt)) {
                $results[] = $row;
            };
            sort($results);
            $groupedDocuments=collect($results)->groupBy('DocEntry');
            return view ('layoutsap.metrokm',compact('groupedDocuments'));

        }
        else if ($layout=="betagenkm")
        {
            $sql='call "usp_NNHD_PhieuGiaoHang_Delivery_DIS"(?)';
            $stmt = odbc_prepare($conDB, $sql);
            odbc_execute($stmt,array($request->so));
            while ($row = odbc_fetch_object($stmt)) {
                $results[] = $row;
            };
            sort($results);
        }
        else {
            //layout Betagen
            $sql='call "usp_NNHD_PhieuGiaoHang_Delivery"(?)';
            $stmt = odbc_prepare($conDB, $sql);
            odbc_execute($stmt,array($request->so));
            while ($row = odbc_fetch_object($stmt)) {
                $results[] = $row;
            };
            sort($results);
            
            $groupedDocuments=collect($results)->groupBy('DocEntry');
            return view ('layoutsap.betagen',compact('groupedDocuments'));
          }

          odbc_close($conDB);
    }
    function removeDo(Request $request){
        $conDB = (new SAPB1Controller)->connect_sap();
        foreach($request->dataNo as $data)
        {
                   
            if( $data['TypeName']=="IT")
            {
                $upate='update OWTQ SET "U_TruckInfo"=?,"U_DelNo"=? where "DocNum"=? ';
                $stmt = odbc_prepare($conDB, $upate);
                odbc_execute($stmt,array(null,$data['U_DelNo'],$data['DocNum']));
            }
            else
            {
                $upate='update ORDR SET "U_TruckInfo"=?,"U_DelNo"=? where "DocNum"=? ';
                $stmt = odbc_prepare($conDB, $upate);
                odbc_execute($stmt,array(null,$data['U_DelNo'],$data['DocNum']));
            }
            
        }
        odbc_close($conDB);
        return response()->json(["success" => true,"data"=>"okay"]);

    }
}
