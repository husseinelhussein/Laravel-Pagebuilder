<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => ['web'],
    'prefix' => 'themes/pagebuilder',
    'namespace' => 'HansSchouten\LaravelPageBuilder\Http\Controllers\Front'
], function () {
    // Handle all assets requests
    Route::get( '/{path}', 'AssetsController@assets')->where('path', '.*');
});

Route::group([
    'middleware' => ['web'],
    'prefix' => 'page',
    'namespace' => 'HansSchouten\LaravelPageBuilder\Http\Controllers\Front'
], function () {
    // route for a page resource:
    Route::get( '/{id}', 'PageController@view');
});