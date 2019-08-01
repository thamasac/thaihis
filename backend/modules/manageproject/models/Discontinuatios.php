<?php

namespace backend\modules\manageproject\models;

use Yii;

/**
 * This is the model class for table "discontinuatios".
 *
 * @property int $id
 * @property int $project_id
 * @property string $descriptions
 * @property int $user_id
 * @property int $project_type
 */
class Discontinuatios extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'discontinuatios';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
             
            [['project_id', 'user_id', 'project_type', 'status'], 'integer'],
            [['descriptions'], 'string'],
            [['id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('project', 'ID'),
            'project_id' => Yii::t('project', 'Project ID'),
            'descriptions' => Yii::t('project', 'Descriptions'),
            'user_id' => Yii::t('project', 'User ID'),
            'project_type' => Yii::t('project', 'Project Type'),
        ];
    }
}
