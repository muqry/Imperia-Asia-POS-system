<?php

use App\Models\Product;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;

use App\Http\Controllers\UserController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\ReportExportController;
use App\Http\Controllers\ProfitController;
use App\Http\Controllers\MyProfitController;
use App\Http\Controllers\TransactionController;
use App\Livewire\Staffproduct;
use App\Models\Category;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::middleware(['auth', 'check.role:1'])->group(function () {

    // only for admin

    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    // Route::resource('/orders', OrderController::class); //orders.index
    Route::resource('/products', ProductController::class);//products.index
    Route::resource('/suppliers', 'SupplierController'); //suppliers.index
    Route::resource('/users', 'UserController'); //users.index
    Route::resource('/companies', 'CompanyController'); //companies.index
    Route::resource('/transactions', 'TransactionController'); //transactions.index
    Route::resource('users', UserController::class); //users.index
    // Route::get('barcode', [ProductController::class, 'GetProductBarcodes'])->name('products.barcode');

    //ni page yg tak pakai
    Route::resource('sections', SectionController::class); //sections.index
    Route::resource('categories', CategoryController::class); //categories.index
    Route::resource('subcategories', SubCategoryController::class); //subcategories.index
    //Route::resource('orders', OrderController::class);

    // export to pdf
    Route::get('/report/export/{filter}', [ReportExportController::class, 'export'])->name('report.export');


    Route::get('/reports', function () {
        return view('reports.index');
    })->name('reports.index');


    Route::get('/report/export/csv/{filter}', [ReportExportController::class, 'exportCsv'])->name('report.export.csv');


    Route::get('profit', [ProfitController::class, 'index'])->name('profit.index');//profit.index

    Route::get('/admin/cashier-profit/{id}', [MyProfitController::class, 'adminView'])->name('admin.cashier.profit');

    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');//transactions.index


});

Route::middleware(['auth', 'check.role:1,2'])->group(function () {
    // for cashier and admin
    Route::resource('/orders', OrderController::class); //orders.index
    Route::get('barcode', [ProductController::class, 'GetProductBarcodes'])->name('products.barcode');
    Route::get('/myprofit', [MyProfitController::class, 'index'])->name('myprofit.index');
     
    Route::get('/staff/products', Staffproduct::class)->name('staff.products');// Staff Product Page
});


//Route::middleware(['auth', 'check.role:2'])->group(function () {
    // for cashier Only
   
//});

Route::post('/products/addstock/{id}', [ProductController::class, 'addStock'])->name('products.addstock');

