<?php

namespace backend\modules\queue\controllers;

use appxq\sdii\utils\SDdate;
use appxq\sdii\utils\VarDumper;
use backend\modules\ezforms2\classes\EzfUiFunc;
use backend\modules\thaihis\classes\OrderFunc;
use backend\modules\thaihis\classes\ThaiHisFunc;
use backend\modules\thaihis\classes\ThaiHisQuery;
use common\lib\tcpdf\SDPDF;
use yii\db\Exception;
use yii\db\Expression;
use yii\helpers\Html;
use yii\web\Controller;
use Yii;
use backend\modules\ezforms2\classes\EzfFunc;
use yii\db\Query;
use backend\modules\ezforms2\classes\EzfQuery;
use yii\helpers\ArrayHelper;
use backend\modules\queue\classes\QueueFunc;
use backend\modules\ezforms2\models\EzformFields;

/**
 * Default controller for the `queue` module
 */
class DefaultController extends Controller
{

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {

        $ezf_main_id = Yii::$app->request->get('ezf_main_id', '');
        $ezf_ref_id = EzfFunc::stringDecode2Array(Yii::$app->request->get('ezf_ref_id', ''));
        $data_columns = EzfFunc::stringDecode2Array(Yii::$app->request->get('data_columns', ''));
        $fields_search_one = EzfFunc::stringDecode2Array(Yii::$app->request->get('fields_search_one', ''));
        $fields_search_multi = EzfFunc::stringDecode2Array(Yii::$app->request->get('fields_search_multi', ''));
        $param = EzfFunc::stringDecode2Array(Yii::$app->request->get('param', []));
        $params_value = EzfFunc::stringDecode2Array(Yii::$app->request->get('params_value', []));
        $condition = EzfFunc::stringDecode2Array(Yii::$app->request->get('condition', []));
        $group_by = Yii::$app->request->get('group_by', '');
        $order_by = EzfFunc::stringDecode2Array(Yii::$app->request->get('order_by', []));
        $dept_field = Yii::$app->request->get('dept_field', '');
        $doc_field = Yii::$app->request->get('doc_field', '');
        $split_permission = Yii::$app->request->get('split_permission', false);
        $bdate_field = Yii::$app->request->get('bdate_field', '');
        $pic_field = Yii::$app->request->get('pic_field', '');
        $custom_label = EzfFunc::stringDecode2Array(Yii::$app->request->get('custom_label', ''));
        $template_content = Yii::$app->request->get('template_content', '');
        $que_type = Yii::$app->request->get('que_type', '1');
        $target = Yii::$app->request->get('target', '');
        $current_url = Yii::$app->request->get('current_url', '');
        $action = Yii::$app->request->get('action', '1');
        $reloadDiv = Yii::$app->request->get('reloadDiv', '');
        $title = Yii::$app->request->get('title', '');
        $icon = Yii::$app->request->get('icon', '');
        $searchBoxOne = Yii::$app->request->get('searchBoxOne', '');
        $search_field = Yii::$app->request->get('search_field', '');
        $check_search = Yii::$app->request->get('check_search', '');
        $element_id = Yii::$app->request->get('element_id', 'element_id');
        $position = EzfFunc::stringDecode2Array(Yii::$app->request->get('position', ''));
        $widget_que_type = Yii::$app->request->get('widget_que_type', 'que');
        $clearDiv = Yii::$app->request->get('clearDiv', 'nullDiv'); //nullDiv กัน javascript error
        $radio_check = Yii::$app->request->get('radio_check', false);
        !isset($vdate_field['field']) ? $vdate_field = ['field' => ''] : null;
        !isset($status_field['field']) ? $status_field = ['field' => ''] : null;
        $report = Yii::$app->request->get('report', '0');
        $btn_report = Yii::$app->request->get('btn_report', false);
//        $page = Yii::$app->request->get('page','');
        $arrEzf = [];
        if (isset($ezf_ref_id) && !empty($ezf_ref_id)) {
            foreach ($ezf_ref_id as $vEzf) {
                if (isset($vEzf['value']) && is_array($vEzf['value']) && !empty($vEzf['value'])) {
                    foreach ($vEzf['value'] as $v) {
                        $arrEzf[] = $v;
                    }
                } else {
                    $arrEzf[] = $vEzf;
                }
            }
        }
//echo $que_type;
        $arrEzf[] = $ezf_main_id;
        $arrField = $data_columns;
        //add param to select
        if (!empty($param)) {
            foreach ($param as $vParam) {
                if (!in_array($vParam['value'], $arrField)) {
                    $arrField[] = $vParam['value'];
                }
            }
        }

        $dept_field != '' && !in_array($dept_field, $arrField) ? $arrField[] = $dept_field : null;
        $doc_field != '' && !in_array($doc_field, $arrField) ? $arrField[] = $doc_field : null;
        $bdate_field != '' && !in_array($bdate_field, $arrField) ? $arrField[] = $bdate_field : null;
        $pic_field != '' && !in_array($pic_field, $arrField) ? $arrField[] = $pic_field : null;

        try {
            $modelFields = QueueFunc::getFieldDetailById($arrField, "all");
            $txtField = null;
            if ($modelFields) {
                foreach ($modelFields as $value) {
                    if ($value['ezf_field_name'] == 'id') {
                        $txtField .= $value['ezf_table'] . '.' . $value['ezf_field_name'] . " AS " . $value['ezf_field_id'] . "_" . $value['ezf_field_name'] . ",";
                    } else {
                        $txtField .= $value['ezf_table'] . '.' . $value['ezf_field_name'] . ",";
                    }
                    if ($doc_field == $value['ezf_field_id']) {
                        $doc_field = $value['ezf_field_name'];
                    }
                    if ($dept_field == $value['ezf_field_id']) {
                        $dept_field = $value['ezf_field_name'];
                    }
                    if ($bdate_field == $value['ezf_field_id']) {
                        $bdate_field = $value['ezf_field_name'];
                        $data_columns[] = $value['ezf_field_id'];
                    }
                    if ($pic_field == $value['ezf_field_id']) {
                        $pic_field = $value['ezf_field_name'];
                        $data_columns[] = $value['ezf_field_id'];
                    }

                    //add field to params
                    foreach ($param as $key => $vParam) {
                        if ($vParam['value'] == $value['ezf_field_id']) {
                            if ($value['ezf_field_name'] == 'id') {
                                $param[$key]['model_field'] = $value['ezf_field_id'] . '_' . $value['ezf_field_name'];
                            } else {
                                $param[$key]['model_field'] = $value['ezf_field_name'];
                            }
                        }
                    }
                }
            }

            $checkField = [];
            $txtSearchOne = 'ค้นหาด้วย ';
            $whereSearchOne = '';
            $modelFieldsSearchOne = null;
            if (!empty($fields_search_one)) {
                $modelFieldsSearchOne = EzformFields::find()
                    ->where(['ezf_id' => $arrEzf])
                    ->andWhere(['ezf_field_id' => $fields_search_one])
                    ->groupBy('ezf_field_name')->all();
                if ($modelFieldsSearchOne) {
                    foreach ($modelFieldsSearchOne as $vModelSearchOne) {
                        $txtSearchOne .= $vModelSearchOne['ezf_field_label'] . ' ';
                        $dataEzform = EzfQuery::getEzformById($vModelSearchOne['ezf_id']);
                        if ($vModelSearchOne['ezf_field_name'] != '')
                            $checkField[] = $vModelSearchOne['ezf_field_name'];
                        $whereSearchOne == '' ? $whereSearchOne = 'CONCAT(' . $dataEzform['ezf_table'] . '.' . $vModelSearchOne['ezf_field_name'] : $whereSearchOne .= ',' . $dataEzform['ezf_table'] . '.' . $vModelSearchOne['ezf_field_name'];
                    }
                }
            }

            $modelFieldsSearchMulti = null;
            if (!empty($fields_search_multi)) {
                $modelFieldsSearchMulti = EzformFields::find()
                    ->where(['ezf_id' => $arrEzf])
                    ->andWhere(['ezf_field_id' => $fields_search_multi])
                    ->groupBy('ezf_field_name')->all();
            }

            if ($widget_que_type == 'queue') {
                $query = QueueFunc::getQueryJoin($ezf_main_id, $ezf_ref_id, $txtField, '', $group_by);
            }

            if ($query) {
                if ($whereSearchOne != '' && $searchBoxOne != '' && $searchBoxOne != null) {
                    $whereSearchOne .= ') LIKE :q';
                    $query->andWhere($whereSearchOne, [':q' => "%$searchBoxOne%"]);
                }

                $order_status = '';
                $order_date = '';

                if (is_array($search_field) && !empty($search_field)) {
                    foreach ($search_field as $kSearch => $vSearch) {
                        if ($vSearch) {
                            $checkField[] = $kSearch;
                            $dataFieldSearchMulti = (new Query())->select('(' . (new Query())->select('ezf_table')->from('ezform as ezf')->where('ezf.ezf_id = ezff.ezf_id')->createCommand()->rawSql . ') as ezf_table')
                                ->from('ezform_fields as ezff')
                                ->where(['ezf_id' => $arrEzf])
                                ->andWhere(['ezf_field_id' => $fields_search_multi, 'ezf_field_name' => $kSearch])
                                ->groupBy('ezf_field_name')->one();
                            $dataFieldSearchMulti ? $query->andWhere(['LIKE', $dataFieldSearchMulti['ezf_table'] . '.' . $kSearch, $vSearch]) : $query->andWhere(['LIKE', $kSearch, $vSearch]);
                            if (($report == 1 && $btn_report) && $kSearch == 'order_tran_status') {
                                $order_status = $vSearch;
                            }
                            if (($report == 1 && $btn_report) && ($kSearch == 'visit_date' || $kSearch == 'order_tran_date')) {
                                $order_date = $vSearch;
                            }
                        }
                    }
                }

                if ($condition) {
                    foreach ($condition as $vCondition) {
                        $modelConditionField = QueueFunc::getFieldDetailById($vCondition['field']);
                        if ($modelConditionField && !in_array($modelConditionField['ezf_field_name'], $checkField)) {
                            $conFild = '';
                            if ($vCondition['value'] == '{department}') {
                                $vCondition['value'] = Yii::$app->user->identity->profile->department;
                            } else if ($vCondition['value'] == '{permission}') {
                                $vCondition['value'] = Yii::$app->user->can('doctor');
                            } else if ($vCondition['value'] == '{user_id}') {
                                $vCondition['value'] = Yii::$app->user->id;
                            } else if ($vCondition['value'] == '{sitecode}') {
                                $vCondition['value'] = Yii::$app->user->identity->profile->sitecode;
                            } else if ($vCondition['value'] == '{today}') {
                                $vCondition['value'] = date('Y-m-d');
                                $conFild = 'DATE(' . $modelConditionField['ezf_table'] . '.' . $modelConditionField['ezf_field_name'] . ')';
                            }
                            if (($report == 1 && $btn_report) && isset($modelConditionField['ezf_field_name']) && $modelConditionField['ezf_field_name'] == 'order_tran_status') {
                                $order_status = $vCondition['value'];
                            }
                            if (($report == 1 && $btn_report) && isset($modelConditionField['ezf_field_name'])
                                && ($modelConditionField['ezf_field_name'] == 'visit_date' || $modelConditionField['ezf_field_name'] == 'order_tran_date')) {
                                $order_date = $vCondition['value'];
                            }
//                            if ($vCondition['value'] == 'NOW()') {
//                                if (isset($vCondition['condition']) && $vCondition['condition'] == 'and') {
//                                    $query->andWhere([$vCondition['operator'], 'DATE(' . $modelConditionField['ezf_table'] . '.' . $modelConditionField['ezf_field_name'] . ')', new Expression('CURDATE()')]);
//                                } else if (isset($vCondition['condition']) && $vCondition['condition'] == 'or') {
//                                    $query->orWhere([$vCondition['operator'], 'DATE(' . $modelConditionField['ezf_table'] . '.' . $modelConditionField['ezf_field_name'] . ')', new Expression('CURDATE()')]);
//                                } else {
//                                    $query->andWhere([$vCondition['operator'], 'DATE(' . $modelConditionField['ezf_table'] . '.' . $modelConditionField['ezf_field_name'] . ')', new Expression('CURDATE()')]);
//                                }
//                            } else {
                            if (isset($vCondition['condition']) && $vCondition['condition'] == 'and') {
                                $query->andWhere([$vCondition['operator'], $conFild != '' ? $conFild : $modelConditionField['ezf_table'] . '.' . $modelConditionField['ezf_field_name'], $vCondition['value']]);
                            } else if (isset($vCondition['condition']) && $vCondition['condition'] == 'or') {
                                $query->orWhere([$vCondition['operator'], $conFild != '' ? $conFild : $modelConditionField['ezf_table'] . '.' . $modelConditionField['ezf_field_name'], $vCondition['value']]);
                            } else {
                                $query->andWhere([$vCondition['operator'], $conFild != '' ? $conFild : $modelConditionField['ezf_table'] . '.' . $modelConditionField['ezf_field_name'], $vCondition['value']]);
                            }
                        }
//                        }
                    }
                }
                if ($que_type == '1' && $dept_field) {
                    if (Yii::$app->user->can('doctor') && $doc_field != '') {
                        $query->andWhere([$doc_field => Yii::$app->user->id]);
                    } else {
                        $query->andWhere(['LIKE', $dept_field, Yii::$app->user->identity->profile->department]);
                        if ($split_permission && $doc_field != '') {
                            $query->andWhere("IFNULL({$doc_field},'') = ''");
                        }
                    }
                }
//
                if (isset($order_by) && is_array($order_by) && !empty($order_by)) {
                    if (isset($order_by['field']) && isset($order_by['type']) && $order_by['field'] != '' && $order_by['type'] != '') {
                        if ((int)$order_by['field']) {
                            $modelFieldOrder = EzformFields::findOne(['ezf_field_id' => $order_by['field']]);
                            if ($modelFieldOrder) {
                                $query->orderBy([$modelFieldOrder['ezf_field_name'] => (int)$order_by['type']]);
                            }

                        } else {
                            $query->orderBy([$order_by['field'] => (int)$order_by['type']]);
                        }

                    }
                }


                if ($report == 1 && $btn_report) {
                    self::reportQue($query, $modelFields, $title, $order_status, $order_date);
                }

                $count_queue = $query->count();
//            \appxq\sdii\utils\VarDumper::dump($split_permission);
//                \appxq\sdii\utils\VarDumper::dump($query->createCommand()->rawSql);

                $dataProviderQue = new \yii\data\SqlDataProvider([
                    'sql' => $query->createCommand()->rawSql,
//                    'pagination' => [
//                        'pageSize' => 20,
//                        'page' => $page
//                    ]
                ]);
                return $this->renderAjax('index', [
                    'ezf_main_id' => $ezf_main_id,
                    'dataProviderQue' => $dataProviderQue,
                    'status_field' => $status_field,
                    'dept_field' => $dept_field,
                    'bdate_field' => $bdate_field,
                    'pic_field' => $pic_field,
                    'template_content' => $template_content,
                    'que_type' => $que_type,
                    'target' => $target,
                    'current_url' => $current_url,
                    'data_columns' => $data_columns,
                    'reloadDiv' => $reloadDiv,
                    'modelFields' => $modelFields,
                    'title' => $title,
                    'icon' => $icon,
                    'param' => $param,
                    'custom_label' => $custom_label,
                    'whereSearchOne' => $whereSearchOne,
                    'txtSearchOne' => $txtSearchOne,
                    'searchBoxOne' => $searchBoxOne,
                    'modelFieldsSearchMulti' => $modelFieldsSearchMulti,
                    'fields_search_multi' => $fields_search_multi,
                    'search_field' => $search_field,
                    'action' => $action,
                    'position' => $position,
                    'check_search' => $check_search,
                    'element_id' => $element_id, 'params_value' => $params_value,
                    'clearDiv' => $clearDiv, 'radio_check' => $radio_check,
                    'count_queue' => $count_queue,
                    'btn_report' => $btn_report
                ]);
            } else {
                return \yii\helpers\Html::tag('div', 'เกิดข้อผิดพลาด', ['class' => 'alert alert-danger']);
            }
        } catch (Exception $ex) {
            EzfFunc::addErrorLog($ex);
            return \yii\helpers\Html::tag('div', 'เกิดข้อผิดพลาด', ['class' => 'alert alert-danger']);
        }
    }


    private function reportQue($query, $modelFields = [], $title, $order_status = null, $order_date = null)
    {

        $dataDept = ThaiHisQuery::getDepartmentFull(Yii::$app->user->id);
        $dept = isset($dataDept['order_type_code']) ? $dataDept['order_type_code'] : null;
        $dept_name = isset($dataDept['unit_name']) ? $dataDept['unit_name'] : '';
        $dataQue = $query->all();


        $pdf = new SDPDF('L', PDF_UNIT, 'A4', true, 'UTF-8', false);

        $pdf->SetCreator('KK');
        $pdf->SetAuthor('UD CANCER');
        $pdf->SetTitle($title);
        $pdf->SetSubject($title);
        $pdf->SetKeywords($title);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
//        $pdf->SetMargins(15, 10, 10, true);
//        $pdf->SetHeaderMargin(10);
//        $pdf->SetFooterMargin(10);
        $pdf->SetMargins(5, 5, 0, true);
//$pdf->SetHeaderMargin(10);
        $pdf->SetFooterMargin(5);

        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, 10);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->SetFont($pdf->fontName, '', $pdf->fontSize);
        $pdf->fontSize = 16;
        $pdf->AddFont('thsarabunpsk', '', 'thsarabunpsk.php');
        $pdf->AddFont('thsarabunpsk', 'B', 'thsarabunpskb.php');
        $pdf->AddPage();
        $pdf->SetFont('thsarabunpsk', 'B', $pdf->fontSize);
//        $pdf->Cell(0, 0, 'โรงพยาบาลมะเร็งอุดรธานี (UDONTHANI CANCER HOSPITAL)', 0, 1, 'C', 0, '', 0, false, 'T', 'M');


//VarDumper::dump($order_status);

//
//        $htmlHeader = <<<HTML
//<table border="1px">
//    <thead>
//        <tr>
//            <th style="width: 5%;text-align: center">
//                ลำดับ
//            </th>
//            <th style="width: 10%;text-align: center">
//                HN
//            </th>
//            <th style="width: 25%;text-align: center">
//                ชื่อ-สกุล
//            </th>
//            <th style="width: 5%;text-align: center">
//                อายุ
//            </th>
//            <th style="width: 55%;text-align: center">
//                Order
//            </th>
//        </tr>
//    </thead>
//
//HTML;
//        $html = '';
//        if ($dataQue && is_array($dataQue) && !empty($dataQue)) {
//            $html .= <<<HTML
//    <tbody>
//HTML;
        $i = 1;
        if ($dataQue) {
            foreach ($dataQue as $kQue => $vQue) {
                $vQue['id'] = $kQue;
                if (is_array($modelFields) && !empty($modelFields) && $dataQue) {
                    foreach ($modelFields as $kField => $vField) {
                        $dataInput = null;
//                $ezf_input=null;
                        if (isset(Yii::$app->session['ezf_input'])) {
//                    $ezf_input = Yii::$app->session['ezf_input'];
                            $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($vField['ezf_field_type'], Yii::$app->session['ezf_input']);
                        }
                        $txtVal = EzfUiFunc::getValueEzform($dataInput, $vField, $vQue);
                        if ($vField['ezf_field_type'] == 66) {
                            $txtVal = str_replace('<p>', '', $txtVal);
                            $txtVal = str_replace('</p>', '', $txtVal);
                            $txtVal = str_replace('<div>', '', $txtVal);
                            $txtVal = str_replace('</div>', '', $txtVal);
                        }

                        $dataQue[$vField['ezf_field_name']] = $txtVal;
                    }

                }
                if ($i <= 1) {
                    $status_name = isset($vQue['order_tran_status']) ? $vQue['order_tran_status'] : '';
                    $pdf->Cell(0, 0, 'รายชื่อผูรับบริการ ' . $dept_name . ' ' . $status_name, 0, 1, 'C', 0, '', 0, false, 'T', 'M');
                    $pdf->SetFont('thsarabunpsk', '', 13);
                    $pdf->Cell(0, 0, '' . SDdate::mysql2phpThDate($order_date) . '', 0, 1, 'C', 0, '', 0, false, 'T', 'M');
                    $pdf->Ln(1);

                    $pdf->Cell(15, 0, 'ลำดับที่', 'TLB', 0, 'C', 0, '', 0, false, 'T', 'M');
                    $pdf->Cell(20, 0, 'HN', 'TB', 0, 'C', 0, '', 0, false, 'T', 'M');
                    $pdf->Cell(40, 0, 'ชื่อนามสกุล', 'TB', 0, 'C', 0, '', 0, false, 'T', 'M');
                    $pdf->Cell(210, 0, 'รายการ', 'TRB', 1, 'C', 0, '', 0, false, 'T', 'M');


                }
                $pt_nh = isset($vQue['pt_hn']) ? $vQue['pt_hn'] : '';
                $pt_firstname = isset($vQue['pt_firstname']) ? $vQue['pt_firstname'] : '';
                $pt_lastname = isset($vQue['pt_lastname']) ? $vQue['pt_lastname'] : '';
                $full_name = $pt_firstname . ' ' . $pt_lastname;
                $pt_bdate = isset($vQue['pt_bdate']) && $vQue['pt_bdate'] != '' ? \backend\modules\queue\classes\QueueFunc::calAge($vQue['pt_bdate'], true, false, false, true) : '';
                $txt_order = '';
                $dataOrder = OrderFunc::getOrderTranReport(isset($vQue['idMain']) ? $vQue['idMain'] : '', 'OPD', $order_date, $dept, $order_status);

                if ($dataOrder && is_array($dataOrder) && !empty($dataOrder)) {
                    $cHeader = '';
                    foreach ($dataOrder as $vOrder) {
//                        $txt_group_name = '';
//                        $br = '';
//                        $txt_order_name = $vOrder['order_name'];
//                        $txt_order_qty = $vOrder['order_qty'];
//                        if ($cHeader != $vOrder['order_group_name']) {
//                            $txt_group_name = $vOrder['order_group_name'];
//                            $txt_order .= <<<HTML
//                            <strong>&nbsp;$txt_group_name  </strong> :
//HTML;
//
//                        }
//                        $txt_order .= <<<HTML
//                                <span>
//                                &nbsp;&nbsp;$txt_order_name
//                                </span>,
//HTML;
                        $txt_order .= $vOrder['order_name'] . ',';
//                        $cHeader = $vOrder['order_group_name'];


                    }
                }
                $txt_order = substr($txt_order, 0, strlen($txt_order) - 1);
                $pdf->Cell(15, 0, $i, 'TLRB', 0, 'C', 0, '', 0, false, 'T', 'M');
                $pdf->Cell(20, 0, $pt_nh, 'TRB', 0, 'C', 0, '', 0, false, 'T', 'M');
                $pdf->Cell(40, 0, $full_name, 'TRB', 0, 'L', 0, '', 0, false, 'T', 'M');
                $pdf->Cell(210, 0, $txt_order, 'TRB', 1, 'L', 0, '', 0, false, 'T', 'M');
//                $html .= <<<HTML
//                            <tr>
//                                <td style="width: 5%;text-align: center">
//                                    $i
//                                </td>
//                                <td style="width: 10%;text-align: center">
//                                    $pt_nh
//                                </td>
//                                <td style="width: 25%;text-align: center">
//                                    $full_name
//                                </td>
//                                <td style="width: 5%;text-align: center">
//                                    $pt_bdate
//                                </td>
//                                <td style="width: 55%">
//                                     $txt_order
//                                </td>
//                            </tr>
//
//HTML;
                $i++;
            }
        } else {
            $pdf->Cell(0, 0, 'รายชื่อผูรับบริการ ' . $dept_name, 0, 1, 'C', 0, '', 0, false, 'T', 'M');
            $pdf->SetFont('thsarabunpsk', '', 13);
            $pdf->Cell(0, 0, '' . SDdate::mysql2phpThDate($order_date) . '', 0, 1, 'C', 0, '', 0, false, 'T', 'M');
            $pdf->Ln(1);
            $pdf->Cell(15, 0, 'ลำดับที่', 'TLB', 0, 'C', 0, '', 0, false, 'T', 'M');
            $pdf->Cell(20, 0, 'HN', 'TB', 0, 'C', 0, '', 0, false, 'T', 'M');
            $pdf->Cell(40, 0, 'ชื่อนามสกุล', 'TB', 0, 'C', 0, '', 0, false, 'T', 'M');
            $pdf->Cell(210, 0, 'รายการ', 'TRB', 1, 'C', 0, '', 0, false, 'T', 'M');
        }
//            $html .= <<<HTML
//    </tbody>
//HTML;
//        }
//        $html .= <<<HTML
//</table>
//HTML;
//
////        VarDumper::dump($txt_order);
//        $pdf->writeHTML($htmlHeader . $html, true, false, false, false, '');
        $pdf->Output('report.pdf', 'I');
        Yii::$app->end();
    }

    public
    function actionGetFormRef()
    {
        $ezf_id = Yii::$app->request->post('ezf_id', '0');
        $name = Yii::$app->request->post('name', '');
        $name_data = Yii::$app->request->post('name_data', '');
        $value_ref = Yii::$app->request->post('value_ref', '0');
        $multiple = Yii::$app->request->post('multiple', false);
        $id = Yii::$app->request->post('id');
        $model1 = new \yii\db\Query();
        $model1->select('ezf.ezf_id as `id`,ezf.ezf_name as `name`')
            ->from('ezform_fields ezff')
            ->innerJoin('ezform ezf', 'ezf.ezf_id=ezff.ezf_id')
            ->where(['ezff.ref_ezf_id' => $ezf_id])
            ->andWhere('ezff.ezf_field_type=79 OR ezff.ezf_field_type=80');

        $model2 = new \yii\db\Query();
        $model2->select('ezf.ezf_id as `id`,ezf.ezf_name as `name`')
            ->from('ezform_fields ezff')
            ->innerJoin('ezform ezf', 'ezf.ezf_id=ezff.ref_ezf_id')
            ->where(['ezff.ezf_id' => $ezf_id])
            ->andWhere('ezff.ezf_field_type=79 OR ezff.ezf_field_type=80');
        $result = $model1->union($model2->createCommand()->rawSql);

        $dataForm = $result->all();

        return $this->renderAjax('_ref_form', [
            'ezf_id' => $ezf_id,
            'dataForm' => ArrayHelper::map($dataForm, 'id', 'name'),
            'multiple' => $multiple,
            'value' => $value_ref,
            'name' => $name,
            'name_data' => $name_data,
            'id' => $id,
        ]);
    }

    public
    function actionGetFieldsForms()
    {
        $ezf_id = Yii::$app->request->post('ezf_id', []);
        $main_ezf_id = Yii::$app->request->post('main_ezf_id', 0);
        $name = Yii::$app->request->post('name', 0);
        $value = Yii::$app->request->post('value', 0);
        $ezf_all_field = Yii::$app->request->post('ezf_all_field', '');
        $ezf_field_type = Yii::$app->request->post('ezf_field_type', []);
        $multiple = Yii::$app->request->post('multiple', 0);
        $date_system = Yii::$app->request->post('date_system', 0);
        $id = Yii::$app->request->post('id');
        if (isset($ezf_id) && is_array($ezf_id)) {
            if ($main_ezf_id != '0') {
                $ezf_id[] = $main_ezf_id;
            }
        } else {
            $ezf_id = [];
            $ezf_id[] = $main_ezf_id;
        }
        $dataFields = (new Query())->select(['ezf_field_id as id', 'CONCAT(ezf_field_name," (",ezf_field_label,")") as name', 'ezf_id'])
            ->from('ezform_fields')
            ->where(['ezf_id' => $ezf_id]);
        !empty($ezf_field_type) ? $dataFields->andWhere(['ezf_field_type' => $ezf_field_type]) : null;
        $ezf_all_field != '' ? $dataFields->andWhere('ezf_field_type <> 0 OR  ezf_field_name = \'id\'') : $dataFields->groupBy('ezf_field_id');
        $dataFields = $dataFields->andWhere('ezf_field_name is not null')->all();
        $dataForm = [];

        if ($multiple) {
            $dataForm = ArrayHelper::map($dataFields, 'id', 'name');
        } else {
            foreach ($dataFields as $vField) {
                $dataEzf = EzfQuery::getEzformById($vField['ezf_id']);
                if ($dataEzf && $vField['name'] != '') {
                    if ($date_system && !isset($dataForm[$dataEzf['ezf_name']])) {
                        $dataForm[$dataEzf['ezf_name']][$dataEzf['ezf_table'] . '.create_date'] = 'create_date (Create Date)';
                        $dataForm[$dataEzf['ezf_name']][$dataEzf['ezf_table'] . '.update_date'] = 'update_date (Update Date)';
                    }
                    $dataForm[$dataEzf['ezf_name']][$vField['id']] = $vField['name'];
                }
            }
        }
//        \appxq\sdii\utils\VarDumper::dump($dataForm);
        return $this->renderAjax('_ref_form', [
            'ezf_id' => $ezf_id,
//                    'dataForm' => ArrayHelper::map($dataFields, 'id', 'name','ezf_id'),
            'dataForm' => $dataForm,
            'multiple' => $multiple,
            'value' => $value,
            'name' => $name,
            'name_data' => '',
            'id' => $id,
        ]);
    }

    public
    function actionGetConstant()
    {
        $ezf_id = Yii::$app->request->post('ezf_id', '');
        $name = Yii::$app->request->post('name', 'constant-print');
        $ezf_all_field = Yii::$app->request->post('ezf_all_field', '');
        $ezf_field_type = Yii::$app->request->post('ezf_field_type', []);
        $dataFields = (new Query())->select(['ezf_field_name as id', 'CONCAT(ezf_field_name," (",ezf_field_label,")") as name', 'ezf_id'])
            ->from('ezform_fields')
            ->where(['ezf_id' => $ezf_id]);
        !empty($ezf_field_type) ? $dataFields->andWhere(['ezf_field_type' => $ezf_field_type]) : null;
        $ezf_all_field ? $dataFields->andWhere('ezf_field_type <> 0') : $dataFields->groupBy('ezf_field_name');
        $dataFields = $dataFields->andWhere('ezf_field_name is not null')->all();
        $dataForm = [];

        foreach ($dataFields as $vField) {
            $dataEzf = EzfQuery::getEzformById($vField['ezf_id']);
            if ($dataEzf && $vField['name'] != '') {
                $dataForm[$dataEzf['ezf_name']][$vField['id']] = strip_tags($vField['name']);
            }
        }
//        \appxq\sdii\utils\VarDumper::dump($dorpdown);
        return $this->renderAjax('_constant', [
//                    'dataForm' => ArrayHelper::map($dataFields, 'id', 'name','ezf_id'),
            'dataForm' => $dataForm,
            'name' => $name,
        ]);
    }

    public
    function actionGetFormParam()
    {
        $ezf_id = Yii::$app->request->post('ezf_id', 0);
        $main_ezf_id = Yii::$app->request->post('main_ezf_id', 0);
        $param_name = Yii::$app->request->post('param_name', '');
        $param_value = Yii::$app->request->post('param_value', '');
        $value = Yii::$app->request->post('value', 0);
        $arrEzf = [];
        if ($ezf_id && is_array($ezf_id) && !empty($ezf_id)) {
            $arrEzf = $ezf_id;
        }
        $arrEzf[] = $main_ezf_id;

        $dataFields = QueueFunc::getFieldFormById($arrEzf);
        $dataForm = [];
        foreach ($dataFields as $vField) {
            $dataForm[$vField['ezf_name']][$vField['id']] = $vField['name'];
        }

        return $this->renderAjax('_param_form', [
            'dataForm' => $dataForm,
            'value' => $value,
            'param_value' => $param_value,
            'param_name' => $param_name,
        ]);
    }

    public
    function actionGetFormCondition()
    {
        $ezf_id = Yii::$app->request->post('ezf_id', 0);
        $main_ezf_id = Yii::$app->request->post('main_ezf_id', 0);
        $condition_value = Yii::$app->request->post('condition_value', '');
        $condition_field = Yii::$app->request->post('condition_field', '');
        $condition_con = Yii::$app->request->post('condition_con', '');
        $type = Yii::$app->request->post('type', []);
        $value = Yii::$app->request->post('value', 0);
        $arrEzf = [];
        if ($ezf_id && is_array($ezf_id) && !empty($ezf_id)) {
            $arrEzf = $ezf_id;
        }
        $arrEzf[] = $main_ezf_id;
        $dataFields = QueueFunc::getFieldFormById($arrEzf, $type);
        $dataForm = [];
        foreach ($dataFields as $vField) {
            $dataForm[$vField['ezf_name']][$vField['id']] = $vField['name'];
        }

        return $this->renderAjax('_condition_form', [
            'dataForm' => $dataForm,
            'value' => $value,
            'condition_field' => $condition_field,
            'condition_value' => $condition_value,
            'condition_con' => $condition_con,
            'type' => $type
        ]);
    }

    public
    function actionTestPdf()
    {
        $pdf = new \common\lib\tcpdf\SDPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);
        $pdf->SetCreator('AppXQ');
        $pdf->SetAuthor('iencoded@gmail.com');
        $pdf->SetSubject('Original');
        $pdf->SetKeywords('AppXQ, SDII, PDF, report, medical,');

// remove default header/footer
        $pdf->setPrintHeader(FALSE);
        $pdf->setPrintFooter(FALSE);
// set margins
        $pdf->SetMargins(15, 10, 15, TRUE);
        $pdf->SetHeaderMargin(10);
        $pdf->SetFooterMargin(10);
// set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, 15);

// set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set font
        $pdf->SetFont($pdf->fontName, '', $pdf->fontSize);
        $pdf->fontSize = 18;

// add a page
        $pdf->AddPage();
        $pdf->writeHTML('test');
        $pdf->Output();
    }

    public
    function actionQueueCashier()
    {
        $ezf_main_id = Yii::$app->request->get('ezf_main_id', '');
        $ezf_ref_id = EzfFunc::stringDecode2Array(Yii::$app->request->get('ezf_ref_id', ''));
        $data_columns = EzfFunc::stringDecode2Array(Yii::$app->request->get('data_columns', ''));
        $fields_search_one = EzfFunc::stringDecode2Array(Yii::$app->request->get('fields_search_one', ''));
        $bdate_field = Yii::$app->request->get('bdate_field', '');
        $pic_field = Yii::$app->request->get('pic_field', '');
        $template_content = Yii::$app->request->get('template_content', '');
        $que_type = Yii::$app->request->get('que_type', '1');
        $target = Yii::$app->request->get('target', '');
        $current_url = Yii::$app->request->get('current_url', '');
        $action = Yii::$app->request->get('action', '1');
        $reloadDiv = Yii::$app->request->get('reloadDiv', '');
        $title = Yii::$app->request->get('title', '');
        $icon = Yii::$app->request->get('icon', '');
        $searchBoxOne = Yii::$app->request->get('searchBoxOne', '');
        $search_field = Yii::$app->request->get('search_field', '');
        $element_id = Yii::$app->request->get('element_id', 'element_id');
        $position = EzfFunc::stringDecode2Array(Yii::$app->request->get('position', ''));
        $clearDiv = Yii::$app->request->get('clearDiv', 'nullDiv'); //nullDiv กัน javascript error
        $param = EzfFunc::stringDecode2Array(Yii::$app->request->get('param', []));
        $params_value = EzfFunc::stringDecode2Array(Yii::$app->request->get('params_value', []));
        !isset($vdate_field['field']) ? $vdate_field = ['field' => ''] : null;
        !isset($status_field['field']) ? $status_field = ['field' => ''] : null;

        $whereSearchOne = '';
        $arrEzf[] = $ezf_main_id;
        $arrField = $data_columns;
        $arrField[] = $bdate_field;
        $arrField[] = $pic_field;
        //add param to select
        if ($param) {
            foreach ($param as $vParam) {
                if (!in_array($vParam['value'], $arrField)) {
                    $arrField[] = $vParam['value'];
                }
            }
        }
        //add params for cashier
        $param[] = [
            'name' => 'target',
            'value' => 'targetMain',
            'model_field' => 'targetMain'
        ];
        $param[] = [
            'name' => 'cashier_status',
            'value' => 'cashier_status',
            'model_field' => 'cashier_status'
        ];
        $param[] = [
            'param_active' => '1',
            'name' => 'receipt_id',
            'value' => 'receipt_id',
            'model_field' => 'receipt_id'
        ];
        //New Select Query & add field to params
        $modelFields = QueueFunc::getFieldDetailById($arrField, "all");
        $txtField = null;
        if ($modelFields) {
            foreach ($modelFields as $value) {
                if ($value['ezf_field_name'] == 'id') {
                    $txtField .= $value['ezf_table'] . '.' . $value['ezf_field_name'] . " AS " . $value['ezf_field_id'] . "_" . $value['ezf_field_name'] . ",";
                } else {
                    $txtField .= $value['ezf_table'] . '.' . $value['ezf_field_name'] . ",";
                }

                if ($bdate_field == $value['ezf_field_id']) {
                    $bdate_field = $value['ezf_field_name'];
                    $data_columns[] = $value['ezf_field_id'];
                }
                if ($pic_field == $value['ezf_field_id']) {
                    $pic_field = $value['ezf_field_name'];
                    $data_columns[] = $value['ezf_field_id'];
                }
                //add field to params
                foreach ($param as $key => $vParam) {
                    if ($vParam['value'] == $value['ezf_field_id']) {
                        if ($value['ezf_field_name'] == 'id') {
                            $param[$key]['model_field'] = $value['ezf_field_id'] . '_' . $value['ezf_field_name'];
                        } else {
                            $param[$key]['model_field'] = $value['ezf_field_name'];
                        }
                        break;
                    }
                }
            }
        }
        //Search multiple //visit_date,cashier_status
        $fields_search_multi = ['1503589502016825300', '1514183418009305500'];
        $modelFieldsSearchMulti = \backend\modules\ezforms2\models\EzformFields::find()
            ->where(['ezf_field_id' => $fields_search_multi])->all();

        $txtField .= 'zdata_visit.id as idMain,zdata_visit.target as targetMain,zdata_visit.ptid as ptidMain,order_tran_cashier_status AS cashier_status,zdata_visit.visit_admit_an';
        $txtField = explode(",", $txtField);
        array_push($txtField, "IFNULL(order_tran_cashier_id,'') AS receipt_id");
        $searchDate = isset($search_field['visit_date']) && $search_field['visit_date'] ? $search_field['visit_date'] : date('Y-m-d');
        $searchCashierStatus = isset($search_field['order_tran_cashier_status']) && $search_field['order_tran_cashier_status'] ? $search_field['order_tran_cashier_status'] : '1';
        $queryOrder = (new Query())
            ->select($txtField)
            ->from('zdata_visit')
            ->leftJoin('zdata_patientright', 'zdata_patientright.right_visit_id=zdata_visit.id')
            ->innerJoin('zdata_patientprofile', 'zdata_patientprofile.id=zdata_visit.ptid')
            ->innerJoin('zdata_order_header', 'zdata_order_header.order_visit_id=zdata_visit.id')
            ->innerJoin('zdata_order_tran', 'zdata_order_tran.order_header_id=zdata_order_header.id')
            ->where(['DATE(visit_date)' => $searchDate])
//                ->where(['BETWEEN', 'visit_date', $searchDate . ' 00:00:00', $searchDate . ' 23:59:59'])
            ->andWhere(['order_tran_cashier_status' => $searchCashierStatus])
            ->andWhere('`zdata_order_tran`.`rstat` not in(0,3) AND zdata_visit.rstat not in(0,3)');

        $queryPis = (new Query())
            ->select($txtField)
            ->from('zdata_visit')
            ->leftJoin('zdata_patientright', 'zdata_patientright.right_visit_id=zdata_visit.id')
            ->innerJoin('zdata_patientprofile', 'zdata_patientprofile.id=zdata_visit.ptid')
            ->innerJoin('zdata_pis_order', 'zdata_pis_order.order_visit_id=zdata_visit.id')
            ->innerJoin('zdata_pis_order_tran', 'zdata_pis_order_tran.order_id=zdata_pis_order.id')
            ->where(['DATE(visit_date)' => $searchDate])
//            ->where(['BETWEEN', 'visit_date', $searchDate . ' 00:00:00', $searchDate . ' 23:59:59'])
            ->andWhere(['order_tran_cashier_status' => $searchCashierStatus])
            ->andWhere('`zdata_pis_order_tran`.`rstat` not in(0,3) AND zdata_visit.rstat not in(0,3)');

        $query = (new \yii\db\Query())
            ->from(['zdata_visit' => $queryOrder->union($queryPis)]);

        $checkField = [];
        $txtSearchOne = 'ค้นหาด้วย ';
        $whereSearchOne = '';
        $modelFieldsSearchOne = null;
        if ($fields_search_one) {
            $modelFieldsSearchOne = QueueFunc::getFieldDetailById($fields_search_one, "all");
            if ($modelFieldsSearchOne) {
                foreach ($modelFieldsSearchOne as $vModelSearchOne) {
                    $txtSearchOne .= $vModelSearchOne['ezf_field_label'] . ' ';
                    if ($vModelSearchOne['ezf_field_name'] != '')
                        $checkField[] = $vModelSearchOne['ezf_field_name'];
                    $whereSearchOne == '' ? $whereSearchOne = 'CONCAT(' . $vModelSearchOne['ezf_field_name'] : $whereSearchOne .= ',' . $vModelSearchOne['ezf_field_name'];
                }
            }
        }

        if ($whereSearchOne != '' && $searchBoxOne != '' && $searchBoxOne != null) {
            $whereSearchOne .= ') LIKE :q';
            $query->andWhere($whereSearchOne, [':q' => "%$searchBoxOne%"]);
        }

//            \appxq\sdii\utils\VarDumper::dump($pic_field);
//        \appxq\sdii\utils\VarDumper::dump($query->createCommand()->rawSql);
        $dataCount = $query->count();
        $dataProviderQue = new \yii\data\SqlDataProvider([
            'sql' => $query->createCommand()->rawSql
        ]);

        return $this->renderAjax('index_cashier', [
            'ezf_main_id' => $ezf_main_id,
            'dataProviderQue' => $dataProviderQue,
            'status_field' => $status_field,
            'bdate_field' => $bdate_field,
            'pic_field' => $pic_field,
            'template_content' => $template_content,
            'que_type' => $que_type,
            'target' => $target,
            'current_url' => $current_url,
            'data_columns' => $data_columns,
            'reloadDiv' => $reloadDiv,
            'modelFields' => $modelFields,
            'title' => $title,
            'icon' => $icon,
            'param' => $param,
            'whereSearchOne' => $whereSearchOne,
            'txtSearchOne' => $txtSearchOne,
            'searchBoxOne' => $searchBoxOne,
            'modelFieldsSearchMulti' => $modelFieldsSearchMulti,
            'fields_search_multi' => $fields_search_multi,
            'search_field' => $search_field,
            'action' => $action,
            'position' => $position,
            'element_id' => $element_id,
            'params_value' => $params_value,
            'clearDiv' => $clearDiv, 'custom_label' => '',
            'dataCount' => $dataCount
        ]);
    }

    public
    function actionAddConstant()
    {
        $input = Yii::$app->request->get('input', '');
        return $this->renderAjax('_add-constant', ['input' => $input]);
    }

}
