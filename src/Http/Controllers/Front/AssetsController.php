<?php

namespace HansSchouten\LaravelPageBuilder\Http\Controllers\Front;

use HansSchouten\LaravelPageBuilder\Http\Controllers\Controller;
use HansSchouten\LaravelPageBuilder\LaravelPageBuilder;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AssetsController  extends Controller {

    public function assets(Request $request, $path){
        $builder = new LaravelPageBuilder(config('pagebuilder'));
        return $builder->getAsset($request, $path);
    }

    public function uploadedAssets(Request $request, $path){
        $file = null;
        try {
            $file = Storage::disk(env('FILESYSTEM_DRIVER'))->get($path);
        } catch (FileNotFoundException $e) {
            abort(404);
        }
        return $file;
    }
}