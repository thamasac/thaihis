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
 * Description of RefFieldBehavior
 *
 * @author appxq
 */
class RefFieldBehavior extends AttributeBehavior {
    
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
                ActiveRecord::EVENT_AFTER_INSERT => $this->ezf_field['ezf_field_name'],
                ActiveRecord::EVENT_AFTER_UPDATE => $this->ezf_field['ezf_field_name'],
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
                $options = \appxq\sdii\utils\SDUtility::string2Array($this->ezf_field['ezf_field_options']);
                $model = $event->sender;
                
                if($event->name == ActiveRecord::EVENT_AFTER_FIND){// ตอนดึงข้อมูล
                    
                    if($options['config']==3 && (isset($value) && $value!='')){
                        return $value;
                    } 
                    
                    $modelTarget = EzfQuery::getTargetOne($this->ezf_field['ezf_id']);
                    $refForm = \appxq\sdii\utils\SDUtility::string2Array($modelTarget['ref_form']);
                    
                    if(!empty($refForm) && $modelTarget->ref_ezf_id!=$this->ezf_field['ref_ezf_id']){
                        if(isset($refForm[$this->ezf_field['ref_ezf_id']])){
                            $field = $refForm[$this->ezf_field['ref_ezf_id']];

                            if(isset($model[$field]) && $model[$field]>0){
                                $modelEzf = EzfQuery::getFormTableName($this->ezf_field['ref_ezf_id']);
                                
                                if($modelEzf){
                                    $query = new \yii\db\Query();
                                    $query->select(["`{$this->ezf_field['ref_field_id']}` AS `name`"]);
                                    $query->from("`{$modelEzf['ezf_table']}`");
                                    $query->where("id=:target  AND rstat not in(0, 3)", [':target' => $model[$field]]);
                                    return $query->createCommand()->queryScalar();
                                }
                            }
                        } else {
                            if(isset($model['target']) && $model['target']>0){
                                $modelEzf = EzfQuery::getFormTableName($this->ezf_field['ref_ezf_id']);
                                if($modelEzf){
                                    $query = new \yii\db\Query();
                                    $query->select(["`{$this->ezf_field['ref_field_id']}` AS `name`"]);
                                    $query->from("`{$modelEzf['ezf_table']}`");
                                    $query->where("target=:target  AND rstat not in(0, 3)", [':target' => $model['target']])->orderBy('create_date DESC')->limit(1);
                                    
                                    return $query->createCommand()->queryScalar();
                                }
                            }
                        }
                    } else {
                        
                        if(isset($model['target']) && $model['target']>0){
                            $modelEzf = EzfQuery::getRefFieldByName($this->ezf_field['ref_ezf_id'], $this->ezf_field['ref_field_id']);
                            if($modelEzf){
                                $query = new \yii\db\Query();
                                $query->select(["`{$this->ezf_field['ref_field_id']}` AS `name`"]);
                                $query->from("`{$modelEzf['ezf_table']}`");
                                $query->where("id=:target  AND rstat not in(0, 3)", [':target' => $model['target']]);

                                return $query->createCommand()->queryScalar();
                            }
                        }
                    }
                    
                    return $value;
                } elseif ($event->name == ActiveRecord::EVENT_AFTER_INSERT || $event->name == ActiveRecord::EVENT_AFTER_UPDATE) {
                    
                    if($options['config']==2){
                        $modelTarget = EzfQuery::getTargetOne($this->ezf_field['ezf_id']);
                        $refForm = \appxq\sdii\utils\SDUtility::string2Array($modelTarget['ref_form']);
                        
                        if(!empty($refForm) && $modelTarget->ref_ezf_id!=$this->ezf_field['ref_ezf_id']){
                            if(isset($refForm[$this->ezf_field['ref_ezf_id']])){
                                $field = $refForm[$this->ezf_field['ref_ezf_id']];
                                if(isset($model[$field]) && $model[$field]>0){
                                    
                                    $modelEzf = EzfQuery::getFormTableName($this->ezf_field['ref_ezf_id']);
                                    if($modelEzf){
                                        $columns = [
                                            $this->ezf_field['ref_field_id'] => $value,
                                        ];
                                        Yii::$app->db->createCommand()->update($modelEzf['ezf_table'], $columns, 'id=:target', [':target' => $model[$field]])->execute();
                                    }
                                    
                                    Yii::$app->queue->push(new \backend\modules\ezforms2\classes\EzformJob([
                                        'target' => $model[$field],
                                        'ezf_field_ref' => $this->ezf_field['ezf_field_ref'],
                                        'value' => $value,
                                    ]));
                                }
                            }
                        } else {
                            if(isset($model['target']) && $model['target']>0){
                                $modelEzf = EzfQuery::getRefFieldByName($this->ezf_field['ref_ezf_id'], $this->ezf_field['ref_field_id']);
                                if($modelEzf){
                                    $columns = [
                                        $this->ezf_field['ref_field_id'] => $value,
                                    ];
                                    Yii::$app->db->createCommand()->update($modelEzf['ezf_table'], $columns, 'id=:target', [':target' => $model['target']])->execute();
                                }
                                //$error = \backend\modules\ezforms2\classes\EzfFunc::updateDataRefField($model['target'], $this->ezf_field['ezf_field_ref'], $value);
                                Yii::$app->queue->push(new \backend\modules\ezforms2\classes\EzformJob([
                                    'target' => $model['target'],
                                    'ezf_field_ref' => $this->ezf_field['ezf_field_ref'],
                                    'value' => $value,
                                ]));
                            }
                        }
                    }
                    return $value;
                }
                
                return $value;
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
