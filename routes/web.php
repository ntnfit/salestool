<?php
  
use Illuminate\Support\Facades\Route;
  
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CustomAuthController; 
use App\Http\Controllers\sap\SAPB1Controller;
use App\Http\Controllers\sap\GetItemController;
use App\Http\Controllers\Auth\ProfilesController;
use App\Http\Controllers\sap\PromotionController;
use App\Http\Controllers\sap\DeliveryController;
use App\Http\Controllers\sap\ImageUploadController;
Auth::routes();

Route::get('sapb1',[SAPB1Controller::class,'connect']);
Route::get('getitemsap',[GetItemController::class,'getItemSAP']);
//

Route::resource('delivery', DeliveryController::class);
Route::post('uploadImg', 'ImageUploadController@postImages')->name('uploadImg'); 
Route::post('deleteImg',[DeliveryController::class,'destroy']); 

Route::group(['middleware' => ['auth']], function() {
    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);
    Route::get('/', function () {
        return view('welcome');
    });
    Route::get('/customer-data', function () {
        return view('sap.CustomerData');
    });
    Route::get('/stock-detail', function () {
        return view('sap.SaleDetail');
    });
    Route::get('/stock-total', function () {
        return view('sap.SaleTotal');
    });
    Route::get('/sale-by-cust', function () {
        return view('sap.SaleBycus');
    });
    Route::get('/total-by-sale', function () {
        return view('sap.TotalSaleByCustomer');
    });
    Route::get('/list-employees', function () {
        return view('sap.listemployees');
    });

    //promotions route
    Route::get('/promotions-list',[PromotionController::class,'listPromotion'])->name('list-promotion');
    Route::get('/promotions-add',[PromotionController::class,'dfPromotion'])->name('add-promotions');
    Route::get('/custmer-filter',[PromotionController::class,'ListCustomerDropDown'])->name('filterCus');
    Route::post('/promotions-submit',[PromotionController::class,'store'])->name('prosubmit');
    //update DO
    Route::post('/updateDo',[DeliveryController::class,'updatestatus'])->name('updateDo');
    Route::get('/connect-setup',
    function () {
        return view('sap.connectSetup');
    })->name('setup-connect');
    Route::post('/connect-setup', [SAPB1Controller::class,'connectSetup'])->name('connect-setup');
   
    Route::get('/profiles', [ProfilesController::class,'show'])->name('profiles');
});
