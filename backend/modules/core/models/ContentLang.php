<?php

namespace backend\modules\core\models;

use Yii;

/**
 * This is the model class for table "content_lang".
 *
 * @property integer $id
 * @property integer $obj_id
 * @property string $language
 * @property string $title
 * @property string $content
 */
class ContentLang extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
	return 'content_lang';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
	return [
            [['id'], 'required'],
            [['id', 'obj_id'], 'integer'],
            [['content'], 'string'],
            [['language'], 'string', 'max' => 10],
            [['title'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
	return [
	    'id' => Yii::t('ezform', 'ID'),
	    'obj_id' => Yii::t('ezform', 'Obj ID'),
	    'language' => Yii::t('ezform', 'Language'),
	    'title' => Yii::t('ezform', 'Title'),
	    'content' => Yii::t('ezform', 'Content'),
	];
    }
}
