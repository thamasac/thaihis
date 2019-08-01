<?php

namespace backend\modules\topic\models;

use Yii;

/**
 * This is the model class for table "topic".
 *
 * @property int $id
 * @property string $name
 * @property string $detail
 * @property int $module_id
 * @property int $widget_id
 * @property int $create_by
 * @property string $create_at
 * @property int $update_by
 * @property string $update_at
 * @property int $rstat
 */
class Topic extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'topic';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            //[['detail','name'],'required'],
            [['module_id', 'widget_id',  'rstat'], 'integer'],
            [['detail'], 'string'],
            [['create_at', 'update_at','create_by', 'update_by','icon'], 'safe'],
            [['name'], 'string', 'max' => 255],
           
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('chanpan', 'ID'),
            'name' => Yii::t('chanpan', 'message'),
            'detail' => Yii::t('chanpan', 'detail'),
            'module_id' => Yii::t('chanpan', 'Module ID'),
            'widget_id' => Yii::t('chanpan', 'Widget ID'),
            'create_by' => Yii::t('chanpan', 'Create By'),
            'create_at' => Yii::t('chanpan', 'Create At'),
            'update_by' => Yii::t('chanpan', 'Update By'),
            'update_at' => Yii::t('chanpan', 'Update At'),
            'rstat' => Yii::t('chanpan', 'Rstat'),
        ];
    }
}
