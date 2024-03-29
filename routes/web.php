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
use App\Http\Controllers\sap\InvController;
use App\Http\Controllers\ExportCrystalReportController;
use App\Http\Controllers\sap\SalesBAController;
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
    Route::get('/test', [GetItemController::class,'test'])->name('test.data');

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
    Route::get('/promotions-list-date',[PromotionController::class,'listPromotionDate'])->name('list-promotion-date');
    Route::get('/promotions-add',[PromotionController::class,'dfPromotion'])->name('add-promotions');
    Route::get('/custmer-filter',[PromotionController::class,'ListCustomerDropDown'])->name('filterCus');
    Route::post('/promotions-submit',[PromotionController::class,'store'])->name('prosubmit');
    Route::get('/promotion/{proid}',[PromotionController::class,'edit'])->name('pro.edit');
    Route::get('/promotion-date/{proid}',[PromotionController::class,'edit_prodate'])->name('prodate.edit');
    Route::post('/promotion/{proid}',[PromotionController::class,'update'])->name('pro.update');
    Route::get('/pro-terminate',[PromotionController::class,'terminated'])->name('pro.terminated');
// sale stock- request
    Route::get('/stock-request-list',[SalesController::class,'listSaleStock'])->name('sales.list');
    Route::get('/stock-request-all',[SalesController::class,'loadall'])->name('sales.all');
    Route::get('/stock-request',[SalesController::class,'addView'])->name('sales.add');
    Route::get('/stock-request-edit/{stockSO}',[SalesController::class,'edit'])->name('sales.edit');
    Route::post('/stock-request',[SalesController::class,'store'])->name('sales.store');
    Route::get('/promotion-click',[SalesController::class,'getpromotion'])->name('promotion.click');
    Route::post('/stock-update/{stockSO}',[SalesController::class,'update'])->name('sales.update');
    Route::get('/applysap',[SalesController::class,'applySAP'])->name('sales.apply');
    Route::get('/cancel-so',[SalesController::class,'CancelSO'])->name('sales.cancel');
    Route::get('/list-ar',[SalesController::class,'listarview'])->name('sales.view');
    Route::get('/list-ar-get',[SalesController::class,'ListAR'])->name('sales.arlist');
    Route::post('/update-ar-status',[SalesController::class,'updateAr'])->name('sales.updatear');

        //logistic
    Route::get('/truck-information',[DeliveryController::class,'truckview'])->name('logistic.truckinfor');
    Route::get('/truck-truckapply',[DeliveryController::class,'TruckApply'])->name('logistic.TruckApply');
    Route::get('/lock-vehicle',[DeliveryController::class,'TruckLockView'])->name('logistic.lock');
    Route::get('/vehicle-lock',[DeliveryController::class,'TruckLock'])->name('logistic.applylock');
    Route::get('/print-so-no-list',[DeliveryController::class,'SoNotPrint'])->name('logistic.sonotprint');
    Route::get('/printed', [DeliveryController::class,'updatePrinted'])->name('printed.do');
    Route::get('/printe-layout', [DeliveryController::class,'PrintLayoutDO'])->name('printed.layout');
    Route::post('/rm-do', [DeliveryController::class,'removeDo'])->name('logistic.removeDo');
    // inventory
    Route::get('/stock-inv-list',[InvController::class,'listInvStock'])->name('inv.list');
    Route::get('/inv-request',[InvController::class,'addView'])->name('inv.add');
    Route::post('/inv-request',[InvController::class,'store'])->name('inv.store');
    Route::get('/inv-request-edit/{stockSO}',[InvController::class,'edit'])->name('inv.edit');
    Route::post('/inv-update/{stockSO}',[InvController::class,'update'])->name('inv.update');
    Route::get('/applysap-inv',[InvController::class,'applySAP'])->name('inv.apply');
    Route::get('/cancel-inv',[InvController::class,'Cancel'])->name('inv.cancel');
    Route::get('/confirm-inv',[InvController::class,'confirm'])->name('inv.confirm');
    Route::get('/stock-inv-all',[InvController::class,'loadall'])->name('inv.loadall');
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
    Route::get('/fill-lot-inv',[InvController::class,'loaddata'])->name('fill-inv');
    Route::get('/getpromotion',[SalesController::class,'getpromotion'])->name('clickgetpromotion');
    Route::get('/saletotal',[GetItemController::class,'getsaletotal'])->name('report.saletotal');
    Route::get('/rpt-salesdetail',[GetItemController::class,'getsaledetail'])->name('report.saledetail');
    Route::get('/rpt-salebycust',[GetItemController::class,'salebycust'])->name('report.salebycust');
    Route::get('/rpt-salebycust-product',[GetItemController::class,'salebycustpro'])->name('report.salebycust.product');
    Route::get('/checkPOID',[GetItemController::class,'ValiatePOID'])->name('checkPOID');
    Route::get('/get-truck-infor',[DeliveryController::class,'TruckInfor'])->name('truck.get'); 
    Route::get('/get-support-no',[GetItemController::class,'GetSupportOrder'])->name('GetSupportOrder');
    Route::get('/get-whs-df',[GetItemController::class,'loadDfWhsCode'])->name('getwhsdf');
    Route::get('/applyDo', [ExportCrystalReportController::class,'applyDo'])->name('applyDo');
    Route::get('/export-truckinfor', [ExportCrystalReportController::class,'print_do'])->name('print-do');
    route::get('/print-preview',[GetItemController::class,'loadprintkeyorder'])->name('print-preview');
    route::get('/check-qty-bap',[GetItemController::class,'ValidateBAP'])->name('check-quantityBAP');
    route::get('/truck-detail-data',[DeliveryController::class,'GetDetail'])->name('truck-detail');
    //upload file 
    route::get('/upload-file',[SalesBAController::class,'view'])->name('import.upload');
    route::post('/upload-excel',[SalesBAController::class,'upload'])->name('import.handle');
    route::get('/import-log/{log}',[SalesBAController::class,'listlog'])->name('import.log');

});
