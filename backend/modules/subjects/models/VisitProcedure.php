<?php

namespace backend\modules\subjects\models;

use Yii;

/**
 * This is the model class for table "zdata_visit_procedure".
 *
 * @property string $id
 * @property string $module_id
 * @property string $widget_id
 * @property string $visit_name
 * @property string $group_name
 * @property string $target
 * @property string $procedure_name
 * @property string $sitecode
 * @property string $create_by
 * @property string $create_at
 * @property string $update_by
 * @property string $update_at
 */
class VisitProcedure extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'zdata_visit_procedure';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['create_at', 'update_at'], 'safe'],
            [['id', 'module_id', 'widget_id','target', 'procedure_name', 'visit_name', 'group_name', 'sitecode', 'create_by', 'update_by'], 'string', 'max' => 150],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'module_id' => 'Module ID',
            'widget_id' => 'Widget ID',
            'target' => 'Target',
            'procedure_name' => 'Procedure Name',
            'visit_name' => 'Visit Name',
            'group_name' => 'Group Name',
            'stecode' => 'Sitecode',
            'create_by' => 'Create By',
            'create_at' => 'Create At',
            'update_by' => 'Update By',
            'update_at' => 'Update At',
        ];
    }
}
