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
use App\Http\Controllers\sap\ListEmployeeController;
use App\Http\Controllers\sap\SalesController;
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
    Route::get('/list-employees', [ListEmployeeController::class,'ListEmploy'])->name('list-employee');
       
    //promotions route
    Route::get('/promotions-list',[PromotionController::class,'listPromotion'])->name('list-promotion');
    Route::get('/promotions-add',[PromotionController::class,'dfPromotion'])->name('add-promotions');
    Route::get('/custmer-filter',[PromotionController::class,'ListCustomerDropDown'])->name('filterCus');
    Route::post('/promotions-submit',[PromotionController::class,'store'])->name('prosubmit');
    //update DO
    Route::post('/updateDo',[DeliveryController::class,'updatestatus'])->name('updateDo');
    // sale stock- request
        Route::get('/stock-request-list',[SalesController::class,'listSaleStock'])->name('sales.list');

        Route::get('/stock-request',[SalesController::class,'addView'])->name('sales.add');
        
        Route::get('/stock-request-edit/{stockSO}',[SalesController::class,'edit'])->name('sales.edit');
        Route::post('/stock-request',[SalesController::class,'store'])->name('sales.store');
        Route::get('/promotion-click',[SalesController::class,'getpromotion'])->name('promotion.click');

        Route::get('/sale-target',
        function () {
            return view('sales.saletarget');
        })->name('sales.saletarget');
    
        Route::get('/actual',
        function () {
            return view('sales.actual');
        })->name('sales.actual');

        Route::get('/sale-out-weekly',
        function () {
            return view('sales.weekly');
        })->name('sales.weekly');

    //logistic
   Route::get('/truck-information',
    function () {
        return view('logistic.truckinfo');
    })->name('logistic.truckinfor');

    Route::get('/lock-vehicle',
    function () {
        return view('logistic.lock');
    })->name('logistic.lock');

    

    Route::get('/connect-setup',
    function () {
        return view('sap.connectSetup');
    })->name('setup-connect');
    Route::post('/connect-setup', [SAPB1Controller::class,'connectSetup'])->name('connect-setup');
   
    Route::get('/profiles', [ProfilesController::class,'show'])->name('profiles');

    //validate data & get data via ODBC
    Route::get('/base-uom',[PromotionController::class,'check_baseUoM'])->name('baseuom');
    Route::get('/bincode',[GetItemController::class,'getTeam'])->name('bincode');
    Route::get('/fill-lot-items',[SalesController::class,'filterdata'])->name('filllot-items');
    Route::get('/getpromotion',[SalesController::class,'getpromotion'])->name('clickgetpromotion');
});
