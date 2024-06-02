<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::group(['middleware' => 'auth'], function () {
    //Print Barcode
    Route::get('/products/print-barcode', 'BarcodeController@printBarcode')->name('barcode.print');
    
    //Product
    Route::resource('products', 'ProductController');


    Route::get('/products/list/{site}', 'ProductController@indexSite')->name('index.site');


    Route::get('/products/create/{site}', 'ProductController@createSite')->name('create.site');


    //Product Category
    Route::resource('product-categories', 'CategoriesController')->except('create', 'show');
});

