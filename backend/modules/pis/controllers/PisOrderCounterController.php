<?php

namespace backend\modules\pis\controllers;

use yii\web\Controller;
use Yii;
use backend\modules\patient\classes\PatientFunc;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use backend\modules\pis\classes\PisQuery;

/**
 * Default controller for the `modules` module
 */
class PisOrderCounterController extends Controller {

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex() {
        Yii::$app->session['ezf_input'] = \backend\modules\ezforms2\classes\EzfQuery::getInputv2All();
        $reloadDiv = Yii::$app->request->get('reloadDiv');
        $order_id = Yii::$app->request->get('order_id');
        $ezf_id = \backend\modules\patient\Module::$formID['pis_order_tran'];

        return $this->render('index', [
                    'reloadDiv' => $reloadDiv,
                    'order_id' => $order_id,
                    'ezf_id' => $ezf_id
        ]);
    }

    public function actionOrderQue() {
        $ezf_id = \backend\modules\patient\Module::$formID['pis_order'];
        $reloadDiv = Yii::$app->request->get('reloadDiv');
        $order_id = Yii::$app->request->get('order_id');

        $searchModel = \backend\modules\patient\classes\PatientFunc::getModel($ezf_id, '');
        $searchModel['create_date'] = date('Y-m-d');
        $searchModel['order_status'] = '1';
        $dataProvider = \backend\modules\pis\classes\PisFunc::getCounterQue($searchModel, Yii::$app->request->get());

        return $this->renderAjax('_que', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'reloadDiv' => $reloadDiv,
                    'order_status' => $searchModel['order_status'],
        ]);
    }

    public function actionOrderTran($order_id, $order_status) {
        if (Yii::$app->getRequest()->isAjax) {
            $ezf_id = \backend\modules\patient\Module::$formID['pis_order_tran'];
            $data = \backend\modules\pis\classes\PisQuery::getOrderTran($order_id, $order_status);

            return $this->renderAjax('_gridorder_receive', [
                        'order_id' => $order_id,
                        'order_status' => $order_status,
                        'data' => $data,
                        'ezf_id' => $ezf_id,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionDrugAllergyPt() {
        if (Yii::$app->getRequest()->isAjax) {
            $ezf_id = \backend\modules\patient\Module::$formID['pis_allergy'];
            $reloadDiv = Yii::$app->request->get('reloadDiv');
            $btnDisabled = Yii::$app->request->get('btnDisabled');
            $pt_id = Yii::$app->request->get('pt_id');

            $data = \backend\modules\pis\classes\PisQuery::getDrugAllergyShow($pt_id);

            return $this->renderAjax('_drug_allergy', [
                        'ezf_id' => $ezf_id,
                        'data' => $data,
                        'reloadDiv' => $reloadDiv,
                        'btnDisabled' => $btnDisabled,
                        'pt_id' => $pt_id,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionOrderTranSave($order_id, $order_status) {
        if (Yii::$app->request->post()) {
            $ezf_id = \backend\modules\patient\Module::$formID['pis_order_tran'];
            $ezf_table = \backend\modules\patient\Module::$formTableName['pis_order_tran'];
            $order = Yii::$app->request->post('order_check', []);
            $order_id = Yii::$app->request->get('order_id');

            if ($order_status == '1') {
                if (!empty($order)) {
                    if ($order_id) {
                        $ezfPisOrder_id = \backend\modules\patient\Module::$formID['pis_order'];
                        $ezfPisOrder_table = \backend\modules\patient\Module::$formTableName['pis_order'];
                        PatientFunc::saveDataNoSys($ezfPisOrder_id, $ezfPisOrder_table, $order_id, ['order_status' => '2']);
                    }
                    foreach ($order as $dataid) {
                        $data['order_tran_status'] = '2';
                        $result = PatientFunc::saveDataNoSys($ezf_id, $ezf_table, $dataid, $data);
                    }
                } else {
                    $result = [
                        'status' => 'error',
                        'message' => \appxq\sdii\helpers\SDHtml::getMsgError() . 'คุณไม่ได้เลือกรายการ',
                    ];
                }
            } else {
                if (!empty($order)) {
                    foreach ($order as $dataid) {
                        $data['order_tran_status'] = '1';
                        $result = PatientFunc::saveDataNoSys($ezf_id, $ezf_table, $dataid, $data);
                    }
                } else {
                    $result = [
                        'status' => 'error',
                        'message' => \appxq\sdii\helpers\SDHtml::getMsgError() . 'คุณไม่ได้เลือกรายการ ที่จะยกเลิก',
                    ];
                }
            }

            Yii::$app->response->format = Response::FORMAT_JSON;
            return $result;
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionPrintLabel($order_id, $order_status) {
        $data = PisQuery::getOrderTran($order_id, $order_status);
        $ptProfile = \backend\modules\thaihis\classes\ThaiHisQuery::getPtProfile($data[0]['pt_id']);
        $pdf = new \common\lib\tcpdf\SDPDF('L', PDF_UNIT, [80, 48], true, 'UTF-8', false);
        $pdf->SetCreator('AppXQ');
        $pdf->SetAuthor('iencoded@gmail.com');
        $pdf->SetTitle('Sticker ' . $ptProfile['fullname']);
        $pdf->SetSubject('Sticker');
        $pdf->SetKeywords('AppXQ, SDII, PDF, report, medical, label, sticker');

        // remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // set margins
        $pdf->SetMargins(0, 11, 0, TRUE);
        // set auto page breaks
        $pdf->SetAutoPageBreak(FALSE, 0);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set font
        $pdf->fontSize = 12.5;
        $pdf->SetFont($pdf->fontName, '', $pdf->fontSize);

        // add a page
        foreach ($data as $value) {
            $pdf->AddPage();
            $this->labelContent($pdf, $value, $ptProfile);
        }

        $pdf->Output('receipt.pdf', 'I');
        Yii::$app->end();
    }

    private function labelContent($pdf, $data, $ptProfile) {
        //profile       
        $pdf->SetFont('thsarabunpsk', '', $pdf->fontSize);
        $pdf->Cell(14, 0, $ptProfile['pt_hn'], 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont('thsarabunpsk', 'B', $pdf->fontSize);
        $pdf->Cell(48, 0, $ptProfile['fullname'], 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont('thsarabunpsk', '', $pdf->fontSize);
        $pdf->Cell(18, 0, \appxq\sdii\utils\SDdate::mysql2phpThDateSmall($data['receive_date']), 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        //drug name
        $pdf->SetFont('thsarabunpsk', 'B', $pdf->fontSize);
        $pdf->Cell(70, 0, $data['trad_itemname'], 'LTB', 0, 'L', 0, 0, 1, false, 'T', 'M');
        $pdf->Cell(10, 0, $data['order_tran_qty'], 'TBR', 1, 'L', 0, '', 0, false, 'T', 'M');
        //drug label_1
        $pdf->SetFont('thsarabunpsk', '', $pdf->fontSize);
        $pdf->Cell(80, 0, $data['label_1'], 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        //drug label_2
        $pdf->Cell(80, 0, $data['label_2'], 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        //note
        //$pdf->Cell(80, 0, $data['order_tran_note'], 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        if ($data['order_tran_note']) {
            $pdf->MultiCell(80, 0, $data['order_tran_note'], 0, 'L', 0, 1, '', '', false, 0, false, false, 0, 'T', false);
        }
        //comment drug info
        $pdf->MultiCell(80, 0, $data['generic_info'], 0, 'L', 0, 1, '', '', false, 0, false, false, 0, 'T', false);
//        $pdf->Cell(80, 0, $data['generic_info'], 0, 0, 'L', 0, '', 0, false, 'T', 'M');
//        $pdf->Cell(10, 0, 'หน้าที่ ' . $pdf->getAliasNumPage() . '/' . $pdf->getAliasNbPages(), 0, 1, 'R', 0, '', 0, false, 'T', 'M');
    }

    public function actionDrugFullname($itemid) {
        if (Yii::$app->getRequest()->isAjax) {
            $data = PisQuery::getItem($itemid);

            Yii::$app->response->format = Response::FORMAT_JSON;
            return $data;
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

}
