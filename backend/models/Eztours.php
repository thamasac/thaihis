<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "eztours".
 *
 * @property int $id
 * @property string $element
 * @property string $title
 * @property string $content
 * @property string $placement
 * @property string $smartPlacement
 * @property int $widget_id
 */
class Eztours extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'eztours';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
//            [['id'], 'required'],
            [['id', 'widget_id','smartPlacement'], 'integer'],
            [['content'], 'string'],
            [['element', 'title'], 'string', 'max' => 255],
            [['placement'], 'string', 'max' => 50],
            [['id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('eztour', 'ID'),
            'element' => Yii::t('eztour', 'Element'),
            'title' => Yii::t('eztour', 'Title'),
            'content' => Yii::t('eztour', 'Content'),
            'placement' => Yii::t('eztour', 'Placement'),
            'smartPlacement' => Yii::t('eztour', 'Smart Placement'),
            'widget_id' => Yii::t('eztour', 'Widget ID'),
        ];
    }
}
