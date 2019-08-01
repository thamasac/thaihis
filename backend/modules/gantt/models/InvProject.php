<?php

namespace backend\modules\gantt\models;

use Yii;

/**
 * This is the model class for table "inv_project".
 *
 * @property integer $id
 * @property string $project
 * @property integer $status
 * @property integer $share
 * @property integer $approve
 */
class InvProject extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'inv_project';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id', 'status', 'share', 'approve'], 'integer'],
            [['project'], 'string', 'max' => 80]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'project' => 'Project',
            'status' => 'Status',
            'share' => 'Share',
            'approve' => 'Approve',
        ];
    }
}
