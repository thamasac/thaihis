<?php
/**
 * Created by PhpStorm.
 * User: kawin
 * Date: 3/7/2018
 * Time: 4:52 PM
 */

namespace backend\modules\api\v1\models;


use yii\db\ActiveRecord;

class LogApiModel extends ActiveRecord
{
    public static function tableName()
    {
        return 'log_api';
    }
}