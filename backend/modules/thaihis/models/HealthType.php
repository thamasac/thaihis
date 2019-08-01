<?php

namespace backend\modules\thaihis\models;

use Yii;

/**
 * This is the model class for table "zdata_health_type".
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
 * @property string $type_name
 * @property string $enable_cash
 * @property string $enable_cash_text
 * @property string $enable_gov
 * @property string $enable_officer
 * @property string $enable_company
 * @property string $enable_company_text
 * @property string $enable_gov_text
 * @property string $enable_officer_text
 * @property string $price
 * @property string $unique_code
 * @property string $enable_gov_extra_cost
 * @property string $enable_cash_extra_cost
 * @property string $enable_officer_extra_cost
 * @property string $enable_company_extra_cost
 * @property string $enable_gender
 * @property string $order_ref
 * @property string $v028_1
 * @property string $enable_age_below_35
 * @property string $enable_age_upper_35
 * @property string $order_ref_02
 * @property string $order_ref_03
 * @property string $init_cash_over_35
 * @property string $init_officer_over_35
 * @property string $init_gov_over_35
 * @property string $init_company_over_35
 * @property string $init_company_below_35
 * @property string $init_gov_below_35
 * @property string $init_officer_below_35
 * @property string $init_cash_below_35
 * @property string $init_company_over_50
 * @property string $init_gov_over_50
 * @property string $init_officer_over_50
 * @property string $init_cash_over_50
 * @property string $order_ref_04
 * @property string $order_ref_05
 * @property string $order_ref_06
 * @property string $order_ref_07
 * @property string $order_ref_08
 */
class HealthType extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'zdata_health_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id', 'ptid', 'target', 'rstat', 'user_create', 'user_update'], 'integer'],
            [['error'], 'string'],
            [['create_date', 'update_date'], 'safe'],
            [['sitecode', 'ptcode', 'hptcode', 'hsitecode'], 'string', 'max' => 10],
            [['ptcodefull', 'xsourcex', 'xdepartmentx', 'sys_lat', 'sys_lng', 'enable_gender', 'order_ref', 'order_ref_02', 'order_ref_03', 'order_ref_04', 'order_ref_05', 'order_ref_06', 'order_ref_07', 'order_ref_08'], 'string', 'max' => 20],
            [['ezf_version', 'v028_1'], 'string', 'max' => 100],
            [['type_name', 'enable_cash_text', 'enable_company_text', 'enable_gov_text', 'enable_officer_text', 'unique_code'], 'string', 'max' => 150],
            [['enable_cash', 'enable_gov', 'enable_officer', 'enable_company', 'enable_age_below_35', 'enable_age_upper_35', 'init_cash_over_35', 'init_officer_over_35', 'init_gov_over_35', 'init_company_over_35', 'init_company_below_35', 'init_gov_below_35', 'init_officer_below_35', 'init_cash_below_35', 'init_company_over_50', 'init_gov_over_50', 'init_officer_over_50', 'init_cash_over_50'], 'string', 'max' => 1],
            [['price', 'enable_gov_extra_cost', 'enable_cash_extra_cost', 'enable_officer_extra_cost', 'enable_company_extra_cost'], 'string', 'max' => 50],
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
            'type_name' => 'Type Name',
            'enable_cash' => 'Enable Cash',
            'enable_cash_text' => 'Enable Cash Text',
            'enable_gov' => 'Enable Gov',
            'enable_officer' => 'Enable Officer',
            'enable_company' => 'Enable Company',
            'enable_company_text' => 'Enable Company Text',
            'enable_gov_text' => 'Enable Gov Text',
            'enable_officer_text' => 'Enable Officer Text',
            'price' => 'Price',
            'unique_code' => 'Unique Code',
            'enable_gov_extra_cost' => 'Enable Gov Extra Cost',
            'enable_cash_extra_cost' => 'Enable Cash Extra Cost',
            'enable_officer_extra_cost' => 'Enable Officer Extra Cost',
            'enable_company_extra_cost' => 'Enable Company Extra Cost',
            'enable_gender' => 'Enable Gender',
            'order_ref' => 'Order Ref',
            'v028_1' => 'V028 1',
            'enable_age_below_35' => 'Enable Age Below 35',
            'enable_age_upper_35' => 'Enable Age Upper 35',
            'order_ref_02' => 'Order Ref 02',
            'order_ref_03' => 'Order Ref 03',
            'init_cash_over_35' => 'Init Cash Over 35',
            'init_officer_over_35' => 'Init Officer Over 35',
            'init_gov_over_35' => 'Init Gov Over 35',
            'init_company_over_35' => 'Init Company Over 35',
            'init_company_below_35' => 'Init Company Below 35',
            'init_gov_below_35' => 'Init Gov Below 35',
            'init_officer_below_35' => 'Init Officer Below 35',
            'init_cash_below_35' => 'Init Cash Below 35',
            'init_company_over_50' => 'Init Company Over 50',
            'init_gov_over_50' => 'Init Gov Over 50',
            'init_officer_over_50' => 'Init Officer Over 50',
            'init_cash_over_50' => 'Init Cash Over 50',
            'order_ref_04' => 'Order Ref 04',
            'order_ref_05' => 'Order Ref 05',
            'order_ref_06' => 'Order Ref 06',
            'order_ref_07' => 'Order Ref 07',
            'order_ref_08' => 'Order Ref 08',
        ];
    }
}
