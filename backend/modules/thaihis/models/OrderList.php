<?php

namespace backend\modules\thaihis\models;

use Yii;

/**
 * This is the model class for table "zdata_order_lists".
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
 * @property string $order_code
 * @property string $order_name
 * @property string $group_code
 * @property string $group_type
 * @property string $fin_item_code
 * @property string $nhso_code
 * @property string $unit_price
 * @property string $unit_price_checkup
 * @property string $order_ezf_id
 * @property string $flag_pay
 * @property string $external_flag
 */
class OrderList extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'zdata_order_lists';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id', 'ptid', 'target', 'rstat', 'user_create', 'user_update'], 'integer'],
            [['error', 'order_ezf_id'], 'string'],
            [['create_date', 'update_date'], 'safe'],
            [['sitecode', 'ptcode', 'hptcode', 'hsitecode'], 'string', 'max' => 10],
            [['ptcodefull', 'xsourcex', 'xdepartmentx', 'sys_lat', 'sys_lng', 'group_code', 'group_type', 'fin_item_code', 'flag_pay'], 'string', 'max' => 20],
            [['ezf_version'], 'string', 'max' => 100],
            [['order_code', 'order_name', 'nhso_code', 'unit_price', 'unit_price_checkup'], 'string', 'max' => 150],
            [['external_flag'], 'string', 'max' => 1],
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
            'order_code' => 'Order Code',
            'order_name' => 'Order Name',
            'group_code' => 'Group Code',
            'group_type' => 'Group Type',
            'fin_item_code' => 'Fin Item Code',
            'nhso_code' => 'Nhso Code',
            'unit_price' => 'Unit Price',
            'unit_price_checkup' => 'Unit Price Checkup',
            'order_ezf_id' => 'Order Ezf ID',
            'flag_pay' => 'Flag Pay',
            'external_flag' => 'External Flag',
        ];
    }
}
