<?php

use yii\helpers\Html;

//$url = \yii\helpers\Url::to(['/cpoe/report-checkup/save-report-2-doc', 'visit_id' => $visit_id, 'pt_id' => $data[0]['ptid']]);
$url = \yii\helpers\Url::to(['/reports/report-checkup/report-to-doc-view', 'visit_id' => $visit_id, 'target' => $data[0]['ptid']]);
?>

<div class="col-md-6">
  <form action="<?= $url ?>" method="post" id="form-doc-lists">
      <?php
      echo Html::radioList('doc_lists', $data[0]['visit_tran_doctor'], ['1514136149083155900' => 'พญ.นฤมล  โพธิ์เปี้ยศรี', '1514136171016664500' => 'พญ.กรรณิการ์  สราญรมย์']);
      echo Html::input('hidden', 'doc_old', $data[0]['visit_tran_doctor']);
      echo Html::tag('div', Html::button('Send doctor <span class="fa fa-user-md"></span> ', ['id' => 'submit-doc-list', 'type' => 'submit', 'class' => 'btn btn-success', 'style' => 'margin-top:10px;']));
      ?>       
  </form>
</div>
<div class="col-md-6">
  <?php
  appxq\sdii\utils\VarDumper::dump($data, 0, 1);
  ?>
</div>

