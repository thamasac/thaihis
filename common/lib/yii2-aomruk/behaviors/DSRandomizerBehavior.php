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
class DSRandomizerBehavior extends AttributeBehavior {

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
//        \appxq\sdii\utils\VarDumper::dump($event->name);
        if ($this->value instanceof Expression) {
            return $this->value;
        } else {
            if ($this->value !== null) {
                return call_user_func($this->value, $event);
            } else {
                //$event->name = event name
//                $event->sender = model

                $model = $event->sender;
                $profile = \Yii::$app->user->identity->profile;
                $ezf_field = $this->ezf_field;
                $value = $model[$ezf_field['ezf_field_name']];
                $field_options = SDUtility::string2Array($ezf_field['ezf_field_options']);
                $options = $field_options['options'];
//                \appxq\sdii\utils\VarDumper::dump($options);

                try {
                    if ($value != '') {
                        if ($event->name == ActiveRecord::EVENT_AFTER_UPDATE && $model['rstat'] != '3') {
                            $sitecode = isset($options['sitecode']) && $options['sitecode'] != '' ? $options['sitecode'] : [];
                            $random_code = isset($options['random_code']) && $options['random_code'] != '' ? $options['random_code'] : [];
                            if (isset($options['check_sitecode']) && $options['check_sitecode'] == 1) {
                                if (!empty($sitecode) && !empty($random_code)) {
                                    foreach ($sitecode as $key => $value) {
                                        if ($value == $profile['sitecode']) {
                                            $dataSitecode = \backend\modules\ezforms2\models\RandomCodeSite::find()->where(['sitecode' => $value, 'random_id' => $random_code[$key], 'ezf_id' => $ezf_field['ezf_id']])->orderBy(['CAST(`key` AS UNSIGNED)' => SORT_DESC])->one();
                                            $dataCode = \backend\modules\ezforms2\models\RandomCode::find()->where(['id' => $random_code[$key]])->one();
                                            $code = [];
                                            $start_row = 0;
                                            if ($dataCode) {
                                                $code = preg_split("/\r\n|\n|\r/", $dataCode['code_random']);
                                                $start_row = isset($dataCode['start_row']) && $dataCode['start_row'] != '' ? $dataCode['start_row']-1 : 0;
                                            }
//                                            if (!$dataSitecode && $code != '' && $code[0] != '') {
                                            if (!$dataSitecode && !empty($code) && isset($code[$start_row]) && $code[$start_row] != '') {
//                                                $code = explode(',', $code[0]);
                                                $code = explode(',', $code[$start_row]);
                                                (new \yii\db\Query())
                                                        ->createCommand()
                                                        ->insert('random_code_site', [
                                                            'id' => SDUtility::getMillisecTime(),
                                                            'random_id' => $random_code[$key],
                                                            'sitecode' => $value,
                                                            'ezf_id' => $ezf_field['ezf_id'],
                                                            'data_id' => $model['id'],
                                                            'code' => $code[$dataCode['code_index'] - 1],
                                                            'key' => $start_row//0
                                                        ])
                                                        ->execute();
                                            } else if ($dataSitecode && !empty($code) && isset($code[$dataSitecode['key'] + 1]) && $code[$dataSitecode['key'] + 1] != '') {
                                                $code = explode(',', $code[$dataSitecode['key'] + 1]);
                                                (new \yii\db\Query())
                                                        ->createCommand()
                                                        ->insert('random_code_site', [
                                                            'id' => SDUtility::getMillisecTime(),
                                                            'random_id' => $random_code[$key],
                                                            'sitecode' => $value,
                                                            'ezf_id' => $ezf_field['ezf_id'],
                                                            'data_id' => $model['id'],
                                                            'code' => $code[$dataCode['code_index'] - 1],
                                                            'key' => $dataSitecode['key'] + 1
                                                        ])
                                                        ->execute();
                                            }
                                        }
                                    }
                                }
                            } else if (isset($options['check_sitecode']) && $options['check_sitecode'] == 2) {
                                if (!empty($random_code) && !empty($sitecode)) {
                                    foreach ($sitecode as $key => $value) {
                                        if ($value == '') {
                                            $dataSitecode = \backend\modules\ezforms2\models\RandomCodeSite::find()->where(['sitecode' => $profile['sitecode'], 'random_id' => $random_code[$key], 'ezf_id' => $ezf_field['ezf_id']])->orderBy(['key' => SORT_DESC])->one();
                                            $dataCode = \backend\modules\ezforms2\models\RandomCode::find()->where(['id' => $random_code[$key]])->one();
                                            $code = [];
                                            $start_row = 0;
                                            if ($dataCode) {
                                                $code = preg_split("/\r\n|\n|\r/", $dataCode['code_random']);
                                                $start_row = isset($dataCode['start_row']) && $dataCode['start_row'] != '' ? $dataCode['start_row']-1 : 0;
                                            }
//                                    \appxq\sdii\utils\VarDumper::dump($code);
//                                            if (!$dataSitecode && $code != '' && $code[0] != '') {
                                            if (!$dataSitecode && !empty($code) && isset($code[$start_row]) && $code[$start_row] != '') {
//                                                $code = explode(',', $code[0]);
                                                $code = explode(',', $code[$start_row]);
                                                (new \yii\db\Query())
                                                        ->createCommand()
                                                        ->insert('random_code_site', [
                                                            'id' => SDUtility::getMillisecTime(),
                                                            'random_id' => $random_code[$key],
                                                            'sitecode' => $profile['sitecode'],
                                                            'ezf_id' => $ezf_field['ezf_id'],
                                                            'data_id' => $model['id'],
                                                            'code' => $code[$dataCode['code_index'] - 1],
                                                            'key' => $start_row//0
                                                        ])
                                                        ->execute();
                                            } else if ($dataSitecode && !empty($code) && isset($code[$dataSitecode['key'] + 1]) && $code[$dataSitecode['key'] + 1] != '') {
                                                $code = explode(',', $code[$dataSitecode['key'] + 1]);
                                                (new \yii\db\Query())
                                                        ->createCommand()
                                                        ->insert('random_code_site', [
                                                            'id' => SDUtility::getMillisecTime(),
                                                            'random_id' => $random_code[$key],
                                                            'sitecode' => $profile['sitecode'],
                                                            'ezf_id' => $ezf_field['ezf_id'],
                                                            'data_id' => $model['id'],
                                                            'code' => $code[$dataCode['code_index'] - 1],
                                                            'key' => $dataSitecode['key'] + 1
                                                        ])
                                                        ->execute();
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                } catch (\yii\db\Exception $ex) {
                    EzfFunc::addErrorLog($ex);
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
    public function touch($attribute) {
        /* @var $owner BaseActiveRecord */
        $owner = $this->owner;
        if ($owner->getIsNewRecord()) {
            throw new InvalidCallException('Timestamp updating is not available for new record.');
        }
        $owner->updateAttributes(array_fill_keys((array) $attribute, $this->getValue(null)));
    }

}
