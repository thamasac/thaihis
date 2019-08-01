<?php

use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use kartik\helpers\Html;
use yii\helpers\Url;
use kartik\widgets\Select2;
use yii\web\JsExpression;

\backend\modules\ezforms2\assets\ListdataAsset::register($this);
if ($params['cashier_status'] == '1') {
    $url = \yii\helpers\Url::to(['/patient/cashier2/save-receipt', 'visit_id' => $visit_id,]);
} else {
    $url = \yii\helpers\Url::to(['/patient/cashier2/cancel-receipt', 'receipt_id' => $params['receipt_id'], 'params' => backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String($params)]);
}
$right_pay = FALSE;
if (isset($data['0']['right_code']) && in_array($data['0']['right_code'], ['ORI', 'ORI-G', 'CASH', null])) {
    $right_pay = TRUE;
}
?>
<form name="cashier-receive" id="cashier-receive" action="<?= $url ?>" method="post" class="h4" <?= ($right_pay ? 'data-pay="true"' : '') ?>>
  <div class="row">
    <div class="col-sm-9">
        <?php if ($params['cashier_status'] == '1') : ?>
          <div class="h5">
            <label class="col-md-1" style="padding-right: 0px">
              สิทธิ :
            </label>
            <div class="col-md-3">       
                <?php
                $item = ['CASH' => 'เงินสด', 'UCS' => 'หลักประกันสุขภาพ', 'OFC' => 'ข้าราชการ (ต่อเนื่อง)', 'SSS' => 'ประกันสังคม', 'LGO' => 'ข้าราชการ อปท.', 'PRO' => 'ตรวจสุขภาพหน่วยงาน', 'ORI' => 'ต้นสังกัด', 'ORI-G' => 'รัฐวิสาหกิจ'];
                echo Html::dropDownList('receipt_right_code', isset($data[0]['right_code']) ? $data[0]['right_code'] : 'CASH', $item, ['id' => 'receipt_right_code', 'class' => 'form-control', 'style' => 'margin-bottom: 5px;']);
                ?>
            </div>
            <div id="group-project">
              <label class="col-md-2 text-right">
                หน่วยงาน :
              </label>
              <div class="col-md-3">       
                  <?php
                  $ezf_table = \backend\modules\patient\Module::$formTableName['project'];
                  $dataProject = \backend\modules\ezforms2\classes\EzfUiFunc::loadTbData($ezf_table, isset($data[0]['right_project_id']) ? $data[0]['right_project_id'] : '');

                  $url = \yii\helpers\Url::to(['/patient/restful/get-list-project']);
                  echo Select2::widget([
                      'id' => 'receipt_project_id',
                      'name' => 'receipt_project_id',
                      'value' => isset($dataProject['id']) ? $dataProject['id'] : '',
                      'initValueText' => isset($dataProject['project_name']) ? $dataProject['project_name'] : '',
                      'options' => ['placeholder' => 'เลือกหน่วยงาน'],
                      'pluginOptions' => [
                          'allowClear' => true,
                          'minimumInputLength' => 3,
                          'language' => [
                              'errorLoading' => new JsExpression("function () { return 'กำลังค้นหา...'; }"),
                          ],
                          'ajax' => [
                              'url' => $url,
                              'dataType' => 'json',
                              'data' => new JsExpression('function(params) { return {q:params.term}; }')
                          ],
                          'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                          'templateResult' => new JsExpression('function(city) { return city.text; }'),
                          'templateSelection' => new JsExpression('function (city) { return city.text; }'),
                      ],
                  ]);
//                  }
                  ?>
              </div>
            </div>
          </div>
      <?php endif; ?>
      <div class="col-md-12">  
        <table class="table table-bordered">
          <thead>
            <tr class="success">
              <td class="col-md-1">
                  <?= Yii::t('patient', 'Order Number'); ?>
              </td>
              <td class="col-md-1">
                  <?= Yii::t('patient', 'Group'); ?>
              </td>
              <td class="col-md-6">
                  <?= Yii::t('patient', 'Order Name'); ?>
              </td>              
              <td class="col-md-2">
                  <?= Yii::t('patient', 'Pay'); ?>
              </td> 
              <td class="col-md-2">
                  <?= Yii::t('patient', 'Not pay'); ?>
              </td>
            </tr>
          </thead>
          <tbody>
              <?php
              $sum_notpay = 0;
              $sum_pay = 0;
              $i = 1;
              foreach ($data as $value) {
                  if (isset($params['cashier_status']) && $params['cashier_status'] == '1') {
                      if ($value['item_type'] == 'DRUG') {
                          if (isset($value['right_code']) && $value['right_code'] == 'OFC') {
                              $value['notpay'] = $value['notpay'];
                              $value['pay'] = $value['pay'];
                          } else {
                              $value['notpay'] = $value['pay'] + $value['notpay'];
                              $value['pay'] = 0;
                          }
                      } elseif ($value['visit_type'] == '1') {
                          if (isset($value['right_code']) && $value['right_code'] == 'CASH') {
                              $value['pay'] = $value['pay'] + $value['notpay'];
                              $value['notpay'] = 0;
                          } elseif (isset($value['right_code']) && $value['right_code'] == 'ORI-G') {
                              $value['notpay'] = $value['pay'] + $value['notpay'];
                              $value['pay'] = 0;
                          }
                      }
                  }
                  ?>                
                <tr class="info" data-key="<?= $value['order_fin_code'] ?>" style="touch-action:manipulation;cursor:pointer;">
                  <td><?= $i; ?></td> 
                  <td><?= $value['fin_group_code']; ?></td> 
                  <td><?= $value['order_fin_name']; ?></td>
                  <td id="sum_pay_<?= $value['order_fin_code'] ?>" class="sum-price">
                      <?php
                      echo number_format($value['pay'], 2, ".", ",");
                      $sum_pay += $value['pay'];
                      ?>
                  </td>
                  <td id="sum_notpay_<?= $value['order_fin_code'] ?>" class="sum-price-notpay">
                      <?php
                      echo number_format($value['notpay'], 2, ".", ",");
                      $sum_notpay += $value['notpay'];
                      ?>
                  </td>
                </tr>
                <tr id="tr_<?= $value['order_fin_code'] ?>" class="collapse out">
                  <td style = "border:none;"></td>
                  <td colspan = "4">
                    <table id = "table_<?= $value['order_fin_code'] ?>" class = "table table-bordered" style = "margin-bottom:0px ">
                      <tbody>
                          <?php
                          //if (in_array($value['fin_group_code'], ['3', '5', '2'])) {
                          if ($value['item_type'] == 'DRUG') {
                              if (isset($params['cashier_status']) && $params['cashier_status'] == '1') {
                                  $data_item = \backend\modules\patient\classes\CashierQuery::getCashierItemDrug($value['order_fin_code'], $params, $unit_id);
                              } else {
                                  $data_item = \backend\modules\patient\classes\CashierQuery::getCashierItemDrug2($value['order_fin_code'], $cashier_id);
                              }
                          } else {
                              if (isset($params['cashier_status']) && $params['cashier_status'] == '1') {
                                  $data_item = \backend\modules\patient\classes\CashierQuery::getCashierItem($value['order_fin_code'], $params, $unit_id, $cashier_id);
                              } else {
                                  $data_item = \backend\modules\patient\classes\CashierQuery::getCashierItemStatus2($value['order_fin_code'], $cashier_id);
                              }
                          }

                          foreach ($data_item as $value_item) {
                              ?>                
                            <tr id="<?= $value_item['item_id'] ?>" class="<?= $value_item['order_tran_status'] !== '1' ? 'bg-danger' : '' ?>">
                              <td class="col-md-2">
                                  <?= $value_item['nhso_code']; ?>
                                <input type="text" value="<?= $value_item['order_code'] ?>" name="order_check[item_code][]" class="hidden">
                              </td> 
                              <td class="col-md-6">
                                  <?php
                                  echo $value_item['order_name'];
                                  ?>
                              </td>
                              <td class="col-md-1">
                                  <?= $value_item['order_qty']; ?>
                              </td>
                              <td class="col-md-2">
                                  <?php
                                  if (isset($params['cashier_status']) && $params['cashier_status'] == '1') {
                                      if ($value['item_type'] == 'DRUG') {
                                          if (isset($value['right_code']) && $value['right_code'] == 'OFC') {
                                              $value_item['pay'] = $value_item['pay'];
                                              $value_item['notpay'] = $value_item['notpay'];
                                          } else {
                                              $value_item['notpay'] = $value_item['pay'] + $value_item['notpay'];
                                              $value_item['pay'] = 0;
                                          }
                                      } elseif ($value['visit_type'] == '1') {
                                          if (isset($value['right_code']) && $value['right_code'] == 'CASH') {
                                              $value_item['pay'] = $value_item['pay'] + $value_item['notpay'];
                                              $value_item['notpay'] = 0;
                                          } elseif ($value['right_code'] == 'ORI-G') {
                                              $value_item['notpay'] = $value_item['pay'] + $value_item['notpay'];
                                              $value_item['pay'] = 0;
                                          }
                                      }
                                  }
                                  ?>
                                <input type="text" name="order_check[pay][]" class="form-control pay" value="<?= $value_item['pay'] ?>">
                              </td>
                              <td class="col-md-2">
                                <input type="text" name="order_check[notpay][]" class="form-control notpay" value="<?= $value_item['notpay'] ?>"/>
                              </td>
                              <td class="text-center col-md-1">   
                                <span class="button-checkbox">
                                  <button type="button" class="btn btn-sm btn-default" data-color="success">
                                    <i class="state-icon glyphicon glyphicon-unchecked"></i>&nbsp; <?= Yii::t('patient', 'Select'); ?> &nbsp;&nbsp;
                                  </button>
                                  <input type="checkbox" value="<?= $value_item['item_id'] ?>" name="order_check[item_id][]" class="hidden" checked>
                                  <input type="checkbox" value="<?= $value_item['type'] ?>" name="order_check[type][]" class="hidden" checked>
                                </span>
                              </td>
                            </tr>                
                            <?php
                        }
                        ?>
                        <?php
                        \richardfan\widget\JSRegister::begin([
                            //'key' => 'bootstrap-modal',
                            'position' => \yii\web\View::POS_READY
                        ]);
                        ?>
                      <script>
                          $('#table_<?= $value['order_fin_code'] ?> input[type="checkbox"]').on('change', function () {
                            if (!this.checked) {
                              $('#' + $(this).val() + ' input[type="text"]').attr('disabled', true);
                              $(this).parent().parent().parent().attr('style', 'color:grey;');
                            } else {
                              $('#' + $(this).val() + ' input[type="text"]').removeAttr('disabled');
                              $(this).parent().parent().parent().removeAttr('style');
                            }
                            sumPrice('<?= $value['order_fin_code'] ?>');
                          });

                          $('#table_<?= $value['order_fin_code'] ?> input[type="text"]').on('keyup', function () {
                            sumPrice('<?= $value['order_fin_code'] ?>');
                          });
                      </script>
                      <?php \richardfan\widget\JSRegister::end(); ?>
              </tbody>
            </table>
            </td>
            </tr>
            <?php
            $i++;
        }
        ?>
        <tr class="success">              
          <td colspan="3" class="text-right">รวม</td>
          <td class="total-pay text-warning">
              <?php
              echo number_format($sum_pay, 2, ".", ",");
              ?>
          </td>
          <td class="total-notpay text-warning">
              <?php
              echo number_format($sum_notpay, 2, ".", ",");
              ?>
          </td>
        </tr>
        </tbody>
        </table>
      </div>

    </div>

    <div class="col-sm-3">
        <?php
        if ($params['cashier_status'] == '1') {
            $userProfile = Yii::$app->user->identity->profile->attributes;
            echo \backend\modules\patient\classes\PatientHelper::uiReceiptNo($userProfile['user_id'], 'receipt_no');
        }
        ?>
      <div id="div_type_mony">
          <?php if ($params['cashier_status'] == '1') { ?>
            <label class="control-label" for="inputWarning1">รวม</label>
            <input type="text" id="total_pay2" name="total_pay" style="color: green;background-color:black;height: 70px;text-align:right;font-size: 50px" class="form-control" value="<?php
            if (isset($value['right_code']) && in_array($value['right_code'], ['ORI', 'ORI-G', 'CASH', null])) {
                $totalPrice = $sum_notpay + $sum_pay;
                echo number_format($totalPrice, 2, ".", ",");
            } else {
                echo number_format($sum_pay, 2, ".", ",");
            }
            ?>" />
            <label class="control-label" for="inputWarning1">เงินสด</label>
            <input type="number" name="ReciveMony" id="ReciveMony" style="height: 70px;text-align:right;font-size: 50px" class="form-control"/>
            <label class="control-label"  for="inputWarning1">เงินทอน</label>
            <input type="text" readonly="" name="Tronmony" id="Tronmony" value="0.00" style="height: 70px;text-align:right;font-size: 50px" class="form-control"/>
            <input type="hidden" name="right_id" value="<?= isset($data[0]['right_id']) ? $data[0]['right_id'] : '' ?>" class="form-control"/>
            <br>
            <label class="control-label" for="inputWarning1">ประเภทการชำระ</label>
            <select name="type_mony" id="type_mony" class="form-control">
              <option value="เงินสด">เงินสด</option>
              <option value="บัตรเครดิต">บัตรเครดิต</option>
              <option value="โอนเงิน">โอนเงิน</option>
              <option value="netbank">Netbank</option>
              <option value="promptpay">PromptPay</option>
              <option value="เช็คเงินสด">เช็คเงินสด</option>
              <option value="เช็คของขวัญ">เช็คของขวัญ</option>
              <option value="ตั๋วแลกเงิน">ตั๋วแลกเงิน</option>
            </select>
            <div id="div_credit_id" class="hidden">
              <label class="control-label" for="inputWarning1">หมายเลขบัตรเครดิต/บัญชี</label>
              <input type="text" name="credit_id" id="credit_id" class="form-control"/>
            </div>
            <br>
            <a onclick="SendSubmitForm()" class="btn btn-primary btn-block"  value="1">ชำระเงิน</a>
            <?php
        } else {
            $receipt_right = \backend\modules\patient\classes\CashierQuery::getReceiptRightShow($params['receipt_id']);
            ?>
            <div class="card-block text-center">
              <strong class="text-info">สิทธิ : <?= $receipt_right['right_name'] ?></strong>
              <?php if ($receipt_right['project_name']) { ?>
                  <strong class="text-info">หน่วยงาน : <?= $receipt_right['project_name'] ?></strong>
                  <?php
              }
              if ($receipt_right['book_no']) {
                  ?>
                  <div>
                    <strong class="text-info">ใบเสร็จเลขที่ : <?= $receipt_right['book_no'] . '/' . $receipt_right['book_num'] ?></strong>
                  </div>
              <?php } ?>
            </div>
            <div style="margin-bottom:5px;">
              <strong>ระบุเหตุผลที่ยกเลิก</strong>
              <input id="form-token" type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>"/>
              <input type="text" value="" name="comment" class="form-control" style="margin-bottom:5px;">
              <button type="submit" class="btn btn-danger btn-block" name="submit" value="1">ยกเลิก</button>
            </div>
            <?php
            echo Html::a('<i class="fa fa-print"></i> ' . Yii::t('patient', 'Print'), '#', [
                'data-key' => $visit_id,
                'data-status' => $params['receipt_id'],
                'data-action' => 'print',
                'class' => 'btn btn-warning btn-block printaa',
            ]);

            echo Html::a('<i class="fa fa-print"></i> ' . Yii::t('patient', 'Print Detail'), '#', [
                'data-key' => $visit_id,
                'data-status' => $params['receipt_id'],
                'data-action' => 'print-detail',
                'class' => 'btn btn-success btn-block printaa',]);
        }
        ?>
      </div>
    </div>

  </div>
</form>
<?php
$url_print = Url::to(['/patient/cashier2/print-receipt2', 'receipt_id' => $params['receipt_id'],]);
$url_print_detail = Url::to(['/patient/cashier2/print-receipt-detail', 'receipt_id' => $params['receipt_id'],]);
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>
<script>
    function getUiAjax(url, divid) {
      $.ajax({
        method: 'POST',
        url: url,
        dataType: 'HTML',
        success: function (result, textStatus) {
          $('#' + divid).html(result);
        }
      });
    }

    $('#div_profile_patient').on('hidden.bs.modal', '#modal-md-profile', function (e) {
      var objOrder = $('#view-pt').attr('data-url');
      getUiAjax(objOrder, 'view-pt');
    });

    $('#receipt_right_code').on('change', function () {
      hideSelectProject();
      if ($(this).val() === 'ORI' || $(this).val() === 'ORI-G' || $(this).val() === 'CASH') {
        $('#cashier-receive').attr('data-pay', 'true');
      } else {
        $('#cashier-receive').removeAttr('data-pay');
      }
      sumTotalPay();
      sumTotalNotPay();
    });

    hideSelectProject();

    function hideSelectProject() {
      if ($('#receipt_right_code').val() !== 'PRO') {
        $('#receipt_project_id').val('').trigger('change');
        $('#group-project').addClass('hidden');
      } else {
        $('#group-project').removeClass('hidden');
      }
    }

    $('#type_mony').change(function () {
      if ($(this).val() != 'เงินสด') {
        $('#div_credit_id').removeClass('hidden');
      } else {
        $('#div_credit_id').addClass('hidden');
      }
    });

    $('.printaa').on('click', function () {
      var action = $(this).attr('data-action');
      if (action === 'print') {
        myWindow = window.open('<?php echo $url_print ?>' + '&right_code=<?= isset($receipt_right) ? $receipt_right['right_code'] : '' ?>', '_blank');
      } else if (action === 'print-detail') {
        myWindow = window.open('<?php echo $url_print_detail ?>', '_blank');
      }
    });

    $("#ReciveMony").focus();
    $('#ReciveMony').on('keyup', function () {
      var str_total_pay = $('#total_pay2').val();
      var res = str_total_pay.replace(",", "");
      var sum_total = $(this).val() - Number(res);
      $('#Tronmony').val(sum_total);
    });
    var clicked = false;
    function SendSubmitForm() {
      if (clicked == false) {
        clicked = true;
        var str_total_pay = $('#total_pay2').val();
        var res = parseFloat(str_total_pay.replace(",", ""));
        var ReciveMony = parseFloat($('#ReciveMony').val());
        if (res > 0) {
          if ($('input[name="receipt_book_no"]').val() === '' || $('input[name="receipt_tr_no"]').val() === '') {
            yii.confirm('<h4>กรุณาเพิ่มเลขที่ใบเสร็จรับเงิน</h4>', function () {
            });
            clicked = false;
            return false;
          }
          if ($('input[name="receipt_tr_no"]').val() > 100) {
            yii.confirm('<h4>กรุณาเล่มใบเสร็จ เนื่องจากเลขที่ครบ 100 แล้ว</h4>', function () {
            });
            clicked = false;
            return false;
          }
        }
        if (ReciveMony >= res) {
          var url = $('#cashier-receive').attr('action');
          $.post(url, $('#cashier-receive').serialize()).done(function (result) {
<?= SDNoty::show('result.message', 'result.status') ?>;
            console.log(result);
            if (result.book_no) {
              myWindow = window.open('<?= $url_print ?>' + result.id + '&right_code=' + result.receipt_right_code, '_blank');
            }
            let urlReloadQue = $('#<?= $params['queueDiv'] ?>').attr('data-url');
            if (urlReloadQue) {
              getUiAjax(urlReloadQue, '<?= $params['queueDiv'] ?>');
            } else {
              window.location.reload();
            }

            $('#view-order-counter').html('<h1 class="text-center" style="font-size: 45px; color: #ccc;margin: 200px 0;"><?= Yii::t('patient', 'Please choose patient') ?>');
            clicked = false;
          }).fail(function () {
<?= SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') ?>
            console.log('server error');
            clicked = false;
          });
          return false;
        } else {
          yii.confirm('<h4>คุณใส่จำนวนเงินน้อยกว่ายอด</h4>', function () {

          });
          clicked = false;
          return false;
        }
      }
    }

    $('#cashier-receive tbody tr').on('click', function () {
      if ($(this).next().hasClass("out")) {
        $(this).next().addClass("in");
        $(this).next().removeClass("out");
      } else {
        $(this).next().addClass("out");
        $(this).next().removeClass("in");
      }
    });

    function sumPrice(fin_code) {
      var pay = 0;
      var price = 0;
      $('#table_' + fin_code + ' tbody tr').each(function () {
        price = $(this).find('input[name="order_check[pay][]"]');
        if (!price.attr('disabled')) {
          pay += parseFloat(price.val() !== '' ? price.val().replace(',', '') : 0);
        }
      });
      $('#sum_pay_' + fin_code).html(format2(pay));
      sumPriceNotpay(fin_code);
      sumTotalPay();
    }

    function sumPriceNotpay(fin_code) {
      var pay = 0;
      var price = 0;
      $('#table_' + fin_code + ' tbody tr').each(function () {
        price = $(this).find('input[name="order_check[notpay][]"]');
        if (!price.attr('disabled')) {
          pay += parseFloat(price.val() !== '' ? price.val().replace(',', '') : 0);
        }
      });
      $('#sum_notpay_' + fin_code).html(format2(pay));
      sumTotalNotPay();
    }

    function sumTotalPay() {
      var total_pay = 0;
      $('.sum-price').each(function () {
        total_pay += parseFloat($(this).text() !== '' ? $(this).text().replace(',', '') : 0);
      });
      if ($('#cashier-receive').attr('data-pay') !== 'true') {
        $('#total_pay2').val(format2(total_pay));
      }
      $('.total-pay').text(format2(total_pay));
    }

    function sumTotalNotPay() {
      var total_notpay = 0;
      $('.sum-price-notpay').each(function () {
        total_notpay += parseFloat($(this).text() !== '' ? $(this).text().replace(',', '') : 0);
      });
      $('.total-notpay').text(format2(total_notpay));

      if ($('#cashier-receive').attr('data-pay') === 'true') {
        let total_pay = 0;
        $('.sum-price').each(function () {
          total_pay += parseFloat($(this).text() !== '' ? $(this).text().replace(',', '') : 0);
        });

        $('#total_pay2').val(format2(total_pay + total_notpay));
      }
    }

    function format2(n) {
      return n.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,");
    }

    $('.button-checkbox').each(function () {
      // Settings
      var $widget = $(this),
              $button = $widget.find('button'),
              $checkbox = $widget.find('input:checkbox'),
              color = $button.data('color'),
              settings = {
                on: {
                  icon: 'glyphicon glyphicon-check'
                },
                off: {
                  icon: 'glyphicon glyphicon-unchecked'
                }
              };
      // Event Handlers
      $button.on('click', function () {
        $checkbox.prop('checked', !$checkbox.is(':checked'));
        $checkbox.triggerHandler('change');
        updateDisplay();
      });
      $checkbox.on('change', function () {
        updateDisplay();
      });
      // Actions
      function updateDisplay() {
        var isChecked = $checkbox.is(':checked');
        // Set the button's state
        $button.data('state', (isChecked) ? "on" : "off");
        // Set the button's icon
        $button.find('.state-icon')
                .removeClass()
                .addClass('state-icon ' + settings[$button.data('state')].icon);
        // Update the button's color
        if (isChecked) {
          $button
                  .removeClass('btn-default')
                  .addClass('btn-' + color + ' active');
        } else {
          $button
                  .removeClass('btn-' + color + ' active')
                  .addClass('btn-default');
        }
      }

      // Initialization
      function init() {

        updateDisplay();
        // Inject the icon if applicable
        if ($button.find('.state-icon').length == 0) {
          $button.prepend('<i class="state-icon ' + settings[$button.data('state')].icon + '"></i> ');
        }
      }
      init();
    });
</script>
<?php \richardfan\widget\JSRegister::end(); ?>