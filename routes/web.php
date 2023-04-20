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
        //update DO
        Route::post('/updateDo',[DeliveryController::class,'store'])->name('updateDo');
        Route::resource('delivery', DeliveryController::class);
        Route::post('uploadImg', 'ImageUploadController@postImages')->name('uploadImg'); 
        Route::post('deleteImg',[DeliveryController::class,'destroy']); 

Route::group(['middleware' => ['auth']], function() {
    Route::patch('/profile', [ProfilesController::class, 'update'])->name('profile.update');
    Route::patch('/resetpass', [ProfilesController::class, 'updatepass'])->name('profile.updatepass');
    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);
    Route::get('/', function () {
        return view('welcome');
    });
    Route::get('/customer-data', [GetItemController::class,'getCustDate'])->name('customer.data');

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
    Route::get('/promotion/{proid}',[PromotionController::class,'edit'])->name('pro.edit');
    Route::post('/promotion/{proid}',[PromotionController::class,'update'])->name('pro.update');
// sale stock- request
    Route::get('/stock-request-list',[SalesController::class,'listSaleStock'])->name('sales.list');

    Route::get('/stock-request',[SalesController::class,'addView'])->name('sales.add');
    
    Route::get('/stock-request-edit/{stockSO}',[SalesController::class,'edit'])->name('sales.edit');
    Route::post('/stock-request',[SalesController::class,'store'])->name('sales.store');
    Route::get('/promotion-click',[SalesController::class,'getpromotion'])->name('promotion.click');
    Route::post('/stock-update/{stockSO}',[SalesController::class,'update'])->name('sales.update');
    Route::get('/applysap',[SalesController::class,'applySAP'])->name('sales.apply');
    Route::get('/cancel-so',[SalesController::class,'CancelSO'])->name('sales.cancel');

        //logistic
    Route::get('/truck-information',[DeliveryController::class,'truckview'])->name('logistic.truckinfor');
    Route::get('/truck-truckapply',[DeliveryController::class,'TruckApply'])->name('logistic.TruckApply');
    Route::get('/lock-vehicle',
    function () {
        return view('logistic.lock');
    })->name('logistic.lock');

    //SAP
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
    Route::get('/saletotal',[GetItemController::class,'getsaletotal'])->name('report.saletotal');
    Route::get('/checkPOID',[GetItemController::class,'ValiatePOID'])->name('checkPOID');
    Route::get('/get-truck-infor',[DeliveryController::class,'TruckInfor'])->name('truck.get'); 
});
