<?php

namespace backend\modules\pis\controllers;

use yii\web\Controller;
use Yii;
use backend\modules\patient\classes\PatientFunc;
use backend\modules\ezforms2\classes\EzfUiFunc;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use backend\modules\pis\classes\PisQuery;
use backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\thaihis\classes\ThaiHisQuery;
use backend\modules\ezforms2\classes\EzfFunc;
use backend\modules\pis\classes\PisFunc;

/**
 * Default controller for the `modules` module
 */
class PisItemOrderController extends Controller {

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex() {
        if (Yii::$app->getRequest()->isAjax) {
            $ezf_id = \backend\modules\patient\Module::$formID['pism_item'];
            $ezfOrder_id = \backend\modules\patient\Module::$formID['pis_order'];
            $ezfOrder_table = \backend\modules\patient\Module::$formTableName['pis_order'];
            $ezfOrderTran_id = \backend\modules\patient\Module::$formID['pis_order_tran'];
            $visit_id = Yii::$app->request->get('visitid');
            $ptid = Yii::$app->request->get('target');
            $options = Yii::$app->request->get('options');
            $user_id = Yii::$app->user->identity->profile;

            $options = EzfFunc::stringDecode2Array($options);
            $actionView = isset($options['action_view']) ? $options['action_view'] : null;
            if ($actionView == '2') { //order Cpoe
                if (Yii::$app->user->can('doctor') || Yii::$app->user->can('pharmacy')) {
                    if (isset($options['order_id']) && $options['order_id']) {
                        $model = EzfUiFunc::loadTbData($ezfOrder_table, $options['order_id']);
                    } else {
                        $model = PatientFunc::loadTbDataByField($ezfOrder_table, ['order_visit_id' => $visit_id,
                                    'order_doctor_id' => $user_id->user_id, //'order_no' => $order_no
                        ]);
                    }

                    $initdata = ['order_doctor_id' => $user_id->user_id,
                        'order_status' => '1',
                        'order_orderby' => '1',
                        'order_dept' => $user_id->department
                    ];
                } else {
                    //ถ้าไม่ใช้แพทย์ให้ออกไปเลย
                    return \yii\helpers\Html::tag('div', 'สิทธิของคุณไม่สามารถ สั่งยาได้'
                                    , ['class' => 'alert alert-danger'
                                , 'style' => 'margin: 20px;']);
                }
            } else { //Counter
                $model = PatientFunc::loadTbDataByField($ezfOrder_table, ['order_visit_id' => $visit_id,
                            'user_create' => $user_id->user_id,
                ]);
                $initdata = ['order_status' => '1', 'order_orderby' => '2', 'order_dept' => $user_id->department];
            }

            if (empty($model)) {
                $model = PatientFunc::BackgroundInsert($ezfOrder_id, '', $visit_id, $initdata)['data'];
            }
            $options['order_id'] = $model['id'];
            $right_ezf_id = \backend\modules\patient\Module::$formID['patientright'];
            $right_ezf_table = \backend\modules\patient\Module::$formTableName['patientright'];
            $dataRight = PatientFunc::loadDataByTarget($right_ezf_id, $right_ezf_table, $visit_id);

            $itemGroup = \yii\helpers\ArrayHelper::map(PisQuery::getDrugGroup(), 'drug_id', 'drug_group_name');
            $searchModel = \backend\modules\patient\classes\PatientFunc::getModel($ezf_id, '');

            return $this->renderAjax('index', [
                        'ezfOrder_id' => $ezfOrder_id, 'ezfOrderTran_id' => $ezfOrderTran_id,
                        'searchModel' => $searchModel,
                        'visit_id' => $visit_id,
                        'model' => $model,
                        'itemGroup' => $itemGroup,
                        'dataRight' => $dataRight,
                        'options' => $options, 'user_id' => $user_id->user_id, 'ptid' => $ptid
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionOrderLists() {
        if (Yii::$app->getRequest()->isAjax) {
            $ezfOrderTran_id = \backend\modules\patient\Module::$formID['pis_order_tran'];
            $ezf_id = \backend\modules\patient\Module::$formID['pism_item'];
            $reloadDiv = Yii::$app->request->get('reloadDiv');
            $right_code = Yii::$app->request->get('right_code');
            $ptid = Yii::$app->request->get('ptid');

            $options = Yii::$app->request->get('options');
            $view = Yii::$app->request->get('view');

            $options = EzfFunc::stringDecode2Array($options);
            $order_id = isset($options['order_id']) ? $options['order_id'] : '';

            $fields = isset($options['fields2']) ? $options['fields2'] : null;
            $forms = isset($options['refform2']) ? $options['refform2'] : null;
            $left_forms = isset($options['left_refform2']) ? $options['left_refform2'] : null;
            $item_form = isset($options['item_ezf_id']) ? $options['item_ezf_id'] : null;
            $conditions = isset($options['conditions']) ? $options['conditions'] : null;
            $image_field = isset($options['image_field']) ? $options['image_field'] : null;
            $fields_search = isset($options['fields_search']) ? $options['fields_search'] : null;

            $ezform = EzfQuery::getEzformOne($item_form);
            $trad_stdtrad_id = isset(Yii::$app->request->post()['EZ' . $ezform->ezf_id]) ? Yii::$app->request->post()['EZ' . $ezform->ezf_id] : null;

            $modelFilter[] = "generic_status = '1'";
            $modelFilter[] = "trad_status = '1'";
            $customSelect = [];
            $search = null;
            if (is_array($fields_search)) {
                $field_list = [];
                foreach ($fields_search as $val) {
                    $field_list[] = "IFNULL(" . $val . ",'')";
                }
                $search = join($field_list, ',');
                $modelFilter[] = "CONCAT(" . $search . ") LIKE '%" . $trad_stdtrad_id['trad_stdtrad_id'] . "%'";
            }
            if (isset($trad_stdtrad_id['trad_content']) && $trad_stdtrad_id['trad_content']) {
                $modelFilter[] = "generic_class_id = {$trad_stdtrad_id['trad_content']}";
            }
            $conditionList = null;
            if (isset($trad_stdtrad_id['trad_extra']) && $trad_stdtrad_id['trad_extra']) {
                $modelFilter[] = "generic_type = '2'";
            } else {
                $modelFilter[] = "generic_type = '1'";
                $conditionList = $conditions;
            }

            $customSelect[] = "CONCAT_WS(',','$ptid'," . $ezform['ezf_table'] . ".id,zdata_pism_generic.id,zdata_pism_generic.generic_class_id ) as item_allergy_id";
            $reponseQuery = PisQuery::getDynamicQuery($fields, $forms, $ezform, $conditionList, null, $image_field, $customSelect, $modelFilter, null, $left_forms);

            return $this->renderAjax('_order_list', [
                        'ezf_id' => $ezf_id,
//                        'searchModel' => $reponseQuery['searchModel'],
                        'dataProvider' => $reponseQuery['dataProvider'],
                        'reloadDiv' => $reloadDiv,
                        'order_id' => $order_id,
                        'right_code' => $right_code,
                        'view' => $view,
                        'ezfOrderTran_id' => $ezfOrderTran_id,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionGridOrder() {
        if (Yii::$app->getRequest()->isAjax) {
            $ezfOrder_id = \backend\modules\patient\Module::$formID['pis_order'];
            $reloadDiv = Yii::$app->request->get('reloadDiv');
            $visit_id = Yii::$app->request->get('visit_id');
            $ptid = Yii::$app->request->get('target');
            $right_code = Yii::$app->request->get('right_code');
            $options = Yii::$app->request->get('options');
            $user_id = Yii::$app->user->identity->profile->user_id;

            $options = EzfFunc::stringDecode2Array($options);
            $order_id = isset($options['order_id']) ? $options['order_id'] : NULL;
            $ezf_id = $options['ezf_id'];
            $fields = isset($options['fields']) ? $options['fields'] : null;
            $forms = isset($options['refform']) ? $options['refform'] : null;
            $left_forms = isset($options['left_refform']) ? $options['left_refform'] : null;
            $visit_form = isset($options['visit_form']) ? $options['visit_form'] : null;
            $order_status = isset($options['order_status']) ? $options['order_status'] : '';
            $actionView = isset($options['action_view']) ? $options['action_view'] : null;

            $ezform = EzfQuery::getEzformOne($ezf_id);

            $modelFilter = null;
            $view = '_gridorder2';
            if ($actionView == '1') { //order Counter                
                $view = '_gridorder_counter';
                $modelFilter[] = ['order_tran_status' => $options['order_tran_status']];

                $model = EzfUiFunc::loadTbData('zdata_pis_order', $order_id);
            } else { //order Cpoe                
                if (empty($order_id)) {
                    if (Yii::$app->user->can('doctor') || Yii::$app->user->can('pharmacy')) {
                        $model = PatientFunc::loadTbDataByField('zdata_pis_order', ['order_visit_id' => $visit_id
                                    , 'order_doctor_id' => $user_id]);
                    } else {
                        $model = PatientFunc::loadTbDataByField('zdata_pis_order', ['order_visit_id' => $visit_id]);
                    }
                } else {
                    $model = EzfUiFunc::loadTbData('zdata_pis_order', $order_id);
                }
                $view = '_gridorder2';
            }

            $ezformParent = Null;
            $targetField = EzfQuery::getTargetOne($ezform->ezf_id);
            if (isset($targetField)) {
                $ezformParent = EzfQuery::getEzformById($targetField->ref_ezf_id);
            }

            if (Yii::$app->user->can('doctor')) {
                $modelFilter[] = [$ezformParent['ezf_table'] . '.target' => $visit_id];
            } else {
                if (isset($model['id']) && $model['id'] != null)
                    $modelFilter[] = [$ezformParent['ezf_table'] . '.target' => $visit_id, $ezformParent['ezf_table'] . '.id' => $model['id']];
                else
                    $modelFilter[] = [$ezformParent['ezf_table'] . '.target' => $visit_id];
            }

            $reponseQuery = PisQuery::getDynamicQuery($fields, $forms, $ezform, null, null, null, null, $modelFilter, null, $left_forms);

            if (empty($right_code)) {
                $right_ezf_id = \backend\modules\patient\Module::$formID['patientright'];
                $right_ezf_table = \backend\modules\patient\Module::$formTableName['patientright'];
                $right_code = PatientFunc::loadDataByTarget($right_ezf_id, $right_ezf_table, $visit_id);
                $right_code = isset($right_code['right_code']) ? $right_code['right_code'] : 'CASH'; //ถ้าค้นหา สิทธิไม่เจอให้ใช่ CASH
            }

            return $this->renderAjax($view, [
                        'ezf_id' => $ezf_id,
                        'dataProvider' => $reponseQuery['dataProvider'],
                        'reloadDiv' => $reloadDiv,
                        'visit_id' => $visit_id,
                        'model' => $model,
                        'modelDyn' => $reponseQuery['modelDynamic'],
                        'user_id' => $user_id,
                        'visit_form' => $visit_form,
                        'fields' => $fields,
                        'ezfOrder_id' => $ezfOrder_id,
                        'actionView' => $actionView,
                        'ptid' => $ptid,
                        'order_status' => $order_status, 'right_code' => $right_code, 'options' => $options
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionOrderSave() {
        if (Yii::$app->getRequest()->isAjax) {
            $ezfOrderTran_id = \backend\modules\patient\Module::$formID['pis_order_tran'];
            $itemid = Yii::$app->request->get('itemid');
            $orderid = Yii::$app->request->get('orderid');
            $initdata = Yii::$app->request->get('initdata');
            $data = \backend\modules\ezforms2\classes\EzfFunc::stringDecode2Array($initdata);

            $data['order_tran_qty'] = '1';
            if ($data['order_tran_type_status'] == '1') {
                $data['order_tran_notpay'] = $data['unit_price'];
                $data['order_tran_pay'] = 0;
            } else {
                $data['order_tran_pay'] = $data['unit_price'];
                $data['order_tran_notpay'] = 0;
            }
            $result = EzfUiFunc::backgroundInsert($ezfOrderTran_id, '', $orderid, $data);

            Yii::$app->response->format = Response::FORMAT_JSON;
            return $result;
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionOrderChange($dataid, $qty, $right_code) {
        if (Yii::$app->getRequest()->isAjax) {
            $ezf_id = \backend\modules\patient\Module::$formID['pis_order_tran'];
            $ezf_table = \backend\modules\patient\Module::$formTableName['pis_order_tran'];

            $model = EzfUiFunc::loadTbData($ezf_table, $dataid);
            if ($model) {
                $data['order_tran_qty'] = $qty;
                if ($model['order_tran_type_status'] == '1') {
                    $data['order_tran_notpay'] = $qty * $model['unit_price'];
                } elseif ($model['order_tran_type_status'] == '2' && in_array($right_code, ['OFC', 'ORI', 'ORI', 'ORI-G'])) {
                    $data['order_tran_notpay'] = $qty * $model['unit_price'];
                } else {
                    $data['order_tran_pay'] = $qty * $model['unit_price'];
                }
                $result = PatientFunc::saveDataNoSys($ezf_id, $ezf_table, $dataid, $data);
            } else {
                $result = [
                    'status' => 'error',
                    'message' => \appxq\sdii\helpers\SDHtml::getMsgError() . Yii::t('patient', 'The transaction has been completed. Can not cancel'),
                ];
            }
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $result;
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionOrderApprove($order_id, $order_status) {
        if (Yii::$app->getRequest()->isAjax) {
            $ezf_id = \backend\modules\patient\Module::$formID['pis_order'];
            $ezf_table = \backend\modules\patient\Module::$formTableName['pis_order'];

            $data['order_status'] = $order_status;
            $result = PatientFunc::saveDataNoSys($ezf_id, $ezf_table, $order_id, $data);

            Yii::$app->response->format = Response::FORMAT_JSON;
            return $result;
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionOrderDelete($order_tran_id) {
        if (Yii::$app->getRequest()->isAjax) {
            $ezf_id = \backend\modules\patient\Module::$formID['pis_order_tran'];
            $ezf_table = \backend\modules\patient\Module::$formTableName['pis_order_tran'];

            $model = EzfUiFunc::loadTbData($ezf_table, $order_tran_id);
            if ($model['order_tran_status'] == '1') {
                $data['rstat'] = 3;
                $result = PatientFunc::saveDataNoSys($ezf_id, $ezf_table, $order_tran_id, $data);
            } else {
                $result = [
                    'status' => 'error',
                    'message' => \appxq\sdii\helpers\SDHtml::getMsgError() . Yii::t('patient', 'The transaction has been completed. Can not cancel'),
                ];
            }
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $result;
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionPrintOrderApprove($order_id) {
        $data = \backend\modules\pis\classes\PisQuery::getOrderTran($order_id);
        $pdf = new \common\lib\tcpdf\SDPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);
        $pdf->SetCreator('AppXQ');
        $pdf->SetAuthor('iencoded@gmail.com');
        $pdf->SetTitle('ใบสรุป Order ' . $data[0]['fullname']);
        $pdf->SetSubject('ใบสรุป Order');
        $pdf->SetKeywords('AppXQ, SDII, PDF, report, medical,');

        // remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // set margins
        $pdf->SetMargins(15, 15, 15, TRUE);
        // set auto page breaks
        $pdf->SetAutoPageBreak(FALSE, 0);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set font
        $pdf->fontSize = 15;
        $pdf->fontName = 'thsarabunpsk';
        $pdf->SetFont($pdf->fontName, '', $pdf->fontSize);

        // add a page
        $pdf->AddPage();
        $this->labelContent($pdf, $data);

        $pdf->Output('order.pdf', 'I');
        Yii::$app->end();
    }

    private function labelContent($pdf, $data) {
        $ezf_id = \backend\modules\patient\Module::$formID['pis_order_tran'];
        //ezform Hospital Config
        $dataid = \backend\modules\patient\Module::$dataidForm['hos_config'];
        $ezf_table = \backend\modules\patient\Module::$formTableName['hos_config'];
        $dataHosConfig = EzfUiFunc::loadTbData($ezf_table, $dataid);
        if ($dataHosConfig['logo_05']) {
            try {
                $path = Yii::getAlias('@storageUrl/ezform/fileinput') . '/' . $dataHosConfig['logo_05'];
            } catch (Exception $ex) {
                $path = Yii::getAlias('@storageUrl/images') . '/nouser.png';
            }
        } else {
            $path = Yii::getAlias('@storageUrl/images') . '/nouser.png';
        }

        $pdf->Image($path, 15, 15, 55, 17, 'JPG');
        $pdf->SetFont($pdf->fontName, 'B', 17);
        $pdf->ln(5);
        $pdf->Cell(180, 0, 'ใบสรุปรายการยา ', 0, 1, 'C', 0, '', 0, false, 'T', 'M');
        $pdf->ln(10);
        $pdf->SetFont($pdf->fontName, '', $pdf->fontSize);

        $text = $this->renderPartial('_report_order', ['data' => $data, 'ezf_id' => $ezf_id]);
//        \appxq\sdii\utils\VarDumper::dump($text);
        $pdf->writeHTMLCell(180, '', '', '', $text, 0, 1);
    }

    public function actionPrintOrderApprovePisPrint($order_id) {
        $data = \backend\modules\pis2\classes\PisQuery::getOrderTran($order_id, '2');
        $ptProfile = ThaiHisQuery::getPtProfile($data[0]['pt_id']);
        $pdf = new \common\lib\tcpdf\SDPDF('P', PDF_UNIT, [80, 150], true, 'UTF-8', false);
        $pdf->SetCreator('AppXQ');
        $pdf->SetAuthor('iencoded@gmail.com');
        $pdf->SetTitle('ใบสรุป Order ' . $ptProfile['fullname']);
        $pdf->SetSubject('Sticker');
        $pdf->SetKeywords('AppXQ, SDII, PDF, report, medical,');

        // remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // set margins
        $pdf->SetMargins(0, 4, 0, TRUE);
        // set auto page breaks
        $pdf->SetAutoPageBreak(FALSE, 0);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set font
        $pdf->fontSize = 12.5;
        $pdf->SetFont($pdf->fontName, '', $pdf->fontSize);

        // add a page
        $pdf->AddPage();
        $this->labelContentPisPrint($pdf, $data, $ptProfile);

        $pdf->Output('order.pdf', 'I');
        Yii::$app->end();
    }

    private function labelContentPisPrint($pdf, $data, $ptProfile) {
        $dataid = \backend\modules\patient\Module::$dataidForm['hos_config'];
        $ezf_table = \backend\modules\patient\Module::$formTableName['hos_config'];
        $dataHosConfig = EzfUiFunc::loadTbData($ezf_table, $dataid);
        if ($dataHosConfig['logo_05']) {
            try {
                $path = Yii::getAlias('@storageUrl/ezform/fileinput') . '/' . $dataHosConfig['logo_05'];
            } catch (Exception $ex) {
                $path = null;
            }
        } else {
            $path = null;
        }

        if ($path) {
            $pdf->Image($path, 2, 2, 18, 18, 'JPG');
        }

        $pdf->SetFont('thsarabunpsk', 'B', $pdf->fontSize);
        $pdf->Cell(20, 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(60, 0, 'UDONTHANI CANCER HOSPITAL', 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(20, 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(60, 0, 'โรงพยาบาลมะเร็งอุดรธานี ', 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(20, 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(60, 0, 'กรมการแพทย์ กระทรวงสาธารณสุข', 0, 1, 'L', 0, '', 0, false, 'T', 'M');

        $pdf->SetFont($pdf->fontName, 'U', $pdf->fontSize);
        $pdf->Cell(0, $pdf->spaceHigh, 'ข้อมูลผู้รับบริการ', 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        //profile       
        $pdf->SetFont('thsarabunpsk', '', $pdf->fontSize);
        $pdf->Cell(14, 0, $ptProfile['pt_hn'], 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont('thsarabunpsk', 'B', $pdf->fontSize);
        $pdf->Cell(48, 0, $ptProfile['fullname'], 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont('thsarabunpsk', '', $pdf->fontSize);
        $pdf->Cell(18, 0, \appxq\sdii\utils\SDdate::mysql2phpThDateSmall($data[0]['receive_date']), 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        //drug name
        foreach ($data as $value) {
            $pdf->SetFont('thsarabunpsk', 'B', $pdf->fontSize);
            $pdf->MultiCell(80, 0, $value['item_name'], 'T', 'L', 0, 1, '', '', false, 0, false, false, 0, 'T', false);
            //drug label
            $pdf->SetFont('thsarabunpsk', '', $pdf->fontSize);
            $pdf->Cell(80, 0, 'จำนวน ' . $value['order_tran_qty'] . ' ' . $value['item_content'], 'T', 1, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->MultiCell(80, 0, $value['label_1'] . ' ' . $value['label_2'], 0, 'L', 0, 1, '', '', false, 0, false, false, 0, 'T', false);
            //note
            if ($value['order_tran_note']) {
                $pdf->MultiCell(80, 0, $value['order_tran_note'], 0, 'L', 0, 1, '', '', false, 0, false, false, 0, 'T', false);
            }
        }

        $pdf->ln();
        $pdf->Cell(80, 0, 'แพทย์ผู้สั่ง................................................', 0, 1, 'C', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(80, 0, '(  ' . $data[0]['doctor_name'] . '  )', 0, 1, 'C', 0, '', 0, false, 'T', 'M');
    }

    public function actionOpenOrder($visitid, $options, $dataid) {
        $ezfOrder_id = \backend\modules\patient\Module::$formID['pis_order'];

        return $this->renderAjax('_open_order', [
                    'visit_id' => $visitid,
                    'options' => $options,
                    'order_id' => $dataid, 'ezfOrder_id' => $ezfOrder_id
        ]);
    }

    public function actionPackageLists($user_id, $visit_id, $order_id) {
        if (Yii::$app->getRequest()->isAjax) {
            $options = Yii::$app->request->get('options');
            $right_code = Yii::$app->request->get('right_code');

            $q = Yii::$app->request->get('q');
            $dataProvider = PisFunc::getPisPackage($user_id, $q);

            return $this->renderAjax('_package_list', [
                        'dataProvider' => $dataProvider
                        , 'user_id' => $user_id
                        , 'visit_id' => $visit_id
                        , 'order_id' => $order_id
                        , 'q' => $q
                        , 'options' => $options
                        , 'right_code' => $right_code
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionOrderHistory($ptid, $q) {
        if (Yii::$app->getRequest()->isAjax) {
            $visit_id = Yii::$app->request->get('visit_id');
            $order_id = Yii::$app->request->get('order_id');
            $options = Yii::$app->request->get('options');
            $right_code = Yii::$app->request->get('right_code');

            $dataProvider = PisFunc::getOrderHistoryByPtid($ptid, $q);

            return $this->renderAjax('_history_lists', [
                        'dataProvider' => $dataProvider
                        , 'visit_id' => $visit_id
                        , 'order_id' => $order_id
                        , 'options' => $options
                        , 'right_code' => $right_code, 'ptid' => $ptid
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionPackageGridItem($item_dataid) {
        if (Yii::$app->getRequest()->isAjax) {
            $ezf_id = \backend\modules\patient\Module::$formID['pis_package_item'];
            $action = Yii::$app->request->get('action');
            $mode = Yii::$app->request->get('mode');
            if ($mode == 'PACKAGE') {
                $dataProvider = PisFunc::getItemPackage($item_dataid); //มันคือ package id
            } elseif ($mode == 'VISIT') {
                $dataProvider = PisFunc::getItemVisit($item_dataid);  //มันคือ visit id
            }


            return $this->renderAjax('_package_grid', [
                        'dataProvider' => $dataProvider
                        , 'action' => $action
                        , 'ezf_id' => $ezf_id
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionPackageShowItems() {
        if (Yii::$app->getRequest()->isAjax) {
            $ezf_id = \backend\modules\patient\Module::$formID['pis_package_item'];
            $item_dataid = Yii::$app->request->get('item_dataid', Yii::$app->request->get('dataid'));
            $order_id = Yii::$app->request->get('order_id');
            $visit_id = Yii::$app->request->get('visit_id');
            $right_code = Yii::$app->request->get('right_code');
            $action = Yii::$app->request->get('action');
            $mode = Yii::$app->request->get('mode');
            $reloadDivOrder = 'pis-item-order';
            $options = Yii::$app->request->get('options');

            $itemGroup = [];
            if ($action <> 'SELECT') {
                $itemGroup = \yii\helpers\ArrayHelper::map(PisQuery::getDrugGroup(), 'drug_id', 'drug_group_name');
            }
            return $this->renderAjax('index_package', [
                        'item_dataid' => $item_dataid
                        , 'ezf_id' => $ezf_id
                        , 'itemGroup' => $itemGroup
                        , 'action' => $action
                        , 'order_id' => $order_id
                        , 'reloadDivOrder' => $reloadDivOrder
                        , 'right_code' => $right_code
                        , 'visit_id' => $visit_id
                        , 'mode' => $mode
                        , 'options' => $options
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionPackageAddOrder() {
        if (Yii::$app->getRequest()->isAjax) {
            $ezf_id = \backend\modules\patient\Module::$formID['pis_package_item'];
            $ezfOrderTran_id = \backend\modules\patient\Module::$formID['pis_order_tran'];
            $visit_id = Yii::$app->request->get('visit_id');
            $order_id = Yii::$app->request->get('order_id');
            $item_dataid = Yii::$app->request->get('item_dataid');
            $right_code = Yii::$app->request->get('right_code');
            $mode = Yii::$app->request->get('mode');
            $user_id = Yii::$app->user->identity->profile->user_id;

            if ($mode == 'PACKAGE') {
                $dataItem = PisFunc::getItemPackage($item_dataid)->models; //มันคือ package id
            } elseif ($mode == 'VISIT') {
                $dataItem = PisFunc::getItemVisit($item_dataid)->models;  //มันคือ visit id
            }

            $dataOrder = PisQuery::getOrderByDocId($user_id, $visit_id);
            foreach ($dataItem as $value) {
                if (!in_array($value['item_id'], $dataOrder)) {
                    $not_pay = 0;
                    $pay = 0;
                    if ($value['order_tran_type_status'] == '1') {
                        $not_pay = $value['order_tran_qty'] * $value['trad_price'];
                    } elseif ($value['order_tran_type_status'] == '2' && in_array($right_code, ['OFC', 'ORI', 'ORI', 'ORI-G'])) {
                        $not_pay = $value['order_tran_qty'] * $value['trad_price'];
                    } else {
                        $pay = $value['order_tran_qty'] * $value['trad_price'];
                    }

                    $initdata = [
                        'order_trad_id' => $value['drug_item_id'],
                        'order_generic_id' => $value['trad_generic_id'],
                        'order_tran_status' => '1',
                        'order_tran_pertime' => $value['order_tran_pertime'],
                        'order_tran_unit_id' => $value['order_tran_unit_id'],
                        'order_tran_use_id' => $value['order_tran_use_id'],
                        'order_tran_timeframe_id' => $value['order_tran_timeframe_id'],
                        'order_tran_usetime_id' => $value['order_tran_usetime_id'],
                        'order_tran_note' => $value['order_tran_note'],
                        'order_tran_label' => $value['order_tran_label'],
                        'unit_price' => $value['trad_price'],
                        'order_tran_type_status' => $value['order_tran_type_status'],
                        'order_tran_right_code' => $right_code,
                        'order_tran_cashier_status' => '1',
                        'order_tran_day' => $value['order_tran_day'],
                        'order_tran_qty' => $value['order_tran_qty'],
                        'order_tran_pay' => "$pay",
                        'order_tran_notpay' => "$not_pay",
                        'order_tran_drugtype' => $value['order_tran_drugtype'],
                        'order_tran_zeropay_status' => $value['order_tran_zeropay_status'],
                        'order_tran_zeropay_type' => $value['order_tran_zeropay_type'],
                        'order_tran_chemo_cal' => $value['order_tran_chemo_cal'],
                        'order_tran_chemo_amount' => $value['order_tran_chemo_amount'],
                        'order_tran_chemo_result' => $value['order_tran_chemo_result'],
                    ];

                    PatientFunc::backgroundInsert($ezfOrderTran_id, '', $order_id, $initdata);
                }
            }

            Yii::$app->response->format = Response::FORMAT_JSON;
            return $result = [
                'status' => 'success', 'message' => \appxq\sdii\helpers\SDHtml::getMsgSuccess() . 'Data success'];
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionOpenPackageShowItems() {
        if (Yii::$app->getRequest()->isAjax) {
            $item_dataid = Yii::$app->request->get('item_dataid', Yii::$app->request->get('dataid'));
            $order_id = Yii::$app->request->get('order_id');
            $visit_id = Yii::$app->request->get('visit_id');
            $right_code = Yii::$app->request->get('right_code');
            $action = Yii::$app->request->get('action');
            $mode = Yii::$app->request->get('mode');
            $options = Yii::$app->request->get('options');


            return $this->renderAjax('_open_package_item', [
                        'item_dataid' => $item_dataid
                        , 'action' => $action
                        , 'order_id' => $order_id
                        , 'right_code' => $right_code
                        , 'visit_id' => $visit_id
                        , 'mode' => $mode
                        , 'options' => $options
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionPackageProfile() {
        if (Yii::$app->getRequest()->isAjax) {
            $item_dataid = Yii::$app->request->get('item_dataid', Yii::$app->request->get('dataid'));
            $reloadDiv = Yii::$app->request->get('reloadDiv');
            $modal = Yii::$app->request->get('modal');

            return $this->renderAjax('_package_profile', [
                        'item_dataid' => $item_dataid
                        , 'reloadDiv' => $reloadDiv
                        , 'modal' => $modal
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public static function replaceTagP($text) {

        $text = str_replace("</p>", "", str_replace("<p>", "<br/>", $text));
        $text = str_replace("</div>", "", str_replace("<div>", "<br/>", $text));
        $text = str_replace('"=', '=', $text);
        $text = str_replace('<p ="">', '<br/>', $text);
        $text = str_replace('<br>', '', $text);

        return $text;
//        $pdf->writeHTML($text, true, false, true, false, '');
    }

    public function actionGetChemoCal($visitid, $formula, $calVal) {
        if (Yii::$app->getRequest()->isAjax) {
            $data = PisQuery::getBmiCal($visitid);
            if ($data) {
                return $this->renderAjax('_chemo_cal', ['data' => $data, 'formula' => $formula, 'calVal' => $calVal]);
            } else {
                return \yii\helpers\Html::tag('div', 'ไม่พบข้อมูล น้ำหนัก,ส่วนสูง กรุณาบันทึกข้อมูลน้ำหนักส่วนสูงก่อน', ['class' => 'alert alert-danger']);
            }
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

}
