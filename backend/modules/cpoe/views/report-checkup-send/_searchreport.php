<?php

use yii\helpers\Html;
use appxq\sdii\helpers\SDHtml;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\UserSearch */
/* @var $form yii\widgets\ActiveForm */
?> 
<div class="col-md-12">
    <?php
    $form = ActiveForm::begin([
                'id' => $model->formName(),
                'action' => ['/cpoe/report-checkup-send',],
                'method' => 'get',
                'options' => ['data-pjax' => true, 'style' => 'margin-bottom: 0px;'],
                'enableClientValidation' => false,
    ]);
    ?>
  <div class="col-md-7">
      <?= Html::activeRadioList($model, 'ckr_status', ['1' => 'รอตอบ', '2' => 'ตอบแล้ว', '3' => 'ส่งแล้ว'], ['class' => 'pull-left', 'itemOptions' => ['class' => 'radio-inline']]); ?>
  </div>
<!--  <div class="col-md-3 sdbox-col">
      <?php
//      echo kartik\widgets\DatePicker::widget([
//          'model' => $model,
//          'attribute' => 'create_date',
//          'type' => kartik\widgets\DatePicker::TYPE_INPUT,
//          'pluginOptions' => [
//              'autoclose' => true,
//              'format' => 'dd/mm/yyyy'
//          ]
//      ]);
      ?>
  </div>-->
  <div class="col-md-5 sdbox-col">
    <div class="input-group">
        <?=
        Html::activeInput('text', $model, 'ckr_summary_detail', ['id' => 'search_reportcounter', 'class' => 'form-control'
            , 'placeholder' => Yii::t('patient', 'Find the form name or hn.')]);
        ?>

      <div class="input-group-btn">
        <?= Html::button(SDHtml::getBtnSearch(), ['class' => 'btn btn-default', 'type' => 'submit']); ?>
      </div>
    </div>
  </div>
  <?php ActiveForm::end(); ?>
</div>
<?php
$this->registerJs(" 
$('#search_reportcounter').select();
/*$('form#{$model->formName()}').on('change', function(e) {
    $(this).submit();   
});*/

$('form#{$model->formName()} input[type=\"radio\"],input[type=\"text\"]').on('change',function(){
    $(this).submit();   
});
");
?>
