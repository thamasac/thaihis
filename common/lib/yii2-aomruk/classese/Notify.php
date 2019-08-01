<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace dms\aomruk\classese;

/**
 * Description of Notify
 *
 * @author AR Soft
 */
use appxq\sdii\utils\VarDumper;
use common\modules\user\models\User;
use Yii;
use backend\modules\ezforms2\classes\EzfFunc;
use backend\modules\ezforms2\models\TbdataAll;
use appxq\sdii\utils\SDUtility;
use backend\modules\ezforms2\classes\EzfUiFunc;
use yii\db\Expression;
use yii\base\Component;
use backend\modules\tmf\classes\TmfFn;
use yii\db\Query;

class Notify extends Component {

    public $ezf_id = '';
    public $data_id = '';
    public $module_id = '';
    public $detail = '';
    public $notify = 'New Notify';
    public $action = '';
    public $mandatory = 0;
    public $readonly = true;
    public $assign = [];
    public $delay_date = '';
    public $effective_date = '';
    public $complete_date = null;
    public $due_date = '';
    public $version = 'v1';
    public $type_link = '';
    public $url = '/ezmodules/ezmodule/view?id=1520785643053421500&notify_id={notify_id}';
    public $sender = '';
    public $status_view = '0';
    public $file_upload = '';
    public $rstat = '1';
    public $send_system = true;
    public $send_email = false;
    public $send_line = false;
    public $due_date_assign = '';
    public $data_target = '';

    /**
     * @inheritdoc
     * @return Notify the newly created [[Notify]] instance.
     */
    public static function setNotify() {
        return Yii::createObject(Notify::className()); //, [get_called_class()]
    }

    /**
     *
     * @param string $ezf_id
     * @return $this
     */
    public function ezf_id($ezf_id) {
        $this->ezf_id = $ezf_id;
        return $this;
    }

    /**
     *
     * @param string $data_id
     * @return $this
     */
    public function data_id($data_id) {
        $this->data_id = $data_id;
        return $this;
    }

    public function data_target($data_target) {
        $this->data_target = $data_target;
        return $this;
    }

    /**
     *
     * @param string $module_id
     * @return $this
     */
    public function module_id($module_id) {
        $this->module_id = $module_id;
        return $this;
    }

    /**
     *
     * @param string $detail
     * @return $this
     */
    public function detail($detail) {
        $this->detail = $detail;
        return $this;
    }

    /**
     *
     * @param string $notify
     * @return $this
     */
    public function notify($notify) {
        $this->notify = $notify;
        return $this;
    }

    /**
     *
     * @param string $action
     * @return $this
     */
    public function action($action) {
        $this->action = $action;
        return $this;
    }

    /**
     *
     * @param boolean $mandatory
     * @return $this
     */
    public function mandatory($mandatory) {
        $this->mandatory = $mandatory;
        return $this;
    }

    /**
     *
     * @param boolean $readonly if true open form readonly mode if false open form edit mode
     * @return $this
     */
    public function readonly($readonly) {
        $this->readonly = $readonly;
        return $this;
    }

    /**
     *
     * @param string|array $assign to User Id '123456' or ['123456','567890']
     * @return $this
     */
    public function assign($assign) {
        $this->assign = $assign;
        return $this;
    }

    /**
     *
     * @param date $delay_date if $delay_date is empty will send notify now
     * if $delay_date is not empty will send notify by $delay_date
     * @return $this
     */
    public function delay_date($delay_date) {
        $this->delay_date = $delay_date;
        return $this;
    }

    public function effective_date($effective_date) {
        $this->effective_date = $effective_date;
        return $this;
    }

    public function complete_date($complete_date) {
        $this->complete_date = $complete_date;
        return $this;
    }

    public function due_date($due_date) {
        $this->due_date = $due_date;
        $this->due_date_assign = $due_date;
        return $this;
    }

    public function send_email($send_email) {
        $this->send_email = $send_email;
        return $this;
    }

    public function send_line($send_line) {
        $this->send_line = $send_line;
        return $this;
    }

    public function send_system($send_system) {
        $this->send_system = $send_system;
        return $this;
    }

    /**
     *
     * @param string $version in ezform version
     * @return $this
     */
    public function version($version) {
        $this->version = $version;
        return $this;
    }

    /**
     *
     * @param string|int $type_link if $type_link = 2 will open ezform
     * if $type_link = 1 will redirect page
     * @return $this
     */
    public function type_link($type_link) {
        $this->type_link = $type_link;
        return $this;
    }

    /**
     *
     * @param string $url
     * @return $this
     */
    public function url($url) {
        if ($url != '' && $url != NULL) {
            $this->url = $url;
        }
        return $this;
    }

    /**
     *
     * @param string $sender is user id
     * @return $this
     */
    public function sender($sender) {
        $this->sender = $sender;
        return $this;
    }

    /**
     *
     * @param string|int $status_view value $status_view 0 = new,1 = reading
     * @return $this
     */
    public function status_view($status_view) {
        $this->status_view = $status_view;
        return $this;
    }

    /**
     *
     * @param string $file_upload is filename when you upload file
     * @return $this
     */
    public function file_upload($file_upload) {
        $this->file_upload = $file_upload;
        return $this;
    }

    /**
     *
     * @param string|int $rstat
     * @return $this
     */
    public function rstat($rstat) {
        $this->rstat = $rstat;
        return $this;
    }

    public function sendStatic() {
        try {
            if (is_array($this->assign)) {
                $dataUsers = $this->assign;
                $url = $this->url;
                foreach ($dataUsers as $vUser) {
                    $model = $this->getModel();
                    $this->url = strtr($url, ["{notify_id}" => $model->id]);
                    $this->assign = $vUser;
                    if ($this->send_system) {
                        $this->setValue($model);
                    }
                    if ($this->send_email) {
                        $this->saveDataMail($model);
                    }
                    if ($this->send_line) {
                        $this->saveDataLine($model);
                    }
                }
            } else {
                $model = $this->getModel();
                if ($this->send_system) {
                    $this->setValue($model);
                }
                if ($this->send_email) {
                    $this->saveDataMail($model);
                }
                if ($this->send_line) {
                    $this->saveDataLine($model);
                }
            }
            return TRUE;
        } catch (\Exception $ex) {
            EzfFunc::addErrorLog($ex);
            return FALSE;
        }
    }

    public function sendUpdate() {
        try {
            if (is_array($this->assign)) {
                $dataUsers = $this->assign;
                $url = $this->url;
                foreach ($dataUsers as $vUser) {
                    $initdata = [
                        'notify' => $this->notify,
                        'detail' => $this->detail,
                        'delay_date' => $this->delay_date,
                        'url' => $this->url,
                    ];
                    $queryCheck = \Yii::$app->db->createCommand(" SELECT * FROM zdata_notify WHERE data_id=:data_id AND assign_to=:assign "
                                    , [':data_id' => $this->data_id, ':assign' => $vUser])->queryOne();

                    if (isset($queryCheck['id'])) {
                        $model = $this->setModel($queryCheck['id'], $initdata);
                        $this->url = strtr($url, ["{notify_id}" => $model->id]);
                        $this->assign = $vUser;

                        if ($this->send_email) {
                            $this->updateDataMail($queryCheck['id']);
                        }
                        if ($this->send_line) {
                            $this->updateDataLine($queryCheck['id']);
                        }
                    } else {
                        $this->sendStatic();
                    }
                }
            }
            return TRUE;
        } catch (\Exception $ex) {
            EzfFunc::addErrorLog($ex);
            return FALSE;
        }
    }

    public function sendViewForm($ezf_id, $data_id) {

        try {
            $this->ezf_id($ezf_id);
            $this->data_id($data_id);
            $this->type_link('2');
            if (is_array($this->assign)) {
                $dataUsers = $this->assign;
                foreach ($dataUsers as $vUser) {
                    $model = $this->getModel();
                    $this->assign = $vUser;
                    if ($this->send_system) {
                        $this->setValue($model);
                    }
                    if ($this->send_email) {
                        $this->saveDataMail($model);
                    }
                    if ($this->send_line) {
                        $this->saveDataLine($model);
                    }
                }
            } else {
                $model = $this->getModel();
                if ($this->send_system) {
                    $this->setValue($model);
                }
                if ($this->send_email) {
                    $this->saveDataMail($model);
                }
                if ($this->send_line) {
                    $this->saveDataLine($model);
                }
            }
            return TRUE;
        } catch (\yii\base\Exception $ex) {
            EzfFunc::addErrorLog($ex);
            return FALSE;
        }
    }

    public function sendEditForm($ezf_id, $data_id) {

        try {
            $this->ezf_id($ezf_id);
            $this->data_id($data_id);
            $this->readonly(false);
            $this->type_link('2');
            if (is_array($this->assign)) {
                $dataUsers = $this->assign;
                foreach ($dataUsers as $vUser) {
                    $model = $this->getModel();
                    $this->assign = $vUser;
                    if ($this->send_system) {
                        $this->setValue($model);
                    }
                    if ($this->send_email) {
                        $this->saveDataMail($model);
                    }
                    if ($this->send_line) {
                        $this->saveDataLine($model);
                    }
                }
            } else {
                $model = $this->getModel();
                if ($this->send_system) {
                    $this->setValue($model);
                }
                if ($this->send_email) {
                    $this->saveDataMail($model);
                }
                if ($this->send_line) {
                    $this->saveDataLine($model);
                }
            }
            return TRUE;
        } catch (yii\base\Exception $ex) {
            EzfFunc::addErrorLog($ex);
            return FALSE;
        }
    }

    public function sendSaveForm($ezf_id) {

        try {
            $this->ezf_id($ezf_id);
            $this->readonly(false);
            $this->type_link('2');
            if (is_array($this->assign)) {
                $dataUsers = $this->assign;
                foreach ($dataUsers as $vUser) {
                    $model = $this->getModel();
                    $this->assign = $vUser;
                    if ($this->send_system) {
                        $this->setValue($model);
                    }
                    if ($this->send_email) {
                        $this->saveDataMail($model);
                    }
                    if ($this->send_line) {
                        $this->saveDataLine($model);
                    }
                }
            } else {
                $model = $this->getModel();
                if ($this->send_system) {
                    $this->setValue($model);
                }
                if ($this->send_email) {
                    $this->saveDataMail($model);
                }
                if ($this->send_line) {
                    $this->saveDataLine($model);
                }
            }
            return TRUE;
        } catch (yii\base\Exception $ex) {
            EzfFunc::addErrorLog($ex);
            return FALSE;
        }
    }

    /**
     *
     * @param string $url
     */
    public function sendRedirect($url) {

        try {

            if ($url != '') {
                $this->url($url);
            }
            $this->type_link('1');
            if (is_array($this->assign)) {
                $dataUsers = $this->assign;
                foreach ($dataUsers as $vUser) {
                    $model = $this->getModel();
                    $this->assign = $vUser;
                    if ($this->send_system) {
                        $this->setValue($model);
                    }
                    if ($this->send_email) {
                        $this->saveDataMail($model);
                    }
                    if ($this->send_line) {
                        $this->saveDataLine($model);
                    }
                }
            } else {
                $model = $this->getModel();
                if ($this->send_system) {
                    $this->setValue($model);
                }
                if ($this->send_email) {
                    $this->saveDataMail($model);
                }
                if ($this->send_line) {
                    $this->saveDataLine($model);
                }
            }
        } catch (yii\base\Exception $ex) {
            EzfFunc::addErrorLog($ex);
        }
    }

    private function setValue($model) {

        if ($model) {
            try {
                $model->update_date = new Expression('NOW()');
                $model->ezf_id = $this->ezf_id;
                $model->data_id = $this->data_id;
                $model->data_target = $this->data_target;
                $model->module_id = '';
                $model->action = $this->action;
                $model->notify = $this->notify;
                $model->detail = $this->detail;
                $model->mandatory = $this->mandatory;
                $model->effective_date = $this->effective_date != '' ? $this->effective_date : new Expression('CURDATE()'); // date('Y-m-d');
                $model->delay_date = $this->delay_date != '' ? $this->delay_date : new Expression('CURDATE()'); //date('Y-m-d');
                $model->due_date = $this->due_date != '' ? $this->due_date : NULL;
                $model->complete_date = $this->complete_date;
                $model->sender = $this->sender == '' ? \Yii::$app->user->id : $this->sender;
                $model->status_view = $this->status_view;
                $model->file_upload = $this->file_upload;
                $model->readonly = $this->readonly;
                $model->rstat = $this->rstat;
                $model->assign_to = $this->assign;
                $model->type_link = $this->type_link;
                $model->url = $this->url;
                $model->ezf_version = $this->version;
                $model->due_date_assign = $this->due_date_assign;
                $model->save();
                return $model;
            } catch (yii\db\Exception $e) {
                EzfFunc::addErrorLog($e);
                
                return false;
            }
        }
    }

    /**
     *
     * @param Model $modelForm โมเดลของ Ezform
     * @param array $ezf_field Ezf Field ของประเภทคำถาม notification
     * @param string $version เป็น version ของ Ezform
     */
    public function sendByEzfModel($modelForm, $ezf_field = [], $version = 'v1') {

        try {

            $ezform = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($ezf_field['ezf_id']);
            $dataEzf = \backend\modules\ezforms2\classes\EzfUiFunc::loadTbData($ezform['ezf_table'], $modelForm['id']);
            $modelFields = \backend\modules\ezforms2\models\EzformFields::find()
                    ->where('ezf_id = :ezf_id ', [':ezf_id' => $ezf_field['ezf_id']
                    ])
                    ->orderBy(['ezf_field_order' => SORT_ASC])
                    ->all();
            $fieldsGroup = \backend\modules\ezforms2\classes\EzfFunc::getFieldsGroup($modelFields, $ezform['ezf_version']);
            if (!isset(Yii::$app->session['ezf_input'])) {
                Yii::$app->session['ezf_input'] = \backend\modules\ezforms2\classes\EzfQuery::getInputv2All();
            }
//            \appxq\sdii\utils\VarDumper::dump($dataEzf);
            $path = [];
            foreach ($modelForm as $key => $field) {
                $fieldName = $key;
                if (is_array($field) && isset($field['attribute'])) {
                    $fieldName = $field['attribute'];
                }

                $changeField = TRUE;
                foreach ($fieldsGroup as $key => $value) {
                    $var = $value['ezf_field_name'];
                    $label = $value['ezf_field_label'];
                    $dataInput = null;
                    $ezf_input = null;
                    if (isset(Yii::$app->session['ezf_input'])) {
                        $ezf_input = Yii::$app->session['ezf_input'];
                        $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($value['ezf_field_type'], Yii::$app->session['ezf_input']);
                    }

                    if ($fieldName == $var && $value['ezf_field_type'] != 57 && $value['ezf_field_type'] != 58 && $value['ezf_field_type'] != 59 && $value['ezf_field_type'] != 899 && $value['ezf_field_type'] != 82) {
                        if ($ezf_input) {
                            $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($value['ezf_field_type'], $ezf_input);
                        }
                        $dataText = '';
                        if (isset($dataEzf[$var])) {
                            $dataText = \backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $value, $dataEzf);
                        }
                        $path["{" . $fieldName . "}"] = $dataText;
                    }
                }
                $path["{" . $key . "}"] = $field;
            }

//            exit();
//            
//            foreach ($modelForm as $key => $value) {
//                $path["{" . $key . "}"] = $value;
//            }
            $options = SDUtility::string2Array($ezf_field['ezf_field_options']);


            if (isset($modelForm[$ezf_field['ezf_field_name']]) && $modelForm[$ezf_field['ezf_field_name']] == 1) {


                $configAdvance = isset($options['options']['config_advance']) ? $options['options']['config_advance'] : [];
                $condition = TRUE;
                $textCondition = '';
                if (!empty($configAdvance)) {
                    foreach ($configAdvance['con_field'] as $key => $value) {
                        $field = isset($modelForm[$configAdvance['con_field'][$key]]) ? $modelForm[$configAdvance['con_field'][$key]] : '';
                        if (is_numeric($configAdvance['con_value'][$key])) {
                            $textCondition .= "'" . addslashes($field) . "' " . $configAdvance['con_condition'][$key] . " " . addslashes($configAdvance['con_value'][$key]) . " " . $configAdvance['con_choice'][$key] . " ";
                        } else {
                            $textCondition .= "'" . addslashes($field) . "' " . $configAdvance['con_condition'][$key] . " '" . addslashes($configAdvance['con_value'][$key]) . "' " . $configAdvance['con_choice'][$key] . " ";
                        }
                    }
                }
                if ($textCondition != '') {
                    $condition = eval("return " . $textCondition . ";");
                }


                if ($condition) {
                    $unique = isset($options['options']['unique']) ? $options['options']['unique'] : false;
                    $notify = isset($options['options']['notify']) ? strtr($options['options']['notify'], $path) : '';
                    $detail = isset($options['options']['detail']) ? strtr($options['options']['detail'], $path) : '';
                    $field_action = isset($options['options']['field_action']) ? $options['options']['field_action'] : '';
                    $mandatory = isset($options['options']['mandatory']) ? 1 : 2;
                    $effective_date = isset($options['options']['effective_date']) && $options['options']['effective_date'] != '' ? $modelForm[$options['options']['effective_date']] : new Expression('CURDATE()');
                    $delay_date = isset($options['options']['field_delay']) && $options['options']['field_delay'] != '' ? $modelForm[$options['options']['field_delay']] : '';
                    $due_date = isset($options['options']['due_date']) && $options['options']['due_date'] != '' ? $modelForm[$options['options']['due_date']] : NULL;
                    $file_upload = isset($options['options']['upload']) && $options['options']['upload'] != '' ? $modelForm[$options['options']['upload']] : NULL;
                    $readonly = isset($options['options']['readonly']) ? $options['options']['readonly'] : '';
                    $type_link = isset($options['options']['type_url']) && $options['options']['type_url'] != '' ? $options['options']['type_url'] : '2';
                    $url = isset($options['options']['url']) && $options['options']['url'] != '' ? $options['options']['url'] : $this->url;
                    $action_choice = isset($options['options']['action_choice']) ? $options['options']['action_choice'] : 1;
                    $alert_role = isset($options['options']['alert_role']) && $options['options']['alert_role'] != '' ? $options['options']['alert_role'] : '';
                    $alert_name = isset($options['options']['alert_name']) && $options['options']['alert_name'] != '' ? $options['options']['alert_name'] : '';
                    $name_fix = isset($options['options']['name_fix']) && $options['options']['name_fix'] != '' ? $options['options']['name_fix'] : [];
                    $role_fix = isset($options['options']['role_fix']) && $options['options']['role_fix'] != '' ? TmfFn::getRole($options['options']['role_fix']) : [];
                    $ass_email = isset($options['options']['ass_email']) && $options['options']['ass_email'] != '' ? $options['options']['ass_email'] : '';
//                    $due_date_review = isset($options['options']['due_date_review']) && $options['options']['due_date_review'] != '' ? $modelForm[$options['options']['due_date_review']] : '';
//                    $due_date_approve = isset($options['options']['due_date_approve']) && $options['options']['due_date_approve'] != '' ? $modelForm[$options['options']['due_date_approve']] : '';
                    $due_date = isset($options['options']['due_date']) && $options['options']['due_date'] != '' ? $modelForm[$options['options']['due_date']] : '';
                    $send_system = isset($options['options']['send_system']) ? $options['options']['send_system'] : false;
                    $send_email = isset($options['options']['send_email']) ? $options['options']['send_email'] : false;
                    $send_line = isset($options['options']['send_line']) ? $options['options']['send_line'] : false;
                    $val_email = isset($options['options']['val_email']) ? $options['options']['val_email'] : '';
                    $ezf_select = isset($options['options']['ezf_select']) ? $options['options']['ezf_select'] : '';
//                    $ezf_add = isset($options['options']['ezf_add']) ? $options['options']['ezf_add'] : '';
                    $ezf_target = isset($options['options']['ezf_target']) ? $options['options']['ezf_target'] : '';
                    $ezf_data = isset($options['options']['ezf_data']) ? $options['options']['ezf_data'] : '';
                    $ezf_data = isset($modelForm[$ezf_data]) ? $modelForm[$ezf_data] : '9999';
                    $this->ezf_id = $ezf_field['ezf_id'];
                    $this->data_id = $ezf_data == '' || $ezf_data == '9999' ? $modelForm['id'] : $ezf_data;
                    $this->module_id = '';

                    if ($field_action != '') {
                        $this->action = $modelForm[$field_action];
                    } else {
                        if ($field_action == 1) {
                            $this->action = $field_action;
                        } else {
                            $this->action = '';
                        }
                    }
//                $this->action = isset($options['options']['field_action']) && $options['options']['field_action'] != '' ? $modelForm[$options['options']['field_action']] : isset($options['options']['action']) && $options['options']['action'] == 1 ? $options['options']['action'] : '';
//                $this->notify = $notify;
//                $this->detail = $detail;
                    $this->mandatory = $mandatory;
//                                        date_default_timezone_set("Asia/Bangkok");
                    $this->effective_date = $effective_date; // date('Y-m-d');
                    $this->delay_date = $delay_date; //new Expression('CURDATE()'); //date('Y-m-d');
                    $this->due_date = $due_date;
                    $this->complete_date = NULL;
                    $this->sender = Yii::$app->user->id;
                    $this->status_view = '0';
                    $this->file_upload = $file_upload;
                    $this->readonly = $readonly;
                    $this->rstat = $modelForm['rstat'] != 3 ? $modelForm['rstat'] : 1;

                    $this->type_link = $type_link;
//                $this->url = $url;
                    $this->version = $version;


                    $saveDarf = FALSE;
                    $submit = FALSE;
                    $delete = FALSE;
                    if ($action_choice == '1' && $ezform['query_tools'] == '2' && $modelForm['rstat'] == '1') {
                        $saveDarf = TRUE;
                    } else if ($action_choice == '2' && (($modelForm['rstat'] == '1' && ($ezform['query_tools'] == '1' || $ezform['query_tools'] == '' || $ezform['query_tools'] == null)) || $modelForm['rstat'] == '2')) {
                        $submit = TRUE;
                    } else if ($action_choice == '3' && $modelForm['rstat'] == '3') {
                        $delete = TRUE;
                    }
//                \appxq\sdii\utils\VarDumper::dump($submit);
//            $modelForm = $event->sender;
                    if ($saveDarf || $submit || $delete) {

                        if ($this->type_link == 4) {
                            if ($ezf_select != '' && $ezf_select != '9999') {
                                $this->ezf_id = $ezf_select;
                                $this->data_id = '';
                                $this->data_target = isset($modelForm[$ezf_target]) ? $modelForm[$ezf_target] : '';
                            } else {
                                $this->data_id = '';
                                $this->data_target = isset($modelForm[$ezf_target]) ? $modelForm[$ezf_target] : '';
                            }
                            $this->type_link = 2;
                        } else if ($this->type_link == 2) {
                            if ($ezf_select != '' && $ezf_select != '9999') {
                                $this->ezf_id = $ezf_select;
                            }
                        }


                        $data_user = [];
                        $model = new TbdataAll();
                        $model->setTableName('zdata_notify');
                        EzfUiFunc::backgroundInsert(1520530564093708000, '', '');
                        $model = $model->find()->where('user_create = :user AND rstat = 0 AND xsourcex = :xsourcex', [':user' => Yii::$app->user->id, ':xsourcex' => Yii::$app->user->identity->profile->sitecode])->one();
                        $data_role = [];
                        if ($alert_role != '') {
                            $cDataRole = explode('[', $modelForm[$alert_role]);
                            if (sizeof($cDataRole) > 1) {
                                $data_role = TmfFn::getRole(SDUtility::string2Array($modelForm[$alert_role]));
                            } else {
                                $data_role = TmfFn::getRole($modelForm[$alert_role]);
                            }
                        }
                        $data_name = [];
                        if ($alert_name != '') {
                            $cDataName = explode('[', $modelForm[$alert_name]);
                            if (sizeof($cDataName) > 1) {
                                $data_name = SDUtility::string2Array($modelForm[$alert_name]);
                            } else {
                                if ($modelForm[$alert_name] != '') {
                                    $data_name[] = $modelForm[$alert_name];
                                }
                            }
                        }
                        $data_email = [];
                        if ($ass_email != '') {
                            $ass_email = str_replace(' ', '', $ass_email);
                            $ass_email = explode(',', $ass_email);
                            $ass_email = (new Query())->select('id')->from('user')->where(['email' => $ass_email])->andWhere('email <> \'\'')->all();
                            foreach ($ass_email as $vMail) {
                                $data_email[] = $vMail['id'];
                            }
                        }

//                $data_role = isset($options['options']['alert_role']) && $options['options']['alert_role'] != '' ? \backend\modules\tmf\classes\TmfFn::getRole(SDUtility::string2Array($modelForm[$options['options']['alert_role']])) : [];
//                $data_name = isset($options['options']['alert_name']) && $options['options']['alert_name'] != '' ? SDUtility::string2Array($modelForm[$options['options']['alert_name']]) : [];

                        $data_users = array_merge($data_role, $data_name, $role_fix, $name_fix, $data_email);
                        foreach ($data_users as $value) {
                            $data_user[$value] = $value;
                        }
//VarDumper::dump($data_user);
                        foreach ($data_user as $vUser) {
                            $model->setTableName('zdata_notify');
                            $this->due_date_assign = '';
                            $dataCheck = true;
                            $dataCheckDate = true;
                            $due_date = '';
                            if ($vUser != '') {
                                $dataCheck = $model->find()
                                                ->where('rstat NOT IN (0,3) AND user_create = :user_create AND assign_to = :assign_to AND data_id = :data_id AND ezf_id = :ezf_id AND sitecode = :sitecode AND action = :action AND rstat = :rstat', [
                                                    ':user_create' => Yii::$app->user->id,
                                                    ':assign_to' => $vUser,
                                                    ':ezf_id' => $ezf_field['ezf_id'],
                                                    ':data_id' => $modelForm['id'],
                                                    ':rstat' => $modelForm['rstat'],
                                                    ':sitecode' => Yii::$app->user->identity->profile->sitecode,
                                                    ':action' => $field_action != '' ? $modelForm[$field_action] : ''
//                            ':module_id'=>$mode
                                                ])->one();

//                                if ($field_action != '' && $modelForm[$field_action] == 'Review') {
//                                    $due_date = $due_date_review != '' ? $due_date_review : '';
//                                } else if ($field_action != '' && $modelForm[$field_action] == 'Approve') {
//                                    $due_date = $due_date_approve != '' ? $due_date_approve : '';
////                            }if (isset($options['options']['field_action']) && $options['options']['field_action'] != '' && $modelForm[$options['options']['field_action']] == 'Acknowledge') {
////                                $due_date = $modelForm[$options['options']['due_date_acknowledge']];
//                                }
                                if ($due_date != '') {
                                    $dataCheckDate = $model->find()
                                                    ->where('rstat NOT IN (0,3) AND user_create = :user_create AND assign_to = :assign_to AND data_id = :data_id AND ezf_id = :ezf_id AND sitecode = :sitecode AND action = :action AND due_date_assign = :due_date', [
                                                        ':user_create' => Yii::$app->user->id,
                                                        ':assign_to' => $vUser,
                                                        ':ezf_id' => $ezf_field['ezf_id'],
                                                        ':data_id' => $modelForm['id'],
                                                        ':sitecode' => Yii::$app->user->identity->profile->sitecode,
                                                        ':due_date' => $due_date,
                                                        ':action' => $field_action != '' ? $modelForm[$field_action] : ''
//                            ':module_id'=>$mode
                                                    ])->one();
                                }
                            }
//                        \appxq\sdii\utils\VarDumper::dump($send_system);
                            if ($dataCheck != TRUE || $unique == FALSE) {
                                $model = new TbdataAll();
                                $model->setTableName('zdata_notify');
                                EzfUiFunc::backgroundInsert(1520530564093708000, '', '');
                                $model = $model->find()->where('user_create = :user AND rstat = 0 AND sitecode = :sitecode', [':user' => Yii::$app->user->id, ':sitecode' => Yii::$app->user->identity->profile->sitecode])->one();

                                $this->assign = $vUser;
                                $assign = \common\modules\user\models\Profile::findOne(['user_id' => $vUser]);
                                if ($assign) {
                                    $path["{assign}"] = $assign['firstname'] . " " . $assign['lastname'];
                                }
                                $path["{ezf_id}"] = $this->ezf_id;
                                if ($ezform) {
                                    $path["{ezf_name}"] = $ezform['ezf_name'];
                                }
                                if ($dataEzf) {
                                    $path["{notify_id}"] = $model['id'];
                                }

                                $path["{project_name}"] = \backend\modules\core\classes\CoreFunc::getParams('company_name', 'name');
                                $this->notify = strtr($notify, $path);
                                $this->detail = strtr($detail, $path);
                                $this->url = strtr($url, $path);
                                if ($send_system) {
                                    $this->setValue($model);
                                }
//                        $this->Line();
                                if ($send_email) {
                                    $this->saveDataMail();
                                }
                                if ($send_line) {
                                    if ($this->delay_date == '' || $this->delay_date == NULL) {
                                        $this->saveDataLine();
                                    } else {
                                        $this->saveDataLine();
                                    }

//                            
                                }
                                if ($dataCheckDate != TRUE) {
                                    $this->due_date_assign = $due_date != '' ? $due_date : new Expression('CURDATE()');
                                    if ($send_system) {
                                        $this->setValue($model);
                                    }
                                    if ($send_email) {
                                        $this->saveDataMail();
                                    }
                                    if ($send_line) {
                                        $this->saveDataLine();
                                    }
                                }
                            }
                        }

                        if ($send_email && $val_email != '') {
//                    $this->url = isset($options['options']['url']) ? $options['options']['url'] : '';
//                    $this->detail = isset($options['options']['topic']) ? $options['options']['topic'] : '';
                            $this->assign = $val_email;
                            $this->saveDataMail();
                        }
                    }
                }
            }
            return TRUE;
        } catch (yii\db\Exception $ex) {
            EzfFunc::addErrorLog($ex);
            return FALSE;
        }
    }

    public function SendMailTemplate($email_to = '', $disableDetailFooter = false) {
        try {
            if ($email_to != '' && filter_var($email_to, FILTER_VALIDATE_EMAIL)) {
                $result = \Yii::$app->mailer->compose('@backend/mail/layouts/notify', [
                            'notify' => $this->notify,
                            'detail' => $this->detail,
                            'url' => $this->url,
                            'disableDetailFooter' => $disableDetailFooter
                        ])
                        ->setFrom(['ncrc.damasac@gmail.com' => 'nCRC Thailand'])
                        ->setTo($email_to)
                        ->setSubject($this->notify)
                        //->attach(Yii::getAlias('@webroot').'/attach/'.'brochure.pdf')
                        //->attach(Yii::getAlias('@webroot').'/attach/'.'Poster.pdf')
                        ->send();
                return $result;
            }
        } catch (\yii\db\Exception $error) {
            EzfFunc::addErrorLog($error);
            return false;
        }
    }

    public function SendMailTemplateNCRC($email_to) {
        try {
            return \Yii::$app->mailer->compose('@backend/mail/layouts/ncrc', [
                                'notify' => $this->notify,
                                'detail' => $this->detail,
                                'url' => $this->url,
                            ])
                            ->setFrom(['ncrc.damasac@gmail.com' => \Yii::$app->user->identity->profile->firstname . " " . \Yii::$app->user->identity->profile->lastname])
                            ->setTo($email_to)
                            ->setSubject($this->notify)
                            //->attach(Yii::getAlias('@webroot').'/attach/'.'brochure.pdf')
                            //->attach(Yii::getAlias('@webroot').'/attach/'.'Poster.pdf')
                            ->send();
            return $result;
        } catch (\yii\db\Exception $error) {
            EzfFunc::addErrorLog($error);
        }
    }

    public function SendMailTemplateRegistration($email_to) {
        try {
            $result = \Yii::$app->mailer->compose('@backend/mail/layouts/registration', [
                        'notify' => $this->notify,
                        'detail' => $this->detail,
                        'url' => $this->url,
                            //'conf_url'=>$conf_url
                    ])
                    ->setFrom(['ncrc.damasac@gmail.com' => "nCRC registration"])
                    ->setTo($email_to)
                    ->setSubject($this->notify)
                    ->send();
            return $result;
        } catch (\yii\db\Exception $error) {
            EzfFunc::addErrorLog($error);
        }
    }

    /**
     *
     * @param type $email_to
     * @param type $setForm array example $setForm=['ncrc.damasac@gmail.com' => "nCRC registration"];
     * @return type
     */
    public function SendMailTemplateAll($email_to, $setForm) {
        try {
            $result = \Yii::$app->mailer->compose('@backend/mail/layouts/registration', [
                        'notify' => $this->notify,
                        'detail' => $this->detail,
                        'url' => $this->url,
                            //'conf_url'=>$conf_url
                    ])
                    ->setFrom($setForm)
                    ->setTo($email_to)
                    ->setSubject($this->notify)
                    ->send();
            return $result;
        } catch (\yii\db\Exception $error) {
            EzfFunc::addErrorLog($error);
        }
    }

    public function SendMailNotTemplate($email_to) {
        try {
            $result = \Yii::$app->mailer->compose()
                    ->setFrom(['ncrc.damasac@gmail.com' => $this->title])
                    ->setTo($email_to)
                    ->setSubject('คำถามของคุณที่ ' . \Yii::$app->name)
                    ->setTextBody('หัวข้อ ติดตามคำถามของคุณได้ที่ : test')//เลือกอยางใดอย่างหนึ่ง
                    ->setHtmlBody('หัวข้อ  ติดตามคำถามของคุณได้ที่ : test ')//เลือกอยางใดอย่างหนึ่ง
                    ->send();
            return $result;
        } catch (yii\base\Exception $error) {
            EzfFunc::addErrorLog($error);
        }
    }

    private function saveDataMail() {

        try {
            $query = new \yii\db\Query();
            $user = \common\modules\user\models\User::findOne(['id' => $this->assign]);
//            $modelMail = new TbdataAll();
//            $modelMail->setTableName('notify_email');
//            $modelMail->setIsNewRecord(true);
//            $modelMail->id = SDUtility::getMillisecTime();
//            $modelMail->notify = $this->notify;
//            $modelMail->detail = $this->detail;
//            $modelMail->email = isset($user['email']) ? $user['email'] : $this->assign;
//            $modelMail->status = 1;
//            $modelMail->ezf_id = $this->ezf_id;
//            $modelMail->data_id = $this->data_id;
//            $modelMail->url = $this->url;
//            $modelMail->delay_date = $this->delay_date;
//            $modelMail->assign_to = $this->assign;
//            $modelMail->action = $this->action;
//            $modelMail->due_date_assign = $this->due_date_assign;
//            $modelMail->save();
//            $reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
            $reg_exUrl = "/(http|https)\:\/\/?/";
            $textUrl = '';
            if (preg_match($reg_exUrl, $this->url)) {
                $textUrl = $this->url;
            } else {
                $http = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
                $textUrl = $http . $_SERVER['SERVER_NAME'] . $this->url;
            }
            $query->createCommand(\Yii::$app->db_main)->insert('notify_email', [
                'id' => SDUtility::getMillisecTime(),
                'notify' => $this->notify,
                'detail' => $this->detail,
                'email' => isset($user['email']) ? $user['email'] : $this->assign,
                'status' => 1,
                'ezf_id' => $this->ezf_id,
                'data_id' => $this->data_id,
                'url' => $textUrl,
                'delay_date' => $this->delay_date,
                'assign_to' => $this->assign,
                'action' => $this->action,
                'due_date_assign' => $this->due_date_assign
            ])->execute();
        } catch (\yii\db\Exception $exc) {
            EzfFunc::addErrorLog($exc);
        }
    }

    private function updateDataMail($dataid) {

        try {
            $query = new \yii\db\Query();
            $user = \common\modules\user\models\User::findOne(['id' => $this->assign]);
            $reg_exUrl = "/(http|https)\:\/\/?/";
            $textUrl = '';
            if (preg_match($reg_exUrl, $this->url)) {
                $textUrl = $this->url;
            } else {
                $http = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
                $textUrl = $http . $_SERVER['SERVER_NAME'] . $this->url;
            }
            $query->createCommand(\Yii::$app->db_main)->update('notify_email', [
                'notify' => $this->notify,
                'detail' => $this->detail,
                'ezf_id' => $this->ezf_id,
                'url' => $textUrl,
                'delay_date' => $this->delay_date,
                    ], " id='{$dataid}' ")->execute();
        } catch (\yii\db\Exception $exc) {
            EzfFunc::addErrorLog($exc);
        }
    }

    private function saveDataLine() {

        try {
            $query = new \yii\db\Query();
            $user = $query->select('*')->from('line_user')->where(['user_id' => $this->assign])->one(\Yii::$app->db_main);
//            VarDumper::dump($user);
            if (isset($user['line_id']) && $user['line_id'] != '') {
//                $modelLine = new TbdataAll();
//                $modelLine->setTableName('notify_line');
//                $modelLine->setIsNewRecord(true);
//                $modelLine->id = SDUtility::getMillisecTime();
//                $modelLine->notify = $this->notify;
//                $modelLine->detail = $this->detail;
//                $modelLine->line_id = $user['line_id'];
//                $modelLine->status = 1;
//                $modelLine->ezf_id = $this->ezf_id;
//                $modelLine->data_id = $this->data_id;
                $reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
                $reg_exUrl = "/(http|https)\:\/\/?/";
                $textUrl = '';
                if (preg_match($reg_exUrl, $this->url)) {
                    $textUrl = $this->url;
                } else {
                    $http = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
                    $textUrl = $http . $_SERVER['SERVER_NAME'] . $this->url;
                }

//                $modelLine->delay_date = $this->delay_date;
//                $modelLine->assign_to = $this->assign;
//                $modelLine->action = $this->action;
//                $modelLine->due_date_assign = $this->due_date_assign;
//                $modelLine->save();
                $query->createCommand(\Yii::$app->db_main)->insert('notify_line', [
                    'id' => SDUtility::getMillisecTime(),
                    'notify' => $this->notify,
                    'detail' => $this->detail,
                    'line_id' => $user['line_id'],
                    'status' => 1,
                    'ezf_id' => $this->ezf_id,
                    'data_id' => $this->data_id,
                    'url' => $textUrl,
                    'delay_date' => $this->delay_date,
                    'assign_to' => $this->assign,
                    'action' => $this->action,
                    'due_date_assign' => $this->due_date_assign
                ])->execute();
            }
        } catch (\yii\db\Exception $exc) {
            EzfFunc::addErrorLog($exc);
        }
    }

    private function updateDataLine($dataid) {

        try {
            $query = new \yii\db\Query();
            $user = $query->select('*')->from('line_user')->where(['user_id' => $this->assign])->one(\Yii::$app->db_main);
//            VarDumper::dump($user);
            if (isset($user['line_id']) && $user['line_id'] != '') {
                $reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
                $reg_exUrl = "/(http|https)\:\/\/?/";
                $textUrl = '';
                if (preg_match($reg_exUrl, $this->url)) {
                    $textUrl = $this->url;
                } else {
                    $http = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
                    $textUrl = $http . $_SERVER['SERVER_NAME'] . $this->url;
                }

                $query->createCommand(\Yii::$app->db_main)->update('notify_line', [
                    'notify' => $this->notify,
                    'detail' => $this->detail,
                    'ezf_id' => $this->ezf_id,
                    'url' => $textUrl,
                    'delay_date' => $this->delay_date,
                    'action' => $this->action,
                        ], " id='{$dataid}' ")->execute();
            }
        } catch (\yii\db\Exception $exc) {
            EzfFunc::addErrorLog($exc);
        }
    }

    public function Line($assign_to) {

        try {
            $query = new \yii\db\Query();
            $user = $query->select('*')->from('line_user')->where(['user_id' => $assign_to])->one(\Yii::$app->db_main);
            if ($user) {
                return \backend\modules\line\classes\LineFn::setLine()
                                ->message($this->detail)
                                ->altMessage($this->notify)
                                ->typeTemplate($this->url)
                                ->token($user['line_token'])
                                ->pushMessage($user['line_id']);
            }
        } catch (\yii\db\Exception $error) {
            EzfFunc::addErrorLog($error);
        }
    }

    protected function getModel() {
        $model = new TbdataAll();
        $model->setTableName('zdata_notify');
        EzfUiFunc::backgroundInsert(1520530564093708000, '', '');
        $model = $model->find()->where('user_create = :user AND rstat = 0 AND sitecode = :sitecode', [':user' => Yii::$app->user->id, ':sitecode' => Yii::$app->user->identity->profile->sitecode])->one();
        return $model;
    }

    protected function setModel($dataid) {
        $model = EzfUiFunc::backgroundInsert(1520530564093708000, $dataid, '');
        return $model;
    }

}
