<?php

use yii\helpers\Html;
use appxq\sdii\helpers\SDHtml;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\UserSearch */
/* @var $form yii\widgets\ActiveForm */
?> 
<div class="ordercounter-search col-md-12">
    <?php
    $form = ActiveForm::begin([
                'id' => $model->formName(),
                'action' => ['/patient/drg/drg-counter', 'reloadDiv' => $reloadDiv],
                'method' => 'get',
                'options' => ['data-pjax' => true, 'style' => 'margin-bottom: 0px;'],
                'enableClientValidation' => false,
    ]);
    ?>
  <div class="col-md-4">
      <?php
      echo kartik\widgets\DatePicker::widget([
          'model' => $model,
          'attribute' => 'create_date',
          'type' => kartik\widgets\DatePicker::TYPE_INPUT,
          'pluginOptions' => [
              'autoclose' => true,
              'format' => 'dd-mm-yyyy'
          ]
      ]);
      ?>
  </div>
  <div class="col-md-8">
    <div class="input-group">
      <div class="input-group-addon">
        <span><i class="fa fa-user"></i> </span>
      </div>
      <?=
      Html::activeInput('text', $model, 'ptid', ['id' => 'search_drgcounter', 'class' => 'form-control'
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
    $('form#{$model->formName()} input[type=\"text\"]').on('change',function(){
        $(this).submit();   
    });

    
");
?>
