<?php

use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use backend\modules\ezmodules\classes\ModuleFunc;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div class="col-md-12">
    <?php
    if ($dataModule):
        foreach ($dataModule as $value):
            ?>
            <div class="flex-container-original">
                <?php
                $image = "";
                if (isset($value['ezm_icon']) && !empty($value['ezm_icon'])) {
                    $image = Html::img(Yii::getAlias('@storageUrl/module') . '/' . $value['ezm_icon'], [ 'class' => 'img-rounded','class'=>'image']);
                } else {
                    $image = Html::img(ModuleFunc::getNoIconModule(), [ 'class' => 'img-rounded','class'=>'image']);
                }
                ?>
                <a href="/ezmodules/ezmodule/view?id=<?=$value['ezm_id']?>" target="_blank" class="flex-items  cursor  dads-children bgcolor" title="<?=$value['ezm_name']?>" data-id="<?=$value['ezm_id']?>">
                    <div class="flex-items-image">
                        <?=$image?>
                    </div>
                    <div class="flex-items-content">
                        <?=$value['ezm_short_title']?>
                    </div>
                </a>
            </div>
            <?php
        endforeach;
    endif;
    ?>
    <div class="clearfix"></div>
</div>