<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace common\modules\user\controllers;

use dektrium\user\controllers\SettingsController as BaseSettingsController;
use Yii;
use yii\web\Response;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * SettingsController manages updating user settings (e.g. profile, email and password).
 *
 * @property \dektrium\user\Module $module
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class SettingsController extends BaseSettingsController
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'disconnect' => ['post'],
                    'delete'     => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow'   => true,
                        'actions' => ['profile', 'account', 'networks', 'disconnect', 'delete','switch-site'],
                        'roles'   => ['@'],
                    ],
                    [
                        'allow'   => true,
                        'actions' => ['confirm'],
                        'roles'   => ['?', '@'],
                    ],
                ],
            ],
        ];
    }
    public function DeleteAssignRole($user_id)
    {
        try {

            $auth_assign = \Yii::$app->db->createCommand()
                ->delete('auth_assignment', ['user_id' => $user_id])
                ->execute();
            return $auth_assign;
        } catch (\yii\db\Exception $e) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
        }
    }

    public function InsertAssignRole($auth_assign, $user_id)
    {
        try {

            foreach ($auth_assign as $value) {
                $auth_assign = \Yii::$app->db->createCommand()
                    ->insert('auth_assignment', [
                        'item_name' => $value,
                        'user_id' => $user_id,
                        'created_at' => date('Y-m-d H:i:s')
                    ])->execute();
            }
            return $auth_assign;
        } catch (\yii\db\Exception $e) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
        }
    }
    /**
     * 
     * @return type | obj message success or ereror
     */
    public function actionSwitchSite(){
       try{
           $site = \Yii::$app->request->post('site');
            $user_id = \cpn\chanpan\classes\CNUser::getUserId();
            $sql = "UPDATE profile SET sitecode=:sitecode WHERE user_id=:user_id";
            $params = [':sitecode'=>$site, ':user_id'=>$user_id];
            $data = Yii::$app->db->createCommand($sql,$params)->execute();
            if($data){
                return \backend\modules\manageproject\classes\CNMessage::getSuccessObj('Switch Site success', '');
            }
            return \backend\modules\manageproject\classes\CNMessage::getError("Switch Site fail!");
       } catch (Exception $ex) {
           \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
           return \backend\modules\manageproject\classes\CNMessage::getError("Switch Site fail!");
       }
        return $site;
    }
    /**
     * Shows profile settings form.
     * @return string|\yii\web\Response
     */
    public function actionProfile()
    {
        $id = isset(Yii::$app->user->id)?Yii::$app->user->id:'';
        $profile = \common\modules\user\models\Profile::findOne($id);
        $auth_str = \common\modules\user\classes\SiteCodeFunc::getAuthList();
        $profile->auth_str = \common\modules\user\classes\SiteCodeFunc::getAuthAssign($id);
        $modelFields = \backend\modules\core\classes\CoreQuery::getAllOptionsTable('profile');

        $defaultSitecode = $profile->sitecode;
        if ($profile->load(\Yii::$app->request->post())) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            
            if (!empty($_FILES['Profile']['name']['secret_file'])) {
                $profile->secret_file = \common\modules\user\classes\AdminClasses::UploadFiles($profile, "secret_file");
            }
            if (!empty($_FILES['Profile']['name']['citizenid_file'])) {
                $profile->citizenid_file = \common\modules\user\classes\AdminClasses::UploadFiles($profile, "citizenid_file");
            }
            $site_switch= isset($_POST['Profile']['site_switch']) ? $_POST['Profile']['site_switch'] : '';
            
            $profile->cid = isset($_POST['Profile']['cid']) ? $_POST['Profile']['cid'] : '';
            $profile->tel = isset($_POST['Profile']['tel']) ? $_POST['Profile']['tel'] : '';

            $profile->allow_assign = isset($_POST['Profile']['allow_assign']) ? $_POST['Profile']['allow_assign'] : '0';
            
            
            $site_switch_arr = isset($_POST['Profile']['site_switch'])?$_POST['Profile']['site_switch']:'';
            //return $_POST['Profile'];
            //\appxq\sdii\utils\VarDumper::dump($site_switch_arr);
            $site_switch_str = '';
            $site_switch_str = \appxq\sdii\utils\SDUtility::array2String($site_switch_arr);
            $profile->site_switch = $site_switch_str;
            $profile->original_site = $profile->sitecode;
            
            try{
                $sql="UPDATE `profile` set site_switch=:site_switch , original_site=:original_site WHERE user_id=:user_id";
                $params=[
                    ':site_switch'=>$site_switch_str,
                    ':original_site'=>$profile->original_site,
                    ':user_id'=>$id
                ];
                
                Yii::$app->db->createCommand($sql, $params)->execute();
            } catch (Exception $ex) {
                \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
                return false;
            }
            
            $saveSuccess = $profile->save();
            
            
          
            if ($profile->sitecode != '-9999' && $saveSuccess) {
                $result = [
                    'status' => 'success',
                    'action' => 'create',
                    'message' => \appxq\sdii\helpers\SDHtml::getMsgSuccess() . Yii::t('chanpan', 'Update profile completed.'),

                ];
            }else{
                $err = json_encode($profile->getErrors());
                $result = [
                    'status' => 'error',
                    'action' => 'create',
                    'message' => \appxq\sdii\helpers\SDHtml::getMsgSuccess() . Yii::t('chanpan', "Update profile Failed\n.$err"),

                ];
            }
            return $result;
        }

        return $this->render('profile', [

            'model' => $profile,
            'modelFields' => $modelFields,
            'auth_str' => $auth_str
        ]);
    }

    public function actionQuerys($q)
    {
        $sql = "SELECT hcode AS `code`, CONCAT(IFNULL(`hcode`,''), ' : ', IFNULL(`name`,''), ' ต.', IFNULL(`tambon`,''), ' อ.', IFNULL(`amphur`,''), ' จ.', IFNULL(`province`,'')) AS `name` FROM `all_hospital_thai` WHERE `name` LIKE '%" . $q . "%' OR `hcode` LIKE '%" . $q . "%' LIMIT 0,10";
        Yii::$app->response->format = Response::FORMAT_JSON;
        $data = Yii::$app->db->createCommand($sql)->queryAll();

        $json = array();
        foreach ($data as $value) {
            $json[] = ['id' => $value['code'], 'label' => $value["name"]];
        }
        return $json;
    }

    public function actionQuerys2($q)
    {
        if (Yii::$app->keyStorage->get('frontend.domain') == 'thaicarecloud.org') {
            $sql = "SELECT medshop AS `code`, CONCAT(IFNULL(`medshop`,''), ' : ', IFNULL(`nameshop`,'')) AS `name` FROM `tbdata_1483603520065899000` WHERE `nameshop` LIKE '%" . $q . "%' OR `medshop` LIKE '%" . $q . "%' LIMIT 0,10";
        } else {
            $sql = "SELECT hcode AS `code`, CONCAT(IFNULL(`hcode`,''), ' : ', IFNULL(`name`,''), ' ต.', IFNULL(`tambon`,''), ' อ.', IFNULL(`amphur`,''), ' จ.', IFNULL(`province`,'')) AS `name` FROM `all_hospital_thai` WHERE `name` LIKE '%" . $q . "%' OR `hcode` LIKE '%" . $q . "%' LIMIT 0,10";
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        $data = Yii::$app->db->createCommand($sql)->queryAll();

        $json = array();
        foreach ($data as $value) {
            $json[] = ['id' => $value['code'], 'label' => $value["name"]];
        }
        return $json;
    }

    public function actionQuerys3($q)
    {
        $sql = "SELECT hcode AS `code`, CONCAT(IFNULL(`hcode`,''), ' : ', IFNULL(`name`,''), ' ต.', IFNULL(`tambon`,''), ' อ.', IFNULL(`amphur`,''), ' จ.', IFNULL(`province`,'')) AS `name` FROM `all_hospital_thai` WHERE `name` LIKE '%" . $q . "%' OR `hcode` LIKE '%" . $q . "%' LIMIT 0,10";

        Yii::$app->response->format = Response::FORMAT_JSON;
        $data = Yii::$app->db->createCommand($sql)->queryAll();

        $json = array();
        foreach ($data as $value) {
            $json[] = ['id' => $value['code'], 'label' => $value["name"]];
        }
        return $json;
    }

    public function actionAccount()
    {
        /** @var SettingsForm $model */
        $model = \Yii::createObject(\common\models\SettingsForm::className());
        $event = $this->getFormEvent($model);

        $this->performAjaxValidation($model);

        $this->trigger(self::EVENT_BEFORE_ACCOUNT_UPDATE, $event);
        if ($model->load(\Yii::$app->request->post())) {
            if ($model->save()) {
                  
                \Yii::$app->session->setFlash('success', \Yii::t('user', 'Your account details have been updated'));
                $this->trigger(self::EVENT_AFTER_ACCOUNT_UPDATE, $event);
                return $this->refresh();
            }
        }

        return $this->render('account', [
            'model' => $model,
        ]);
    }


}
