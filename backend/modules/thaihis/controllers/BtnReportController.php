<?php

/**
 * Created by PhpStorm.
 * User: AR9
 * Date: 9/12/2561
 * Time: 22:02
 */

namespace backend\modules\thaihis\controllers;

use appxq\sdii\utils\SDdate;
use appxq\sdii\utils\SDUtility;
use appxq\sdii\utils\VarDumper;
use backend\modules\ezforms2\classes\EzfFunc;
use backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\ezforms2\classes\EzfUiFunc;
use backend\modules\managedata\models\EzformFields;
use backend\modules\queue\classes\QueueFunc;
use backend\modules\thaihis\classes\ThaiHisQuery;
use common\lib\tcpdf\SDPDF;
use yii\db\Query;
use yii\web\Controller;
use Yii;

class BtnReportController extends Controller {

    public function actionBtnReport() {

        $ezf_main_id = Yii::$app->request->get('ezf_main_id', '');
        $ezf_ref_id = EzfFunc::stringDecode2Array(Yii::$app->request->get('ezf_ref_id', ''));
        $condition = EzfFunc::stringDecode2Array(Yii::$app->request->get('condition', ''));
        $group_by = Yii::$app->request->get('group_by', '');
        $target = Yii::$app->request->get('target', '');
        $reloadDiv = Yii::$app->request->get('reloadDiv', 'brn-report');
        $btn_text = Yii::$app->request->get('btn_text', 'brn-report');
        $btn_color = Yii::$app->request->get('btn_color', 'btn-default');
        $btn_style = Yii::$app->request->get('btn_style', ' ');
        $btn_icon = Yii::$app->request->get('btn_icon', ' fa-print');
        $visitid = Yii::$app->request->get('visitid', '');
        $status = Yii::$app->request->get('status', '0');
        $match_field = EzfFunc::stringDecode2Array(Yii::$app->request->get('match_field', ''));
        $header_report = Yii::$app->request->get('header_report', 'EMR');

        if ($status == '1') {

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
            $arrEzf[] = $ezf_main_id;
            $arrFields = [];
            if (is_array($match_field) && !empty($match_field)) {
                foreach ($match_field as $kField => $valField) {
                    $arrFields[] = $valField;
                }
            }

            $cEzf = [];
            $modelFields = QueueFunc::getFieldDetailById($arrFields, "all");
            $txtField = null;
            $orderBy = [];
            if ($modelFields) {
                foreach ($modelFields as $value) {

                    if ($value['ezf_field_type'] != 76) {
                        if (!in_array($value['ezf_table'], $cEzf)) {
                            $orderBy[$value['ezf_table'] . '.update_date'] = SORT_DESC;
                            $cEzf[] = $value['ezf_table'];
                        }
                        if ($value['ezf_id'] == $ezf_main_id) {
                            $txtField .= $value['ezf_table'] . '.id,';
                        }
                        if ($value['ezf_field_name'] == 'id') {
                            $txtField .= $value['ezf_table'] . '.' . $value['ezf_field_name'] . " AS " . 'report_' . $value['ezf_field_id'] . ",";
                        } else {
                            $txtField .= $value['ezf_table'] . '.' . $value['ezf_field_name'] . " AS " . 'report_' . $value['ezf_field_id'] . ",";
                        }
                    } else {
                        $fieldRef = (new Query())
                                        ->select(['ez.ezf_id', 'ezf_name', 'ezf_table', 'ef.*'])
                                        ->from('ezform_fields ef')
                                        ->innerJoin('ezform ez', "ez.ezf_id=ef.ezf_id AND (ez.ezf_version=ef.ezf_version OR ef.ezf_version='all')")
                                        ->where(['ef.ezf_field_ref' => $value['ezf_field_id']])->all();
//                        VarDumper::dump($fieldRef);
                        if ($fieldRef) {
//                            $modelFields[] = $fieldRef;
                            foreach ($fieldRef as $vFieldRef) {
                                $txtField .= $vFieldRef['ezf_table'] . '.' . $vFieldRef['ezf_field_name'] . ",";
                            }
                        }
                    }
                }
            }
//            VarDumper::dump($modelFields);
//            $txtField ? $txtField = substr($txtField, 0, strlen($txtField) - 1) : null;
            $txtField .= 'visit_date';
//            $dataQuery = QueueFunc::getQueryJoin($ezf_main_id, $ezf_ref_id, $txtField);
            $dept_id = Yii::$app->user->identity->profile->department;
            $dataQuery = (new Query())->select($txtField)
                    ->from('zdata_patientprofile')
                    ->leftJoin('zdata_visit', 'zdata_visit.target = zdata_patientprofile.id AND zdata_visit.rstat not in (0,3)')
                    ->leftJoin('(SELECT * FROM zdata_tk WHERE zdata_tk.rstat not in (0,3) AND zdata_tk.xdepartmentx = :xdpart ORDER BY zdata_tk.create_date DESC) AS zdata_tk', 'zdata_tk.target = zdata_visit.id')
                    ->leftJoin('(SELECT * FROM zdata_pe WHERE zdata_pe.rstat not in (0,3) AND zdata_pe.xdepartmentx = :xdpart ORDER BY zdata_pe.create_date DESC) AS zdata_pe', 'zdata_pe.target = zdata_visit.id')
                    ->leftJoin('(SELECT * FROM zdata_dt WHERE zdata_dt.rstat not in (0,3)) AS zdata_dt', 'zdata_dt.target = zdata_visit.id ')
                    ->leftJoin('(SELECT * FROM zdata_vs WHERE zdata_vs.rstat not in (0,3)) AS zdata_vs', 'zdata_vs.target = zdata_visit.id')
                    ->leftJoin('(SELECT * FROM zdata_bmi WHERE zdata_bmi.rstat not in (0,3)) AS zdata_bmi', 'zdata_bmi.target = zdata_visit.id')
                    ->leftJoin('(SELECT * FROM zdata_treatment WHERE zdata_treatment.rstat not in (0,3)) AS zdata_treatment', 'zdata_treatment.target = zdata_treatment.id')
                    ->params([':xdpart' => $dept_id]);

            if (isset($condition) && is_array($condition) && !empty($condition)) {
                if ($condition) {
                    foreach ($condition as $vCondition) {
                        $modelConditionField = QueueFunc::getFieldDetailById($vCondition['field']);
                        if ($modelConditionField) {
                            $conFild = '';
                            if ($vCondition['value'] == '{department}') {
                                $vCondition['value'] = Yii::$app->user->identity->profile->department;
                            } else if ($vCondition['value'] == '{permission}') {
                                $vCondition['value'] = Yii::$app->user->can('doctor');
                            } else if ($vCondition['value'] == '{user_id}') {
                                $vCondition['value'] = Yii::$app->user->id;
                            } else if ($vCondition['value'] == '{today}') {
                                $vCondition['value'] = new Expression('CURDATE()');
                                $conFild = 'DATE(' . $modelConditionField['ezf_table'] . '.' . $modelConditionField['ezf_field_name'] . ')';
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
                                $dataQuery->andWhere([$vCondition['operator'], $conFild != '' ? $conFild : $modelConditionField['ezf_table'] . '.' . $modelConditionField['ezf_field_name'], $vCondition['value']]);
                            } else if (isset($vCondition['condition']) && $vCondition['condition'] == 'or') {
                                $dataQuery->orWhere([$vCondition['operator'], $conFild != '' ? $conFild : $modelConditionField['ezf_table'] . '.' . $modelConditionField['ezf_field_name'], $vCondition['value']]);
                            } else {
                                $dataQuery->andWhere([$vCondition['operator'], $conFild != '' ? $conFild : $modelConditionField['ezf_table'] . '.' . $modelConditionField['ezf_field_name'], $vCondition['value']]);
                            }
                        }
//                        }
                    }
                }
            }
//            VarDumper::dump($dataQuery->createCommand()->rawSql);
//            $dataQuery = (new Query())->select('zpp.id')
//                ->from('zdata_patientprofile AS zpp')
//                ->leftJoin('zdata_prefix AS zp','vpp.pt_prefix_id=zp.prefix_id')
//                ->leftJoin('zdata_visit AS zv','zv.ptid=zpp.id')
//                ->leftJoin('zdata_vs AS vs','vs.vs_visit_id=zv.id')
//                ->leftJoin('zdata_bmi AS bmi','bmi.bmi_visit_id=zv.id')
//                ->leftJoin('zdata_tk AS tk','tk.tk_visit_id=zv.id')
//                ->leftJoin('zdata_pe AS pe','pe.pe_visit_id=zv.id')
//                ->leftJoin('zdata_dt AS dt','dt.di_visit_id=zv.id')
//                ->leftJoin('zdata_treatment AS zt','zt.treat_visit_id=zv.id')
//                ->where(['zv.id' => $visitid,'zpp.id'=>$target]);
            $dataReport = $dataQuery->orderBy($orderBy)->one();

            $dataText = '';
            if (!isset(Yii::$app->session['ezf_input'])) {
                Yii::$app->session['ezf_input'] = EzfQuery::getInputv2All();
            }
            if ($dataReport) {
                foreach ($modelFields as $k => $val) {
                    $dataInput = null;
//                $ezf_input=null;
                    if (isset(Yii::$app->session['ezf_input'])) {
//                    $ezf_input = Yii::$app->session['ezf_input'];
                        $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($val['ezf_field_type'], Yii::$app->session['ezf_input']);
                        if ($val['ezf_field_type'] != 76) {
                            $val['ezf_field_name'] = 'report_' . $val['ezf_field_id'];
                        }
                    }
//                    VarDumper::dump($val);
                    $txtVal = EzfUiFunc::getValueEzform($dataInput, $val, $dataReport);

                    if ($val['ezf_field_type'] == 66) {
//                        $txtVal = str_replace("<p>", "", $txtVal);
                        $txtVal = str_replace("</p>", "<br>", $txtVal);
//                        $txtVal = str_replace("<div>", "", $txtVal);
                        $txtVal = str_replace("</div>", "<br>", $txtVal);
                        $search = array('@<script[^>]*?>.*?</script>@si', // Strip out javascript
                            '@<style[^>]*?>.*?</style>@siU', // Strip style tags properly
                            '@<p[\/\!]*?[^<>]*?>@si', // Strip out HTML tags
                            '@<p[\/\!]*?[^<>]*?>@si',
                            '@<![\s\S]*?--[ \t
 ]*>@'        // Strip multi-line comments including CDATA
                        );
                        $txtVal = preg_replace($search, '', $txtVal);
                    }


                    $dataReport['report_' . $val['ezf_field_id']] = $txtVal;
                }
            }
            $pdf = new SDPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);
            $pdf->SetCreator('KK');
            $pdf->SetAuthor('UD CANCER');
            $pdf->SetTitle($header_report);
            $pdf->SetSubject($header_report);
            $pdf->SetKeywords($header_report);
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
            $pdf->SetMargins(15, 10, 10, true);
            $pdf->SetHeaderMargin(10);
            $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

            // set auto page breaks
            $pdf->SetAutoPageBreak(TRUE, 3);

            // set image scale factor
            $dataid = \backend\modules\patient\Module::$dataidForm['hos_config'];
            $ezf_table = \backend\modules\patient\Module::$formTableName['hos_config'];
            $dataHosConfig = \backend\modules\ezforms2\classes\EzfUiFunc::loadTbData($ezf_table, $dataid);

            if ($dataHosConfig['logo_02']) {
                $path = Yii::getAlias('@storageUrl/ezform/fileinput') . '/' . $dataHosConfig['logo_02'];
            } else {
                $path = Yii::getAlias('@storageUrl/images') . '/nouser.png';
            }

            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
            $pdf->SetFont($pdf->fontName, '', $pdf->fontSize);
            $pdf->fontSize = 18;
            $pdf->AddFont('thsarabunpsk', '', 'thsarabunpsk.php');
            $pdf->AddFont('thsarabunpsk', 'B', 'thsarabunpskb.php');
            $pdf->AddPage();
            $pdf->SetFont('thsarabunpsk', 'B', 20);
            if (\backend\modules\thaihis\classes\ThaiHisFunc::isUrlExist($path)) {
                $pdf->Image($path, 96, 10, 20, 20, 'JPG');
            }
            $pdf->Ln(24);
            $pdf->Cell(185, 0, 'โรงพยาบาลมะเร็งอุดรธานี (UDONTHANI CANCER HOSPITAL)', 0, 1, 'C', 0, '', 0, false, 'T', 'M');
            $pdf->Cell(0, 0, $header_report, 0, 1, 'C', 0, '', 0, false, 'T', 'M');

            $pdf->Ln(10);
//            $pdf->set
//            VarDumper::dump($match_field);
            $pdf->SetFont('thsarabunpsk', '', 16);
            $prefix_id = isset($match_field['pt_prefix']) ? $match_field['pt_prefix'] : '';
            $txtPrefix = isset($dataReport['report_' . $prefix_id]) ? $dataReport['report_' . $prefix_id] : '';
            $firstname = isset($match_field['pt_firstname']) ? $match_field['pt_firstname'] : '';
            $txtFirstname = isset($dataReport['report_' . $firstname]) ? $dataReport['report_' . $firstname] : '';
            $lastname = isset($match_field['pt_lastname']) ? $match_field['pt_lastname'] : '';
            $txtLastname = isset($dataReport['report_' . $lastname]) ? $dataReport['report_' . $lastname] : '';
            $pdf->SetFont('thsarabunpsk', 'B', 16);
            $pdf->Cell(75, 0, 'ชื่อ-สกุล : ' . $txtPrefix . ' ' . $txtFirstname . ' ' . $txtLastname, 0, 0, 'L', 0, '', 0, false, 'T', 'M');


            $pt_age = isset($match_field['pt_age']) ? $match_field['pt_age'] : '';
            $txtAge = isset($dataReport['report_' . $pt_age]) ? $dataReport['report_' . $pt_age] : '';
//            VarDumper::dump($txtAge);
            $txtAge = $txtAge != '' ? QueueFunc::calAge($txtAge, true, false, false) : '';
            $pdf->Cell(25, 0, 'อายุ : ' . $txtAge, 0, 0, 'L', 0, '', 0, false, 'T', 'M');

            $pt_hn = isset($match_field['pt_hn']) ? $match_field['pt_hn'] : '';
            $txtHN = isset($dataReport['report_' . $pt_hn]) ? $dataReport['report_' . $pt_hn] : '';
            $pdf->Cell(35, 0, 'HN : ' . $txtHN, 0, 0, 'L', 0, '', 0, false, 'T', 'M');

            $txtVsDate = isset($dataReport['visit_date']) && $dataReport['visit_date'] != '' ? SDdate::mysql2phpThDateSmall($dataReport['visit_date'], false) : '';
            $pdf->Cell(40, 0, 'วันที่ : ' . $txtVsDate, 0, 0, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->Ln(10);
            $pdf->SetFont('thsarabunpsk', '', 16);
            $pdf->writeHTML('<hr>', false, false, true, false, '');
//            $pdf->Ln(2);
            $pdf->SetFont('thsarabunpsk', '', 16);

            //Vital start
            $vs_bp_squeeze = isset($match_field['vs_bp_squeeze']) ? $match_field['vs_bp_squeeze'] : '';
            $txtVsBpSqueeze = isset($dataReport['report_' . $vs_bp_squeeze]) && $dataReport['report_' . $vs_bp_squeeze] != '' ? $dataReport['report_' . $vs_bp_squeeze] : '';

            $vs_bp_loosen = isset($match_field['vs_bp_loosen']) ? $match_field['vs_bp_loosen'] : '';
            $txtVsBpLoosen = isset($dataReport['report_' . $vs_bp_loosen]) && $dataReport['report_' . $vs_bp_loosen] != '' ? $dataReport['report_' . $vs_bp_loosen] : '';

            $txtVsBpSqueeze != '' || $txtVsBpLoosen != '' ? $txtVsSl = "<span>  <b>BP</b> : {$txtVsBpSqueeze} / {$txtVsBpLoosen}</span> mmHg" : $txtVsSl = '';

            $vs_pulse = isset($match_field['vs_pulse']) ? $match_field['vs_pulse'] : '';
            $txtVsPulse = isset($dataReport['report_' . $vs_pulse]) && $dataReport['report_' . $vs_pulse] != '' ? " <span>  <b>P</b> : {$dataReport['report_' . $vs_pulse]}</span> / min" : '';

            $vs_respiratory = isset($match_field['vs_respiratory']) ? $match_field['vs_respiratory'] : '';
            $txtVsRespiratory = isset($dataReport['report_' . $vs_respiratory]) && $dataReport['report_' . $vs_respiratory] != '' ? " <span>  <b>R</b> : {$dataReport['report_' . $vs_respiratory]}</span> / min" : '';

            $vs_temperature = isset($match_field['vs_temperature']) ? $match_field['vs_temperature'] : '';
            $txtVsTemperature = isset($dataReport['report_' . $vs_temperature]) && $dataReport['report_' . $vs_temperature] != '' ? " <span>  <b>T</b> : {$dataReport['report_' . $vs_temperature]}</span> <sup>0</sup>C" : '';

            $txtVs = $txtVsSl . ' ' . $txtVsPulse . ' ' . $txtVsRespiratory . ' ' . $txtVsTemperature;
            //Vital end
            //BMI start
            $bmi_bw = isset($match_field['bmi_bw']) ? $match_field['bmi_bw'] : '';
            $txtBmiBw = isset($dataReport['report_' . $bmi_bw]) && $dataReport['report_' . $bmi_bw] != '' ? " <span>  <b>BW</b> : {$dataReport['report_' . $bmi_bw]}</span> kg" : '';

            $bmi_ht = isset($match_field['bmi_ht']) ? $match_field['bmi_ht'] : '';
            $txtBmiHt = isset($dataReport['report_' . $bmi_ht]) && $dataReport['report_' . $bmi_ht] != '' ? " <span>  <b>HT</b> : {$dataReport['report_' . $bmi_ht]}</span> cms" : '';

            $bmi_bmi = isset($match_field['bmi_bmi']) ? $match_field['bmi_bmi'] : '';
            $txtBmiBmi = isset($dataReport['report_' . $bmi_bmi]) && $dataReport['report_' . $bmi_bmi] != '' ? " <span>  <b>BMI</b> : {$dataReport['report_' . $bmi_bmi]}</span> " : '';

            $bmi_bsa = isset($match_field['bmi_bsa']) ? $match_field['bmi_bsa'] : '';
            $txtBmiBsa = isset($dataReport['report_' . $bmi_bsa]) && $dataReport['report_' . $bmi_bsa] != '' ? " <span>  <b>BSA</b> : {$dataReport['report_' . $bmi_bsa]}</span> cms" : '';

            $bmi_waistline = isset($match_field['bmi_waistline']) ? $match_field['bmi_waistline'] : '';
            $txtBmiWaistline = isset($dataReport['report_' . $bmi_waistline]) ? $dataReport['report_' . $bmi_waistline] : '';

            $txtBMI = $txtBmiBw . ' ' . $txtBmiHt . ' ' . $txtBmiBmi . ' ' . $txtBmiBsa;
            //BMI end
            //Treat start
            $treat_consult = isset($match_field['treat_consult']) ? $match_field['treat_consult'] : '';
            $txtTreatConsult = isset($dataReport['report_' . $treat_consult]) && $dataReport['report_' . $treat_consult] != '' ? "<span>  <b>Consult แผนก</b> : {$dataReport['report_' . $treat_consult]}</span><br>" : '';

            $treat_send_hosp = isset($match_field['treat_send_hosp']) ? $match_field['treat_send_hosp'] : '';
            $txtTreatSendHosp = isset($dataReport['report_' . $treat_send_hosp]) && $dataReport['report_' . $treat_send_hosp] != '' ? "<span>  <b>ตอบกลับ รพ.ต้นสังกัด</b> : {$dataReport['report_' . $treat_send_hosp]}</span> <br>" : '';

            $treat_med_check = isset($match_field['treat_med_check']) ? $match_field['treat_med_check'] : '';
            $txtTreatMedCheck = isset($dataReport['report_' . $treat_med_check]) && $dataReport['report_' . $treat_med_check] != '' && $dataReport['report_' . $treat_med_check] != 'No' ? "<span>  <b>Medication</b> : {$dataReport['report_' . $treat_med_check]}</span><br>" : '';

            $treat_fu_time = isset($match_field['treat_fu_time']) ? $match_field['treat_fu_time'] : '';
            $txtTreatFuTime = isset($dataReport['report_' . $treat_fu_time]) && $dataReport['report_' . $treat_fu_time] != '' ? "<span>  <b>Follow up</b> : {$dataReport['report_' . $treat_fu_time]}</span><br>" : '';

            $treat_advice_check = isset($match_field['treat_advice_check']) ? $match_field['treat_advice_check'] : '';
            $txtTreatAdviceCheck = isset($dataReport['report_' . $treat_advice_check]) && $dataReport['report_' . $treat_advice_check] != '' && $dataReport['report_' . $treat_advice_check] != 'No' ? "<span>  <b>แนะนําอาหาร ,การปฏิบัติตัว ,ตรวจรักษาตามอาการที่ รพ. ตามสิทธิ์</b> : {$dataReport['report_' . $treat_advice_check]}</span><br>" : '';

            $treat_advicedoc_txt = isset($match_field['treat_advicedoc_txt']) ? $match_field['treat_advicedoc_txt'] : '';
            $txtTreatAdvicedocTxt = isset($dataReport['report_' . $treat_advicedoc_txt]) && $dataReport['report_' . $treat_advicedoc_txt] != '' ? "<span>  <b>แนะนำพบแพทย์ เฉพาะทาง</b> : {$dataReport['report_' . $treat_advicedoc_txt]}</span><br>" : '';

            $treat_commant = isset($match_field['treat_commant']) ? $match_field['treat_commant'] : '';
            $txtTreatCommant = isset($dataReport['report_' . $treat_commant]) && $dataReport['report_' . $treat_commant] != '' ? "<span>  <b>อื่นๆ</b> : {$dataReport['report_' . $treat_commant]}</span>" : '';
            //Treat end
            //TK start
            $tk_cc = isset($match_field['tk_cc']) ? $match_field['tk_cc'] : '';
            $txtTkCC = isset($dataReport['report_' . $tk_cc]) && $dataReport['report_' . $tk_cc] != '' ? "<span>  <b>CC</b> : {$dataReport['report_' . $tk_cc]} </span><br>" : '';

            $tk_ph = isset($match_field['tk_ph']) ? $match_field['tk_ph'] : '';
            $txtTkPH = isset($dataReport['report_' . $tk_ph]) && $dataReport['report_' . $tk_ph] != '' ? "<span>  <b>PH</b> : {$dataReport['report_' . $tk_ph]} </span> <br>" : '';

            $tk_pi = isset($match_field['tk_pi']) ? $match_field['tk_pi'] : '';
            $txtTkPI = isset($dataReport['report_' . $tk_pi]) && $dataReport['report_' . $tk_pi] != '' ? " <span>  <b>PI</b> : {$dataReport['report_' . $tk_pi]} </span><br>" : '';

            $tk_fh = isset($match_field['tk_fh']) ? $match_field['tk_fh'] : '';
            $txtTkFH = isset($dataReport['report_' . $tk_fh]) && $dataReport['report_' . $tk_fh] != '' ? "<span>  <b>FH</b> : {$dataReport['report_' . $tk_fh]} </span> <br>" : '';

            $tk_inspect = isset($match_field['tk_inspect']) ? $match_field['tk_inspect'] : '';
            $txtTkInspect = isset($dataReport['report_' . $tk_inspect]) && $dataReport['report_' . $tk_inspect] != '' ? "<span>  <b>การตรวจ</b> : {$dataReport['report_' . $tk_inspect]} </span> <br>" : '';

            $tk_nop = isset($match_field['tk_nop']) ? $match_field['tk_nop'] : '';
            $txtTkNOP = isset($dataReport['report_' . $tk_nop]) && $dataReport['report_' . $tk_nop] != '' ? "<span>  <b>NOP</b> : {$dataReport['report_' . $tk_nop]} </span>" : '';
            //TK end
            //DI start
            $di_txt = isset($match_field['di_txt']) ? $match_field['di_txt'] : '';
            $txtDiTxt = isset($dataReport['report_' . $di_txt]) && $dataReport['report_' . $di_txt] != '' ? " <span>  <b>Principal Dx</b> : {$dataReport['report_' . $di_txt]} </span><br>" : '';

            $di_icd10 = isset($match_field['di_icd10']) ? $match_field['di_icd10'] : '';
            $txtDiIcd10 = isset($dataReport['report_' . $di_icd10]) && $dataReport['report_' . $di_icd10] != '' ? "<span>  <b>Principal ICD10</b> : {$dataReport['report_' . $di_icd10]} </span>" : '';
            //DI end
            //PE start
            $dataPE = ThaiHisQuery::GetTableData('zdata_pe', ['pe_visit_id' => $visitid, 'xdepartmentx' => Yii::$app->user->identity->profile->department], 'one', '', ['column' => 'create_date', 'order' => 'DESC']);

            $search = array('@<script[^>]*?>.*?</script>@si', // Strip out javascript
                '@<style[^>]*?>.*?</style>@siU', // Strip style tags properly
                '@<p[\/\!]*?[^<>]*?>@si', // Strip out HTML tags
                '@<p[\/\!]*?[^<>]*?>@si',
                '@<![\s\S]*?--[ \t
 ]*>@'        // Strip multi-line comments including CDATA
            );
            $txtPeCC = '';
            if (isset($dataPE['pe_cc']) && $dataPE['pe_cc'] != '') {
                $dataPE['pe_cc'] = str_replace("</p>", "<br>", $dataPE['pe_cc']);
                $dataPE['pe_cc'] = str_replace("</div>", "<br>", $dataPE['pe_cc']);

                $dataPE['pe_cc'] = preg_replace($search, '', $dataPE['pe_cc']);
                $txtPeCC = "<span><b>CC</b> : {$dataPE['pe_cc']}</span> <br>";
            }
//            $txtPeCC = isset($dataPE['pe_cc']) && $dataPE['pe_cc'] != '' ? "<span>  <b>CC</b> : {$dataPE['pe_cc']}</span> <br>" : '';

            $txtPePI = '';
            if (isset($dataPE['pe_pi']) && $dataPE['pe_pi'] != '') {
                $dataPE['pe_pi'] = str_replace("</p>", "<br>", $dataPE['pe_pi']);
                $dataPE['pe_pi'] = str_replace("</div>", "<br>", $dataPE['pe_pi']);

                $dataPE['pe_pi'] = preg_replace($search, '', $dataPE['pe_pi']);
                $txtPePI = "<span><b>PI</b> : {$dataPE['pe_pi']}</span><br>";
            }
//            $txtPePI = isset($dataPE['pe_pi']) && $dataPE['pe_pi'] != '' ? "<span>  <b>PI</b> : {$dataPE['pe_pi']}</span><br>" : '';


            $pe_ga = isset($match_field['pe_ga']) ? $match_field['pe_ga'] : '';
            $txtPeGa = isset($dataReport['report_' . $pe_ga]) && $dataReport['report_' . $pe_ga] != '' ? ' : ' . $dataReport['report_' . $pe_ga] : '';

            $pe_n_all = isset($match_field['pe_n_all']) ? $match_field['pe_n_all'] : '';
            $txtPeAll = isset($dataReport['report_' . $pe_n_all]) && $dataReport['report_' . $pe_n_all] != '' ? " <span>  <b>PE</b> : {$dataReport['report_' . $pe_n_all]} $txtPeGa</span> <br>" : '';

            $pe_note = isset($match_field['pe_note']) ? $match_field['pe_note'] : '';
            $txtPeNote = isset($dataReport['report_' . $pe_note]) && $dataReport['report_' . $pe_note] != '' ? "<span> <b>Note</b> : {$dataReport['report_' . $pe_note]}</span>" : '';

            //PE end
//            $pdf->SetFont('thsarabunpsk', 'B', 16);
//            $pdf->Cell(30, 0, 'ดัชนีมวลกาย', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
//            $pdf->SetFont('thsarabunpsk', '', 16);
//            $pdf->Cell(20, 0, 'BW: ' . $txtBmiBw, 0, 0, 'L', 0, '', 0, false, 'T', 'M');
//            $pdf->Cell(20, 0, 'HT: ' . $txtBmiHt, 0, 0, 'L', 0, '', 0, false, 'T', 'M');
//            $pdf->Cell(20, 0, 'BMI: ' . $txtBmiBmi, 0, 0, 'L', 0, '', 0, false, 'T', 'M');
//            $pdf->Cell(20, 0, 'BSA: ' . $txtBmiBsa, 0, 0, 'L', 0, '', 0, false, 'T', 'M');
//            $pdf->Cell(20, 0, 'รอบเอว: ' . $txtBmiWaistline, 0, 0, 'L', 0, '', 0, false, 'T', 'M');
//            $pdf->Ln(10);
            if ($txtBmiBw != '' || $txtBmiHt != '' || $txtBmiBmi != '' || $txtBmiBsa != '') {
                $bmiHtml = <<<HTML
                <table>
                 
                     <tbody>
                        <tr>
                            <td width="20%"> <strong>ดัชนีมวลกาย : </strong> </td>
                            <td width="80%">
                            $txtBMI
                            </td>
                        </tr>
                    </tbody>
                
                </table>
HTML;
                $pdf->writeHTML($bmiHtml, true, false, false, false, '');
                $pdf->writeHTML('<hr>', false, false, true, false, '');
            }


//            $pdf->SetFont('thsarabunpsk', 'B', 16);
//            $pdf->Cell(30, 0, 'Vital sign', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
//            $pdf->SetFont('thsarabunpsk', '', 16);
//            $pdf->Cell(30, 0, 'BP: ' . $txtVsBpSqueeze . ' / ' . $txtVsBpLoosen, 0, 0, 'L', 0, '', 0, false, 'T', 'M');
//            $pdf->Cell(20, 0, 'P: ' . $txtVsPulse, 0, 0, 'L', 0, '', 0, false, 'T', 'M');
//            $pdf->Cell(20, 0, 'R: ' . $txtVsRespiratory, 0, 0, 'L', 0, '', 0, false, 'T', 'M');
//            $pdf->Cell(20, 0, 'T: ' . $txtVsTemperature, 0, 0, 'L', 0, '', 0, false, 'T', 'M');
//            $pdf->Ln(10);

            if ($txtVsSl != '' || $txtVsPulse != '' || $txtVsRespiratory != '' || $txtVsTemperature != '') {
                $vsHtml = <<<HTML
                <table>
                 
                     <tbody>
                        <tr>
                            <td width="20%"> <strong>สัญญาณชีพ : </strong> </td>
                            <td width="80%">
                            $txtVs
                            </td>
                        </tr>
                    </tbody>
                
                </table>
HTML;
                $pdf->writeHTML($vsHtml, true, false, false, false, '');
                $pdf->writeHTML('<hr>', false, false, true, false, '');
            }

            if ($txtTkCC != '' || $txtTkFH != '' || $txtTkInspect != '' || $txtTkNOP != '' || $txtTkPH != '' || $txtTkPI != '') {
                $htmlTk = <<<HTML
                <table>
                 
                     <tbody>
                        <tr>
                            <td width="20%"> <strong>อาการนำผู้ป่วย : </strong> </td>
                            <td width="80%">
                            
                                $txtTkCC
                                $txtTkPH 
                                $txtTkPI
                                $txtTkFH
                                $txtTkInspect
                                $txtTkNOP
                            </td>
                        </tr>
                    </tbody>
                
                </table>
HTML;
//                VarDumper::dump($htmlTk);
                $pdf->writeHTML($htmlTk, true, false, false, false, '');
                $pdf->writeHTML('<hr>', false, false, true, false, '');
            }


            if ($dataPE || $txtPeNote != '') {
                $htmlPE = <<<HTML
               
                <table>
                     <tbody>
                        <tr>
                        <td width="20%"> <strong>การตรวจร่างกาย : </strong></td>
                             <td width="80%">
                                $txtPeAll
                                $txtPeCC
                                $txtPePI
                                $txtPeNote
                            </td>
                     
                        </tr>
                    </tbody>
                
                </table>
HTML;

//                                VarDumper::dump($htmlPE);
                $pdf->writeHTML($htmlPE, true, false, false, false, '');
                $pdf->writeHTML('<hr>', false, false, true, false, '');
            }

            if ($txtDiTxt != '' || $txtDiIcd10 != '') {
                $htmlDiag = <<<HTML
                <table>
                    
                     <tbody>
                        <tr>
                        <td width="20%"><strong>Diagnosis : </strong></td>
                            <td width="80%">
                               $txtDiTxt
                               $txtDiIcd10
                            </td>
                        </tr>
                    </tbody>
                </table>
                
HTML;
//VarDumper::dump($htmlDiagAndPE);
                $pdf->writeHTML($htmlDiag, true, false, false, false, '');
                $pdf->writeHTML('<hr>', false, false, true, false, '');
            }

            if ($txtTreatConsult != '' || $txtTreatSendHosp != '' || $txtTreatMedCheck != '' || $txtTreatFuTime != '' || $txtTreatAdviceCheck != '' || $txtTreatAdvicedocTxt != '' || $txtTreatCommant != '') {
                $htmlTreat = <<<HTML
                <table>
                   
                     <tbody>
                        <tr>
                           <td width="20%"><strong>Treatment : </strong></td>
                           <td width="80%">
                                $txtTreatConsult
                                $txtTreatSendHosp
                                $txtTreatMedCheck
                                $txtTreatFuTime
                                $txtTreatAdviceCheck
                                $txtTreatAdvicedocTxt
                                $txtTreatCommant
                            </td>
                        </tr>
                    </tbody>
                </table>
                
HTML;

                $pdf->writeHTML($htmlTreat, true, false, false, false, '');
                $pdf->writeHTML('<hr>', false, false, true, false, '');
            }


            $dataOrder = \backend\modules\thaihis\classes\OrderFunc::getOrderTranReport($visitid);
            if ($dataOrder) {
                $txtOrder = '';
                if (is_array($dataOrder)) {
                    foreach ($dataOrder as $key => $val) {
                        $txtOrder .= $val['order_name'] . ' , ';
                    }
                }
                $InvestHtml = <<<HTML
 <table>
                   
                     <tbody>
                        <tr>
                           <td width="20%"><strong>Investigation : </strong></td>
                           <td width="80%">
                                $txtOrder
                            </td>
                        </tr>
                    </tbody>
                </table>
HTML;


                $pdf->writeHTML($InvestHtml, true, false, false, false, '');
                $pdf->writeHTML('<hr>', false, false, true, false, '');
            }


            $dataDrug = \backend\modules\pis\classes\PisFunc::getOrderTranReport($visitid);
            if ($dataDrug) {
                $txtDrug = '';
                if (is_array($dataDrug)) {
                    foreach ($dataDrug as $key => $val) {

                        $txtDrug .= $val['item_name'] . ' , ';
                    }
                }
                $drugHtml = <<<HTML
 <table>
                   
                     <tbody>
                        <tr>
                           <td width="20%"><strong>Drug  prescribe : </strong></td>
                           <td width="80%">
                                $txtDrug
                            </td>
                        </tr>
                    </tbody>
                </table>
HTML;
                $pdf->writeHTML($drugHtml, true, false, false, false, '');
                $pdf->writeHTML('<hr>', true, false, true, false, '');
            }

            $pdf->Ln(10);
            $pdf->SetFont('thsarabunpsk', 'B', 16);
            $pdf->Cell(165, 0, 'ลงชื่อ.....................................................', 0, 1, 'R', 0, '', 0, false, 'T', 'M');
            $doc_name = Yii::$app->user->identity->profile->title . ' ' . Yii::$app->user->identity->profile->firstname . ' ' . Yii::$app->user->identity->profile->lastname;
            $pdf->Cell(160, 0, '( ' . $doc_name . ' )', 0, 1, 'R', 0, '', 0, false, 'T', 'M');

            $pdf->Output('report.pdf', 'I');
            Yii::$app->end();
        } else {
            return $this->renderAjax('_btn-report', [
                        'ezf_main_id' => $ezf_main_id,
                        'ezf_ref_id' => $ezf_ref_id,
                        'condition' => $condition,
                        'group_by' => $group_by,
                        'target' => $target,
                        'reloadDiv' => $reloadDiv,
                        'btn_text' => $btn_text,
                        'btn_color' => $btn_color,
                        'btn_style' => $btn_style,
                        'btn_icon' => $btn_icon,
            ]);
        }
    }

    public function actionGetMatchField() {
        $options = Yii::$app->request->post('options', '');
        $ezf_id = Yii::$app->request->post('ezf_id', []);
        $main_ezf_id = Yii::$app->request->post('main_ezf_id', 0);
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
                        ->where(['ezf_id' => $ezf_id])->andWhere('ezf_field_name is not null')->all();
        $dataForm = [];
        if ($dataFields) {
            foreach ($dataFields as $vField) {
                $dataEzf = EzfQuery::getEzformById($vField['ezf_id']);
                if ($dataEzf && $vField['name'] != '') {
                    $dataForm[$dataEzf['ezf_name']][$vField['id']] = $vField['name'];
                }
            }
        }

        return $this->renderAjax('_match-field', [
                    'dataForm' => $dataForm,
                    'options' => $options
        ]);
    }

    public static function removeTag($txtVal) {
        $txtVal = str_replace("</p>", "<br>", $txtVal);
        $txtVal = str_replace("</div>", "<br>", $txtVal);
        $txtVal = str_replace("</label>", "", $txtVal);
        $search = array('@<script[^>]*?>.*?</script>@si', // Strip out javascript
            '@<style[^>]*?>.*?</style>@siU', // Strip style tags properly
            '@<p[\/\!]*?[^<>]*?>@si', // Strip out HTML tags
            '@<div[\/\!]*?[^<>]*?>@si',
            '@<hr[\/\!]*?[^<>]*?>@si',
            '@<label[\/\!]*?[^<>]*?>@si',
            '@<![\s\S]*?--[ \t]*>@'        // Strip multi-line comments including CDATA
        );
        $txtVal = preg_replace($search, '', $txtVal);

        return preg_replace("/\r\n|\r|\n/", ' ', $txtVal);
    }

    private function removeAll($txtVal) {
        $search = array('@<script[^>]*?>.*?</script>@si', // Strip out javascript
            '@<style[^>]*?>.*?</style>@siU', // Strip style tags properly
            '@<[\/\!]*?[^<>]*?>@si', // Strip out HTML tags
            '@<![\s\S]*?--[ \t]*>@'        // Strip multi-line comments including CDATA
        );
        $txtVal = preg_replace($search, '', $txtVal);

        return preg_replace("/\r\n|\r|\n/", ' ', $txtVal);
    }

    private function getValue($data, $fieldName) {
        //$modelFields = \backend\modules\ezforms2\models\EzformFields::find()->where('ezf_id = :ezf_id AND ezf_field_name = :ezf_field_name', [':ezf_field_name' => $fieldName, ':ezf_id' => $ezf_id])->one();
        if (isset(Yii::$app->session['ezf_input'])) {
            $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($fieldName['ezf_field_type'], Yii::$app->session['ezf_input']);
        }
        $txtVal = EzfUiFunc::getValueEzform($dataInput, $fieldName, $data);
        if ($fieldName['ezf_field_type'] == 66) {
            $txtVal = self::removeTag($txtVal);
        }

        return $txtVal;
    }

    private function getQueryDeptForm($dept_id, $queryBuilder) {
        $data = ThaiHisQuery::getDeptMapForm($dept_id);

        $queryBuilder->leftJoin('(SELECT * FROM ' . $data['ezf_table_nu'] . ' WHERE ' . $data['ezf_table_nu'] . '.rstat not in (0,3) AND ' . $data['ezf_table_nu'] . '.xdepartmentx = :xdpart ORDER BY ' . $data['ezf_table_nu'] . '.create_date DESC) AS zdata_tk', 'zdata_tk.target = zdata_visit.id');
        $queryBuilder->leftJoin('(SELECT * FROM ' . $data['ezf_table_doc'] . ' WHERE ' . $data['ezf_table_doc'] . '.rstat not in (0,3) AND ' . $data['ezf_table_doc'] . '.xdepartmentx = :xdpart ORDER BY ' . $data['ezf_table_doc'] . '.create_date DESC) AS zdata_pe', 'zdata_pe.target = zdata_visit.id');

        return $queryBuilder;
    }

    public function actionBtnReport2() {

        $ezf_main_id = Yii::$app->request->get('ezf_main_id', '');
        $ezf_ref_id = EzfFunc::stringDecode2Array(Yii::$app->request->get('ezf_ref_id', ''));
        $condition = EzfFunc::stringDecode2Array(Yii::$app->request->get('condition', ''));
        $group_by = Yii::$app->request->get('group_by', '');
        $target = Yii::$app->request->get('target', '');
        $reloadDiv = Yii::$app->request->get('reloadDiv', 'brn-report');
        $btn_text = Yii::$app->request->get('btn_text', 'brn-report');
        $btn_color = Yii::$app->request->get('btn_color', 'btn-default');
        $btn_style = Yii::$app->request->get('btn_style', ' ');
        $btn_icon = Yii::$app->request->get('btn_icon', ' fa-print');
        $visitid = Yii::$app->request->get('visitid', '');
        $status = Yii::$app->request->get('status', '0');
        $match_field = EzfFunc::stringDecode2Array(Yii::$app->request->get('match_field', ''));
        $header_report = Yii::$app->request->get('header_report', 'EMR');

        if ($status == '1') {

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
            $arrEzf[] = $ezf_main_id;
            $arrFields = [];
            if (is_array($match_field) && !empty($match_field)) {
                foreach ($match_field as $kField => $valField) {
                    $arrFields[] = $valField;
                }
            }

            $cEzf = [];
            $modelFields = QueueFunc::getFieldDetailById($arrFields, "all");
            $txtField = null;
            $orderBy = [];
            if ($modelFields) {
                foreach ($modelFields as $value) {

                    if ($value['ezf_field_type'] != 76) {
                        if (!in_array($value['ezf_table'], $cEzf)) {
                            $orderBy[$value['ezf_table'] . '.update_date'] = SORT_DESC;
                            $cEzf[] = $value['ezf_table'];
                        }
                        if ($value['ezf_id'] == $ezf_main_id) {
                            $txtField .= $value['ezf_table'] . '.id,';
                        }
                        if ($value['ezf_field_name'] == 'id') {
                            $txtField .= $value['ezf_table'] . '.' . $value['ezf_field_name'] . " AS " . 'report_' . $value['ezf_field_id'] . ",";
                        } else {
                            $txtField .= $value['ezf_table'] . '.' . $value['ezf_field_name'] . " AS " . 'report_' . $value['ezf_field_id'] . ",";
                        }
                    } else {
                        $fieldRef = (new Query())
                                        ->select(['ez.ezf_id', 'ezf_name', 'ezf_table', 'ef.*'])
                                        ->from('ezform_fields ef')
                                        ->innerJoin('ezform ez', "ez.ezf_id=ef.ezf_id AND (ez.ezf_version=ef.ezf_version OR ef.ezf_version='all')")
                                        ->where(['ef.ezf_field_ref' => $value['ezf_field_id']])->all();
//                        VarDumper::dump($fieldRef);
                        if ($fieldRef) {
//                            $modelFields[] = $fieldRef;
                            foreach ($fieldRef as $vFieldRef) {
                                $txtField .= $vFieldRef['ezf_table'] . '.' . $vFieldRef['ezf_field_name'] . ",";
                            }
                        }
                    }
                }
            }
//            VarDumper::dump($modelFields);
//            $txtField ? $txtField = substr($txtField, 0, strlen($txtField) - 1) : null;
            $txtField .= 'visit_date';
//            $dataQuery = QueueFunc::getQueryJoin($ezf_main_id, $ezf_ref_id, $txtField);
            $dept_id = Yii::$app->user->identity->profile->department;
            $dataQuery = (new Query())->select($txtField)
                    ->from('zdata_patientprofile')
                    ->leftJoin('zdata_visit', 'zdata_visit.target = zdata_patientprofile.id AND zdata_visit.rstat not in (0,3)')
                    //->leftJoin('(SELECT * FROM zdata_tk WHERE zdata_tk.rstat not in (0,3) AND zdata_tk.xdepartmentx = :xdpart ORDER BY zdata_tk.create_date DESC) AS zdata_tk', 'zdata_tk.target = zdata_visit.id')
                    //->leftJoin('(SELECT * FROM zdata_pe WHERE zdata_pe.rstat not in (0,3) AND zdata_pe.xdepartmentx = :xdpart ORDER BY zdata_pe.create_date DESC) AS zdata_pe', 'zdata_pe.target = zdata_visit.id')
                    ->leftJoin('(SELECT * FROM zdata_dt WHERE zdata_dt.rstat not in (0,3)) AS zdata_dt', 'zdata_dt.target = zdata_visit.id ')
                    ->leftJoin('(SELECT * FROM zdata_vs WHERE zdata_vs.rstat not in (0,3)) AS zdata_vs', 'zdata_vs.target = zdata_visit.id')
                    ->leftJoin('(SELECT * FROM zdata_bmi WHERE zdata_bmi.rstat not in (0,3)) AS zdata_bmi', 'zdata_bmi.target = zdata_visit.id')
                    ->leftJoin('(SELECT * FROM zdata_treatment WHERE zdata_treatment.rstat not in (0,3)) AS zdata_treatment', 'zdata_treatment.target = zdata_treatment.id');
//                    ->params([':xdpart' => $dept_id]);

            if (isset($condition) && is_array($condition) && !empty($condition)) {
                if ($condition) {
                    foreach ($condition as $vCondition) {
                        $modelConditionField = QueueFunc::getFieldDetailById($vCondition['field']);
                        if ($modelConditionField) {
                            $conFild = '';
                            if ($vCondition['value'] == '{department}') {
                                $vCondition['value'] = Yii::$app->user->identity->profile->department;
                            } else if ($vCondition['value'] == '{permission}') {
                                $vCondition['value'] = Yii::$app->user->can('doctor');
                            } else if ($vCondition['value'] == '{user_id}') {
                                $vCondition['value'] = Yii::$app->user->id;
                            } else if ($vCondition['value'] == '{today}') {
                                $vCondition['value'] = new Expression('CURDATE()');
                                $conFild = 'DATE(' . $modelConditionField['ezf_table'] . '.' . $modelConditionField['ezf_field_name'] . ')';
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
                                $dataQuery->andWhere([$vCondition['operator'], $conFild != '' ? $conFild : $modelConditionField['ezf_table'] . '.' . $modelConditionField['ezf_field_name'], $vCondition['value']]);
                            } else if (isset($vCondition['condition']) && $vCondition['condition'] == 'or') {
                                $dataQuery->orWhere([$vCondition['operator'], $conFild != '' ? $conFild : $modelConditionField['ezf_table'] . '.' . $modelConditionField['ezf_field_name'], $vCondition['value']]);
                            } else {
                                $dataQuery->andWhere([$vCondition['operator'], $conFild != '' ? $conFild : $modelConditionField['ezf_table'] . '.' . $modelConditionField['ezf_field_name'], $vCondition['value']]);
                            }
                        }
//                        }
                    }
                }
            }
//            VarDumper::dump($dataQuery->createCommand()->rawSql);
//            $dataQuery = (new Query())->select('zpp.id')
//                ->from('zdata_patientprofile AS zpp')
//                ->leftJoin('zdata_prefix AS zp','vpp.pt_prefix_id=zp.prefix_id')
//                ->leftJoin('zdata_visit AS zv','zv.ptid=zpp.id')
//                ->leftJoin('zdata_vs AS vs','vs.vs_visit_id=zv.id')
//                ->leftJoin('zdata_bmi AS bmi','bmi.bmi_visit_id=zv.id')
//                ->leftJoin('zdata_tk AS tk','tk.tk_visit_id=zv.id')
//                ->leftJoin('zdata_pe AS pe','pe.pe_visit_id=zv.id')
//                ->leftJoin('zdata_dt AS dt','dt.di_visit_id=zv.id')
//                ->leftJoin('zdata_treatment AS zt','zt.treat_visit_id=zv.id')
//                ->where(['zv.id' => $visitid,'zpp.id'=>$target]);
            $dataReport = $dataQuery->orderBy($orderBy)->one();
            if (!isset(Yii::$app->session['ezf_input'])) {
                Yii::$app->session['ezf_input'] = EzfQuery::getInputv2All();
            }
            if ($dataReport) {
                foreach ($modelFields as $k => $val) {
                    $dataInput = null;
//                $ezf_input=null;
                    if (isset(Yii::$app->session['ezf_input'])) {
//                    $ezf_input = Yii::$app->session['ezf_input'];
                        $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($val['ezf_field_type'], Yii::$app->session['ezf_input']);
                        if ($val['ezf_field_type'] != 76) {
                            $val['ezf_field_name'] = 'report_' . $val['ezf_field_id'];
                        }
                    }
//                    VarDumper::dump($val);
                    $txtVal = EzfUiFunc::getValueEzform($dataInput, $val, $dataReport);

                    if ($val['ezf_field_type'] == 66) {
//                        $txtVal = str_replace("<p>", "", $txtVal);
                        $txtVal = str_replace("</p>", "<br>", $txtVal);
//                        $txtVal = str_replace("<div>", "", $txtVal);
                        $txtVal = str_replace("</div>", "<br>", $txtVal);
                        $search = array('@<script[^>]*?>.*?</script>@si', // Strip out javascript
                            '@<style[^>]*?>.*?</style>@siU', // Strip style tags properly
                            '@<p[\/\!]*?[^<>]*?>@si', // Strip out HTML tags
                            '@<p[\/\!]*?[^<>]*?>@si',
                            '@<![\s\S]*?--[ \t
 ]*>@'        // Strip multi-line comments including CDATA
                        );
                        $txtVal = preg_replace($search, '', $txtVal);
                    }


                    $dataReport['report_' . $val['ezf_field_id']] = $txtVal;
                }
            }
            $pdf = new SDPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);
            $pdf->SetCreator('KK');
            $pdf->SetAuthor('UD CANCER');
            $pdf->SetTitle($header_report);
            $pdf->SetSubject($header_report);
            $pdf->SetKeywords($header_report);
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
            $pdf->SetMargins(15, 10, 10, true);
            $pdf->SetHeaderMargin(10);
            $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

            // set auto page breaks
            $pdf->SetAutoPageBreak(TRUE, 3);

            // set image scale factor
            $dataid = \backend\modules\patient\Module::$dataidForm['hos_config'];
            $ezf_table = \backend\modules\patient\Module::$formTableName['hos_config'];
            $dataHosConfig = \backend\modules\ezforms2\classes\EzfUiFunc::loadTbData($ezf_table, $dataid);

            if ($dataHosConfig['logo_02']) {
                $path = Yii::getAlias('@storageUrl/ezform/fileinput') . '/' . $dataHosConfig['logo_02'];
            } else {
                $path = Yii::getAlias('@storageUrl/images') . '/nouser.png';
            }

            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
            $pdf->SetFont($pdf->fontName, '', $pdf->fontSize);
            $pdf->fontSize = 18;
            $pdf->AddFont('thsarabunpsk', '', 'thsarabunpsk.php');
            $pdf->AddFont('thsarabunpsk', 'B', 'thsarabunpskb.php');
            $pdf->AddPage();
            $pdf->SetFont('thsarabunpsk', 'B', 20);
            if (\backend\modules\thaihis\classes\ThaiHisFunc::isUrlExist($path)) {
                $pdf->Image($path, 20, 5, 20, 20, 'JPG');
            }
            //$pdf->Ln(24);
            $pdf->Cell(185, 0, 'โรงพยาบาลมะเร็งอุดรธานี (UDONTHANI CANCER HOSPITAL)', 0, 1, 'C', 0, '', 0, false, 'T', 'M');
            $pdf->Cell(0, 0, $header_report, 0, 1, 'C', 0, '', 0, false, 'T', 'M');

            $pdf->Ln(5);
//            $pdf->set
//            VarDumper::dump($match_field);
            $pdf->SetFont('thsarabunpsk', '', 16);
            $prefix_id = isset($match_field['pt_prefix']) ? $match_field['pt_prefix'] : '';
            $txtPrefix = isset($dataReport['report_' . $prefix_id]) ? $dataReport['report_' . $prefix_id] : '';
            $firstname = isset($match_field['pt_firstname']) ? $match_field['pt_firstname'] : '';
            $txtFirstname = isset($dataReport['report_' . $firstname]) ? $dataReport['report_' . $firstname] : '';
            $lastname = isset($match_field['pt_lastname']) ? $match_field['pt_lastname'] : '';
            $txtLastname = isset($dataReport['report_' . $lastname]) ? $dataReport['report_' . $lastname] : '';
            $pdf->SetFont('thsarabunpsk', 'B', 16);
            $pdf->Cell(75, 0, 'ชื่อ-สกุล : ' . $txtPrefix . ' ' . $txtFirstname . ' ' . $txtLastname, 0, 0, 'L', 0, '', 0, false, 'T', 'M');


            $pt_age = isset($match_field['pt_age']) ? $match_field['pt_age'] : '';
            $txtAge = isset($dataReport['report_' . $pt_age]) ? $dataReport['report_' . $pt_age] : '';
//            VarDumper::dump($txtAge);
            $txtAge = $txtAge != '' ? QueueFunc::calAge($txtAge, true, false, false) : '';
            $pdf->Cell(25, 0, 'อายุ : ' . $txtAge, 0, 0, 'L', 0, '', 0, false, 'T', 'M');

            $pt_hn = isset($match_field['pt_hn']) ? $match_field['pt_hn'] : '';
            $txtHN = isset($dataReport['report_' . $pt_hn]) ? $dataReport['report_' . $pt_hn] : '';
            $pdf->Cell(35, 0, 'HN : ' . $txtHN, 0, 0, 'L', 0, '', 0, false, 'T', 'M');

            $txtVsDate = isset($dataReport['visit_date']) && $dataReport['visit_date'] != '' ? SDdate::mysql2phpThDateSmall($dataReport['visit_date'], false) : '';
            $pdf->Cell(40, 0, 'วันที่ : ' . $txtVsDate, 0, 0, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->Ln(10);
            $pdf->SetFont('thsarabunpsk', '', 16);
            $pdf->writeHTML('<hr>', false, false, true, false, '');
//            $pdf->Ln(2);
            $pdf->SetFont('thsarabunpsk', '', 16);

            //Vital start
            $vs_bp_squeeze = isset($match_field['vs_bp_squeeze']) ? $match_field['vs_bp_squeeze'] : '';
            $txtVsBpSqueeze = isset($dataReport['report_' . $vs_bp_squeeze]) && $dataReport['report_' . $vs_bp_squeeze] != '' ? $dataReport['report_' . $vs_bp_squeeze] : '';

            $vs_bp_loosen = isset($match_field['vs_bp_loosen']) ? $match_field['vs_bp_loosen'] : '';
            $txtVsBpLoosen = isset($dataReport['report_' . $vs_bp_loosen]) && $dataReport['report_' . $vs_bp_loosen] != '' ? $dataReport['report_' . $vs_bp_loosen] : '';

            $txtVsBpSqueeze != '' || $txtVsBpLoosen != '' ? $txtVsSl = "<span><b>B</b> : {$txtVsBpSqueeze} / {$txtVsBpLoosen}</span> mmHg" : $txtVsSl = '';

            $vs_pulse = isset($match_field['vs_pulse']) ? $match_field['vs_pulse'] : '';
            $txtVsPulse = isset($dataReport['report_' . $vs_pulse]) && $dataReport['report_' . $vs_pulse] != '' ? " <span><b>P</b> : {$dataReport['report_' . $vs_pulse]}</span> / min" : '';

            $vs_respiratory = isset($match_field['vs_respiratory']) ? $match_field['vs_respiratory'] : '';
            $txtVsRespiratory = isset($dataReport['report_' . $vs_respiratory]) && $dataReport['report_' . $vs_respiratory] != '' ? " <span><b>R</b> : {$dataReport['report_' . $vs_respiratory]}</span> / min" : '';

            $vs_temperature = isset($match_field['vs_temperature']) ? $match_field['vs_temperature'] : '';
            $txtVsTemperature = isset($dataReport['report_' . $vs_temperature]) && $dataReport['report_' . $vs_temperature] != '' ? " <span><b>T</b> : {$dataReport['report_' . $vs_temperature]}</span> <sup>0</sup>C" : '';

            $txtVs = $txtVsSl . ' ' . $txtVsPulse . ' ' . $txtVsRespiratory . ' ' . $txtVsTemperature;
            //Vital end
            //BMI start
            $bmi_bw = isset($match_field['bmi_bw']) ? $match_field['bmi_bw'] : '';
            $txtBmiBw = isset($dataReport['report_' . $bmi_bw]) && $dataReport['report_' . $bmi_bw] != '' ? " <span><b>BW</b> : {$dataReport['report_' . $bmi_bw]}</span> kg" : '';

            $bmi_ht = isset($match_field['bmi_ht']) ? $match_field['bmi_ht'] : '';
            $txtBmiHt = isset($dataReport['report_' . $bmi_ht]) && $dataReport['report_' . $bmi_ht] != '' ? " <span>  <b>HT</b> : {$dataReport['report_' . $bmi_ht]}</span> cms" : '';

            $bmi_bmi = isset($match_field['bmi_bmi']) ? $match_field['bmi_bmi'] : '';
            $txtBmiBmi = isset($dataReport['report_' . $bmi_bmi]) && $dataReport['report_' . $bmi_bmi] != '' ? " <span>  <b>BMI</b> : {$dataReport['report_' . $bmi_bmi]}</span> " : '';

            $bmi_bsa = isset($match_field['bmi_bsa']) ? $match_field['bmi_bsa'] : '';
            $txtBmiBsa = isset($dataReport['report_' . $bmi_bsa]) && $dataReport['report_' . $bmi_bsa] != '' ? " <span>  <b>BSA</b> : {$dataReport['report_' . $bmi_bsa]}</span> cms" : '';

            $bmi_waistline = isset($match_field['bmi_waistline']) ? $match_field['bmi_waistline'] : '';
            $txtBmiWaistline = isset($dataReport['report_' . $bmi_waistline]) ? $dataReport['report_' . $bmi_waistline] : '';

            $txtBMI = $txtBmiBw . ' ' . $txtBmiHt . ' ' . $txtBmiBmi . ' ' . $txtBmiBsa;
            //BMI end
            //Treat start
            $treat_consult = isset($match_field['treat_consult']) ? $match_field['treat_consult'] : '';
            $txtTreatConsult = isset($dataReport['report_' . $treat_consult]) && $dataReport['report_' . $treat_consult] != '' ? "<span>  <b>Consult แผนก</b> : {$dataReport['report_' . $treat_consult]}</span><br>" : '';

            $treat_send_hosp = isset($match_field['treat_send_hosp']) ? $match_field['treat_send_hosp'] : '';
            $txtTreatSendHosp = isset($dataReport['report_' . $treat_send_hosp]) && $dataReport['report_' . $treat_send_hosp] != '' ? "<span>  <b>ตอบกลับ รพ.ต้นสังกัด</b> : {$dataReport['report_' . $treat_send_hosp]}</span> <br>" : '';

            $treat_med_check = isset($match_field['treat_med_check']) ? $match_field['treat_med_check'] : '';
            $txtTreatMedCheck = isset($dataReport['report_' . $treat_med_check]) && $dataReport['report_' . $treat_med_check] != '' && $dataReport['report_' . $treat_med_check] != 'No' ? "<span>  <b>Medication</b> : {$dataReport['report_' . $treat_med_check]}</span><br>" : '';

            $treat_fu_time = isset($match_field['treat_fu_time']) ? $match_field['treat_fu_time'] : '';
            $txtTreatFuTime = isset($dataReport['report_' . $treat_fu_time]) && $dataReport['report_' . $treat_fu_time] != '' ? "<span>  <b>Follow up</b> : {$dataReport['report_' . $treat_fu_time]}</span><br>" : '';

            $treat_advice_check = isset($match_field['treat_advice_check']) ? $match_field['treat_advice_check'] : '';
            $txtTreatAdviceCheck = isset($dataReport['report_' . $treat_advice_check]) && $dataReport['report_' . $treat_advice_check] != '' && $dataReport['report_' . $treat_advice_check] != 'No' ? "<span>  <b>แนะนําอาหาร ,การปฏิบัติตัว ,ตรวจรักษาตามอาการที่ รพ. ตามสิทธิ์</b> : {$dataReport['report_' . $treat_advice_check]}</span><br>" : '';

            $treat_advicedoc_txt = isset($match_field['treat_advicedoc_txt']) ? $match_field['treat_advicedoc_txt'] : '';
            $txtTreatAdvicedocTxt = isset($dataReport['report_' . $treat_advicedoc_txt]) && $dataReport['report_' . $treat_advicedoc_txt] != '' ? "<span>  <b>แนะนำพบแพทย์ เฉพาะทาง</b> : {$dataReport['report_' . $treat_advicedoc_txt]}</span><br>" : '';

            $treat_commant = isset($match_field['treat_commant']) ? $match_field['treat_commant'] : '';
            $txtTreatCommant = isset($dataReport['report_' . $treat_commant]) && $dataReport['report_' . $treat_commant] != '' ? "<span>  <b>อื่นๆ</b> : {$dataReport['report_' . $treat_commant]}</span>" : '';
            //Treat end
            //DI start
            $di_txt = isset($match_field['di_txt']) ? $match_field['di_txt'] : '';
            $txtDiTxt = isset($dataReport['report_' . $di_txt]) && $dataReport['report_' . $di_txt] != '' ? " <span>  <b>Principal Dx</b> : {$dataReport['report_' . $di_txt]} </span><br>" : '';

            $di_icd10 = isset($match_field['di_icd10']) ? $match_field['di_icd10'] : '';
            $txtDiIcd10 = isset($dataReport['report_' . $di_icd10]) && $dataReport['report_' . $di_icd10] != '' ? "<span>  <b>Principal ICD10</b> : {$dataReport['report_' . $di_icd10]} </span>" : '';
            //DI end 

            if ($txtVsSl != '' || $txtVsPulse != '' || $txtVsRespiratory != '' || $txtVsTemperature != '') {
                $vsHtml = <<<HTML
                <table>
                 
                     <tbody>
                        <tr>
                            <td width="20%"> <strong>Vital Sign : </strong> </td>
                            <td width="80%">
                            $txtVs
                            </td>
                        </tr>
                    </tbody>
                
                </table>
HTML;
                $pdf->writeHTML($vsHtml, true, false, false, false, '');
                $pdf->writeHTML('<hr>', false, false, true, false, '');
            }

            if ($txtBmiBw != '' || $txtBmiHt != '' || $txtBmiBmi != '' || $txtBmiBsa != '') {
                $bmiHtml = <<<HTML
                <table>
                 
                     <tbody>
                        <tr>
                            <td width="20%"> <strong>ดัชนีมวลกาย : </strong> </td>
                            <td width="80%">
                            $txtBMI
                            </td>
                        </tr>
                    </tbody>
                
                </table>
HTML;
                $pdf->writeHTML($bmiHtml, true, false, false, false, '');
                $pdf->writeHTML('<hr>', false, false, true, false, '');
            }

            //TK start
            $data_widget = (new Query())->select('*')
                    ->from('ezmodule_widget')
                    ->where(['widget_id' => '1545553268068964800'])
                    ->one();
            $options_widget = null;
            if ($data_widget) {
                $options_widget = SDUtility::string2Array($data_widget['options']);
                $dataDeptMap = ThaiHisQuery::getDeptMapForm($dept_id);
                $tablePe = $dataDeptMap['ezf_table_pe'];
                $tableTk = $dataDeptMap['ezf_table_tk'];
                $fields_dept = [];
                $template_content = '';
                foreach ($options_widget['tabs'] as $value) {
                    if (in_array($dept_id, $value['dept_list'])) {
                        $arrFields = $value['field_display'];
                        $template_content = $value['template_content'];
                        break;
                    }
                }

                $fieldName = \backend\modules\ezforms2\models\EzformFields::findAll(['ezf_field_id' => $arrFields]);
                $txtField = ['id'];
                foreach ($fieldName as $value) {
                    $txtField[] = $value['ezf_field_name'];
                }

                $dataTk = (new Query())->select($txtField)
                        ->from($tableTk)
                        ->where(['rstat' => '1', 'target' => $visitid])
                        ->one();

                $template_content = $this->removeAll($template_content);

                foreach ($fieldName as $value) {
                    if ($value['ezf_field_type'] !== 66) {
                        $path_items["{{$value['ezf_field_name']}}"] = $this->getValue($dataTk, $value) . '<br>';
                    } else {
                        $path_items["{{$value['ezf_field_name']}}"] = $this->getValue($dataTk, $value);
                    }
                }

                $template_content = strtr($template_content, $path_items);
                $template_content = self::removeTag($template_content);
//            VarDumper::dump($template_content);
                //TK end
                if ($dataTk) {
                    $htmlTk = <<<HTML
               
                <table>
                     <tbody>
                        <tr>
                        <td width="20%"> <strong>ตรวจคัดกรอง / หน้าห้องตรวจแพทย์ : </strong></td>
                             <td width="80%">
                                $template_content
                            </td>
                     
                        </tr>
                    </tbody>                
                </table>
HTML;
                    $pdf->writeHTML($htmlTk, true, false, false, false, '');
                    $pdf->writeHTML('<hr>', false, false, true, false, '');
                }
            }

            //PE start
            $path_items = null;
            $data_widget = (new Query())->select('*')->from('ezmodule_widget')
                    ->where(['widget_id' => '1546697649071881100'])
                    ->one();
            $options_widget = null;
            if ($data_widget) {
                $options_widget = SDUtility::string2Array($data_widget['options']);

                $fields_dept = [];
                $template_content = '';
                foreach ($options_widget['tabs'] as $value) {
                    if (in_array($dept_id, $value['dept_list'])) {
                        $arrFields = $value['field_display'];
                        $template_content = $value['template_content'];
                        break;
                    }
                }

                $fieldName = \backend\modules\ezforms2\models\EzformFields::findAll(['ezf_field_id' => $arrFields]);
                $txtField = ['id'];
                foreach ($fieldName as $value) {
                    $txtField[] = $value['ezf_field_name'];
                }
//$dataPe = (new Query())->select($txtField)
                $dataPe = (new Query())->select('*')
                        ->from($tablePe)
                        ->where(['rstat' => '1', 'target' => $visitid])
                        ->one();
                
                $template_content = $this->removeAll($template_content);
                foreach ($fieldName as $value) {
                    if ($value['ezf_field_type'] !== 66) {
                        $path_items["{{$value['ezf_field_name']}}"] = $this->getValue($dataPe, $value) . '<br>';
                    } else {
                        $path_items["{{$value['ezf_field_name']}}"] = $this->getValue($dataPe, $value);
                    }
                }

                $template_content = strtr($template_content, $path_items);
                $template_content = self::removeTag($template_content);         
                //PE end
                if ($dataPe) {
                    $htmlTk = <<<HTML
               
                <table>
                     <tbody>
                        <tr>
                        <td width="20%"> <strong>History and Physical examination : </strong></td>
                             <td width="80%">
                                $template_content
                            </td>
                     
                        </tr>
                    </tbody>                
                </table>
HTML;
                    $pdf->writeHTML($htmlTk, true, false, false, false, '');
                    $pdf->writeHTML('<hr>', false, false, true, false, '');
                }
            }

            if ($txtDiTxt != '' || $txtDiIcd10 != '') {
                $htmlDiag = <<<HTML
                <table>
                    
                     <tbody>
                        <tr>
                        <td width="20%"><strong>Diagnosis : </strong></td>
                            <td width="80%">
                               $txtDiTxt
                               $txtDiIcd10
                            </td>
                        </tr>
                    </tbody>
                </table>
                
HTML;
//VarDumper::dump($htmlDiagAndPE);
                $pdf->writeHTML($htmlDiag, true, false, false, false, '');
                $pdf->writeHTML('<hr>', false, false, true, false, '');
            }

            if ($txtTreatConsult != '' || $txtTreatSendHosp != '' || $txtTreatMedCheck != '' || $txtTreatFuTime != '' || $txtTreatAdviceCheck != '' || $txtTreatAdvicedocTxt != '' || $txtTreatCommant != '') {
                $htmlTreat = <<<HTML
                <table>
                   
                     <tbody>
                        <tr>
                           <td width="20%"><strong>Treatment : </strong></td>
                           <td width="80%">
                                $txtTreatConsult
                                $txtTreatSendHosp
                                $txtTreatMedCheck
                                $txtTreatFuTime
                                $txtTreatAdviceCheck
                                $txtTreatAdvicedocTxt
                                $txtTreatCommant
                            </td>
                        </tr>
                    </tbody>
                </table>
                
HTML;

                $pdf->writeHTML($htmlTreat, true, false, false, false, '');
                $pdf->writeHTML('<hr>', false, false, true, false, '');
            }


            $dataOrder = \backend\modules\thaihis\classes\OrderFunc::getOrderTranReport($visitid);
            if ($dataOrder) {
                $txtOrder = '';
                if (is_array($dataOrder)) {
                    foreach ($dataOrder as $key => $val) {
                        $txtOrder .= $val['order_name'] . ' , ';
                    }
                }
                $InvestHtml = <<<HTML
 <table>
                   
                     <tbody>
                        <tr>
                           <td width="20%"><strong>Investigation : </strong></td>
                           <td width="80%">
                                $txtOrder
                            </td>
                        </tr>
                    </tbody>
                </table>
HTML;


                $pdf->writeHTML($InvestHtml, true, false, false, false, '');
                $pdf->writeHTML('<hr>', false, false, true, false, '');
            }


            $dataDrug = \backend\modules\pis\classes\PisFunc::getOrderTranReport($visitid);
            if ($dataDrug) {
                $txtDrug = '';
                if (is_array($dataDrug)) {
                    foreach ($dataDrug as $key => $val) {

                        $txtDrug .= $val['item_name'] . ' , ';
                    }
                }
                $drugHtml = <<<HTML
 <table>
                   
                     <tbody>
                        <tr>
                           <td width="20%"><strong>Drug  prescribe : </strong></td>
                           <td width="80%">
                                $txtDrug
                            </td>
                        </tr>
                    </tbody>
                </table>
HTML;
                $pdf->writeHTML($drugHtml, true, false, false, false, '');
                $pdf->writeHTML('<hr>', true, false, true, false, '');
            }

            $pdf->Ln(10);
            $pdf->SetFont('thsarabunpsk', 'B', 16);
            $pdf->Cell(165, 0, 'ลงชื่อ.....................................................', 0, 1, 'R', 0, '', 0, false, 'T', 'M');
            $doc_name = Yii::$app->user->identity->profile->title . ' ' . Yii::$app->user->identity->profile->firstname . ' ' . Yii::$app->user->identity->profile->lastname;
            $pdf->Cell(160, 0, '( ' . $doc_name . ' )', 0, 1, 'R', 0, '', 0, false, 'T', 'M');

            $pdf->Output('report.pdf', 'I');
            Yii::$app->end();
        } else {
            return $this->renderAjax('_btn-report', [
                        'ezf_main_id' => $ezf_main_id,
                        'ezf_ref_id' => $ezf_ref_id,
                        'condition' => $condition,
                        'group_by' => $group_by,
                        'target' => $target,
                        'reloadDiv' => $reloadDiv,
                        'btn_text' => $btn_text,
                        'btn_color' => $btn_color,
                        'btn_style' => $btn_style,
                        'btn_icon' => $btn_icon,
            ]);
        }
    }

}
