<?php

namespace common\modules\user\models;

use Yii;

/**
 * This is the model class for table "auth".
 *
 * @property int $id
 * @property int $user_id
 * @property string $source
 * @property string $source_id
 */
class Auth extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'auth';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id'], 'integer'],
            [['source', 'source_id'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('chanpan', 'ID'),
            'user_id' => Yii::t('chanpan', 'User ID'),
            'source' => Yii::t('chanpan', 'Source'),
            'source_id' => Yii::t('chanpan', 'Source ID'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return AuthQuery the active query used by this AR class.
     */
    public static function getUser()
    {
         return $this->hasOne(User::className(), ['id'=>'user_id']);
    }
}
