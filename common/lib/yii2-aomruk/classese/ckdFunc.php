<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace dms\aomruk\classese;

/**
 * Description of ckdFunc
 *
 * @author AR9
 */
class ckdFunc {

    public static function getDb() {
        $sitecode = \Yii::$app->user->identity->profile->sitecode;
        $con = new \yii\db\Query();
        $provice_code = $con->select('*')->from('all_hospital_thai')->where(['hcode' => $sitecode])->one();
        $config_serv = $con->select('*')->from('db_config_province')->where(['province' => $provice_code['provincecode']])->one();
        $db = new \yii\db\Connection([
            'dsn' => 'mysql:host='.$config_serv['server'].';port='.$config_serv['port'].';dbname='.$config_serv['db'],
            'username' => $config_serv['user'],
            'password' => $config_serv['passwd'],
            'charset' => 'utf8',
        ]);
        return $db;
    }

}
