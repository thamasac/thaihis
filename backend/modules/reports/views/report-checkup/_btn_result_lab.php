<?php

$append = '';
$cyto = '';
$cytoAppend = '';
$chem = '';
$sero = '';
$checkup = FALSE;
if ($data) {
//    appxq\sdii\utils\VarDumper::dump($data,1,0);
    if ($data[0]['visit_type'] == '1') {
        $checkup = TRUE;
    }
    foreach ($data as $value) {
        if ($value['order_tran_code'] == 'CH') {
            $append .= "$.get('/cpoe/report-checkup/result-xray',{visit_id:visit_id,order_code:'CH'}).done(function(result) {
                            $('#ez1514016599071774100-ckr_cxr').append(result);
                        }); ";
            $append .= "$('[item-id=1514028729000035700]').removeClass('hidden'); ";
        }
        if ($value['order_tran_code'] == 'FE001') {
            $btn = yii\helpers\Html::a('<span class="fa fa-bars"></span>', 'javascript:void(0)', ['class' => 'btn btn-sm btn-success ezform-main-open', 'data-modal' => 'modal-ezform-main',
                        'data-url' => yii\helpers\Url::to(['/patient/order/result-lab-show', 'pt_id' => $value['ptid'], 'pt_hn' => $dataProfile['pt_hn'],
                            'visit_id' => $value['order_visit_id'], 'view' => 'modal', 'secname' => 'Stool Exam', 'date' => $value['visit_date']])]);
            $append .= "$('#ez1514016599071774100-ckr_se').append('$btn'); $('[item-id=1514028609024278500]').removeClass('hidden'); ";
        }
        if ($value['order_tran_code'] == 'HM001') {
            //CBC
            $btn = yii\helpers\Html::a('<span class="fa fa-bars"></span>', 'javascript:void(0)', ['class' => 'btn btn-sm btn-success ezform-main-open', 'data-modal' => 'modal-ezform-main',
                        'data-url' => yii\helpers\Url::to(['/patient/order/result-lab-show', 'pt_id' => $value['ptid'], 'pt_hn' => $dataProfile['pt_hn'],
                            'visit_id' => $value['order_visit_id'], 'view' => 'modal', 'secname' => 'CBC', 'date' => $value['visit_date']])]);
            $append .= "$('#ez1514016599071774100-ckr_cbc').append('$btn'); $('[item-id=1514017604017181300]').removeClass('hidden'); ";
            //All Lab
            $txtVisitDate = appxq\sdii\utils\SDdate::mysql2phpThDate($value['visit_date']);
            $btn = yii\helpers\Html::a('View all <span class="fa fa-bars"></span>', 'javascript:void(0)', ['class' => 'btn btn-sm btn-success ezform-main-open', 'data-modal' => 'modal-ezform-main',
                        'data-url' => yii\helpers\Url::to(['/patient/order/result-lab-show', 'pt_id' => $value['ptid'], 'pt_hn' => $dataProfile['pt_hn'],
                            'visit_id' => $value['order_visit_id'], 'view' => 'modal', 'secname' => '', 'date' => $value['visit_date']])]);
            $append .= "$('#ezform-1514016599071774100 #itemModalLabel small') . html('$btn '+' $txtVisitDate'); ";
        }
        if ($value['order_tran_code'] == 'UR001') {
            $btn = yii\helpers\Html::a('<span class="fa fa-bars"></span>', 'javascript:void(0)', ['class' => 'btn btn-sm btn-success ezform-main-open', 'data-modal' => 'modal-ezform-main',
                        'data-url' => yii\helpers\Url::to(['/patient/order/result-lab-show', 'pt_id' => $value['ptid'], 'pt_hn' => $dataProfile['pt_hn'],
                            'visit_id' => $value['order_visit_id'], 'view' => 'modal', 'secname' => 'Urine Exam', 'date' => $value['visit_date']])]);
            $append .= "$('#ez1514016599071774100-ckr_ua').append('$btn'); $('[item-id=1514018002039290200]').removeClass('hidden'); ";
        }
        //ตรวจเพิ่มพิเศษ
        if ($value['order_tran_code'] == 'IM006') {
            $btn = yii\helpers\Html::a('<span class="fa fa-bars"></span>', 'javascript:void(0)', ['class' => 'btn btn-sm btn-success ezform-main-open', 'data-modal' => 'modal-ezform-main',
                        'data-url' => yii\helpers\Url::to(['/patient/order/result-lab-show', 'pt_id' => $value['ptid'], 'pt_hn' => $dataProfile['pt_hn'],
                            'visit_id' => $value['order_visit_id'], 'view' => 'modal', 'secname' => 'Serology', 'test_code' => '3001', 'date' => $value['visit_date']])]);
            $append .= "$('#ez1514016599071774100-ckr_rpr').append('$btn'); $('[item-id=1514967751079912300]').removeClass('hidden'); ";
        }
        if ($value['order_tran_code'] == 'IM002') {
            $btn = yii\helpers\Html::a('<span class="fa fa-bars"></span>', 'javascript:void(0)', ['class' => 'btn btn-sm btn-success ezform-main-open', 'data-modal' => 'modal-ezform-main',
                        'data-url' => yii\helpers\Url::to(['/patient/order/result-lab-show', 'pt_id' => $value['ptid'], 'pt_hn' => $dataProfile['pt_hn'],
                            'visit_id' => $value['order_visit_id'], 'view' => 'modal', 'secname' => 'Serology', 'test_code' => '3010', 'date' => $value['visit_date']])]);
            $append .= "$('#ez1514016599071774100-ckr_hba').append('$btn'); $('[item-id=1514967695061503200]').removeClass('hidden'); ";
        }
        if ($value['order_tran_code'] == 'PH001') {
            $ezf_id = \backend\modules\patient\Module::$formID['report_ekg'];
            $ezf_table = \backend\modules\patient\Module::$formTableName['report_ekg'];

            $data = \backend\modules\patient\classes\PatientFunc::loadTbDataByTarget($ezf_table, $value['id']);

            $btn = \yii\helpers\Html::a('<span class="fa fa-heartbeat"></span>', 'javascript:void(0)', ['class' => 'btn btn-sm btn-primary ezform-main-open', 'data-modal' => 'modal-ezform-main',
                        'data-url' => yii\helpers\Url::to(['/ezforms2/ezform-data/ezform-view', 'ezf_id' => $ezf_id,
                            'modal' => 'modal-ezform-main', 'dataid' => $data['id']])]);
            $append .= "$('#ez1514016599071774100-ckr_ekg').append('$btn'); $('[item-id=1514967817036889300]').removeClass('hidden'); ";
        }
        if (in_array($value['order_tran_code'], ['BC001', 'BC002', 'BC003', 'BC005', 'BC006', 'BC009', 'BC010', 'BC015', 'BC016', 'BC017']) && empty($chem)) {
            $chem = 1;
            
            $btn = yii\helpers\Html::a('<span class="fa fa-bars"></span>', 'javascript:void(0)', ['class' => 'btn btn-sm btn-success ezform-main-open', 'data-modal' => 'modal-ezform-main',
                        'data-url' => yii\helpers\Url::to(['/patient/order/result-lab-show', 'pt_id' => $value['ptid'], 'pt_hn' => $dataProfile['pt_hn'],
                            'visit_id' => $value['order_visit_id'], 'view' => 'modal', 'secname' => 'Chemistry', 'date' => $value['visit_date']])]);
            $append .= "$('#ez1514016599071774100-ckr_chem').append('$btn'); $('[item-id=1518324678046856400]').removeClass('hidden'); ";
        }
        if (in_array($value['order_tran_code'], ['IM001', 'IM008', 'IM047', 'BC030', 'BC029', 'BC032']) && empty($sero)) {
            $sero = 1;
            $btn = yii\helpers\Html::a('<span class="fa fa-bars"></span>', 'javascript:void(0)', ['class' => 'btn btn-sm btn-success ezform-main-open', 'data-modal' => 'modal-ezform-main',
                        'data-url' => yii\helpers\Url::to(['/patient/order/result-lab-show', 'pt_id' => $value['ptid'], 'pt_hn' => $dataProfile['pt_hn'],
                            'visit_id' => $value['order_visit_id'], 'view' => 'modal', 'test_code' => 'Serology', 'date' => $value['visit_date']])]);
            $append .= "$('#ez1514016599071774100-ckr_sero').append('$btn'); $('[item-id=1520338544071684700]').removeClass('hidden'); ";
        }

        if ($value['order_tran_code'] == 'CG001') {
            $cyto = 1;
            $cytoAppend = "$.get('/cpoe/report-checkup/result-cyto',{order_id:'{$value['id']}'}).done(function(result) {                            
                            $('[item-id=1514730732009849700]').html(result);
                         }); ";
        }
        if ($value['order_tran_code'] == 'CG015') {
            $cyto = 2;
        }
        if ($value['order_tran_code'] == 'CG016') {
            $btn = yii\helpers\Html::a('HPV <span class="fa fa-bars"></span>', 'javascript:void(0)', ['class' => 'btn btn-sm btn-success ezform-main-open', 'data-modal' => 'modal-ezform-main',
                        'data-url' => yii\helpers\Url::to(['/patient/order/result-lab-show', 'pt_id' => $value['ptid'], 'pt_hn' => $dataProfile['pt_hn'],
                            'visit_id' => $value['order_visit_id'], 'view' => 'modal', 'ln' => $value['order_vender_no']])]);
            $cytoAppend .= "$('[item-id=\"1514079832051038300\"] h3').append(' $btn'); $('[item-id=1518532667077127800]').removeClass('hidden'); ";

            $cyto = 3;
        }
    }

    if ($cyto == 1) {
        $cyto = "$('input[name=\"EZ1514016599071774100[ckr_epeptp]\"][value=\"1\"]').prop('checked',true); ";
        $cyto .= "$('#ez1514016599071774100-ckr_sgt_other_1').val('1 ปี'); ";
    } elseif ($cyto == 2) {
        $cyto = "$('input[name=\"EZ1514016599071774100[ckr_epeptp]\"][value=\"2\"]').prop('checked',true); ";
        $cyto .= "$('#ez1514016599071774100-ckr_sgt_other_1').val('2 ปี'); ";
    } elseif ($cyto == 3) {
        $cyto = "$('input[name=\"EZ1514016599071774100[ckr_epeptp]\"][value=\"3\"]').prop('checked',true); ";
        $cyto .= "$('#ez1514016599071774100-ckr_sgt_other_1').val('3 ปี'); ";
    }
    if ($checkup == FALSE) {
        //มาตรวจ Prep อย่างเดียวปิดตรวจสุขภาพทั้งหมด
        $append = "$('#ezform-1514016599071774100 .modal-header').addClass('hidden'); ";

        $append .= "$('[item-id=1518443317030085100],[item-id=1514017101034126400],[item-id=1514023848095631700],[item-id=1517726500054332200]').addClass('hidden'); ";
        $append .= "$('[item-id=1514024000012353800],[item-id=1514024519042021700],[item-id=1514024854094308800]').addClass('hidden'); ";
        $append .= "$('[item-id=1514027694076592600],[item-id=1514027947086195800],[item-id=1514028128002067000]').addClass('hidden'); ";
        $append .= "$('[item-id=1518678055033386800],[item-id=1528173848050142000],[item-id=1528173946011759900],[item-id=1528184026075282500],[item-id=1528184072077301600]').addClass('hidden'); ";
        $append .= "$('[item-id=1514028287037793700]').addClass('hidden'); ";
        ?>
        <script type="text/javascript">
            setTimeout(function () {
              $('input[name="EZ1514016599071774100[ckr_pe]"][value="2"]').prop('checked', false);
            }, 1000);

            //$('input[name="EZ1514016599071774100[ckr_breast]"][value="2"]').prop('checked',true);
        </script>
        <?php

    } else {
        if ($dataProfile['pt_sex'] == '2') {
            $append .= "$('[item-id=1518531894093045000]').removeClass('hidden'); ";
        }
    }
    $this->registerJS("$append $cyto $cytoAppend ");
} else {
    echo 0;
}
?>