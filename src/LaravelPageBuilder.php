<?php

namespace HansSchouten\LaravelPageBuilder;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use PHPageBuilder\PHPageBuilder;

class LaravelPageBuilder extends PHPageBuilder
{

    public function getAsset(Request $request, $path){
        $stop = null;
        // remove the theme name from path
        $parts = explode('/', $path);
        if(count($parts) < 2){
            abort(404);
        }
        $ext = explode('.', $parts[count($parts) -1]);
        $ext = last($ext);
        $contentTypes = [
            'js' => 'application/javascript; charset=utf-8',
            'css' => 'text/css; charset=utf-8',
            'png' => 'image/png',
            'jpg' => 'image/jpeg'
        ];
        if (! in_array($ext, array_keys($contentTypes))){
            abort(404);
        }

        $theme_name = $parts[0];
        $asset_path = substr($path, strlen($theme_name) +1);
        $requestedFile = phpb_config('theme.folder') . '/' . $theme_name . '/public/' . $asset_path;
        $requestedFile = realpath($requestedFile);
        if(!$requestedFile){
            abort(404, 'File not found!');
        }
        $file_name = last($parts);
        $headers = [
            'Content-Type' => $contentTypes[$ext],
            'Content-Disposition' => 'inline; filename="' . $file_name . '"',
            'Expires' => '0',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Content-Length:' => filesize($requestedFile),
        ];
        return response()->file($requestedFile, $headers);
    }
}
