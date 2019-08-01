<?php
/**
 * Created by PhpStorm.
 * User: kawin
 * Date: 7/11/2018
 * Time: 11:38 AM
 */

namespace common\modules\user\classes;


use Yii;

class InvitationInfo
{
    public $token = null;
    public $project_id = null;
    public $email = null;
    public $isHasInvite = null;

    function init()
    {
        $this->token = Yii::$app->request->get("token", null);
        $this->project_id = Yii::$app->request->get("project_id", null);
        $this->email = Yii::$app->request->get("email", null);
        $this->isHasInvite = $this->token != null && $this->project_id != null;
    }

    function initFromSession()
    {
        $infoJson = Yii::$app->session->get("inviteInfo", null);
        Yii::$app->session->remove("inviteInfo");
        $infoJson = json_decode($infoJson, true);
        $this->token = $infoJson["token"];
        $this->project_id = $infoJson["project_id"];
        $this->email = $infoJson["email"];
        $this->isHasInvite = $this->token != null && $this->project_id != null;
    }

    function saveToSession()
    {
        $infoJson = [
            "token" => $this->token,
            "project_id" => $this->project_id,
            "email" => $this->email,
        ];
        Yii::$app->session->set("inviteInfo", json_encode($infoJson));

    }

    function toArray(){
        return [
            "email" => $this->email ,
            "project_id" => $this->project_id,
            "token" => $this->token
            ];
    }
}