<?php
namespace backend\modules\ezforms2\classes\behavior;

use Yii;
use yii\base\InvalidCallException;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\behaviors\AttributeBehavior;
use backend\modules\ezforms2\classes\EzfQuery;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of StapleBehavior
 *
 * @author appxq
 */
class StapleBehavior extends AttributeBehavior {
    
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
//                ActiveRecord::EVENT_BEFORE_INSERT => $this->ezf_field['ezf_field_name'],
//                ActiveRecord::EVENT_BEFORE_UPDATE => $this->ezf_field['ezf_field_name'],
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
                    $staple_id = isset($options['options']['data-id'])?$options['options']['data-id']:0;
                    $target_id = 0;
                    $dataid = 0;
                    $dataEzf = EzfQuery::getTargetOne($this->ezf_field['ezf_id']);
                    if($dataEzf){
                        $target_id = isset($dataEzf['ezf_field_id'])?$dataEzf['ezf_field_id']:0;
                        $ref_ezf_id = isset($dataEzf['ref_ezf_id'])?$dataEzf['ref_ezf_id']:0;
                        
                        $ref_form = \appxq\sdii\utils\SDUtility::string2Array($dataEzf['ref_form']);
                        $ref_form[$ref_ezf_id] = isset($dataEzf['ezf_field_name'])?$dataEzf['ezf_field_name']:'';
                        
                        $model_auto = \backend\modules\ezforms2\models\EzformAutonum::findOne($staple_id);
                        if($model_auto){
                            
                            if(isset($ref_form[$model_auto['ezf_id']]) && !empty($ref_form[$model_auto['ezf_id']])){
                                $target_name = $ref_form[$model_auto['ezf_id']];
                                $dataid = $model[$target_name];

                                $model_log = \backend\modules\ezforms2\models\EzformStamper::find()->where('ezf_id=:ezf_id AND dataid = :dataid AND auto_id=:auto_id', [
                                    ':ezf_id'=>$model_auto['ezf_id'],
                                    ':dataid'=>$dataid,
                                    ':auto_id'=>$staple_id,
                                ])->one();
                                
                                if($model_log){
                                    $value = $model_log['auto_num'];
                                }
                            }
                            
                        }
                        
                    }
                    
                    return $value;
                    
                } 
//                elseif ($event->name == ActiveRecord::EVENT_BEFORE_INSERT || $event->name == ActiveRecord::EVENT_BEFORE_UPDATE) {
//                    if(isset($value) && $value!=''){
//                        if(isset($options['options']['multiple']) && $options['options']['multiple']==1){
//                            return \appxq\sdii\utils\SDUtility::array2String($value);
//                        } else {
//                            return $value;
//                        }
//                    } else {
//                        return $value;
//                    }
//                }
                //return new Expression('NULL');
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
