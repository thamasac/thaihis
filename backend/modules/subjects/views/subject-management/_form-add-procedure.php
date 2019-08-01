<?php

use backend\modules\ezforms2\classes\EzActiveForm;
use yii\helpers\Html;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
?>
<div class="modal-header">
    <label style="font-size: 18px;"><?= Yii::t('ezmodule', 'Procedure Name') ?></label>
<?= Html::button('<i class="fa fa-plus"></i> Add', ['class' => 'btn btn-success btn-name-add']) ?>
    <br>
</div>
<?php
$form = EzActiveForm::begin([
            'id' => 'form-submit',
            'action' => ['/ezforms2/subject-management/update-procedure',
            ],
            'options' => [
                'enctype' => 'multipart/form-data',
                'data-ezf_id' => $ezf_id,
                'data-dataid' => $dataid,
                'data-modal' => $modal,
            ]
        ]);
?>
<div class="modal-body">


    <?=
    Html::hiddenInput('widget_id', $widget_id);
    ?>
    <div class="show-procedure-name">

    </div>

</div>

<div class="modal-footer">
    <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
<?= Html::button('Close', ['class' => 'btn btn-defualt', 'data-dismiss' => 'modal']) ?>
</div>
<?php EzActiveForm::end(); ?>
<?php
$this->registerJs("
    $(function(){
        $.get('/ezforms2/subject-management/add-input-procedure',{},function(result){
             $('.show-procedure-name').append(result);
        });
    });
    
    $('.btn-name-add').click(function(){
        $.get('/ezforms2/subject-management/add-input-procedure',{},function(result){
             $('.show-procedure-name').append(result);
        });
    })
    
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
                $(document).find('#modal-add-procedure').modal('hide');
                location.reload();
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