<?php

use backend\modules\ezmodules\classes\ModuleFunc;
use yii\helpers\Url;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 
 */
if (isset($model) && !empty($model)) {
    foreach ($model as $key => $data) {
        ?>
        <?php
        $linkModule = '';
        if ($data['ezm_type'] == 1) {
            $linkModule = \yii\helpers\Url::to($data['ezm_link']);
        } 
//        elseif ($data['ezm_type'] == 2) {
//            //$linkModule = \yii\helpers\Url::to(['/inv/inv-map/index', 'module' => $value['gid']]);
//        }
        else {
            $linkModule = \yii\helpers\Url::to(['/ezmodules/ezmodule/view', 'id' => $data['ezm_id']]);
        }

        $gname = yii\helpers\Html::encode($data['ezm_short_title']);
        $checkthai = ModuleFunc::checkthai($gname);
        $len = 12;
        if ($checkthai != '') {
            $len = $len * 3;
        }
        if (strlen($gname) > $len) {
            $gname = substr($gname, 0, $len) . '...';
        }
        
        $storagePath = isset($data['icon_base_url'])?$data['icon_base_url']:Yii::getAlias('@storageUrl/module');
        ?>

        <div class="col-xs-3 col-md-2" style="margin-bottom: 20px;">
            <div class="media-left">
                <?php if($mode==1):?>
                    <a style="cursor: pointer;" data-url="<?=Url::to(['/ezmodules/default/info-app', 'id'=>$data['ezm_id']])?>"  class="info-app">
                <?php else:?>
                    <a href="<?= $linkModule ?>">
                <?php endif;?>
                      <div style="margin: 4px;"><img src="<?= (isset($data['ezm_icon']) && $data['ezm_icon'] != '') ? $storagePath. '/' . $data['ezm_icon'] : ModuleFunc::getNoIconModule() ?>" class="img-rounded" width="72" height="72"></div>
                </a>
                <h4 class="media-heading text-center" style="font-size: 13px;">
                    <strong><?= $gname ?></strong> 
                    <?php if($mode==0):?>
                        <a style="cursor: pointer;" data-url="<?= Url::to(['/ezmodules/default/info-app', 'id' => $data['ezm_id']]) ?>" class="info-app">
                            <i class="glyphicon glyphicon-info-sign"></i>
                        </a>
                    <?php endif;?>
                    
                </h4>
            </div>
        </div>

        <?php
    }
} else {
    echo '<div class="col-xs-3 col-md-2"><p class="lead">' . Yii::t('app', 'No results found.') . '</p></div>';
}
?>




