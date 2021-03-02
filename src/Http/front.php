<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => ['web'],
    'namespace' => 'HansSchouten\LaravelPageBuilder\Http\Controllers\Front'
], function () {
    Route::get( 'themes/pagebuilder/{path}', 'AssetsController@assets')->where('path', '.*');
    Route::get( 'uploads/pagebuilder/{path}', 'AssetsController@uploadedAssets')->where('path', '.*');
});