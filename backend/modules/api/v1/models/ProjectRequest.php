<?php
/**
 * Created by PhpStorm.
 * User: developer
 * Date: 3/7/2018 AD
 * Time: 22:05
 */

namespace backend\modules\api\v1\models;


use yii\db\ActiveRecord;

class ProjectRequest extends ActiveRecord
{
    public static function tableName()
    {
        return 'zdata_project_join_request';
    }
}