<?php
namespace HansSchouten\LaravelPageBuilder\Repositories;
use Illuminate\Support\Facades\DB;
use PHPageBuilder\Contracts\PageContract;
use PHPageBuilder\Repositories\PageRepository as BaseRepository;
use Exception;

class PageRepository extends BaseRepository
{
    protected function createInstances(array $records)
    {
        return parent::createInstances($records); // TODO: Change the autogenerated stub
    }
    public function findWithId($id)
    {
        $table = config('pagebuilder.storage.database.prefix') . 'pages';
        $multi_saas_id = $this->getMultiSaasId();
        $res = DB::table($table)->where(['id' => $id, 'multi_saas_id' => $multi_saas_id])->first();
        $res = collect($res)->toArray();
        if(!$res){
            return null;
        }
        return $this->createInstance([$res]);
    }
    public function findWhere($column, $value)
    {
        $table = config('pagebuilder.storage.database.prefix') . 'pages';
        $multi_saas_id = $this->getMultiSaasId();
        $res = DB::table($table)->where(['multi_saas_id' => $multi_saas_id, $column => $value])->first();
        if(!$res){
            return null;
        }
        $res = collect($res)->toArray();
        return $this->createInstances([$res]);
    }

    /**
     * @inheritDoc
     */
    public function create(array $data)
    {
        foreach (['name', 'layout'] as $field) {
            if (! isset($data[$field]) || ! is_string($data[$field])) {
                return false;
            }
        }
        $multi_saas_id = $this->getMultiSaasId();
        $page = $this->superCreate([
            'name' => $data['name'],
            'layout' => $data['layout'],
            'meta' => isset($data['meta'])? $data['meta']: null,
            'multi_saas_id' => $multi_saas_id,
        ]);
        if (! ($page instanceof PageContract)) {
            throw new Exception("Page not of type PageContract");
        }
        return $this->replaceTranslations($page, $data);
    }

    /** Overrides parent to use PageTranslationRepository and add multi_saas_id
     * @param PageContract $page
     * @param array $data
     * @return bool
     */
    protected function replaceTranslations(PageContract $page, array $data)
    {
        $activeLanguages = phpb_active_languages();
        foreach (['title', 'route'] as $field) {
            foreach ($activeLanguages as $languageCode => $languageTranslation) {
                if (! isset($data[$field][$languageCode])) {
                    return false;
                }
            }
        }

        $pageTranslationRepository = new PageTranslationRepository();
        $pageTranslationRepository->destroyWhere(phpb_config('page.translation.foreign_key'), $page->getId());
        foreach ($activeLanguages as $languageCode => $languageTranslation) {
            $pageTranslationRepository->create([
                phpb_config('page.translation.foreign_key') => $page->getId(),
                'locale' => $languageCode,
                'title' => $data['title'][$languageCode],
                'route' => $data['route'][$languageCode],
                'multi_saas_id' => $this->getMultiSaasId(),
            ]);
        }

        return true;
    }

    protected function superCreate($data){
        $columns = array_keys($data);
        foreach ($columns as &$column) {
            $column = $this->removeNonAlphaNumeric($column);
        }
        $columns = implode(', ', $columns);
        $questionMarks = implode(', ', array_fill(0, sizeof($data), '?'));

        $this->db->query(
            "INSERT INTO {$this->table} ({$columns}) VALUES ({$questionMarks})",
            array_values($data)
        );

        $id = $this->db->lastInsertId();
        if ($id) {
            return $this->findWithId($id);
        }
        return null;
    }

    /**
     * @inheritDoc
     */
    public function update($page, array $data)
    {
        foreach (['name', 'layout'] as $field) {
            if (! isset($data[$field]) || ! is_string($data[$field])) {
                return false;
            }
        }

        $page->invalidateCache();
        $this->replaceTranslations($page, $data);

        return parent::update($page, [
            'name' => $data['name'],
            'layout' => $data['layout'],
            'meta' => isset($data['meta'])? $data['meta']: null,
        ]);
    }

    protected function getMultiSaasId(){
        $multi_saas_id = phpb_config('general.multi_saas_id');
        if(function_exists($multi_saas_id)){
            $multi_saas_id = call_user_func($multi_saas_id);
        }
        return $multi_saas_id;
    }
}