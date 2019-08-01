<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace appxq\sdii\validators;

use Yii;
use yii\base\InvalidConfigException;
use yii\web\JsExpression;
use yii\helpers\Json;

/**
 * EmailValidator validates that the attribute value is a valid email address.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class CitizenIdValidator extends \yii\validators\Validator
{
    /**
     * @var boolean whether to allow name in the email address (e.g. "John Smith <john.smith@example.com>"). Defaults to false.
     * @see fullPattern
     */
    public $allowName = false;
    /**
     * @var boolean whether to check whether the email's domain exists and has either an A or MX record.
     * Be aware that this check can fail due to temporary DNS problems even if the email address is
     * valid and an email would be deliverable. Defaults to false.
     */

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        
        if ($this->message === null) {
            $this->message = Yii::t('app', 'เลขบัตรประชาชนไม่ถูกต้อง');
        }
    }

    /**
     * @inheritdoc
     */
    protected function validateValue($value)
    {
        // make sure string length is limited to avoid DOS attacks
	$valid = $this->valid_citizen_id($value);

        return $valid ? null : [$this->message, []];
    }

    protected function valid_citizen_id($personID) { 
//	if (strlen($pid) != 13) return false;
//        for ($i = 0, $sum = 0; $i < 12; $i++)
//            $sum += (int)($pid{$i}) * (13 - $i);
//        if ((11 - ($sum % 11)) % 10 == (int)($pid{12}))
//            return true;
//	
//        return false;
        $personID = str_replace('-','', $personID);
        
	if (strlen($personID) != 13) {
	    return false;
	}
        
        if(substr($personID, 0,4)=='9999'){
            return true;
        }

	$rev = strrev($personID); // reverse string ขั้นที่ 0 เตรียมตัว
	$total = 0;
	for($i=1;$i<13;$i++) // ขั้นตอนที่ 1 - เอาเลข 12 หลักมา เขียนแยกหลักกันก่อน
	{
		$mul = $i +1;
		$count = $rev[$i]*$mul; // ขั้นตอนที่ 2 - เอาเลข 12 หลักนั้นมา คูณเข้ากับเลขประจำหลักของมัน
		$total = $total + $count; // ขั้นตอนที่ 3 - เอาผลคูณทั้ง 12 ตัวมา บวกกันทั้งหมด
	}
	$mod = $total % 11; //ขั้นตอนที่ 4 - เอาเลขที่ได้จากขั้นตอนที่ 3 มา mod 11 (หารเอาเศษ)
	$sub = 11 - $mod; //ขั้นตอนที่ 5 - เอา 11 ตั้ง ลบออกด้วย เลขที่ได้จากขั้นตอนที่ 4
	$check_digit = $sub % 10; //ถ้าเกิด ลบแล้วได้ออกมาเป็นเลข 2 หลัก ให้เอาเลขในหลักหน่วยมาเป็น Check Digit
	if($rev[0] == $check_digit)  // ตรวจสอบ ค่าที่ได้ กับ เลขตัวสุดท้ายของ บัตรประจำตัวประชาชน
		return true; /// ถ้า ตรงกัน แสดงว่าถูก
	else
		return false; // ไม่ตรงกันแสดงว่าผิด 
    } 

    /**
     * @inheritdoc
     */
    public function clientValidateAttribute($model, $attribute, $view)
    {
        $options = [
            'message' => Yii::$app->getI18n()->format($this->message, [
                'attribute' => $model->getAttributeLabel($attribute),
            ], Yii::$app->language),
        ];
        if ($this->skipOnEmpty) {
            $options['skipOnEmpty'] = 1;
        }

        \appxq\sdii\assets\ValidationAsset::register($view);
       
	
	return "ssn(value, messages,". Json::htmlEncode($options) .");";
       
    }
}
