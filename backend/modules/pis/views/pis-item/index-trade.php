<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\helpers\Html;

$this->params['breadcrumbs'][] = ['label' => Yii::t('patient', 'Generic Name'), 'url' => ['/pis/pis-item/index-generic']];
$this->params['breadcrumbs'][] = Yii::t('patient', 'Trad Name');
\backend\modules\ezforms2\classes\EzfStarterWidget::begin();
?>
<div class="pis-item-trad"> 
  <div class="sdbox-header">
    <!--<h3> <?= Yii::t('patient', 'Generic Name') ?> <span style="color:#3B5998;"><?= $dataGeneric['generic_name'] ?></span></h3>-->
    <div class="row">
      <div class="col-md-11">
          <?php
          echo backend\modules\ezforms2\classes\TargetBuilder::targetWidget()
                  ->ezf_id($ezfGeneric_id)
                  ->modal('modal-ezform-main')
                  ->fields(['generic_name', 'generic_class_id', 'generic_type_id', 'generic_label'])
                  ->fields_search(['generic_name'])
                  ->template_items('{generic_name}<div style="color:#999;">Sig : {generic_label}</div>')
                  ->template_selection(Yii::t('patient', 'Generic Name') . ' {generic_name}  <span style="color:#999;">Sig : {generic_label} </span>')
                  ->dataid($dataGeneric['id'])->options(['style' => 'margin-bottom:10px;', 'class' => 'h3'])
                  ->buildTarget();
          ?>
      </div>
      <div class="col-md-1 sdbox-col">
        <?=
                backend\modules\ezforms2\classes\BtnBuilder::btn()
                ->ezf_id($ezfGeneric_id)->reloadDiv('divReloadPage')
                ->label('<i class="glyphicon glyphicon-pencil"></i> แก้ไข')->options(['class' => 'btn btn-primary'])
                ->buildBtnEdit($dataGeneric['id'])
        ?>
      </div>
    </div>
  </div>
  <?php
  Pjax::begin(['id' => 'trad-grid-pjax', 'timeout' => FALSE]);
  echo appxq\sdii\widgets\GridView::widget([
      'id' => 'trad-grid',
      'panelBtn' => backend\modules\ezforms2\classes\BtnBuilder::btn()
              ->ezf_id($ezf_id)->reloadDiv('test')->target($dataGeneric['id'])
              ->label('<i class="glyphicon glyphicon-plus"></i> เพิ่มข้อมูล การค้า')->options(['class' => 'btn btn-success'])
              ->buildBtnAdd(),
      'filterModel' => $searchModel,
      'dataProvider' => $dataProvider,
      'rowOptions' => function ($model) {
          return [
              'data' => ['key' => $model['item_id']],
          ];
      },
      'columns' => [
          [
              'attribute' => 'trad_item_pic',
              'label' => Yii::t('patient', 'Image'),
              'filter' => false,
              'format' => 'html',
              'contentOptions' => ['style' => 'width: 5%;'],
              'value' => function($model, $key, $index) {
                  $img = ($model['trad_item_pic'] ? Yii::getAlias('@storageUrl/ezform/fileinput') . '/' . $model['trad_item_pic'] : Yii::getAlias('@storageUrl/images') . '/noimg.png');
                  $html = '<img class="img-responsive img-rounded" src="' . $img . '" alt="trad-pic">';
                  return $html;
              }
          ],
          [
              'attribute' => 'trad_stdtrad_id',
              'label' => Yii::t('patient', 'Trad Name'),
              'format' => 'html',
              'contentOptions' => ['style' => 'width: 60%;'],
              'value' => function($model, $key, $index) {
                  $html = Html::tag('div', $model['drug_use'], ['style' => 'color:#999;']);
                  return $model['trad_name'] . $html;
              }
          ],
          [
              'attribute' => 'trad_nickname',
              'label' => Yii::t('patient', 'Nickname'),
          ],
          [
              'attribute' => 'trad_price',
              'label' => Yii::t('patient', 'Price'),
              'format' => ['decimal', 2],
          ],
          [
              'filter' => ["1" => "เปิดใช้งาน", "2" => "ปิดใช้งาน"],
              'attribute' => 'trad_status',
              'label' => 'สถานะ',
              'value' => function($model, $key, $index) {
                  $status = ($model['trad_status'] == '1') ? 'เปิดใช้งาน' : 'ปิดใช้งาน';
                  return $status;
              }
          ],
          [
              'class' => 'yii\grid\ActionColumn',
              'template' => '{receive}',
              'buttons' => [
                  'receive' => function($url, $model) {
                      $html = Html::a('<i class="fa fa fa-pencil"></i> ' . Yii::t('patient', 'Edit'), '#', [
                                  'data-key' => $model['item_id'],
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
  echo \appxq\sdii\widgets\ModalForm::widget([
      'id' => 'modal-use-set',
      'size' => 'modal-xxl',
      'tabindexEnable' => false,
  ]);
  ?>
</div>
<?php
$url = Url::to(['/ezforms2/ezform-data/ezform', 'ezf_id' => $ezf_id, 'modal' => 'modal-ezform-main', 'reloadDiv' => 'modal-appoint']);
$this->registerJs("
$(document).on('hidden.bs.modal', '.modal', function (e) {
        var hasmodal = $('body .modal').hasClass('in');
        if (hasmodal) {
            $('body').addClass('modal-open');
        }
    });     

$('#trad-grid-pjax').on('dblclick', 'tbody tr', function() {
        var url = '$url' + '&dataid=' + $(this).attr('data-key'); 
        modalEzformMain2(url,'#modal-ezform-main');  
});	

$('#trad-grid-pjax').on('click', 'tbody tr a', function() {
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
    $.pjax.reload({container:'#trad-grid-pjax',timeout:false});
 });
");
?>
