<?php

namespace HansSchouten\LaravelPageBuilder\Http\Controllers\Front;

use HansSchouten\LaravelPageBuilder\Http\Controllers\Controller;
use HansSchouten\LaravelPageBuilder\LaravelPageBuilder;
use Illuminate\Http\Request;

class AssetsController  extends Controller {

    public function assets(Request $request, $path){
        $builder = new LaravelPageBuilder(config('pagebuilder'));
        return $builder->getAsset($request, $path);
    }
}