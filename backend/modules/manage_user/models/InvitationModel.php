<?php
namespace backend\modules\manage_user\models;
use yii\db\ActiveRecord;

/**
 * Created by PhpStorm.
 * User: kawin
 * Date: 7/10/2018
 * Time: 3:52 PM
 */

class InvitationModel extends ActiveRecord
{
    public static function tableName()
    {
        return "invitation_project";
    }
}