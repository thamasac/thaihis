<?php
/**
 * Created by PhpStorm.
 * User: kawin
 * Date: 3/9/2018
 * Time: 11:24 AM
 */

namespace backend\modules\api\v1\models;


use yii\db\ActiveRecord;

class UserApplicationInfo extends ActiveRecord
{
    public static function tableName()
    {
        return 'user_application';
    }

}