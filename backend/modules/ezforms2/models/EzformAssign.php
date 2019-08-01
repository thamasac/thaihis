<?php

namespace backend\modules\ezforms2\models;

use Yii;

/**
 * This is the model class for table "ezform_assign".
 *
 * @property string $ezf_id
 * @property string $user_id
 * @property integer $status
 */
class EzformAssign extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ezform_assign';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ezf_id', 'user_id'], 'required'],
            [['ezf_id', 'user_id', 'status'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ezf_id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('yii', 'User'),
            'status' => Yii::t('ezform', 'Status'),
        ];
    }
}
