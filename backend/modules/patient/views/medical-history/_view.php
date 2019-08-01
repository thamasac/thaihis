<?php
use backend\modules\patient\classes\PatientHelper;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

?>
<div class="panel panel-primary"> 
  <div class="panel-body">
    <div class="row">
      <div class="col-md-4">
          <?= PatientHelper::listVisit($target, $options, 'cpoe', 'list-visit',$modal); ?>
      </div>
      <div class="col-md-8 sdbox-col">
        <div id="view-detail">

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
    function getUiAjax(url, reloadDiv){
        var div = $('#'+reloadDiv);
        div.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        $.get(url, function(result){
            div.html(result);
        });
    }

</script>
<?php \richardfan\widget\JSRegister::end(); ?>