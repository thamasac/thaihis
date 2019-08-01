<?php

use yii\helpers\Url;
use appxq\sdii\widgets\ModalForm;
?>
<table class="table" style="margin-bottom: 0px">      
  <tbody>    
      <?php
      $i = 0;
      $chkOrderDate = '';
      foreach ($dataOrderGroupDetail as $value) :
          if ($i == 0) {
              ?>
            <tr class="warning ">
              <td><?= \appxq\sdii\utils\SDdate::mysql2phpThDateSmallYear($value['order_date']) ?></td>
              <td><?= Yii::t('patient', 'Order Name') ?></td>
              <td><?= Yii::t('patient', 'Not pay') ?></td>
              <td><?= Yii::t('patient', 'Pay') ?></td>
              <td><?= Yii::t('patient', 'Sum') ?></td>
            </tr>
            <?php
        }
        if ($chkOrderDate !== $value['order_date'] && $i !== 0) {
            ?>
            <tr class="warning ">
              <td colspan="5"><?= \appxq\sdii\utils\SDdate::mysql2phpThDateSmallYear($value['order_date']) ?></td>              
            </tr>
            <?php
            $chkOrderDate = $value['order_date'];
        }
        ?>
        <tr class="info" data-code="<?= $value['order_code'] ?>">
          <td><?= $value['order_code'] ?></td>
          <td><?= $value['order_name'] ?></td>
          <td><?= $value['item_notpay'] ?></td>
          <td><?= $value['item_pay'] ?></td>
          <td><?= $value['item_pay'] + $value['item_notpay'] ?></td>
        </tr>
        <?php
        $i++;
    endforeach;
    ?>
  </tbody>
</table>
<?php
$url = Url::to(['/patient/order/order-admit-group', 'admit_id' => $admit_id, 'visit_id' => $visit_id, 'reloadDiv' => $reloadDiv]);
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>
<script>
    $('.card-admit-order table tbody tr').on('click', function (e) {
      let group_code = $(this).attr('data-code');

      if (group_code) {
        getUiAjax('<?= $url ?>', '<?= $reloadDiv ?>');
      }
    });
</script>