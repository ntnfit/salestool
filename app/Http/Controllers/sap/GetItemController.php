<?php

namespace App\Http\Controllers\sap;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\sap\SAPB1Controller;
use DB;
class GetItemController extends Controller
{
    function getItemSAP()
    {
        $conDB =(new SAPB1Controller)->connect_sap();
      
        $query = 'SELECT * FROM OITM';
        $result = odbc_exec($conDB, $query);
       
        // Fetch the results as an associative array
        $results = array();
       
        while ($row = odbc_fetch_array($result)) {
           
            $results[] = $row;
          
        }
        
        odbc_close($conDB);
        echo json_encode($results,JSON_UNESCAPED_UNICODE);
    }

    function getTeam(Request $request)
    {
        $validated = $request->validate([
            'WhsCode' => 'required',
        ]);
        $whscode=$request->WhsCode;
        $BinCode=DB::table('SAL_BINCODE')->where('WhsCode', $whscode)->get();
        $results=json_encode($BinCode);
        return  $results;
    }
    function getCustDate(Request $request)
    {
        
        $custdata = DB::table('GT_CUSTOMER_DATA')->get();
    
        return view('sap.CustomerData',compact('custdata'));
    }
    function test(Request $request)
    {
        
        $custdata = DB::table('GT_CUSTOMER_DATA')->get()->take(10);
    
        return view('sap.test',compact('custdata'));
    }
    function getsaletotal(Request $request)
    {
        $conDB = (new SAPB1Controller)->connect_sap();
        
        $query='call "usp_Rpt_BS_Item_StockSalesAvailable_byLotNo_Total_web" (?,?)';
        $stmt = odbc_prepare($conDB, $query);
        
        if (!odbc_execute($stmt, array($request->whscode, $request->bincode))) {
            // Handle execution error
            die("Error executing SQL statement: " . odbc_errormsg());
        }

        $results = array();
        while ($row = odbc_fetch_array($stmt)) {
            $results[] = $row;
        }
        $results=json_encode($results);
        odbc_close($conDB);
        return  $results;
    }
    function getsaledetail(Request $request)
    {
        $conDB = (new SAPB1Controller)->connect_sap();
        
        $query='call "usp_Rpt_BS_Item_StockSalesAvailable_byLotNo_Detail" (?,?,?,?)';
        $stmt = odbc_prepare($conDB, $query);
       
        if (!odbc_execute($stmt, array( date('Ymd', strtotime($request->fromdate)), date('Ymd', strtotime($request->todate)),$request->whscode, $request->bincode))) {
            // Handle execution error
            die("Error executing SQL statement: " . odbc_errormsg());
        }

        $results = array();
        while ($row = odbc_fetch_array($stmt)) {
            $results[] = $row;
        }
        odbc_close($conDB);
        $results=json_encode($results);
        return  $results;
    }

    function ValiatePOID(Request $request)
    {
        $conDB = (new SAPB1Controller)->connect_sap();
        $query='SELECT count(*) as NUM FROM BS_STOCKOUTREQUEST WHERE "POCardCode"=\'' . $request->po .'\' AND IFNULL("Canceled",\'\')'."<>'C'";
        
      $stmt = odbc_prepare($conDB, $query);
        odbc_execute($stmt);
        $results = array();
        while ($row = odbc_fetch_array($stmt)) {
            $results[] = $row;
        }
        odbc_close($conDB);
        if($results[0]['NUM']!="0")
        {
            return response()->json(["success" => true,"data"=>1]);
        }
        else
        {
            return response()->json(["success" => true,"data"=>0]);
        }
        
    }

    function GetSupportOrder(Request $request)
    {
       
        $conDB = (new SAPB1Controller)->connect_sap();
        $query='call USP_BS_BLANKET(?, ?)';
        if($request->type=="01")// đơn hàng '01' -- SUPPORT
        {
            $stmt = odbc_prepare($conDB, $query);
            odbc_execute($stmt,[$request->custcode,'01']);
        }
        else if($request->type=="02")//   if(==)// '02' -- SAMPLING
        {
            $stmt = odbc_prepare($conDB, $query);
            odbc_execute($stmt,[$request->custcode,'02']);
        }
        else //03 DA
        { $querys='call "USP_BS_BLANKET"(?, ?)';
            $stmt = odbc_prepare($conDB, $querys);
            odbc_execute($stmt,[$request->custcode,'03']);
        }
       
       
        $results = array();
        while ($row = odbc_fetch_object($stmt)) {
            $results[] = $row;
        }
        odbc_close($conDB);
      return $results;
        
    }
    function salebycust (Request $request)
    {
        $conDB = (new SAPB1Controller)->connect_sap();
        $sql='call "usp_Rpt_TotalSalesByCustGroup_Customer_WEB" (?,?,?)';
        $stmt = odbc_prepare($conDB, $sql);
        odbc_execute($stmt,array($request->fromDate,$request->toDate,$request->channel));
        $results = array();
        while ($row = odbc_fetch_object($stmt)) {
            $results[] = $row;
        }
        odbc_close($conDB);
        return json_encode($results);
    }
    function salebycustpro (Request $request)
    {
        $conDB = (new SAPB1Controller)->connect_sap();
        $sql='call "usp_Rpt_TotalSalesByCustGroup_Customer_Product_WEB" (?,?,?)';
        
        $stmt = odbc_prepare($conDB, $sql);
       
        odbc_execute($stmt,array($request->fromDate,$request->toDate,$request->channel));
        $results = array();
        while ($row = odbc_fetch_object($stmt)) {
            $results[] = $row;
        }
        odbc_close($conDB);
        return json_encode($results);

    }
    function loadDfWhsCode(Request $request)
    {
        $conDB = (new SAPB1Controller)->connect_sap();
        $sql='call "usp_BS_Customer_getDefaultWhs" (?)';
        
        $stmt = odbc_prepare($conDB, $sql);
       
        odbc_execute($stmt,array($request->customer));
        $results = array();
        while ($row = odbc_fetch_object($stmt)) {
            $results[] = $row;
        }
        odbc_close($conDB);

        return json_encode($results[0]);
    }
    function loadprintkeyorder(Request $request)
    {
        $conDB = (new SAPB1Controller)->connect_sap();
        $sql = 'CALL "usp_load_preview_stock_key_order"(?)';

        $stmt = odbc_prepare($conDB, $sql);
          
        if (!$stmt) {
            // Handle SQL error
            die("Error preparing SQL statement: " . odbc_errormsg());
        }
        // Execute the stored procedure with the input parameters
        if (!odbc_execute($stmt, array($request->so))) {
            // Handle execution error
            die("Error executing SQL statement: " . odbc_errormsg());
        }
        // Retrieve the result set from the stored procedure
        $results = array();
        while ($row = odbc_fetch_array($stmt)) {
            $results[] = $row;
        }
        //sort($results);
        $itemCodes = array_column($results, 'ItemCode');
        $typePrds = array_column($results, 'TypePrd');

        // Sort the data based on multiple columns
        array_multisort($typePrds, SORT_ASC, $itemCodes, SORT_ASC, $results);
                
        
        // get number lot
        $distinctLots = array_unique(array_column($results, 'LotNo'));
        odbc_close($conDB);
        // pass data to the view and render the Blade template
        return  view('sales.print_preview', compact('distinctLots', 'results'));
        
         
    }
    function ValidateBAP(Request $request)
    {
        $conDB = (new SAPB1Controller)->connect_sap();
        $da='151-54,154-55';
       $sql='call "usp_BS_StockOutRequest_Check_OOAT" (?,?)';
        $stmt = odbc_prepare($conDB, $sql);
        odbc_execute($stmt,array($request->AbsID, $request->ItemList));
        $results = array();
        while ($row = odbc_fetch_array($stmt)) {
            $results[] = $row;
        }
        odbc_close($conDB);
        $results=json_encode($results);
        return  $results;
    }
}
