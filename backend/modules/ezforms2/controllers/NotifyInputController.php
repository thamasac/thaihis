<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\modules\ezforms2\controllers;

use yii\web\Response;
use Yii;

/**
 * Description of NotifyInput
 *
 * @author AR9
 */
class NotifyInputController extends \yii\web\Controller {

    public function actionGetFormBasic() {
        $ezf_id = \Yii::$app->request->get('ezf_id', '');
        $ezf_version = \Yii::$app->request->get('ezf_version', '');
        return $this->renderAjax('_basic', ['ezf_id' => $ezf_id, 'ezf_version' => $ezf_version]);
    }
    
    public function actionGetFormAdvance() {
        $configAdvance = \backend\modules\ezforms2\classes\EzfFunc::stringDecode2Array(\Yii::$app->request->get('data', ''));
        $ezf_id = \Yii::$app->request->get('ezf_id', '');
        $ezf_version = \Yii::$app->request->get('ezf_version', '');
        $queryTool = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($ezf_id);
        return $this->renderAjax('_advance',[
            'configAdvance' => $configAdvance,
            'ezf_version' => $ezf_version,
            'ezf_id' => $ezf_id,
            'queryTool' => $queryTool
        ]);
    }
    
    public function actionGetFormAdvances() {
        
        $configAdvance = \backend\modules\ezforms2\classes\EzfFunc::stringDecode2Array(\Yii::$app->request->get('data', []));
        $ezf_id = \Yii::$app->request->get('ezf_id', '');
        $ezf_version = \Yii::$app->request->get('ezf_version', '');
        $queryTool = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($ezf_id);
        return $this->renderAjax('_advances',[
            'configAdvance' => $configAdvance,
            'ezf_version' => $ezf_version,
            'ezf_id' => $ezf_id,
            'queryTool' => $queryTool
        ]);
    }

    public function actionConstant() {
        $ezf_id = Yii::$app->request->get('ezf_id', '');
        $id_input = Yii::$app->request->get('id_input', 'id_input');
        $modal = Yii::$app->request->get('modal', 'modal-constant');
//        $version = Yii::$app->request->get('version','');
        $fieldData = [];
        $fieldData = \backend\modules\ezforms2\models\EzformFields::findAll(['ezf_id' => $ezf_id]);
        return $this->renderAjax('_constant', [
                    'fieldData' => $fieldData,
                    'id_input' => $id_input,
                    'modal' => $modal
        ]);
    }

    public function actionGetField($q = '') {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $ezf_id = \Yii::$app->request->get('ezf_id', '');
        $ezf_field_id = \Yii::$app->request->get('ezf_field_id', '');
        $ezf_field_id = \backend\modules\ezforms2\classes\EzfFunc::stringDecode2Array($ezf_field_id);
        $ezf_version = \Yii::$app->request->get('ezf_version', '');

        $out = ['results' => []];
        if (is_array($ezf_field_id) && !empty($ezf_field_id)) {
            $instr = implode(',', $ezf_field_id);
            $items_field_name = (new \yii\db\Query())
                    ->select(['ezf_field_name AS `id`', 'concat(ezf_field_name, \' (\', ezf_field_label, \')\') AS`text`', 'ezf_version', 'ezf_field_name', 'ezf_field_label'])
                    ->from('ezform_fields')->where(['ezf_id' => $ezf_id])->andWhere("table_field_type not in('none','field') AND ezf_field_type in($instr) AND concat(ezf_field_name, ' (', ezf_field_label, ')') LIKE :q AND ezf_field_name != ''", [':q' => "%$q%"])
                    ->orderBy(['ezf_version' => SORT_ASC, 'ezf_field_order' => SORT_ASC])
                    ->all();
            if ($ezf_version != '') {
                $items_field_name = \backend\modules\ezforms2\classes\EzfFunc::getFieldsGroup($items_field_name, $ezf_version);
                $i = 0;
//        \appxq\sdii\utils\VarDumper::dump($items_field_name);
                foreach ($items_field_name as $value) {
                    $out["results"][$i] = ['id' => $value['id'], 'text' => $value["text"]];
                    $i++;
                }
            }
        }
        return $out;
    }

}
