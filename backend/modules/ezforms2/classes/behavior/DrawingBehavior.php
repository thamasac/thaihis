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
 * Description of DrawingBehavior
 *
 * @author appxq
 */
class DrawingBehavior extends AttributeBehavior {
    
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
                ActiveRecord::EVENT_AFTER_DELETE =>  $this->ezf_field['ezf_field_name'],
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

                if($event->name == ActiveRecord::EVENT_AFTER_DELETE){
                    if($value!='' && stristr($value, '.png') == TRUE){
                        $fileArr = explode(',', $value);
                        $fileName = $fileArr[0];
                        $fileBg = $fileArr[1];
                        $newFileName = $fileName;
                        $newFileBg = $fileBg;
                        
                        if (stristr($fileName, '.png') == TRUE) {
                            @unlink(Yii::getAlias('@storage/web/ezform/drawing/data/') . $fileName);
                        }
                        
                        if (stristr($fileBg, '.png') == TRUE) {
                            @unlink(Yii::getAlias('@storage/web/ezform/drawing/bg/') . $fileBg);
                        }
                    }
                } else {
                    if($value!='' && stristr($value, 'tmp.png') == TRUE){
                        //set data Drawing
                        $fileArr = explode(',', $value);
                        $fileName = $fileArr[0];
                        $fileBg = $fileArr[1];
                        $newFileName = $fileName;
                        $newFileBg = $fileBg;
                        $nameEdit = false;
                        $bgEdit = false;
                        if (stristr($fileName, 'tmp.png') == TRUE) {
                            $nameEdit = true;
                            $newFileName = 'drawing_' .\appxq\sdii\utils\SDUtility::getMillisecTime() . '.png';
                            @copy(Yii::getAlias('@storage/web/ezform/drawing/') . $fileName, Yii::getAlias('@storage/web/ezform/drawing/data/') . $newFileName);
                            @unlink(Yii::getAlias('@storage/web/ezform/drawing/') . $fileName);
                            @unlink(Yii::getAlias('@app/web/drawing/') . $fileName);
                        }
                        if (stristr($fileBg, 'tmp.png') == TRUE) {
                            $bgEdit = true;
                            $newFileBg = 'bg_' . \appxq\sdii\utils\SDUtility::getMillisecTime() . '.png';
                            @copy(Yii::getAlias('@storage/web/ezform/drawing/') . $fileBg, Yii::getAlias('@storage/web/ezform/drawing/bg/') . $newFileBg);
                            @unlink(Yii::getAlias('@storage/web/ezform/drawing/') . $fileBg);
                        }

                        try {
                            $modelTmp = \backend\modules\ezforms2\classes\EzfQuery::getDynamicFormById($this->ezf_table, $model->id);
                            if (isset($modelTmp['id'])) {
                                $fileArr = explode(',', $modelTmp[$this->ezf_field['ezf_field_name']]);
                                if (count($fileArr) > 1) {
                                    $fileName = $fileArr[0];
                                    $fileBg = $fileArr[1];
                                    if ($nameEdit) {
                                        @unlink(Yii::getAlias('@storage/web/ezform/drawing/data/') . $fileName);
                                    }
                                    if ($bgEdit) {
                                        @unlink(Yii::getAlias('@storage/web/ezform/drawing/bg/') . $fileBg);
                                    }
                                }
                            }
                        } catch (\yii\db\Exception $e) {
                            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
                        }

                        return $newFileName . ',' . $newFileBg;
                    }
                
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
