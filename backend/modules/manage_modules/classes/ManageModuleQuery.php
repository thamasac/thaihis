<?php
namespace backend\modules\manage_modules\classes;
use backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\ezforms2\classes\EzfUiFunc;
use backend\modules\ezforms2\classes\EzfFunc;
use Yii;
class ManageModuleQuery{
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
        $modelEzf = EzfQuery::getEzformOne($ezf_id);
        Yii::$app->session['show_varname'] = 0;
        Yii::$app->session['ezf_input'] = EzfQuery::getInputv2All();
        $userProfile = \common\modules\user\models\User::findOne(['id' => '1'])->profile;
        $modelFields = \backend\modules\ezforms2\models\EzformFields::find()
                ->where('ezf_id = :ezf_id', [':ezf_id' => $modelEzf->ezf_id])
                ->orderBy(['ezf_field_order' => SORT_ASC])
                ->all();
        $model = EzfFunc::setDynamicModel($modelFields, $modelEzf->ezf_table, Yii::$app->session['ezf_input'], Yii::$app->session['show_varname']);
        $targetReset = false;
        //ขั้นตอนกรอกข้อมูลสำคัญ
        $evenFields = EzfFunc::getEvenField($modelFields);
        $special = isset($evenFields['special']) && !empty($evenFields['special']);
        $fieldSpecial = EzfFunc::checkSpecial($model, $evenFields, $targetReset);
        if ($model->id) {
            $dataTarget = EzfQuery::getTarget($modelEzf->ezf_table, $model->id);
        } else {
            $dataTarget = [];
        }
        //เพิ่มและแก้ไขข้อมูล system
        $model->attributes = EzfUiFunc::setSystemProperty($model, $target, $dataTarget, $modelEzf->ezf_table, '', $fieldSpecial, $special, $userProfile, NULL, 0);
        if ($dataTarget && $type == "main") {
//            $model->id = $dataid;
            self::DeleteData($modelEzf->ezf_table, $dataid);
        }
        if (!empty($initdata)) {//กำหนดค่าเริ่มต้น
            $model->attributes = $initdata;
            if (isset($initdata['rstat'])) {
                $model->rstat = $initdata['rstat'];
            } else {
                $model->rstat = 1;
            }
            $model->user_update = $userProfile->user_id;
            $model->update_date = new \yii\db\Expression('NOW()');
            $result = EzfUiFunc::saveData($model, $modelEzf->ezf_table, $modelEzf->ezf_id, $model->id);
            return $result;
        }
        return $model;
    }
    public static function DeleteData($ezf_table, $dataid) {
        $sql = "delete FROM $ezf_table WHERE id = :id AND rstat not in(0,3)";
        Yii::$app->db->createCommand($sql, [':id' => $dataid])->execute();
    }
    
    /**
     * 
     * @param type $ezm_id module id
     * @return query module by id
     */
    public static function getModuleById($ezm_id) {
        $model = \backend\modules\ezmodules\models\Ezmodule::findOne($ezm_id);
        return $model;
    }
}
