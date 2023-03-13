<?php

namespace App\Http\Controllers\sap;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
class DeliveryController extends Controller
{
    public function index()
    {
        return view('delivery.index');
    }
 
 
    public function store(Request $request)
    { if ($request->ajax()) {
        if ($request->hasFile('file')) {
            $imageFiles = $request->file('file');
            // set destination path
            $folderDir = 'uploads/delivery';
            $destinationPath = base_path() . '/' . $folderDir;
            // this form uploads multiple files
            foreach ($request->file('file') as $fileKey => $fileObject ) {
                // make sure each file is valid
                if ($fileObject->isValid()) {
                    // make destination file name
                    $destinationFileName =$fileObject->getClientOriginalName();
                    // move the file from tmp to the destination path
                    $fileObject->move($destinationPath, $destinationFileName);
                    // save the the destination filename
                    // $prodcuctImage = new ProductImage;
                    // $ProdcuctImage->image_path = $folderDir . $destinationFileName;
                    // $prodcuctImage->title = $originalNameWithoutExt;
                    // $prodcuctImage->alt = $originalNameWithoutExt;
                    // $prodcuctImage->save();
                }
            }
        }
    }
    }
    function destroy (Request $request)
    {
        
        
        $destinationPath =base_path() . '/' .'uploads/delivery/';
       
        if(file_exists($destinationPath.$request->id))
        {
           unlink($destinationPath.$request->id);
        } 
           
            
    }
    function updatestatus(Request $request)
    {
        dd($request->all());
    }
   
}
