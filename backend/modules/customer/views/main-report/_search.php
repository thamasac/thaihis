<?php

use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use kartik\widgets\Select2;
use yii\web\JsExpression;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div id="ezf-search" style="padding: 5px;">  
    <?php
    $form = ActiveForm::begin([
                'id' => 'search-' . $model->formName(),
                'action' => ['/customer/main-report/grid'],
                'method' => 'POST',
                'options' => ['class' => 'form-horizontal'],
    ]);
    ?>
    <div class="form-group">
        <div class="col-md-3">
            <label class="control-label">สิทธิ</label>
            <?php
            $item = ['CASH' => 'เงินสด', 'UCS' => 'หลักประกันสุขภาพ', 'OFC' => 'ข้าราชการ (ต่อเนื่อง)', 'SSS' => 'ประกันสังคม', 'LGO' => 'ข้าราชการ อปท.', 'PRO' => 'ตรวจสุขภาพหน่วยงาน', 'ORI' => 'ต้นสังกัด', 'ORI-G' => 'รัฐวิสาหกิจ'];

            echo \kartik\widgets\Select2::widget([
                'model' => $model,
                'name' => 'EZ1504537671028647300[order_tran_code]',
                'data' => $item,
                'options' => ['placeholder' => 'เลือกสิทธิ', 'multiple' => FALSE, 'class' => 'form-control'],
            ]);
            ?>
        </div>
        <div class="sessno-type">

            <div class="col-md-2">
                <label class="control-label">SESSNO</label>
                <?= Html::input('text', 'EZ1504537671028647300[sessno]', '', ['class' => 'form-control', 'id' => 'sessno']) ?>
            </div>
            <div class="col-md-3">
                <label class="control-label">&nbsp;</label>

                <label class="control-label">สถานะ</label>
                <?= Html::radioList('EZ1504537671028647300[order_tran_comment]', '1', ['1' => 'ยังไม่ยืนยัน', '2' => 'ยืนยันแล้ว', '3' => 'เรียกเก็บแล้ว'], ['class' => 'form-control']) ?>   

            </div>
            <div class="col-md-1">
                <label class="control-label">&nbsp;</label>
                <?=
                Html::a(' Export สกส.', 'javascript:void(0)', ['class' => 'form-control btn btn-warning'
                    , 'data-action' => 'report', 'id' => 'export_drug']);
                ?>
            </div>
            <div class="col-md-2"> 
                <label class="control-label">&nbsp;</label>
                <?=
                Html::a('import BIL สกส.', 'javascript:void(0)', [
                    'class' => 'form-control btn btn-danger',
//                    'data-action' => 'report',
                     'id' => 'import_show'
                ]);
                ?></div>
        </div>  
        <div class="select2-project">
            <div class="col-md-3">
                <label class="control-label">เลือกหน่วยงาน</label>
                <?php
                $url = \yii\helpers\Url::to(['/patient/restful/get-list-project']);
                echo Select2::widget([
                    'id' => 'order_tran_dept',
                    'name' => 'EZ1504537671028647300[order_tran_dept]',
                    'options' => ['placeholder' => 'เลือกหน่วยงาน'],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'minimumInputLength' => 3,
                        'language' => [
                            'errorLoading' => new JsExpression("function () { return 'กำลังค้นหา...'; }"),
                        ],
                        'ajax' => [
                            'url' => $url,
                            'dataType' => 'json',
                            'data' => new JsExpression('function(params) { return {q:params.term}; }')
                        ],
                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                        'templateResult' => new JsExpression('function(city) { return city.text; }'),
                        'templateSelection' => new JsExpression('function (city) { return city.text; }'),
                    ],
                ]);
                ?>
            </div>
            <div class="col-md-3">
                <label class="control-label">ประเภทหน่วยงาน</label>
                <?= Html::radioList('EZ1504537671028647300[project_type]', '1', ['1' => 'หน่วยงานราชการ', '2' => 'หน่วยงานรัฐวิสาหกิจ'], ['class' => 'form-control']) ?>   
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-3">
            <label class="control-label">วันที่</label>
            <?php
            echo \kartik\daterange\DateRangePicker::widget([
                'model' => $model,
                'attribute' => 'create_date',
                'convertFormat' => true,
                //'useWithAddon'=>true,
                'options' => ['id' => 'create_date', 'class' => 'form-control', 'placeholder' => Yii::t('patient', 'Date')],
                'pluginOptions' => [
                    'locale' => [
                        'format' => 'd-m-Y',
                        'separator' => ',',
                    ],
                ]
            ]);
            ?>
        </div>

        <div class="col-md-1">
            <label class="control-label">ค้นหา</label>
            <?=
            Html::a('Search', Url::to(['/customer/main-report/grid',]), ['id' => 'btn-search', 'class' => 'form-control btn btn-success'
                , 'data-action' => 'search']);
            ?>
        </div>
        <div class="col-md-3">
            <label class="control-label">รายงาน</label>
            <?php
            $item = ['report_excel' => 'Excel', 'report_eclaim' => 'Eclaim', 'report_chi' => 'Xml สกส.', 'report_checkup' => 'Report Checkup',
                'report_checkup_excel' => 'Report Checkup Excel'];

            echo \kartik\widgets\Select2::widget([
                'name' => 'EZ1504537671028647300[report_type]',
                'data' => $item,
                'options' => ['placeholder' => 'เลือกรายงาน', 'multiple' => FALSE, 'class' => 'form-control'],
            ]);
            ?>
        </div>
        <div class="col-md-1">
            <label class="control-label">&nbsp;</label>
            <?=
            Html::a('Report', Url::to(['/customer/main-report/export-report',]), ['class' => 'form-control btn btn-warning'
                , 'data-action' => 'report']);
            ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>
<div id ="reloadCheckUp" data-url="<?= Url::to(['import-bill','id'=>'11']); ?>" >               
  <!--   -->
</div>
<?php
$urlimport = Url::to(['import-bill']);
$urlreportdrug = Url::to(['export-drug']);
$urlreport = Yii::$app->params['storageUrl'] . '/Export_XML/';
$this->registerJs(" 
    $('#w1').on('change', function () {
      hideSelectProject();
    });

    hideSelectProject();

    function hideSelectProject() {
      if ($('#w1').val() == 'PRO') {
        $('.select2-project').removeClass('hidden');
        $('.sessno-type').addClass('hidden');
      }else if($('#w1').val() == 'OFC'){
        $('.sessno-type').removeClass('hidden');
        $('.select2-project').addClass('hidden');
      } else {
        $('.select2-project').addClass('hidden');
        $('.sessno-type').addClass('hidden');
      }
    }
  $('#import_show').on('click', function () {       
    $('#modal-order-counter .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
    $('#modal-order-counter').modal('show')
    .find('.modal-content')
    .load('/ezforms2/ezform-data/ezform?ezf_id=1525330055092213900&modal=modal-ezform-main&reloadDiv=reloadCheckUp&initdata=&target=');
  });
$('form#search-{$model->formName()}').on('beforeSubmit', function(e) {
    let url = $('#search-{$model->formName()}').attr('action');
    actionGet(url);
    return false;
});

$('form#search-{$model->formName()} a').on('click', function(e) {
    let url = $(this).attr('href');
    let action = $(this).attr('data-action');
    actionGet(url,action);
    return false;
});

$('#export_drug').on('click', function () { //Next 
      var sessno = $('#sessno').val();
      var create_date = $('#create_date').val();
        $.ajax({
                method: 'POST',
                url: '$urlreportdrug?create_date='+create_date+'&sessno='+sessno,
                dataType: 'Json',
                success: function(result, textStatus) {
                var noty_id = noty({'text': result.message, 'type': result.status});
                var win = window.open('" . $urlreport . "'+result.pathZip, '_blank');
                    win.focus();
                }
        });
});

$('.report-content').on('click', '#grid-report .pagination li a', function () { //Next 
      let url = $(this).attr('href');
      actionGet(url,'search');
      return false;
});

function actionGet(url,action){  
    $.post(url,$('#search-{$model->formName()}').serialize()).done(function(result) {
      if(action === 'search'){
        $('.report-content').html(result);
      }else{
        $('.report-export').html(result);
      }        
    }).fail(function() {
            " . SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') . "
            console.log('server error');
    });
}
");
?>
