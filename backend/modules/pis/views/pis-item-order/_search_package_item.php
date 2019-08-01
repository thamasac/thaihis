<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
?>
<div id="order-lists-search">  
    <?php
    $form = ActiveForm::begin([
                'id' => 'search-package-item',
                'action' => ['/pis/pis-item-order/order-lists', 'reloadDiv' => $reloadDiv
                    , 'view' => 'PACKAGE', 'options' => $options]
    ]);
    ?>
  <div class="form-group">
    <?=
    kartik\widgets\Select2::widget([
        'name' => 'EZ1515588745039739100[trad_content]',
        'id' => 'ez1515588745039739100-trad_content2',
        'value' => '',
        'data' => $itemGroup,
        'options' => ['placeholder' => Yii::t('patient', 'All'), 'multiple' => FALSE, 'class' => 'form-control'],
        'pluginOptions' => [
            'minimumInputLength' => 0,
            'allowClear' => true,
        ],
    ]);
    ?>
  </div>
  <?php
  echo Html::input("text", "EZ1515588745039739100[trad_stdtrad_id]", "", ['id' => 'ez1515588745039739100-trad_stdtrad_id', 'class' => 'form-control search-query', 'placeholder' => Yii::t('patient', 'Please Input Order')]);

  ActiveForm::end();
  ?>
</div>
<?php
$this->registerJs(" 
$('form#search-package-item input').select();

$('form#search-package-item').on('beforeSubmit', function(e) {
    actionSearchPackageItem();    
});

$('form#search-package-item select').on('change', function(e) {
    actionSearchPackageItem();
});

$('form#search-package-item input').on('keyup', function(e) {
   actionSearchPackageItem();
});

function actionSearchPackageItem(){
    var form = $('form#search-package-item')
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
