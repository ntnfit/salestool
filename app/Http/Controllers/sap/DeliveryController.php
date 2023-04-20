<?php

namespace App\Http\Controllers\sap;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\sap\SAPB1Controller;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Visibility;
class DeliveryController extends Controller
{
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
        if (!empty($request->TruckCode) && !empty($request->No))
        {
            foreach($request->No as $no)
            {
                $sql='update ORDR set "U_TruckInfo"=? Where "DocNum"=?';       
                $stmt = odbc_prepare($conDB, $sql);
                odbc_execute($stmt,array($request->TruckCode,$no));

            }
            odbc_close($conDB);
            
        }
       
        return response()->json(["success" => true,"data"=>"okay"]);

    }
   
}
