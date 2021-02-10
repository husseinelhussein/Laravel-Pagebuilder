<?php
use Illuminate\Support\Facades\Route;
use HansSchouten\LaravelPageBuilder\LaravelPageBuilder;

Route::any( '/themes/pagebuilder' . '{any}', function() {

    $builder = new LaravelPageBuilder(config('pagebuilder'));
    $builder->handlePageBuilderAssetRequest();

})->where('any', '.*');