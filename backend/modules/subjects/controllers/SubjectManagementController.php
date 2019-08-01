<?php

namespace backend\modules\subjects\controllers;

use appxq\sdii\utils\VarDumper;
use yii\web\Controller;
use backend\modules\gantt\models\InvProject;
use Yii;
use appxq\sdii\helpers\SDHtml;
use yii\web\Response;
use backend\modules\subjects\classes\SubjectManagementQuery;
use backend\modules\ezforms2\classes\EzfQuery;
use yii\helpers\Json;
use appxq\sdii\utils\SDdate;
use appxq\sdii\utils\SDUtility;
use yii\helpers\Url;
use yii\web\UploadedFile;

class SubjectManagementController extends Controller {

    public function beforeAction($action) {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionIndex() {
        $mid = Yii::$app->request->get('id');
        $project = InvProject::findOne(['id' => $mid]);
        return $this->renderAjax('index', [
                    'project' => $project,
        ]);
    }

    public function actionTabSchedule() {
        $schedule_id = Yii::$app->request->get('widget_id');
        $procedure_id = Yii::$app->request->get('procedure_id');
        $widget_id = Yii::$app->request->get('widget_id');
        $options = Yii::$app->request->get('options');
        $user_create = Yii::$app->request->get('user_create');
        $user_update = Yii::$app->request->get('user_update');
        $reloadDiv = Yii::$app->request->get('reloadDiv');
        $module_id = Yii::$app->request->get('module_id');

        return $this->renderAjax('tab-schedule', [
                    'widget_id' => $widget_id,
                    'schedule_id' => $schedule_id,
                    'procedure_id' => $procedure_id,
                    'options' => $options,
                    'user_create' => $user_create,
                    'user_update' => $user_update,
                    'reloadDiv' => $reloadDiv,
                    'module_id' => $module_id,
        ]);
    }

    public function actionMainSchedule() {
        $schedule_id = Yii::$app->request->get('widget_id');
        $procedure_id = Yii::$app->request->get('procedure_id');
        $widget_id = Yii::$app->request->get('widget_id');
        $options = Yii::$app->request->get('options');
        $user_create = Yii::$app->request->get('user_create');
        $user_update = Yii::$app->request->get('user_update');
        $reloadDiv = Yii::$app->request->get('reloadDiv');
        $module_id = Yii::$app->request->get('module_id');

        return $this->renderAjax('main-schedule', [
                    'widget_id' => $widget_id,
                    'schedule_id' => $schedule_id,
                    'procedure_id' => $procedure_id,
                    'options' => $options,
                    'user_create' => $user_create,
                    'user_update' => $user_update,
                    'reloadDiv' => $reloadDiv,
                    'module_id' => $module_id,
        ]);
    }

    public function actionSchedule() {
        $schedule_id = Yii::$app->request->get('schedule_id');
        $procedure_id = Yii::$app->request->get('procedure_id');
        $widget_id = Yii::$app->request->get('widget_id');
        $group_name = Yii::$app->request->get('group_name');
        $group_id = Yii::$app->request->get('group_id');
        $options = Yii::$app->request->get('options');
        $user_create = Yii::$app->request->get('user_create');
        $user_update = Yii::$app->request->get('user_update');
        $reloadDiv = Yii::$app->request->get('reloadDiv');
        $module_id = Yii::$app->request->get('module_id');
        $thisPage = Yii::$app->request->get('thisPage');

        $export = Yii::$app->request->get('export');
        if (!isset($thisPage))
            $thisPage = 1;

        $pageLimit = 50;
        $pageStart = $thisPage - 1;
        if ($thisPage > 1)
            $pageStart = ($thisPage - 1) * $pageLimit;

        $visitSchedule = [];
        $count['amt'] = 0;

        try {
            $visitSchedule = SubjectManagementQuery::getVisitScheduleByWidget($widget_id, $group_id);
            
            if (isset($options['subject_ezf_id']))
                $subjectForm = EzfQuery::getEzformOne($options['subject_ezf_id']);

            if (isset($visitSchedule['11111'])) {
                $detail_form = EzfQuery::getEzformOne($visitSchedule['11111']['ezf_id']);
                $field_visit = isset($options['11111']['main_visit_name']) ? $options['11111']['main_visit_name'] : 'visit_name';

                $query = new \yii\db\Query();
                $query->select('count(*) as amt')
                        ->from($subjectForm->ezf_table)
                        ->innerJoin($detail_form['ezf_table'], $subjectForm->ezf_table . ".id=" . $detail_form['ezf_table'] . ".target")
                        ->where('group_name=:group_name AND ' . $field_visit . '="22222"', [':group_name' => $group_id]);
                $query->andWhere($subjectForm->ezf_table . '.rstat NOT IN (0,3)');

                if ($detail_form['public_listview'] == 2) {
                    $query->andWhere($subjectForm->ezf_table . '.xsourcex = :site', [':site' => Yii::$app->user->identity->profile->sitecode]);
                }

                if ($detail_form['public_listview'] == 3) {
                    $query->andWhere($detail_form->ezf_table . '.xdepartmentx = :unit', [':unit' => Yii::$app->user->identity->profile->department]);
                }

                if ($detail_form['public_listview'] == 0) {
                    $query->andWhere($detail_form->ezf_table . ".user_create=:created_by", [':created_by' => Yii::$app->user->id]);
                }

                $count = $query->one();
            }


            if ($export) {

                $result = [];
                foreach ($visitSchedule as $key => $val) {
                    $result[] = $val;
                }

                if (isset($result[1]) ){
                    $fileName = \backend\modules\subjects\classes\ExportexcelFunc::ExportExcelTbdata('Visit of schedule', 'Visit of schedule', $result[1], $result);
                    $this->redirect(Yii::getAlias('@web/print/').$fileName);
                }else{
                    return false;
                }
            }
        } catch (yii\db\Exception $e) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
        }

        return $this->renderAjax('schedule', [
                    'widget_id' => $widget_id,
                    'schedule_id' => $schedule_id,
                    'procedure_id' => $procedure_id,
                    'options' => $options,
                    'user_create' => $user_create,
                    'user_update' => $user_update,
                    'group_name' => $group_name,
                    'group_id' => $group_id,
                    'reloadDiv' => $reloadDiv,
                    'module_id' => $module_id,
                    'visitSchedule' => $visitSchedule,
                    'pageAmt' => $count['amt'],
                    'thisPage' => $thisPage,
                    'pageLimit' => $pageLimit,
                    'pageStart' => $pageStart,
        ]);
    }

    public function actionMainProcedure() {
        $schedule_id = Yii::$app->request->get('schedule_id');
        $procedure_id = Yii::$app->request->get('procedure_id');
        $widget_id = Yii::$app->request->get('widget_id');
        $options = Yii::$app->request->get('options');
        $reloadDiv = Yii::$app->request->get('reloadDiv');
        $user_create = Yii::$app->request->get('user_create');
        $user_update = Yii::$app->request->get('user_update');
        $module_id = Yii::$app->request->get('module_id');

        return $this->renderAjax('main-procedure', [
                    'widget_id' => $widget_id,
                    'schedule_id' => $schedule_id,
                    'procedure_id' => $procedure_id,
                    'options' => $options,
                    'user_create' => $user_create,
                    'user_update' => $user_update,
                    'reloadDiv' => $reloadDiv,
                    'module_id' => $module_id,
        ]);
    }

    public function actionVisitProcedure() {
        $schedule_id = Yii::$app->request->get('schedule_id');
        $procedure_id = Yii::$app->request->get('procedure_id');
        $widget_id = Yii::$app->request->get('widget_id');
        $options = Yii::$app->request->get('options');
        $reloadDiv = Yii::$app->request->get('reloadDiv');
        $group_name = Yii::$app->request->get('group_name');
        $group_id = Yii::$app->request->get('group_id');
        $user_create = Yii::$app->request->get('user_create');
        $user_update = Yii::$app->request->get('user_update');
        $module_id = Yii::$app->request->get('module_id');
        $export = Yii::$app->request->get('export');
        $dataid = Yii::$app->request->get('dataid');
        if ($dataid) { // ACTION When submit form
            $procedure_ezf_id = $options['procedure_ezf_id'];
            $procedure_form = EzfQuery::getEzformOne($procedure_ezf_id);
            $dataProcedure = SubjectManagementQuery::GetTableData($procedure_form, ['id' => $dataid], 'one');
            if (isset($dataProcedure['ezform_crf']) && $dataProcedure['ezform_crf'] != '') {
                $form_crf = SDUtility::string2Array($dataProcedure['ezform_crf']);

                foreach ($form_crf as $value) {
                    $ezform = EzfQuery::getEzformOne($value);
                    if ($ezform) {
                        $sqlAlter = " ALTER TABLE `{$ezform['ezf_table']}` ADD COLUMN IF NOT EXISTS `subject_link` VARCHAR(150) DEFAULT NULL, ADD COLUMN IF NOT EXISTS `visit_link` VARCHAR(150) DEFAULT NULL; ";
                        Yii::$app->db->createCommand($sqlAlter)->execute();
                    }
                }
            }
        }

        if ($export == true) {
            $visitSchedule = SubjectManagementQuery::getVisitScheduleByWidget($schedule_id, $group_id);
            $ezform_procedure = EzfQuery::getEzformOne($options['procedure_ezf_id']);
            $prodecureData = SubjectManagementQuery::GetTableData($ezform_procedure, "procedure_type=1 AND (group_name='$group_id' OR group_name='0' OR group_name IS NULL)");

            $number = 1;
            $modelData = [];
            $modelHeader = [];
            $modelHeader['Procedure name'][] = "Procedure name";
            $modelHeader['Procedure id'][] = "";
            foreach ($prodecureData as $key => $value) {

                $val = $value['procedure_name'];

                $subjectList = [];
                $data_subject = SubjectManagementQuery::getSubjectProcedureByName($value['id'], $group_id);
                $proId = "";

                if (is_array($data_subject)) {
                    foreach ($data_subject as $keyPro => $valPro) {
                        $subjectList[] = $valPro['visit_name'];
                    }
                }

                $index = 0;
                $modelData[$number][] = $value['id'];

                foreach ($visitSchedule as $key2 => $value2) {
                    $index ++;
                    $form_name = $value2['visit_name'];
                    $modelHeader[$form_name][] = $form_name;
                    if (in_array($value2['id'], $subjectList)) {
                        $modelData[$number][] = $value2['id'];
                    } else {
                        $modelData[$number][] = '';
                    }
                }

                $number++;
            }

            $url_curr = \cpn\chanpan\classes\CNServerConfig::getDomainName();
            $projectData = \backend\modules\subjects\classes\ReportQuery::getProjectData($url_curr);
            $acronym = isset($projectData['projectacronym']) ? $projectData['projectacronym'] : '';

            $fileName = \backend\modules\subjects\classes\ExportexcelFunc::ExportExcelTbdata('Procedure of schedule', 'Procedure of schedule-' . $acronym, $modelHeader, $modelData);
            $this->redirect(Yii::getAlias('@web/print/').$fileName);
        }

        return $this->renderAjax('visit-procedure', [
                    'widget_id' => $widget_id,
                    'schedule_id' => $schedule_id,
                    'procedure_id' => $procedure_id,
                    'options' => $options,
                    'user_create' => $user_create,
                    'user_update' => $user_update,
                    'reloadDiv' => $reloadDiv,
                    'group_name' => $group_name,
                    'group_id' => $group_id,
                    'module_id' => $module_id,
        ]);
    }

    public function actionMainFinancial() {

        $schedule_id = Yii::$app->request->get('schedule_id');
        $procedure_id = Yii::$app->request->get('procedure_id');
        $widget_id = Yii::$app->request->get('widget_id');
        $options = Yii::$app->request->get('options');
        $reloadDiv = Yii::$app->request->get('reloadDiv');
        $user_create = Yii::$app->request->get('user_create');
        $user_update = Yii::$app->request->get('user_update');
        $module_id = Yii::$app->request->get('module_id');
        $maintab = Yii::$app->request->get('maintab');
        $subtab = Yii::$app->request->get('subtab');
        $status = Yii::$app->request->get('status');

        return $this->renderAjax('main-financial', [
                    'widget_id' => $widget_id,
                    'schedule_id' => $schedule_id,
                    'procedure_id' => $procedure_id,
                    'options' => $options,
                    'user_create' => $user_create,
                    'user_update' => $user_update,
                    'reloadDiv' => $reloadDiv,
                    'module_id' => $module_id,
                    'status' => $status,
                    'maintab' => $maintab,
                    'subtab' => $subtab,
        ]);
    }

    public function actionSubFinancial() {
        $schedule_id = Yii::$app->request->get('schedule_id');
        $procedure_id = Yii::$app->request->get('procedure_id');
        $financial_id = Yii::$app->request->get('financial_id');
        $widget_id = Yii::$app->request->get('widget_id');
        $options = Yii::$app->request->get('options');
        $reloadDiv = Yii::$app->request->get('reloadDiv');
        $user_create = Yii::$app->request->get('user_create');
        $user_update = Yii::$app->request->get('user_update');
        $view = Yii::$app->request->get('view');
        $module_id = Yii::$app->request->get('module_id');
        $status = Yii::$app->request->get('status');
        $maintab = Yii::$app->request->get('maintab');
        $subtab = Yii::$app->request->get('subtab');

        return $this->renderAjax($view, [
                    'widget_id' => $widget_id,
                    'schedule_id' => $schedule_id,
                    'financial_id' => $financial_id,
                    'procedure_id' => $procedure_id,
                    'options' => $options,
                    'user_create' => $user_create,
                    'user_update' => $user_update,
                    'reloadDiv' => $reloadDiv,
                    'module_id' => $module_id,
                    'view' => $view,
                    'status' => $status,
                    'maintab' => $maintab,
                    'subtab' => $subtab,
        ]);
    }

    public function actionPaymentBreakdownGrid() {
        $schedule_id = Yii::$app->request->get('schedule_id');
        $procedure_id = Yii::$app->request->get('procedure_id');
        $financial_id = Yii::$app->request->get('financial_id');
        $widget_id = Yii::$app->request->get('widget_id');
        $options = Yii::$app->request->get('options');
        $reloadDiv = Yii::$app->request->get('reloadDiv');
        $user_create = Yii::$app->request->get('user_create');
        $user_update = Yii::$app->request->get('user_update');
        $module_id = Yii::$app->request->get('module_id');
        $dataProvider = null;

        $procedure_widget = SubjectManagementQuery::getWidgetById($options['procedure_widget_id']);
        $procedure_option = \appxq\sdii\utils\SDUtility::string2Array($procedure_widget['options']);

        $procedure_form = EzfQuery::getEzformOne($procedure_option['procedure_ezf_id']);
        $budget_form = EzfQuery::getEzformOne($options['budget_ezf_id']);
        foreach ($options['budget_fields']as $val) {
            $columns[] = $budget_form->ezf_table . "." . $val;
        }

        $activityQuery = SubjectManagementQuery::getVisitBudgetProcedure($budget_form, $columns);

        $dataProvider = new \yii\data\ArrayDataProvider([
            'allModels' => $activityQuery,
            'pagination' => [
                'pageSize' => 50,
            ],
            'sort' => [
                'attributes' => ['pro_name', 'visit_name'],
            ],
        ]);


        return $this->renderAjax('payment-breakdown-grid', [
                    'widget_id' => $widget_id,
                    'schedule_id' => $schedule_id,
                    'financial_id' => $financial_id,
                    'procedure_id' => $procedure_id,
                    'options' => $options,
                    'user_create' => $user_create,
                    'user_update' => $user_update,
                    'reloadDiv' => $reloadDiv,
                    'module_id' => $module_id,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionFinancial() {
        $schedule_id = Yii::$app->request->get('schedule_id');
        $procedure_id = Yii::$app->request->get('procedure_id');
        $widget_id = Yii::$app->request->get('widget_id');
        $options = Yii::$app->request->get('options');
        $reloadDiv = Yii::$app->request->get('reloadDiv');
        $group_name = Yii::$app->request->get('group_name');
        $group_id = Yii::$app->request->get('group_id');
        $user_create = Yii::$app->request->get('user_create');
        $user_update = Yii::$app->request->get('user_update');
        $module_id = Yii::$app->request->get('module_id');
        $view = Yii::$app->request->get('view');
        $pms_tab = Yii::$app->request->get('pms_tab');
        $_SESSION['group_id'] = $group_id;

        if (!isset($view)) {
            $view = 'financial';
        }
        return $this->renderAjax($view, [
                    'widget_id' => $widget_id,
                    'schedule_id' => $schedule_id,
                    'procedure_id' => $procedure_id,
                    'options' => $options,
                    'user_create' => $user_create,
                    'user_update' => $user_update,
                    'reloadDiv' => $reloadDiv,
                    'group_name' => $group_name,
                    'group_id' => $group_id,
                    'module_id' => $module_id,
                    'pms_tab' => $pms_tab
        ]);
    }

    public function actionGroupFinancial() {
        $schedule_id = Yii::$app->request->get('schedule_id');
        $procedure_id = Yii::$app->request->get('procedure_id');
        $widget_id = Yii::$app->request->get('widget_id');
        $options = Yii::$app->request->get('options');
        $reloadDiv = Yii::$app->request->get('reloadDiv');
        $user_create = Yii::$app->request->get('user_create');
        $user_update = Yii::$app->request->get('user_update');
        $module_id = Yii::$app->request->get('module_id');

        return $this->renderAjax('group-financial', [
                    'widget_id' => $widget_id,
                    'schedule_id' => $schedule_id,
                    'procedure_id' => $procedure_id,
                    'options' => $options,
                    'user_create' => $user_create,
                    'user_update' => $user_update,
                    'reloadDiv' => $reloadDiv,
                    'module_id' => $module_id,
        ]);
    }

    public function actionMainSubjectPayment() {
        $schedule_id = Yii::$app->request->get('schedule_id');
        $financial_id = Yii::$app->request->get('financial_id');
        $procedure_id = Yii::$app->request->get('procedure_id');
        $widget_id = Yii::$app->request->get('widget_id');
        $options = Yii::$app->request->get('options');
        $reloadDiv = Yii::$app->request->get('reloadDiv');
        $module_id = Yii::$app->request->get('module_id');

        return $this->renderAjax('main-subject-payment', [
                    'widget_id' => $widget_id,
                    'schedule_id' => $schedule_id,
                    'procedure_id' => $procedure_id,
                    'financial_id' => $financial_id,
                    'options' => $options,
                    'reloadDiv' => $reloadDiv,
                    'module_id' => $module_id,
        ]);
    }

    public function actionSubjectPayment() {
        $options = Yii::$app->request->get('options');
        $schedule_id = $options['schedule_widget_id'];
        $financial_id = $options['budget_ezf_id'];
        $procedure_id = $options['procedure_widget_id'];
        $widget_id = Yii::$app->request->get('widget_id');
        $options = Yii::$app->request->get('options');
        $reloadDiv = Yii::$app->request->get('reloadDiv');
        $module_id = Yii::$app->request->get('module_id');
        $thisPage = Yii::$app->request->get('thisPage');

        if (!isset($thisPage))
            $thisPage = 1;

        $pageLimit = 50;
        $pageStart = $thisPage - 1;
        if ($thisPage > 1)
            $pageStart = ($thisPage - 1) * $pageLimit;

        if (!isset($options['subject_payment_widget_id'])) {

            $subject_payment_widget = $options;
        } else {
            $subject_payment_widget = SDUtility::string2Array(SubjectManagementQuery::getWidgetById($options['subject_payment_widget_id'])['options']);
        }

        $schedule_widget_ref = SubjectManagementQuery::getWidgetById($options['schedule_widget_id']);

        $scheduleOptions = SDUtility::string2Array($schedule_widget_ref['options']);
        if (isset($scheduleOptions['subject_ezf_id']))
            $subjectForm = EzfQuery::getEzformOne($scheduleOptions['subject_ezf_id']);

        $ezform_main = EzfQuery::getEzformOne($scheduleOptions['11111']['main_ezf_id']);
        $subDisplay = $scheduleOptions['subject_field'];
        $field_visit = $scheduleOptions['11111']['main_visit_name'];
        $visit_name = $scheduleOptions['11111']['form_name'];
        $visit_id = '11111';
        $field_visit2 = $scheduleOptions['22222']['random_visit_name'];
        $visit_name2 = $scheduleOptions['22222']['form_name'];
        $visit_id2 = '22222';
        if ($field_visit == '')
            $field_visit = 'visit_name';


        $query = new \yii\db\Query();
        $query->select('count(*) as amt')
                ->from($subjectForm->ezf_table)
                ->innerJoin($ezform_main->ezf_table, $subjectForm->ezf_table . ".id=" . $ezform_main->ezf_table . ".target")
                ->where('(' . $ezform_main->ezf_table . '.' . $field_visit . '="' . $visit_name . '" OR ' . $field_visit . '="' . $visit_id . '")')
                ->andWhere($subjectForm->ezf_table . ".rstat NOT IN(0,3) ");
        if ($subjectForm['public_listview'] == 2) {
            $query->andWhere($subjectForm->ezf_table . '.xsourcex = :site', [':site' => Yii::$app->user->identity->profile->sitecode]);
        }

        if ($subjectForm['public_listview'] == 3) {
            $query->andWhere($subjectForm->ezf_table . '.xdepartmentx = :unit', [':unit' => Yii::$app->user->identity->profile->department]);
        }

        if ($subjectForm['public_listview'] == 0) {
            $query->andWhere($subjectForm->ezf_table . ".user_create=:created_by", [':created_by' => Yii::$app->user->id]);
        }
        $count = $query->one();

        $data = SubjectManagementQuery::GetScheduleActivity($subjectForm, $ezform_main->ezf_table, $subDisplay, '(' . $ezform_main->ezf_table . '.' . $field_visit . '="' . $visit_name . '" OR ' . $field_visit . '="' . $visit_id . '")', $pageStart, $pageLimit);


        return $this->renderAjax('subject-payment', [
                    'widget_id' => $widget_id,
                    'schedule_id' => $schedule_id,
                    'procedure_id' => $procedure_id,
                    'financial_id' => $financial_id,
                    'options' => $options,
                    'reloadDiv' => $reloadDiv,
                    'module_id' => $module_id,
                    'ezform_main' => $ezform_main,
                    'subDisplay' => $subDisplay,
                    'subject_payment_widget' => $subject_payment_widget,
                    'pageAmt' => $count['amt'],
                    'thisPage' => $thisPage,
                    'pageStart' => $pageStart,
                    'pageLimit' => $pageLimit,
                    'data' => $data,
        ]);
    }

    public function actionSubjectPaymentSearch() {
        $schedule_id = Yii::$app->request->get('schedule_id');
        $financial_id = Yii::$app->request->get('financial_id');
        $procedure_id = Yii::$app->request->get('procedure_id');
        $widget_id = Yii::$app->request->get('widget_id');
        $reloadDiv = Yii::$app->request->get('reloadDiv');
        $subjectSearch = Yii::$app->request->get('subject_search');
        $module_id = Yii::$app->request->get('module_id');
        $group_id = Yii::$app->request->get('group_id');
        $groupData = Yii::$app->request->get('groupData');
        
        $groupData = base64_decode($groupData);
        
        $groupData = SDUtility::string2Array($groupData);

        $thisPage = Yii::$app->request->get('thisPage');
        
        if (!isset($thisPage))
            $thisPage = 1;

        $pageLimit = 50;
        $pageStart = $thisPage - 1;
        if ($thisPage > 1)
            $pageStart = ($thisPage - 1) * $pageLimit;
        
        $schedule_widget_ref = SubjectManagementQuery::getWidgetById($schedule_id);
        $subjectForm = [];
        $scheduleOptions = \appxq\sdii\utils\SDUtility::string2Array($schedule_widget_ref['options']);
        if (isset($scheduleOptions['subject_ezf_id']))
            $subjectForm = EzfQuery::getEzformOne($scheduleOptions['subject_ezf_id']);
        
        $detail_ezf_id = $scheduleOptions['11111']['main_ezf_id'];
        $detail_form = EzfQuery::getEzformOne($detail_ezf_id);
        $subDisplay = $scheduleOptions['subject_field'];
        $where = "";
        if($subjectSearch){
            $where = " AND {$subDisplay} LIKE '%$subjectSearch%' ";
        }
        if($group_id != null){
            $where = " AND group_name='{$group_id}' ";
        }
        
        $data = SubjectManagementQuery::GetTableLeftJoinData($subjectForm,$detail_form, " 1=1 {$where}", 'all', '100');
        $count = SubjectManagementQuery::GetTableLeftJoinDataCount($subjectForm,$detail_form, " 1=1 {$where}");

        return $this->renderAjax('subject-payment-subject', [
            'data'=>$data,
            'group_id'=>$group_id,
            'subDisplay' => $subDisplay,
            'groupData' => $groupData,
            'thisPage' => $thisPage,
            'pageLimit' => $pageLimit,
            'pageAmt' => $count,
            'ezform_main'=>$detail_form,
            'reloadDiv' => 'display_subject_payment',
        ]);
    }

    public function actionGroupSubjectPayment() {
        $schedule_id = Yii::$app->request->get('schedule_id');
        $financial_id = Yii::$app->request->get('financial_id');
        $procedure_id = Yii::$app->request->get('procedure_id');
        $widget_id = Yii::$app->request->get('widget_id');
        $options = Yii::$app->request->get('options');
        $reloadDiv = Yii::$app->request->get('reloadDiv');
        $start_date = Yii::$app->request->get('start_date');
        $end_date = Yii::$app->request->get('end_date');
        $view = Yii::$app->request->get('view');
        $module_id = Yii::$app->request->get('module_id');
        $thisPage = Yii::$app->request->get('thisPage');

        if (isset($start_date) && $start_date != '')
            $date_start = new \DateTime($start_date);

        if (isset($end_date) && $end_date != '')
            $date_end = new \DateTime($end_date);

        return $this->renderAjax($view, [
                    'widget_id' => $widget_id,
                    'schedule_id' => $schedule_id,
                    'procedure_id' => $procedure_id,
                    'financial_id' => $financial_id,
                    'options' => $options,
                    'reloadDiv' => $reloadDiv,
                    'start_date' => isset($date_start) ? $date_start->format('Y-m-d') : null,
                    'end_date' => isset($date_end) ? $date_end->format('Y-m-d') : null,
                    'module_id' => $module_id,
                    'thisPage' => $thisPage,
        ]);
    }

    public function actionAllSubjectPayment() {
        $schedule_id = Yii::$app->request->get('schedule_id');
        $financial_id = Yii::$app->request->get('financial_id');
        $procedure_id = Yii::$app->request->get('procedure_id');
        $widget_id = Yii::$app->request->get('widget_id');
        $options = Yii::$app->request->get('options');
        $reloadDiv = Yii::$app->request->get('reloadDiv');
        $group_name = Yii::$app->request->get('group_name');
        $group_id = Yii::$app->request->get('group_id');
        $start_date = Yii::$app->request->get('start_date');
        $end_date = Yii::$app->request->get('end_date');
        $view = Yii::$app->request->get('view');
        $module_id = Yii::$app->request->get('module_id');
        $thisPage = Yii::$app->request->get('thisPage');

        if (!isset($thisPage))
            $thisPage = 1;

        $pageLimit = 50;
        $pageStart = $thisPage - 1;
        if ($thisPage > 1)
            $pageStart = ($thisPage - 1) * $pageLimit;

        //$visitSchedule = SubjectManagementQuery::getVisitScheduleByWidget($options['schedule_widget_id'], $group_id);
        $schedule_widget_ref = SubjectManagementQuery::getWidgetById($options['schedule_widget_id']);
        $scheduleOptions = SDUtility::string2Array($schedule_widget_ref['options']);

        if (isset($scheduleOptions['subject_ezf_id']))
            $subjectForm = EzfQuery::getEzformOne($scheduleOptions['subject_ezf_id']);

        if (isset($scheduleOptions['11111']))
            $detail_form = EzfQuery::getEzformOne($scheduleOptions['11111']['main_ezf_id']);

        $count = '0';
        $data= [];
        $subject_no_sql = [" (SELECT subject_no FROM  {$detail_form['ezf_table']} WHERE {$subjectForm['ezf_table']}.id={$detail_form['ezf_table']}.target AND IFNULL(subject_no,'')<>'' LIMIT 1 ) as subject_no1 "];
        if($group_id == "0" || $group_id == null){
            $data = SubjectManagementQuery::GetTableJoinData($subjectForm, $detail_form, null, 'all', $pageLimit, $pageStart,$subject_no_sql); 
            $count = SubjectManagementQuery::GetTableJoinDataCount($subjectForm, $detail_form, null);
        }else{
            $data = SubjectManagementQuery::GetTableJoinData($subjectForm, $detail_form, ['group_name' => $group_id], 'all', $pageLimit, $pageStart,$subject_no_sql); 
            $count = SubjectManagementQuery::GetTableJoinDataCount($subjectForm, $detail_form, ['group_name' => $group_id]);
        }


        return $this->renderAjax($view, [
                    'widget_id' => $widget_id,
                    'schedule_id' => $schedule_id,
                    'procedure_id' => $procedure_id,
                    'financial_id' => $financial_id,
                    'options' => $options,
                    'reloadDiv' => $reloadDiv,
                    'group_name' => $group_name,
                    'group_id' => ($group_id == '0'?null:$group_id),
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'module_id' => $module_id,
                    'thisPage' => $thisPage,
                    'pageAmt' => $count,
                    'pageLimit' => $pageLimit,
                    'pageStart' => $pageStart,
                    'data' => $data,
                    'scheduleOptions' => $scheduleOptions,
        ]);
    }

    public function actionOtherPayment() {

        $view = Yii::$app->request->get('view');

        $options = Yii::$app->request->get('options');
        $reloadDiv = Yii::$app->request->get('reloadDiv');
        $data_id = Yii::$app->request->get('data_id');
        $subject_id = Yii::$app->request->get('subject_id');
        $module_id = Yii::$app->request->get('module_id');
        $subject_payment_widget = SDUtility::string2Array(SubjectManagementQuery::getWidgetById($options['subject_payment_widget_id'])['options']);
        if (!isset($options['subject_payment_widget_id']) && $options['subject_payment_widget_id'] == '') {
            $subject_payment_widget = $options;
        }

        return $this->renderAjax($view, [
                    'options' => $options,
                    'data_id' => $data_id,
                    'reloadDiv' => $reloadDiv,
                    'subject_id' => $subject_id,
                    'module_id' => $module_id,
                    'subject_payment_widget' => $subject_payment_widget
        ]);
    }

    public function actionSubjectVisit() {
        $options = Yii::$app->request->get('options');
        $data_id = Yii::$app->request->get('data_id');
        $subject_id = Yii::$app->request->get('subject_id');
        $group_name = Yii::$app->request->get('group_name');
        $group_id = Yii::$app->request->get('group_id');
        $module_id = Yii::$app->request->get('module_id');
        $subject_payment_widget = Yii::$app->request->get('subject_payment_widget');

        return $this->renderAjax('subject-visit', [
                    'options' => $options,
                    'data_id' => $data_id,
                    'subject_id' => $subject_id,
                    'group_name' => $group_name,
                    'group_id' => $group_id,
                    'module_id' => $module_id,
                    'subject_payment_widget' => $subject_payment_widget,
        ]);
    }

    public function actionSubjectViewData() {
        $options = Yii::$app->request->get('options');
        $visit_name = Yii::$app->request->get('name');
        $visit_id = Yii::$app->request->get('visit_id');
        $visit_field = Yii::$app->request->get('field');
        $data_id = Yii::$app->request->get('data_id');
        $subject_id = Yii::$app->request->get('subject_id');
        $group_name = Yii::$app->request->get('group_name');
        $group_id = Yii::$app->request->get('group_id');
        $module_id = Yii::$app->request->get('module_id');
        $subject_payment_widget = Yii::$app->request->get('subject_payment_widget');

        return $this->renderAjax('subject-view-data', [
                    'options' => $options,
                    'visit_id' => $visit_id,
                    'visit_name' => $visit_name,
                    'data_id' => $data_id,
                    'subject_id' => $subject_id,
                    'visit_field' => $visit_field,
                    'group_name' => $group_name,
                    'group_id' => $group_id,
                    'module_id' => $module_id,
                    'subject_payment_widget' => $subject_payment_widget,
        ]);
    }

    public function actionAllSubjectViewData() {
        $options = Yii::$app->request->get('options');
        $visit_id = Yii::$app->request->get('id');
        $visit_name = Yii::$app->request->get('name');
        $visit_field = Yii::$app->request->get('field');
        $data_id = Yii::$app->request->get('data_id');
        $module_id = Yii::$app->request->get('module_id');

        return $this->renderAjax('subject-view-data', [
                    'options' => $options,
                    'visit_id' => $visit_id,
                    'visit_name' => $visit_name,
                    'data_id' => $data_id,
                    'visit_field' => $visit_field,
                    'module_id' => $module_id,
        ]);
    }

    public function actionSubjectAdditionalPayment() {
        $options = Yii::$app->request->get('options');
        $visit_name = Yii::$app->request->get('visit_name');
        $data_id = Yii::$app->request->get('data_id');
        $budget_id = \appxq\sdii\utils\SDUtility::string2Array(Yii::$app->request->get('budget_id'));
        $revenue = Yii::$app->request->get('revenue');
        $expense = Yii::$app->request->get('expense');
        $income_lumpsum = Yii::$app->request->get('income_lumpsum');
        $group_name = Yii::$app->request->get('group_name');
        $group_id = Yii::$app->request->get('group_id');
        $visit_id = Yii::$app->request->get('visit_id');
        $visit_field = Yii::$app->request->get('visit_field');
        $module_id = Yii::$app->request->get('module_id');
        $procedure_widget_id = Yii::$app->request->get('procedure_widget_id');
        $budget_ezf_id = Yii::$app->request->get('budget_ezf_id');
        $budget_fields = Yii::$app->request->get('budget_fields');

        return $this->renderAjax('subject-additional', [
                    'options' => $options,
                    'visit_name' => $visit_name,
                    'data_id' => $data_id,
                    'budget_id' => $budget_id,
                    'budget_ezf_id' => $budget_ezf_id,
                    'revenue' => $revenue,
                    'expense' => $expense,
                    'income_lumpsum' => $income_lumpsum,
                    'group_name' => $group_name,
                    'group_id' => $group_id,
                    'module_id' => $module_id,
                    'visit_field' => $visit_field,
                    'visit_id' => $visit_id,
                    'procedure_widget_id' => $procedure_widget_id,
                    'budget_fields' => $budget_fields,
        ]);
    }

    public function actionConfigView() {
        $id = Yii::$app->request->get('widget_id');
        $data_id = Yii::$app->request->get('data_id');
        $key_index = Yii::$app->request->get('key_index');
        $ezf_id = Yii::$app->request->get('ezf_id');
        $group_name = Yii::$app->request->get('group_name');
        $group_id = Yii::$app->request->get('group_id');
        $reloadDiv = Yii::$app->request->get('reloadDiv');
        $options = Yii::$app->request->get('options');
        $action = Yii::$app->request->get('action');
        $model = null;
        if (isset($data_id) && $data_id != null) {
            if ($data_id == '11111' || $data_id == '22222') {
                if (empty($group_id))
                    $group_id = '0';
                $modelWidget = new \backend\modules\ezmodules\models\EzmoduleWidget();
                $model = $modelWidget->findOne(['widget_id' => $id]);
            } else {
                $modelVisit = new \backend\modules\subjects\models\VisitSchedule();
                $model = $modelVisit->findOne(['id' => $data_id]);
                if (empty($group_id))
                    $group_id = $model->group_name;
            }
        }

        return $this->renderAjax('_schedule-config', [
                    'model' => $model,
                    'key_index' => $key_index,
                    'data_id' => $data_id,
                    'ezf_id' => $ezf_id,
                    'widget_id' => $id,
                    'group_name' => $group_name,
                    'group_id' => $group_id,
                    'reloadDiv' => $reloadDiv,
                    'options' => $options,
                    'action' => $action,
        ]);
    }

    public function actionConfigVisitProcedure() {
        $id = Yii::$app->request->get('widget_id');
        $data_id = Yii::$app->request->get('data_id');
        $key_index = Yii::$app->request->get('key_index');
        $ezf_id = Yii::$app->request->get('ezf_id');
        $group_name = Yii::$app->request->get('group_name');
        $group_id = Yii::$app->request->get('group_id');
        $widget_id = Yii::$app->request->get('widget_id');
        $schedule_id = Yii::$app->request->get('schedule_id');
        $reloadDiv = Yii::$app->request->get('reloadDiv');
        $options = Yii::$app->request->get('options');
        $action = Yii::$app->request->get('action');
        $model = null;

        $scheduleWidget = SubjectManagementQuery::getWidgetById($schedule_id);
        $scheduleOption = $scheduleWidget['options'];
        $scheduleOption = SDUtility::string2Array($scheduleOption);

        if (isset($data_id) && $data_id != null) {
            if ($data_id == '11111' || $data_id == '22222') {
                if (empty($group_id))
                    $group_id = '0';
                $modelWidget = new \backend\modules\ezmodules\models\EzmoduleWidget();
                $model = $modelWidget->findOne(['widget_id' => $schedule_id]);
            } else {
                $modelVisit = new \backend\modules\subjects\models\VisitSchedule();
                $model = $modelVisit->findOne(['id' => $data_id]);
                if (empty($group_id))
                    $group_id = $model->group_name;
            }
        }

        return $this->renderAjax('_schedule-config', [
                    'model' => $model,
                    'key_index' => $key_index,
                    'data_id' => $data_id,
                    'ezf_id' => $ezf_id,
                    'widget_id' => $schedule_id,
                    'group_name' => $group_name,
                    'group_id' => $group_id,
                    'reloadDiv' => $reloadDiv,
                    'options' => $scheduleOption,
                    'action' => $action,
        ]);
    }

    public function actionFirstSchedule() {
        $reloadDiv = Yii::$app->request->get('reloadDiv');
        $field_subject = Yii::$app->request->get('field_subject');
        $schedule_id = Yii::$app->request->get('schedule_id');
        $options = Yii::$app->request->get('options');
        $modal = Yii::$app->request->get('modal');
        $number = Yii::$app->request->get('number');
        $group_id = Yii::$app->request->get('group_id');
        $visit_id = Yii::$app->request->get('visit_id');
        $module_id = Yii::$app->request->get('module_id');
        $date_consent = Yii::$app->request->get('date_consent');
        $next_date = Yii::$app->request->get('next_date');
        $subject_status = Yii::$app->request->get('subject_status');
        $subject_profile_ezf = $options['subject_ezf_id'];
        $subject_detail_ezf = $options['11111']['main_ezf_id'];
        $detail_column = $options['11111']['main_field_display'];
        $profile_column = $options['subject_field_display'];
        $export = Yii::$app->request->get('export');
        $profile_form = EzfQuery::getEzformOne($subject_profile_ezf);
        $detail_form = EzfQuery::getEzformOne($subject_detail_ezf);
        if (isset($date_consent) && !empty($date_consent))
            $date_consent = SDdate::phpThDate2mysqlDate($date_consent);
        if (isset($next_date) && !empty($next_date))
            $next_date = SDdate::phpThDate2mysqlDate($next_date);
        $where = null;
        $having = null;
        if ($number != '') {
            $where = "  " . $profile_form->ezf_table . ".subject_number LIKE '%{$number}%'";
        }
        if ($group_id != '' && $group_id != '1' && $group_id != '0') {
            if ($where != null)
                $where .= " AND " . $detail_form->ezf_table . ".group_name LIKE '%{$group_id}%'";
            else {
                $where .= "  " . $detail_form->ezf_table . ".group_name LIKE '%{$group_id}%'";
            }
        }
        if ($visit_id != '' && $visit_id != 'Loading ...') {
            if ($having != null)
                $having .= " AND visit_name LIKE '%{$visit_id}%'";
            else
                $having .= "visit_name LIKE '%{$visit_id}%'";
        }
        if ($date_consent != '') {
            if ($having != null)
                $having .= " AND DATE(inform_date)='{$date_consent}'";
            else
                $having .= "  DATE(inform_date)='{$date_consent}'";
        }
        if ($next_date != '') {
            if ($having != null)
                $having .= " AND DATE(next_visit_date)='{$next_date}'";
            else
                $having .= "  DATE(next_visit_date)='{$next_date}'";
        }

        if (isset($subject_status) && $subject_status != '0') {
            if ($having != null)
                $having .= " AND type_visit='{$subject_status}'";
            else
                $having .= "  type_visit='{$subject_status}'";
        }

        if (isset($export) && $export == true) {
            $activityQuery = SubjectManagementQuery::GetTableActivity($profile_form, $detail_form->ezf_table, $detail_column, $where, $having);
            $visitSchedule = SubjectManagementQuery::getVisitScheduleByWidget($schedule_id);
            $result = $activityQuery->all();
            $fileName = \backend\modules\subjects\classes\ExportexcelFunc::ExportExcelTbdata('Subjects Management System', 'Subjects Management System', $result[0], $result);
            $this->redirect(Yii::getAlias('@web/print/').$fileName);
        } else {
            $activityQuery = SubjectManagementQuery::GetTableActivity($profile_form, $detail_form->ezf_table, $detail_column, $where, $having);
            $visitSchedule = SubjectManagementQuery::getVisitScheduleByWidget($schedule_id);
            $dataProvider = new \yii\data\ActiveDataProvider([
                'query' => $activityQuery,
                'pagination' => [
                    'pageSize' => 50,
                //'route' => '/ezforms2/fileinput/grid-update',
                ],
                'sort' => [
//'route' => '/ezforms2/fileinput/grid-update',
                    'defaultOrder' => [
//'create_date' => SORT_DESC
                    ]
                ]
            ]);

            return $this->renderAjax('first-schedule', [
                        'reloadDiv' => $reloadDiv,
                        'subject_profile_ezf' => $subject_profile_ezf,
                        'subject_detail_ezf' => $subject_detail_ezf,
                        'dataProvider' => $dataProvider,
                        'profile_column' => $profile_column,
                        'detail_column' => $detail_column,
                        'field_subject' => $field_subject,
                        'schedule_id' => $schedule_id,
                        'visitSchedule' => $visitSchedule,
                        'options' => $options,
                        'modal' => $modal,
                        'module_id' => $module_id,
                        'profile_ezf' => $subject_profile_ezf,
            ]);
        }
    }

    public function actionExportFirstSchedule() {
        if (Yii::$app->getRequest()->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $reloadDiv = Yii::$app->request->get('reloadDiv');
            $field_subject = Yii::$app->request->get('field_subject');
            $schedule_id = Yii::$app->request->get('schedule_id');
            $options = Yii::$app->request->get('options');
            $modal = Yii::$app->request->get('modal');
            $number = Yii::$app->request->get('number');
            $group_id = Yii::$app->request->get('group_id');
            $module_id = Yii::$app->request->get('module_id');
            $date_consent = Yii::$app->request->get('date_consent');
            $next_date = Yii::$app->request->get('next_date');
            $subject_status = Yii::$app->request->get('subject_status');

            $export = Yii::$app->request->get('export');

            //=== Export Data ===
            $url = Url::to(['/subjects/subject-management/first-schedule',
                        'schedule_id' => $schedule_id,
                        'module_id' => $module_id,
                        'options' => $options,
                        'reloadDiv' => $reloadDiv,
                        'next_date' => $next_date,
                        'date_consent' => $date_consent,
                        'subject_status' => $subject_status,
                        'number' => $number,
                        'group_id' => $group_id,
                        'field_subject' => $field_subject,
                        'export' => true,
            ]);

            $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
            $html = '<iframe src="' . $protocol . getenv('HTTP_HOST') . $url . '" width="100%" height="200px" />';

            $result = [
                'status' => 'success',
                'action' => 'report',
                'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Load completed.'),
                'html' => $html,
            ];

            return \yii\helpers\Json::encode($result);
        } else {
            throw new \yii\web\NotFoundHttpException('Invalid request. Please do not repeat this request again.');
        }
    }

    public function actionExportVisitSchedule() {
        if (Yii::$app->getRequest()->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $reloadDiv = Yii::$app->request->get('reloadDiv');
            $field_subject = Yii::$app->request->get('field_subject');
            $schedule_id = Yii::$app->request->get('schedule_id');
            $options = Yii::$app->request->get('options');
            $modal = Yii::$app->request->get('modal');
            $number = Yii::$app->request->get('number');
            $group_id = Yii::$app->request->get('group_id');
            $widget_id = Yii::$app->request->get('widget_id');
            $module_id = Yii::$app->request->get('module_id');
            $date_consent = Yii::$app->request->get('date_consent');
            $next_date = Yii::$app->request->get('next_date');
            $subject_status = Yii::$app->request->get('subject_status');

            //=== Export Data ===
            $url = Url::to(['/subjects/subject-management/schedule',
                        'schedule_id' => $schedule_id,
                        'module_id' => $module_id,
                        'widget_id' => $widget_id,
                        'options' => $options,
                        'reloadDiv' => $reloadDiv,
                        'next_date' => $next_date,
                        'date_consent' => $date_consent,
                        'subject_status' => $subject_status,
                        'number' => $number,
                        'group_id' => $group_id,
                        'field_subject' => $field_subject,
                        'export' => true,
            ]);

            $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
            $html = '<iframe src="' . $protocol . getenv('HTTP_HOST') . $url . '" width="100%" height="200px" />';

            $result = [
                'status' => 'success',
                'action' => 'report',
                'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Load completed.'),
                'html' => $html,
            ];

            return \yii\helpers\Json::encode($result);
        } else {
            throw new \yii\web\NotFoundHttpException('Invalid request. Please do not repeat this request again.');
        }
    }

    public function actionExportVisitProcedure() {
        if (Yii::$app->getRequest()->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $reloadDiv = Yii::$app->request->get('reloadDiv');
            $field_subject = Yii::$app->request->get('field_subject');
            $schedule_id = Yii::$app->request->get('schedule_id');
            $options = Yii::$app->request->get('options');
            $modal = Yii::$app->request->get('modal');
            $group_id = Yii::$app->request->get('group_id');
            $widget_id = Yii::$app->request->get('widget_id');
            $module_id = Yii::$app->request->get('module_id');

            //=== Export Data ===
            $url = Url::to(['/subjects/subject-management/visit-procedure',
                        'schedule_id' => $schedule_id,
                        'module_id' => $module_id,
                        'widget_id' => $widget_id,
                        'options' => $options,
                        'reloadDiv' => $reloadDiv,
                        'group_id' => $group_id,
                        'export' => true,
            ]);

            $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
            $html = '<iframe src="' . $protocol . getenv('HTTP_HOST') . $url . '" width="100%" height="200px" />';

            $result = [
                'status' => 'success',
                'action' => 'report',
                'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Load completed.'),
                'html' => $html,
            ];

            return \yii\helpers\Json::encode($result);
        } else {
            throw new \yii\web\NotFoundHttpException('Invalid request. Please do not repeat this request again.');
        }
    }

    public function actionDeleteVisit() {
        if (\Yii::$app->getRequest()->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $data_id = Yii::$app->request->get('data_id');

            try {
                $sql = " DELETE FROM zdata_visit_schedule WHERE id=:id ";
                $result = \Yii::$app->db->createCommand($sql, [':id' => $data_id])->execute();
                if ($result) {
                    $result = [
                        'status' => 'success',
                        'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
                    ];

                    return $result;
                }
            } catch (\yii\db\Exception $ex) {
                \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
            }
        }
    }

    public function actionRandomFormConfig() {
        $key = Yii::$app->request->get('key');
        $optionsReq = Yii::$app->request->get('options');
        $options = \appxq\sdii\utils\SDUtility::string2Array($optionsReq);
        return $this->renderAjax('_schedule-rct-config', ['key_index' => $key, 'options' => $options]);
    }

    public function actionAddInputProcedure() {
        $options_get = Yii::$app->request->get('options');
        $options = \appxq\sdii\utils\SDUtility::string2Array($options_get);
        return $this->renderAjax('_form-procedure', ['options' => $options]);
    }

    public function actionFormAddProcedure() {
        $widget_id = Yii::$app->request->get('widget_id');
        return $this->renderAjax('_form-add-procedure', ['widget_id' => $widget_id]);
    }

    public function actionUpdateSubjectProcedure() {
        if (\Yii::$app->getRequest()->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $module_id = Yii::$app->request->get('module_id');
            $widget_id = Yii::$app->request->get('widget_id');
            $check_val = Yii::$app->request->get('check_val');
            $index = Yii::$app->request->get('index');
            $target = Yii::$app->request->get('id');
            $name = Yii::$app->request->get('name');
            $visit_name = Yii::$app->request->get('visit_name');
            $visit_id = Yii::$app->request->get('visit_id');
            $group_name = Yii::$app->request->get('group_name');
            $group_id = Yii::$app->request->get('group_id');
            $checked = Yii::$app->request->get('checked');
            $sitecode = Yii::$app->user->identity->profile->sitecode;
            try {
                if ($check_val == '1') {
                    $model = new \backend\modules\subjects\models\VisitProcedure();
                    $id = \appxq\sdii\utils\SDUtility::getMillisecTime();
                    $userId = \Yii::$app->user->id;
                    $nowDate = date('Y-m-d H:i:s');

                    $model->id = $id;
                    $model->procedure_name = $target;
                    $model->module_id = $module_id;
                    $model->widget_id = $widget_id;
                    $model->target = $target;
                    $model->visit_name = $visit_id;
                    $model->group_name = $group_id;
                    $model->sitecode = $sitecode;
                    $model->create_by = $userId . '';
                    $model->create_at = $nowDate;
                    $model->update_by = $userId . '';
                    $model->update_at = $nowDate;

                    if ($model->save()) {
                        $result = [
                            'status' => 'success',
                            'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
                            'data' => $model,
                        ];
                    } else {
                        $result = [
                            'status' => 'error',
                            'message' => SDHtml::getMsgError() . Yii::t('app', 'Data error..'),
                        ];
                    }
                    return $result;
                } else {
                    $visitPro = new \backend\modules\subjects\models\VisitProcedure();
                    $delete = Yii::$app->db->createCommand("DELETE FROM " . $visitPro->tableName() . " WHERE target='" . $target . "' AND visit_name='" . $visit_id . "' AND group_name='" . $group_id . "'");
                    $result = [];
                    if ($delete->execute()) {
                        $result = [
                            'status' => 'success',
                            'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
                        ];
                    }
                    return $result;
                }
            } catch (yii\db\Exception $ex) {
                \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
                $result = [
                    'status' => 'error',
                    'message' => SDHtml::getMsgError() . Yii::t('app', 'Data error base.'),
                ];
                return $result;
            }
        }
    }

    public function actionSaveSchedule() {
        if (\Yii::$app->getRequest()->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $widget_id = Yii::$app->request->post('widget_id');
            $data_id = Yii::$app->request->post('data_id');
            $ezf_id = Yii::$app->request->post('ezf_id');
            $options = Yii::$app->request->post('options');
            $node_id = \appxq\sdii\utils\SDUtility::getMillisecTime();
            $nowDate = date('Y-m-d H:i:s');
            $userid = \Yii::$app->user->id;
            $mid = Yii::$app->request->post('id');
            $mid == '' ? $mid = '1' : '';
            $department = Yii::$app->user->identity->profile->department;
            $sitecode = Yii::$app->user->identity->profile->sitecode;

            try {
                if ($data_id != null) {
                    if ($data_id == '11111' || $data_id == '22222') {
                        $widgetModel = new \backend\modules\ezmodules\models\EzmoduleWidget();
                        $query = $widgetModel->findOne(['widget_id' => $widget_id]);
                        $options_old = \appxq\sdii\utils\SDUtility::string2Array($query['options']);
                        if ($data_id == '11111') {
                            $options_old[$data_id]['form_name'] = $options['form_name'];
                            $options_old[$data_id]['main_ezf_id'] = $options['ezf_id'];
                            $options_old[$data_id]['main_actual_date'] = isset($options['actual_date']) ? $options['actual_date'] : null;
                            $options_old[$data_id]['main_visit_name'] = isset($options['visit_name_mapping']) ? $options['visit_name_mapping'] : null;
                            $options_old[$data_id]['main_earliest_distance'] = $options['earliest_date'];
                            $options_old[$data_id]['main_latest_distance'] = $options['latest_date'];
                            $options_old[$data_id]['form_list'] = isset($options['form_list']) ? \appxq\sdii\utils\SDUtility::array2String($options['form_list']) : null;
                            $options_old[$data_id]['warning_users'] = isset($options['warning_users']) ? \appxq\sdii\utils\SDUtility::array2String($options['warning_users']) : null;
                            $options_old[$data_id]['warning_roles'] = isset($options['warning_roles']) ? \appxq\sdii\utils\SDUtility::array2String($options['warning_roles']) : null;
                        } else {
                            $options_old[$data_id]['form_name'] = $options['form_name'];
                            $options_old[$data_id]['random_ezf_id'] = $options['ezf_id'];
                            $options_old[$data_id]['random_actual_date'] = isset($options['actual_date']) ? $options['actual_date'] : null;
                            $options_old[$data_id]['random_visit_name'] = isset($options['visit_name_mapping']) ? $options['visit_name_mapping'] : null;
                            $options_old[$data_id]['random_plan_distance'] = $options['plan_date'];
                            $options_old[$data_id]['random_earliest_distance'] = $options['earliest_date'];
                            $options_old[$data_id]['random_latest_distance'] = $options['latest_date'];
                            $options_old[$data_id]['form_list'] = isset($options['form_list']) ? \appxq\sdii\utils\SDUtility::array2String($options['form_list']) : null;
                            $options_old[$data_id]['warning_users'] = isset($options['warning_users']) ? \appxq\sdii\utils\SDUtility::array2String($options['warning_users']) : null;
                            $options_old[$data_id]['warning_roles'] = isset($options['warning_roles']) ? \appxq\sdii\utils\SDUtility::array2String($options['warning_roles']) : null;
                        }

                        if (isset($options['form_list']) && is_array($options['form_list'])) {
                            foreach ($options['form_list'] as $value) {
                                $ezform = EzfQuery::getEzformOne($value);
                                if ($ezform) {
                                    $sqlAlter = " ALTER TABLE `{$ezform['ezf_table']}` ADD COLUMN IF NOT EXISTS `subject_link` VARCHAR(150) DEFAULT NULL, ADD COLUMN IF NOT EXISTS `visit_link` VARCHAR(150) DEFAULT NULL; ";
                                    Yii::$app->db->createCommand($sqlAlter)->execute();
                                }
                            }
                        }

                        $query->options = \appxq\sdii\utils\SDUtility::array2String($options_old);
                        $query->updated_at = $nowDate;

                        if ($query->update()) {
                            $result = [
                                'status' => 'success',
                                'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
                                'data' => $options_old,
                            ];
                            return $result;
                        } else {
                            $result = [
                                'status' => 'error',
                                'message' => SDHtml::getMsgError() . Yii::t('app', 'Data error.'),
                            ];
                            return $result;
                        }
                    } else {
                        $visitModel = new \backend\modules\subjects\models\VisitSchedule();
                        $query = $visitModel->findOne(['id' => $data_id]);

                        $query->ezf_id = $options['ezf_id'];
                        $query->visit_name = $options['form_name'];
                        $query->group_name = $options['group_name'];
                        $query->plan_date = $options['plan_date'];
                        $query->visit_cal_date = $options['visit_cal_date'];
                        $query->field_cal_date = $options['field_cal_date'];
                        $query->earliest_date = $options['earliest_date'];
                        $query->latest_date = $options['latest_date'];
                        $query->form_list = isset($options['form_list']) ? \appxq\sdii\utils\SDUtility::array2String($options['form_list']) : null;
                        $query->warning_users = isset($options['warning_users']) ? \appxq\sdii\utils\SDUtility::array2String($options['warning_users']) : null;
                        $query->warning_roles = isset($options['warning_roles']) ? \appxq\sdii\utils\SDUtility::array2String($options['warning_roles']) : null;
                        $query->sitecode = $sitecode;
                        $query->created_by = $userid . '';
                        $query->created_at = $nowDate;

                        if ($query->update()) {
                            $result = [
                                'status' => 'success',
                                'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
                                'data' => $visitModel,
                            ];
                            return $result;
                        } else {
                            $result = [
                                'status' => 'error',
                                'message' => SDHtml::getMsgError() . Yii::t('app', 'Data error.'),
                            ];
                            return $result;
                        }
                    }
                } else {
                    $visitModel = new \backend\modules\subjects\models\VisitSchedule();
                    $visitModel->id = $node_id;
                    $visitModel->module_id = $mid;
                    $visitModel->widget_id = $widget_id;
                    $visitModel->schedule_id = $widget_id;
                    $visitModel->ezf_id = $options['ezf_id'];
                    $visitModel->visit_name = $options['form_name'];
                    $visitModel->visit_parent = '0';
                    $visitModel->actual_date = isset($options['actual_date']) ? $options['actual_date'] : null;
                    $visitModel->visit_name_mapping = isset($options['visit_name_mapping']) ? $options['visit_name_mapping'] : null;
                    $visitModel->plan_date = $options['plan_date'];
                    $visitModel->earliest_date = $options['earliest_date'];
                    $visitModel->latest_date = $options['latest_date'];
                    $visitModel->group_name = $options['group_name'];
                    $visitModel->visit_cal_date = isset($options['visit_cal_date']) ? $options['visit_cal_date'] : null;
                    $visitModel->field_cal_date = isset($options['field_cal_date']) ? $options['field_cal_date'] : null;
                    $visitModel->rstat = '1';
                    $visitModel->sitecode = $sitecode;
                    $visitModel->open_node = '1';
                    $visitModel->form_list = isset($options['form_list']) ? \appxq\sdii\utils\SDUtility::array2String($options['form_list']) : null;
                    $visitModel->warning_users = isset($options['warning_users']) ? \appxq\sdii\utils\SDUtility::array2String($options['warning_users']) : null;
                    $visitModel->warning_roles = isset($options['warning_roles']) ? \appxq\sdii\utils\SDUtility::array2String($options['warning_roles']) : null;
                    $visitModel->created_by = $userid . '';
                    $visitModel->created_at = $nowDate;
                    $visitModel->update_by = $userid . '';
                    $visitModel->update_at = $nowDate;

                    if ($visitModel->save()) {
                        $result = [
                            'status' => 'success',
                            'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
                            'data' => $visitModel,
                        ];
                        return $result;
                    } else {
                        $result = [
                            'status' => 'error',
                            'message' => SDHtml::getMsgError() . Yii::t('app', 'Data error.'),
                        ];
                        return $result;
                    }
                }
            } catch (\yii\db\Exception $ex) {
                \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
                $result = [
                    'status' => 'error',
                    'message' => SDHtml::getMsgError() . Yii::t('app', 'Data error base.'),
                ];
                return $result;
            }
        }
    }

    public function actionUpdateProcedure() {
        if (\Yii::$app->getRequest()->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $widget_id = Yii::$app->request->post('widget_id');
            $options = Yii::$app->request->post('options');

            $widgetModel = new \backend\modules\ezmodules\models\EzmoduleWidget();
            $model = $widgetModel->findOne($widget_id);
            $options_old = \appxq\sdii\utils\SDUtility::string2Array($model->options);

            foreach ($options['procedure_name'] as $key => $val) {
                $options_old['procedure_name'][$key] = $val;
            }

            $model->options = \appxq\sdii\utils\SDUtility::array2String($options_old);
            if ($model->update()) {
                $result = [
                    'status' => 'success',
                    'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
                    'data' => $model,
                ];
                return $result;
            }
        }
    }

    public function actionDeleteProcedure() {
        if (\Yii::$app->getRequest()->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $widget_id = Yii::$app->request->get('widget_id');
            $procedure_name = Yii::$app->request->get('procedure_name');
            $procedure_id = Yii::$app->request->get('procedure_id');

            $widgetModel = new \backend\modules\ezmodules\models\EzmoduleWidget();
            $model = $widgetModel->findOne($widget_id);
            $options_old = \appxq\sdii\utils\SDUtility::string2Array($model->options);
            $options_new = [];

            foreach ($options_old['procedure_name'] as $key => $val) {
                if ($key != $procedure_id) {
                    $options_new[$key] = $val;
                }
            }
            $options_old['procedure_name'] = $options_new;

            $model->options = \appxq\sdii\utils\SDUtility::array2String($options_old);
            if ($model->update()) {
                $result = [
                    'status' => 'success',
                    'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
                    'data' => $model,
                ];
                return $result;
            } else {
                $result = [
                    'status' => 'error',
                    'message' => SDHtml::getMsgError() . Yii::t('app', 'Data error.'),
                    'data' => $model,
                ];

                return $result;
            }
        }
    }

    function actionVisitProcedureAll() {
        $userid = \Yii::$app->user->id;
        $mid = Yii::$app->request->get('id');
        $schedule_id = Yii::$app->request->get('schedule_id');
        $mid == '' ? $mid = '1' : '';
        $department = Yii::$app->user->identity->profile->department;
        $sitecode = Yii::$app->user->identity->profile->sitecode;

        $schedule_widget_ref = SubjectManagementQuery::getWidgetById($schedule_id);
        $schedule_data = \appxq\sdii\utils\SDUtility::string2Array($schedule_widget_ref['options']);

        $visitProcedure = SubjectManagementQuery::getVisitScheduleByWidget($schedule_id);
        $result = [];
        $i = 0;
        foreach ($visitProcedure as $key => $value) {
            $result[$i]['id'] = $value['id'];
            $result[$i]['visit_name'] = $value['visit_name'];
            $i++;
        }

        return json_encode($result);
    }

    function actionSaveAdditional() {
        $userid = \Yii::$app->user->id;
        $mid = Yii::$app->request->get('id');
        $target = Yii::$app->request->get('target');
        $budget_id = Yii::$app->request->get('budget_id');
        $pro_name = Yii::$app->request->get('pro_name');
        $visit_name = Yii::$app->request->get('visit_name');
        $visit_id = Yii::$app->request->get('visit_id');
        $budget_ezf_id = Yii::$app->request->get('budget_ezf_id');
        $department = Yii::$app->user->identity->profile->department;
        $sitecode = Yii::$app->user->identity->profile->sitecode;
        $id = \appxq\sdii\utils\SDUtility::getMillisecTime();
        $now = date('Y-m-d H:i:s');
        $budgetList = \appxq\sdii\utils\SDUtility::string2Array($budget_id);
        $ezform_budget = EzfQuery::getEzformOne($budget_ezf_id);
        $query = \Yii::$app->db->createCommand()
                ->delete('subject_additional_payment', 'subject_target_id="' . $target . '" AND visit_name=' . $visit_id)
                ->execute();

        foreach ($budgetList as $key => $val) {
            $budgetData = SubjectManagementQuery::GetTableData($ezform_budget, ['id' => $val], 'one');
            $insert = \Yii::$app->db->createCommand()
                    ->insert('subject_additional_payment', [
                'id' => $id,
                'subject_target_id' => $target,
                'budget_id' => $val,
                'visit_name' => $visit_id,
                'procedure_name' => $budgetData['pro_name'],
                'sitecode' => $sitecode,
                'xdepartmentx' => $department,
                'rstat' => '1',
                'user_create' => $userid,
                'create_date' => $now,
                'user_update' => $userid,
                'update_date' => $now,
            ]);

            if ($insert->execute()) {
                return 'success';
            }
        }
    }

    function actionSaveApproved() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $userid = \Yii::$app->user->id;
        $mid = Yii::$app->request->get('id');
        $target = Yii::$app->request->get('target');
        $visit_name = Yii::$app->request->get('visit_name');
        $visit_id = Yii::$app->request->get('visit_id');
        $visit_field = Yii::$app->request->get('visit_field');
        $procedure_name = Yii::$app->request->get('pro_name');
        $department = Yii::$app->user->identity->profile->department;
        $sitecode = Yii::$app->user->identity->profile->sitecode;
        $procedure_widget_id = Yii::$app->request->get('procedure_widget_id');
        $budget_ezf_id = Yii::$app->request->get('budget_ezf_id');
        $id = \appxq\sdii\utils\SDUtility::getMillisecTime();
        $now = date('Y-m-d H:i:s');

        $insert = \Yii::$app->db->createCommand()
                ->insert('subject_visit_approved', [
            'id' => $id,
            'subject_target_id' => $target,
            'visit_name' => $visit_id,
            'procedure_name' => $procedure_name,
            'approved_date' => $now,
            'sitecode' => $sitecode,
            'xdepartmentx' => $department,
            'rstat' => '1',
            'user_create' => $userid,
            'create_date' => $now,
            'user_update' => $userid,
            'update_date' => $now,
        ]);

        if ($insert->execute()) {
            $result = [
                'status' => 'success',
                'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
            ];
            return $result;
        }
    }

    function actionGetVisit() {
        $schedule_id = Yii::$app->request->get('schedule_id');
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $group_id = $parents[0];
                $out = SubjectManagementQuery::getVisitScheduleByInput($schedule_id, $group_id);
                return Json::encode(['output' => $out, 'selected' => '']);
            }
        }
        return Json::encode(['output' => '', 'selected' => '11111']);
    }

    function actionGetVisitGroup() {
        $out = [];

        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $group_id = $parents[0];

                $param1 = null;
                if (!empty($_POST['depdrop_params'])) {
                    $params = $_POST['depdrop_params'];
                    $param1 = $params[0]; // get the value of input-type-1
                    $param2 = $params[1];
                }

                $out = SubjectManagementQuery::getVisitScheduleByInput($param1, $group_id);
                return Json::encode(['output' => $out, 'selected' => $param2]);
            }
        }
        return Json::encode(['output' => '', 'selected' => '11111']);
    }

    public function actionEnabledVisit() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $visit_id = Yii::$app->request->get('visit_id');
        $widget_id = Yii::$app->request->get('widget_id');
        $enable_visit = Yii::$app->request->get('enable_visit');

        $scheduleData = SubjectManagementQuery::getWidgetById($widget_id);
        $scheduleOptions = SDUtility::string2Array($scheduleData['options']);

        $scheduleOptions[$visit_id]['enable_visit'] = $enable_visit;
        $scheduleWidget = \backend\modules\ezmodules\models\EzmoduleWidget::find()->where(['widget_id' => $widget_id])->one();
        $scheduleWidget->options = SDUtility::array2String($scheduleOptions);

        if ($scheduleWidget->update()) {
            $result = [
                'status' => 'success',
                'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
                'data' => $model,
            ];
        } else {
            $result = [
                'status' => 'error',
                'message' => SDHtml::getMsgError() . Yii::t('app', 'Data error..'),
            ];
        }
        return $result;
    }

    public function actionVisitImportExcel() {
        $visit_id = Yii::$app->request->get('visit_id');
        $group_id = Yii::$app->request->get('group_id');
        $widget_id = Yii::$app->request->get('widget_id');
        $module_id = Yii::$app->request->get('module_id');
        $schedule_id = Yii::$app->request->get('schedule_id');
        $options = Yii::$app->request->get('options');

        return $this->renderAjax('visit-schedule-import', [
                    'visit_id' => $visit_id,
                    'group_id' => $group_id,
                    'options' => $options,
                    'widget_id' => $widget_id,
                    'schedule_id' => $schedule_id,
                    'module_id' => $module_id,
        ]);
    }

    public function actionExcelSave() {
        $excel_path = $_FILES['excel_file'];
        $user_id = \Yii::$app->user->identity->id;
        $sitecode = \Yii::$app->user->identity->profile->sitecode;
        $widget_id = Yii::$app->request->post('widget_id');
        $module_id = Yii::$app->request->post('module_id');
        $schedule_id = Yii::$app->request->post('schedule_id');
        $group_id = Yii::$app->request->post('group_id');
        $header = true;
        $filePath = $excel_path['tmp_name'];
        $arrList = array();
        $sum = [];
        if (isset($excel_path['name']) && $excel_path['name'] != '') {
            ini_set('max_execution_time', 0);
            set_time_limit(0);
            ini_set('memory_limit', '256M');

            $excel_file = UploadedFile::getInstanceByName('excel_file');

            if ($excel_file) {
                $arrList = \moonland\phpexcel\Excel::import($excel_file->tempName, [
                            'setFirstRecordAsKeys' => true,
                            'setIndexSheetByName' => true,
                                //'getOnlySheet' => 'sheet1',
                ]);
            }
        }
        $response = '';
        if ($arrList) {
            $nowDate = date('Y-m-d H:i:s');
            $data = [];
            
            foreach ($arrList as $key => $val2) {
                //foreach ($val as $key2 => $val2) {
                    if ($val2['id'] == '11111' || $val2['id'] == '22222') {
                        
                    } else {
                        $val2['id'] = SDUtility::getMillisecTime();
                        $val2['group_name'] = $group_id;
                        $val2['widget_id'] = $widget_id;
                        $val2['schedule_id'] = $schedule_id;
                        $val2['module_id'] = $module_id;
                        $val2['sitecode'] = $sitecode;
                        $val2['rstat'] = '1';
                        $val2['created_at'] = $nowDate;
                        $val2['created_by'] = $user_id . '';
                        $val2['update_at'] = $nowDate;
                        $val2['update_by'] = $user_id . '';
                        $data[] = $val2;
                    }
                //}
            }
            
            $response = SubjectManagementQuery::importVisitScheduleSave($data);
        }

        return "<br/> Import success <code>{$response['success']}</code> record , fail <code>{$response['fail']}</code> record";
    }

    public function actionUpdateSubjectlink() {
        $target = Yii::$app->request->get('target');
        $dataid = Yii::$app->request->get('dataid');
        $visitid = Yii::$app->request->get('visit_id');
        $ezf_id = Yii::$app->request->get('ezf_id');
        $ezform = EzfQuery::getEzformOne($ezf_id);
        if ($ezform) {
            $sqlAlter = " ALTER TABLE `{$ezform['ezf_table']}` ADD COLUMN IF NOT EXISTS `subject_link` VARCHAR(150) DEFAULT NULL, ADD COLUMN IF NOT EXISTS `visit_link` VARCHAR(150) DEFAULT NULL; ";
            Yii::$app->db->createCommand($sqlAlter)->execute();
        }
        Yii::$app->db->createCommand()->update($ezform['ezf_table'], ['subject_link' => $target,'visit_link'=>$visitid], "id=:id", [':id' => $dataid])->execute();
    }
    
    public function actionGetSubjectNumber(){
        $ezf_id = Yii::$app->request->get('ezf_id');
        $dataid = Yii::$app->request->get('dataid');
        $ezform = EzfQuery::getEzformOne($ezf_id);
        
        $sql = " SELECT subject_no FROM {$ezform['ezf_table']} WHERE target='$dataid' AND IFNULL(subject_no,'')<>'' ";
        $query = Yii::$app->db->createCommand($sql)->queryOne();
        
        $result = isset($query['subject_no']) && $query['subject_no'] != ''?$query['subject_no']:'';
        
        return $result;
    }
    
    public function actionGetLastSubjectNumber(){
        $ezf_id = Yii::$app->request->get('ezf_id');
        $ezform = EzfQuery::getEzformOne($ezf_id);
        
        $sql = " SELECT MAX(subject_no) as maxId FROM {$ezform['ezf_table']} ";
        $query = Yii::$app->db->createCommand($sql)->queryOne();
        
        $result = isset($query['maxId']) && $query['maxId'] != ''?" The last subject number is: ".$query['maxId']:'Not found latest subject number.';
        
        return $result;
    }

}
