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
        return  $results;
    }

    function ValiatePOID(Request $request)
    {
        $conDB = (new SAPB1Controller)->connect_sap();
        $query='call "usp_Rpt_BS_Item_StockSalesAvailable_byLotNo_Total_web" (?,?)';
        $stmt = odbc_prepare($conDB, $query);
        
    }
    
}
