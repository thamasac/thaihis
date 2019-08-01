<?php

namespace backend\modules\topic\models;

use Yii;

/**
 * This is the model class for table "zdata_1542121296042041400".
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
 * @property string $widget_id
 * @property string $header_text
 * @property string $content
 * @property string $user_id
 * @property string $assign_by
 * @property string $assign_date
 * @property string $status_complete
 * @property string $detail
 * @property string $forder
 */
class CompleteTask extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'zdata_1542120564041895900';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'ptid', 'target', 'rstat', 'user_create', 'user_update', 'forder'], 'integer'],
            [['error', 'content', 'user_id', 'assign_by', 'detail'], 'string'],
            [['create_date', 'update_date'], 'safe'],
            [['sitecode', 'ptcode', 'hptcode', 'hsitecode'], 'string', 'max' => 10],
            [['ptcodefull', 'xsourcex', 'xdepartmentx', 'sys_lat', 'sys_lng', 'assign_date', 'status_complete'], 'string', 'max' => 20],
            [['ezf_version'], 'string', 'max' => 100],
            [['widget_id', 'header_text'], 'string', 'max' => 150],
            //[[''], 'string', 'max' => 50],
            [['id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('topic', 'ไอดี'),
            'ptid' => Yii::t('topic', 'ไอดีเป้าหมายแรก'),
            'sitecode' => Yii::t('topic', 'หน่วยงานแรก'),
            'ptcode' => Yii::t('topic', 'รหัสเป้าหมายหน่วยงานแรก'),
            'ptcodefull' => Yii::t('topic', 'ptcode+sitecode'),
            'target' => Yii::t('topic', 'ไอดีของเป้าหมาย'),
            'hptcode' => Yii::t('topic', 'รหัสเป้าหมายของหน่วยงานนั้น'),
            'hsitecode' => Yii::t('topic', 'หน่วยงานที่บันทึก(xsourcex)'),
            'xsourcex' => Yii::t('topic', 'หน่วยงานที่บันทึก'),
            'xdepartmentx' => Yii::t('topic', 'ฝ่ายงานที่บันทึก'),
            'sys_lat' => Yii::t('topic', 'Sys Lat'),
            'sys_lng' => Yii::t('topic', 'Sys Lng'),
            'error' => Yii::t('topic', 'Error'),
            'rstat' => Yii::t('topic', 'สถานะ '),
            'user_create' => Yii::t('topic', 'User Create'),
            'create_date' => Yii::t('topic', 'Create Date'),
            'user_update' => Yii::t('topic', 'User Update'),
            'update_date' => Yii::t('topic', 'Update Date'),
            'ezf_version' => Yii::t('topic', 'Ezf Version'),
            'widget_id' => Yii::t('topic', 'Widget ID'),
            'header_text' => Yii::t('topic', 'Header Text'),
            'content' => Yii::t('topic', 'Content'),
            'user_id' => Yii::t('topic', 'User ID'),
            'assign_by' => Yii::t('topic', 'Assign By'),
            'assign_date' => Yii::t('topic', 'Assign Date'),
            'status_complete' => Yii::t('topic', 'Status Complete'),
            'detail' => Yii::t('topic', 'Detail'),
            'forder' => Yii::t('topic', 'Forder'),
        ];
    }
}
