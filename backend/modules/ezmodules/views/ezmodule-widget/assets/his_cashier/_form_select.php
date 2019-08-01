<?php

use yii\helpers\Html;
use yii\helpers\Url;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$attrname_fields = [];
$value_fields = [];
$val = isset($val) ? $val : null;
$nameType = isset($nameType) ? $nameType : 'configs';
?>
<div class="form-group row" id="content-select<?= $sub_index ?>">
  <div class="col-md-6">
      <?php
      $attrname_fields = 'options[' . $nameType . '][' . $key_index . '][selects][' . $sub_index . '][field]';
      $value_fields = isset($val['field']) ? $val['field'] : '';
      ?>
    <div id="ref_field<?= $sub_index ?>_box" data-name="<?= $attrname_fields ?>" data-value="<?= $value_fields ?>">

    </div>
  </div>
  <div class="col-md-3 sdbox-col">
      <?php
      $attrname_custom_val = 'options[' . $nameType . '][' . $key_index . '][selects][' . $sub_index . '][custom_val]';
      $value_custom_val = isset($val['custom_val']) ? $val['custom_val'] : '';
      ?>
      <?= Html::textInput($attrname_custom_val, $value_custom_val, ['class' => 'form-control']) ?>
  </div>
  <div class="col-md-2 sdbox-col">
      <?php
      $attrname_alias_name = 'options[' . $nameType . '][' . $key_index . '][selects][' . $sub_index . '][alias_name]';
      $value_alias_name = isset($val['alias_name']) ? $val['alias_name'] : '';
      ?>

    <?= Html::textInput($attrname_alias_name, $value_alias_name, ['class' => 'form-control']) ?>
  </div>
  <div class="col-md-1 sdbox-col">
    <?= Html::button('<i class="fa fa-trash"></i>', ['class' => 'btn btn-danger', 'id' => 'btn-remove-select' . $sub_index, 'data-key_index' => $sub_index]) ?>
  </div>
</div>
<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>
<script>
    $(function () {
      var key_index = '<?= $sub_index ?>';
      field<?= $sub_index ?>(key_index);
    });

    function field<?= $sub_index ?>(key_index) {
      var renderDiv = $('#ref_field' + key_index + '_box');
      var ezf_id = <?= json_encode($ezf_id) ?>;
      var main_ezf_id = '<?= $main_ezf_id ?>';
      var value = '<?= $value_fields ?>';
      var name = '<?= $attrname_fields ?>';

      $.post('<?= Url::to(['/thaihis/configs/get-fields-forms']) ?>', {ezf_id: ezf_id, main_ezf_id: main_ezf_id, multiple: 0, name: name, value: value, id: 'config_header_select_field' + key_index}
      ).done(function (result) {
        renderDiv.html(result);
      }).fail(function () {
<?= \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') ?>
        console.log('server error');
      });
    }

    $(document).on('click', '[id^=btn-remove-select]', function () {
      var key_index = $(this).attr('data-key_index');
      var div_select = $('#content-select' + key_index);
      div_select.remove();
    });

</script>
<?php \richardfan\widget\JSRegister::end(); ?>