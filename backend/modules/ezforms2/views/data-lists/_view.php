<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$url = ['/ezforms2/data-lists/index'];
$queryParams = Yii::$app->request->getQueryParams();
?>


<a href="<?= Url::to(ArrayHelper::merge($url, $queryParams, ['ezf_id' => $model->ezf_id, 'target' => ''])) ?>" class="media <?= $model->ezf_id == $ezf_id ? 'active' : ''; ?>" style="position: relative;">
    <div class="media-left"> 
      <?= backend\modules\ezforms2\classes\EzfUiFunc::getEzformIcon($model, 42)?>
    </div>
    <div class="media-body"> 
        <h4 class="list-group-item-heading"><span><i class="fa fa-bars draggable"></i></span>  <?= $model->ezf_name ?></h4> 
        <p class="list-group-item-text">
        <div class=""><strong><?= Yii::t('ezform', 'Date') ?> : </strong><?= \appxq\sdii\utils\SDdate::mysql2phpDateTime($model->updated_at) ?></div>
        <div class=""><?= Html::encode($model->ezf_detail) ?> </div>
        </p>
<!--        <p class="list-group-item-text">
            <?php
//            $all = Yii::$app->db->createCommand("SELECT count(*) FROM {$model->ezf_table} WHERE rstat in(1,2)")->queryScalar();
//            $draft = Yii::$app->db->createCommand("SELECT count(*) FROM {$model->ezf_table} WHERE rstat = 1")->queryScalar();
//            $submitted = Yii::$app->db->createCommand("SELECT count(*) FROM {$model->ezf_table} WHERE rstat = 2")->queryScalar();
//            
            ?>
        <button class="btn btn-success btn-xs"><?php //echo Yii::t('ezform', 'Save Draft') ?> <span class="badge"><?php //echo number_format($draft)?></span></button> 
            <button class="btn btn-primary btn-xs"><?php //echo Yii::t('ezform', 'Submitted') ?> <span class="badge"><?php //echo number_format($submitted)?></span></button> 
            <button class="btn btn-default btn-xs"><?php //echo Yii::t('ezform', 'All') ?> <span class="badge"><?php //echo number_format($all)?></span></button> 
        </p>-->
    </div>
</a>

<div class="btn-group" style="position: absolute; right: 10px; top: 10px;">
    <button type="button" class="close dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fa fa-cog"></i> </button>

    <ul class="dropdown-menu dropdown-menu-right">
      <li class="disabled"><a href=""><i class="fa fa-area-chart"></i> <?= Yii::t('ezform', 'EDAT') ?></a></li>
        <li><a class="ezform-main-open" data-modal="modal-ezform-info" data-url="/ezforms2/ezform-data/ezform-annotated?ezf_id=<?=$model->ezf_id?>&amp;modal=modal-ezform-info&amp;reloadDiv="><i class="fa fa-code"></i> <?= Yii::t('ezform', 'Annotated CRF') ?></a></li>
        <!--        <li role="separator" class="divider"></li>-->
        <li><a class="ezform-main-open" data-modal="modal-ezform-info" data-url="/ezforms2/ezform-data/ezform-dictionary?ezf_id=<?=$model->ezf_id?>&amp;modal=modal-ezform-info&amp;reloadDiv="><i class="fa fa-book" ></i> <?= Yii::t('ezform', 'Dictionary') ?></a></li>
    </ul>
</div>