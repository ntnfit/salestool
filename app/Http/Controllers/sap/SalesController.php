<?php

namespace App\Http\Controllers\sap;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\sap\SAPB1Controller;
use DB;
use Response;
use Carbon\Carbon;
use Auth;
class SalesController extends Controller
{
    function listSaleStock(Request $request)
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
           $sql = 'CALL USP_BS_STOCKOUTREQUEST2(?,?,?,?)';
           $stmt = odbc_prepare($conDB, $sql);
           if (!$stmt) {
               // Handle SQL error
               die("Error preparing SQL statement: " . odbc_errormsg());
           }

           // Set the input parameters for the stored procedure
           $promotionType = '';
           
           $special = 0;

           // Execute the stored procedure with the input parameters
           if (!odbc_execute($stmt, array($promotionType, $fromDate, $toDate, $special))) {
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
           return view('sales.list',compact('results'));
            
        }
        else
        {
           $fromDate = Carbon::createFromFormat('m/d/Y', $request->fromdate)->format('Ymd');
           $toDate =Carbon::createFromFormat('m/d/Y', $request->todate)->format('Ymd');
           $conDB = (new SAPB1Controller)->connect_sap();
           if (!$conDB) {
               // Handle connection error
               die("Error connecting to SAPB1: " . odbc_errormsg());
           }

           // Prepare the SQL statement
           $sql = 'CALL USP_BS_STOCKOUTREQUEST2(?,?,?,?)';
           $stmt = odbc_prepare($conDB, $sql);
           if (!$stmt) {
               // Handle SQL error
               die("Error preparing SQL statement: " . odbc_errormsg());
           }

           // Set the input parameters for the stored procedure
           $promotionType = '';
           
           $special = 0;

           // Execute the stored procedure with the input parameters
           if (!odbc_execute($stmt, array($promotionType, $fromDate, $toDate, $special))) {
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
    function edit($id)
    {
        $orderTypes=DB::table('SAL_ORDER_TYPE')->get();
        $conDB = (new SAPB1Controller)->connect_sap();
        if (!$conDB) {
            // Handle connection error
            die("Error connecting to SAPB1: " . odbc_errormsg());
        }     
        // get data pass to pramater
        $so=DB::TABLE('SAL_LIST_STOCK_REQUEST')->where('StockNo',$id)->first();
     
        $sql = 'CALL USP_BS_LOT_OINM_STOCKREQUEST(?,?,?,?,?,?)';
        $stmt = odbc_prepare($conDB, $sql);
        if (!$stmt) {
            // Handle SQL error
            die("Error preparing SQL statement: " . odbc_errormsg());
        }
        // Execute the stored procedure with the input parameters
        if (!odbc_execute($stmt, array($so->StockDate,$so->CustCode,$so->FromWhsCode,$id,0,$so->AbsEntry))) {
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
        
        return view('sales.edit',compact('results','distinctLots','orderTypes','so'));
    }
    function addView()
    {
        $orderTypes=DB::table('SAL_ORDER_TYPE')->get();
        $customers=DB::table('Customerlist')->orderby('CardCode','ASC')->get();
        $whsCodes=DB::table('SAL_OWHS')->get();
        
        return view('sales.add',compact('orderTypes','customers','whsCodes'));
    }

    
    function store(Request $request)
    {
       
      // OPEN connect ODBC
      $conDB =(new SAPB1Controller)->connect_sap();
      //dd($Itempost);
        // post to header
        $date=(string)date("Ymd", strtotime($request->date));
        $ordertype=$request->ordertype;
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
                    WHERE 
                        \"StockDate\" = '".$date."' 
                        AND \"OrderType\" = '".$ordertype."'
                ) T0";
        
        $SOID = 'SO'.date("ym", strtotime($request->date)).odbc_result(odbc_exec($conDB, $sqlStockNo),1);
        $Stocktype=2;
        $custcode=$request->cuscode;
        $custname=$request->custname;
        $FromWhsCode=$request->WhsCode;
        $FromWhsName=$request->frmwhsname;
        $OrderType=$request->ordertype;
        $POCardCode=$request->pono;
        $PODate=date("Ymd", strtotime($request->podate));
        $AbsEntry=$request->bincode;
        $AbsId=null;//Sale blanket id
        $team=$request->teams;
        $note=$request->note;
        $statusSAP=0;
        $userId = Auth::user()->UserID;
        $applysap=0;
        $datecreate=date("Ymd", strtotime(date("Y/m/d")));
        $insertHeader='insert into "BS_STOCKOUTREQUEST" 
        ("StockNo","StockDate","StockType",
        "CustCode","CustName","FromWhsCode","FromWhsName",
        "OrderType","POCardCode","PODate","AbsID",
        "AbsEntry","BinCode","Note","StatusSAP","DateCreate",
        "UserID","ApplyStatus")
        values
        (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
         $stmtsheader = odbc_prepare($conDB, $insertHeader);
        $result = odbc_execute($stmtsheader, array($SOID,$date,$Stocktype,$custcode,$custname,$FromWhsCode,$FromWhsName,
        $OrderType, $POCardCode, $PODate,$AbsId,$AbsEntry,$team,$note,$statusSAP,$datecreate,$userId,$applysap));
        
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
                            'Type'=>$ordertype
                        );
                    }
                }
            }
        }
        
        // item post
        $ItemPro=[];
        if($request->proout)
        {
        
        foreach ($request->proout as $item => $lots) {
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
                    $ItemPro[] = array(
                        'Item' => $item,
                        'lot' => $lotKey,
                        'Quantity' => $lotValue['Quantity']
                    );
                }
            }
        }
        }
        }
       
        //insert data item to table line
        $sqlinsertline='insert into BS_STOCKOUTREQUEST_Detail 
        ("StockNo","ItemCode","LotNo","TypePrd","QuantityPro","Quantity","CreatedDate") values(?,?,?,?,?,?,?)';
        foreach ($Itempost as $item)
        {
           
        $stmtline = odbc_prepare($conDB, $sqlinsertline);
        $result = odbc_execute($stmtline, array($SOID,$item['Item'],$item['lot'],$ordertype,$item['Quantity'],$item['Quantity'], $datecreate));

        }
       if( $ItemPro)
       {
        foreach ($ItemPro as $item)
        {
           
        $stmtline = odbc_prepare($conDB, $sqlinsertline);
        $result = odbc_execute($stmtline, array($SOID,$item['Item'],$item['lot'],"002",$item['Quantity'],$item['Quantity'],$datecreate));

        }

       }
        //update itemname in line 
        $sqlupdate='call "SAL_UPDATE_ITM_NAME"'; 
        odbc_exec($conDB, $sqlupdate);
        odbc_close($conDB);

        return  redirect()->route('sales.list')->with('message', 'add sucesss!');
       
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
        $date=(string)date("Ymd", strtotime($request->date));
        $ordertype=$request->ordertype; 
        $SOID =$request->sono;
        $Stocktype=2;
        $custcode=$request->cuscode;
        $custname=$request->custname;
        $FromWhsCode=$request->WhsCode;
        $FromWhsName=$request->frmwhsname;
        $OrderType=$request->ordertype;
        $POCardCode=$request->pono;
        $PODate=date("Ymd", strtotime($request->podate));
        $AbsEntry=$request->bincode;
        $AbsId=null;//Sale blanket id
        $team=$request->teams;
        $note=$request->note;
        $statusSAP=0;
        $userId = Auth::user()->UserID;
        $applysap=0;
        $datecreate=date("Ymd", strtotime(date("Y/m/d")));
        $insertHeader='insert into "BS_STOCKOUTREQUEST" 
        ("StockNo","StockDate","StockType",
        "CustCode","CustName","FromWhsCode","FromWhsName",
        "OrderType","POCardCode","PODate","AbsID",
        "AbsEntry","BinCode","Note","StatusSAP","DateCreate",
        "UserID","ApplyStatus")
        values
        (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
         $stmtsheader = odbc_prepare($conDB, $insertHeader);
        $result = odbc_execute($stmtsheader, array($SOID,$date,$Stocktype,$custcode,$custname,$FromWhsCode,$FromWhsName,
        $OrderType, $POCardCode, $PODate,$AbsId,$AbsEntry,$team,$note,$statusSAP,$datecreate,$userId,$applysap));
        
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
                            'Type' => $ordertype
                        );
                    }
                }
            }
        }
        
        // item post
        $ItemPro=[];
        if($request->proout)
        {
        
        foreach ($request->proout as $item => $lots) {
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
                    $ItemPro[] = array(
                        'Item' => $item,
                        'lot' => $lotKey,
                        'Quantity' => $lotValue['Quantity']
                       
                    );
                }
            }
        }
        }
        }
        //insert data item to table line
        $sqlinsertline='insert into BS_STOCKOUTREQUEST_Detail 
        ("StockNo","ItemCode","LotNo","TypePrd","QuantityPro","Quantity","CreatedDate") values(?,?,?,?,?,?,?)';
        foreach ($Itempost as $item)
        {

           
           
        $stmtline = odbc_prepare($conDB, $sqlinsertline);
        $result = odbc_execute($stmtline, array($SOID,$item['Item'],$item['lot'],$ordertype,$item['Quantity'],$item['Quantity'], $datecreate));

        }
       if( $ItemPro)
       {
        foreach ($ItemPro as $item)
        {
           
        $stmtline = odbc_prepare($conDB, $sqlinsertline);
        $result = odbc_execute($stmtline, array($SOID,$item['Item'],$item['lot'],"002",$item['Quantity'],$item['Quantity'],$datecreate));

        }

       }
        //update itemname in line 
        $sqlupdate='call "SAL_UPDATE_ITM_NAME"'; 
        odbc_exec($conDB, $sqlupdate);
        odbc_close($conDB);
        return  redirect()->route('sales.list')->with('message', 'update sucesss!');
    

    }
    function applySAP(Request $request)
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
             where "StatusSAP"=0 and "StockNo"=?';
            $stmt = odbc_prepare($conDB, $sql);
            odbc_execute($stmt,array($SoNo));
            $line = array();
            while ($row = odbc_fetch_array($stmt)) {
                $line[] = $row;
            }
            
            $ldt=[];
            foreach ($line as $dt)
            {
                $km="";
                if($dt['TypePrd']=="002")
                {
                    $km="1";
                }
                else
                {
                    $km="0";
                }
                $ldt=[
                    "ItemCode"=> $dt['ItemCode'],
                    "Quantity"=> $dt['Quantity'],
                    "TaxCode" => "SVN10",
                    "WarehouseCode" => $results[0]['FromWhsCode'],
                    "U_LoaiKM" => $km,
                    "U_BatchNo" => $dt['LotNo']
                ];
                array_push($line, $line);
            }
            $body=[
                "CardCode"=> $results[0]['CustCode'],
                "Comments"=>"Apply from salesHub",
                "U_SoPhieu"=>$SoNo,
                "U_FromBIN"=> $results[0]['AbsEntry'],
                "U_FromBinCode"=> $results[0]['BinCode'],
                "DocumentLines"=>[$ldt]
                
            ];
           
         
             // Make a request to the service layer
        $response = $client->request("POST", "/b1s/v1/Quotations",['verify' => false, 'body' =>  json_encode($body)]);

          $sql = 'Update  BS_STOCKOUTREQUEST set "StatusSAP"=1 where "StockNo"=?';
            $stmt = odbc_prepare($conDB, $sql);
            odbc_execute($stmt, array($SoNo));
        }
        
        return response()->json(["success" => true,"data"=>"okay"]);

    }
    // function filter 
    function filterdata(Request $request)
    {
        $so="";
      
        if($request->sono)
        {
            $so=$request->sono;
        }
        else
        {
            $so='SO'.substr(date("Y"), -2).date("m").'99999999999999';
        }
       
        $blanket=0;
        if($request->sporderno)
        {
            $blanket=$request->sporderno;
        }
        else
        {
            $blanket=0;
        }
      
       
        $conDB = (new SAPB1Controller)->connect_sap();
        if (!$conDB) {
            // Handle connection error
            die("Error connecting to SAPB1: " . odbc_errormsg());
        }     
        $sql = 'CALL USP_BS_LOT_OINM_STOCKREQUEST(?,?,?,?,?,?)';

        $stmt = odbc_prepare($conDB, $sql);
        
        if (!$stmt) {
            // Handle SQL error
            die("Error preparing SQL statement: " . odbc_errormsg());
        }
        // Execute the stored procedure with the input parameters
        if (!odbc_execute($stmt, array(date("Ymd", strtotime($request->sodate)),$request->custcode,$request->whscode,$so,$blanket,$request->team))) {
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
        // get number lot
        $distinctLots = array_unique(array_column($results, 'LotNo'));
        $ordertype=$request->ordertype;
        // pass data to the view and render the Blade template
        $tableHtml = view('sales.tabletemplate', compact('distinctLots', 'results','ordertype'))->render();
        odbc_close($conDB);
         return  $tableHtml;
    }
    function getpromotion(Request $request)
    {
        
       
        $conDB = (new SAPB1Controller)->connect_sap();
        if (!$conDB) {
            // Handle connection error
            die("Error connecting to SAPB1: " . odbc_errormsg());
        }     
        $sql = 'call "USP_BS_GET_PROMOTION_Click"(?,?,?,?,?)';
        $stmt = odbc_prepare($conDB, $sql);
        $itemlot="";
        if($request->itemlots)
        {
            $itemlot=$request->itemlots;
        }
        else{
            $itemlot="";
        }
        if (!$stmt) {
            // Handle SQL error
            die("Error preparing SQL statement: " . odbc_errormsg());
        }
        
        // Execute the stored procedure with the input parameters
        //if (!odbc_execute($stmt, array('102522','HO03','2018-4-5','201-25152',''))) {
        if (!odbc_execute($stmt, array($request->custcodes,$request->whscodes,$request->dates,$request->itemlists,$itemlot))) {
            // Handle execution error
            die("Error executing SQL statement: " . odbc_errormsg());
        }
        $results = array();
        // Check if there are any results
        if (odbc_num_rows($stmt) == 0) {
            $results = [];
        }

        // Retrieve the result set from the stored procedure
        while ($row = odbc_fetch_array($stmt)) {
            $results[] = $row;
        }
        $listrs = array();
        foreach ($results as $item) {
            $listrs[$item["ProItemCode"]] = (int)$item["TotalQuantity"];
          }
          odbc_close($conDB);
       return $listrs;
    }
     
}
   
