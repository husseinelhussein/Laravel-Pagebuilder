<?php

namespace HansSchouten\LaravelPageBuilder\Http\Controllers\Admin;

use HansSchouten\LaravelPageBuilder\Http\Controllers\Controller;
use HansSchouten\LaravelPageBuilder\NativePageBuilderWrapper;
use Illuminate\Http\Request;

class PageBuilderController  extends Controller {

    public function __construct()
    {
        $this->middleware('admin');
    }

    public function adminPage(Request $request){
        $pageBuilder = new NativePageBuilderWrapper();
        $route = $request->query('route')?? null;
        $action = $request->query('action')?? 'edit';
        return $pageBuilder->handleRequest($route, $action);
    }
}