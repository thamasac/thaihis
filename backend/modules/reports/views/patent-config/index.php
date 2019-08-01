<?php

use yii\helpers\Url;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\web\JsExpression;

$this->params['breadcrumbs'][] = Yii::t('patient', '');
\backend\modules\ezforms2\classes\EzfStarterWidget::begin();
?>
<div class="pis-item-generic"> 
  <div class="sdbox-header">
    <div class="row">
      <div class="col-md-3">
        <h3> <?= Yii::t('patient', 'รายชื่อผู้รับบริการแบบกลุ่ม') ?></h3>
        <?php
        $url = \yii\helpers\Url::to(['get-list']);
        echo Select2::widget([
            'id' => 'project_id',
            'name' => 'EZ1517227483007856300[project_id]',
            // 'data' => ArrayHelper::map(CpoeQuery::getProjectNo(), 'target', 'project_name'),
            'options' => ['placeholder' => 'Select states ...'],
            'pluginOptions' => [
                'allowClear' => true,
                'minimumInputLength' => 3,
                'language' => [
                    'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
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
      <div class="col-md-1">
        <h3>เพิ่ม/แก้ไข</h3>
        <?php
        echo backend\modules\ezforms2\classes\BtnBuilder::btn()
                ->ezf_id($ezf_id)
                ->label('<i class="glyphicon glyphicon-pencil"></i>')->options(['class' => 'btn btn-primary btn-sm'])
                ->buildBtnEdit('1234')
        . ' ' . backend\modules\ezforms2\classes\BtnBuilder::btn()
                ->ezf_id($ezf_id)
                ->label('<i class="glyphicon glyphicon-plus"></i>')->options(['class' => 'btn btn-success btn-sm'])
                ->buildBtnAdd();
        ?>
      </div>
      <div class="col-md-3">
        <h3> <?= Yii::t('patient', 'วันที่') ?></h3>
        <?php
        echo DatePicker::widget([
            'name' => 'EZ1517227483007856300[date_start_project]',
            'id' => 'date_start_project',
            'value' => !empty($date_start_project) ? $date_start_project : '',
            'options' => [
                'placeholder' => 'Date start ...',
                'readonly' => TRUE
            ],
            'pluginOptions' => [
                'format' => 'dd-mm-yyyy',
                'todayHighlight' => true,
                'autoclose' => true
            ]
        ]);
        ?>
      </div>
      <div class="col-md-3">
        <h3> <?= Yii::t('patient', 'ถึงวันที่') ?></h3>
        <?php
        echo DatePicker::widget([
            'name' => 'EZ1517227483007856300[date_end_project]',
            'id' => 'date_end_project',
            'options' => [
                'placeholder' => 'Date end ...',
                'readonly' => TRUE
            ],
            'pluginOptions' => [
                'format' => 'dd-mm-yyyy',
                'todayHighlight' => true,
                'autoclose' => true
            ]
        ]);
        ?>
      </div>     
    </div>
    <br>
    <div id="gridview"></div>
  </div>

</div>
<?php
\backend\modules\ezforms2\classes\EzfStarterWidget::end();
$url = Url::to(['/ezforms2/ezform-data/ezform', 'ezf_id' => $ezf_id, 'modal' => 'modal-ezform-main', 'reloadDiv' => 'modal-appoint']);
$url_change = Url::to(['get-gridview']);
$this->registerJs("
 $('#date_start_project,#date_end_project').on('change',function(){
    actionGet();
});

$('#project_id').on('change', function (e) {
    let url = '$url&dataid='+$('#project_id').val();
    $('.btn-primary').attr('data-url',url);
    actionGet();
});

function actionGet(pProject_id){
    let project_id = $('#project_id').val();    
    if(null === project_id){
        project_id = '';
    }
    
    let date_start_project = $('#date_start_project').val();
    let date_end_project = $('#date_end_project').val();
    let status = 1;
    if(date_start_project === '' && project_id === ''){
        status = 0;
    }
    if(status === 1){
        $('#gridview').load('$url_change?target='+project_id+'&date_start_project='+date_start_project+'&date_end_project='+date_end_project);
    }else{
         $('#gridview').html('');
    }
}
");
?>
