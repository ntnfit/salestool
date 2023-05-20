<?php

namespace App\Http\Controllers\sap;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\sap\SAPB1Controller;
use DB;
use Response;
use Carbon\Carbon;
class PromotionController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:marketing-module');
         
    }
    // get list promotion
    function listPromotion(Request $request)
    {
        $Promotionlist = DB::table('promotionlist')->orderby('ProId', 'desc')->get();
        
        return view('promotion.ListPromotions',compact('Promotionlist'))
        ->with('i', ($request->input('page', 1) - 1) * 100);
    }
    function listPromotionDate(Request $request)
    {
        $Promotionlist = DB::table('PromotionDateList')->orderby('ProId', 'desc')->get();
        
        return view('promotion.ListPromotionsDate',compact('Promotionlist'))
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
       // $Customers=DB::table('Customerlist')->get();
        $Uoms=DB::table('UOMSAP')->get();
       
        return view('promotion.add',compact('PromTypes','Custgroups','channels','Routes','Locations','ItemCodes','Uoms'));

    }

     function edit ($id)
    {   
        $conDB =(new SAPB1Controller)->connect_sap();
        $sql='select * from "BS_PROMOTION" where "ProId"=('.$id.')';
        $stmt = odbc_prepare($conDB, $sql);
        $Datas=odbc_exec($conDB,$sql);
        $header=[];
         // Retrieve the result set from the stored procedure
         while ($row = odbc_fetch_array($Datas)) {
            $header[] = $row;
        }
        $sql='select * from "BS_PRO_CUSTOMER" where "ProId"=('.$id.') and ifnull("ProCustCode",'.'\'\''.') <>'.'\'\'';;
        $stmt = odbc_prepare($conDB, $sql);
        $Datas=odbc_exec($conDB,$sql);
        $customerdt=[];
         // Retrieve the result set from the stored procedure
        while ($row = odbc_fetch_object($Datas)) {
            $customerdt[] = $row;
        }

        $sql='select * from "BS_PROMOTION_ITEM" where "ProId"=('.$id.') and ifnull("ProItemCode",'.'\'\''.') <>'.'\'\'';
        $stmt = odbc_prepare($conDB, $sql);
        $Datas=odbc_exec($conDB,$sql);
        $proitems=[];
         // Retrieve the result set from the stored procedure
         while ($row = odbc_fetch_array($Datas)) {
            $proitems[] = $row;
        }
        $sql='select * from "BS_PRO_ITEMLIST" where "ProId"=('.$id.') and ifnull("ItemCode",'.'\'\''.') <>'.'\'\'';
        $stmt = odbc_prepare($conDB, $sql);
        $Datas=odbc_exec($conDB,$sql);
        $listItems=[];
         // Retrieve the result set from the stored procedure
         while ($row = odbc_fetch_array($Datas)) {
            $listItems[] = $row;
        }
        odbc_close($conDB);

       
        $PromTypes = DB::table('PromotionType')->get();
        $Custgroups =DB::table('CUSTGroupDis')->get();
        $channels=DB::table('Channel')->get();
        $Routes=DB::table('Routes')->get();
        $ItemCodes=DB::table('ItemSAP')->get();
        $Locations=DB::table('Locations')->get();
        $Uoms=DB::table('UOMSAP')->get();
       
        $customerdt=json_encode( $customerdt);
       
        return view('promotion.edit',compact('PromTypes','Custgroups',
        'channels','Routes','Locations',
        'ItemCodes','Uoms','listItems','customerdt','header','proitems'
        ));

    }
    function edit_prodate ($id)
    {   
        $conDB =(new SAPB1Controller)->connect_sap();
        $sql='select * from "BS_DatePromotion" where "ProID"=('.$id.')';
        $stmt = odbc_prepare($conDB, $sql);
        $Datas=odbc_exec($conDB,$sql);
        $header=[];
         // Retrieve the result set from the stored procedure
         while ($row = odbc_fetch_array($Datas)) {
            $header[] = $row;
        }
        $sql='select "CustCode" "CardCode","CustName" "CardName",* from "BS_DatePromotion_Cust" where "ProId"=('.$id.')';
        $stmt = odbc_prepare($conDB, $sql);
        $Datas=odbc_exec($conDB,$sql);
        $customerdt=[];
         // Retrieve the result set from the stored procedure
         while ($row = odbc_fetch_object($Datas)) {
            $customerdt[] = $row;
        }
        $sql='select * from "BS_DatePromotion_Item" where "ProId"=('.$id.')';
        $stmt = odbc_prepare($conDB, $sql);
        $Datas=odbc_exec($conDB,$sql);
        $listItems=[];
         // Retrieve the result set from the stored procedure
         while ($row = odbc_fetch_array($Datas)) {
            $listItems[] = $row;
        }
        odbc_close($conDB);

        $customerdt=json_encode( $customerdt);
        $PromTypes = DB::table('PromotionType')->get();
        $Custgroups =DB::table('CUSTGroupDis')->get();
        $channels=DB::table('Channel')->get();
        $Routes=DB::table('Routes')->get();
        $ItemCodes=DB::table('ItemSAP')->get();
        $Locations=DB::table('Locations')->get();
        $Uoms=DB::table('UOMSAP')->get();
       
       
        return view('promotion.editdate',compact('PromTypes','Custgroups',
        'channels','Routes','Locations',
        'ItemCodes','Uoms','listItems','customerdt','header'
        ));

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

        return json_encode($results);
     
       // return response()->json(['cust' => $Customers], 200);
    }
    function store(Request $request)
    {
      
     
        
        $conDB =(new SAPB1Controller)->connect_sap();
       
       $sql="";
        if($request->protype!="5")
        {
            $sql = 'INSERT INTO BS_PROMOTION ("PromotionType", "PromotionName","Fromdate","ToDate","Quantity",
            "TotalAmount","DiscountAmt","DiscountPercent","Special","Rouding","DateCreate"
           ) VALUES (?,?,?,?,?,?,?,?,?, ?,?)';
        }
        else
        {
            $sql = 'INSERT INTO "BS_DatePromotion" ("ProName","FromDate","ToDate","Rouding","FixCust","DateCreated") 
            VALUES (?,?,?,?,?,?)';
        }
       
        
        //format data
        $period = $request->period;
        $date_parts = explode('-', $period);
        $fromdate = date('Ymd', strtotime(str_replace('/', '-',$date_parts[0])));
        $todate = date('Ymd', strtotime(str_replace('/', '-',$date_parts[1])));
      
        // Bind the values to the statement
        $PromotionType = $request->protype;
        $PromotionName = $request->promotionname;
        $Fromdate=$fromdate;
        $ToDate=$todate;
       
        $Quantity=$request->Quantity;
        $TotalAmount=$request->Amount;
        $DiscountAmt=0;
        $DiscountPercent=$request->dispercent;
        if($request->special)
        {
            $Special=1;
        }
        else{
            $Special=0;
        }
       if($request->rouding)
       {
        $Rouding=1;
       }
       else{
        $Rouding=0;
       }
       if($request->fixcus)
       {
        $fixcus=1;
       }
       else{
        $fixcus=0;
       }
       
       
        $stmt = odbc_prepare($conDB, $sql);
        if (!$stmt) {
            // Handle SQL error
            die("Error preparing SQL statement: " . odbc_errormsg());
        }
        if($request->protype!="5")
        {
            if (!odbc_execute($stmt, array($PromotionType, $PromotionName,$Fromdate,$ToDate,$Quantity,$TotalAmount,$DiscountAmt,$DiscountPercent,$Special,$Rouding,date("Ymd")))) {
                // Handle execution error
                die("Error executing SQL statement: " . odbc_errormsg());
            }
        }
        else
        {
            if (!odbc_execute($stmt, array($PromotionName,$Fromdate,$ToDate,$Rouding,$fixcus,date("Ymd")))) {
                // Handle execution error
                die("Error executing SQL statement: " . odbc_errormsg());
            }
        }
        // Get the ID of the inserted row
        $id = odbc_result(odbc_exec($conDB, "SELECT CURRENT_IDENTITY_VALUE() from dummy"), 1);
        // insert to row data
            // Item row
            foreach($request->Item as $key => $Items)
            {
                if ($Items !== null) {
                $item=$request->Item[$key];
                $Qty=(float)$request->Qty[$key];
                $UomCode=(INT)$request->UomCode[$key];
                $BaseQty=(float)$request->BaseQty[$key];
                $BaseUom=(INT)$request->BaseUom[$key];
                $Batch=$request->Batch[$key];
                $Itemdate=$request->Itemdate[$key];
                $ProQtydate=$request->ProQtydate[$key];
                $ProBatchdate=$request->ProBatchdate[$key];
                    if($request->protype!="5")
                    { 
                        $querySQL='insert into BS_PRO_ITEMLIST ("ProId", "ItemCode","Quantity","UoMEntry","BaseQuantity","BaseUoMEntry")
                        values (?, ?, ?, ?, ?, ?)';
                        $stmt = odbc_prepare($conDB, $querySQL);               
                        $result = odbc_execute($stmt, array($id, $item, $Qty, $UomCode, $BaseQty, $BaseUom));
                    }
                    else
                    {
                        $querySQL='insert into "BS_DatePromotion_Item" ("ProId", "ItemCode","InputQty","InputUoMCode","Quantity","UoMCode","BatchNo","ProItemCode","ProQuantity","ProDate")
                        values (?,?,?,?,?,?,?,?,?,?)';
                        $stmt = odbc_prepare($conDB, $querySQL);               
                        $result = odbc_execute($stmt, array($id, $item, $Qty, $UomCode, $BaseQty, $BaseUom,$Batch,$Itemdate, $ProQtydate, $ProBatchdate));

                    }
                }    

            }
            $cusdata=json_decode($request->customerdata[0]);
             //Customer row
            if (!empty( $cusdata)) {
                
                
                foreach ($cusdata as $key => $cusCode) {    
                    if($cusCode !== null)
                    {
                        $customercode= $cusCode->CardCode;
                     
                        if($request->protype!="5")
                        {
                            $insertCusQuery='insert into BS_PRO_CUSTOMER  ("ProId","ProCustCode","ProCustName","GroupCode","ChannelCode","RouteCode","LocationCode")';  
                        }
                        else
                        {
                            $insertCusQuery='insert into "BS_DatePromotion_Cust" ("ProId","CustCode","CustName","GroupCode","ChannelCode","RouteCode","LocationCode")';
                        }

                       
                        $cusQuery='SELECT '.$id.',"CardCode","CardName","GroupCode","ChannelCode","RouteCode","LocationCode" FROM ST_CUSTOMER_DROPDOWN where "CardCode"='."'".$customercode."'";
                        $run=odbc_prepare($conDB,$insertCusQuery.$cusQuery);
                        odbc_execute($run );
                    }   
              
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
        if($request->protype!="5")
        {
            return redirect()->route('list-promotion')->with('message', 'Add promotion successfully.');
        }
        else
        {
            return redirect()->route('list-promotion-date')->with('message', 'Add promotion successfully.');
        }
        

    }
    function check_baseUoM(Request $request)
    {
        
        $BaseUom=DB::table('ST_BaseUom')->where('ItemCode',$request->itemcode)->where('UomEntry',$request->uomcode)->get()->first(); 
       return response()->json(['baseUomCode'=>$BaseUom->BaseUoMEntry,
                        'baseQuantity'=>$request->quantity*$BaseUom->UoMCode_Qty
         ]);
    }

    function update(Request $request, $id)
    {
       
       
        $conDB =(new SAPB1Controller)->connect_sap();
        
        $sql="";
        if($request->protype!="5")
        {
           $sql = 'UPDATE BS_PROMOTION SET
            "PromotionType" = ?,
            "PromotionName" = ?,
            "Fromdate" = ?,
            "ToDate" = ?,
            "Quantity" = ?,
            "TotalAmount" = ?,
            "DiscountAmt" = ?,
            "DiscountPercent" = ?,
            "Special" = ?,
            "Rouding" = ?,
            "DateUpdate" =?
            WHERE "ProId"='.$id;
        }
        else
        {
            $sql = 'UPDATE "BS_DatePromotion" SET
                "ProName" = ?,
                "FromDate" = ?,
                "ToDate" = ?,
                "Rouding" = ?,
                "FixCust" = ?,
                "DateUpdated"=?      
                WHERE "ProID"='.$id;
        }
       

        //format data
        $period = $request->period;
        $dates = explode(" - ", $period);
        // Convert each date to the "YYYYMMDD" format
        
        $from_date = \Carbon\Carbon::createFromFormat('d/m/Y',$dates[0])->format('Ymd');
        $to_date =\Carbon\Carbon::createFromFormat('d/m/Y',$dates[1])->format('Ymd');
        // Bind the values to the statement
        $PromotionType = $request->protype;
        $PromotionName = $request->promotionname;
        $Fromdate=$from_date;
        $ToDate=$to_date;
        $Quantity=$request->Quantity;
        $TotalAmount=$request->Amount;
        $DiscountAmt=0;
        $DiscountPercent=$request->dispercent;
        if($request->special)
        {
            $Special=1;
        }
        else{
            $Special=0;
        }
       if($request->rouding)
       {
        $Rouding=1;
       }
       else{
        $Rouding=0;
       }
       if($request->fixcus)
       {
        $fixcus=1;
       }
       else{
        $fixcus=0;
       }
        $stmt = odbc_prepare($conDB, $sql);
        if (!$stmt) {
            // Handle SQL error
            die("Error preparing SQL statement: " . odbc_errormsg());
        }

        if($request->protype!="5")
        {
            if (!odbc_execute($stmt, array($PromotionType, $PromotionName,$Fromdate,$ToDate,$Quantity,$TotalAmount,$DiscountAmt,$DiscountPercent,$Special,$Rouding,date("Ymd")))) {
                // Handle execution error
                die("Error executing SQL statement: " . odbc_errormsg());
            }
            // clear row add again:
            $sql='delete  from "BS_PRO_ITEMLIST" where "ProId"='.$id.'';
            odbc_exec($conDB,$sql);
            $sql='delete  from "BS_PRO_CUSTOMER" where "ProId"='.$id.'';
            odbc_exec($conDB,$sql);
            $sql='delete  from "BS_PROMOTION_ITEM" where "ProId"='.$id.'';
            odbc_exec($conDB,$sql);
        }
        else
        {
            if (!odbc_execute($stmt, array($PromotionName,$Fromdate,$ToDate,$Rouding,$fixcus,date("Ymd")))) {
                // Handle execution error
                die("Error executing SQL statement: " . odbc_errormsg());
            }
                // clear row add again:
                $sql='delete  from "BS_DatePromotion_Item" where "ProId"='.$id.'';
                odbc_exec($conDB,$sql);
                $sql='delete  from "BS_DatePromotion_Cust" where "ProId"='.$id.'';
                odbc_exec($conDB,$sql);

        }
        
        
        
       
        // insert to row data
            // Item row
            foreach($request->Item as $key => $Items)
            {
                if ($Items !== null) {
                    $item=$request->Item[$key];
                    $Qty=(float)$request->Qty[$key];
                    $UomCode=(INT)$request->UomCode[$key];
                    $BaseQty=(float)$request->BaseQty[$key];
                    $BaseUom=(INT)$request->BaseUom[$key];
                   
                   
                        if($request->protype!="5")
                        { 
                            $querySQL='insert into BS_PRO_ITEMLIST ("ProId", "ItemCode","Quantity","UoMEntry","BaseQuantity","BaseUoMEntry")
                            values (?, ?, ?, ?, ?, ?)';
                            $stmt = odbc_prepare($conDB, $querySQL);               
                            $result = odbc_execute($stmt, array($id, $item, $Qty, $UomCode, $BaseQty, $BaseUom));
                        }
                        else
                        {
                            $Itemdate=$request->Itemdate[$key];
                            $ProQtydate=$request->ProQtydate[$key];
                            $ProBatchdate=$request->ProBatchdate[$key];
                            $Batch=$request->Batch[$key];
                            $querySQL='insert into "BS_DatePromotion_Item" 
                            ("ProId", "ItemCode","InputQty","InputUoMCode",
                            "Quantity","UoMCode","BatchNo","ProItemCode","ProQuantity","ProDate")
                            values (?,?,?,?,?,?,?,?,?,?)';
                            $stmt = odbc_prepare($conDB, $querySQL);               
                            $result = odbc_execute($stmt, array($id, $item, $Qty, $UomCode, $BaseQty, $BaseUom,$Batch,$Itemdate, $ProQtydate, $ProBatchdate));
    
                        }
                    }            

            }
             //Customer row
             $cusdata=json_decode($request->customerdata[0]);
            if (!empty($cusdata)) {
            
                foreach ( $cusdata as $key => $cusCode) {       
                  
                    if($request->protype!="5")
                    { 
                        $customercode=$cusCode->ProCustCode;
                        $insertCusQuery='insert into BS_PRO_CUSTOMER ("ProId","ProCustCode","ProCustName","GroupCode","ChannelCode","RouteCode","LocationCode")';
                       
                    }
                    else
                    {
                        $customercode=$cusCode->CardCode;
                        $insertCusQuery='insert into "BS_DatePromotion_Cust" ("ProId","CustCode","CustName","GroupCode","ChannelCode","RouteCode","LocationCode")';
                    }
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
        if($request->protype!="5")
        {
        return redirect()->route('list-promotion')->with('message', 'update promotion successfully.');
        }
        else
        {
            return redirect()->route('list-promotion-date')->with('message', 'update promotion successfully.');
        }
    }
    function terminated(Request $request)
    {
        $conDB =(new SAPB1Controller)->connect_sap();
        foreach($request->SoNo as $SoNo)
        {
            if($request->protype!="5")
            {
                $querySQL='update "BS_PROMOTION" set "hasTerminate"=1 where "ProId"=?';
            }
            else
            {
                $querySQL='update "BS_DatePromotion" set "HasTerm"=1 where "ProID"=?';   
            }
                    
            $stmt = odbc_prepare($conDB, $querySQL);
            $result = odbc_execute($stmt, array($SoNo));
        }

       odbc_close($conDB);
       return response()->json(["success" => true]);
    }
}
