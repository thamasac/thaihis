<?php

namespace backend\modules\thaihis\controllers;

use Yii;
use backend\modules\thaihis\classes\ThaiHisQuery;

class ConfigsController extends \yii\web\Controller {

    public function actionGetFormRef() {
        $ezf_id = Yii::$app->request->post('ezf_id', 0);
        $name = Yii::$app->request->post('name', 0);
        $value_ref = Yii::$app->request->post('value_ref', 0);
        $multiple = Yii::$app->request->post('multiple', 0);
        $id = Yii::$app->request->post('id');

        $dataForm = ThaiHisQuery::getEzformRefAll($ezf_id);

        return $this->renderAjax('_ref_form', [
                    'ezf_id' => $ezf_id,
                    'dataForm' => \yii\helpers\ArrayHelper::map($dataForm, 'id', 'name'),
                    'multiple' => $multiple,
                    'value' => $value_ref,
                    'name' => $name,
                    'id' => $id,
        ]);
    }

    public function actionGetFormRef2() {
        $ezf_id = Yii::$app->request->post('ezf_id', 0);
        $name = Yii::$app->request->post('name', 0);
        $value_ref = Yii::$app->request->post('value_ref', 0);
        $value_merge = Yii::$app->request->post('value_merge', 0);
        $multiple = Yii::$app->request->post('multiple', 0);
        $id = Yii::$app->request->post('id');
        if ($value_merge == 0)
            $value_merge = $value_ref;

        $dataForm = ThaiHisQuery::getEzformRefAll2($ezf_id, $value_merge);

        return $this->renderAjax('_ref_form', [
                    'ezf_id' => $ezf_id,
                    'dataForm' => \yii\helpers\ArrayHelper::map($dataForm, 'id', 'name'),
                    'multiple' => $multiple,
                    'value' => $value_ref,
                    'name' => $name,
                    'id' => $id,
        ]);
    }

    public function actionGetFieldsForms() {
        $ezf_id = Yii::$app->request->post('ezf_id', 0);
        $main_ezf_id = Yii::$app->request->post('main_ezf_id', 0);
        //\appxq\sdii\utils\VarDumper::dump($ezf_id);
        $name = Yii::$app->request->post('name', 0);
        $value = Yii::$app->request->post('value', 0);
        $multiple = Yii::$app->request->post('multiple', 0);
        $id = Yii::$app->request->post('id');
        $dataForm = [];
        if ($ezf_id && is_array($ezf_id)) {
            foreach ($ezf_id as $value_ezf_id) {
                foreach (ThaiHisQuery::getFields($value_ezf_id) as $value_field) {
                    $dataForm[] = $value_field;
                }
            }
        }

        if ($main_ezf_id) {
            foreach (ThaiHisQuery::getFields($main_ezf_id) as $value_field) {
                $dataForm[] = $value_field;
            }
        }
        return $this->renderAjax('_ref_form', [
                    'ezf_id' => $ezf_id,
                    'dataForm' => \yii\helpers\ArrayHelper::map($dataForm, 'id', 'name'),
                    'multiple' => $multiple,
                    'value' => $value,
                    'name' => $name,
                    'id' => $id,
        ]);
    }

    public function actionGetFieldsForms2() {
        $ezf_id = Yii::$app->request->post('ezf_id', 0);
        $main_ezf_id = Yii::$app->request->post('main_ezf_id', 0);
        $name = Yii::$app->request->post('name', 0);
        $value = Yii::$app->request->post('value', 0);
        $type = Yii::$app->request->post('type', null);
        $multiple = Yii::$app->request->post('multiple', 0);
        $id = Yii::$app->request->post('id');
        $dataForm = [];
        if ($ezf_id && is_array($ezf_id)) {
            foreach ($ezf_id as $value_ezf_id) {
                foreach (ThaiHisQuery::getFields2($value_ezf_id, $type) as $value_field) {
                    $dataForm[] = $value_field;
                }
            }
        }else if($ezf_id){
            foreach (ThaiHisQuery::getFields2($ezf_id, $type) as $value_field) {
                    $dataForm[] = $value_field;
                }
        }

        if ($main_ezf_id) {
            foreach (ThaiHisQuery::getFields2($main_ezf_id, $type) as $value_field) {
                $dataForm[] = $value_field;
            }
        }
        return $this->renderAjax('_ref_form', [
                    'ezf_id' => $ezf_id,
                    'dataForm' => \yii\helpers\ArrayHelper::map($dataForm, 'id', 'name'),
                    'multiple' => $multiple,
                    'value' => $value,
                    'name' => $name,
                    'id' => $id,
        ]);
    }

    public function actionAddNewtab() {
        $ezf_id = Yii::$app->request->post('ezf_id');
        $ezm_id = Yii::$app->request->post('ezm_id');
        $tabs = Yii::$app->request->post('tabs');
        $key_index = Yii::$app->request->post('key_index');
        $act = Yii::$app->request->post('act');
        $firstIndex = Yii::$app->request->post('firstIndex');

        return $this->renderAjax('../../../ezmodules/views/ezmodule-widget/assets/his_patient_visit/_tab', [
                    'ezf_id' => $ezf_id,
                    'ezm_id' => $ezm_id,
                    'tabs' => $tabs,
                    'key_index' => $key_index,
                    'firstIndex' => $firstIndex,
                    'act' => $act,
        ]);
    }

    public function actionAddNewcondition() {
        $ezf_id = Yii::$app->request->get('ezf_id');
        $ezf_id2 = Yii::$app->request->get('ezf_id2');
        $main_ezf_id = Yii::$app->request->get('main_ezf_id');
        $ezm_id = Yii::$app->request->get('ezm_id');
        $act = Yii::$app->request->get('act');
        $conditions = Yii::$app->request->get('conditions');
        $key_index = Yii::$app->request->get('key_index');
        $sub_index = Yii::$app->request->get('sub_index');

        return $this->renderAjax('../../../ezmodules/views/ezmodule-widget/assets/template_form/_condition_form', [
                    'ezf_id' => $ezf_id,
                    'ezf_id2' => $ezf_id2,
                    'ezm_id' => $ezm_id,
                    'act' => $act,
                    'main_ezf_id' => $main_ezf_id,
                    'conditions' => $conditions,
                    'key_index' => $key_index,
                    'sub_index' => $sub_index,
        ]);
    }

    public function actionAddNewsummary() {
        $ezf_id = Yii::$app->request->get('ezf_id');
        $ezf_id2 = Yii::$app->request->get('ezf_id2');
        $main_ezf_id = Yii::$app->request->get('main_ezf_id');
        $ezm_id = Yii::$app->request->get('ezm_id');
        $act = Yii::$app->request->get('act');
        $summarys = Yii::$app->request->get('summarys');
        $key_index = Yii::$app->request->get('key_index');
        $sub_index = Yii::$app->request->get('sub_index');

        return $this->renderAjax('../../../ezmodules/views/ezmodule-widget/assets/template_form/_summary_form', [
                    'ezf_id' => $ezf_id,
                    'ezf_id2' => $ezf_id2,
                    'ezm_id' => $ezm_id,
                    'act' => $act,
                    'main_ezf_id' => $main_ezf_id,
                    'summarys' => $summarys,
                    'key_index' => $key_index,
                    'sub_index' => $sub_index,
        ]);
    }
    
    public function actionAddNewSubquery() {
        $ezf_id = Yii::$app->request->get('ezf_id');
        $main_ezf_id = Yii::$app->request->get('main_ezf_id');
        $ezm_id = Yii::$app->request->get('ezm_id');
        $act = Yii::$app->request->get('act');
        $subquery = Yii::$app->request->get('subquery');
        $key_index = Yii::$app->request->get('key_index');
        $sub_index = Yii::$app->request->get('sub_index');

        return $this->renderAjax('../../../ezmodules/views/ezmodule-widget/assets/his_patient_visit/_subquery_custom', [
                    'ezf_id' => $ezf_id,
                    'ezm_id' => $ezm_id,
                    'act' => $act,
                    'main_ezf_id' => $main_ezf_id,
                    'subquery' => isset($subquery[$sub_index])?$subquery[$sub_index]:null,
                    'key_index' => $key_index,
                    'sub_index' => $sub_index,
        ]);
    }

    public function actionAddNewVariable() {
        $ezf_id = Yii::$app->request->get('ezf_id');
        $main_ezf_id = Yii::$app->request->get('main_ezf_id');
        $ezm_id = Yii::$app->request->get('ezm_id');
        $act = Yii::$app->request->get('act');
        $variables = Yii::$app->request->get('variables');
        $key_index = Yii::$app->request->get('key_index');
        $sub_index = Yii::$app->request->get('sub_index');

        return $this->renderAjax('../../../ezmodules/views/ezmodule-widget/assets/his_patient_visit/_variable_custom', [
                    'ezf_id' => $ezf_id,
                    'ezm_id' => $ezm_id,
                    'act' => $act,
                    'main_ezf_id' => $main_ezf_id,
                    'variables' => $variables,
                    'key_index' => $key_index,
                    'sub_index' => $sub_index,
        ]);
    }

    public function actionAddNewSubcontent() {
        $ezf_id = Yii::$app->request->get('ezf_id');
        $main_ezf_id = Yii::$app->request->get('main_ezf_id');
        $ezm_id = Yii::$app->request->get('ezm_id');
        $act = Yii::$app->request->get('act');
        $subcontent = Yii::$app->request->get('subcontent');
        $key_index = Yii::$app->request->get('key_index');
        $sub_index = Yii::$app->request->get('sub_index');

        return $this->renderAjax('../../../ezmodules/views/ezmodule-widget/assets/his_patient_visit/_sub_content', [
                    'ezf_id' => $ezf_id,
                    'ezm_id' => $ezm_id,
                    'act' => $act,
                    'main_ezf_id' => $main_ezf_id,
                    'subcontent' => $subcontent,
                    'key_index' => $key_index,
                    'sub_index' => $sub_index,
        ]);
    }

    public function actionAddNewItemcondition() {
        $ezf_id = Yii::$app->request->get('ezf_id');
        $main_ezf_id = Yii::$app->request->get('main_ezf_id');
        $ezm_id = Yii::$app->request->get('ezm_id');
        $act = Yii::$app->request->get('act');
        $conditions = Yii::$app->request->get('conditions');
        $key_index = Yii::$app->request->get('key_index');
        $nameType = Yii::$app->request->get('nameType', 'configs');

        return $this->renderAjax('../../../ezmodules/views/ezmodule-widget/assets/cashier/_item_condition', [
                    'ezf_id' => $ezf_id,
                    'ezm_id' => $ezm_id,
                    'act' => $act,
                    'main_ezf_id' => $main_ezf_id,
                    'conditions' => $conditions,
                    'key_index' => $key_index, 'nameType' => $nameType
        ]);
    }

    public function actionAddNewItemselect() {
        $ezf_id = Yii::$app->request->get('ezf_id');
        $main_ezf_id = Yii::$app->request->get('main_ezf_id');
        $ezm_id = Yii::$app->request->get('ezm_id');
        $act = Yii::$app->request->get('act');
        $selects = Yii::$app->request->get('selects');
        $key_index = Yii::$app->request->get('key_index');
        $sub_index = Yii::$app->request->get('sub_index');

        return $this->renderAjax('../../../ezmodules/views/ezmodule-widget/assets/cashier/_item_select', [
                    'ezf_id' => $ezf_id,
                    'ezm_id' => $ezm_id,
                    'act' => $act,
                    'main_ezf_id' => $main_ezf_id,
                    'selects' => $selects,
                    'key_index' => $key_index,
                    'sub_index' => $sub_index,
        ]);
    }

    public function actionAddNewHeaderselect() {
        $ezf_id = Yii::$app->request->get('ezf_id');
        $ezf_id2 = Yii::$app->request->get('ezf_id2');
        $main_ezf_id = Yii::$app->request->get('main_ezf_id');
        $ezm_id = Yii::$app->request->get('ezm_id');
        $act = Yii::$app->request->get('act');
        $selects = Yii::$app->request->get('selects');
        $key_index = Yii::$app->request->get('key_index');
        $sub_index = Yii::$app->request->get('sub_index');
        $getFiledForm = Yii::$app->request->get('getFiledForm', 2); //ให้ส่งมาว่าจะใช้ configs/get-fields-forms 1 หรือ 2 default=2 ByOak
        $nameType = Yii::$app->request->get('nameType', 'configs');

        return $this->renderAjax('../../../ezmodules/views/ezmodule-widget/assets/template_form/_select_form', [
                    'ezf_id' => $ezf_id,
                    'ezf_id2' => $ezf_id2,
                    'ezm_id' => $ezm_id,
                    'act' => $act,
                    'main_ezf_id' => $main_ezf_id,
                    'selects' => $selects,
                    'key_index' => $key_index,
                    'sub_index' => $sub_index,
                    'getFiledForm' => $getFiledForm, 'nameType' => $nameType
        ]);
    }

    public function actionAddNewMedcontent() {
        $key_index = Yii::$app->request->get('key_index');
        $contents = Yii::$app->request->get('contents');
        $ezf_id = Yii::$app->request->get('ezf_id');

        return $this->renderAjax('../../../ezmodules/views/ezmodule-widget/assets/medicalhis/_form', [
                    'contents' => $contents,
                    'key_index' => $key_index,
                    'ezf_id' => $ezf_id,
        ]);
    }

    public function actionAddNewcontentMedical() {
        $ezf_id = Yii::$app->request->get('ezf_id');
        $main_ezf_id = Yii::$app->request->get('main_ezf_id');
        $ezm_id = Yii::$app->request->get('ezm_id');
        $act = Yii::$app->request->get('act');
        $contents = Yii::$app->request->get('contents');
        $key_index = Yii::$app->request->get('key_index');

        return $this->renderAjax('../../../ezmodules/views/ezmodule-widget/assets/medicalhis/_form', [
                    'ezf_id' => $ezf_id,
                    'ezm_id' => $ezm_id,
                    'act' => $act,
                    'main_ezf_id' => $main_ezf_id,
                    'contents' => $contents,
                    'key_index' => $key_index,
        ]);
    }

    public function actionAddNewcolumnGrid() {
        $ezf_id = Yii::$app->request->get('ezf_id');
        $main_ezf_id = Yii::$app->request->get('main_ezf_id');
        $value_ref = Yii::$app->request->get('value_ref');
        $ezm_id = Yii::$app->request->get('ezm_id');
        $act = Yii::$app->request->get('act');
        $columns = Yii::$app->request->get('columns');
        $key_index = Yii::$app->request->get('key_index');

        return $this->renderAjax('../../../ezmodules/views/ezmodule-widget/assets/multiple_grid/_form', [
                    'ezf_id' => $ezf_id,
                    'ezm_id' => $ezm_id,
                    'act' => $act,
                    'main_ezf_id' => $main_ezf_id,
                    'value_ref' => $value_ref,
                    '$columns' => $columns,
                    'key_index' => $key_index,
        ]);
    }

    public function actionAddNewcontainerWidget() {
        $ezf_id = Yii::$app->request->get('ezf_id');
        $main_ezf_id = Yii::$app->request->get('main_ezf_id');
        $ezm_id = Yii::$app->request->get('ezm_id');
        $act = Yii::$app->request->get('act');
        $contents = Yii::$app->request->get('contents');
        $key_index = Yii::$app->request->get('key_index');

        return $this->renderAjax('../../../ezmodules/views/ezmodule-widget/assets/container_widget/_form', [
                    'ezf_id' => $ezf_id,
                    'ezm_id' => $ezm_id,
                    'act' => $act,
                    'main_ezf_id' => $main_ezf_id,
                    'contents' => $contents,
                    'key_index' => $key_index,
        ]);
    }

}
