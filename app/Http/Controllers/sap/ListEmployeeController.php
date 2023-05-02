<?php

namespace App\Http\Controllers\sap;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\sap\SAPB1Controller;
use DB;
use Response;
class ListEmployeeController extends Controller
{
    //
    function ListEmploy()
    {
        $conDB =(new SAPB1Controller)->connect_sap();
        $sql='select * from "employ_data"';
        $stmt = odbc_prepare($conDB, $sql);
        odbc_execute($stmt);
        $results=[];
        while ($row = odbc_fetch_object($stmt)) {
            $results[] = $row;
        }
        $ListEmp=json_encode($results) ;
        odbc_close($conDB);
        return view('sap.listemployees',compact('ListEmp'));
    }
}
