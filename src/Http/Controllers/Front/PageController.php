<?php
namespace HansSchouten\LaravelPageBuilder\Http\Controllers\Front;
use HansSchouten\LaravelPageBuilder\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class PageController extends Controller{

    public function view($slug){
        $page = $this->cmsRepository->findByUrlKeyOrFail($urlKey);

        $image='';
        if(!empty($page->path_hero_image )){
            $image = '<a href="" target="_blank" class="page-hero-image">
            <img src="' . getenv('WASSABI_STORAGE')."/".$page->path_hero_image . '"/>
        </a>';
        }

        $page->html_content = $image . $page->html_content;
        $hero_image_wasabi = null;
        if($page->path_hero_image && strpos($page->path_hero_image, 'http') === false){
            $hero_image_wasabi = Storage::disk('wassabi_public')->url($page->path_hero_image);
        }
        return view('shop::cms.page',compact('page','hero_image_wasabi'));
    }
}