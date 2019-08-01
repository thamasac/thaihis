<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\modules\reports\controllers;

use Yii;
/**
 * Description of CustomReportController
 *
 * @author chanpan
 */
use common\lib\tcpdf\SDPDF;

class ThaihisReportController extends \yii\web\Controller {

    //put your code here
    public function actionIndex() {
        $target = Yii::$app->request->get('target');
        $module_id = Yii::$app->request->get('module_id');
        $item_report = Yii::$app->request->get('item_report');
        $options = Yii::$app->request->get('options');

        return $this->renderAjax('index', [
                    'target' => $target,
                    'module_id' => $module_id,
                    'item_report' => $item_report,
                    'options' => $options,
        ]);
    }

    public function actionPrint() {
        $ezfId = \Yii::$app->request->get('ezf_id', '1523071255006806900');
        $dataId = \Yii::$app->request->get('data_id', '1528879530068152000');
        $template = \Yii::$app->request->get('template', '');
        $print = \Yii::$app->request->get('print', '1');

        $patient = \Yii::$app->request->get('patient', '0');
        $visit_id = \Yii::$app->request->get('visit_id', '1537683022033912900');
        $template_id = \Yii::$app->request->get('template_id', ''); //template id

        $layout = \Yii::$app->request->get('layout', 'P'); //P แนวตั้ง L แนวนอน
        $paperSize = \Yii::$app->request->get('paper_size', 'A4'); //string x,y x=white y=height
        $paperSize = (explode(",", $paperSize));
        if (count($paperSize) == 2) {
            $paperSize = [$paperSize[0], $paperSize[1]];
        } else {
            $paperSize = $paperSize[0];
        }

        if ($template_id) {
            $template = \backend\modules\reports\models\CustomPrint::findOne($template_id);
        } else {
            $template = \backend\modules\reports\models\CustomPrint::find()->where("rstat not in(0,3) AND `default`='10'")->one();
        }
        $dataId = ($dataId != "") ? $dataId : '1528879530068152000';
        if ($patient == "1" && $visit_id != "") {
            $output = \backend\modules\reports\classes\CustomReport::getPatientprofile($visit_id);
            $title = "";
        } else {
            $data = \backend\modules\reports\classes\CustomReport::getEzfData($ezfId, $dataId);
            $dataKey = $data['dataKey'];
            $output = $data['output'];
            $title = $data['ezfStruc']['ezf_name'];
        }
        $path = [];
        foreach ($output as $key => $value) {
            $path["{" . $key . "}"] = $value;
        }
        $template = strtr($template['template'], $path);
        return \backend\modules\reports\classes\CustomReport::printPDF($print, $layout, $paperSize, $title, $template, $ezfId, $dataId);
    }

    public function actionSave() {
        $post = \Yii::$app->request->post();
//        \appxq\sdii\utils\VarDumper::dump($post);

        if (!empty($post)) {
            //\appxq\sdii\utils\VarDumper::dump($post);
            $model = \backend\modules\reports\models\CustomPrint::findOne($post['id']);
            //\appxq\sdii\utils\VarDumper::dump($post);
            $model->default = $post['defaults'];
            $model->template_id = $post['template_id'];
            $model->template = $post['template'];
            if ($model->save()) {
                $out = ['success' => true, ['data' => ['message' => 'Save success!']]];
                return json_encode($out);
            }
        }
    }

}
