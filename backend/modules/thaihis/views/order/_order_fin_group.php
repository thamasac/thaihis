<?php

use yii\helpers\Url;
use appxq\sdii\widgets\ModalForm;
?>
<table class="table" style="margin-bottom: 0px"> 
  <thead>
    <tr>
      <td><?= Yii::t('patient', 'Group') ?></td>
      <td><?= Yii::t('patient', 'Order Name') ?></td>
      <td><?= Yii::t('patient', 'Not pay') ?></td>
      <td><?= Yii::t('patient', 'Pay') ?></td>
      <td><?= Yii::t('patient', 'Sum') ?></td>
    </tr>
  </thead>
  <tbody>    
      <?php
      $i = 0;
      foreach ($dataOrderGroup as $value) :
          ?>
        <tr <?php if ($i % 2 == 0) { ?>class="info"<?php } ?> data-code="<?= $value['drg_item_code'] ?>">
          <td><?= $value['drg_item_code'] ?></td>
          <td><?= $value['drg_item_desc'] ?></td>
          <td><?= $value['group_notpay'] ?></td>
          <td><?= $value['group_pay'] ?></td>
          <td><?= $value['group_pay'] + $value['group_notpay'] ?></td>
        </tr>
        <?php
        $i++;
    endforeach;
    ?>
  </tbody>
</table>
<?php
echo ModalForm::widget([
    'id' => 'modal-order-xxl',
    'size' => 'modal-xxl',
    'tabindexEnable' => false,
]);
$url = Url::to(['/patient/order/order-admit-group-detail', 'admit_id' => $admit_id, 'visit_id' => $visit_id, 'reloadDiv' => $reloadDiv, 'group_code' => '']);
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>
<script>
    $('.card-admit-order table tbody tr').on('click', function (e) {
      let group_code = $(this).attr('data-code');

      if (group_code) {
        getUiAjax('<?= $url ?>' + group_code, '<?= $reloadDiv ?>');
      }
    });
</script>