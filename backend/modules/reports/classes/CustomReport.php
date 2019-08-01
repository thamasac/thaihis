<?php

namespace backend\modules\reports\classes;

use Yii;

class CustomReport {

    /**
     * 
     * @param type $ezfId ezf_id ezform
     * @param type $dataId zdata_xxx id 
     * @return object
     */
    public static function getEzfData($ezfId, $dataId = "") {
        $dataForm = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($ezfId);
        if (!$dataForm) {
            return false;
        }
        $version = (isset($_GET['v']) && $_GET['v'] != '') ? $_GET['v'] : isset($dataForm->ezf_version) ? $dataForm->ezf_version : '';
        $ezField = isset($dataForm->ezf_id) ? \backend\modules\ezforms2\classes\EzfQuery::getFieldAll($dataForm->ezf_id, $version) : '';
        if (!$dataId) {
            return $ezField;
        }

        $ezfTable = isset($dataForm['ezf_table']) ? $dataForm['ezf_table'] : $dataForm['ezf_table'];
        $ezfData = \backend\modules\ezforms2\classes\EzfUiFunc::loadTbData($ezfTable, $dataId);
        $output = [];
        $dataKey = [];
        $outputArr = [];

        Yii::$app->session['show_varname'] = 0;
        Yii::$app->session['ezf_input'] = \backend\modules\ezforms2\classes\EzfQuery::getInputv2All();

        $userProfile = isset(Yii::$app->user->identity->profile) ? Yii::$app->user->identity->profile : '';
        $model = \backend\modules\ezforms2\classes\EzfFunc::setDynamicModel($ezField, $dataForm->ezf_table, Yii::$app->session['ezf_input'], Yii::$app->session['show_varname']);
        if ($ezfData) {
            $model->attributes = isset($ezfData->attributes) ? $ezfData->attributes : '';
        }

        foreach ($ezField as $key => $value) {
            $fieldName = isset($value['ezf_field_name']) ? $value['ezf_field_name'] : '';
            $var = isset($value['ezf_field_name']) ? $value['ezf_field_name'] : '';
            $label = isset($value['ezf_field_label']) ? $value['ezf_field_label'] : '';

            $dataInput;
            if (isset(Yii::$app->session['ezf_input'])) {
                $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($value['ezf_field_type'], Yii::$app->session['ezf_input']);
            }

            $data = \backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $value, $model);
            $output[$fieldName] = $data;
            $dataKey[$key] = ['name' => $fieldName, 'label' => $label];
        }
        $outputArr = [
            'output' => $output,
            'dataKey' => $dataKey,
            'ezfStruc' => $dataForm
        ];
//        \appxq\sdii\utils\VarDumper::dump($outputArr);
        return $outputArr;
    }

    /**
     * 
     * @param type $visit_id
     * @return boolean|array
     */
    public static function getPatientprofile($visit_id) {
        try {

            $output = [];
            $sql = "
                SELECT CONCAT(prefix_name_cid,pt_firstname,' ',pt_lastname) AS fullname,zv.visit_date,zvt.visit_type_name 
                ,zr.right_name , zpp.pt_hn as hn , zpp.pt_bdate, zpp.pt_cid as cid
                FROM zdata_visit zv  
                INNER JOIN zdata_visit_type zvt ON(zvt.visit_type_code=zv.visit_type) 
                INNER JOIN zdata_patientright zpr ON(zpr.right_visit_id=zv.id)  
                LEFT JOIN zdata_right zr ON(zr.right_code=zpr.right_code)  
                INNER JOIN zdata_patientprofile zpp ON(zpp.id=zv.ptid)  
                LEFT JOIN zdata_prefix zpf ON(zpf.prefix_id=zpp.pt_prefix_id)  
                WHERE zv.id=:id
            ";
            $data = \Yii::$app->db->createCommand($sql, [':id' => $visit_id])->queryOne();
            if (!$data) {
                return false;
            }
            $output = [];
            $output['visit_type_name'] = isset($data['visit_type_name']) ? $data['visit_type_name'] : '';
            $output['visit_date'] = isset($data['visit_date']) && $data['visit_date'] != '' ? \appxq\sdii\utils\SDdate::mysql2phpThDateTime($data['visit_date']) : "";
            $output['hn'] = isset($data['hn']) ? $data['hn'] : '';
            $output['fullname'] = $data['fullname'];
            $output['visit_type_name'] = isset($data['visit_type_name']) ? $data['visit_type_name'] : '';
            $output['right_name'] = isset($data['right_name']) ? $data['right_name'] : '';
            $output['pt_bdate'] = isset($data['pt_bdate']) ? $data['pt_bdate'] : '';
            $output['cid'] = isset($data['pt_bdate']) ? $data['cid'] : '';
            //กรณีไม่เจอ ไม่พบข้อมูลสิทธ์ กรุณาติดต่อห้องตรวจสิทธิ์!
            $title = "";
            return $output;
        } catch (\yii\db\Exception $ex) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
            return false;
        }
    }

    /**
     * 
     * @param type $print 1 preview pdf 2 save file pdf return json data location pdf file
     * @param type $layout P = Vertical L horizontal
     * @param type $paperSize  A4 or custom page 130,90
     * @param type $title set Title pdf
     * @param type $template html editor 
     * @return pdf preview or save json data
     */
    public static function printPDF($print, $layout, $paperSize, $title, $template, $ezfId, $dataId, $hn = '') {
        $viewPath = \Yii::getAlias('@storageUrl');
        $path = \Yii::getAlias('@storage') . "/web/source";
        // \appxq\sdii\utils\VarDumper::dump($paperSize);
        $pdf = new \common\lib\tcpdf\SDPDF($layout, PDF_UNIT, $paperSize, true, 'UTF-8', false);
        $pdf->SetCreator('AppXQ');
        $pdf->SetAuthor('iencoded@gmail.com');
        $pdf->SetTitle($title);
        $pdf->SetSubject('Original');
        $pdf->SetKeywords('AppXQ, SDII, PDF, report, medical, clinic');

        // remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // set margins
        $pdf->SetMargins(5, 5, 0);
        $pdf->SetHeaderMargin(10);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, 3);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set font
        $pdf->SetFont($pdf->fontName, '', $pdf->fontSize);
        $pdf->fontSize = 15;
        $style = [
            'position' => '',
            'align' => 'C',
            'stretch' => false,
            'fitwidth' => true,
            'cellfitalign' => '',
            // 'border' => true,
            'hpadding' => 'auto',
            'vpadding' => 'auto',
            // 'fgcolor' => array(0, 0, 0),
            // 'bgcolor' => false, //array(255,255,255),
            'text' => true,
            'font' => 'helvetica',
            'fontsize' => 8,
            'stretchtext' => 4
        ];

        $pdf->AddPage();


        $html = "";
        $html .= $template;

        $pdf->writeHTML($html, true, false, true, false, '');
        
        $fileName = \appxq\sdii\utils\SDUtility::getMillisecTime();

        try {
            $sql = "INSERT INTO tbl_files(filename, path,create_date) VALUES(:filename, :path, :create_date)";
            Yii::$app->db->createCommand($sql, [
                ':filename' => "{$fileName}.pdf",
                ':path' => "{$viewPath}/source/",
                ':create_date' => date('Y-m-d')
            ])->execute();
        } catch (\yii\db\Exception $ex) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
        }

        if ($print == "1") {//preview
            $pdf->write1DBarcode('H6100004', 'C128', '', '', '', 17, 0.5, $style, 'N');
            $pdf->SetFont('thsarabunpsk', 'B', 8);

            $file = "{$path}/{$fileName}.pdf";
            $pdf->Output($file, 'I');
        } else if ($print == "2") {//save file storage/web/source
            if ($hn != '') {
                $pdf->write1DBarcode(isset($hn) ? $hn : '1234', 'C128', '', '', '', 17, 0.5, $style, 'N');
                $pdf->SetFont('thsarabunpsk', 'B', 8);
            }
            $file = "{$path}/{$fileName}.pdf";
            $pdf->Output($file, 'F');
            $out = [
                'success' => true,
                'data' => [
                    'ezf_id' => $ezfId,
                    'data_id' => $dataId,
                    'path' => "{$viewPath}/source/",
                    'fileName' => "{$fileName}.pdf",
                ]
            ];
            return json_encode($out);
//                \appxq\sdii\utils\VarDumper::dump($out);
        } else {
            $pdf->Output();
        }
    }

}
