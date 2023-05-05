<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

// Route::get('/', function () {
//     return view('auth.login');
// });

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/', 'HomeController@index');


Route::get('/page', function () {
    return view('page');
});

Route::get('/dashboard', 'HomeController@dashboard');

// Admin Panel
Route::group(['prefix' => 'admin', 'middleware' => ['auth']], function () {

    //Admin
    Route::get('/', 'Admin\AdminController@dashboard');

    //Categories
    Route::get('/categories', 'CategoriesController@index');
    Route::get('categories/create', 'CategoriesController@create');
    Route::post('categories/add', 'CategoriesController@store');
    Route::get('categories/{category_id}/edit', 'CategoriesController@edit');
    Route::post('categories/update', 'CategoriesController@update');
    Route::post('categories/delete', 'CategoriesController@destroy');

    //Services
    Route::get('/services', 'ServicesController@index');
    Route::get('services/create', 'ServicesController@create');
    Route::post('services/add', 'ServicesController@store');
    Route::get('services/{service_id}/edit', 'ServicesController@edit');
    Route::post('services/update', 'ServicesController@update');
    Route::post('services/delete', 'ServicesController@destroy');

    // Running Services
    Route::post('/running/services/add', 'ServicesController@addRunningService');

    //Products
    Route::get('/products', 'ProductsController@index');
    Route::get('products/create', 'ProductsController@create');
    Route::post('products/add', 'ProductsController@store');
    Route::get('products/{product_id}/edit', 'ProductsController@edit');
    Route::post('products/update', 'ProductsController@update');
    Route::post('products/delete', 'ProductsController@destroy');

    //Package
    Route::get('/package', 'PackageController@index');
    Route::get('package/create', 'PackageController@create');
    Route::post('package/add', 'PackageController@store');
    Route::get('package/{package_id}/edit', 'PackageController@edit');
    Route::post('package/update', 'PackageController@update');
    Route::post('package/delete', 'PackageController@destroy');

    //Clients
    Route::get('/clients', 'ClientsController@index');
    Route::get('/clients/lvp/get', 'ClientsController@getClientData');


    Route::get('clients/create', 'ClientsController@create');
    Route::post('clients/add', 'ClientsController@store');
    Route::get('clients/{client_id}/edit', 'ClientsController@edit');
    Route::post('clients/update', 'ClientsController@update');
    Route::post('clients/delete', 'ClientsController@destroy');
    Route::get('client/services', 'ClientsController@clientServicesList');
    Route::post('client/services/add', 'ClientsController@clientServicesAdd');
    Route::post('client/service/delete', 'ClientsController@clientServicesDestroy');
    Route::post('client/{client_id}/service/start', 'ClientsController@startService');
    Route::post('client/{client_id}/service/end', 'ClientsController@endService');
    Route::post('client/settle-debit', 'ClientsController@clientSettleDebitAmount');
    Route::post('client/phone-number', 'ClientsController@checkClientPhoneNumber');
    Route::post('client/service/delete', 'ClientsController@clientRunningServiceDelete');
    Route::post('client/running-services', 'ClientsController@getClientRunningServices');
    Route::post('client/reset', 'ClientsController@postResetClientDetail');

    //Employees
    Route::get('/employees', 'EmployeesController@index');
    Route::get('employee/create', 'EmployeesController@create');
    Route::post('employee/add', 'EmployeesController@store');
    Route::get('employee/{employee_id}/edit', 'EmployeesController@edit');
    Route::post('employee/update', 'EmployeesController@update');
    Route::post('employee/delete', 'EmployeesController@destroy');
    Route::get('employee/{employee_id}/view', 'EmployeesController@show');
    Route::get('print/employee/{employee_id}/reports', 'EmployeesController@printEmployeeReports');

    //Invoices
    Route::get('/invoices', 'InvoiceController@index');
    Route::get('invoices/create', 'InvoiceController@create');

    Route::get('invoices/lvp/get', 'InvoiceController@getInvoiceData');

    Route::post('invoices/add', 'InvoiceController@store');
    Route::get('invoices/{invoice_number}/edit', 'InvoiceController@edit');
    Route::post('invoices/update', 'InvoiceController@update');
    Route::post('invoices/delete', 'InvoiceController@destroy');
    Route::get('invoices/{invoice_number}/print', 'InvoiceController@show');
    Route::post('invoice/clients/get', 'InvoiceController@getInvoiceClients');
    Route::post('invoice/items/get', 'InvoiceController@getInvoiceItems');
    Route::post('invoice/change/billing-date', 'InvoiceController@changeInvoiceBillingDate');

    //Reports
    Route::get('/reports', 'ReportsController@index');
    Route::get('/print/reports', 'ReportsController@show');

    //Expenses
    Route::get('/expenses', 'ExpensesController@index');
    Route::post('/expenses/add', 'ExpensesController@store');
    Route::get('/expenses/{expense_id}/edit', 'ExpensesController@edit');
    Route::post('/expenses/update', 'ExpensesController@update');
    Route::post('/expenses/delete', 'ExpensesController@destroy');
    Route::get('/print/expenses', 'ExpensesController@show');

    //Password
    Route::get('/change/password', 'UserController@show');
    Route::post('/change/password', 'UserController@update');
});
