<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\helpers\Url;
use appxq\sdii\widgets\GridView;
use appxq\sdii\widgets\ModalForm;

//kartik\switchinput\SwitchInputAsset::register($this);
backend\modules\ezforms2\classes\EzfStarterWidget::begin();
$this->title = Yii::t('app', 'Order Lists');
?>
<div class="ordercounter-index"> 
  <div class="sdbox-header">
    <h3><?= Html::encode($this->title) ?></h3>
  </div>
  <?php
  echo GridView::widget([
      'id' => 'ordercounter-grid',
      'panelBtn' => $this->render('_searchordercounter_cyto', ['model' => $searchModel, 
          'unit_id' => $unit_id, 
          'reloadDiv' => $reloadDiv]),
      //'filterModel' => $searchModel,
      'dataProvider' => $dataProvider,
      'rowOptions' => function ($model) {
          return [
              'data' => ['status' => $model['order_tran_status'], 'key' => $model['order_visitid']],
          ];
      },
      'columns' => [
          [
              'attribute' => 'pt_hn',
              'label' => 'HN'
          ],
          [
              'attribute' => 'fullname',
              'label' => Yii::t('patient', 'Name')
          ],
          [
              'attribute' => 'sect_name',
              'label' => Yii::t('patient', 'Department')
          ],
          [
              'class' => 'yii\grid\ActionColumn',
              'template' => '{receive}',
              'buttons' => [
                  'receive' => function($url, $model) {

                      if ($model['order_tran_status'] == '1') {
                          $html = Html::a('<i class="fa fa-check-square-o"></i> ' . Yii::t('patient', 'Order Receive'), '#', [
                                      'data-key' => $model['order_visitid'],
                                      'data-report-status' => $model['order_tran_status'],
                                      'data-order-status' => $model['order_tran_status'],
                                      'class' => 'btn btn-primary btn-xs btn-block',
                          ]);
                      } else {
                          $html = Html::a('<i class="fa fa-edit"></i> ' . Yii::t('patient', 'Order Edit'), '#', [
                                      'data-key' => $model['order_visitid'],
                                      'data-status' => $model['order_tran_status'],
                                      'class' => 'btn btn-warning btn-xs btn-block',
                          ]);
                      }

                      return $html;
                  },
              ],
              'contentOptions' => ['style' => 'width:120px;text-align: center;'],
          ],
      ],
  ]);
  ?>
  <?php
  echo ModalForm::widget([
      'id' => 'modal-order-counter',
      'size' => 'modal-xxl',
      'tabindexEnable' => false,
  ]);
  ?>
</div>
<?php
$url = Url::to(['/thaihis/order/order-receive-cyto', 'ezf_id' => $ezf_id, 'unit_id' => $unit_id, 'reloadDiv' => $reloadDiv,]);
$this->registerJs("
$('#ordercounter-grid').on('dblclick', 'tbody tr', function() {
    var url = '$url' + '&visitid=' + $(this).attr('data-key') + '&order_status=' +$(this).attr('data-status'); 
    modalOrdercounter(url);
});	

$('#ordercounter-grid').on('click', 'tbody tr td a', function() {
    var url = '$url' + '&visitid=' + $(this).attr('data-key') + '&order_status=' +$(this).attr('data-status');
    modalOrdercounter(url);
    return false;
});

$('#modal-order-counter .modal-content').on('click', 'tbody tr td a', function () {
      var url = $(this).attr('data-url');
      modalEzformMain(url,'modal-ezform-main');
      return false;
});

function modalOrdercounter(url) {
    $('#modal-order-counter .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
    $('#modal-order-counter').modal('show')
    .find('.modal-content')
    .load(url);
}
");
?>
