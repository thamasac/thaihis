<?php

use yii\helpers\Html;

backend\modules\ezforms2\classes\EzfStarterWidget::begin();
$this->title = Yii::t('app', 'Customer Report');
?>
<div class="report-body"> 
  <div class="sdbox-header">
    <h3><?= Html::encode($this->title) ?></h3>
  </div>
  <?= $this->render('_search', ['model' => $searchModel]) ?>
  <div class="report-content">

  </div>
  <div class="report-export">

  </div>
</div>
<?php
echo appxq\sdii\widgets\ModalForm::widget([
    'id' => 'modal-order-counter',
    'size' => 'modal-xxl',
    'tabindexEnable' => false,
]);

\richardfan\widget\JSRegister::begin([
//'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>
<script>
    $('.report-content').on('dblclick', '#grid-report tbody tr', function () {
      var url = $(this).attr('data-url');
      modalOrdercounter(url, 'modal-order-counter');
    });

    $('.report-content').on('click', '#grid-report tbody tr td a', function () {
      var url = $(this).attr('data-url');
      myWindow = window.open(url, '_blank');
      return false;
    });

    function modalOrdercounter(url) {
      $('#modal-order-counter .modal-content').html(' <div class="sdloader"><i class="sdloader-icon"></i></div>');
      $('#modal-order-counter').modal('show')
              .find('.modal-content')
              .load(url);
    }
</script>
<?php \richardfan\widget\JSRegister::end(); ?>