<?php
use backend\modules\ezforms2\classes\EzfFunc;
use yii\helpers\Url;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$initdata=[];
$data = EzfFunc::arrayEncode2String($initdata);
$modal = 'modal-ezform-main';
$ezf_id = $model['ezf_id'];
$target = '';
?>

<div id="widget-button_add" style="display: inline-block;">
    <button class="btn btn-success ezform-main-open" data-modal="<?=$modal?>" data-url="<?=Url::to(['/ezforms2/ezform-data/ezform',
                'ezf_id' => $ezf_id,
                'modal' => $modal,
                'reloadDiv' => $reloadDiv,
                'initdata' => $data,
                'target' => $target,
            ])?>">
            <i class="fa fa-plus "></i> 
            <?= Yii::t('app', 'Add New Record')?>
    </button>
    
</div>

