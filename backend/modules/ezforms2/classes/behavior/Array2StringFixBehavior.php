<?php
namespace backend\modules\ezforms2\classes\behavior;

use Yii;
use yii\base\InvalidCallException;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\behaviors\AttributeBehavior;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DateBehavior
 *
 * @author appxq
 */
class Array2StringFixBehavior extends AttributeBehavior {
    
    public $ezf_field;
    public $ezf_table;
    /**
     * @var callable|Expression The expression that will be used for generating the timestamp.
     * This can be either an anonymous function that returns the timestamp value,
     * or an [[Expression]] object representing a DB expression (e.g. `new Expression('NOW()')`).
     * If not set, it will use the value of `time()` to set the attributes.
     */
    public $value;


    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (empty($this->attributes)) {
            $this->attributes = [
                ActiveRecord::EVENT_BEFORE_INSERT => $this->ezf_field['ezf_field_name'],
                ActiveRecord::EVENT_BEFORE_UPDATE => $this->ezf_field['ezf_field_name'],
                ActiveRecord::EVENT_AFTER_FIND =>  $this->ezf_field['ezf_field_name'],
                //ActiveRecord::EVENT_INIT =>  $this->ezf_field['ezf_field_name']
            ];
        }
    }

    /**
     * @inheritdoc
     */
    protected function getValue($event)
    {
        if ($this->value instanceof Expression) {
            return $this->value;
        } else {
            if($this->value !== null){
                return call_user_func($this->value, $event);
            } else {
                //$event->name = event name
                //$event->sender = model
                $value = $event->sender[$this->ezf_field['ezf_field_name']];
                $type = $this->ezf_field['ezf_field_type'];
                $model = $event->sender;
                $options = \appxq\sdii\utils\SDUtility::string2Array($this->ezf_field['ezf_field_options']);
                
                if($event->name == ActiveRecord::EVENT_AFTER_FIND){
                    return \appxq\sdii\utils\SDUtility::string2Array($value);
                } elseif ($event->name == ActiveRecord::EVENT_BEFORE_INSERT || $event->name == ActiveRecord::EVENT_BEFORE_UPDATE) {
                    if(isset($value) && !empty($value)){
                        return \appxq\sdii\utils\SDUtility::array2String($value);
                    } else {
                        return '';
                    }
                }
            }
        }
    }

    /**
     * Updates a timestamp attribute to the current timestamp.
     *
     * ```php
     * $model->touch('lastVisit');
     * ```
     * @param string $attribute the name of the attribute to update.
     * @throws InvalidCallException if owner is a new record.
     */
    public function touch($attribute)
    {
        /* @var $owner BaseActiveRecord */
        $owner = $this->owner;
        if ($owner->getIsNewRecord()) {
            throw new InvalidCallException('Timestamp updating is not available for new record.');
        }
        $owner->updateAttributes(array_fill_keys((array) $attribute, $this->getValue(null)));
    }
}
