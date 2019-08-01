<?php

namespace backend\modules\subjects\models;

use Yii;

/**
 * This is the model class for table "zdata_visit_schedule".
 *
 * @property string $id
 * @property string $widget_id
 * @property string $schedule_id
 * @property string $module_id
 * @property string $sitecode
 * @property string $visit_name
 * @property string $visit_parent
 * @property string $group_name
 * @property string $ezf_id
 * @property string $actual_date
 * @property string $visit_name_mapping
 * @property string $plan_date
 * @property string $earliest_date
 * @property string $latest_date
 * @property string $visit_cal_date
 * @property string $field_cal_date
 * @property string $form_list
 * @property string $progress
 * @property integer $open_node
 * @property integer $rstat
 * @property string $created_by
 * @property string $created_at
 * @property string $update_by
 * @property string $update_at
 * @property string $start_date

 */
class VisitSchedule extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'zdata_visit_schedule';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['open_node', 'rstat'], 'integer'],
            [['created_at', 'update_at','start_date'], 'safe'],
            [['id', 'widget_id', 'module_id','schedule_id','visit_cal_date','field_cal_date', 'ezf_id', 'created_by', 'update_by'], 'string', 'max' => 50],
            [['sitecode', 'plan_date', 'earliest_date', 'latest_date','progress'], 'string', 'max' => 11],
            [['visit_name'], 'string', 'max' => 200],
            [['group_name'], 'string', 'max' => 150],
            [['form_list'], 'string'],
            [['visit_parent', 'actual_date','visit_name_mapping'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'widget_id' => 'Widget ID',
            'module_id' => 'Module ID',
            'schedule_id'=>'Schedule ID',
            'sitecode' => 'Sitecode',
            'visit_name' => 'Visit Name',
            'group_name' => 'Group Name',
            'visit_parent' => 'Visit Parent',
            'ezf_id' => 'Ezf ID',
            'start_date'=>'Start Date',
            'actual_date' => 'Actual Date',
            'visit_name_mapping'=>'Visit Name Mapping',
            'plan_date' => 'Plan Date',
            'earliest_date' => 'Earliest Date',
            'latest_date' => 'Latest Date',
            'visit_cal_date' => 'Visit Cal Date',
            'field_cal_date' => 'Field Cal Date',
            'form_list'=>'Form List',
            'progress'=>'Progress',
            'open_node' => 'Open Node',
            'rstat' => 'Rstat',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'update_by' => 'Update By',
            'update_at' => 'Update At',
        ];
    }
}
