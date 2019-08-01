<?php

namespace backend\modules\manageproject\models;

use Yii;

/**
 * This is the model class for table "system_log".
 *
 * @property int $id
 * @property string $create_date Time/Date
 * @property string $create_by Name
 * @property string $action Action
 * @property string $detail
 */
class SystemLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'system_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['create_date'], 'safe'],
            [['detail'], 'string'],
            [['create_by', 'action'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'create_date' => Yii::t('app', 'Time/Date'),
            'create_by' => Yii::t('app', 'Name'),
            'action' => Yii::t('app', 'Action'),
            'detail' => Yii::t('app', 'Detail'),
        ];
    }
    public  function getUsers(){
        return @$this->hasOne(\common\modules\user\models\User::className(), ['id' => 'create_by']);
    }
    public  function getProfiles(){
        return @$this->hasOne(\common\modules\user\models\Profile::className(), ['user_id' => 'create_by']);
    }
}
