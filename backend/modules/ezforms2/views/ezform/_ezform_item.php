<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$gname = yii\helpers\Html::encode($model->ezf_name);
$checkthai = backend\modules\ezmodules\classes\ModuleFunc::checkthai($gname);
$len = 12;
if ($checkthai != '') {
    $len = $len * 3;
}
if (strlen($gname) > $len) {
    $gname = substr($gname, 0, $len) . '...';
}
?>
<div class="col-md-3 col-sm-4 col-lg-2 col-xs-4 text-center" style="margin-bottom: 20px;">
  <a title="<?= !empty($model->ezf_detail)?$model->ezf_detail:$model->ezf_name ?>" href="<?= yii\helpers\Url::to(['/ezforms2/ezform/clone-ajax', 'ezf_id' => $model->ezf_id]) ?>" class="btn-tmp" >
      <?= backend\modules\ezforms2\classes\EzfUiFunc::getEzformIcon($model, 72, ['style' => 'min-width: 72px; min-height: 72px;margin: 4px;']); ?>
  </a>
  <h4 class="media-heading text-center" style="font-size: 13px;">
    <strong><?= $gname ?></strong> 
  </h4>
</div>
