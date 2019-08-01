<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "const_hospital".
 *
 * @property integer $id
 * @property integer $ptid
 * @property string $xsourcex
 * @property string $xdepartmentx
 * @property integer $rstat
 * @property integer $user_create
 * @property string $create_date
 * @property integer $user_update
 * @property string $update_date
 * @property string $target
 * @property string $code
 * @property string $name
 * @property string $error
 * @property string $sitecode
 * @property string $ptcode
 * @property string $ptcodefull
 * @property string $hsitecode
 * @property string $hptcode
 */
class Hospital extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'const_hospital';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ptid', 'rstat', 'user_create', 'user_update'], 'integer'],
            [['xsourcex'], 'required'],
            [['create_date', 'update_date'], 'safe'],
            [['target', 'error'], 'string'],
            [['xsourcex', 'xdepartmentx', 'ptcodefull'], 'string', 'max' => 20],
            [['code', 'name'], 'string', 'max' => 255],
            [['sitecode', 'ptcode', 'hsitecode', 'hptcode'], 'string', 'max' => 10],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ptid' => 'Ptid',
            'xsourcex' => 'Xsourcex',
            'xdepartmentx' => 'Xdepartmentx',
            'rstat' => 'Rstat',
            'user_create' => 'User Create',
            'create_date' => 'Create Date',
            'user_update' => 'User Update',
            'update_date' => 'Update Date',
            'target' => 'Target',
            'code' => 'Code',
            'name' => 'Name',
            'error' => 'Error',
            'sitecode' => 'Sitecode',
            'ptcode' => 'Ptcode',
            'ptcodefull' => 'Ptcodefull',
            'hsitecode' => 'Hsitecode',
            'hptcode' => 'Hptcode',
        ];
    }
}
