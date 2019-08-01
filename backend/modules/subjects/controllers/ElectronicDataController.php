<?php

namespace backend\modules\subjects\controllers;

use yii\web\Controller;
use backend\modules\gantt\models\InvProject;
use Yii;
use appxq\sdii\helpers\SDHtml;
use yii\web\Response;
use backend\modules\subjects\classes\SubjectManagementQuery;
use backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\subjects\classes\ReportQuery;
use yii\helpers\Url;

class ElectronicDataController extends Controller {

    public function beforeAction($action) {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionMainElectronic() {
        $schedule_id = Yii::$app->request->get('schedule_id');
        $financial_id = Yii::$app->request->get('financial_id');
        $procedure_id = Yii::$app->request->get('procedure_id');
        $widget_id = Yii::$app->request->get('widget_id');
        $options = Yii::$app->request->get('options');
        $reloadDiv = Yii::$app->request->get('reloadDiv');

        return $this->renderAjax('main-electronic', [
                    'widget_id' => $widget_id,
                    'schedule_id' => $schedule_id,
                    'procedure_id' => $procedure_id,
                    'financial_id' => $financial_id,
                    'options' => $options,
                    'reloadDiv' => $reloadDiv,
        ]);
    }

    public function actionGroupElectronic() {
        $schedule_id = Yii::$app->request->get('schedule_id');
        $financial_id = Yii::$app->request->get('financial_id');
        $procedure_id = Yii::$app->request->get('procedure_id');
        $widget_id = Yii::$app->request->get('widget_id');
        $options = Yii::$app->request->get('options');
        $reloadDiv = Yii::$app->request->get('reloadDiv');

        return $this->renderAjax('group-electronic', [
                    'widget_id' => $widget_id,
                    'schedule_id' => $schedule_id,
                    'procedure_id' => $procedure_id,
                    'financial_id' => $financial_id,
                    'options' => $options,
                    'reloadDiv' => $reloadDiv,
        ]);
    }

    public function actionElectronicData() {
        $schedule_id = Yii::$app->request->get('schedule_id');
        $financial_id = Yii::$app->request->get('financial_id');
        $procedure_id = Yii::$app->request->get('procedure_id');
        $widget_id = Yii::$app->request->get('widget_id');
        $options = Yii::$app->request->get('options');
        $reloadDiv = Yii::$app->request->get('reloadDiv');
        $group_id = Yii::$app->request->get('group_id');
        $view = Yii::$app->request->get('view');
        $form_list = Yii::$app->request->get('form_list');

        return $this->renderAjax($view, [
                    'widget_id' => $widget_id,
                    'schedule_id' => $schedule_id,
                    'procedure_id' => $procedure_id,
                    'financial_id' => $financial_id,
                    'options' => $options,
                    'reloadDiv' => $reloadDiv,
                    'group_id' => $group_id,
        ]);
    }

    public function actionElectronicDataSearch() {
        $schedule_id = Yii::$app->request->get('schedule_id');
        $financial_id = Yii::$app->request->get('financial_id');
        $procedure_id = Yii::$app->request->get('procedure_id');
        $widget_id = Yii::$app->request->get('widget_id');
        $options = Yii::$app->request->get('options');
        $reloadDiv = Yii::$app->request->get('reloadDiv');
        $subjectSearch = Yii::$app->request->get('subject_search');

        $schedule_widget_ref = SubjectManagementQuery::getWidgetById($options['schedule_widget_id']);

        $scheduleOptions = \appxq\sdii\utils\SDUtility::string2Array($schedule_widget_ref['options']);
        if (isset($scheduleOptions['subject_ezf_id']))
            $subjectForm = EzfQuery::getEzformOne($scheduleOptions['subject_ezf_id']);

        $subDisplay = $scheduleOptions['subject_field'];
        $data = SubjectManagementQuery::GetTableData($subjectForm, $subDisplay . " LIKE '%$subjectSearch%'", 'all', '200');

        $html = "<div class='list-group'>
            <li class='list-group-item active' style='text-align: center;'>Subject Number.</li>
            <li class='list-group-item' style='text-align: center;'>
                <input type='text' name='subject-number-search' id='subject-number-search' class='form-control subject-number-search' placeholder='Subject Search...'> 
            </li>";
        foreach ($data as $key => $value):
            $html .= "<a href='#' class='list-group-item subject-item' data-id='{$value['id']}' data-subject='{$value[$subDisplay]}' style='text-align:right;font-size:16px;'><i class='fa fa-address-card '></i> {$value[$subDisplay]}</a>";
        endforeach;
        $html .= "</div>";

        return $html;
    }

    public function actionElectronicDataVisit() {
        $options = Yii::$app->request->get('options');
        $data_id = Yii::$app->request->get('data_id');
        $subject_id = Yii::$app->request->get('subject_id');
        $subject_number = Yii::$app->request->get('subject_number');
        $group_name = Yii::$app->request->get('group_name');
        $group_id = Yii::$app->request->get('group_id');
        return $this->renderAjax('electronic-data-visit', [
                    'subject_number'=>$subject_number,
                    'options' => $options,
                    'data_id' => $data_id,
                    'subject_id' => $subject_id,
                    'group_name' => $group_name,
                    'group_id' => $group_id,
        ]);
    }

    public function actionElectronicDataForm() {
        $options = Yii::$app->request->get('options');
        $visit_name = Yii::$app->request->get('name');
        $visit_id = Yii::$app->request->get('visit_id');
        $visit_field = Yii::$app->request->get('field');
        $data_id = Yii::$app->request->get('data_id');
        $dataid = Yii::$app->request->get('dataid');
        $subject_id = Yii::$app->request->get('subject_id');
        $subject_number = Yii::$app->request->get('subject_number');
        $group_name = Yii::$app->request->get('group_name');
        $group_id = Yii::$app->request->get('group_id');
        $form_list = Yii::$app->request->get('form_list');
        $form_list = base64_decode($form_list);
        $form_list = \appxq\sdii\utils\SDUtility::string2Array($form_list);

        $ezf_id = Yii::$app->request->get('ezf_id');
        if ($dataid && $ezf_id) {
            $ezform = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($ezf_id);
            if ($ezform) {
                $sqlAlter = " ALTER TABLE `{$ezform['ezf_table']}` ADD COLUMN IF NOT EXISTS `subject_link` VARCHAR(150) DEFAULT NULL, ADD COLUMN IF NOT EXISTS `visit_link` VARCHAR(150) DEFAULT NULL; ";
                Yii::$app->db->createCommand($sqlAlter)->execute();
            }
            Yii::$app->db->createCommand()->update($ezform['ezf_table'], ['subject_link' => $data_id, 'visit_link' => $visit_id], "id=:id", [':id' => $dataid])->execute();
        }

        return $this->renderAjax('electronic-data-form', [
                    'options' => $options,
                    'visit_id' => $visit_id,
                    'visit_name' => $visit_name,
                    'data_id' => $data_id,
                    'subject_id' => $subject_id,
                    'subject_number'=>$subject_number,
                    'visit_field' => $visit_field,
                    'group_name' => $group_name,
                    'group_id' => $group_id,
                    'form_list' => $form_list,
        ]);
    }

    public function actionElectronicDashboard() {
        $schedule_id = Yii::$app->request->get('schedule_id');
        $financial_id = Yii::$app->request->get('financial_id');
        $procedure_id = Yii::$app->request->get('procedure_id');
        $widget_id = Yii::$app->request->get('widget_id');
        $group_id = Yii::$app->request->get('group_id');
        $options = Yii::$app->request->get('options');
        $reloadDiv = Yii::$app->request->get('reloadDiv');
        $thisPage = Yii::$app->request->get('thisPage');

        $scheduleOptions = \appxq\sdii\utils\SDUtility::string2Array(SubjectManagementQuery::getWidgetById($options['schedule_widget_id'])['options']);
        if (isset($scheduleOptions['subject_ezf_id']))
            $subjectForm = EzfQuery::getEzformOne($scheduleOptions['subject_ezf_id']);
        if (isset($scheduleOptions['11111']['main_ezf_id']))
            $ezform_main = EzfQuery::getEzformOne($scheduleOptions['11111']['main_ezf_id']);
        if (isset($scheduleOptions['22222']['random_ezf_id']))
            $form_random = EzfQuery::getEzformOne($scheduleOptions['22222']['random_ezf_id']);

        $fieldDisplay = $scheduleOptions['subject_field'];
        $field_visit = isset($scheduleOptions['11111']['main_visit_name']) ? $scheduleOptions['11111']['main_visit_name'] : 'visit_name';
        $visit_name = $scheduleOptions['11111']['form_name'];
        $field_visit2 = $scheduleOptions['22222']['random_visit_name'];
        $visit_name2 = $scheduleOptions['22222']['form_name'];
        if (!isset($thisPage))
            $thisPage = 1;

        $pageLimit = 50;
        $pageStart = $thisPage - 1;
        if ($thisPage > 1)
            $pageStart = ($thisPage - 1) * $pageLimit;

        $query = new \yii\db\Query();
        $count = $query->select('count(*) as amt')
                ->from($ezform_main->ezf_table)
                ->where('group_name=:group_name', [':group_name' => $group_id])
                ->one();

        $where_group = "";
        if ($group_id != '') {
            $where_group = $field_visit . "='22222' AND group_name= '$group_id' ";
        } else {
            $where_group = $field_visit . "='11111' ";
        }

        $data = SubjectManagementQuery::GetScheduleActivity($subjectForm, $ezform_main->ezf_table, $fieldDisplay, $ezform_main->ezf_table . '.' . $where_group, $pageStart, $pageLimit);

        return $this->renderAjax('electronic-dashboard', [
                    'widget_id' => $widget_id,
                    'schedule_id' => $schedule_id,
                    'procedure_id' => $procedure_id,
                    'financial_id' => $financial_id,
                    'options' => $options,
                    'reloadDiv' => $reloadDiv,
                    'data' => \appxq\sdii\utils\SDUtility::array2String($data),
                    'thisPage' => $thisPage,
                    'pageLimit' => $pageLimit,
                    'pageStart' => $pageStart,
                    'subjectForm' => $subjectForm,
                    'ezform_main' => $ezform_main,
                    'group_id' => $group_id,
        ]);
    }

    public function actionDashboardModal() {
        $type = Yii::$app->request->get('type');
        $dataid = Yii::$app->request->get('dataid');
        $target = Yii::$app->request->get('target');
        $visit_id = Yii::$app->request->get('visit_id');
        $form_list = Yii::$app->request->get('form_list');
        $ezf_id = Yii::$app->request->get('ezf_id');
        if ($dataid && $ezf_id) {
            $ezform = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($ezf_id);
            if ($ezform) {
                $sqlAlter = " ALTER TABLE `{$ezform['ezf_table']}` ADD COLUMN IF NOT EXISTS `subject_link` VARCHAR(150) DEFAULT NULL, ADD COLUMN IF NOT EXISTS `visit_link` VARCHAR(150) DEFAULT NULL; ";
                Yii::$app->db->createCommand($sqlAlter)->execute();
            }
            Yii::$app->db->createCommand()->update($ezform['ezf_table'], ['subject_link' => $target, 'visit_link' => $visit_id], "id=:id", [':id' => $dataid])->execute();
        }
        return $this->renderAjax('dashboard-modal', [
                    'type' => $type,
                    'dataid' => $dataid,
                    'form_list' => base64_decode($form_list),
                    'target' => $target,
                    'visit_id' => $visit_id,
        ]);
    }

    public function actionExportSubjectCrfs() {
        if (Yii::$app->getRequest()->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $ezf_id = Yii::$app->request->get('ezf_id');
            $schedule_id = Yii::$app->request->get('schedule_id');
            $group_id = Yii::$app->request->get('group_id');


            //=== Export Data ===
            $url = Url::to(['/subjects/electronic-data/subject-crfs',
                        'ezf_id' => $ezf_id,
                        'schedule_id' => $schedule_id,
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

    public function actionSubjectCrfs() {
        $ezf_id = Yii::$app->request->get('ezf_id');
        $schedule_id = Yii::$app->request->get('schedule_id');
        $group_id = Yii::$app->request->get('group_id');

        $ezform = EzfQuery::getEzformOne($ezf_id);

        $schedule_widget_ref = SubjectManagementQuery::getWidgetById($schedule_id);
        $scheduleOptions = \appxq\sdii\utils\SDUtility::string2Array($schedule_widget_ref['options']);
        $visitSchedule = SubjectManagementQuery::getVisitScheduleByWidget($schedule_id, $group_id);

        $ezformProfile = EzfQuery::getEzformOne($scheduleOptions['subject_ezf_id']);
        $ezformDetail = EzfQuery::getEzformOne($visitSchedule['11111']['ezf_id']);

        $query = new \yii\db\Query();
        $query->select("{$ezformProfile['ezf_table']}.subject_number , {$ezformDetail['ezf_table']}.subject_no , {$ezformDetail['ezf_table']}.visit_name, {$ezform['ezf_table']}.* ")->distinct();
        $query->from($ezformProfile['ezf_table']);
        $query->innerJoin($ezformDetail['ezf_table'], "{$ezformProfile['ezf_table']}.id={$ezformDetail['ezf_table']}.target");
        $query->innerJoin($ezform['ezf_table'], "{$ezformProfile['ezf_table']}.id={$ezform['ezf_table']}.subject_link AND {$ezformDetail['ezf_table']}.visit_name={$ezform['ezf_table']}.visit_link");

        $result = $query->all();
        $result_finish = [];
        if ($result) {
            foreach ($result as $val) {
                $visitFinded = \backend\modules\gantt\classes\GanttQuery::findArraybyFieldName($visitSchedule, $val['visit_name'], 'id');
                $val['visit_name'] = $visitFinded['visit_name'];
                $result_finish[] = $val;
            }
        }

        $fileName = \backend\modules\subjects\classes\ExportexcelFunc::ExportExcelTbdata('Subjects data of crf form-' . $ezform['ezf_name'], 'Subjects data', $result_finish[0], $result_finish);
        $this->redirect(Yii::getAlias('@web/print/').$fileName);
    }

}
