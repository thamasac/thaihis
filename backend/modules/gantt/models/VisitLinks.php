<?php

namespace backend\modules\gantt\models;

use Yii;

/**
 * This is the model class for table "zdata_visit_links".
 *
 * @property string $source
 * @property string $target
 * @property integer $type
 * @property string $sitecode
 * @property string $widget_id
 * @property string $module_id
 * @property string $created_by
 * @property string $created_at
 * @property string $update_by
 * @property string $update_at
 */
class VisitLinks extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'zdata_visit_links';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['source', 'target', 'type', 'widget_id'], 'required'],
            [['type'], 'integer'],
            [['created_at', 'update_at'], 'safe'],
            [['source', 'target', 'sitecode', 'widget_id', 'module_id', 'created_by', 'update_by'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'source' => 'Source',
            'target' => 'Target',
            'type' => 'Type',
            'sitecode' => 'Sitecode',
            'widget_id' => 'Widget ID',
            'module_id' => 'Module ID',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'update_by' => 'Update By',
            'update_at' => 'Update At',
        ];
    }
}
