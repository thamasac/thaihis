<?php

namespace backend\modules\topic\models;

use Yii;

/**
 * This is the model class for table "ezsetup".
 *
 * @property int $id
 * @property string $priority
 * @property string $status
 * @property string $steps
 * @property string $action_taken
 * @property int $parent_id
 * @property int $forder
 */
class Ezsetup extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ezsetup';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['steps'], 'string'],
            [['parent_id', 'forder'], 'integer'],
            [['priority', 'status', 'action_taken'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('topic', 'ID'),
            'priority' => Yii::t('topic', 'Priority'),
            'status' => Yii::t('topic', 'Status'),
            'steps' => Yii::t('topic', 'Steps'),
            'action_taken' => Yii::t('topic', 'Action Taken'),
            'parent_id' => Yii::t('topic', 'Parent ID'),
            'forder' => Yii::t('topic', 'Forder'),
        ];
    }
}
