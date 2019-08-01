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
use Yii;
use backend\modules\ezforms2\classes\EzfFunc;
use backend\modules\ezforms2\models\TbdataAll;
use appxq\sdii\utils\SDUtility;
use backend\modules\ezforms2\classes\EzfUiFunc;
use yii\db\Expression;
use yii\base\Component;
use backend\modules\tmf\classes\TmfFn;

class Notify_1 extends Component {

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
    public $url = '/ezmodules/ezmodule/view?id=1520785643053421500';
    public $sender = '';
    public $status_view = '0';
    public $file_upload = '';
    public $rstat = '1';
    public $send_system = true;
    public $send_email = false;
    public $send_line = false;
    public $due_date_assign = '';

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
        return $this;
    }

    public function send_email($send_email) {
        $this->send_email = send_email;
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

    public function sendSatatic() {
        try {
            if (is_array($this->assign)) {
                foreach ($this->assign as $vUser) {
                    $this->assign = $vUser;
                    if ($this->send_system) {
                        $this->setValue();
                    }
                    if ($this->send_email) {
                        $this->saveDataMail();
                    }
                    if ($this->send_line) {
                        $this->saveDataLine();
                    }
                }
            } else {
                if ($this->send_system) {
                    $this->setValue();
                }
                if ($this->send_email) {
                    $this->saveDataMail();
                }
                if ($this->send_line) {
                    $this->saveDataLine();
                }
            }
            return TRUE;
        } catch (yii\db\Exception $ex) {
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
                foreach ($this->assign as $vUser) {
                    $this->assign = $vUser;
                    if ($this->send_system) {
                        $this->setValue();
                    }
                    if ($this->send_email) {
                        $this->saveDataMail();
                    }
                    if ($this->send_line) {
                        $this->saveDataLine();
                    }
                }
            } else {
                if ($this->send_system) {
                    $this->setValue();
                }
                if ($this->send_email) {
                    $this->saveDataMail();
                }
                if ($this->send_line) {
                    $this->saveDataLine();
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
                foreach ($this->assign as $vUser) {
                    $this->assign = $vUser;
                    if ($this->send_system) {
                        $this->setValue();
                    }
                    if ($this->send_email) {
                        $this->saveDataMail();
                    }
                    if ($this->send_line) {
                        $this->saveDataLine();
                    }
                }
            } else {
                if ($this->send_system) {
                    $this->setValue();
                }
                if ($this->send_email) {
                    $this->saveDataMail();
                }
                if ($this->send_line) {
                    $this->saveDataLine();
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
                foreach ($this->assign as $vUser) {
                    $this->assign = $vUser;
                    if ($this->send_system) {
                        $this->setValue();
                    }
                    if ($this->send_email) {
                        $this->saveDataMail();
                    }
                    if ($this->send_line) {
                        $this->saveDataLine();
                    }
                }
            } else {
                if ($this->send_system) {
                    $this->setValue();
                }
                if ($this->send_email) {
                    $this->saveDataMail();
                }
                if ($this->send_line) {
                    $this->saveDataLine();
                }
            }
        } catch (yii\base\Exception $ex) {
            EzfFunc::addErrorLog($ex);
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
                foreach ($this->assign as $vUser) {
                    $this->assign = $vUser;
                    if ($this->send_system) {
                        $this->setValue();
                    }
                    if ($this->send_email) {
                        $this->saveDataMail();
                    }
                    if ($this->send_line) {
                        $this->saveDataLine();
                    }
                }
            } else {
                if ($this->send_system) {
                    $this->setValue();
                }
                if ($this->send_email) {
                    $this->saveDataMail();
                }
                if ($this->send_line) {
                    $this->saveDataLine();
                }
            }
        } catch (yii\base\Exception $ex) {
            EzfFunc::addErrorLog($ex);
        }
    }

    private function setValue() {
        $model = new TbdataAll();
        $model->setTableName('zdata_notify');
        EzfUiFunc::backgroundInsert(1520530564093708000, '', '');
        $model = $model->find()->where('user_create = :user AND rstat = 0 AND sitecode = :sitecode', [':user' => Yii::$app->user->id, ':sitecode' => Yii::$app->user->identity->profile->sitecode])->one();
        if ($model) {
            $model->ezf_id = $this->ezf_id;
            $model->data_id = $this->data_id;
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

            $options = SDUtility::string2Array($ezf_field['ezf_field_options']);
            if (isset($modelForm[$ezf_field['ezf_field_name']]) && $modelForm[$ezf_field['ezf_field_name']] == 1) {

                $saveDarf = FALSE;
                if (isset($options['options']['action_savedarft']) && $options['options']['action_savedarft'] == true && isset($options['options']['query_tool']) && $options['options']['query_tool'] == '2' && $modelForm['rstat'] == 1) {
                    $saveDarf = TRUE;
                }
                $submit = FALSE;
                if (isset($options['options']['action_submit']) && $options['options']['action_submit'] == true && (($modelForm['rstat'] == 1 && isset($options['options']['query_tool']) && ($options['options']['query_tool'] == '1' || $options['options']['query_tool'] == '')) || $modelForm['rstat'] == 2)) {
                    $submit = TRUE;
                }
                $delete = FALSE;
                if (isset($options['options']['action_delete']) && $options['options']['action_delete'] == true && $modelForm['rstat'] == 3) {
                    $delete = TRUE;
                }

                $this->ezf_id = $ezf_field['ezf_id'];
                $this->data_id = $modelForm['id'];
                $this->module_id = '';
                if (isset($options['options']['field_action']) && $options['options']['field_action'] != '') {
                    $this->action = $modelForm[$options['options']['field_action']];
                } else {
                    if (isset($options['options']['action']) && $options['options']['action'] == 1) {
                        $this->action = $options['options']['action'];
                    } else {
                        $this->action = '';
                    }
                }
//                $this->action = isset($options['options']['field_action']) && $options['options']['field_action'] != '' ? $modelForm[$options['options']['field_action']] : isset($options['options']['action']) && $options['options']['action'] == 1 ? $options['options']['action'] : '';
                if ($modelForm['rstat'] == 1 && isset($options['options']['query_tool']) && $options['options']['query_tool'] == '2') {
                    $this->notify = isset($options['options']['notify_savedarft']) ? $options['options']['notify_savedarft'] : '';
                } else if (($modelForm['rstat'] == 1 && isset($options['options']['query_tool']) && ($options['options']['query_tool'] == '1' || $options['options']['query_tool'] == '')) || $modelForm['rstat'] == 2) {
                    $this->notify = isset($options['options']['notify_submit']) ? $options['options']['notify_submit'] : '';
                } else if ($modelForm['rstat'] == 3 && $delete) {
                    $this->notify = isset($options['options']['notify_delete']) ? $options['options']['notify_delete'] : '';
                }

                $this->detail = isset($options['options']['topic']) ? $options['options']['topic'] : '';
                $this->mandatory = isset($options['options']['mandatory']) ? 1 : 2;
//                                        date_default_timezone_set("Asia/Bangkok");
                $this->effective_date = isset($options['options']['effective_date']) && $options['options']['effective_date'] != '' ? $modelForm[$options['options']['effective_date']] : new Expression('CURDATE()'); // date('Y-m-d');
                $this->delay_date = isset($options['options']['field_delay']) && $options['options']['field_delay'] != '' ? $modelForm[$options['options']['field_delay']] : ''; //new Expression('CURDATE()'); //date('Y-m-d');
                $this->due_date = isset($options['options']['due_date']) && $options['options']['due_date'] != '' ? $modelForm[$options['options']['due_date']] : NULL;
                $this->complete_date = NULL;
                $this->sender = Yii::$app->user->id;
                $this->status_view = '0';
                $this->file_upload = isset($options['options']['upload']) && $options['options']['upload'] != '' ? $modelForm[$options['options']['upload']] : NULL;
                $this->readonly = isset($options['options']['readonly']) ? $options['options']['readonly'] : '';
                $this->rstat = $modelForm['rstat'] != 3 ? $modelForm['rstat'] : 1;

                $this->type_link = isset($options['options']['type_url']) && $options['options']['type_url'] != '' ? $options['options']['type_url'] : '2';
                $this->url = isset($options['options']['url']) && $options['options']['url'] != '' ? $options['options']['url'] : $this->url;
                $this->version = $version;

//            $modelForm = $event->sender;
                if ($saveDarf || $submit || $delete) {
                    $data_user = [];
                    $model = new TbdataAll();
                    $data_role = [];
                    if (isset($options['options']['alert_role']) && $options['options']['alert_role'] != '') {
                        $cDataRole = explode('[', $modelForm[$options['options']['alert_role']]);
                        if (sizeof($cDataRole) > 1) {
                            $data_role = TmfFn::getRole(SDUtility::string2Array($modelForm[$options['options']['alert_role']]));
                        } else {
                            $data_role = TmfFn::getRole($modelForm[$options['options']['alert_role']]);
                        }
                    }
                    $data_name = [];
                    if (isset($options['options']['alert_name']) && $options['options']['alert_name'] != '') {
                        $cDataName = explode('[', $modelForm[$options['options']['alert_name']]);
                        if (sizeof($cDataName) > 1) {
                            $data_name = SDUtility::string2Array($modelForm[$options['options']['alert_name']]);
                        } else {
                            if ($modelForm[$options['options']['alert_name']] != '') {
                                $data_name[] = $modelForm[$options['options']['alert_name']];
                            }
                        }
                    }

//                $data_role = isset($options['options']['alert_role']) && $options['options']['alert_role'] != '' ? \backend\modules\tmf\classes\TmfFn::getRole(SDUtility::string2Array($modelForm[$options['options']['alert_role']])) : [];
//                $data_name = isset($options['options']['alert_name']) && $options['options']['alert_name'] != '' ? SDUtility::string2Array($modelForm[$options['options']['alert_name']]) : [];
                    $name_fix = isset($options['options']['name_fix']) && $options['options']['name_fix'] != '' ? $options['options']['name_fix'] : [];
                    $role_fix = isset($options['options']['role_fix']) && $options['options']['role_fix'] != '' ? TmfFn::getRole($options['options']['role_fix']) : [];
                    $data_users = array_merge($data_role, $data_name, $role_fix, $name_fix);
                    foreach ($data_users as $value) {
                        $data_user[$value] = $value;
                    }
                    foreach ($data_user as $vUser) {
                        $model->setTableName('zdata_notify');
                        $this->due_date_assign = '';
                        $dataCheck = true;
                        $dataCheckDate = true;
                        if ($vUser != '') {
                            $dataCheck = $model->find()
                                            ->where('rstat NOT IN (0,3) AND user_create = :user_create AND assign_to = :assign_to AND data_id = :data_id AND ezf_id = :ezf_id AND sitecode = :sitecode AND action = :action AND rstat = :rstat', [
                                                ':user_create' => Yii::$app->user->id,
                                                ':assign_to' => $vUser,
                                                ':ezf_id' => $ezf_field['ezf_id'],
                                                ':data_id' => $modelForm['id'],
                                                ':rstat' => $modelForm['rstat'],
                                                ':sitecode' => Yii::$app->user->identity->profile->sitecode,
                                                ':action' => isset($options['options']['field_action']) && $options['options']['field_action'] != '' ? $modelForm[$options['options']['field_action']] : ''
//                            ':module_id'=>$mode
                                            ])->one();
                            $due_date = '';
                            if (isset($options['options']['field_action']) && $options['options']['field_action'] != '' && $modelForm[$options['options']['field_action']] == 'Review') {
                                $due_date = $modelForm[$options['options']['due_date_review']];
                            } else if (isset($options['options']['field_action']) && $options['options']['field_action'] != '' && $modelForm[$options['options']['field_action']] == 'Approve') {
                                $due_date = $modelForm[$options['options']['due_date_approve']];
//                            }if (isset($options['options']['field_action']) && $options['options']['field_action'] != '' && $modelForm[$options['options']['field_action']] == 'Acknowledge') {
//                                $due_date = $modelForm[$options['options']['due_date_acknowledge']];
                            }
                            if ($due_date != '') {
                                $dataCheckDate = $model->find()
                                                ->where('rstat NOT IN (0,3) AND user_create = :user_create AND assign_to = :assign_to AND data_id = :data_id AND ezf_id = :ezf_id AND sitecode = :sitecode AND action = :action AND due_date_assign = :due_date', [
                                                    ':user_create' => Yii::$app->user->id,
                                                    ':assign_to' => $vUser,
                                                    ':ezf_id' => $ezf_field['ezf_id'],
                                                    ':data_id' => $modelForm['id'],
                                                    ':sitecode' => Yii::$app->user->identity->profile->sitecode,
                                                    ':due_date' => $due_date,
                                                    ':action' => isset($options['options']['field_action']) && $options['options']['field_action'] != '' ? $modelForm[$options['options']['field_action']] : ''
//                            ':module_id'=>$mode
                                                ])->one();
                            }
                        }
                        if ($dataCheck != TRUE || (isset($options['options']['check_tmf']) && $options['options']['check_tmf'] == 1)) {
                            $this->assign = $vUser;
                            $this->setValue();
//                        $this->Line();
                            if (isset($options['options']['send_email']) && $options['options']['send_email'] == 1) {
                                $this->saveDataMail();
                            }
                            if (isset($options['options']['send_line']) && $options['options']['send_line'] == 1) {
                                if ($this->delay_date == '' || $this->delay_date == NULL) {
                                    $this->saveDataLine();
                                } else {
                                    $this->saveDataLine();
                                }

//                            
                            }
                            if ($dataCheckDate != TRUE) {
                                $this->due_date_assign = $due_date != '' ? $due_date : new Expression('CURDATE()');
                                $this->setValue();
                                if (isset($options['options']['send_email']) && $options['options']['send_email'] == 1) {
                                    $this->saveDataMail();
                                }
                                if (isset($options['options']['send_line']) && $options['options']['send_line'] == 1) {
                                    $this->saveDataLine();
                                }
//                            $this->Line();
//                            Yii::$app->queue->push(new \dms\aomruk\classese\NotifyJob());
//                            file_get_contents("https://thaicarecloud.000webhostapp.com/job.php");
                            }
                        }
                    }

                    if ((isset($options['options']['send_email']) && $options['options']['send_email'] == true) && (isset($options['options']['val_email']) && $options['options']['val_email'] != '')) {
//                    $this->url = isset($options['options']['url']) ? $options['options']['url'] : '';
//                    $this->detail = isset($options['options']['topic']) ? $options['options']['topic'] : '';
                        $this->assign = isset($options['options']['val_email']) && $options['options']['val_email'] != '' ? $options['options']['val_email'] : '';
                        $this->saveDataMail();
                    }
                }
            }
            return TRUE;
        } catch (yii\db\Exception $ex) {
            EzfFunc::addErrorLog($ex);
            return FALSE;
        }
    }

    public function SendMailTemplate($email_to = '') {
        try {
            if ($email_to != '') {
                $result = \Yii::$app->mailer->compose('@backend/mail/layouts/notify', [
                            'notify' => $this->notify,
                            'detail' => $this->detail,
                            'url' => $this->url,
                        ])
                        ->setFrom(['ncrc.damasac@gmail.com' => 'nCRC Thailand'])
                        ->setTo($email_to)
                        ->setSubject($this->notify)
                        //->attach(Yii::getAlias('@webroot').'/attach/'.'brochure.pdf')
                        //->attach(Yii::getAlias('@webroot').'/attach/'.'Poster.pdf')        
                        ->send();
            }
            return $result;
        } catch (\yii\db\Exception $error) {
            EzfFunc::addErrorLog($error);
        }
    }

    public function SendMailTemplateNCRC($email_to) {
        try {
            $result = \Yii::$app->mailer->compose('@backend/mail/layouts/ncrc', [
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

    public function SendMailNotTemplate($email_to) {
        try {
            $result = \Yii::$app->mailer->compose()
                    ->setFrom(['ncrc.damasac@gmail.com' => $title])
                    ->setTo($email_to)
                    ->setSubject('คำถามของคุณที่ ' . \Yii::$app->name)
                    ->setTextBody('หัวข้อ ติดตามคำถามของคุณได้ที่ : test') //เลือกอยางใดอย่างหนึ่ง
                    ->setHtmlBody('หัวข้อ  ติดตามคำถามของคุณได้ที่ : test ') //เลือกอยางใดอย่างหนึ่ง
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
            $query->createCommand(\Yii::$app->db_main)->insert('notify_email', [
                'id' => SDUtility::getMillisecTime(),
                'notify' => $this->notify . " (" . \backend\modules\core\classes\CoreFunc::getParams('company_name', 'name') . ")",
                'detail' => $this->detail,
                'email' => isset($user['email']) ? $user['email'] : $this->assign,
                'status' => 1,
                'ezf_id' => $this->ezf_id,
                'data_id' => $this->data_id,
                'url' => $this->url,
                'delay_date' => $this->delay_date,
                'assign_to' => $this->assign,
                'action' => $this->action,
                'due_date_assign' => $this->due_date_assign
            ])->execute();
        } catch (\yii\db\Exception $exc) {
            EzfFunc::addErrorLog($exc);
        }
    }

    private function saveDataLine() {

        try {
            $query = new \yii\db\Query();
            $user = $query->select('*')->from('line_user')->where(['user_id' => $this->assign])->one(\Yii::$app->db_main);
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
                $url = '';
                if (preg_match($reg_exUrl, $this->url)) {
//                    $modelLine->url = $this->url;
                    $url = $this->url;
                } else {
//                    $modelLine->url = \Yii::getAlias('@backendUrl').$this->url;
                    $http = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
                    $url = $http . $_SERVER['SERVER_NAME'] . $this->url;
//                    $textUrl = Yii::getAlias('@backendUrl') . $url;
                }

//                $modelLine->delay_date = $this->delay_date;
//                $modelLine->assign_to = $this->assign;
//                $modelLine->action = $this->action;
//                $modelLine->due_date_assign = $this->due_date_assign;
//                $modelLine->save();
                $query->createCommand(\Yii::$app->db_main)->insert('notify_line', [
                    'id' => SDUtility::getMillisecTime(),
                    'notify' => $this->notify . " (" . \backend\modules\core\classes\CoreFunc::getParams('company_name', 'name') . ")",
                    'detail' => $this->detail,
                    'line_id' => $user['line_id'],
                    'status' => 1,
                    'ezf_id' => $this->ezf_id,
                    'data_id' => $this->data_id,
                    'url' => $url,
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

}
