
<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
  <h4 class="modal-title" id="itemModalLabel">รายละเอียดค่าใช้จ่าย <?= 'HN : ' . $data[0]['pt_hn'] . ' ชื่อ-สกุล : ' . $data[0]['fullname'] . ' สิทธิ : ' . $data[0]['right_name'] ?></h4>
</div>
<div class="modal-body">
  <div class="row">
    <div class="col-md-12">  
      <table class="table table-bordered" id="table">
        <thead>
          <tr>
            <td>หมวด</td>
            <td>รหัสเบิก</td>
            <td>รหัสรายการ</td>
            <td>ชื่อรายการ</td>
            <td>ชำระเอง</td>
            <td>เบิกได้</td>            
          </tr>
        </thead>
        <tbody>
            <?php
            $i = 1;
            $ACC_GROUP = '';
            $sumTotalPay = 0;
            $sumTotalNotPay = 0;
            $sumNOTPAY = 0;
            $sumPAY = 0;
            foreach ($data as $value) {
                if ($ACC_GROUP != $value['fin_item_group_code'] AND $i != 1) {
                    ?>
                  <tr class="alert alert-warning">
                    <td colspan="4" class="text-right"><strong><?= 'รวมหมวด ' . $ACC_GROUP ?></strong></td>
                    <td><strong><?= number_format($sumPAY, 2, ".", ","); ?></strong></td> 
                    <td><strong><?= number_format($sumNOTPAY, 2, ".", ",") ?></strong></td>
                  </tr>  
                  <?php
                  $sumTotalPay += $sumPAY;
                  $sumTotalNotPay += $sumNOTPAY;
                  $sumNOTPAY = 0;
                  $sumPAY = 0;
                  $ACC_GROUP = $value['fin_item_group_code'];
              } else {
                  $ACC_GROUP = $value['fin_item_group_code'];
              }
              $sumPAY += $value['sumpay'];
              $sumNOTPAY += $value['sumnotpay'];
              ?>
              <tr>
                <td><?= $i ?></td> 
                <td><?= $value['sks_code']; ?></td>
                <td><?= $value['order_code']; ?></td>
                <td><?= $value['order_name']; ?></td>
                <td><?= number_format($value['sumpay'], 2, ".", ","); ?></td> 
                <td><?= number_format($value['sumnotpay'], 2, ".", ",") ?></td>
              </tr>                
              <?php
              $i++;
          }
          $sumTotalPay += $sumPAY;
          $sumTotalNotPay += $sumNOTPAY;
          ?>
          <tr class="alert alert-warning">
            <td colspan="4" class="text-right"><strong><?= 'รวมหมวด ' . $ACC_GROUP ?></strong></td>
            <td><strong><?= number_format($sumPAY, 2, ".", ","); ?></strong></td> 
            <td><strong><?= number_format($sumNOTPAY, 2, ".", ",") ?></strong></td>
          </tr>  
          <tr class="alert alert-info">
            <td colspan="4" class="text-right"><strong><?= 'รวม' ?></strong></td>
            <td><strong><?= number_format($sumTotalPay, 2, ".", ","); ?></strong></td> 
            <td><strong><?= number_format($sumTotalNotPay, 2, ".", ",") ?></strong></td>
          </tr>  
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>
<script>
    $('.print-order-report').on('click', function () {
      var url = $(this).attr('href');
      myWindow = window.open(url, '_blank');
      return false;
    });

</script>
<?php \richardfan\widget\JSRegister::end(); ?>