<?php

namespace App\Http\Controllers\sap;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\sap\SAPB1Controller;
use Spatie\Permission\Models\Role;
use DB;
use Response;
use Carbon\Carbon;
use Auth;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
class SalesController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:sales-module');
         
    }
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
           $fromDate = Carbon::createFromFormat('d/m/Y', $request->fromdate)->format('Ymd');
           $toDate =Carbon::createFromFormat('d/m/Y', $request->todate)->format('Ymd');
           
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
    function loadall()
    {
        $conDB = (new SAPB1Controller)->connect_sap();
     
        $sql = 'select * from UV_SO_LOADALL';
        $stmt = odbc_prepare($conDB, $sql);
        odbc_execute($stmt);

        // Retrieve the result set from the stored procedure
        $results = array();
        while ($row = odbc_fetch_array($stmt)) {
            $results[] = $row;
        }
        odbc_close($conDB);
        $results=json_encode($results);
       
        return  $results;
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
        $AbsID=0;
        if($so->AbsID!=null)
        {
            $AbsID=$so->AbsID;
        }
        else
        {
        $AbsID=0;
        }
        if (!$stmt) {
            // Handle SQL error
            die("Error preparing SQL statement: " . odbc_errormsg());
        }
       
        // Execute the stored procedure with the input parameters
        if (!odbc_execute($stmt, array($so->StockDate,$so->CustCode,$so->FromWhsCode,$id,$AbsID,$so->AbsEntry))) {
            // Handle execution error
            die("Error executing SQL statement: " . odbc_errormsg());
        }

        // Retrieve the result set from the stored procedure
        $results = array();
        while ($row = odbc_fetch_array($stmt)) {
            $results[] = $row;
        }
        $results = array_filter($results, function($item) {
            return $item['QuantityIn'] > 0;
        });
        $blanket=0;
       if($so->AbsID){
        $blanket=$so->AbsID;
       }
       $itemCodes = array_column($results, 'ItemCode');
       $typePrds = array_column($results, 'TypePrd');

       // Sort the data based on multiple columns
       array_multisort($typePrds, SORT_ASC, $itemCodes, SORT_ASC, $results);
       // dd($results);
        // get number lot
        $distinctLots = array_unique(array_column($results, 'LotNo'));
       
        odbc_close($conDB);
        
        return view('sales.edit',compact('results','distinctLots','orderTypes','so','blanket'));
    }
    function addView()
    {
        $response = Http::withOptions([
            'verify' => false,
        ])->withHeaders([
            "Content-Type" => "application/json",
            "Accept" => "application/json",
            "Authorization" => "Basic U1lTVEVNOlNhcEAyMDIzI0Ix",
            ])->get("http://172.31.246.123:8000/GTHUB/xsjs/LoadCustData.xsjs");

         // Make a request to the service layer
    //$response = $client->request("POST", "/b1s/v1/Quotations",['verify' => false, 'body' =>  json_encode($body)]);
        $customers = json_decode($response, true);
        $orderTypes=DB::table('SAL_ORDER_TYPE')->get();
        $whsCodes=DB::table('SAL_OWHS')->get();
     
        return view('sales.add',compact('orderTypes','customers','whsCodes'));
    }
    function store(Request $request)
    {
     
        $prefix="";
        
        if($request->ordertype=="001")
        {
            $prefix='SO';
        }
        else{
            $prefix='PR';
        }
      // OPEN connect ODBC
      $conDB =(new SAPB1Controller)->connect_sap();
      //dd($Itempost);
        // post to header
        $date=Carbon::createFromFormat('d/m/Y', $request->date)->format('Ymd');
        
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
                    WHERE   YEAR(\"StockDate\") = YEAR('".$date."') 
                        AND MONTH(\"StockDate\") = MONTH('".$date."') 
                        
                ) T0";
               // AND \"OrderType\" = '".$ordertype."'
        $SOID = $prefix.date("ym", strtotime( $date)).odbc_result(odbc_exec($conDB, $sqlStockNo),1);
      
        $Stocktype=2;
        $custcode=$request->cuscode;
        $custname=$request->custname;
        $FromWhsCode=$request->WhsCode;
        $FromWhsName=$request->frmwhsname;
        $OrderType=$request->ordertype;
        $POCardCode=$request->pono;
        $PODate="";
        if($request->podate)
        {
            $PODate=Carbon::createFromFormat('d/m/Y', $request->podate)->format('Ymd');
        }
        $AbsEntry=$request->bincode;
        $AbsId=$request->sporderno;
        $team=$request->teams;
        $note=$request->note;
        $statusSAP=0;
        $userId = Auth::user()->UserID;
        $applysap=0;
        $datecreate=date("Y-m-d H:i:s", time());

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

        return  redirect()->route('sales.add')->with('message', 'add sucesss!');
       
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
        $ordertype=$request->ordertype; 
        $SOID =$request->sono;
        $Stocktype=2;
        $custcode=$request->cuscode;
        $custname=$request->custname;
        $FromWhsCode=$request->WhsCode;
        $FromWhsName=$request->frmwhsname;
        $OrderType=$request->ordertype;
        $POCardCode=$request->pono;
        $PODate=Carbon::createFromFormat('d/m/Y', $request->podate)->format('Ymd');
        $AbsEntry=$request->bincode;
        $AbsId=null;//Sale blanket id
        $team=$request->teams;
        $note=$request->note;
        $statusSAP=0;
        $userId = Auth::user()->UserID;
        $applysap=0;
        $datecreate=$date;
        $insertHeader='insert into "BS_STOCKOUTREQUEST" 
        ("StockNo","StockDate","StockType",
        "CustCode","CustName","FromWhsCode","FromWhsName",
        "OrderType","POCardCode","PODate","AbsID",
        "AbsEntry","BinCode","Note","StatusSAP","DateCreate","DateUpdate",
        "UserID","ApplyStatus")
        values
        (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)';

         $stmtsheader = odbc_prepare($conDB, $insertHeader);
        $result = odbc_execute($stmtsheader, array($SOID,$date,$Stocktype,$custcode,$custname,$FromWhsCode,$FromWhsName,
        $OrderType, $POCardCode, $PODate,$AbsId,$AbsEntry,$team,$note,$statusSAP,$datecreate,date("Y-m-d H:i:s", time()),$userId,$applysap));
        
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
      
        try {
            $serviceLayerUrl = "https://" . env('SAP_SERVER') . ":" . env('SAP_PORT');

            

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
          
            $sql = 'select t1.*,t3."AgrLineNum","AgrNo",t4."U_Location",t4."U_Channel" from BS_STOCKOUTREQUEST t0 join BS_STOCKOUTREQUEST_Detail t1
            on t0."StockNo"=t1."StockNo"
            left join OOAT t2 on t2."Number"=T0."AbsID"
            LEFT JOIN OAT1 T3 on t2."AbsID"=t3."AgrNo" 
            and t1."ItemCode"=t3."ItemCode"
            left join OCRD t4 on t0."CustCode" = t4."CardCode"
            where "Quantity"<>0 and t0."StatusSAP"=0 and t0."StockNo"=?';
            $stmt = odbc_prepare($conDB, $sql);
            odbc_execute($stmt,array($SoNo));
            $line = array();
            while ($row = odbc_fetch_object($stmt)) {
                $line[] = $row;
            }
            $itemCodes = array_column($line, 'ItemCode');
        $typePrds = array_column($line, 'TypePrd');

        // Sort the data based on multiple columns
        array_multisort($typePrds, SORT_ASC, $itemCodes, SORT_ASC, $line);
            $price=null;
            $ldt=[];
            foreach ($line as $dt)
            {
                $km="";
                if($dt->TypePrd!="001")
                {
                    $km="1";
                    $price=0;
                    $ldt[]=[
                        "ItemCode"=> $dt->ItemCode,
                        "Quantity"=> $dt->Quantity,
                        "TaxCode" => "SVN10",
                        "UnitPrice"=>$price,
                        "WarehouseCode" => $results[0]['FromWhsCode'],
                        "U_LoaiKM" => $km,
                        "U_BatchNo" => $dt->LotNo,
                        "AgreementNo" =>$dt->AgrNo,
                        "AgreementRowNumber"=>$dt->AgrLineNum,
                        "CostingCode" => $dt->ItemCode,
                        "CostingCode2" => $dt->U_Channel,
                        "CostingCode3" => $dt->U_Location,
                        "COGSCostingCode" => $dt->ItemCode,
                        "COGSCostingCode2" => $dt->U_Channel,
                        "COGSCostingCode3" => $dt->U_Location
                    ];
                }
                else
                {
                    $km="0";
                    $ldt[]=[
                        "ItemCode"=> $dt->ItemCode,
                        "Quantity"=> $dt->Quantity,
                        "TaxCode" => "SVN10",
                        "WarehouseCode" => $results[0]['FromWhsCode'],
                        "U_LoaiKM" => $km,
                        "U_BatchNo" => $dt->LotNo,
                        "AgreementNo" =>$dt->AgrNo,
                        "AgreementRowNumber"=>$dt->AgrLineNum,
                        "CostingCode" => $dt->ItemCode,
                        "CostingCode2" => $dt->U_Channel,
                        "CostingCode3" => $dt->U_Location,
                        "COGSCostingCode" => $dt->ItemCode,
                        "COGSCostingCode2" => $dt->U_Channel,
                        "COGSCostingCode3" => $dt->U_Location
                    ];
                }
               
            }
            $body=[
                "CardCode"=> $results[0]['CustCode'],
                "Comments"=>"".$results[0]['Note'],
                "U_SoPhieu"=>$SoNo,
                "U_SoNo"=>$results[0]['POCardCode'],
                "U_FromBIN"=> $results[0]['AbsEntry'],
                "U_FromBinCode"=> $results[0]['BinCode'],
                "U_OrderType"=> $results[0]['OrderType'],
                "DocumentLines"=>$ldt
                
            ];
           
            $response = Http::withOptions([
                'verify' => false,
            ])->withHeaders([
                "Content-Type" => "application/json",
                "Accept" => "application/json",
                "Authorization" => "Basic " . env('BSHeader'),
                ])->post($serviceLayerUrl . "/b1s/v1/Quotations", $body);
             // Make a request to the service layer
        //$response = $client->request("POST", "/b1s/v1/Quotations",['verify' => false, 'body' =>  json_encode($body)]);
        $res=$response->json();
        if(!empty($res['error']))
        { 
           
            $sql = 'Update  BS_STOCKOUTREQUEST set "ApplySAPRemark"=? where "StockNo"=?';
            $stmt = odbc_prepare($conDB, $sql);
            odbc_execute($stmt, array($res['error']['message']['value'],$SoNo));
           // odbc_close($conDB); 
            //return response()->json(["success" => false,"data"=>$res['error']['code'].$res['error']['message']['value']],401);
            
        }
        else{

            $sql = 'Update  BS_STOCKOUTREQUEST set "ApplySAPRemark"=?,"StatusSAP"=1 where "StockNo"=?';
            $stmt = odbc_prepare($conDB, $sql);
            odbc_execute($stmt, array($res['DocNum'],$SoNo));    
        }    
        }
        odbc_close($conDB); 
        return response()->json(["success" => true,"data"=> 'okay'],200);
    }
    catch (ConnectionException $e) {
        // Handle the exception (failed connection)
        return response()->json(["success" => false,"data"=> 'API request failed: ' . $e->getMessage()], 500);
    }
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
            if($request->sporderno)
            {
                $so='PR'.substr(date("Y"), -2).date("m").'99999999999999';
            }
            else
            {
                $so='SO'.substr(date("Y"), -2).date("m").'99999999999999';
            }
           
        }
     
        $blanket=0;
        if($request->sporderno!=null)
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
       
        $results = array_filter($results, function($item) {
            return $item['QuantityIn'] > 0;
        });
        
        $itemCodes = array_column($results, 'ItemCode');
        $typePrds = array_column($results, 'TypePrd');
        $Lotdata = array_column($results, 'LotNo');
        // Sort the data based on multiple columns
        array_multisort( $typePrds, SORT_ASC,$Lotdata,SORT_ASC,  $itemCodes, SORT_DESC, $results);
        // get number lot
        $distinctLots = array_unique(array_column($results, 'LotNo'));
        $ordertype=$request->ordertype;
        // pass data to the view and render the Blade template
        $tableHtml = view('sales.tabletemplate', compact('distinctLots', 'results','ordertype','blanket'))->render();
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
        if (!odbc_execute($stmt, array($request->custcodes,$request->whscodes,Carbon::createFromFormat('dmY', $request->dates)->format('Ymd'),$request->itemlists,$itemlot))) {
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
        $itemName=array();
        foreach ($results as $item) {
            $listrs[$item["ProItemCode"]] = (int)$item["TotalQuantity"];   
            $itemName[$item["ProItemCode"]] = $item["ItemName"];       
          }
         
          odbc_close($conDB);
       return response()->json(["promotiodt" => $listrs,"ItemName"=>$itemName]);
    }
    function CancelSO(Request $request)
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

    function ListAR(Request $request)
    {
        $conDB = (new SAPB1Controller)->connect_sap();
        if(!empty($request->fromDate) && !empty($request->toDate) )
       {
        $sql='call "USP_BS_INVSTATUS_DUPLICATE" (?,?)';
        $stmt = odbc_prepare($conDB, $sql);
        odbc_execute($stmt,array($request->fromDate,$request->toDate));
       }
        else
        {
            $sql='call "USP_BS_INVSTATUS_DUPLICATE_all" ()';
            $stmt = odbc_prepare($conDB, $sql);
            odbc_execute($stmt);
        }
       
        $results = array();
        while ($row = odbc_fetch_object($stmt)) {
            $results[] = $row;
        };
        odbc_close($conDB);
        return json_encode($results);
        
    }
     function listarview()
     {
        return view ('sales.updateArStatus');
     }
    function updateAr(Request $request)
    {
            $conDB = (new SAPB1Controller)->connect_sap();
            foreach($request->dataNo as $data){
                $sql='update BS_IVNSTATUS set "Received"=?,"DateReceived"=?,"sendInvoice"=?,"DateSend"=?,"Receiver"=? where "DocEntry"=?';
                $stmt = odbc_prepare($conDB, $sql);
                
                odbc_execute($stmt,array($data['Received'],$data['DateReceived'],$data['sendInvoice'],$data['DateSend'],$data['Receiver'],$data['DocEntry']));
                
                if($data['Received']==1)
                {
                    $sqlso='update ORDR set "U_InvStatus"=? where "DocEntry"=?';
                    $stmt = odbc_prepare($conDB, $sqlso);
                    
                    odbc_execute($stmt,array('03',$data['DocEntry']));
                }
                else
                {
                    $sqlso='update ORDR set "U_InvStatus"=? where "DocEntry"=?';
                    $stmt = odbc_prepare($conDB, $sqlso);
                    
                    odbc_execute($stmt,array('01',$data['DocEntry']));

                }
               
            
            }
            odbc_close($conDB);
            return response()->json(["success" => true]);
        
    } 

    
}
   
