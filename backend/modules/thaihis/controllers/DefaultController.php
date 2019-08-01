<?php

namespace backend\modules\thaihis\controllers;

use backend\modules\ezforms2\classes\EzfQuery;
use Yii;
use yii\db\Query;
use yii\web\Controller;
use yii\web\Response;

class DefaultController extends Controller {

    public function actionIndex() {
        return $this->render('index');
    }

    public function actionConfigbku() {
        unset(Yii::$app->session['bku_initdata']);
        unset(Yii::$app->session['bku_version']);
        unset(Yii::$app->session['bku_step']);
        unset(Yii::$app->session['table_name']);
        Yii::$app->session['oh_id'] = 0;
        Yii::$app->session['pt_id'] = 0;
        Yii::$app->session['vn_id'] = 0;        

        return $this->render('config_bku');
    }

    public function actionBackup() {
        ini_set('max_execution_time', 0);
        set_time_limit(0);
        ini_set('memory_limit', '5000M');

        $func = isset($_POST['func']) ? $_POST['func'] : '';
        $table_name = isset($_POST['table_name']) ? $_POST['table_name'] : '';
        $step = isset($_POST['step']) ? $_POST['step'] : '';
        $initdata = isset($_POST['initdata']) ? $_POST['initdata'] : '';
        $initdata = \appxq\sdii\utils\SDUtility::strArray2String($initdata);
        $initdata = \appxq\sdii\utils\SDUtility::string2Array($initdata);

        if (isset($_POST['func'])) {
            Yii::$app->session['func'] = $func;
            Yii::$app->session['table_name'] = $table_name;
            Yii::$app->session['bku_step'] = $step;
            Yii::$app->session['bku_initdata'] = $initdata;
        }

        return $this->render('backup');
    }

    public function actionLogToDb() {
        unset(Yii::$app->session['bku_step']);
        unset(Yii::$app->session['table_name']);

        $sql = "SELECT distinct tb_name as id , tb_name as name
                FROM backup_logs";

        $data_form = Yii::$app->db_nhis->createCommand($sql)->queryAll();

        return $this->render('log-to-db',['data_form'=>$data_form]);
    }

    public function actionBackupToDb() {
        ini_set('max_execution_time', 0);
        set_time_limit(0);
        ini_set('memory_limit', '5000M');

        $table_name = isset($_POST['table_name']) ? $_POST['table_name'] : '';
        $step = isset($_POST['step']) ? $_POST['step'] : '';

        if (isset($_POST['table_name'])) {
            Yii::$app->session['table_name'] = $table_name;
            Yii::$app->session['bku_step'] = $step;
        }

        return $this->render('backup-to-db');
    }

}
