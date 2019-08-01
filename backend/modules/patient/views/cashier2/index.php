<?php

use backend\modules\ezforms2\classes\EzfFunc;
use backend\modules\ezforms2\classes\EzfStarterWidget;

\backend\modules\ezforms2\assets\ListdataAsset::register($this);
\backend\modules\cpoe\assets\CpoeAsset::register($this);
$this->title = Yii::t('app', 'Cashier Counter');
EzfStarterWidget::begin();
?>


<div class="row">       
  <div class="col-md-12" id="view-order-counter">
    <h1 class="text-center " style="font-size: 45px; color: #ccc;margin: 200px 0;">
      <?= Yii::t('patient', 'Please choose patient') ?>
    </h1>
  </div>
</div>

<?php
EzfStarterWidget::end();
$txtUrl = $params['cashier_status'] == '1' ? '/patient/cashier2/receive-show-dept' : '/patient/cashier2/receive-show-detail';
$url = \yii\helpers\Url::to([$txtUrl
            , 'visit_id' => $visit_id, 'target' => $target
            , 'configs' => EzfFunc::arrayEncode2String($configs)
            , 'items' => EzfFunc::arrayEncode2String($items)
            , 'params' => EzfFunc::arrayEncode2String($params),]);
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>
<script>
    $(function () {
      var div = $('#view-order-counter');
      div.html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
      var url = '<?= $url ?>';
      $.get(url, function (result) {
        div.empty();
        div.html(result);
      });
    });

    $(document).on('hidden.bs.modal', '.modal', function (e) {
      var hasmodal = $('body .modal').hasClass('in');
      if (hasmodal) {
        $('body').addClass('modal-open');
      }
    });
</script>
<?php \richardfan\widget\JSRegister::end(); ?>