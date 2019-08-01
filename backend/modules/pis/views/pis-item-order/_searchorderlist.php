<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
?>
<div id="order-lists-search">  
    <?php
    $form = ActiveForm::begin([
                'id' => 'search-' . $model->formName(),
                'action' => ['/pis/pis-item-order/order-lists', 'reloadDiv' => $reloadDiv
                    , 'right_code' => $right_code, 'ptid' => $ptid, 'options' => $options, 'view' => 'ORDER'],
                'enableClientValidation' => false,
    ]);
    ?>
  <div class="row">
    <div class="col-md-9">     
        <?php
        echo $form->field($model, 'trad_content')->widget(kartik\widgets\Select2::className(), [
            'data' => $itemGroup,
            'options' => ['placeholder' => Yii::t('patient', 'All'), 'multiple' => FALSE, 'class' => 'form-control'],
            'pluginOptions' => [
                'minimumInputLength' => 0,
                'allowClear' => true,
            ],
        ])->label(FALSE);
        ?>
    </div>
    <div class="col-md-3 sdbox-col">
        <?php
        echo $form->field($model, 'trad_extra', ['errorOptions' => ['tag' => null], 'options' => ['tag' => null]])->checkbox(['1'])->label('เวชภัณฑ์');
        ?>
    </div>
    <div class="col-md-12">    
      <?php
      echo $form->field($model, 'trad_stdtrad_id', ['errorOptions' => ['tag' => null], 'options' => ['tag' => null]])->textInput(['class' => 'form-control search-query', 'placeholder' => Yii::t('patient', 'Please Input Order')])->label(false);
      ?>
    </div>
    <?php
    ActiveForm::end();
    ?>
  </div>
</div>
<?php
$this->registerJs(" 
$('form#search-{$model->formName()} input').select();

$('form#search-{$model->formName()}').on('beforeSubmit', function(e) {
    actionSearch{$model->formName()}();    
});

$('form#search-{$model->formName()} select').on('change', function(e) {
    actionSearch{$model->formName()}();
});

$('form#search-{$model->formName()} input').on('keyup', function(e) {
   actionSearch{$model->formName()}();
});

$('form#search-{$model->formName()} input').on('change', function(e) {
   actionSearch{$model->formName()}();
});

function actionSearch{$model->formName()}(){
    var form = $('form#search-{$model->formName()}')
    $.post(form.attr('action'),form.serialize()).done(function(result) {
	$('#$reloadDiv').html(result);
    }).fail(function() {
	" . \appxq\sdii\helpers\SDNoty::show("'" . appxq\sdii\helpers\SDHtml::getMsgError() . "Server Error'", '"error"') . "
	console.log('server error');
    });
    
    return false;
}

");
?>
