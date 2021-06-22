<?php

namespace HansSchouten\LaravelPageBuilder;

use HansSchouten\LaravelPageBuilder\Contracts\ThemeContract;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use PHPageBuilder\Contracts\PageContract;
use PHPageBuilder\Modules\GrapesJS\PageBuilder;
use PHPageBuilder\Modules\GrapesJS\PageRenderer;
use PHPageBuilder\Modules\GrapesJS\Thumb\ThumbGenerator;
use PHPageBuilder\Modules\GrapesJS\Upload\Uploader;
use HansSchouten\LaravelPageBuilder\Repositories\PageRepository;
use HansSchouten\LaravelPageBuilder\Repositories\PageTranslationRepository;
use HansSchouten\LaravelPageBuilder\Repositories\UploadRepository;
use PHPageBuilder\UploadedFile;

class NativePageBuilderWrapper extends PageBuilder
{
    /** @var ThemeContract */
    protected $theme;

    public function __construct()
    {
        parent::__construct();
        $this->loadTheme();
    }


    /**
     * loads Theme class from the active theme folder.
     * @return void|null
     */
    protected function loadTheme(){
        $themes_folder = phpb_config('theme.folder');
        $file = $themes_folder . '/' . phpb_config('theme.active_theme') . '/Theme.php';
        if(file_exists($file)){
            require_once $file;
            $theme_namespace = $this->getNamespace();
            $theme_class = $theme_namespace . '\Theme';
            $this->theme = new $theme_class(phpb_config('theme'), phpb_config('theme.active_theme'));
            return;
        }
        $this->theme = new ThemeWrapper(phpb_config('theme'), phpb_config('theme.active_theme'));
        return null;
    }

    /**
     * Return the namespace to the folder of the current theme.
     *
     * @return string
     */
    protected function getNamespace()
    {
        $themesPath = phpb_config('theme.folder');
        $themesFolderName = basename($themesPath);
        $currentTheme = '/' . phpb_config('theme.active_theme');
        $namespacePath = $themesFolderName . str_replace($themesPath, '', $currentTheme);

        // convert each character after a - to uppercase
        $namespace = implode('-', array_map('ucfirst', explode('-', $namespacePath)));
        // convert each character after a _ to uppercase
        $namespace = implode('_', array_map('ucfirst', explode('-', $namespace)));
        // convert each character after a / to uppercase
        $namespace = implode('/', array_map('ucfirst', explode('/', $namespace)));
        // remove all dashes
        $namespace = str_replace('-', '', $namespace);
        // remove all underscores
        $namespace = str_replace('_', '', $namespace);
        // replace / by \
        $namespace = str_replace('/', '\\', $namespace);

        return $namespace;
    }

    /**
     * @param $route
     * @param $action
     * @param PageContract|null $page
     * @return bool|View
     * @throws \Exception
     */
    public function handleRequest($route, $action, PageContract $page = null)
    {
        phpb_set_in_editmode();
        if ($route === 'thumb_generator') {
            $thumbGenerator = new ThumbGenerator($this->theme);
            return $thumbGenerator->handleThumbRequest($action);
        }
        if ($route && !$page) {
            $pageRepository = new PageTranslationRepository();
            $pageTranslation = $pageRepository->findWhere('route', $route);
            if($pageTranslation){
                $pageTranslation = $pageTranslation[0];
                $page = $pageTranslation->getPage();
            }
        }
        if (is_null($page)) {
            $pageId = $_GET['page_id'] ?? null;
            $pageRepository = new PageRepository;
            $page = $pageRepository->findWithId($pageId);
        }
        if (! ($page instanceof PageContract)) {
            return false;
        }

        switch ($action) {
            case null:
            case 'edit':
                return $this->renderPageBuilder($page);
            case 'store':
                if (isset($_POST) && isset($_POST['data'])) {
                    $data = json_decode($_POST['data'], true);
                    $this->updatePage($page, $data);
                    exit();
                }
                break;
            case 'upload':
                if (isset($_FILES)) {
                    $this->handleFileUpload();
                }
                break;
            case 'upload_delete':
                if (isset($_POST['id'])) {
                    return $this->handleFileDelete();
                }
                break;
            case 'renderBlock':
                if (isset($_POST['language']) && isset($_POST['data']) && isset(phpb_active_languages()[$_POST['language']])) {
                    $this->renderPageBuilderBlock($page, $_POST['language'], json_decode($_POST['data'], true));
                    exit();
                }
                break;
            case 'renderLanguageVariant':
                if (isset($_POST['language']) && isset($_POST['data']) && isset(phpb_active_languages()[$_POST['language']])) {
                    $this->renderLanguageVariant($page, $_POST['language'], json_decode($_POST['data'], true));
                    exit();
                }
                break;
        }

        return false;
    }

    /**
     * Overrides the original method to return a blade view instead.
     * @param PageContract $page
     * @throws \Exception
     */
    public function renderPageBuilder(PageContract $page)
    {
        phpb_set_in_editmode();

        // init variables that should be accessible in the view
        $pageBuilder = $this;
        $pageRenderer = phpb_instance(PageRendererWrapper::class, [$this->theme, $page, true]);

        // create an array of theme blocks and theme block settings for in the page builder sidebar
        $blocks = [];
        $blockSettings = [];
        foreach ($this->theme->getThemeBlocks() as $themeBlock) {
            $slug = phpb_e($themeBlock->getSlug());
            $adapter = new BlockAdapterWrapper($pageRenderer, $themeBlock);
            $blockSettings[$slug] = $adapter->getBlockSettingsArray();

            if ($themeBlock->get('hidden') !== true) {
                $blocks[$slug] = $adapter->getBlockManagerArray();
            }
        }

        // create an array of all uploaded assets
        $assets = [];
        $multi_saas_id = $this->getMultiSaasId();
        if ($multi_saas_id) {
            $uploaded_assets = (new UploadRepository)->findWhere('multi_saas_id', $multi_saas_id);
        }
        else {
            $uploaded_assets = (new UploadRepository)->getAll();
        }
        foreach ($uploaded_assets as $file) {
            $src = phpb_config('general.uploads_url') . '/' . $file->server_file;
            $assets[] = [
                'src' => $src,
                'public_id' => $file->public_id
            ];
        }
        $vars = [
          'pageBuilder' => $this,
          'page' => $page,
          'blocks' => $blocks,
          'blockSettings' => $blockSettings,
          'pageRenderer' => $pageRenderer,
          'assets' => $assets,
          'theme' => $this->theme,
        ];
        return view('pagebuilder::layout', $vars);
    }

    /**
     * Overrides the function to return PageRendererWrapper
     * @param PageContract $page
     * @param null $language
     * @return string
     */
    public function renderPage(PageContract $page, $language = null): string
    {
        /** @var PageRendererWrapper $pageRenderer */
        $pageRenderer = phpb_instance(PageRendererWrapper::class, [$this->theme, $page]);
        if (! is_null($language)) {
            $pageRenderer->setLanguage($language);
        }
        return $pageRenderer->render();
    }

    public function renderPageBuilderBlock(PageContract $page, string $language, $blockData = [])
    {
        phpb_set_in_editmode();

        $blockData = is_array($blockData) ? $blockData : [];
        $page->setData(['data' => $blockData], false);

        /** @var PageRendererWrapper $pageRenderer */
        $pageRenderer = phpb_instance(PageRendererWrapper::class, [$this->theme, $page, true]);
        $pageRenderer->setLanguage($language);
        echo $pageRenderer->parseShortcodes($blockData['html'], $blockData['blocks']);
    }

    public function handleFileUpload()
    {
        $file = request()->file('files');
        $originalMime = $file->getMimeType();
        $originalFile = $file->getClientOriginalName();
        $publicId = sha1(uniqid(rand(), true));
        $uploadRepository = new UploadRepository;
        $multi_saas_id = $this->getMultiSaasId();
        $folder = "uploads";
        if($multi_saas_id){
            $folder .= '/' . $multi_saas_id;
        }
        $res = $file->store($folder);
        /** @var UploadedFile $uploadedFile */
        $uploadedFile = $uploadRepository->create([
            'multi_saas_id' => $multi_saas_id,
            'public_id' => $publicId,
            'original_file' => $originalFile,
            'mime_type' => $originalMime,
            'server_file' => $res
        ]);
        $src = phpb_config('general.uploads_url') . '/' . $res;
        echo json_encode([
            'data' => [
                'src' => $src,
                'type' => 'image'
            ]
        ]);
        exit();
    }

    public function handleFileDelete()
    {
        $uploadRepository = new UploadRepository;
        $uploadedFileResult = $uploadRepository->findWhere('public_id', $_POST['id']);
        if(empty($uploadedFileResult)){
            $res = [
                'success' => false,
                'message' => 'File not found'
            ];
            return response()->json($res);
        }
        $uploadedFile = $uploadedFileResult[0];
        $stop = null;
        $deleted = false;
        $message = '';
        try {
            $deleted = Storage::disk(env('FILESYSTEM_DRIVER'))->delete($uploadedFile->server_file);
        } catch (\Exception $e) {
            $message = $e->getMessage();
        }
        $uploadRepository->destroy($uploadedFile->id);
        $res = [
            'success' => $deleted,
            'message' => $message,
        ];
        return response()->json($res);
    }

    protected function getMultiSaasId(){
        $is_multi_saas = phpb_config('general.is_multi_saas');
        if(!$is_multi_saas){
            return null;
        }
        $multi_saas_id = phpb_config('general.multi_saas_id');
        if(function_exists($multi_saas_id)){
            $multi_saas_id = call_user_func($multi_saas_id);
        }
        return $multi_saas_id;
    }

}
