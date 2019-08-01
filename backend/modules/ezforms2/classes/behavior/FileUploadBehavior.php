<?php

namespace backend\modules\ezforms2\classes\behavior;

use Yii;
use yii\base\InvalidCallException;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\behaviors\AttributeBehavior;
use backend\modules\ezforms2\models\EzformUpload;

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
class FileUploadBehavior extends AttributeBehavior {

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
                ActiveRecord::EVENT_BEFORE_INSERT => $this->ezf_field['ezf_field_name'],
                ActiveRecord::EVENT_BEFORE_UPDATE => $this->ezf_field['ezf_field_name'],
                ActiveRecord::EVENT_AFTER_DELETE => $this->ezf_field['ezf_field_name'],
                    //BaseActiveRecord::EVENT_INIT =>  $this->ezf_field['ezf_field_name']
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
                $value = $event->sender[$this->ezf_field['ezf_field_name']];
                $type = $this->ezf_field['ezf_field_type'];
                $model = $event->sender;
                if (isset($value)) {
                    if ($event->name == ActiveRecord::EVENT_AFTER_DELETE) {
                        $data = EzformUpload::find()->where('tbid=:id', [':id' => $model->id])->all();
                        foreach ($data as $key => $value) {
                            $modelDelete = EzformUpload::find()->where('fid=:fid', [':fid' => $value['fid']])->one();
                            $fileItem = $value['file_name'];
                            if ($modelDelete->delete()) {
                                @unlink(Yii::getAlias('@storage/web/ezform/fileinput/') . $fileItem);
                            }
                        }
                    } else {
                        $newFileItems = [];
                        $action = false;
                        $fileItems = \yii\web\UploadedFile::getInstances($model, $this->ezf_field['ezf_field_name']);
                        
                        foreach ($fileItems as $i => $fileItem) {
                            if (isset($fileItem->name) && $fileItem->name != '') {
                                $fileArr = explode('/', $fileItem->type);
                                if (isset($fileArr[1])) {
                                    $action = true;
                                    $fileBg = $fileArr[1];

                                    if (!in_array($fileBg, ['pdf', 'png', 'jpg', 'jpeg'])) {
                                        $tmpFile = explode('.', $fileItem->name);
                                        $num = count($tmpFile);
                                        if ($num > 0) {
                                            $fileBg = $tmpFile[$num - 1];
                                        }
                                    }

                                    $newFileName = $this->ezf_field['ezf_field_name'] . '_' . \appxq\sdii\utils\SDUtility::getMillisecTime() . '.' . $fileBg;
                                    //chmod(Yii::$app->basePath . '/../backend/web/fileinput/', 0777);
                                    $fullPath = Yii::getAlias('@storage/web/ezform/fileinput/') . $newFileName;

                                    $fileupload = $fileItem->saveAs($fullPath);
                                    if ($fileupload) {
                                        $newFileItems[] = $newFileName;
                                        //add file to db
                                        $file_db = new EzformUpload();
                                        $file_db->tbid = $model->id;
                                        $file_db->ezf_id = $this->ezf_field['ezf_id'];
                                        $file_db->ezf_field_id = $this->ezf_field['ezf_field_id'];
                                        $file_db->file_active = 0;
                                        $file_db->file_name = $newFileName;
                                        $file_db->file_name_old = $fileItem->name;
                                        $file_db->target = ($model->ptid ? $model->ptid : $model->target) . '';
                                        $file_db->save();
                                    }
                                }
                            }
                        }

                        if ($action) {
                            return implode(',', $newFileItems);
                        } else {
                            $res = Yii::$app->db->createCommand("SELECT `" . $this->ezf_field['ezf_field_name'] . "` as filename FROM `" . $this->ezf_table . "` WHERE id = :dataid", [':dataid' => $model->id])->queryOne();
                            if ($res) {
                                return $res['filename'];
                            }
                        }
                    }
                }


                //return $value;
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
    public function touch($attribute) {
        /* @var $owner BaseActiveRecord */
        $owner = $this->owner;
        if ($owner->getIsNewRecord()) {
            throw new InvalidCallException('Timestamp updating is not available for new record.');
        }
        $owner->updateAttributes(array_fill_keys((array) $attribute, $this->getValue(null)));
    }

}
