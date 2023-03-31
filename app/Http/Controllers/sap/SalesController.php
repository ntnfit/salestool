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
       
}
