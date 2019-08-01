<?php
namespace backend\modules\ezforms2\models;
class CNProfileTcc extends \yii\db\ActiveRecord{
    public static function tableName()
    {
        return '{{%profile}}';
    }
    public function rules() {
        return[
            [['user_id','sitecode'],'required']
        ];
    }
}
