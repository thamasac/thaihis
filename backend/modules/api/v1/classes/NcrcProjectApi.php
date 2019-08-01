<?php
/**
 * Created by PhpStorm.
 * User: kawin
 * Date: 7/10/2018
 * Time: 11:01 AM
 */

namespace backend\modules\api\v1\classes;

use backend\modules\api\v1\models\SocialTokenModel;
use common\modules\user\models\User;
use Yii;
use yii\httpclient\Client;

class NcrcProjectApi
{
    public static function addAdminUser($userData,$profileData,$role){
        if (User::findIdentity($userData["id"]) == null) {
            $profileData["sitecode"] = "00";
            Yii::$app->db->createCommand()->insert("user", $userData)->execute();
            Yii::$app->db->createCommand()->insert("profile", $profileData)->execute();
        }
        return ["success" => true];
    }

    /**
     * @param $token
     * @return ValidateResponse
     * @throws \yii\base\InvalidConfigException
     */
    public static function sendValidateSocialToken($token){
        $client = new Client();
        $validateResponse = new ValidateResponse();
        $main_url = isset(Yii::$app->params['main_url']) ? Yii::$app->params['main_url'] : '';
        $response = $client->createRequest()
            ->setMethod('POST')
            ->setUrl("https://$main_url/api/ncrc-project/validate-social-token")
            ->setData(['token'=> $token , 'project' => '1542704131025288400'])
            ->send();
        if ($response->isOk) {
            $res = $response->getData();
            if($res["success"]){
                $validateResponse->isValid = true;
                $validateResponse->userId =  $res['data'];
                return $validateResponse;
            }else{
                $validateResponse->isValid = false;
                $validateResponse->userId = $res;
                return $validateResponse;
            }
        }
        return $validateResponse;
    }

    /**
     * @param $token
     * @param $project
     * @return array|SocialTokenModel|null|\yii\db\ActiveRecord
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public static function validateSocialToken($token, $project){
        $socialToken = SocialTokenModel::find()->where(['token'=> $token,'rstat'=> 0 ,'target_project'=> $project])->one();
        if($socialToken){
            $socialToken->setAttribute('rstat','2');
            $socialToken->update();
            return $socialToken;
        }else{
            return null;
        }
    }
}

class ValidateResponse{
    public $isValid = false;
    public $userId = null;

}