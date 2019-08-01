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

class MultipleGridController extends \yii\web\Controller {

    public function actionIndex() {
        $options = Yii::$app->request->get('options');
        $reloadDiv = Yii::$app->request->get('reloadDiv');
        $modal = Yii::$app->request->get('modal');

        return $this->renderAjax('index', [
                    'options' => $options,
                    'modal' => $modal,
                    'reloadDiv' => $reloadDiv,
        ]);
    }

    public function actionGrid() {
        $options = Yii::$app->request->get('options');
        $reloadDiv = Yii::$app->request->get('reloadDiv');
        $search = Yii::$app->request->post('model');
        $modal = Yii::$app->request->get('modal');
        
        $columns = isset($options['columns']) ? $options['columns'] : null;
        $ezf_id = isset($options['ezf_id']) ? $options['ezf_id'] : null;
        $refform = isset($options['refform']) ? $options['refform'] : null;
        $left_refform = isset($options['left_refform']) ? $options['left_refform'] : null;
        $fields = isset($options['fields']) ? $options['fields'] : null;
        $group_fields = isset($options['group_fields']) ? $options['group_fields'] : null;
        $group_concat = isset($options['group_concat']) ? $options['group_concat'] : null;
        $date_search = isset($options['date_field']) ? $options['date_field'] : null;
        $search_field = isset($options['search_field']) ? $options['search_field'] : null;
        $conditions = isset($options['conditions']) ? $options['conditions'] : null;
        $summarys = isset($options['summarys']) ? $options['summarys'] : null;
        $selects = isset($options['selects']) ? $options['selects'] : null;

        if (!isset($searchModel)) {
            $searchModel = $searchModel = PatientFunc::getModel($ezf_id, '');
            $searchModel['create_date'] = date('d-m-Y');
        }
        
        $ezform = EzfQuery::getEzformOne($ezf_id);
        $searchName = "";
        if (isset($search)) {
            $searchModel['create_date'] = $search[0]['value'];
            $searchName = isset($search[1]['value']) ? $search[1]['value'] : null;
        }

        $groupBy = [];
        $customSelect = [];
        $reponseQuery = [];
        if (isset($group_fields)) {
            foreach ($group_fields as $key => $val) {
                $groupBy[] = $val;
            }
        }

        if (isset($group_concat)) {
            foreach ($group_concat as $key => $val) {
                $fieldName = explode('.', $val);
                $customSelect[] = "GROUP_CONCAT(DISTINCT {$val}) as '{$fieldName[1]}'";
            }
        }
        $modelFilter = [];
        if ($date_search) {
            $dateStamp = strtotime($searchModel['create_date']);
            $dateCon = date('Y-m-d', $dateStamp);
            $modelFilter[] = "DATE({$date_search})='" . $dateCon . "'";
        }

        if (isset($searchName) && $searchName != '' && isset($search_field) && $search_field) {
            $modelFilter[] = "CONCAT(" . join($search_field, ',') . ")" . " LIKE '%" . $searchName . "%'";
        }
        
        if ($ezform) {
            $reponseQuery = ThaiHisQuery::getDynamicQuery($fields, $refform, $ezform, $conditions, $summarys, null, $customSelect, $modelFilter, $groupBy, $left_refform,null,$selects,50);
        }
        
        return $this->renderAjax('_grid', [
                    'modelFields' => isset($reponseQuery['modelFields']),
                    'searchModel' => $searchModel,
                    'dataProvider' => isset($reponseQuery['dataProvider']) ? $reponseQuery['dataProvider'] : null,
                    'columns' => $columns,
                    'ezf_id' => $ezf_id,
                    'refform' => $refform,
                    'left_refform' => $left_refform,
                    'reloadDiv' => $reloadDiv,
                    'options' => $options,
                    'modal' => $modal,
        ]);
    }

    public function actionModalContent() {
        if (Yii::$app->getRequest()->isAjax) {
            $widget_id = Yii::$app->request->get('widget_id');
            $modal = Yii::$app->request->get('modal');
            $visitid = Yii::$app->request->get('visitid');
            $visit_type = Yii::$app->request->get('visit_type');
            $target = Yii::$app->request->get('target');
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
