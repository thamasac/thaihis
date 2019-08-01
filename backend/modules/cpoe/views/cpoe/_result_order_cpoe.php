<?php

use backend\modules\patient\classes\PatientHelper;
use yii\helpers\Url;

$url_patho = Url::to(['/patient/order/result-patho-show', 'pt_id' => $pt_id, 'pt_hn' => $pt_hn, 'visit_id' => $visit_id,
            'reloadDiv' => $reloadDiv, 'view' => 'cpoe', 'date' => $date]);

$url_xray = Url::to(['/patient/order/result-xray-show', 'pt_id' => $pt_id, 'pt_hn' => $pt_hn, 'visit_id' => $visit_id,
            'reloadDiv' => $reloadDiv, 'view' => 'cpoe', 'date' => $date]);

$url_lab = Url::to(['/patient/order/result-lab-show', 'pt_id' => $pt_id, 'pt_hn' => $pt_hn, 'visit_id' => $visit_id,
            'reloadDiv' => $reloadDiv, 'view' => 'cpoe', 'date' => $date]);

$url_cyto = Url::to(['/patient/order/result-cyto-show', 'pt_id' => $pt_id, 'pt_hn' => $pt_hn, 'visit_id' => $visit_id,
            'reloadDiv' => $reloadDiv, 'view' => 'cpoe', 'date' => $date]);
?>

<div class="panel panel-primary">
  <div class="panel-heading" style="padding-bottom: 0px;">
    <div class="pull-right" id="btn-right">
    </div>
    <ul class="nav nav-tabs" id="result-order-tab">
      <li id="tab-lab" role="presentation" class="nav-item"><a class="tab-primary tab-active" href="<?= $url_lab ?>"><i class="fa fa-flask"></i> LAB</a></li>
      <li id="tab-xray" role="presentation" class="nav-item"><a class="tab-primary" href="<?= $url_xray ?>"><i class="fa fa-male"></i> XR,CT,MRI </a></li>
      <!--<li id="tab-pat" role="presentation"><a href="<?= $url_patho ?>">PATHO </a></li>-->
      <li id="tab-ot" role="presentation" class="nav-item"><a class="tab-primary" href="<?= $url_cyto ?>"><i class="fa fa-cube"></i> Others </a></li>
    </ul>
  </div>
  <div class="panel-body" style="padding: 5px">
    <div id="result-order-content">
      <div class="col-md-12">
        <?php        
        echo PatientHelper::uiResultLabChart($pt_id, $pt_hn, $visit_id, $date, 'view-result-lab');
        ?>
      </div>           
    </div>
  </div>
</div>
<?php
echo appxq\sdii\widgets\ModalForm::widget([
    'id' => 'modal-result-order',
    'size' => 'modal-xxl',
]);

\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>
<script>
    $('#result-order-content').on('click', '.print-order-report', function () {
      var url = $(this).attr('href');
      myWindow = window.open(url, '_blank');
      return false;
    });

    $('#result-order-tab li a').on('click', function () {
      $('#result-order-tab li a').removeClass('tab-active');

      $(this).addClass('tab-active');
    });

    $('#result-order-tab li a').on('click', function () {
      var url = $(this).attr('href');
      if (url) {
        $.get(url).done(function (result) {
          //$('#<?= $reloadDiv ?>').html(result);
          $('#result-order-content').html(result);
        }).fail(function () {
          console.log('server error');
        });
      }
      return false;
    });

    function modalResulOrder(url) {
      $('#modal-result-order .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
      $('#modal-result-order').modal('show')
              .find('.modal-content')
              .load(url);
    }

</script>
<?php \richardfan\widget\JSRegister::end(); ?>