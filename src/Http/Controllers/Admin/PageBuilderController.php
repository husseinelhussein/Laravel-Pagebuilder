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
        $page_id = $request->query('page_id');
        $page_id = (int) $page_id;
        if(!$page_id && !$route){
            abort(404);
        }
        $res = $pageBuilder->handleRequest($route, $action);
        if(!$res){
            abort(404);
        }
        return $res;
    }
}