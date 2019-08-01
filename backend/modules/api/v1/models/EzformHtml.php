<?php
/**
 * Created by PhpStorm.
 * User: kawin
 * Date: 4/2/2018
 * Time: 11:52 AM
 */

namespace backend\modules\api\v1\models;


use yii\db\ActiveRecord;

class EzformHtml extends ActiveRecord
{
    public static function tableName()
    {
        return 'ezform_html_template';
    }
}