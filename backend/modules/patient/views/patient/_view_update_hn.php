<?php

use yii\helpers\Html;
use yii\helpers\Url;
use backend\modules\ezforms2\classes\EzActiveForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div class="modal-header">
    <h4 class="modal-title">แก้ไข HN ผู้ป่วย</h4>
</div>
<?php
EzActiveForm::begin([
    'id' => 'form-submit',
    'action' => ['/patient/patient/update-hn-save',]
])
?>
<div class="modal-body">

            <?= Html::hiddenInput('target', $target) ?>
    <div class="container-fluid">
        <div class="col-md-6">
            <?= Html::label(Yii::t('thaihis', 'HN เดิม')) ?>
            <?= Html::textInput('pt_hn_old', $pt_hn, ['class' => 'form-control']) ?>
        </div>
        <div class="col-md-6">
<?= Html::label(Yii::t('thaihis', 'HN ใหม่')) ?>
<?= Html::textInput('pt_hn_new', '', ['class' => 'form-control']) ?>
        </div>
    </div>

</div>
<div class="modal-footer">
<?= Html::submitButton("Submit", ['class' => 'btn btn-primary pull-right']) ?>
</div>
<?php EzActiveForm::end() ?>
<?php
$this->registerJS("

$('form#form-submit').on('beforeSubmit', function(e) {
    
    var \$form = $(this);
    var formData = new FormData($(this)[0]);
    
    $.ajax({
          url: \$form.attr('action'),
          type: 'POST',
          data: formData,
	  dataType: 'JSON',
	  enctype: 'multipart/form-data',
	  processData: false,  // tell jQuery not to process the data
	  contentType: false,   // tell jQuery not to set contentType
          success: function (result) {
	    if(result.status == 'success') {
                " . SDNoty::show('result.message', 'result.status') . "
                window.location.reload();
            } else {
                " . SDNoty::show('result.message', 'result.status') . "
                    $('#form-submit .btn-submit').attr('disabled', false);
            } 
          },
          error: function () {
            " . SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') . "
                $('#form-submit .btn-submit').attr('disabled', false);
	    console.log('server error');
          }
      });
      
    return false;
});

");
?>