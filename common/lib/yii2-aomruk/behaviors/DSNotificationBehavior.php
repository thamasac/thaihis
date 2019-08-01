<?php

namespace dms\aomruk\behaviors;

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
class DSNotificationBehavior extends AttributeBehavior {

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
//                ActiveRecord::EVENT_AFTER_INSERT => $this->ezf_field['ezf_field_name'],
                ActiveRecord::EVENT_AFTER_UPDATE => $this->ezf_field['ezf_field_name'],
                ActiveRecord::EVENT_AFTER_DELETE => $this->ezf_field['ezf_field_name'],
            ];
        }
    }

    /**
     * @inheritdoc
     */
    protected function getValue($event) {
        
        if ($this->value instanceof Expression) {
            return $this->value;
        } else {
            if ($this->value !== null) {
                return call_user_func($this->value, $event);
            } else {
                //$event->name = event name
                //$event->sender = model
                try {
                    if ($event->name == ActiveRecord::EVENT_AFTER_UPDATE || $event->name == ActiveRecord::EVENT_AFTER_DELETE) {
                        $value = $event->sender[$this->ezf_field['ezf_field_name']];
                        $type = $this->ezf_field['ezf_field_type'];
//                        $t = Yii::$app->queue->push(new \dms\aomruk\classese\DowloadJob([
//                            'model' => $event->sender,
//                            'ezf_field' => $this->ezf_field,
//                            'version' => \Yii::$app->request->get('v', 'v1')
//                        ]));
//                        \appxq\sdii\utils\VarDumper::dump($event->name);
                        \dms\aomruk\classese\Notify::setNotify()->sendByEzfModel($event->sender, $this->ezf_field, \Yii::$app->request->get('v', 'v1'));
//                       
                    }
                } catch (\Exception $ex) {
                    EzfFunc::addErrorLog($ex);
                }
            }
        }
    }

    public function SaveNotify($model) {
//         EzfUiFunc::backgroundInsert(1520530564093708000, '', '');
//                    $model = $model->find()->where('user_create = :user AND rstat = 0', [':user' => Yii::$app->user->id])->one();
//                    $userForm = $model->assign;
//                       
//        if ($model) {
//            $model->ezf_id = $this->ezf_field['ezf_id'];
//            $model->data_id = $event->sender['id'];
//            $model->notify = $options['options']['notify_text'];
//            $model->detail = $options['options']['topic'];
//            $model->mandatory = $options['options']['mandatory'] ? 1 : 2;
//            $model->effective_date = $options['options']['effective_date'] ? $event->sender[$options['options']['effective_date']] : $event->sender['create_date'];
//            $model->due_date = isset($event->sender[$options['options']['due_date']]) ? $event->sender[$options['options']['due_date']] : NULL;
//            $model->action = $event->sender[$options['options']['field_action']];
//            $model->file_upload = isset($event->sender[$options['options']['upload']]) ? $event->sender[$options['options']['upload']] : NULL;
//            $model->sender = \Yii::$app->user->id;
//            $model->complete_date = NULL;
//            $model->readonly = $options['options']['readonly'];
//            $model->rstat = $event->sender['rstat'];
//            $model->save();
//        }
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
    public function touch($attribute) {
        /* @var $owner BaseActiveRecord */
        $owner = $this->owner;
        if ($owner->getIsNewRecord()) {
            throw new InvalidCallException('Timestamp updating is not available for new record.');
        }
        $owner->updateAttributes(array_fill_keys((array) $attribute, $this->getValue(null)));
    }

}
