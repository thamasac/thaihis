<?php
namespace backend\modules\manage_user\models;
use yii\base\Model;
class CNUserForm extends Model{
    //put your code here
    public $user_id;
    public $sitecode;
    public $department;
    public $email;
    
    public function rules(){
        return [
            [['user_id','sitecode','department'],'required']
        ];
    }
}
