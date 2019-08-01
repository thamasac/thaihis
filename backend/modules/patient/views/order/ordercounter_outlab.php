<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\helpers\Url;
use appxq\sdii\widgets\GridView;
use appxq\sdii\widgets\ModalForm;

backend\modules\ezforms2\classes\EzfStarterWidget::begin();
$this->title = Yii::t('app', 'Order Lists');
?>
<div class="ordercounter-index"> 
  <div class="sdbox-header">
    <h3><?= Html::encode($this->title) ?></h3>
  </div>
  <?php
  $dept = '';
  Pjax::begin(['id' => 'ordercounter-grid-pjax', 'timeout' => FALSE]);
  echo GridView::widget([
      'id' => 'ordercounter-grid',
      'panelBtn' => $this->render('_searchordercounter_outlab', ['model' => $searchModel,]),
      //'filterModel' => $searchModel,
      'dataProvider' => $dataProvider,
      'rowOptions' => function ($model) {
          return [
              'data' => ['status' => (isset($model['external_result_status']) ? '2' : '1'), 'key' => $model['order_tran_id'], 'dept-code' => $model['sect_code']]
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
              'attribute' => 'order_name',
              'label' => Yii::t('patient', 'Order Name')
          ],
          [
              'attribute' => 'right_name',
              'label' => Yii::t('patient', 'Right')
          ],
          [
              'class' => 'yii\grid\ActionColumn',
              'template' => '{receive}',
              'buttons' => [
                  'receive' => function($url, $model) {

                      $html = Html::a('<i class="fa fa-edit"></i> ' . Yii::t('patient', 'Result'), '#', [
                                  'data-key' => $model['order_tran_id'],
                                  'data-status' => (isset($model['external_result_status']) ? '2' : '1'),
                                  'data-dept-code' => $model['sect_code'],
                                  'class' => 'btn btn-warning btn-xs btn-block',
                      ]);

                      return $html;
                  },
              ],
              'contentOptions' => ['style' => 'width:120px;text-align: center;'],
          ],
      ],
  ]);
  ?>
  <?php
  Pjax::end();

  echo ModalForm::widget([
      'id' => 'modal-order-counter',
      'size' => 'modal-xxl',
      'tabindexEnable' => false,
  ]);

  echo ModalForm::widget([
      'id' => 'modal-order-report',
      'size' => 'modal-xxl',
      'tabindexEnable' => false,
      'clientOptions' => ['backdrop' => 'static', 'keyboard' => false],
  ]);
  ?>
</div>
<?php
backend\modules\ezforms2\classes\EzfStarterWidget::end();

$url = Url::to(['/patient/order/order-outlab-receive-show', 'ezf_id' => $ezf_id, 'reloadDiv' => 'ordercounter-grid-pjax',]);
$this->registerJs("
$('#ordercounter-grid-pjax').on('dblclick', 'tbody tr', function() {
    var url = '$url' + '&target=' + $(this).attr('data-key') + '&order_status=' +$(this).attr('data-status');
    modalOrdercounter(url,'modal-order-counter');
});	

$('#ordercounter-grid-pjax').on('click', 'tbody tr td a', function() {
    var url = '$url' + '&target=' + $(this).attr('data-key') + '&order_status=' +$(this).attr('data-status');  
    modalOrdercounter(url,'modal-order-counter');  
    return false;
});

$('#modal-order-counter .modal-content').on('click', 'tbody tr td a', function () {
      var url = $(this).attr('data-url');
      if(url){
        modalOrdercounter(url,'modal-order-report');
      }
});

function modalOrdercounter(url,modal) {
    $('#'+modal+' .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
    $('#'+modal).modal('show')
    .find('.modal-content')
    .load(url);
}
");
?>
