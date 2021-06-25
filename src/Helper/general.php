<?php
if(!function_exists('getMultiSaasId')){
    function getMultiSaasId() {
        $is_multi_saas = phpb_config('general.is_multi_saas');
        if (!$is_multi_saas) {
            return null;
        }
        $multi_saas_id = phpb_config('general.multi_saas_id');
        if (function_exists($multi_saas_id)) {
            $multi_saas_id = call_user_func($multi_saas_id);
        }
        return $multi_saas_id;
    }
}
