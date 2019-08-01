<?php

namespace backend\modules\patient\controllers;

use Yii;
use backend\modules\patient\classes\PatientFunc;
use backend\modules\patient\classes\PatientQuery;
use backend\modules\ezforms2\classes\EzfUiFunc;
use backend\modules\ezforms2\classes\EzfFunc;
use yii\web\Response;
use backend\modules\ezforms2\classes\EzfQuery;
use yii\web\NotFoundHttpException;

class MedicalHistoryController extends \yii\web\Controller {

    public function actionIndex() {
        return $this->renderAjax('index');
    }

    public function actionView() {
        if (Yii::$app->getRequest()->isAjax) {
            $target = Yii::$app->request->get('target');
            $sitecode = \Yii::$app->user->identity->profile->sitecode;
            $options = \Yii::$app->request->get('options');
            $modal = Yii::$app->request->get('modal');

            return $this->renderAjax('_view', [
                        'target' => $target,
                        'sitecode' => $sitecode,
                        'options' => $options,
                        'modal' => $modal,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionVisitHospital() {
        if (Yii::$app->getRequest()->isAjax) {
            $dataid = Yii::$app->request->get('dataid');
            $reloadDiv = Yii::$app->request->get('reloadDiv');
            $reloadChildDiv = Yii::$app->request->get('reloadChildDiv');

            $dataProvider = PatientFunc::getVisitHospital($dataid);

            return $this->renderAjax('_visithos', [
                        'dataProvider' => $dataProvider,
                        'dataid' => $dataid,
                        'reloadDiv' => $reloadDiv,
                        'reloadChildDiv' => $reloadChildDiv,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionVisit() {
        if (Yii::$app->getRequest()->isAjax) {
            $ezf_id = \backend\modules\patient\Module::$formID['visit'];
            $target = Yii::$app->request->get('target');
            $sitecode = \Yii::$app->user->identity->profile->sitecode;
            $view = Yii::$app->request->get('view');
            $reloadDiv = Yii::$app->request->get('reloadDiv');
            $ragedate = Yii::$app->request->get('ragedate');
            $options = Yii::$app->request->get('options');
            $modal = Yii::$app->request->get('modal');

            $ezf_id = isset($options['ezf_id']) ? $options['ezf_id'] : null;
            $refform = isset($options['forms']) ? $options['forms'] : null;
            $visit_date_field = isset($options['visit_date']) ? $options['visit_date'] : null;
            $fields = isset($options['fields']) ? $options['fields'] : null;
            $contents = isset($options['contents']) ? $options['contents'] : null;
            $conditions = isset($options['search_field']) ? $options['conditions'] : null;
            $summarys = isset($options['summarys']) ? $options['summarys'] : null;
            $selects = isset($options['selects']) ? $options['selects'] : null;

            $customSelect = [];
            if(isset($selects) && is_array($selects)){
                foreach ($selects as $key => $val){
                    if(isset($val['custom_value'])&&$val['custom_value']!='')
                        $customSelect[] = $val['custom_value']." as ".$val['alias_name'];
                    else
                        $customSelect[] = $val['field']." as ".$val['alias_name'];
                }
            }

            $ezform = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($ezf_id);
            $searchModel = PatientFunc::getModel($ezf_id, '');
            $searchModel['target'] = $target;
            $searchModel['sitecode'] = $sitecode;
            $searchModel['visit_date'] = $ragedate;
            //$dataProvider = PatientFunc::getVisit($searchModel);
            $subSql = '';
            if ($ragedate && $ragedate != '') {
                $date = explode(",", $ragedate);
                if (count($date) > 1) {
                    $stDate = date('Y-m-d', strtotime($date[0]));
                    $enDate = date('Y-m-d', strtotime($date[1]));

                    $subSql = " {$visit_date_field} BETWEEN '{$stDate} 00:00:00' AND '{$enDate} 23:59:59'";
                }
            }
            $modelFilter = [$ezform->ezf_table . '.target=' . $target];
            if (isset($visit_date_field) && $visit_date_field != null){
                $modelFilter[] = $subSql;
                $sort_order = ['column' => $visit_date_field, 'order' => 'desc'];
            }

            
            if ($ezform) {
                $reponseQuery = \backend\modules\thaihis\classes\ThaiHisQuery::getDynamicQuery($fields, $refform, $ezform, $conditions, $summarys, null, $customSelect, $modelFilter, null, null, $sort_order);
            }
            
            $model = [];
            $modelFields = [];
            //$data_tab = ThaiHisFunc::modelSearch($searchModelTab, $ezformTab, $targetField, $ezformParent, $fieldsTab, $modelFilterTab, 0, Yii::$app->request->queryParams);
            if (isset($reponseQuery['modelDynamic']))
                $model = $reponseQuery['modelDynamic'];

            if (isset($reponseQuery['modelFields']))
                $modelFields = $reponseQuery['modelFields'];

            $dataProvider = $reponseQuery['dataProvider'];
            return $this->renderAjax('_visit_' . $view, [
                        'dataProvider' => $dataProvider,
                        'target' => $target,
                        'sitecode' => $sitecode,
                        'reloadDiv' => $reloadDiv,
                        'searchModel' => $searchModel,
                        'model' => $model,
                        'options' => $options,
                        'modal' => $modal,
                        'reloadChildDiv' => 'view-detail'//fix id ไปก่อน
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionShowDetail() {
        if (Yii::$app->getRequest()->isAjax) {
            $target = Yii::$app->request->get('target');
            $visit_id = Yii::$app->request->get('visitid');
            $visit_type = Yii::$app->request->get('visittype');
            $visit_an = Yii::$app->request->get('visitan');
            $visit_date = Yii::$app->request->get('visitdate');
            $modal = Yii::$app->request->get('modal');
            $data = \backend\modules\thaihis\classes\ThaiHisQuery::getPtProfile($target);

            return $this->renderAjax('_viewhistory', [
                        'target' => $target,
                        'visit_type' => $visit_type,
                        'visit_id' => $visit_id, 'visit_date' => $visit_date,
                        'visit_an' => $visit_an, 'pt_hn' => $data['pt_hn']
            ]);
        } else {
            throw new \yii\web\NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionShowDetailCpoe() {
        if (Yii::$app->getRequest()->isAjax) {
            $target = Yii::$app->request->get('target');
            $visit_id = Yii::$app->request->get('visitid');
            $visit_type = Yii::$app->request->get('visittype');
            $visit_an = Yii::$app->request->get('visitan');
            $visit_date = Yii::$app->request->get('visitdate');
            $options = Yii::$app->request->get('options');

            $modal = Yii::$app->request->get('modal');
            $model_content = [];
            $modelFields = [];
            $contents = isset($options['contents']) ? $options['contents'] : [];
            foreach ($contents as $key => $value) {
                $model = [];
                $fields = [];
                $reponseQuery = [];
                $modelFilter = [];
                $conditions = isset($value['conditions']) ? $value['conditions'] : null;
                if (isset($value['ezf_id'])) {
                    $ezform = EzfQuery::getEzformOne($value['ezf_id']);
                    //$fields = \backend\modules\thaihis\classes\ThaiHisQuery::getEzformFieldsById2($value['fields'], $value['ezf_id']);
                    //$model = \backend\modules\thaihis\classes\ThaiHisFunc::setDynamicModel($fields, $ezform['ezf_table'], Yii::$app->session['ezf_input']);
                    //$data = \backend\modules\thaihis\classes\ThaiHisQuery::getTableData($ezform, ['target' => $visit_id], 'one', null, ['column' => 'create_date', 'order' => 'desc']);
                    $modelFilter[] = [$ezform['ezf_table'] . '.target' => $visit_id];
                    $customSelect = [];
                    if(isset($value['group_concat'])){
                        foreach ($value['group_concat'] as $valG){
                            $fieldName = explode('.', $valG);
                            $customSelect[] = " GROUP_CONCAT({$valG}) as '{$fieldName[1]}'";
                        }
                        
                    }
                    if ($ezform) {
                        $reponseQuery = \backend\modules\thaihis\classes\ThaiHisQuery::getDynamicQuery($value['fields'], isset($value['refform']) ? $value['refform'] : null, $ezform, $conditions, null, null, $customSelect, $modelFilter);
                    }
                }

                $model_content[$key] = $reponseQuery['modelDynamic'];
                $modelFields[$key] = $reponseQuery['modelFields'];
            }

            return $this->renderAjax('_view_medical', [
                        'target' => $target,
                        'visit_type' => $visit_type,
                        'visit_id' => $visit_id,
                        'visit_an' => $visit_an,
                        'visit_date' => $visit_date,
                        'options' => $options,
                        'model_content' => $model_content,
                        'modelFields' => $modelFields,
                        'modal' => $modal,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionMedContent() {
        if (Yii::$app->getRequest()->isAjax) {
            $widget_id = Yii::$app->request->get('widget_id');
            $modal = Yii::$app->request->get('modal');
            $container_widget = \backend\modules\subjects\classes\SubjectManagementQuery::getWidgetById($widget_id);

            $options = $container_widget['options'];
            $options = \appxq\sdii\utils\SDUtility::string2Array($options);
            $contents = $options['contents'];
            $template_content = "<div class='form-group row' id='show-content-tab{$widget_id}'>";

            usort($contents, function($a, $b) {
                return $a['widget_order'] - $b['widget_order'];
            });
            $template_content .= "<div class='col-md-6'>";
            $count = 1;
            foreach ($contents as $key => $val) {
                
                if(($count%2) <> 0){
                    $widget_ops = \backend\modules\subjects\classes\SubjectManagementQuery::getWidgetById($val['widget_id']);
                    $template_content .= "<div class='col-md-12'>";
                    $template_content .= $this->renderAjax($widget_ops['widget_render'], ['widget_config' => $widget_ops, 'modal' => $modal]);
                    $template_content .= "</div>";
                }
                $count++;
            }
            $template_content .= "</div>";
            $template_content .= "<div class='col-md-6 sdbox-col'>";
            $count = 1;
            foreach ($contents as $key => $val) {
                if(($count%2) == 0){
                    $widget_ops = \backend\modules\subjects\classes\SubjectManagementQuery::getWidgetById($val['widget_id']);
                    $template_content .= "<div class=''>";
                    $template_content .= $this->renderAjax($widget_ops['widget_render'], ['widget_config' => $widget_ops, 'modal' => $modal]);
                    $template_content .= "</div>";
                }
                $count++;
            }
            $template_content .= "</div>";
            $template_content .= "</div>";

            return $template_content;
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

}
