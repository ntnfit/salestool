<?php

namespace App\Http\Controllers\sap;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\sap\SAPB1Controller;
use DB;
use Response;
class PromotionController extends Controller
{
    // get list promotion
    function listPromotion(Request $request)
    {
        $Promotionlist = DB::table('promotionlist')->orderby('ProId', 'desc')->get();
        
        return view('promotion.ListPromotions',compact('Promotionlist'))
        ->with('i', ($request->input('page', 1) - 1) * 100);
    }

    
    function dfPromotion()
    {
        $PromTypes = DB::table('PromotionType')->get();
        $Custgroups =DB::table('CUSTGroupDis')->get();
        $channels=DB::table('Channel')->get();
        $Routes=DB::table('Routes')->get();
        $ItemCodes=DB::table('ItemSAP')->get();
        $Locations=DB::table('Locations')->get();
        $Customers=DB::table('Customerlist')->get();
        $Uoms=DB::table('UOMSAP')->get();
       
        return view('promotion.add',compact('PromTypes','Custgroups','channels','Routes','Locations','ItemCodes','Customers','Uoms'));

    }

    function ListCustomerDropDown (Request $request)
    {
        $query = DB::table('Customerlist');

       
        if ($request->cusgrp) {
            $query->whereIn('GroupCode', array_map('trim', explode(',', $request->cusgrp)));
        }
    
        if ($request->channel) {
            $query->whereIn('ChannelCode', array_map('trim', explode(',', $request->channel)));
        }
    
        if ($request->route) {
            $query->whereIn('RouteCode', array_map('trim', explode(',', $request->route)));
        }
    
        if ($request->location) {
            $query->whereIn('LocationCode', array_map('trim', explode(',', $request->location)));
          
        }
    
        $results = $query->get();

        $Customers = '';
        foreach ($results as $result) {
            $Customers .= '<tr class="tr_clone">
                    <td>
                        <select class="items" name="cus[]" data-placeholder="Select an customer">
                            <option value="'.$result->CardCode.'" selected>'.$result->CardName.'--'.$result->GroupName.'--'.$result->ChannelName.'--'.$result->RouteName.'--'.$result->LocationName.'--'.'betagen'.'</option> 
                        </select>
                    </td>
                    <td>
                        <button type="button" class="btn btn-outline-danger" onclick="removeRow(this, \'#tablecustomer\')">
                            <i class="fa fa-trash" aria-hidden="true"></i> 
                        </button>
                    </td>
                </tr>';
        }
      
        return response()->json(['cust' => $Customers], 200);
    }
    function store(Request $request)
    {
      
       
        $conDB =(new SAPB1Controller)->connect_sap();
        
       
        $sql = 'INSERT INTO BS_PROMOTION ("PromotionType", "PromotionName","Fromdate","ToDate","Quantity",
        "TotalAmount","DiscountAmt","DiscountPercent","Special","Rouding"
       ) VALUES (?,?,?,?,?,?,?,?,?, ?)';

        //format data
        $period = $request->period;
        $date_parts = explode('-', $period);
        $fromdate = trim($date_parts[0]);
        $todate = trim($date_parts[1]);
       
        // Bind the values to the statement
        $PromotionType = $request->protype;
        $PromotionName = $request->promotionname;
        $Fromdate=date("Ymd", strtotime($fromdate));
        $ToDate=date("Ymd", strtotime($todate));
        $Quantity=$request->Quantity;
        $TotalAmount=$request->Amount;
        $DiscountAmt=0;
        $DiscountPercent=$request->dispercent;
        if(!$request->special)
        {
            $Special=1;
        }
        else{
            $Special=0;
        }
       if(!$request->rouding)
       {
        $Rouding=1;
       }
       else{
        $Rouding=0;
       }
       
        
        $stmt = odbc_prepare($conDB, $sql);
        if (!$stmt) {
            // Handle SQL error
            die("Error preparing SQL statement: " . odbc_errormsg());
        }
        if (!odbc_execute($stmt, array($PromotionType, $PromotionName,$Fromdate,$Fromdate,$Quantity,$TotalAmount,$DiscountAmt,$DiscountPercent,$Special,$Rouding))) {
            // Handle execution error
            die("Error executing SQL statement: " . odbc_errormsg());
        }
        
        // Get the ID of the inserted row
        $id = odbc_result(odbc_exec($conDB, "SELECT CURRENT_IDENTITY_VALUE() from dummy"), 1);
        // insert to row data
            // Item row
            foreach($request->Item as $key => $Items)
            {
                $item=$request->Item[$key];
                $Qty=(float)$request->Qty[$key];
                $UomCode=(INT)$request->UomCode[$key];
                $BaseQty=(float)$request->BaseQty[$key];
                $BaseUom=(INT)$request->BaseUom[$key];
                
               $querySQL='insert into BS_PRO_ITEMLIST ("ProId", "ItemCode","Quantity","UoMEntry","BaseQuantity","BaseUoMEntry")
               values (?, ?, ?, ?, ?, ?)';
            
                $stmt = odbc_prepare($conDB, $querySQL);               
                $result = odbc_execute($stmt, array($id, $item, $Qty, $UomCode, $BaseQty, $BaseUom));
                

            }
             //Customer row
            if (!empty($request->cus)) {
            
                foreach ($request->cus as $key => $cusCode) {       
                $customercode=$request->cus[$key];
                $insertCusQuery='insert into BS_PRO_CUSTOMER ("ProId","ProCustCode","ProCustName","GroupCode","ChannelCode","RouteCode","LocationCode")';
                $cusQuery='SELECT '.$id.',"CardCode","CardName","GroupCode","ChannelCode","RouteCode","LocationCode" FROM ST_CUSTOMER_DROPDOWN where "CardCode"='."'".$customercode."'";
                $run=odbc_prepare($conDB,$insertCusQuery.$cusQuery);
                odbc_execute($run );
                }
            }
            if (!empty($request->proitem)) {
            //Item promotion
            foreach($request->proitem as $key => $Items)
            {
                $item=$request->proitem[$key];
                $Qty=(float)$request->proqty[$key];
                $UomCode=$request->prouomcode[$key];
                $BaseQty=(float)$request->probaseqty[$key];
                $BaseUom=$request->probaseoum[$key];
                
               $querySQL='insert into BS_PROMOTION_ITEM ("ProId", "ProItemCode","ProQuantity","ProUoMEntry","ProBaseQuantity","ProBaseUoMEntry")
                values (?,?,?,?,?,?)';              
               $stmt = odbc_prepare($conDB, $querySQL);
               $result = odbc_execute($stmt, array($id,$item,$Qty,$UomCode,$BaseQty,$BaseUom));

            }
        }
        odbc_close($conDB);
        return redirect()->route('list-promotion')->with('success', 'add promotion successfully.');

    }
    function check_baseUoM(Request $request)
    {
        
        $BaseUom=DB::table('ST_BaseUom')->where('ItemCode',$request->itemcode)->where('UomEntry',$request->uomcode)->get()->first(); 
       return response()->json(['baseUomCode'=>$BaseUom->BaseUoMEntry,
                        'baseQuantity'=>$request->quantity*$BaseUom->UoMCode_Qty
    ]);
    }

}
