<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\controllers;

/**
 * Description of TestController
 *
 * @author Sammy Guergachi <sguergachi at gmail.com>
 */

use backend\modules\api\v1\models\SocialTokenModel;
use backend\modules\manageproject\classes\CNEzform;
use common\modules\user\classes\CNSocialFunc;
use yii\db\Exception;
use Yii;

class SocialMediaController extends \yii\web\Controller
{

    public function beforeAction($action)
    {
        $project = Yii::$app->request->get('target_project', 'NONE');
        if ($project != 'NONE')
            \Yii::$app->getSession()->setFlash('target_project', $project);
        return parent::beforeAction($action);
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'auth' => [
                'class' => 'yii\authclient\AuthAction',
                'successCallback' => [$this, 'onAuthSuccess'],
            ],
        ];
    }

    public function onAuthSuccess($client)
    {
        $project = Yii::$app->session->getFlash('target_project');
        if ($project != null) {
            try {
                $token = Yii::$app->security->generateRandomString(64);
                $socialToken = new SocialTokenModel();
                $attributes = $client->getUserAttributes();
                $email = 'NONE';

                if($client->getId() == 'facebook'){
                    $email = $attributes['email'];
                }else if($client->getId() == 'google'){
                    $email = $attributes['emails'][0]['value'];
                }
                $user = CNSocialFunc::checkUser('', $email);
                $socialToken->setAttributes([
                    'token' => $token,
                    'vendor' => $client->getId(),
                    'user_id' => $user->id,
                    'target_project' => $project
                ],false);
                $socialToken->save();
                $projectData = CNEzform::getDynamicTableByDataId("zdata_create_project", $project);
                $purl = $projectData['projurl'].".".$projectData['projdomain'];
                return $this->redirect("https://$purl/api/login/social-login?token=$token");
            } catch (Exception $db) {
                var_dump($db);
            }
        }else{
            (new \common\modules\user\classes\AuthHandler($client))->handle();
        }
    }
}
