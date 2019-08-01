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
class Array2StringActivatorBehavior extends AttributeBehavior {
    
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
                    if(isset($options['options']['multiple']) && $options['options']['multiple']==1){
                        $value = \appxq\sdii\utils\SDUtility::string2Array($value);
                    } 
                        
                    return $value;
                    
                } elseif ($event->name == ActiveRecord::EVENT_BEFORE_INSERT || $event->name == ActiveRecord::EVENT_BEFORE_UPDATE) {
                    if(isset($value) && $value!='' && $model->rstat!=3){
                        $staple = 0;
                        if(isset($options['options']['staple']) && $options['options']['staple']==1){
                            $autonum_id = isset($options['options']['data-auto'])?$options['options']['data-auto']:0;

                            $fields = \backend\modules\ezforms2\classes\EzfQuery::getFieldStapleAll($this->ezf_field['ezf_id'], $this->ezf_field['ezf_version']);
                            if($fields){
                                foreach ($fields as $key_field => $value_field) {
                                    $foptions = \appxq\sdii\utils\SDUtility::string2Array($value_field['ezf_field_options']);
                                    $autonum_attr = isset($options['options']['data-auto'])?$options['options']['data-auto']:'';
                                    if($autonum_id == $autonum_attr){
                                        $staple = $model[$value_field['ezf_field_name']];
                                        break;
                                    }
                                }
                            }
                        }
                        
                        if(isset($options['options']['multiple']) && $options['options']['multiple']==1){
                            if(isset($options['options']['activator']) && $options['options']['activator']==1){
                                $send_unit = is_array($value)?$value:\appxq\sdii\utils\SDUtility::string2Array($value);
                                $userProfile = Yii::$app->user->identity->profile;
                                $dept = (isset($userProfile->department) && !empty($userProfile->department)) ? $userProfile->department : '';
                                
                                $modelQueueAll = \backend\modules\ezforms2\models\QueueLog::find()->where('ezf_id=:ezf_id AND dataid=:dataid', [':ezf_id'=>$this->ezf_field['ezf_id'], ':dataid'=>$model->id])->all();
                                $unit_item = [];
                                if($modelQueueAll){
                                    foreach ($send_unit as $valueUnit) {
                                        $addnew = true;
                                        foreach ($modelQueueAll as $keyQ => $valueQ) {
                                            if($valueUnit==$valueQ['unit']){
                                                $unit_item[] = $valueQ['unit'];
                                                try {
                                                    $modelQueue = \backend\modules\ezforms2\models\QueueLog::find()->where('id=:id', [':id'=>$valueQ['id']])->one();
                                                    if($modelQueue){
                                                        $modelQueue->tab_name = isset($options['options']['tab_name'])?$options['options']['tab_name']:$this->ezf_field['ezf_field_label'];
                                                        $modelQueue->current_unit = $dept;
                                                        $modelQueue->enable = 1;
                                                        $modelQueue->staple_id = $staple;
                                                        $modelQueue->save();
                                                    }
                                                } catch (\yii\db\Exception $e) {
                                                    \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
                                                }
                                                $addnew = false;
                                                break;
                                            }
                                        }
                                        
                                        if($addnew){
                                            try {
                                                $modelQueue = new \backend\modules\ezforms2\models\QueueLog();
                                                $modelQueue->id = \appxq\sdii\utils\SDUtility::getMillisecTime();
                                                $modelQueue->tab_name = isset($options['options']['tab_name'])?$options['options']['tab_name']:$this->ezf_field['ezf_field_label'];
                                                $modelQueue->current_unit = $dept;
                                                $modelQueue->unit = $valueUnit;
                                                $modelQueue->ezf_id = $this->ezf_field['ezf_id'];
                                                $modelQueue->dataid = $model->id;
                                                $modelQueue->status = 'in_comming';
                                                $modelQueue->type = 'send';
                                                $modelQueue->enable = 1;
                                                $modelQueue->setting_id = $this->ezf_field['ezf_field_id'];
                                                $modelQueue->module_id = 0;
                                                $modelQueue->staple_id = $staple;
                                                $modelQueue->save();
                                            } catch (\yii\db\Exception $e) {
                                                \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
                                            }
                                        }

                                    }
                                    //disable
                                    foreach ($modelQueueAll as $keyQ => $valueQ) {
                                        if(!in_array($valueQ['unit'], $unit_item)){
                                            try {
                                                $modelQueue = \backend\modules\ezforms2\models\QueueLog::find()->where('id=:id', [':id'=>$valueQ['id']])->one();
                                                if($modelQueue){
                                                    $modelQueue->tab_name = isset($options['options']['tab_name'])?$options['options']['tab_name']:$this->ezf_field['ezf_field_label'];
                                                    $modelQueue->current_unit = $dept;
                                                    $modelQueue->enable = 0;
                                                    $modelQueue->staple_id = $staple;
                                                    $modelQueue->save();
                                                }
                                            } catch (\yii\db\Exception $e) {
                                                \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
                                            }
                                        }
                                    }
                                    
                                    
                                } else {
                                    foreach ($send_unit as $valueUnit) {
                                        try {
                                            $modelQueue = new \backend\modules\ezforms2\models\QueueLog();
                                            $modelQueue->id = \appxq\sdii\utils\SDUtility::getMillisecTime();
                                            $modelQueue->tab_name = isset($options['options']['tab_name'])?$options['options']['tab_name']:$this->ezf_field['ezf_field_label'];
                                            $modelQueue->current_unit = $dept;
                                            $modelQueue->unit = $valueUnit;
                                            $modelQueue->ezf_id = $this->ezf_field['ezf_id'];
                                            $modelQueue->dataid = $model->id;
                                            $modelQueue->status = 'in_comming';
                                            $modelQueue->type = 'send';
                                            $modelQueue->enable = 1;
                                            $modelQueue->setting_id = $this->ezf_field['ezf_field_id'];
                                            $modelQueue->module_id = 0;
                                            $modelQueue->staple_id = $staple;
                                            $modelQueue->save();
                                        } catch (\yii\db\Exception $e) {
                                            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
                                        }

                                    }
                                }
                            }
                            
                            return \appxq\sdii\utils\SDUtility::array2String($value);
                        } else {
                            if(isset($options['options']['activator']) && $options['options']['activator']==1){
                                $send_unit = $value;
                                $userProfile = Yii::$app->user->identity->profile;
                                $dept = (isset($userProfile->department) && !empty($userProfile->department)) ? $userProfile->department : '';
                                
                                try {
                                    $modelQueue = \backend\modules\ezforms2\models\QueueLog::find()->where('ezf_id=:ezf_id AND dataid=:dataid', [':ezf_id'=>$this->ezf_field['ezf_id'], ':dataid'=>$model->id])->one();
                                    if($modelQueue){
                                        $modelQueue->tab_name = isset($options['options']['tab_name'])?$options['options']['tab_name']:$this->ezf_field['ezf_field_label'];
                                        $modelQueue->current_unit = $dept;
                                        $modelQueue->enable = 1;
                                        $modelQueue->staple_id = $staple;
                                        $modelQueue->unit = $send_unit;
                                        
                                    } else {
                                        $modelQueue = new \backend\modules\ezforms2\models\QueueLog();
                                        $modelQueue->id = \appxq\sdii\utils\SDUtility::getMillisecTime();
                                        $modelQueue->tab_name = isset($options['options']['tab_name'])?$options['options']['tab_name']:$this->ezf_field['ezf_field_label'];
                                        $modelQueue->current_unit = $dept;
                                        $modelQueue->unit = $send_unit;
                                        $modelQueue->ezf_id = $this->ezf_field['ezf_id'];
                                        $modelQueue->dataid = $model->id;
                                        $modelQueue->status = 'in_comming';
                                        $modelQueue->type = 'send';
                                        $modelQueue->enable = 1;
                                        $modelQueue->setting_id = $this->ezf_field['ezf_field_id'];
                                        $modelQueue->module_id = 0;
                                        $modelQueue->staple_id = $staple;
                                    } 
                                    $modelQueue->save();
                                } catch (\yii\db\Exception $e) {
                                    \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
                                }
                                
                            }
                            
                            return $value;
                        }
                    } else {
                        Yii::$app->db->createCommand()->update('queue_log', ['enable'=>0], 'ezf_id=:ezf_id AND dataid=:dataid', [':ezf_id'=>$this->ezf_field['ezf_id'], ':dataid'=>$model->id])->execute();
                        
                        return $value;
                    }
                } elseif ($event->name == ActiveRecord::EVENT_AFTER_DELETE) {
                    Yii::$app->db->createCommand()->update('queue_log', ['enable'=>0], 'ezf_id=:ezf_id AND dataid=:dataid', [':ezf_id'=>$this->ezf_field['ezf_id'], ':dataid'=>$model->id])->execute();
                }
                //return new Expression('NULL');
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
