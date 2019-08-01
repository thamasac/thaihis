<?php
namespace common\modules\user\classes;
use Yii;
use backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\ezforms2\classes\EzfFunc;
use backend\modules\ezforms2\models\EzformFields;
use backend\modules\ezforms2\classes\EzfUiFunc;
use backend\modules\ezforms2\models\TbdataAll;
use appxq\sdii\helpers\SDHtml;
/**
 * Description of UserExtendFunc
 *
 * @author damasa
 */
class UserExtendFunc {
    //put your code here
    /**
     * function backgroundInsert
     *
     * @param string @ezf_id ezf_id
     * @param string $dataid dataid
     * @param string $target target
     * @param array $initdata $data['visit_no']
     */
    public static function backgroundInsert($ezf_id, $dataid, $target, $initdata = [], $post = null) {
        //$dataid = '';

        $modelEzf = EzfQuery::getEzformOne($ezf_id);
        Yii::$app->session['show_varname'] = 0;
        Yii::$app->session['ezf_input'] = EzfQuery::getInputv2All();
        $userProfile = \common\modules\user\models\User::findOne(['id' => '1'])->profile;
        $modelFields = \backend\modules\ezforms2\models\EzformFields::find()
                ->where('ezf_id = :ezf_id', [':ezf_id' => $modelEzf->ezf_id])
                ->orderBy(['ezf_field_order' => SORT_ASC])
                ->all();

        $model = EzfFunc::setDynamicModel($modelFields, $modelEzf->ezf_table, Yii::$app->session['ezf_input'], Yii::$app->session['show_varname']);
        //$model = EzfUiFunc::loadData($model, $modelEzf->ezf_table, $dataid);
        $targetReset = false;
        if (!isset($model->id)) {// ถ้ามี new record ที่คนUserนั้นสร้างไว้ ให้ใช้ record นั้น
            $modelNewRecord = EzfUiFunc::loadNewRecord($model, $modelEzf->ezf_table, $userProfile->user_id);
            if ($modelNewRecord) {
                $targetReset = true;
                $model = $modelNewRecord;
            }
        }

        //ขั้นตอนกรอกข้อมูลสำคัญ
        $evenFields = EzfFunc::getEvenField($modelFields);
        $special = isset($evenFields['special']) && !empty($evenFields['special']);

        if (isset($evenFields['target']) && !empty($evenFields['target'])) { //มีเป้าหมาย
            if ($targetReset) {
                $model[$evenFields['target']['ezf_field_name']] = '';
            }

            $modelEzfTarget = EzfQuery::getEzformOne($evenFields['target']['ref_ezf_id']);
            $target = ($target == '') ? $model[$evenFields['target']['ezf_field_name']] : $target;
            $dataTarget = EzfQuery::getTargetNotRstat($modelEzfTarget->ezf_table, $target);

            if ($dataTarget) {//เลือกเป้าหมายแล้ว
                if (isset($modelEzf['unique_record']) && $modelEzf['unique_record'] == 2) {
                    $unique = EzfUiFunc::loadUniqueRecord($model, $modelEzf->ezf_table, $target);
                    //\appxq\sdii\utils\VarDumper::dump($unique);
                    if ($unique) {
                        return $this->renderAjax('_error', [
                                    'ezf_id' => $ezf_id,
                                    'dataid' => $model->id,
                                    'modelEzf' => $modelEzf,
                                    'msg' => Yii::t('ezform', 'This form only records 1 record.'),
                        ]);
                    }
                }

                //เพิ่มและแก้ไขข้อมูล system
                $model->attributes = EzfUiFunc::setSystemProperty($model, $target, $dataTarget, $modelEzf->ezf_table, $evenFields['target']['ezf_field_name'], '', $special, $userProfile, $evenFields['target'], 0);
            } else { //ฟอร์มค้นหาเป้าหมาย
                return false;
            }
        } else {// ไม่มีเป้าหมาย
            $fieldSpecial = EzfFunc::checkSpecial($model, $evenFields, $targetReset);

            if ($model->id) {
                $dataTarget = EzfQuery::getTarget($modelEzf->ezf_table, $model->id);
            } else {
                $dataTarget = [];
            }

            //เพิ่มและแก้ไขข้อมูล system
            $model->attributes = EzfUiFunc::setSystemProperty($model, $target, $dataTarget, $modelEzf->ezf_table, '', $fieldSpecial, $special, $userProfile, NULL, 0);
        }

        if (!empty($initdata)) {//กำหนดค่าเริ่มต้น
            if ($post) {
                $model->load($post);
            }
            $model->attributes = $initdata;

            if (isset($initdata['rstat'])) {
                $model->rstat = $initdata['rstat'];
            } else {
                $model->rstat = 1;
            }
            $model->user_update = $userProfile->user_id;
            $model->update_date = new \yii\db\Expression('NOW()');
            $result = EzfUiFunc::saveData($model, $modelEzf->ezf_table, $modelEzf->ezf_id, $model->id);
            //$result = self::saveDataNotEvent($model, $modelEzf->ezf_table, $modelEzf->ezf_id, $model->id);
            return $result;
        }

        return $model;
    }
}
