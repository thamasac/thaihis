<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\helpers\Html;

//$this->params['breadcrumbs'][] = ['label' => Yii::t('patient', 'Generic Name'), 'url' => ['/pis/pis-item-generic']];
$this->params['breadcrumbs'][] = Yii::t('patient', 'Generic Name');
?>
<div class="pis-item-generic"> 
  <div class="sdbox-header">
    <h3> <?= Yii::t('patient', 'Generic Name') ?></h3>
  </div>
  <?php
  \backend\modules\ezforms2\classes\EzfStarterWidget::begin();
  Pjax::begin(['id' => 'generic-grid-pjax', 'timeout' => FALSE]);
  echo appxq\sdii\widgets\GridView::widget([
      'id' => 'generic-grid',
      'panelBtn' => backend\modules\ezforms2\classes\BtnBuilder::btn()
              ->ezf_id($ezf_id)
              ->reloadDiv('aaa')
              ->label('<i class="glyphicon glyphicon-plus"></i> เพิ่มข้อมูล ยาสามัญ')->options(['class' => 'btn btn-success'])
              ->buildBtnAdd(),
      'filterModel' => $searchModel,
      'dataProvider' => $dataProvider,
      'rowOptions' => function ($model) {
          return [
              'data' => ['key' => $model['generic_id']],
          ];
      },
      'columns' => [
          [
              'attribute' => 'generic_name',
              'label' => 'ชื่อสามัญทางยา',
              'format' => 'html',
              'contentOptions' => ['style' => 'width: 60%;'],
              'value' => function($model, $key, $index) {
                  $html = Html::tag('div', $model['drug_use'], ['style' => 'color:#999;']);
                  return $model['generic_name'] . $html;
              }
          ],
          [
              'attribute' => 'generic_type_id',
              'label' => 'บัญชี',
              'value' => function($model, $key, $index) {
                  return $model['drug_type_name'];
              }
          ],
          [
              'filter' => ["1" => "ยา", "2" => "เวชภัณฑ์"],
              'attribute' => 'generic_type',
              'label' => 'สถานะ',
              'value' => function($model, $key, $index) {
                  $status = ($model['generic_type'] == '1') ? 'ยา' : 'เวชภัณฑ์';
                  return $status;
              }
          ],
          [
              'filter' => ["1" => "เปิดใช้งาน", "2" => "ปิดใช้งาน"],
              'attribute' => 'generic_status',
              'label' => 'สถานะ',
              'value' => function($model, $key, $index) {
                  $status = ($model['generic_status'] == '1') ? 'เปิดใช้งาน' : 'ปิดใช้งาน';
                  return $status;
              }
          ],
          [
              'class' => 'yii\grid\ActionColumn',
              'template' => '{receive}',
              'buttons' => [
                  'receive' => function($url, $model) {
                      $html = Html::a('<i class="fa fa-stack-exchange"></i> ' . Yii::t('patient', 'Trad Name'), Url::to(['/pis/pis-item/index-trade', 'target' => $model['generic_id']]), [
                                  'data-key' => $model['generic_id'],
                                  'class' => 'btn btn-primary btn-xs btn-block',
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
  \backend\modules\ezforms2\classes\EzfStarterWidget::end();
  ?>
</div>
<?php
$url = Url::to(['/ezforms2/ezform-data/ezform', 'ezf_id' => $ezf_id, 'modal' => 'modal-ezform-main', 'reloadDiv' => 'modal-appoint']);
$this->registerJs("
$('#generic-grid-pjax').on('dblclick', 'tbody tr', function() {
        var url = '$url' + '&dataid=' + $(this).attr('data-key'); 
        modalEzformMain2(url,'#modal-ezform-main');  
});	

function modalEzformMain2(url,modal) {
    $(modal+' .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
    $(modal).modal('show')
    .find('.modal-content')
    .load(url);
}

 $('#modal-ezform-main').on('hidden.bs.modal', function (e) {
    $.pjax.reload({container:'#generic-grid-pjax',timeout:false});
 });
");
?>
