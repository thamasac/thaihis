<?php

use kartik\helpers\Html;
?>
<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
  <h4 class="modal-title" id="itemModalLabel">รายละเอียดค่าใช้จ่าย <?= 'HN : ' . $data[0]['pt_hn'] . ' ชื่อ-สกุล : ' . $data[0]['fullname'] . ' สิทธิ : ' . $data[0]['right_name'] ?></h4>
</div>
<?= Html::beginForm(['main-report/save-data'], 'post', ['enctype' => 'multipart/form-data', 'id' => 'form-list']) ?>
<div class="modal-body">
  <div class="row">
    <div id="itemlists">

      <?php
      $i = 1;
      $ACC_GROUP = '';
      $sumTotalPay = 0;
      $sumTotalNotPay = 0;
      $sumNOTPAY = 0;
      $sumPAY = 0;
      $tmt_code = '';
      foreach ($data as $value) {
          $sumPAY += $value['sumpay'];
          $sumNOTPAY += $value['sumnotpay'];
          ?>
          <div class="row" id="row-<?= $i ?>">
            <div class="col-md-1 text-right">
                <?= $value['fin_item_group_code'] ?>
            </div>
            <div class="col-md-5"><?= $value['order_name']; ?></div>
            <div class="col-md-1"><?= Html::input('text', 'qty[]', $value['order_qty'], ['class' => 'form-control']) ?></div>
            <div class="col-md-3"><?= Html::input('text', 'sumnotpay[]', number_format($value['sumnotpay'], 2, ".", ","), ['class' => 'form-control t1']) ?></div>
            <div class="col-md-2"><button type="button" class="btn btn-link" onclick="remove('row-<?= $i ?>')"><i class="glyphicon glyphicon-remove" style="color:#ff0000;font-size: 20px"></i></button></div>
            <input type="hidden" class="form-control" name="order_code[]" value="<?= $value['order_code']; ?>">
            <?php if ($value['fin_item_group_code'] == '03'): ?>
                <input type="hidden" class="form-control" name="item_reqno[]" value="<?= $value['order_code'] ?>"> 
            <?php else: ?>
                <input type="hidden" class="form-control" name="item_reqno[]" value=""> 
            <?php endif; ?>
            <input type="hidden" class="form-control" name="doctorcode[]" value="<?php echo @$value['certificate'] ?>">
            <input type="hidden" class="form-control" name="item_group[]" value="<?= $value['fin_item_group_code'] ?>">
            <input type="hidden" class="form-control" name="sks_code[]" value="<?= $value['sks_code'] ?>">
          </div>
          <?php
          $i++;
          $tmt_code = '';
      }
      $sumTotalPay += $sumPAY;
      $sumTotalNotPay += $sumNOTPAY;
      ?>
      <div id="addRows"></div>
      <div>
        <div class="col-md-1 text-right"></div>
        <div class="col-md-5"></div>
        <div class="col-md-1"></div>
        <div class="col-md-3"></div>
        <div class="col-md-2"><button type="button" class="btn btn-link" onclick="add()"><i class="glyphicon glyphicon-plus" style="color:#398439;font-size: 20px"></i></button></div>
      </div>
      <div>
        <div class="col-md-1 text-right">Approval Code</div>
        <div class="col-md-5"><?= Html::input('text', 'app_code', !empty($data[0]['app_code']) ? $data[0]['app_code'] : '', ['class' => 'form-control']) ?></div>
        <div class="col-md-1">&nbsp;</div>
        <div class="col-md-3">&nbsp;</div>
        <div class="col-md-2">&nbsp;</div>
      </div>
      <div>
        <div class="col-md-1 text-right">
          เลขที่
        </div>
        <input type="hidden" name="pt_hn" value="<?= $data[0]['pt_hn']; ?>" />
        <input type="hidden" name="visit_date" value="<?= $data[0]['visit_date']; ?>" />
        <div class="col-md-5"><?= Html::input('text', 'receipt_visit_id', $receipt_visit_id, ['class' => 'form-control', 'readonly' => true]) ?></div>
        <div class="col-md-1">รวม</div>
        <div class="col-md-3"><?= Html::input('text', '', number_format($sumTotalNotPay, 2, ".", ","), ['class' => 'form-control', 'id' => 'sumnotpay']) ?></div>
        <div class="col-md-2"></div>
      </div>
    </div>
  </div>
</div>
<div class="modal-footer">
  <button type="submit" class="btn btn-primary btn-submit" >Submit</button>
  <button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button>
</div>
<?= Html::endForm() ?>
<?php
\richardfan\widget\JSRegister::begin([
    'position' => \yii\web\View::POS_END
]);
?>
<script>
    $('#itemlists').on('change', '.t1', function () {
      sumprice();
    });

    function sumprice() {
      var total_price = 0;
      var _val = 0;
      $('#itemlists .t1').each(function () {
        _val = $(this).val().replace(',', '');
        total_price += parseFloat(_val !== '' ? _val : 0);
      });
      $('#sumnotpay').val(format2(total_price));
    }

    function remove(rowid) {
      $('#' + rowid).remove();
      sumprice();
    }

    function add() {
      var url = '<?php echo yii\helpers\Url::to(['item']) ?>';
      var divid = 'addRows';
      getUiAjax(url, divid);
    }

    function getUiAjax(url, divid) {
      $.ajax({
        method: 'POST',
        url: url,
        dataType: 'HTML',
        success: function (result, textStatus) {
          $('#' + divid).append(result);
        }
      });
    }
    $('.print-order-report').on('click', function () {
      var url = $(this).attr('href');
      myWindow = window.open(url, '_blank');
      return false;
    });

    function format2(n) {
      return n.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,");
    }

    $('#form-list').submit(function (e) {

      var $form = $(this);
      var formData = new FormData($(this)[0]);

      $.ajax({
        url: $form.attr('action'),
        type: 'POST',
        data: formData,
        dataType: 'JSON',
        enctype: 'multipart/form-data',
        processData: false, // tell jQuery not to process the data
        contentType: false, // tell jQuery not to set contentType
        success: function (result) {
          if (result.status == 'success') {
            if (result.status == "error") {
              var noty_id = noty({"text": result.message, "type": result.status,
                "buttons": [{type: "btn btn-default", text: "Ok", click: function ($noty) {
                      $noty.close();
                    }
                  },
                ], "timeout": false});
            } else {
              var noty_id = noty({"text": result.message, "type": result.status});
              $('#btn-search').trigger("click");
            }
            $(document).find('#modal-order-counter').modal('hide');
          }
        },
        error: function () {
        }
      });

      return false;
    });


</script>
<?php \richardfan\widget\JSRegister::end(); ?>