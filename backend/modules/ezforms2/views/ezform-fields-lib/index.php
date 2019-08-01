<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\helpers\Url;
use appxq\sdii\widgets\GridView;
use appxq\sdii\widgets\ModalForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use backend\modules\ezforms2\controllers\EzformFieldsLibController;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\ezfieldlib\models\EzformFieldsLibSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Question Library Manage';
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ezform-fields-lib-index">

  <div class="sdbox-header">
    <h3><?= Html::encode($this->title) ?></h3>
  </div>
  <?php // echo $this->render('_search', ['model' => $searchModel]);  ?>

  <?php Pjax::begin(['id' => 'ezform-fields-lib-grid-pjax']); ?>
  <?=
  GridView::widget([
      'id' => 'ezform-fields-lib-grid',
      'panelBtn' => Html::button(SDHtml::getBtnAdd(), ['data-url' => Url::to(['ezform-fields-lib/create']), 'class' => 'btn btn-success btn-sm', 'id' => 'modal-addbtn-ezform-fields-lib']) . ' ' .
      Html::button(SDHtml::getBtnDelete(), ['data-url' => Url::to(['ezform-fields-lib/deletes']), 'class' => 'btn btn-danger btn-sm', 'id' => 'modal-delbtn-ezform-fields-lib', 'disabled' => true]),
      'dataProvider' => $dataProvider,
      'filterModel' => $searchModel,
      'columns' => [
          [
              'class' => 'yii\grid\CheckboxColumn',
              'checkboxOptions' => [
                  'class' => 'selectionEzformFieldsLibIds'
              ],
              'headerOptions' => ['style' => 'text-align: center;'],
              'contentOptions' => ['style' => 'width:40px;text-align: center;'],
          ],
          [
              'class' => 'yii\grid\SerialColumn',
              'headerOptions' => ['style' => 'text-align: center;'],
              'contentOptions' => ['style' => 'width:60px;text-align: center;'],
          ],
          [
              'attribute' => 'lib_group_name',
              'label' => Yii::t('ezform', 'Group Question Library'),
              'filter' => kartik\select2\Select2::widget([
                  'model' => $searchModel,
                  'attribute' => 'lib_group_name',
                  'options' => ['placeholder' => Yii::t('ezform', 'Form')],
                  'pluginOptions' => [
                      'minimumInputLength' => 0,
                      'allowClear' => true,
                      'ajax' => [
                          'url' => Url::to(['/ezforms2/ezform-fields-lib/get-lib-group']),
                          'dataType' => 'json',
                          'data' => new JsExpression('function(params) { return {q:params.term}; }')
                      ],
                      'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                      'templateResult' => new JsExpression('function(result) { return result.text; }'),
                      'templateSelection' => new JsExpression('function (selection) { return selection.text; }'),
//                      'initSelection' => new JsExpression('function (result) { return result.text; }')
                  ],
              ])
          ],
          'field_lib_name',
          [
              'attribute' => 'ezf_name',
              'label' => Yii::t('ezform', 'Form'),
          ],
          [
              'attribute' => 'ezf_field_name',
              'label' => Yii::t('ezform', 'Question'),
          ],
//          [
//              'attribute' => 'field_lib_approved',
//              'value' => function ($data) {
//                  if ($data['field_lib_share'] == 1) {
//                      $btn = 'default';
//                      $icon = 'remove';
//                      if (isset($data['field_lib_approved']) && $data['field_lib_approved'] == 1) {
//                          $btn = 'warning';
//                          $icon = 'ok';
//                      }
//                      if (Yii::$app->user->can('administrator')) {
//                          return Html::a('<span class="glyphicon glyphicon-' . $icon . '"></span> ' . EzformFieldsLibController ::itemAlias('approved', $data['field_lib_approved']), Url::to(['/ezforms2/ezform-fields-lib/approve',
//                                              'id' => $data['field_lib_id'],
//                                          ]), [
//                                      'data-action' => 'approve',
//                                      'class' => "btn btn-$btn btn-xs",
//                          ]);
//                      } else {
//                          return EzformFieldsLibController ::itemAlias('approved', $data['field_lib_approved']);
//                      }
//                  } else {
//                      return '';
//                  }
//              },
//              'format' => 'raw',
//              'headerOptions' => ['style' => 'text-align: center;'],
//              'contentOptions' => ['style' => 'width:150px; text-align: center;'],
//              'filter' => Html::activeDropDownList($searchModel, 'field_lib_approved', EzformFieldsLibController ::itemAlias('approved'), ['class' => 'form-control', 'prompt' => Yii::t('ezform', 'All')]),
//          ],
          [
              'attribute' => 'field_lib_share',
              'value' => function ($data) {
                  $txt = EzformFieldsLibController ::itemAlias('public', ( $data['field_lib_share']) ? $data['field_lib_share'] : '');

                  return $txt;
              },
              'headerOptions' => ['style' => 'text-align: center;'],
              'contentOptions' => ['style' => 'width:100px; text-align: center;'],
              'filter' => Html::activeDropDownList($searchModel, 'field_lib_share', EzformFieldsLibController::itemAlias('public'), ['class' => 'form-control', 'prompt' => Yii::t('ezform', 'All')]),
          ],
          [
              'attribute' => 'field_lib_status',
              'value' => function ($data) {
                  $txt = EzformFieldsLibController ::itemAlias('status', $data['field_lib_status']);

                  return $txt;
              },
              'headerOptions' => ['style' => 'text-align: center;'],
              'contentOptions' => ['style' => 'width:100px; text-align: center;'],
              'filter' => Html::activeDropDownList($searchModel, 'field_lib_status', EzformFieldsLibController::itemAlias('status'), ['class' => 'form-control', 'prompt' => Yii::t('ezform', 'All')]),
          ],
          [
              'attribute' => 'updated_at',
              'format' => ['date', 'php:d/m/Y'],
              'contentOptions' => ['style' => 'width:100px;text-align: center;'],
          ],
          [
              'class' => 'appxq\sdii\widgets\ActionColumn',
              'contentOptions' => ['style' => 'width:210px;'],
              'template' => '{view} {update} {delete}',
              'buttons' => [
                  'view' => function ($url, $data, $key) {
                      return Html::a('<span class="glyphicon glyphicon-eye-open"></span> ' . Yii::t('ezform', 'View Question'), $url, [
                                  'data-action' => 'view',
                                  'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                                  'class' => 'btn btn-info btn-xs',
                      ]);
                  },
                  'update' => function ($url, $data, $key) {
                      return Html::a('<span class="glyphicon glyphicon-pencil"></span> ' . Yii::t('yii', 'Update'), $url, [
                                  'data-action' => 'update',
                                  'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                                  'class' => 'btn btn-primary btn-xs',
                      ]);
                  },
                  'delete' => function ($url, $data, $key) {
                      return Html::a('<span class="glyphicon glyphicon-trash"></span> ' . Yii::t('yii', 'Delete'), $url, [
                                  'data-action' => 'delete',
                                  'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                  'data-method' => 'post',
                                  'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                                  'class' => 'btn btn-danger btn-xs',
                      ]);
                  }
              ],
          ],
      ],
  ]);
  ?>
  <?php Pjax::end(); ?>

</div>

<?=
ModalForm::widget([
    'id' => 'modal-ezform-fields-lib',
    'size' => 'modal-lg',
    'tabindexEnable' => FALSE
]);
?>

<?=
ModalForm::widget([
    'id' => 'modal-ezform-fields-group',
    'size' => 'modal-md',
    'tabindexEnable' => FALSE
]);
?>

<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>
<script>
// JS script
    $('#ezform-fields-lib-grid-pjax').on('click', '#modal-addbtn-ezform-fields-lib', function () {
      modalEzformFieldsLib($(this).attr('data-url'));
    });

    $('#ezform-fields-lib-grid-pjax').on('click', '#modal-delbtn-ezform-fields-lib', function () {
      selectionEzformFieldsLibGrid($(this).attr('data-url'));
    });

    $('#ezform-fields-lib-grid-pjax').on('click', '.select-on-check-all', function () {
      window.setTimeout(function () {
        var key = $('#ezform-fields-lib-grid').yiiGridView('getSelectedRows');
        disabledEzformFieldsLibBtn(key.length);
      }, 100);
    });

    $('#ezform-fields-lib-grid-pjax').on('click', '.selectionEzformFieldsLibIds', function () {
      var key = $('input:checked[class=\"' + $(this).attr('class') + '\"]');
      disabledEzformFieldsLibBtn(key.length);
    });

    $('#ezform-fields-lib-grid-pjax').on('dblclick', 'tbody tr', function () {
      var id = $(this).attr('data-key');
      modalEzformFieldsLib('<?= Url::to(['ezform-fields-lib/update', 'id' => '']) ?>' + id);
    });

    $('#ezform-fields-lib-grid-pjax').on('click', 'tbody tr td a', function () {
      var url = $(this).attr('href');
      var action = $(this).attr('data-action');

      if (action === 'update' || action === 'view') {
        modalEzformFieldsLib(url);
      } else if (action === 'approve') {
        $.post(url).done(function (result) {
          if (result.status == 'success') {
<?= SDNoty::show('result.message', 'result.status') ?>
            $.pjax.reload({container: '#ezform-fields-lib-grid-pjax'});
          } else {
<?= SDNoty::show('result.message', 'result.status') ?>
          }
        }).fail(function () {
<?= SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') ?>
          console.log('server error');
        });
        return false;
      } else if (action === 'delete') {
        yii.confirm('<?= Yii::t('app', 'Are you sure you want to delete this item?') ?>', function () {
          $.post(
                  url
                  ).done(function (result) {
            if (result.status == 'success') {
<?= SDNoty::show('result.message', 'result.status') ?>
              $.pjax.reload({container: '#ezform-fields-lib-grid-pjax'});
            } else {
<?= SDNoty::show('result.message', 'result.status') ?>
            }
          }).fail(function () {
<?= SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') ?>
            console.log('server error');
          });
        });
      }
      return false;
    });

    function disabledEzformFieldsLibBtn(num) {
      if (num > 0) {
        $('#modal-delbtn-ezform-fields-lib').attr('disabled', false);
      } else {
        $('#modal-delbtn-ezform-fields-lib').attr('disabled', true);
      }
    }

    function selectionEzformFieldsLibGrid(url) {
      yii.confirm('<?= Yii::t('app', 'Are you sure you want to delete these items?') ?>', function () {
        $.ajax({
          method: 'POST',
          url: url,
          data: $('.selectionEzformFieldsLibIds:checked[name=\"selection[]\"]').serialize(),
          dataType: 'JSON',
          success: function (result, textStatus) {
            if (result.status == 'success') {
<?= SDNoty::show('result.message', 'result.status') ?>
              $.pjax.reload({container: '#ezform-fields-lib-grid-pjax'});
            } else {
<?= SDNoty::show('result.message', 'result.status') ?>
            }
          }
        });
      });
    }

    $('#modal-ezform-fields-lib').on('click', '.btn-open-lib', function () {
      let group_id = $('#ezformfieldslib-field_lib_group').val();
      let url = $(this).attr('data-url') + '?id=' + group_id;
      modalEzformFieldsLib(url, 'modal-ezform-fields-group');
    });

    function modalEzformFieldsLib(url, modal = 'modal-ezform-fields-lib') {
      $('#' + modal + ' .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
      $('#' + modal).modal('show')
              .find('.modal-content')
              .load(url);
    }
</script>
<?php \richardfan\widget\JSRegister::end(); ?>