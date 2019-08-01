<?php
namespace backend\modules\manage_modules\classes;
use backend\modules\manage_modules\classes\ManageModuleQuery; 
class ManageModuleFunc {
    /**
     * 
     * @param type $ezf_id  Ezform id
     * @param type $dataid id zdata_
     * @param type $target 
     * @param type $initdata data in zdata_ object ['name'=>1]
     * @param type $type  string '' or 'main'
     * @return insert background ezform
     */
    public static function backgroundInsert($ezf_id, $dataid, $target, $initdata = [], $type) {
        return ManageModuleQuery::backgroundInsert($ezf_id, $dataid, $target, $initdata, '');
    }
    /**
     * 
     * @param type $ezm_id module id
     * @return query module by id
     */
    public static function getModuleById($ezm_id) {
        return ManageModuleQuery::getModuleById($ezm_id);
    }
     
}
