<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
?>
<div id="order-lists-search">
  <?php
  $form = ActiveForm::begin([
              'id' => 'search-package',
              'action' => ['/pis/pis-item-order/package-lists', 'user_id' => $user_id
                  , 'visit_id' => $visit_id, 'order_id' => $order_id
                  , 'right_code' => $right_code],
              'enableClientValidation' => false,
  ]);

  echo Html::input("text", "package_name", "", ['id' => 'package_name', 'class' => 'form-control search-query', 'placeholder' => Yii::t('patient', 'Please Input Order')]);

  ActiveForm::end();
  ?>
</div>
<?php
$this->registerJs(" 
$('form#search-package input').select();

$('form#search-package').on('beforeSubmit', function(e) {
    actionSearchPackage();    
});

$('form#search-package input').on('keyup', function(e) {
   actionSearchPackage();
});

function actionSearchPackage(){
    let form = $('form#search-package')
    let q = $('#package_name').val();
    $.get(form.attr('action'),{q:q}).done(function(result) {
	$('#$reloadDiv').html(result);
    }).fail(function() {
	" . \appxq\sdii\helpers\SDNoty::show("'" . appxq\sdii\helpers\SDHtml::getMsgError() . "Server Error'", '"error"') . "
	console.log('server error');
    });
    
    return false;
}

");
?>
