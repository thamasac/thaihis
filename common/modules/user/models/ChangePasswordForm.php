<?php

namespace common\modules\user\models;
use yii\base\Model;
use Yii;
class ChangePasswordForm extends Model{
    public $new_password;
    public $confirm_password;
    
    public function rules(){
        return[
            [['new_password','confirm_password'],'required'],
            ['new_password','string', 'max' => 72, 'min' => 6],
            ['confirm_password', 'compare', 'compareAttribute'=>'new_password', 'message'=> Yii::t('chanpan','Passwords don\'t match')]
        ];
    }
    public function attributeLabels(){
        return [
            'new_password'=> \Yii::t('chanpan','New password'),
            'confirm_password'=> \Yii::t('chanpan','Confirm password'),
        ];
    }
}
