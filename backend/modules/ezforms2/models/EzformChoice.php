<?php

namespace backend\modules\ezforms2\models;

use Yii;

/**
 * This is the model class for table "ezform_choice".
 *
 * @property integer $ezf_choice_id
 * @property integer $ezf_id
 * @property integer $ezf_field_id
 * @property string $ezf_choicevalue
 * @property string $ezf_choicelabel
 * @property string $ezf_choiceetc
 * @property integer $ezf_choice_col
 * @property string $ezf_version
 *
 * @property EzformFields $ezfField
 */
class EzformChoice extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ezform_choice';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ezf_choice_id'], 'required'],
            [['ezf_choice_id', 'ezf_field_id', 'ezf_id','ezf_choiceetc', 'ezf_choice_col'], 'integer'],
            [['ezf_choicevalue', 'ezf_choicelabel'], 'string', 'max' => 255],
            [['ezf_version'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ezf_choice_id' => Yii::t('ezform', 'Choice ID'),
            'ezf_id' => Yii::t('ezform', 'Ezform ID'),
            'ezf_version' => Yii::t('ezform', 'Version'),
            'ezf_field_id' => Yii::t('ezform', 'Field ID'),
            'ezf_choicevalue' => Yii::t('ezform', 'Value'),
            'ezf_choicelabel' => Yii::t('ezform', 'Label'),
            'ezf_choiceetc' => Yii::t('ezform', 'etc'),
            'ezf_choice_col' => Yii::t('ezform', 'Column'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEzfField()
    {
        return $this->hasOne(EzformFields::className(), ['ezf_field_id' => 'ezf_field_id']);
    }
}
