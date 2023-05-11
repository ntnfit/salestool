<?php

namespace App\Http\Controllers\sap;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\sap\SAPB1Controller;
use Illuminate\Validation\Rule;
use DB;
use Response;
use Carbon\Carbon;
use Auth;
class InvController extends Controller
{
    function addview()
    {
        $whsCodes=DB::table('SAL_OWHS')->get();
        
        return view('inv.add',compact('whsCodes'));
    }
    function listInvStock(Request $request)
    {

        if(!$request->fromdate ||!$request->todate )
        {
            $fromDate = date("Ymd");
            $toDate = date("Ymd");
             // Connect to SAPB1 using ODBC
           $conDB = (new SAPB1Controller)->connect_sap();
           if (!$conDB) {
               // Handle connection error
               die("Error connecting to SAPB1: " . odbc_errormsg());
           }

           // Prepare the SQL statement
           $sql = 'call USP_BS_STOCKOUTREQUEST(?,?,?,?)';
           $stmt = odbc_prepare($conDB, $sql);
           if (!$stmt) {
               // Handle SQL error
               die("Error preparing SQL statement: " . odbc_errormsg());
           }

           // Execute the stored procedure with the input parameters
           if (!odbc_execute($stmt, array(1, $fromDate, $toDate,1))) {
               // Handle execution error
               die("Error executing SQL statement: " . odbc_errormsg());
           }

           $results = array();
           // Check if there are any results
           if (odbc_num_rows($stmt) == 0) {
               $results = [];
           }

           // Retrieve the result set from the stored procedure
           $results = array();
           while ($row = odbc_fetch_array($stmt)) {
               $results[] = $row;
           }
           $results=json_encode($results);
           return view('inv.list',compact('results'));
            
        }
        else
        {
           $fromDate = Carbon::createFromFormat('d/m/Y', $request->fromdate)->format('Ymd');
           $toDate =Carbon::createFromFormat('d/m/Y', $request->todate)->format('Ymd');
          
           $conDB = (new SAPB1Controller)->connect_sap();
           if (!$conDB) {
               // Handle connection error
               die("Error connecting to SAPB1: " . odbc_errormsg());
           }

           // Prepare the SQL statement
           $sql = 'CALL USP_BS_STOCKOUTREQUEST(?,?,?,?)';
           $stmt = odbc_prepare($conDB, $sql);
           if (!$stmt) {
               // Handle SQL error
               die("Error preparing SQL statement: " . odbc_errormsg());
           }

           // Execute the stored procedure with the input parameters
           if (!odbc_execute($stmt, array(1, $fromDate, $toDate, 1))) {
               // Handle execution error
               die("Error executing SQL statement: " . odbc_errormsg());
           }

           $results = array();
           // Check if there are any results
           if (odbc_num_rows($stmt) == 0) {
               $results = [];
           }

           // Retrieve the result set from the stored procedure
           $results = array();
           while ($row = odbc_fetch_array($stmt)) {
               $results[] = $row;
           }
           $results=json_encode($results);
           odbc_close($conDB);
           return  $results;
        }
          
    }
    function loaddata(Request $request)
    {
        
        $so='IT'.substr(date("Y"), -2).date("m").'99999999999999';
        $conDB = (new SAPB1Controller)->connect_sap();
        if (!$conDB) {
            // Handle connection error
            die("Error connecting to SAPB1: " . odbc_errormsg());
        }     
        $sql = 'CALL USP_BS_LOT_OINM(?,?,?)';

        $stmt = odbc_prepare($conDB, $sql);
          
        if (!$stmt) {
            // Handle SQL error
            die("Error preparing SQL statement: " . odbc_errormsg());
        }
        // Execute the stored procedure with the input parameters
        if (!odbc_execute($stmt, array($request->whscode,$so,$request->team))) {
            // Handle execution error
            die("Error executing SQL statement: " . odbc_errormsg());
        }
        $results = array();
        // Check if there are any results
        if (odbc_num_rows($stmt) == 0) {
            $results = [];
        }

        // Retrieve the result set from the stored procedure
        $results = array();
        while ($row = odbc_fetch_array($stmt)) {
            $results[] = $row;
        }
        sort($results);
        $blanket=0;
        // get number lot
        $distinctLots = array_unique(array_column($results, 'LotNo'));
        $ordertype=$request->ordertype;
        // pass data to the view and render the Blade template
        $tableHtml = view('inv.tabletemplate', compact('distinctLots', 'results','ordertype','blanket'))->render();
        odbc_close($conDB);
         return  $tableHtml;
    }
    function store(Request $request)
    {
        
            $rules = [
                'stockOuts' => [
                    function ($attribute, $value, $fail) {
                        $foundNonZeroValue = false;
                        foreach ($value as $i => $innerArray) {
                            foreach ($innerArray as $j => $innerArray2) {
                                foreach ($innerArray2 as $k => $val) {
                                    if ($val !== null && $val !== "0") {
                                        $foundNonZeroValue = true;
                                        break 3;
                                    }
                                }
                            }
                        }
                        if (!$foundNonZeroValue) {
                            $fail('The stockOuts field must have at least one value that is not 0 or null.');
                        }
                    }
                ],
                // Add any other validation rules you need
            ];

            $validatedData = $request->validate($rules);
        // next pass
          // OPEN connect ODBC
        $conDB =(new SAPB1Controller)->connect_sap();
        $date=Carbon::createFromFormat('d/m/Y', $request->date)->format('Ymd');
        $sqlStockNo = "SELECT 
        CASE 
            WHEN 9 >= NO THEN '0000'||CAST(NO AS NVARCHAR(20)) 
            WHEN 99 >= NO AND NO > 9 THEN '000'||CAST(NO AS NVARCHAR(20))
            WHEN 999 >= NO AND NO > 99 THEN '00'||CAST(NO AS NVARCHAR(20))
            WHEN 9999 >= NO AND NO > 999 THEN '0'||CAST(NO AS NVARCHAR(20))
            WHEN 99999 >= NO AND NO > 9999 THEN CAST(NO AS NVARCHAR(20))
            WHEN NO>99999 THEN CAST(NO AS NVARCHAR(20)) END NO      
            FROM 
                (
                    SELECT 
                        ifnull(COUNT(\"StockNo\"),0)+1 AS NO 
                    FROM 
                        \"BS_STOCKOUTREQUEST\" 
                    WHERE   YEAR(\"StockDate\") = YEAR('".$date."') 
                        AND MONTH(\"StockDate\") = MONTH('".$date."') 
                        
                ) T0";
               // AND \"OrderType\" = '".$ordertype."'
        $prefix="IT";
        if($request->WhsCode==$request->toWhsCode)    
        {
            $prefix="BO";
        }  
       
        $SOID = $prefix.date("ym", strtotime($date)).odbc_result(odbc_exec($conDB, $sqlStockNo),1);
       
        $Stocktype=1;
        
        $FromWhsCode=$request->WhsCode;
        $FromWhsName=$request->frmwhsname;
        $ToWhsCode=$request->toWhsCode;
        $ToWhsName=$request->towhsname;
       
       
        $AbsEntry=$request->bincode;
        $team=$request->teams;
        $AbsEntry1=$request->tobincode;
        $team1=$request->toteams;

        $note=$request->note;
        $statusSAP=0;
        $userId = Auth::user()->UserID;
        $applysap=0;
        $datecreate=date("Ymd", strtotime(date("Y/m/d")));
        $insertHeader='insert into "BS_STOCKOUTREQUEST" 
        ("StockNo","StockDate","StockType",
        "FromWhsCode","FromWhsName", "ToWhsCode","ToWhsName",
        "AbsEntry","BinCode","AbsEntry1","BinCode1","Note","StatusSAP","DateCreate",
        "UserID","ApplyStatus")
        values
        (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
         $stmtsheader = odbc_prepare($conDB, $insertHeader);
        $result = odbc_execute($stmtsheader, array($SOID,$date,$Stocktype,$FromWhsCode,$FromWhsName, $ToWhsCode,$ToWhsName,
        $AbsEntry,$team, $AbsEntry1,$team1,$note,$statusSAP,$datecreate,$userId,$applysap));
        //detail
        $Itempost=[];
        foreach ($request->stockOuts as $item => $lots) {
            $newLots = [];
        
            foreach ($lots as $lot => $data) {
                if ($data[0] !== null && $data[0] !== 0) {
                    $newLots[$lot] = array(
                        'Quantity' => $data[0]
                    );
                }
            }
        
            if (!empty($newLots)) {
                foreach ($newLots as $lotKey => $lotValue) {
                    if ($lotValue['Quantity'] !== null && $lotValue['Quantity'] !== "0") {
                        $Itempost[] = array(
                            'Item' => $item,
                            'lot' => $lotKey,
                            'Quantity' => $lotValue['Quantity'],
                           
                        );
                    }
                }
            }
        }       

        


         //insert data item to table line
         $sqlinsertline='insert into BS_STOCKOUTREQUEST_Detail 
         ("StockNo","ItemCode","LotNo","TypeOfPrd","Quantity","CreatedDate") values(?,?,?,?,?,?)';
         foreach ($Itempost as $item)
         {
            
         $stmtline = odbc_prepare($conDB, $sqlinsertline);
         $result = odbc_execute($stmtline, array($SOID,$item['Item'],$item['lot'],01,$item['Quantity'], $datecreate));
 
         }
         //update itemname in line 
        $sqlupdate='call "SAL_UPDATE_ITM_NAME"'; 
        odbc_exec($conDB, $sqlupdate);
        odbc_close($conDB);

        return  redirect()->route('inv.add')->with('message', 'add sucesss!');
    }

    function applysap(Request $request)
    {
        $serviceLayerUrl = "https://".env('SAP_SERVER').":".env('SAP_PORT');
      
        $headers = [
            "Content-Type" => "application/json",
            "Accept" => "application/json",
            "Authorization" => "Basic " . env('BSHeader'),
        ];

        $client = new \GuzzleHttp\Client([
            "base_uri" => $serviceLayerUrl,
            "headers" => $headers,
        ]);
        // json data
        $attachment=[];

        $conDB = (new SAPB1Controller)->connect_sap();
        foreach($request->SoNo as $SoNo)
        {
            $sql = 'select * from BS_STOCKOUTREQUEST where "StatusSAP"=0 and "StockNo"=?';
            $stmt = odbc_prepare($conDB, $sql);
            odbc_execute($stmt, array($SoNo));
            $results = array();
            while ($row = odbc_fetch_array($stmt)) {
                $results[] = $row;
            };
          
            $sql = 'select t1.* from BS_STOCKOUTREQUEST t0 join BS_STOCKOUTREQUEST_Detail t1
            on t0."StockNo"=t1."StockNo"
             where "Quantity"<>0 and  t0."StatusSAP"=0 and t0."StockNo"=?';
            $stmt = odbc_prepare($conDB, $sql);
            odbc_execute($stmt,array($SoNo));
            $line = array();
            while ($row = odbc_fetch_object($stmt)) {
                $line[] = $row;
            }
            
            $ldt=[];
            foreach ($line as $dt)
            {
                $km="";
                if($dt->TypePrd=="002")
                {
                    $km="1";
                }
                else
                {
                    $km="0";
                }
                $ldt[]=[
                    "ItemCode"=> $dt->ItemCode,
                    "Quantity"=> $dt->Quantity,
                    "U_BatchNo" => $dt->LotNo
                ];
            }
            $body=[
                "Comments"=>"Apply from salesHub ".$results[0]['Note']." ".$results[0]['StockNo'],
                "U_SoPhieu"=>$SoNo,
                "FromWarehouse"=> $results[0]['FromWhsCode'],
                "ToWarehouse"=> $results[0]['ToWhsCode'],
                "U_FromBIN"=> $results[0]['AbsEntry'],
                "U_FromBinCode"=> $results[0]['BinCode'],
                "U_TOBIN"=> $results[0]['AbsEntry1'],
                "U_ToBinCode"=> $results[0]['BinCode1'],
                "StockTransferLines"=>$ldt
                
            ];
           
            
             // Make a request to the service layer
        $response = $client->request("POST", "/b1s/v1/InventoryTransferRequests",['verify' => false, 'body' =>  json_encode($body)]);

          $sql = 'Update  BS_STOCKOUTREQUEST set "StatusSAP"=1,"ApplyStatus"=-1 where "StockNo"=?';
            $stmt = odbc_prepare($conDB, $sql);
            odbc_execute($stmt, array($SoNo));
        }
        odbc_close($conDB); 
        return response()->json(["success" => true,"data"=>"okay"]);
    }
    function Cancel(Request $request)
    {
        $conDB = (new SAPB1Controller)->connect_sap();
        foreach($request->SoNo as $SoNo)
        {
            $sql = 'update  BS_STOCKOUTREQUEST set "Note"=?, "Canceled"=? where "StockNo"=?';
            $stmt = odbc_prepare($conDB, $sql);
            odbc_execute($stmt, array('Cancled from SALEHUBS','C',$SoNo));
        }
        odbc_close($conDB);
        return response()->json(["success" => true]);
    }
    function edit($id)
    {
       
        $conDB = (new SAPB1Controller)->connect_sap();
        if (!$conDB) {
            // Handle connection error
            die("Error connecting to SAPB1: " . odbc_errormsg());
        }     
        // get data pass to pramater
        $so=DB::TABLE('SAL_LIST_STOCK_REQUEST')->where('StockNo',$id)->first();
     
        $sql = 'CALL USP_BS_LOT_OINM(?,?,?)';
        $stmt = odbc_prepare($conDB, $sql);
        if (!$stmt) {
            // Handle SQL error
            die("Error preparing SQL statement: " . odbc_errormsg());
        }
        // Execute the stored procedure with the input parameters
        if (!odbc_execute($stmt, array($so->FromWhsCode,$id,$so->AbsEntry))) {
            // Handle execution error
            die("Error executing SQL statement: " . odbc_errormsg());
        }

        // Retrieve the result set from the stored procedure
        $results = array();
        while ($row = odbc_fetch_array($stmt)) {
            $results[] = $row;
        }
        // get number lot
        $distinctLots = array_unique(array_column($results, 'LotNo'));
       
        odbc_close($conDB);
        
        return view('inv.edit',compact('results','distinctLots','so'));
    }
    function update(Request $request,$id)
    {

      
        $conDB = (new SAPB1Controller)->connect_sap();
        if($request->sono)
        {
           
            $sqldete='delete from "BS_STOCKOUTREQUEST" where "StockNo"=\''.$id.'\'';
           
            odbc_exec($conDB,$sqldete);          
            $sqldeteline='delete from BS_STOCKOUTREQUEST_Detail where "StockNo"=\''.$id.'\'';
            odbc_exec($conDB,$sqldeteline);
        }
     
      //dd($Itempost);
        // post to header
        $date=Carbon::createFromFormat('d/m/Y', $request->date)->format('Ymd');
        $SOID =$request->sono;
        $Stocktype=1;
        
        $FromWhsCode=$request->WhsCode;
        $FromWhsName=$request->frmwhsname;
        $ToWhsCode=$request->toWhsCode;
        $ToWhsName=$request->towhsname;
       
       
        $AbsEntry=$request->bincode;
        $team=$request->teams;
        $AbsEntry1=$request->tobincode;
        $team1=$request->toteams;

        $note=$request->note;
        $statusSAP=0;
        $userId = Auth::user()->UserID;
        $applysap=0;
        $datecreate=date("Ymd", strtotime(date("Y/m/d")));
        $insertHeader='insert into "BS_STOCKOUTREQUEST" 
        ("StockNo","StockDate","StockType",
        "FromWhsCode","FromWhsName", "ToWhsCode","ToWhsName",
        "AbsEntry","BinCode","AbsEntry1","BinCode1","Note","StatusSAP","DateCreate",
        "UserID","ApplyStatus")
        values
        (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
         $stmtsheader = odbc_prepare($conDB, $insertHeader);
        $result = odbc_execute($stmtsheader, array($SOID,$date,$Stocktype,$FromWhsCode,$FromWhsName, $ToWhsCode,$ToWhsName,
        $AbsEntry,$team, $AbsEntry1,$team1,$note,$statusSAP,$datecreate,$userId,$applysap));
        
        //handler data to detail
        // item post
       
        $Itempost=[];
        foreach ($request->stockOuts as $item => $lots) {
            $newLots = [];
        
            foreach ($lots as $lot => $data) {
                if ($data[0] !== null && $data[0] !== 0) {
                    $newLots[$lot] = array(
                        'Quantity' => $data[0]
                    );
                }
            }
        
            if (!empty($newLots)) {
                foreach ($newLots as $lotKey => $lotValue) {
                    if ($lotValue['Quantity'] !== null && $lotValue['Quantity'] !== "0") {
                        $Itempost[] = array(
                            'Item' => $item,
                            'lot' => $lotKey,
                            'Quantity' => $lotValue['Quantity'],
                        );
                    }
                }
            }
        }
        
       
        //insert data item to table line
        $sqlinsertline='insert into BS_STOCKOUTREQUEST_Detail 
        ("StockNo","ItemCode","LotNo","TypeOfPrd","Quantity","CreatedDate") values(?,?,?,?,?,?)';
        foreach ($Itempost as $item)
        {
            $stmtline = odbc_prepare($conDB, $sqlinsertline);
            $result = odbc_execute($stmtline, array($SOID,$item['Item'],$item['lot'],01,$item['Quantity'], $datecreate));
        }
        //update itemname in line 
        $sqlupdate='call "SAL_UPDATE_ITM_NAME"'; 
        odbc_exec($conDB, $sqlupdate);
        odbc_close($conDB);
        return  redirect()->route('inv.list')->with('message', 'update sucesss!');
    

    }
    function confirm(Request $request)
    {
        $serviceLayerUrl = "https://".env('SAP_SERVER').":".env('SAP_PORT');
      
        $headers = [
            "Content-Type" => "application/json",
            "Accept" => "application/json",
            "Authorization" => "Basic " . env('BSHeader'),
        ];

        $client = new \GuzzleHttp\Client([
            "base_uri" => $serviceLayerUrl,
            "headers" => $headers,
        ]);
        $conDB = (new SAPB1Controller)->connect_sap();
        foreach($request->SoNo as $SoNo)
        {
            $sqlStockNo = 'Select "DocEntry" from OWTQ where "DocNum"=\''.$SoNo.'\'';
               // AND \"OrderType\" = '".$ordertype."'
        $SOID =odbc_result(odbc_exec($conDB, $sqlStockNo),1);
            $body=[
                "U_Confirm"=>"02",       
            ];
           
            
             // Make a request to the service layer
        $response = $client->request("patch", "/b1s/v1/InventoryTransferRequests(".$SOID.")",
        ['verify' => false, 'body' =>  json_encode($body)]);

        }
        odbc_close($conDB); 
        return response()->json(["success" => true,"data"=>"okay"]);
    }
}
