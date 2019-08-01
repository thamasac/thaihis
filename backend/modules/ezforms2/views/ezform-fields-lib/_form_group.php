<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;

/* @var $this yii\web\View */
/* @var $model backend\Modules\ezforms2\models\EzformFieldsLibGroup */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="ezform-fields-lib-group-form">

  <?php
  $form = ActiveForm::begin([
              'id' => $model->formName(),
  ]);
  ?>

  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="itemModalLabel">Ezform Question Library Group</h4>
  </div>

  <div class="modal-body">
    <div class="form-group row">
      <div class="col-md-12">
          <?= $form->field($model, 'lib_group_name')->textInput(['maxlength' => true]) ?>
      </div>
    </div>
  </div>
  <div class="modal-footer">
    <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
  </div>

  <?php ActiveForm::end(); ?>

</div>

<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>
<script>
// JS script
    $('form#<?= $model->formName() ?>').on('beforeSubmit', function (e) {
      var $form = $(this);
      $.post(
              $form.attr('action'), //serialize Yii2 form
              $form.serialize()
              ).done(function (result) {
        if (result.status == 'success') {
<?= SDNoty::show('result.message', 'result.status') ?>      
            $('#modal-ezform-fields-group').modal('hide');
        } else {
<?= SDNoty::show('result.message', 'result.status') ?>
        }
      }).fail(function () {
<?= SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') ?>
        console.log('server error');
      });
      return false;
    });
</script>
<?php \richardfan\widget\JSRegister::end(); ?>