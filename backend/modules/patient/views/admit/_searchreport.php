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
                'action' => ['/patient/admit/dashboard'],
                'method' => 'get',
                'options' => ['data-pjax' => true, 'style' => 'margin-bottom: 0px;'],
                'enableClientValidation' => false,
    ]);
    ?>
  <div class="col-md-7">
      <?= Html::activeRadioList($model, 'admit_status', ['2' => 'Admit', '3' => 'Pre Discharge', '4' => 'Discharge'], ['class' => 'pull-left', 'itemOptions' => ['class' => 'radio-inline']]); ?>
  </div>
  <div class="col-md-3 sdbox-col">
    <?php
    echo kartik\widgets\DatePicker::widget([
        'model' => $model,
        'attribute' => 'update_date',
        'type' => kartik\widgets\DatePicker::TYPE_INPUT,
        'pluginOptions' => [
            'autoclose' => true,
            'format' => 'dd/mm/yyyy'
        ]
    ]);
    ?>
  </div>
  <?php ActiveForm::end(); ?>
</div>
<?php
$this->registerJs(" 
$('form#{$model->formName()} input[type=\"radio\"],input[type=\"text\"]').on('change',function(){
    $(this).submit();
});
");
?>
