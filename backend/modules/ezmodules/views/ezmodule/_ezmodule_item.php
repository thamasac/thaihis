<?php
use backend\modules\ezmodules\classes\ModuleFunc;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$gname = yii\helpers\Html::encode($model->ezm_name);
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
  <a title="<?= $model->ezm_name ?>" href="<?= yii\helpers\Url::to(['/ezmodules/ezmodule/clone-ajax', 'ezm_id' => $model->ezm_id]) ?>" class="btn-tmp" >
    <img style="min-width: 72px; min-height: 72px;margin: 4px;" src="<?= (isset($model['ezm_icon']) && $model['ezm_icon'] != '') ? $model['icon_base_url']. '/' . $model['ezm_icon'] : ModuleFunc::getNoIconModule() ?>" class="img-rounded" width="72" height="72">
  </a>
  <h4 class="media-heading text-center" style="font-size: 13px;">
    <strong><?= $gname ?></strong> 
  </h4>
</div>
