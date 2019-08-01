<?php

use yii\bootstrap\ActiveForm;
?>
<div style="padding: 5px;">  
  <?php
  $form = ActiveForm::begin([
              'id' => $model->formName(),
              'enableClientValidation' => FALSE,
              'action' => ['/ezforms2/ezform-fields-lib/lib-lists',
                  'ezf_id' => $ezf_id,
                  'v' => $v,
              ],
              'fieldConfig' => [
                  'options' => [
                      'tag' => false,
                  ],
              ],
  ]);
  ?>
  <?=
          $form->field($model, 'field_lib_name', ['options' => ['class' => 'form-control search-query', 'placeholder' => Yii::t('ezform', 'Search Question') . ' ' . Yii::t('ezform', 'By Name,Group')], 'errorOptions' => ['tag' => FALSE],])
          ->label(false);
  ?>
  <?php
  $item = ['2' => Yii::t('ezform', 'Private'), '3' => Yii::t('ezform', 'Everyone in site'), '1' => Yii::t('ezform', 'Public User'), '4' => Yii::t('ezform', 'Public Common(Admin select)')];
  $model->field_lib_share = ['1', '2', '3', '4'];

  echo $form->field($model, 'field_lib_share')->inline(true)->checkboxList($item)->label(false);

  ActiveForm::end();
  ?>
</div>
<?php
$this->registerJs(" 
$('form#{$model->formName()} input').select();

$('form#{$model->formName()}').on('beforeSubmit', function(e) {
    return false;
});

$('form#{$model->formName()} input[type=\"checkbox\"]').on('change', function(e) {
    actionSearch();
});

$('form#{$model->formName()} input').on('keyup', delay(function (e) {
    actionSearch();
}, 5));

var q;
function actionSearch(){
if(q != null){
console.log('abort');
q.abort();
}

    q = $.post($('#{$model->formName()}').attr('action'),$('#{$model->formName()}').serialize()).done(function(result) {
	$('#$reloadDiv').html(result);
            console.log('$reloadDiv');
            q = null;
    }).fail(function(e) {
    if(e.statusText !== 'abort'){
    " . \appxq\sdii\helpers\SDNoty::show("'" . appxq\sdii\helpers\SDHtml::getMsgError() . "Server Error'", '"error"') . "
	console.log('server error');
        console.error('SEARCH ERROR',e);
    }	 
    q = null;
    });
    
return false;
}

function delay(callback, ms) {
  var timer = 0;
  return function() {
    var context = this, args = arguments;
    clearTimeout(timer);
    timer = setTimeout(function () {
      callback.apply(context, args);
    }, ms || 0);
  };
}
");
?>
