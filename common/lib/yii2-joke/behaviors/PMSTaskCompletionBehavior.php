<?php

namespace dms\joke\behaviors;

use Yii;
use yii\base\InvalidCallException;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\behaviors\AttributeBehavior;
use backend\modules\ezforms2\classes\EzfUiFunc;
use backend\modules\ezforms2\models\TbdataAll;
use backend\modules\ezforms2\classes\MyWorkbenchFunc;
use appxq\sdii\utils\SDUtility;
use backend\modules\ezforms2\classes\EzfFunc;

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
class PMSTaskCompletionBehavior extends AttributeBehavior {

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
    public function init() {
        parent::init();

        if (empty($this->attributes)) {
            $this->attributes = [
                ActiveRecord::EVENT_AFTER_INSERT => $this->ezf_field['ezf_field_name'],
                ActiveRecord::EVENT_AFTER_UPDATE => $this->ezf_field['ezf_field_name'],
                ActiveRecord::EVENT_AFTER_DELETE => $this->ezf_field['ezf_field_name'],
            ];
        }
    }

    protected function getValue($event) {

        if ($this->value instanceof Expression) {
            return $this->value;
        } else {
            if ($this->value !== null) {
                return call_user_func($this->value, $event);
            } else {
                //$event->name = event name
                //$event->sender = model
                $todate = date('Y-m-d h:i:s');
                $model = $event->sender;
                
                try {

                    if ($event->name == 'afterUpdate'|| $event->name == 'afterInsert'||$event->name == ActiveRecord::EVENT_AFTER_UPDATE || $event->name == ActiveRecord::EVENT_AFTER_INSERT) {
        
                        $value = $model[$this->ezf_field['ezf_field_name']];
                        $type = $this->ezf_field['ezf_field_type'];
                        $field_data = SDUtility::string2Array($this->ezf_field['ezf_field_data']);
                        $field_options = SDUtility::string2Array($this->ezf_field['ezf_field_options']);
                        $maxValue = $field_options['options']['max'];
                        
                        foreach ($field_data['task'] as $valid){
                            \backend\modules\gantt\classes\GanttQuery::updateTaskCompletion($valid,$value,$field_data['actual_date'],$maxValue);
                        }
                    }
                } catch (\Exception $ex) {
                    EzfFunc::addErrorLog($ex);
                }
            }
        }
    }
    
    public function touch($attribute) {
        /* @var $owner BaseActiveRecord */
        $owner = $this->owner;
        if ($owner->getIsNewRecord()) {
            throw new InvalidCallException('Timestamp updating is not available for new record.');
        }
        $owner->updateAttributes(array_fill_keys((array) $attribute, $this->getValue(null)));
    }

}
