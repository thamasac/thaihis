<?php

namespace backend\modules\gantt\models;

use Yii;

/**
 * This is the model class for table "visit_procedure".
 *
 * @property string $node_id
 * @property string $mid
 * @property string $widget_id
 * @property string $start_date
 * @property integer $duration
 * @property string $text
 * @property string $progress
 * @property string $sortorder
 * @property string $parent_id
 * @property integer $ref_ezform
 * @property integer $open_state
 */
class GanttProcedure extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'visit_procedure';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['node_id'], 'required'],
            [['start_date', 'progress'], 'safe'],
            [['duration', 'ref_ezform', 'open_state'], 'integer'],
            [['text'], 'string'],
            [['node_id', 'mid', 'widget_id'], 'string', 'max' => 100],
            [['sortorder', 'parent_id'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'node_id' => 'Node ID',
            'mid' => 'Mid',
            'widget_id' => 'Widget ID',
            'start_date' => 'Start Date',
            'duration' => 'Duration',
            'text' => 'Text',
            'progress' => 'Progress',
            'sortorder' => 'Sortorder',
            'parent_id' => 'Parent ID',
            'ref_ezform' => 'Ref Ezform',
            'open_state' => 'Open State',
        ];
    }
}
