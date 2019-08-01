<?php

use backend\modules\patient\classes\PatientHelper;
?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="itemModalLabel"><?= Yii::t('patient', 'Move bed') ?></h4>
</div>
<div class="modal-body">
    <div id="admit-pt" class="row" style="margin-top: 15px;">
        <div class="col-md-12">
            <div class="card card-cpoe">
              <div class="card-block">
                  <?= PatientHelper::uiBedTran($admit_id, $visit_id, 'view-bed-tran-show'); ?>
              </div>
            </div>       
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
    $('#tab_order .ezform-main-open').on('click', function () {
      var url = $(this).attr('data-url');
      var modal = $(this).attr('data-modal');
      modalEzformMain(url, modal);
    });

    function modalEzformMain(url, modal) {
      $('#' + modal + ' .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
      $('#' + modal).modal('show')
              .find('.modal-content')
              .load(url);
    }

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

    $('#modal-ward-xxl').on('hidden.bs.modal', function (e) {
      $('#modal-ward-xxl .modal-content').html('');
      var url = $('#view-order-admit').attr('data-url');
      getUiAjax(url, 'view-order-admit');
    });

    $('.btn-order-admit').on('click', function (e) {
      var url = $('#view-order-admit').attr('data-url');
      getUiAjax(url, 'view-order-admit');
    });

</script>
<?php \richardfan\widget\JSRegister::end(); ?>
