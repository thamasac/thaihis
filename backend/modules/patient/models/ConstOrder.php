<?php

namespace backend\modules\patient\models;

use Yii;

/**
 * This is the model class for table "const_order".
 *
 * @property string $order_code
 * @property string $order_name
 * @property string $group_code
 * @property string $group_type
 * @property string $fin_item_code
 * @property string $sks_code
 * @property string $full_price
 * @property string $order_status
 * @property string $ezf_id
 * @property string $checkup_flag_pay
 * @property string $order_group_name 
 */
class ConstOrder extends \yii\db\ActiveRecord {

    public $order_type_name;
    public $order_group_name;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'const_order';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['order_code', 'full_price', 'order_name', 'group_type', 'order_status'], 'required'],
            [['ezf_id'], 'integer'],
            [['full_price'], 'number'],
            [['checkup_flag_pay', 'order_group_name'], 'safe'],
            [['order_code', 'fin_item_code'], 'string', 'max' => 7],
            [['order_name'], 'string', 'max' => 150],
            [['group_code', 'sks_code'], 'string', 'max' => 5],
            [['group_type', 'order_status'], 'string', 'max' => 1],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'order_code' => 'รหัส',
            'order_name' => 'รายการ',
            'group_code' => 'รหัสกลุ่มรายการ',
            'group_type' => 'ประเภท',
            'fin_item_code' => 'รหัสการเงิน',
            'sks_code' => 'รหัสการเบิก',
            'full_price' => 'ราคา',
            'order_status' => 'สถานะ',
            'order_group_name' => 'กลุ่ม',
        ];
    }

}
