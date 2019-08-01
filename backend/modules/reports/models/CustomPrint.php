<?php

namespace backend\modules\reports\models;

use Yii;

/**
 * This is the model class for table "zdata_1537848949032767100".
 *
 * @property int $id ไอดี
 * @property int $ptid ไอดีเป้าหมายแรก
 * @property string $sitecode หน่วยงานแรก
 * @property string $ptcode รหัสเป้าหมายหน่วยงานแรก
 * @property string $ptcodefull ptcode+sitecode
 * @property int $target ไอดีของเป้าหมาย
 * @property string $hptcode รหัสเป้าหมายของหน่วยงานนั้น
 * @property string $hsitecode หน่วยงานที่บันทึก(xsourcex)
 * @property string $xsourcex หน่วยงานที่บันทึก
 * @property string $xdepartmentx ฝ่ายงานที่บันทึก
 * @property string $sys_lat
 * @property string $sys_lng
 * @property string $error
 * @property int $rstat สถานะ 
 * @property int $user_create
 * @property string $create_date
 * @property int $user_update
 * @property string $update_date
 * @property string $ezf_version
 * @property string $ezf_id
 * @property string $template
 * @property string $default
 * @property string $template_id
 * @property string $template_name
 */
class CustomPrint extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'zdata_1537848949032767100';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id', 'ptid', 'target', 'rstat', 'user_create', 'user_update'], 'integer'],
            [['error', 'template'], 'string'],
            [['create_date', 'update_date'], 'safe'],
            [['sitecode', 'ptcode', 'hptcode', 'hsitecode'], 'string', 'max' => 10],
            [['ptcodefull', 'xsourcex', 'xdepartmentx', 'sys_lat', 'sys_lng'], 'string', 'max' => 20],
            [['ezf_version'], 'string', 'max' => 100],
            [['ezf_id', 'default', 'template_id', 'template_name'], 'string', 'max' => 150],
            [['id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ptid' => 'Ptid',
            'sitecode' => 'Sitecode',
            'ptcode' => 'Ptcode',
            'ptcodefull' => 'Ptcodefull',
            'target' => 'Target',
            'hptcode' => 'Hptcode',
            'hsitecode' => 'Hsitecode',
            'xsourcex' => 'Xsourcex',
            'xdepartmentx' => 'Xdepartmentx',
            'sys_lat' => 'Sys Lat',
            'sys_lng' => 'Sys Lng',
            'error' => 'Error',
            'rstat' => 'Rstat',
            'user_create' => 'User Create',
            'create_date' => 'Create Date',
            'user_update' => 'User Update',
            'update_date' => 'Update Date',
            'ezf_version' => 'Ezf Version',
            'ezf_id' => 'Ezf ID',
            'template' => 'Template',
            'default' => 'Default',
            'template_id' => 'Template ID',
            'template_name' => 'Template Name',
        ];
    }
}