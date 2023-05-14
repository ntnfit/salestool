<?php

namespace App\Http\Controllers\sap;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Spatie\Permission\Models\Role;
use App\Models\LogData;
use App\Models\LogImport;
use Auth;
class SalesBAController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:marketing-module');
         
    }
    function view()
    {
        return view('importBA.upload');
    }
    function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);
        
        
    // Get the uploaded file
    $file = $request->file('file');

    // Read the Excel file
    $data = Excel::toArray([], $file);

    $headerRow = $data[0][0];

    $responseData=[];
    // Process the data rows
    for ($i = 1; $i < count($data[0]); $i++) {
       
        $rowData = $data[0][$i];
       
        // Extract the CustCode value
        $custCode = $rowData[0];
        
        $outputData = $this->processExcelData($headerRow,$rowData);
        // Create the item lines
        $ouput= [
            'AgreementType'=>"atGeneral",
            'BPCode' => $rowData[0],
            'Status' =>'asApproved',
            'StartDate' =>Carbon::createFromFormat('m/d/Y', '1/1/1900')
            ->addDays($rowData[1] - 2)
            ->toDateString(),
            'EndDate' =>Carbon::createFromFormat('m/d/Y', '1/1/1900')
            ->addDays($rowData[2] - 2)
            ->toDateString(),
            'U_Type'=>$rowData[3],
            'BlanketAgreements_ItemsLines' => $outputData
        ];
        
        $responseData[]= $this->postBA($ouput);
         
    }   
    $id=0;
    if (!empty($responseData)) {
        $logImport = new LogImport;
        $logImport->type="BA";
        $logImport->userID=auth()->user()->id;
        $logImport->save();
        $logId=$logImport->id;
        $id= $logId;
        // inser detail
        foreach($responseData as $res)
        {
           
            if(!empty($res['error']))
            {
                $logData = new LogData;
                $logData->LogId= $logId;
                $logData->Status="Failed";
                $logData->Error_code= $res['error']['code'];
                $logData->Message=  $res['error']['message']['value'];
                $logData->save();

            }
            else
            {
               
                $logData = new LogData;
                $logData->LogId= $logId;
                $logData->Status="succeed";
                $logData->DocNum= $res['DocNum'];
             
                $logData->save();
            }
        }
       
     return redirect()->route('import.log',[$id]);
    }
    else
    {
        return  redirect()->route('import.upload')->with('error', 'Please check again file import!');
    }
   
    }

    function  processExcelData($headerRow,$rowData)
    {
        $itemLines = [];
        for ($j = 4; $j < count($headerRow); $j++) {
            $itemCode = $headerRow[$j];
            $quantity = $rowData[$j];

            // Skip rows with null or 0 quantity
            if ($quantity !== null && $quantity !== 0) {
                $itemLines[] = [
                    'ItemNo' => $itemCode,
                    'PlannedQuantity' => $quantity,
                ];
            }
        }

        return $itemLines;
    }
    function postBA($data)
    {
        try {
        $serviceLayerUrl = "https://" . env('SAP_SERVER') . ":" . env('SAP_PORT');

        $response = Http::withOptions([
            'verify' => false,
        ])->withHeaders([
            "Content-Type" => "application/json",
            "Accept" => "application/json",
            "Authorization" => "Basic " . env('BSHeader'),
        ])->post($serviceLayerUrl . "/b1s/v1/BlanketAgreements", $data);

        return $response->json();
        } catch (ConnectionException $e) {
            // Handle the exception (failed connection)
            return response()->json(['error' => 'API request failed: ' . $e->getMessage()], 500);
        }
    }
    function listlog(Request $request,$id)
    {
        $datas=LogData::where('LogId',$id)->get();
        return view('importBA.log',compact('datas'));
    }
}
