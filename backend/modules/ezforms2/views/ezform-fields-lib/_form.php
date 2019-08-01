<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use yii\helpers\Url;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model backend\modules\ezfieldlib\models\EzformFieldsLib */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="ezform-fields-lib-form">

  <?php
  $form = ActiveForm::begin([
              'id' => $model->formName(),
  ]);
  ?>

  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="itemModalLabel">Question Library</h4>
  </div>

  <div class="modal-body">
    <div class="form-group row">
      <div class="col-md-6">
          <?php
          $options['placeholder'] = Yii::t('ezform', 'Form');
          if (isset($mode) && $mode == 'ezform') {
              $options['disabled'] = TRUE;
          }

          $ezf_initValue = empty($model->ezf_id) ? '' : \backend\modules\ezforms2\models\Ezform::findOne($model->ezf_id)->ezf_name;

          echo $form->field($model, 'ezf_id')->widget(kartik\widgets\Select2::classname(), [
              'initValueText' => $ezf_initValue,
              'options' => $options,
              'pluginOptions' => [
                  'minimumInputLength' => 0,
//                  'allowClear' => true,
                  'ajax' => [
                      'url' => Url::to(['/ezforms2/ezform/get-forms']),
                      'dataType' => 'json',
                      'data' => new JsExpression('function(params) { return {q:params.term}; }')
                  ],
                  'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                  'templateResult' => new JsExpression('function(result) { return result.text; }'),
                  'templateSelection' => new JsExpression('function (selection) { return selection.text; }'),
              ],
          ])->label(Yii::t('ezform', 'Form'));
          ?>
      </div>
      <div id="divFieldLib" class="col-md-6 hidden">
          <?php
          $options['placeholder'] = Yii::t('ezform', 'Search Question');
          if (isset($mode) && $mode == 'ezform') {
              $options['disabled'] = TRUE;
          }
          
          $ezf_initValue = empty($model->ezf_field_id) ? '' : \backend\modules\ezforms2\models\EzformFields::findOne($model->ezf_field_id)->ezf_field_name;

          echo $form->field($model, 'ezf_field_id')->widget(kartik\widgets\Select2::classname(), [
              'initValueText' => $ezf_initValue,
              'options' => $options,
              'pluginOptions' => [
                  'minimumInputLength' => 0,
                  'allowClear' => true,
                  'ajax' => [
                      'url' => Url::to(['/ezforms2/ezform-fields-lib/get-fields']),
                      'dataType' => 'json',
                      'data' => new JsExpression("function(params) { return {q:params.term,ezf_id:$('#ezformfieldslib-ezf_id').val()}; }")
                  ],
                  'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                  'templateResult' => new JsExpression('function(result) { return result.text; }'),
                  'templateSelection' => new JsExpression('function (selection) { return selection.text; }'),
              ],
          ])->label(Yii::t('ezform', 'Question Library'));
          ?>
      </div>
    </div>

    <div class="form-group row">
      <div class="col-md-6">
        <div class="input-group">
            <?php
            if ($model->field_lib_group) {
                $ezf_initValue = \backend\modules\ezforms2\models\EzformFieldsLibGroup::findOne($model->field_lib_group);

                $ezf_initValue = isset($ezf_initValue->lib_group_name) ? $ezf_initValue->lib_group_name : '';
            }

            echo $form->field($model, 'field_lib_group')->widget(kartik\widgets\Select2::classname(), [
                'initValueText' => $ezf_initValue,
                'options' => ['placeholder' => Yii::t('ezform', 'Form')],
                'pluginOptions' => [
                    'minimumInputLength' => 0,
//                  'allowClear' => true,
                    'ajax' => [
                        'url' => Url::to(['/ezforms2/ezform-fields-lib/get-lib-group']),
                        'dataType' => 'json',
                        'data' => new JsExpression('function(params) { return {q:params.term}; }')
                    ],
                    'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                    'templateResult' => new JsExpression('function(result) { return result.text; }'),
                    'templateSelection' => new JsExpression('function (selection) { return selection.text; }'),
                ],
            ])->label(Yii::t('ezform', 'Group Question Library'));
            ?>

          <div class="input-group-btn" style="padding-top: 25px;">
              <?php
              echo Html::button('<i class="glyphicon glyphicon-cog"></i> ', [
                  'data-container' => 'body',
                  'class' => 'btn btn-default btn-cong',
                  'data-active' => 0,
                  'data-url' => Url::to(['/ezforms2/ezform-fields-lib/btn-add-edit'])
              ]);
              ?>
          </div>
        </div>
      </div>
      <div class="col-md-6">
          <?= $form->field($model, 'field_lib_name', ['enableAjaxValidation' => true])->textInput(['maxlength' => true]) ?>
      </div>
    </div>

    <div class="form-group row">
      <div class="col-md-6">
          <?php
          if (empty($model->field_lib_share)) {
              $model->field_lib_share = 2;
          }

          if (Yii::$app->user->can('administrator')) {
              $item = ['2' => Yii::t('ezform', 'Private'), '3' => Yii::t('ezform', 'Everyone in site'), '1' => Yii::t('ezform', 'Public User'), '4' => Yii::t('ezform', 'Public Common(Admin select)')];
          } else {
              $item = ['2' => Yii::t('ezform', 'Private'), '3' => Yii::t('ezform', 'Everyone in site'), '1' => Yii::t('ezform', 'Public User')];
          }

          echo $form->field($model, 'field_lib_share')->radioList($item, ['class' => ''])
          ?>
      </div>
      <div class="col-md-6">
          <?php
          if (empty($model->field_lib_status)) {
              $model->field_lib_status = 1;
          }
          echo $form->field($model, 'field_lib_status')->radioList(['1' => Yii::t('ezform', 'Enable'), '0' => Yii::t('ezform', 'Disable')], ['class' => '']);
          ?>
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
if (isset($model['field_lib_id'])) {
    $action = '&action=submit';
} else {
    $action = '?action=submit';
}
\richardfan\widget\JSRegister::begin([
//'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>
<script>
// JS script
    $('#<?= $model->formName() ?> .btn-cong').on('click', function (e) {
      let group_id = $('#ezformfieldslib-field_lib_group').val();
      let url = $(this).attr('data-url') + '?group_id=' + group_id;

      $.get(url, function (result) {
        $("#<?= $model->formName() ?> .input-group-btn").html(result);
      });
    });

    hideLibField($('#ezformfieldslib-ezf_id').val());

    $('#ezformfieldslib-ezf_id').on('change', function (e) {
      hideLibField($(this).val());
    });

    function hideLibField(val) {
      if (val) {
        $('#divFieldLib').removeClass('hidden');
      } else {
        $('#divFieldLib').addClass('hidden');
      }
    }

    $('form#<?= $model->formName() ?>').on('beforeSubmit', function (e) {
      var $form = $(this);
      $.post(
              $form.attr('action') + '<?= $action ?>', //serialize Yii2 form
              $form.serialize()
              ).done(function (result) {
        if (result.status == 'success') {
<?= SDNoty::show('result.message', 'result.status') ?>
<?php if (isset($mode) && $mode <> 'ezform') : ?>
              if (result.action == 'create') {
                $(document).find('#modal-ezform-fields-lib').modal('hide');
                $.pjax.reload({container: '#ezform-fields-lib-grid-pjax'});
              } else if (result.action == 'update') {
                $(document).find('#modal-ezform-fields-lib').modal('hide');
                $.pjax.reload({container: '#ezform-fields-lib-grid-pjax'});
              }
<?php else: ?>
              $(document).find('#modal-ezform-version').modal('hide');
              $('#modal-ezform-version .modal-content').html('');
<?php endif; ?>
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