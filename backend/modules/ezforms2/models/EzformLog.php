<?php

namespace backend\modules\ezforms2\models;

use Yii;

/**
 * This is the model class for table "ezform_log".
 *
 * @property integer $id
 * @property integer $data_id
 * @property integer $ezf_id
 * @property string $sql_log
 * @property integer $user_id
 * @property string $create_date
 * @property string $xsourcex
 * @property integer $rstat
 */
class EzformLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
	return 'ezform_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
	return [
            [['id', 'data_id', 'ezf_id', 'user_id'], 'required'],
            [['id', 'data_id', 'ezf_id', 'user_id', 'rstat'], 'integer'],
            [['sql_log'], 'string'],
            [['create_date'], 'safe'],
            [['xsourcex'], 'string', 'max' => 10]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
	return [
	    'id' => Yii::t('ezform', 'ID'),
	    'data_id' => Yii::t('ezform', 'Data ID'),
	    'ezf_id' => Yii::t('ezform', 'Ezf ID'),
	    'sql_log' => Yii::t('ezform', 'Sql Log'),
	    'user_id' => Yii::t('ezform', 'User ID'),
	    'create_date' => Yii::t('ezform', 'Create Date'),
	    'xsourcex' => Yii::t('ezform', 'Xsourcex'),
	    'rstat' => Yii::t('ezform', 'Rstat'),
	];
    }
}
