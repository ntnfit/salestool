<?php

namespace App\Http\Controllers\sap;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\sap\SAPB1Controller;
use DB;
use Response;
use Carbon\Carbon;

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
           return  $results;
        }
          
    }
    function edit($id)
    {
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
        if (!odbc_execute($stmt, array('20220727','102800','HN03','SO220715484',0,24))) {
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
       

        return view('sales.edit',compact('results','distinctLots'));
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
        dd($request->all());
    }
    function update()
    {

    }
    function applySAP()
    {

    }
    // function filter 
    function filterdata(Request $request)
    {
      
        $so='SO'.substr(date("Y"), -2).date("m").'99999999999999';
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
        // pass data to the view and render the Blade template
        $tableHtml = view('sales.tabletemplate', compact('distinctLots', 'results'))->render();
         return  $tableHtml;
    }
    function getpromotion()
    {

    }
     
}
   
