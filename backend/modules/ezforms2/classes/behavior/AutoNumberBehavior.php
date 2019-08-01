<?php
namespace backend\modules\ezforms2\classes\behavior;

use Yii;
use yii\base\InvalidCallException;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\behaviors\AttributeBehavior;
use backend\modules\ezforms2\classes\EzfQuery;
use appxq\sdii\utils\SDUtility;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AutoNumberBehavior
 *
 * @author appxq
 */
class AutoNumberBehavior extends AttributeBehavior {
    
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
                //ActiveRecord::EVENT_AFTER_FIND =>  $this->ezf_field['ezf_field_name'],
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
                $options = \appxq\sdii\utils\SDUtility::string2Array($this->ezf_field['ezf_field_options']);
                $model = $event->sender;
                
                if ($event->name == ActiveRecord::EVENT_BEFORE_INSERT || $event->name == ActiveRecord::EVENT_BEFORE_UPDATE) {
                    
                    if(empty($value)){
                        
                        if(isset($options['data-id']) && !empty($options['data-id'])){
                           
                            $modelAuto = \backend\modules\ezforms2\models\EzformAutonum::find()->where('id=:id', [':id'=>$options['data-id']])->one();
                             
                            if($modelAuto){
                                if($modelAuto->per_day==1){
                                    $modelEzf = EzfQuery::getEzformOne($this->ezf_field['ezf_id']);
                                    $options = SDUtility::string2Array($modelEzf->ezf_options);
                                    $create_date_field = isset($options['create_date_field']) && !empty($options['create_date_field'])?$options['create_date_field']:'create_date';
                                    $unit_field = isset($options['unit_field']) && !empty($options['unit_field'])?$options['unit_field']:'';
                                    $enable_field = isset($options['enable_field']) && !empty($options['enable_field'])?$options['enable_field']:'';
                                    
                                    $target = $model->target;
                                    
                                    $modelLastRecord = \backend\modules\ezforms2\classes\EzfUiFunc::loadLastDateRecordNotModel($modelEzf->ezf_table, $target, $create_date_field);
                                    if($modelLastRecord){
                                        $value = $modelLastRecord[$this->ezf_field['ezf_field_name']];
                                        return $value;
                                    }
                                }
                                
                                $value = \backend\modules\ezforms2\classes\EzfFunc::getAutoNumber($modelAuto->attributes);
                                
                                $modelAuto->count = $modelAuto->count+$modelAuto->per_time;
                                $modelAuto->save();
                                
                                $modelStamper = new \backend\modules\ezforms2\models\EzformStamper();
                                $modelStamper->stamper_id = \appxq\sdii\utils\SDUtility::getMillisecTime();
                                $modelStamper->auto_id = $modelAuto->id;
                                $modelStamper->auto_num = $value;
                                $modelStamper->ezf_id = $this->ezf_field['ezf_id'];
                                $modelStamper->ezf_field_id = $this->ezf_field['ezf_field_id'];
                                $modelStamper->dataid = $model->id;
                                $modelStamper->xsourcex = $model->xsourcex;
                                $modelStamper->save();
                                
                                
                                return $value;
                            }
                        }
                    }
                    
                    return $value;
                }
                
                return $value;
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
