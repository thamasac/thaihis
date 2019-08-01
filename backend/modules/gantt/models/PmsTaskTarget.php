<?php

namespace backend\modules\gantt\models;

use Yii;

/**
 * This is the model class for table "pms_task_target".
 *
 * @property string $id
 * @property string $dataid
 * @property string $ezf_id
 * @property string $parent
 * @property string $target
 * @property string $task_name
 * @property string $start_date
 * @property string $end_date
 * @property int $priority
 * @property string $progress
 * @property int $open_node
 * @property int $order_node
 * @property int $task_type
 * @property int $rstat
 * @property int $response_id
 * @property int $actual_date
 * @property int $progress
 * @property int $user_create
 * @property int $create_date
 * @property int $user_update
 * @property int $update_date
 * @property int $assign_user
 * @property int $assign_role
 */
class PmsTaskTarget extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pms_task_target';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['start_date', 'end_date', 'create_date', 'update_date','actual_date'], 'safe'],
            [['priority', 'open_node', 'order_node','rstat','response_id'], 'integer'],
            [['dataid', 'ezf_id', 'parent', 'target','dataid', 'ezf_id', 'parent','user_create','user_update'], 'string', 'max' => 150],
            [['task_name'], 'string', 'max' => 255],
            [['progress','task_type','sitecode'], 'string', 'max' => 30],
            [['dataid', 'ezf_id', 'parent'], 'unique', 'targetAttribute' => ['dataid', 'ezf_id', 'parent']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Id',
            'dataid' => 'Dataid',
            'ezf_id' => 'Ezf ID',
            'parent' => 'Parent',
            'target' => 'Target',
            'task_name' => 'Task Name',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'priority' => 'Priority',
            'progress' => 'Progress',
            'open_node' => 'Open Node',
            'order_node' => 'Order Node',
            'task_type'=>'Task Type',
            'rstat'=>'Rstat'
        ];
    }
}
