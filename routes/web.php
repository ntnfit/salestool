<?php
  
use Illuminate\Support\Facades\Route;
  
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CustomAuthController; 
use App\Http\Controllers\sap\SAPB1Controller;
use App\Http\Controllers\Auth\ProfilesController;
  
Auth::routes();
Route::get('connect_saphana',[SAPB1Controller::class,'connect_saphan_db']);
Route::get('sapb1',[SAPB1Controller::class,'connect']);
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


    Route::get('/promotions-items', function () {
        return view('promotion.definePromotions');
    });
    
    Route::get('/connect-setup',
    function () {
        return view('sap.connectSetup');
    })->name('setup-connect');
    Route::post('/connect-setup', [SAPB1Controller::class,'connectSetup'])->name('connect-setup');
   
    Route::get('/profiles', [ProfilesController::class,'show'])->name('profiles');
});
