<?php

namespace HansSchouten\LaravelPageBuilder\Repositories;

use Illuminate\Support\Facades\DB;
use PHPageBuilder\Contracts\PageTranslationRepositoryContract;
use PHPageBuilder\Repositories\PageTranslationRepository as BaseRepo;
class PageTranslationRepository extends BaseRepo implements PageTranslationRepositoryContract
{
    public function findWhere($column, $value)
    {
        $table = config('pagebuilder.storage.database.prefix') . 'page_translations';
        $is_multi_saas = phpb_config('general.is_multi_saas');
        $where = [
            $column => $value,
        ];
        if ($is_multi_saas) {
            $where['multi_saas_id'] = getMultiSaasId();
        }
        $res = DB::table($table)->where($where)->first();
        if(!$res){
            return null;
        }
        $res = collect($res)->toArray();
        return $this->createInstances([$res]);
    }
}
