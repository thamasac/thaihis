<?php

namespace backend\modules\ezforms2\models;

use Yii;

/**
 * This is the model class for table "random_code".
 *
 * @property integer $id
 * @property string $name
 * @property string $code_random
 * @property integer $max_index
 * @property integer $code_index
 * @property integer $user_create
 * @property integer $ezf_id
 */
class RandomCode extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'random_code';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'code_random'], 'required'],
            [['id', 'max_index', 'code_index', 'user_create', 'ezf_id'], 'integer'],
            [['code_random'], 'string'],
            [['name'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'code_random' => 'Code Random',
            'max_index' => 'Max Index',
            'code_index' => 'Code Index',
            'user_create' => 'User Create',
            'ezf_id' => 'Ezf ID',
        ];
    }
}
