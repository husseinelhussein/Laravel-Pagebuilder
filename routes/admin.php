<?php

use Illuminate\Support\Facades\Route;
use HansSchouten\LaravelPageBuilder\LaravelPageBuilder;

Route::group(['middleware' => ['web','admin'], 'prefix' => 'admin/pagebuilder'], function () {
    // handle all website manager requests
    Route::any( '/', function() {
        $pageBuilder = new \PHPageBuilder\Modules\GrapesJS\PageBuilder();
        $pageBuilder->handleRequest(null, 'edit');
    });
});
