<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$options = isset($model->options) ? \appxq\sdii\utils\SDUtility::string2Array($model->options) : [];

$target = backend\modules\ezforms2\classes\EzfQuery::getTargetOne($ezf_id);
if ($target) {
    $itemsEzform = \backend\modules\ezforms2\classes\EzfQuery::getEzformRef($ezf_id, $target['parent_ezf_id']);
} else {
    $itemsEzform = \backend\modules\ezforms2\classes\EzfQuery::getEzformAll($ezf_id);
}

$configs = isset($options['configs']) ? $options['configs'] : [];
$items = isset($options['items']) ? $options['items'] : [];

$key_index = count($configs);
$key_index_item = count($items);
?>

<div class="modal-header" style="margin-bottom: 15px;">
  <h4 class="modal-title" class="pull-left"><?= Yii::t('ezmodule', 'Widget Config') ?></h4>
  <?= Html::button('<i class="fa fa-plus"></i> ' . Yii::t('thaihis', " Add more order"), ['class' => 'btn btn-success pull-right', 'id' => 'btn-add-more-order']) ?>
  <?= Html::button('<i class="fa fa-plus"></i> ' . Yii::t('thaihis', " Add more item order"), ['class' => 'btn btn-success pull-right', 'id' => 'btn-add-more-order-item', 'style' => 'display:none;']) ?>
</div>
<ul class="nav nav-tabs">
  <li class="active"><a class="tab-header" id="tabHeader" href="#">Order header</a></li>
  <li ><a href="#" class="tab-header" id="tabOrderItem">Item of order header</a></li>
</ul>
<div class="panel panel-default">
  <div class="panel-body " id="display-config-order-header">

  </div>
  <div class="panel-body " id="display-config-order-item" style="display: none;">

  </div>
</div>

<div class="form-group row">
  <div class="col-md-12">
    <?= Html::radioList('options[oipd_type]', (isset($options['oipd_type']) ? $options['oipd_type'] : 'OPD'), ['OPD' => 'OPD', 'IPD' => 'IPD']); ?>
  </div>
</div>

<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>
<script>
    var key_index = '<?= $key_index ?>';
    var key_index_item = '<?= $key_index_item ?>';
    $(function () {
      var configs = <?= json_encode($configs) ?>;
      var items = <?= json_encode($items) ?>;
      $.each(configs, function (i, e) {
        onLoadConfig(i);
      });

      $.each(items, function (i, e) {
        onLoadConfigItem(i);
      });
    });

    $('#tabHeader').click(function () {
      $('#tabOrderItem').parent().removeClass('active');
      $('#btn-add-more-item-order').css('display', 'none');
      $('#btn-add-more-order').css('display', 'block');
      $('#display-config-order-header').css('display', 'block');
      $('#display-config-order-item').css('display', 'none');
      $(this).parent().addClass('active');

    });

    $('#tabOrderItem').click(function () {
      $('#tabHeader').parent().removeClass('active');
      $('#btn-add-more-item-order').css('display', 'block');
      $('#btn-add-more-order').css('display', 'none');
      $('#display-config-order-header').css('display', 'none');
      $('#display-config-order-item').css('display', 'block');
      $(this).parent().addClass('active');
    });

    function onLoadConfig(index) {
      var configs = <?= json_encode($configs) ?>;
      var ezf_id = '<?= $ezf_id ?>';
      var div = $('#display-config-order-header');
      var url = '<?= Url::to(['/patient/cashier2/add-more-order']) ?>';

      $.get(url, {key_index: index, configs: configs, ezf_id: ezf_id}, function (result) {
        div.append(result);
      });
    }

    function onLoadConfigItem(index) {
      var items = <?= json_encode($items) ?>;
      var ezf_id = '<?= $ezf_id ?>';
      var div = $('#display-config-order-item');
      var url = '<?= Url::to(['/patient/cashier2/add-more-item-order']) ?>';

      $.get(url, {key_index: index, items: items, ezf_id: ezf_id}, function (result) {
        div.append(result);
      });
    }

    $('#btn-add-more-order').on('click', function () {
      var configs = <?= json_encode($configs) ?>;
      var ezf_id = '<?= $ezf_id ?>';
      var div = $('#display-config-order-header');
      var url = '<?= Url::to(['/patient/cashier2/add-more-order']) ?>';

      $.get(url, {key_index: key_index, configs: configs, ezf_id: ezf_id}, function (result) {
        key_index++;
        div.append(result);
      });
    });

    $('#btn-add-more-order-item').on('click', function () {
      var items = <?= json_encode($items) ?>;
      var ezf_id = '<?= $ezf_id ?>';
      var div = $('#display-config-order-item');
      var url = '<?= Url::to(['/patient/cashier2/add-more-item-order']) ?>';

      $.get(url, {key_index: key_index_item, configs: items, ezf_id: ezf_id}, function (result) {
        key_index_item++;
        div.append(result);
      });
    });

    function getMilisecTime() {
      var d = new Date();
      var key_index = d.getFullYear() + '' + d.getMonth() + '' + d.getDate() + '' + d.getHours() + '' + d.getMinutes() + '' + d.getSeconds() + '' + d.getMilliseconds();
      return key_index;
    }

</script>
<?php \richardfan\widget\JSRegister::end(); ?>
