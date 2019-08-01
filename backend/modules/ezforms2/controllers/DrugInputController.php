<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\modules\ezforms2\controllers;

use yii\helpers\Url;
use dms\aomruk\classese\ckdFunc;

/**
 * Description of DrugInputController
 *
 * @author AR9
 */
class DrugInputController extends EzformDataController {

    //put your code here
    public function actionLoadDrug() {
        $html = $this->renderPartial('drug-ipd');
        $drug_ipd = \Yii::$app->request->get('drug_ipd');
        $drug_opd = \Yii::$app->request->get('drug_opd');
        $id = \Yii::$app->request->get('id');

        return $this->renderAjax('load-drug', [
                    'id' => $id,
                    'drug_ipd' => $drug_ipd,
                    'drug_opd' => $drug_opd
        ]);
    }

    public function actionAddDrug() {
        $date = \Yii::$app->request->get('date', '0000-00-00');
        $type = \Yii::$app->request->get('type', '');
        $db = ckdFunc::getDb();
        $sitecode = \Yii::$app->user->identity->profile->sitecode;
        $query = new \yii\db\Query();
        if ($type == 'ipd') {
            $data = $query->select(['DNAME', 'AMOUNT', 'UNIT_PACKING'])->from('f_drug_ipd')
                    ->where('pid=:pid', [':pid' => '4028'])
                    ->andWhere('sitecode=:sitecode', [':sitecode' => $sitecode])
                    ->andWhere('DATE(DATETIME_ADMIT) = :date', [':date' => $date])
                    ->andWhere('TYPEDRUG = :type_drug', [':type_drug' => '2'])
                    ->all($db);
        } 
        if ($type == 'opd') {
            $data = $query->select(['DNAME', 'AMOUNT', 'UNIT_PACKING'])->from('f_drug_opd')
                    ->where('pid=:pid', [':pid' => '9355'])
                    ->andWhere('sitecode=:sitecode', [':sitecode' => $sitecode])
                    ->andWhere('DATE(DATE_SERV) = :date', [':date' => $date])
                    ->all($db);
        }


        return \yii\helpers\Json::encode($data);
    }

    public function actionDate() {
        $type = \Yii::$app->request->get('type', '');
        $db = ckdFunc::getDb();
        $sitecode = \Yii::$app->user->identity->profile->sitecode;
        $query = new \yii\db\Query();
        if ($type == 'ipd') {
            $date = $query->select('DATE(DATETIME_ADMIT) AS DATETIME_ADMIT')
                    ->from('f_drug_ipd')
                    ->where('pid=:pid', [':pid' => '4028'])
                    ->andWhere('sitecode=:sitecode', [':sitecode' => $sitecode])
                    ->groupBy('DATETIME_ADMIT')
                    ->all($db);
            return $this->renderAjax('drug-ipd', [
                        'date' => $date
            ]);
        }
        if ($type == 'opd') {
            $date = $query->select('DATE_SERV')
                ->from('f_drug_opd')
                ->where('pid=:pid', [':pid' => '9355'])
                ->andWhere('sitecode=:sitecode', [':sitecode' => $sitecode])
                ->groupBy('DATE_SERV')
                ->all($db);
            return $this->renderAjax('drug-opd', [
                        'date' => $date
            ]);
        }
    }

}
