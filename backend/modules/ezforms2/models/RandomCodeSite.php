<?php

namespace backend\modules\ezforms2\models;

use Yii;

/**
 * This is the model class for table "random_code_site".
 *
 * @property integer $id
 * @property integer $random_id
 * @property string $sitecode
 * @property integer $ezf_id
 * @property integer $data_id
 * @property string $code
 * @property string $key
 */
class RandomCodeSite extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'random_code_site';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id', 'random_id', 'ezf_id', 'data_id'], 'integer'],
            [['sitecode', 'key'], 'string', 'max' => 10],
            [['code'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'random_id' => 'Random ID',
            'sitecode' => 'Sitecode',
            'ezf_id' => 'Ezf ID',
            'data_id' => 'Data ID',
            'code' => 'Code',
            'key' => 'Key',
        ];
    }
}
