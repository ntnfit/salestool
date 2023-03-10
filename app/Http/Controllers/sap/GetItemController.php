<?php

namespace App\Http\Controllers\sap;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\sap\SAPB1Controller;
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
}
