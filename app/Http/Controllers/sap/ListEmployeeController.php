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
        $ListEmp = DB::table('employee_data')->get();
    
        return view('sap.listemployees',compact('ListEmp'));
    }
}
