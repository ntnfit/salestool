<?php

namespace App\Http\Controllers;
use App\Http\Controllers\sap\SAPB1Controller;
use Illuminate\Http\Request;
use DB;
use Response;
class ExportCrystalReportController extends Controller
{
    public function export()
    {
        
    }
    public function applyDo(Request $request)
    {
        // Fetch the data from the database
        $conDB = (new SAPB1Controller)->connect_sap();
        //get new Do
        
        if($request->type=="01")
        {
          
           
            if($request->delno)
            {
               
                foreach($request->Prama as $so)
                {
                    
                    list($no, $type) = explode('-', $so);
                 
                    if( $type=="IT")
                    {
                        $upate='update OWTQ SET "U_DelNo"=? where "DocNum"=? ';
                        $stmt = odbc_prepare($conDB, $upate);
                        odbc_execute($stmt,array($request->delno[0],$no));
                    }
                    else
                    {
                        $upate='update ORDR SET "U_DelNo"=? where "DocNum"=? ';
                        $stmt = odbc_prepare($conDB, $upate);
                        odbc_execute($stmt,array($request->delno[0],$no));
                    }
                    
                }
            }
            else
            {
               
                $sqLDo='call USP_SAL_Generate_DO';
                $DO = odbc_result(odbc_exec($conDB, $sqLDo), 1);
               
                foreach($request->Prama as $so)
                {
                    
                    list($no, $type) = explode('-', $so);
                 
                    if( $type=="IT")
                    {
                        $upate='update OWTQ SET "U_DelNo"=? where "DocNum"=? ';
                        $stmt = odbc_prepare($conDB, $upate);
                        odbc_execute($stmt,array($DO,$no));
                    }
                    else
                    {
                        $upate='update ORDR SET "U_DelNo"=? where "DocNum"=? ';
                        $stmt = odbc_prepare($conDB, $upate);
                        odbc_execute($stmt,array($DO,$no));
                    }
                    
                }
             
            }
            
           
        }
        odbc_close($conDB);
        return response()->json(["success" => true]);
    
       
    }
    function print_do(Request $request)
    {
        $conDB = (new SAPB1Controller)->connect_sap();
        $type="";
       if($request->type=="stockout")
       {
        $sql='call  "USP_BS_TRUCKINFOMATION_REPORT1" (?)';
        $stmt = odbc_prepare($conDB, $sql);
        $type="stockout";
       }
       
       else
       {
        $sql='call  "USP_BS_TRUCKINFOMATION_REPORT" (?)';
        $stmt = odbc_prepare($conDB, $sql);
        $type="print";
       } 
        odbc_execute($stmt,array($request->pra));
        $results = array();
        while ($row = odbc_fetch_object($stmt)) {
            $results[] = $row;
        };
       
        if($request->type=="stockout")
            {
                $data=collect($results)->groupBy('U_TruckInfo');  
            }
            else
            {
                $data = collect($results)->groupBy(['U_TruckInfo', 'CardCode']);
                
               
            }
            
          
        odbc_close($conDB);
        
        // Pass the data to the Blade template and render the view
        return view('layoutsap.truckinfor', ['data' => $data,'type'=>$type]);

    }
}
