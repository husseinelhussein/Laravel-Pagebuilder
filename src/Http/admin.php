<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => ['admin'],
    'prefix' => 'admin/pagebuilder',
    'namespace' => 'HansSchouten\LaravelPageBuilder\Http\Controllers\Admin'
], function () {
    // handle all website manager requests
    Route::any( '/', 'PageBuilderController@adminPage')->name('admin.page_builder.edit');
});
