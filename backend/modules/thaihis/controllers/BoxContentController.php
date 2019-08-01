<?php

namespace backend\modules\thaihis\controllers;

use Yii;
use backend\modules\thaihis\classes\ThaiHisQuery;
use backend\modules\ezforms2\classes\EzfFunc;
use backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\ezforms2\classes\EzfUiFunc;
use backend\modules\patient\classes\PatientFunc;
use backend\modules\ezforms2\models\TbdataAll;
use backend\modules\thaihis\classes\ThaiHisFunc;
use yii\web\Response;

class BoxContentController extends \yii\web\Controller {

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

    public function actionContent($ezf_id = null) {
        if (Yii::$app->getRequest()->isAjax) {
            $modal = isset($_GET['modal']) ? $_GET['modal'] : '';
            $reloadDiv = isset($_GET['reloadDiv']) ? $_GET['reloadDiv'] : '';
            $widget_id = isset($_GET['widget_id']) ? $_GET['widget_id'] : '';
            $target = isset($_GET['target']) ? $_GET['target'] : '';
            $visitid = isset($_GET['visitid']) ? $_GET['visitid'] : '';
            $visit_type = isset($_GET['visit_type']) ? $_GET['visit_type'] : '';
            $targetField = isset($_GET['targetField']) ? $_GET['targetField'] : '';
            $fields = isset($_GET['fields']) ? $_GET['fields'] : '';
            $initdata = isset($_GET['initdata']) ? $_GET['initdata'] : false;
            $dataid = isset($_GET['dataid']) ? $_GET['dataid'] : '';
            $options = isset($_GET['options']) ? $_GET['options'] : '';
          
            $fields = EzfFunc::stringDecode2Array($fields);
            $options = EzfFunc::stringDecode2Array($options);
            $tabs = isset($options['tabs']) ? $options['tabs'] : '';
  
            $modelEzf = EzfQuery::getEzformOne($ezf_id);
            $version = (isset($_GET['v']) && $_GET['v'] != '') ? $_GET['v'] : $modelEzf->ezf_version;
            $model_tabs = null;
            $modelFields_tabs = null;
            if (isset($tabs) && is_array($tabs)) {
                foreach ($tabs as $key => $val) {
                    $ezformTab = EzfQuery::getEzformOne($val['ezf_id']);
                    if (isset($val['field_display']) && is_array($val['field_display'])) {
                        if (isset($val['field_pic']) && $val['field_pic'] != '') {
                            $val['field_display'][] = $val['field_pic'];
                        }

                        $fieldsTab = ThaiHisQuery::getModelFields($val['ezf_id'], $ezformTab->ezf_version, $val['field_display']);
                        $searchModelTab = new TbdataAll();
                        $searchModelTab->setTableName($ezformTab->ezf_table);
                        $modelFields_tabs[$key] = $fieldsTab;
                        $model_tabs[$key] = ThaiHisFunc::setDynamicModel($fieldsTab, $ezformTab->ezf_table, Yii::$app->session['ezf_input'], Yii::$app->session['show_varname']);
                        $modelFilterTab = ['target' => $visitid];
                        $data_tab = ThaiHisFunc::modelSearch($searchModelTab, $ezformTab, null, null, $fieldsTab, $modelFilterTab, 0, Yii::$app->request->queryParams);
                        if ($data_tab)
                            $model_tabs[$key]->attributes = $data_tab;
                    }else {
                        $modelFields_tabs[$key] = null;
                    }
                }
            }

            //fix version by dataid
            if ($dataid != '') {
                $modelZdata = EzfUiFunc::loadTbData($modelEzf->ezf_table, $dataid);
                if ($modelZdata) {
                    if ($modelZdata->rstat != 0 && !empty($modelZdata->ezf_version)) {
                        $version = $modelZdata->ezf_version;
                    }
                    if (!empty($modelZdata->ezf_version)) {
                        $modelEzf->ezf_version = $modelZdata->ezf_version;
                    }
                } else {
                    return $this->renderAjax('_error', [
                                'ezf_id' => $ezf_id,
                                'dataid' => $dataid,
                                'modelEzf' => $modelEzf,
                                'msg' => Yii::t('app', 'No results found.'),
                    ]);
                }
            }

            if ($modelEzf->enable_version) {
                $modelVersion = EzfQuery::getEzformConfigApprov($modelEzf->ezf_id, $version);
            } else {
                $modelVersion = EzfQuery::getEzformConfig($modelEzf->ezf_id, $version);
            }
            if ($modelVersion) {
                $modelEzf->field_detail = $modelVersion->field_detail;
                $modelEzf->ezf_sql = $modelVersion->ezf_sql;
                $modelEzf->ezf_js = $modelVersion->ezf_js;
                $modelEzf->ezf_error = $modelVersion->ezf_error;
                $modelEzf->ezf_options = $modelVersion->ezf_options;
            } else {
                return $this->renderAjax('_error', [
                            'ezf_id' => $ezf_id,
                            'dataid' => $dataid,
                            'modelEzf' => $modelEzf,
                            'msg' => Yii::t('app', 'No version found.'),
                ]);
            }

            $modelFields = EzfQuery::getFieldAll($modelEzf->ezf_id, $version);
            if (!isset(Yii::$app->session['ezf_input'])) {
                Yii::$app->session['show_varname'] = 0;
                Yii::$app->session['ezf_input'] = EzfQuery::getInputv2All();
            }

            $model = EzfFunc::setDynamicModel($modelFields, $modelEzf->ezf_table, Yii::$app->session['ezf_input'], Yii::$app->session['show_varname']);

            if ($dataid != '') {
                $model = EzfUiFunc::loadData($model, $modelEzf->ezf_table, $dataid);

                if (!$model) {// dataid ส่งมาผิดหาไม่เจอ / ไม่คิดรวมถ้าส่ง '' มา
                    return $this->renderAjax('_error', [
                                'ezf_id' => $ezf_id,
                                'dataid' => $dataid,
                                'modelEzf' => $modelEzf,
                                'msg' => Yii::t('app', 'No results found.'),
                    ]);
                }
            } else {
                if ($initdata) {
                    $modelLastRecord = EzfUiFunc::loadLastRecord($model, $modelEzf->ezf_table, $visitid);

                    if ($modelLastRecord) {
                        $model = $modelLastRecord;
                    }
                }
            }
            $view = '_content_data';
            if (isset($tabs) && is_array($tabs)) {
                $view = '_content_tabs';
            }
            
            return $this->renderAjax($view, [
                        'modelFields' => $modelFields,
                        'model' => $model,
                        'ezf_id' => $ezf_id,
                        'modelEzf' => $modelEzf,
                        'modal' => $modal,
                        'reloadDiv' => $reloadDiv,
                        'fields' => $fields,
                        'target' => $target,
                        'visitid' => $visitid,
                        'visit_type' => $visit_type,
                        'targetField' => $targetField,
                        'initdata' => $initdata,
                        'dataid' => $dataid,
                        'options' => $options,
                        'widget_id' => $widget_id,
                        'version' => $version,
                        'tabs' => $tabs,
                        'modelFields_tabs' => $modelFields_tabs,
                        'model_tabs' => $model_tabs,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

}
