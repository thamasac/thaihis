<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::t('chanapn', 'Topic');
 $panel_type = isset($options['panel_type']) ? $options['panel_type'] : 'primary';
 $panel = isset($options['panel']) && $options['panel'] == 1 ? 'panel panel-'.$panel_type : '';
 $panel_heading = isset($options['panel']) && $options['panel'] == 1 ? 'panel-heading' : '';
 $panel_title = isset($options['panel']) && $options['panel'] == 1 ? 'panel-title' : '';
 $panel_body = isset($options['panel']) && $options['panel'] == 1 ? 'panel-body' : '';
?>
<div class="<?= $panel?>">    
    <div class="<?= $panel_heading?>">
        <div class="<?= $panel_title?>">
            <div class="row">
                <div class="col-md-12">
                    <span class="pull-left"><i class="fa <?= isset($options['icon']) ? $options['icon'] : ''?>"></i> <?= isset($data['name']) ? $data['name'] : '' ?></span>
                    <span class="pull-right">
                        <?php if (backend\modules\ezforms2\classes\EzfAuthFunc::canReadWrite($options['module_id']) || backend\modules\ezforms2\classes\EzfAuthFunc::canManage($options['module_id'])) { ?>
                            <?=
                            \yii\helpers\Html::button('<i class="glyphicon glyphicon-pencil"></i>', [
                                'data-id' => $value['id'],
                                'class' => 'btn btn-info btn-sm btnEdit',
                                'data-url' => yii\helpers\Url::to(['/topic/topic-multi/manage', 'options' => $options])
                            ])
                            ?>

                        <?php } ?>
                    </span>
                </div>          
            </div>
        </div>
    </div>
    <div class="<?= $panel_body?>">        
        <div><?= $data['detail'] ?></div>
    </div>
</div>



<?php
$this->registerJs("
    $('.btnRefresh').click(function(){
        setTimeout(function(){
            getData();
        },1000);
     });
     $('.btnEdit').click(function(){
         modalTopic($(this).attr('data-url'));
     });   

");
?>