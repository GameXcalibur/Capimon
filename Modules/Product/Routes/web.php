<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::group(['middleware' => 'auth'], function () {
    
    //Product
    Route::resource('products', 'ProductController');


    Route::get('/products/list/{site}', 'ProductController@indexSite')->name('index.site');


    Route::get('/products/create/{site}', 'ProductController@createSite')->name('create.site');


    //Product Category
    Route::resource('product-categories', 'CategoriesController')->except('create', 'show');
});

