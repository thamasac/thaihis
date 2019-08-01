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

class ContainerWidgetController extends \yii\web\Controller {

    public function actionIndex() {
        $modal = Yii::$app->request->get('modal');
        $visitid = Yii::$app->request->get('visitid');
        $visit_type = Yii::$app->request->get('visit_type');
        $target = Yii::$app->request->get('target');
        $options = Yii::$app->request->get('options');

        return $this->render('index', [
                    'modal' => $modal,
                    'options' => $options,
                    'visitid' => $visitid,
                    'visit_type' => $visit_type,
                    'target' => $target,
        ]);
    }

    public function actionContainerContent() {
        $template_content = '';
        if (Yii::$app->getRequest()->isAjax) {
            $widget_id = Yii::$app->request->get('widget_id');
            $modal = Yii::$app->request->get('modal');
            $visitid = Yii::$app->request->get('visitid');
            $visit_type = Yii::$app->request->get('visit_type');
            $target = Yii::$app->request->get('target');
            $options = Yii::$app->request->get('options');
            $readonly = Yii::$app->request->get('readonly');
            
            $contents = $options['contents'];
            $readonly = isset($options['readonly']) ? $options['readonly'] : null;
            $column = isset($options['column']) ? $options['column'] : 2;
            $template_content = "<div class='form-group row' id='show-content-tab{$widget_id}'>";
            
            usort($contents, function($a, $b) {
                return $a['widget_order'] - $b['widget_order'];
            });

            for ($i = 1; $i <= $column; $i++) {
                $col = 12 / $column;
                if ($i == 1)
                    $template_content .= "<div class='col-md-{$col}'>";
                else
                    $template_content .= "<div class='col-md-{$col} sdbox-col'>";
                $count = 1;
                $count2 = 0;
                foreach ($contents as $key => $val) {
                    if ($count == $i  || ($count) == ($count2 + $column)) {
                        $widget_ops = \backend\modules\subjects\classes\SubjectManagementQuery::getWidgetById($val['widget_id']);
                        $template_content .= "<div class='col-md-{$val['widget_size']}'>";
                        $template_content .= $this->renderAjax($widget_ops['widget_render'], ['widget_config' => $widget_ops, 'modal' => $modal, 'readonly' => $readonly,]);
                        $template_content .= "</div>";
                        $count2 = $count;
                    }
                    $count++;
                    
                }

                $template_content .= "</div>";
            }
            $template_content .= "</div>";
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }

        return $this->renderAjax('_view', [
                    'template_content' => $template_content,
        ]);
    }

}
