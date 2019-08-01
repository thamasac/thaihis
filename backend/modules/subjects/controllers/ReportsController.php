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
use appxq\sdii\utils\SDUtility;

class ReportsController extends Controller {

    public function beforeAction($action) {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionPaymentForm() {
        $id = Yii::$app->request->get('id');
        $subject_id = Yii::$app->request->get('subject_id');
        $visit_name = Yii::$app->request->get('visit_name');
        $visit_id = Yii::$app->request->get('visit_id');
        $budget_id = Yii::$app->request->get('budget_id');
        $procedure_id = Yii::$app->request->get('procedure_id');
        $schedule_id = Yii::$app->request->get('schedule_id');
        $section_ezf_id = Yii::$app->request->get('section_ezf_id');
        $group_name = Yii::$app->request->get('group_name');
        $group_id = Yii::$app->request->get('group_id');
        $data_id = Yii::$app->request->get('data_id');
        $checkbox = Yii::$app->request->post('checkbox');
        $icome = Yii::$app->request->get('income_lumpsum');
        $type = Yii::$app->request->get('type');
        $sectionProcedure = Yii::$app->request->post('sectionProcedure');
        $total_budget = Yii::$app->request->get('total_budget');
        return $this->renderPartial('_payment_form', [
                    'id' => $id,
                    'subject_id' => $subject_id,
                    'icome' => $icome,
                    'type' => $type,
                    'checkbox' => $checkbox,
                    'visit_name' => $visit_name,
                    'visit_id' => $visit_id,
                    'data_id' => $data_id,
                    'procedure_id' => $procedure_id,
                    'schedule_id' => $schedule_id,
                    'group_name' => $group_name,
                    'group_id' => $group_id,
                    'sectionProcedure' => $sectionProcedure,
                    'section_ezf_id' => $section_ezf_id,
                    'budget_id' => $budget_id,
                    'total_budget' => $total_budget,
        ]);
    }

    public function actionInvoiceSite() {
        $id = Yii::$app->request->get('id');
        return $this->renderPartial('_invoice_site', [
                    'id' => $id,
        ]);
    }

    public function actionFinancialReport() {
        $schedule_id = Yii::$app->request->get('schedule_id');
        $procedure_id = Yii::$app->request->get('procedure_id');
        $widget_id = Yii::$app->request->get('widget_id');
        $options = Yii::$app->request->get('options');
        $reloadDiv = Yii::$app->request->get('reloadDiv');
        $user_create = Yii::$app->request->get('user_create');
        $user_update = Yii::$app->request->get('user_update');
        $startdate = Yii::$app->request->get('start_date');
        $enddate = Yii::$app->request->get('end_date');

        $month = [];
        $month[0] = [\Yii::t('subjects', 'January'), \Yii::t('subjects', 'Jan')];
        $month[1] = [\Yii::t('subjects', 'February'), \Yii::t('subjects', 'Feb')];
        $month[2] = [\Yii::t('subjects', 'March'), \Yii::t('subjects', 'Mar')];
        $month[3] = [\Yii::t('subjects', 'April'), \Yii::t('subjects', 'Apr')];
        $month[4] = [\Yii::t('subjects', 'May'), \Yii::t('subjects', 'May')];
        $month[5] = [\Yii::t('subjects', 'June'), \Yii::t('subjects', 'Jun')];
        $month[6] = [\Yii::t('subjects', 'July'), \Yii::t('subjects', 'Jul')];
        $month[7] = [\Yii::t('subjects', 'August'), \Yii::t('subjects', 'Aug')];
        $month[8] = [\Yii::t('subjects', 'September'), \Yii::t('subjects', 'Sep')];
        $month[9] = [\Yii::t('subjects', 'October'), \Yii::t('subjects', 'Oct')];
        $month[10] = [\Yii::t('subjects', 'November'), \Yii::t('subjects', 'Nov')];
        $month[11] = [\Yii::t('subjects', 'December'), \Yii::t('subjects', 'Dec')];

        $start_date = '';
        $end_date = '';

        if (isset($startdate) && $startdate != '')
            $dateStart = new \DateTime($startdate);

        if (isset($enddate) && $enddate != '')
            $dateEnd = new \DateTime($enddate);

        $start_date = isset($dateStart) ? $dateStart->format('Y-m-d') : null;
        $end_date = isset($dateEnd) ? $dateEnd->format('Y-m-d') : null;

        $ezform_budget = EzfQuery::getEzformOne($options['budget_ezf_id']);
        $ezform_section = EzfQuery::getEzformOne($options['section_ezf_id']);
        $study_form = EzfQuery::getEzformOne($options['study_ezf_id']);

        $ezf_section = SubjectManagementQuery::GetTableData($ezform_section);

        $dataItems = SDUtility::string2Array(EzfQuery::getFieldByName($study_form->ezf_id, 'items')['ezf_field_data'])['items'];

        $whereDate = '';
        $whereDate2 = '';

        if (isset($start_date) && $start_date != null) {
            $whereDate = " AND DATE(approved_date) BETWEEN '$start_date' AND '$end_date' ";
            $whereDate2 = " AND DATE(received_date) BETWEEN '$start_date' AND '$end_date' ";
        }

        // REVENUE =================================================
        $dataRevenue = [];
        $totalRevenue = [];
        foreach ($dataItems as $key => $value) {
            $dataRevenue['items'][$key] = $value;
            foreach ($month as $key2 => $value2) :
                empty($totalRevenue[$key2]) ? $totalRevenue[$key2] = 0 : null;
                $dataStudy = SubjectManagementQuery::getSumBudgetApprovedRevenue($study_form, 'items=' . $key . ' AND MONTH(received_date)="' . ($key2 + 1) . '"' . $whereDate2);

                $totalRevenue[$key2] += $dataStudy['sum_amount'];
                $dataRevenue['amount'][$key][] = $dataStudy['sum_amount'];
            endforeach;
        }

        // EXPENSE ================================================
        $dataExpense = [];
        $totalExpense = [];
        $count = 0;
        foreach ($ezf_section as $key => $value) {
            $dataExpense['items'][$key] = $value['section_name'];
            foreach ($month as $key2 => $value2) :
                empty($totalExpense[$key2]) ? $totalExpense[$key2] = 0 : null;
                $dataAppoved = SubjectManagementQuery::getSumBudgetApprovedExpense($ezform_budget, ' AND section="' . $value['id'] . '"', 'budget', ' AND MONTH(approved_date)="' . ($key2 + 1) . '"' . $whereDate);
                $totalExpense[$key2] += $dataAppoved['sum_budget'];
                $dataExpense['amount'][$key][] = $dataAppoved['sum_budget'];
            endforeach;
            $count++;
        }

        $dataExpense['items'][$count] = "Hospital fees";
        foreach ($month as $key2 => $value2) :
            empty($totalExpense[$key2]) ? $totalExpense[$key2] = 0 : null;
            $dataAppoved = SubjectManagementQuery::getSumBudgetApprovedExpense($ezform_budget, '', 'hos_fee', ' AND MONTH(approved_date)="' . ($key2 + 1) . '"' . $whereDate);
            $totalExpense[$key2] += $dataAppoved['sum_budget'];
            $dataExpense['amount'][$count][] = $dataAppoved['sum_budget'];

        endforeach;
        $count++;

        $dataExpense['items'][$count] = "CRC fees";
        foreach ($month as $key2 => $value2) :
            empty($totalExpense[$key2]) ? $totalExpense[$key2] = 0 : null;
            $dataAppoved = SubjectManagementQuery::getSumBudgetApprovedExpense($ezform_budget, '', 'crc_fee', ' AND MONTH(approved_date)="' . ($key2 + 1) . '"' . $whereDate);
            $totalExpense[$key2] += $dataAppoved['sum_budget'];
            $dataExpense['amount'][$count][] = $dataAppoved['sum_budget'];

        endforeach;
        $count++;

        $dataExpense['items'][$count] = "Profresional fees";
        foreach ($month as $key2 => $value2) :
            empty($totalExpense[$key2]) ? $totalExpense[$key2] = 0 : null;
            $dataAppoved = SubjectManagementQuery::getSumBudgetApprovedExpense($ezform_budget, '', 'profesional_fees', ' AND MONTH(approved_date)="' . ($key2 + 1) . '"' . $whereDate);
            $totalExpense[$key2] += $dataAppoved['sum_budget'];
            $dataExpense['amount'][$count][] = $dataAppoved['sum_budget'];

        endforeach;


        // TOTAL BALANCE ============================================
        $balance[0] = $totalRevenue[0] - $totalExpense[0];
        $totalBalance[0] = $balance[0];

        foreach ($month as $key => $value) :
            $balance[$key] = $totalRevenue[$key] - $totalExpense[$key];
        endforeach;

        foreach ($month as $key => $value) :
            if ($key > 0):
                $totalBalance[$key] = $balance[$key] + $totalBalance[$key - 1];
            endif;
        endforeach;

        $sumBalance = $totalBalance[intval(date('m')) - 1];
        $sumExpense = array_sum($totalExpense);
        $sumRevenue = array_sum($totalRevenue);

        return $this->renderAjax('financial-report', [
                    'widget_id' => $widget_id,
                    'schedule_id' => $schedule_id,
                    'procedure_id' => $procedure_id,
                    'options' => $options,
                    'user_create' => $user_create,
                    'user_update' => $user_update,
                    'reloadDiv' => $reloadDiv,
                    'month' => $month,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'dataRevenue' => $dataRevenue,
                    'totalRevenue' => $totalRevenue,
                    'dataExpense' => $dataExpense,
                    'totalExpense' => $totalExpense,
                    'totalBalance' => $totalBalance,
                    'sumExpense' => $sumExpense,
                    'sumBalance' => $sumBalance,
                    'sumRevenue' => $sumRevenue,
        ]);
    }

    public function actionReport() {
        $schedule_id = Yii::$app->request->get('schedule_id');
        $procedure_id = Yii::$app->request->get('procedure_id');
        $widget_id = Yii::$app->request->get('widget_id');
        $options = Yii::$app->request->get('options');
        $reloadDiv = Yii::$app->request->get('reloadDiv');
        $user_create = Yii::$app->request->get('user_create');
        $user_update = Yii::$app->request->get('user_update');


        return $this->renderAjax('_report', [
                    'widget_id' => $widget_id,
                    'schedule_id' => $schedule_id,
                    'procedure_id' => $procedure_id,
                    'options' => $options,
                    'user_create' => $user_create,
                    'user_update' => $user_update,
                    'reloadDiv' => $reloadDiv,
        ]);
    }

    public function actionPaymentInform() {

        $procedure_id = Yii::$app->request->get('procedure_id');
        $schedule_id = Yii::$app->request->get('schedule_id');
        $budget_id = Yii::$app->request->get('budget_id');
        $section_ezf_id = Yii::$app->request->get('section_ezf_id');
        $visit_name = Yii::$app->request->get('visit_name');
        $visit_id = Yii::$app->request->get('visit_id');
        $group_name = Yii::$app->request->get('group_name');
        $group_id = Yii::$app->request->get('group_id');
        $subject_id = Yii::$app->request->get('subject_id');
        $data_id = Yii::$app->request->get('data_id');
        $icome = Yii::$app->request->get('income_lumpsum');
        $type = Yii::$app->request->get('type');
        $sectionProcedure = Yii::$app->request->get('sectionProcedure');
        $total_budget = Yii::$app->request->get('total_budget');
        $financial_type = Yii::$app->request->get('financial_type');
        $total_invoice = Yii::$app->request->get('total_invoice');
        return $this->renderAjax('_payment-inform', [
                    'procedure_id' => $procedure_id,
                    'schedule_id' => $schedule_id,
                    'visit_name' => $visit_name,
                    'visit_id' => $visit_id,
                    'group_name' => $group_name,
                    'group_id' => $group_id,
                    'subject_id' => $subject_id,
                    'budget_id' => $budget_id,
                    'section_ezf_id' => $section_ezf_id,
                    'data_id' => $data_id,
                    'sectionProcedure' => \appxq\sdii\utils\SDUtility::string2Array(base64_decode($sectionProcedure)),
                    'icome' => $icome,
                    'type' => $type,
                    'total_budget' => $total_budget,
                    'total_invoice' => $total_invoice,
                    'financial_type' => $financial_type,
        ]);
    }

    public function actionInvoiceSubject() {
        $budget_id = Yii::$app->request->get('budget_id');
        $group_name = Yii::$app->request->get('group_name');
        $group_id = Yii::$app->request->get('group_id');
        $profile_ezf_id = Yii::$app->request->get('profile_ezf_id');
        $detail_ezf_id = Yii::$app->request->get('detail_ezf_id');
        $schedule_id = Yii::$app->request->get('schedule_id');
        $procedure_id = Yii::$app->request->get('procedure_id');
        $data_json = base64_decode(Yii::$app->request->get('dat_json'));
        return $this->renderAjax('_invoice-subject', [
                    'group_name' => $group_name,
                    'group_id' => $group_id,
                    'budget_id' => $budget_id,
                    'profile_ezf_id' => $profile_ezf_id,
                    'detail_ezf_id' => $detail_ezf_id,
                    'schedule_id' => $schedule_id,
                    'procedure_id' => $procedure_id,
                    'dat_print' => \appxq\sdii\utils\SDUtility::string2Array($data_json),
        ]);
    }

    public function actionPrintInvoice() {
        $checkbox = Yii::$app->request->post('checkbox');
        $customer_name = Yii::$app->request->post('customer_name');
        $address = Yii::$app->request->post('address');
        $invoice_no = Yii::$app->request->post('invoice_no');
        $received_by = Yii::$app->request->post('received_by');
        $paid_by = Yii::$app->request->post('paid_by');
        $total_invoice = Yii::$app->request->get('total_invoice');

        $subjectList = \appxq\sdii\utils\SDUtility::string2Array(base64_decode(Yii::$app->request->get('subjectList')));
        $subject_id = Yii::$app->request->get('subject_id');

        $header = array();
        $header[0]['align'] = "";
        $header[0]['w'] = "20";
        $header[0]['txt'] = Yii::t('subjects', 'Order');
        $header[0]['name'] = "orderID";
        $header[0]['type'] = "text";

        $header[1]['align'] = "";
        $header[1]['w'] = "80";
        $header[1]['txt'] = Yii::t('subjects', 'Items');
        $header[1]['name'] = "items";
        $header[1]['type'] = "text";

        $header[2]['align'] = "";
        $header[2]['w'] = "45";
        $header[2]['txt'] = Yii::t('subjects', 'Type');
        $header[2]['name'] = "type";
        $header[2]['type'] = "text";

        $header[3]['align'] = "";
        $header[3]['w'] = "45";
        $header[3]['txt'] = Yii::t('subjects', 'Amount');
        $header[3]['name'] = "amount";
        $header[3]['type'] = "number";

        $contenHeader = array();
        $contenHeader['title'] = "Receipt";
        $contenHeader['by'] = "Test";
        $data = array();
        $c = 0;
        $key = 0;

        foreach ($checkbox as $key => $value) {
            $c2 = 0;
            $dataSec = $subjectList[$key];
            $datSec = SubjectManagementQuery::GetTableData('zdata_budget_section', ['id' => $key], 'one');
            $data[$c]['header'][] = $datSec['section_name'];

            foreach ($value as $pro => $proval) {
                foreach ($dataSec as $dat => $valdat) {
                    if ($valdat['id'] == $proval) {
                        $financial_type = SubjectManagementQuery::GetTableData('zdata_financial_type', ['id' => $valdat['financial_type']], 'one');
                        $data[$c]['data'][$c2][] = $valdat['procedure_name'];
                        $data[$c]['data'][$c2][] = $financial_type['financial_type'];
                        $data[$c]['data'][$c2][] = $valdat['budget'];
                        $c2++;
                    }
                }
            }

            $c++;
        }


        return $this->renderPartial('invoice_print', [
                    'data' => $data,
                    'contenHeader' => $contenHeader,
                    'header' => $header,
                    'subject_id' => $subject_id,
                    'customer_name' => $customer_name,
                    'address' => $address,
                    'invoice_no' => $invoice_no,
                    'received_by' => $received_by,
                    'total_invoice'=>$total_invoice,
                    'paid_by' => $paid_by,
        ]);
    }

    public function actionPrintInvoiceRequest() {

        $budget_id = Yii::$app->request->get('budget_id');
        $group_name = Yii::$app->request->get('group_name');
        $group_id = Yii::$app->request->get('group_id');
        $profile_ezf_id = Yii::$app->request->get('profile_ezf_id');
        $detail_ezf_id = Yii::$app->request->get('detail_ezf_id');
        $schedule_id = Yii::$app->request->get('schedule_id');
        $procedure_id = Yii::$app->request->get('procedure_id');
        $data_json = base64_decode(Yii::$app->request->get('dat_json'));
        $customer_name = Yii::$app->request->get('customer_name');
        $address = Yii::$app->request->get('address');
        $invoice_no = Yii::$app->request->get('invoice_no');
        $print_visit = Yii::$app->request->get('print_visit');

        $header = array();
        $header[0]['align'] = "";
        $header[0]['w'] = "20";
        $header[0]['txt'] = Yii::t('subjects', 'Order');
        $header[0]['name'] = "orderID";
        $header[0]['type'] = "text";

        $header[1]['align'] = "";
        $header[1]['w'] = "125";
        $header[1]['txt'] = Yii::t('subjects', 'Order List');
        $header[1]['name'] = "items";
        $header[1]['type'] = "text";

        $header[2]['align'] = "";
        $header[2]['w'] = "45";
        $header[2]['txt'] = Yii::t('subjects', 'Amount');
        $header[2]['name'] = "amount";
        $header[2]['type'] = "number";

        $contenHeader = array();
        $contenHeader['title'] = "Invoice";
        $contenHeader['by'] = "Test";
        $data = array();
        $c = 0;
        $sumBudget = 0;
        $visitSchedule = SubjectManagementQuery::getVisitScheduleByWidget($schedule_id, $group_id);
        $data_array = \appxq\sdii\utils\SDUtility::string2Array($data_json);

        $ezform_budget = EzfQuery::getEzformOne($budget_id);
        $profile_form = EzfQuery::getEzformOne($profile_ezf_id);
        $detail_form = EzfQuery::getEzformOne($detail_ezf_id);

        foreach ($data_array as $key => $val) {
            $sub_num = SubjectManagementQuery::GetTableData($profile_form, ['id' => $val['target']], 'one');
            $sumBudget += $val['budget'];
            if (isset($print_visit) || $print_visit != '') {
                if ($print_visit == $key) {
                    $data[$c]['data'][$c2][] = $sub_num['subject_number'] . ' ' . $visitSchedule[$val['visit_name']]['visit_name'] . " Visit Date " . SubjectManagementQuery::convertDate($val['visit_date']);
                    $data[$c]['data'][$c2][] = $val['budget'];
                    $c++;
                }
            } else {
                $data[$c]['data'][$c2][] = $sub_num['subject_number'] . ' ' . $visitSchedule[$val['visit_name']]['visit_name'] . " Visit Date " . SubjectManagementQuery::convertDate($val['visit_date']);
                $data[$c]['data'][$c2][] = $val['budget'];
                $c++;
            }
        }


        return $this->renderPartial('invoice_request_print', [
                    'data' => $data,
                    'contenHeader' => $contenHeader,
                    'header' => $header,
                    'sumBudget' => $sumBudget,
                    'customer_name' => $customer_name,
                    'address' => $address,
                    'invoice_no' => $invoice_no,
        ]);
    }

    public function actionHighchartReport() {
        $title = Yii::$app->request->get('title');
        $type = Yii::$app->request->get('type');
        $series = Yii::$app->request->get('series');
        $renderDiv = Yii::$app->request->get('renderDiv');
        $categories = Yii::$app->request->get('categories');
        $graphheight = Yii::$app->request->get('graphheight');
        $dataChart = Yii::$app->request->get('dataChart');
        
        $view = "";
        if($type=='pie'){
            $view = "cicle-chart-report";
        }else{
            $view = "line-chart-report";
        }

        return $this->renderAjax($view, [
                    'title' => $title,
                    'dataChart' => $dataChart,
                    'type'=>$type,
                    'series'=>$series,
                    'renderDiv'=>$renderDiv,
                    'categories'=>$categories,
                    'graphheight'=>$graphheight,
        ]);
    }

}
