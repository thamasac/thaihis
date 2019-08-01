<?php

namespace backend\modules\ezforms2\models;

use Yii;

/**
 * This is the model class for table "ezform_mobile".
 *
 * @property integer $ezf_id
 * @property integer $userid
 * @property integer $forder
 */
class EzformMobile extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ezform_mobile';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ezf_id', 'userid'], 'required'],
            [['ezf_id', 'userid', 'forder'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ezf_id' => Yii::t('ezform', 'Ezf ID'),
            'userid' => Yii::t('ezform', 'Userid'),
            'forder' => Yii::t('ezform', 'Forder'),
        ];
    }

    public static function getOrder($userid) {
        $sql = "SELECT MAX(forder)+1 AS num
		FROM ezform_mobile
		WHERE userid=:userid
		";
        $order = Yii::$app->db->createCommand($sql, [':userid'=>$userid])->queryScalar();
        return isset($order)?(int)$order:1;
    }
}
