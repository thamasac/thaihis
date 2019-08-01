<?php

use yii\helpers\Url;
use appxq\sdii\widgets\ModalForm;

$moduleID = '';
$controllerID = '';
$actionID = '';
if (isset(Yii::$app->controller->module->id)) {
    $moduleID = Yii::$app->controller->module->id;
}
if (isset(Yii::$app->controller->id)) {
    $controllerID = Yii::$app->controller->id;
}
if (isset(Yii::$app->controller->action->id)) {
    $actionID = Yii::$app->controller->action->id;
}
?>

<div id="ezmodule-menu" class="widget-menu" data-url="<?= Url::to(['/ezmodules/ezmodule-menu/get-menu', 
    'moduleID'=>$moduleID,
    'controllerID'=>$controllerID,
    'actionID'=>$actionID,
    'menu'=>$menu, 
    'module'=>$module, 
    ])?>" style="margin-bottom: 15px;">
    
    <?php
    echo $this->render('/ezmodule/_widget_menu_items', [
                'moduleID'=>$moduleID,
                'controllerID'=>$controllerID,
                'actionID'=>$actionID,
                'model' => $modelOrigin,
                'menu'=>$menu,
                'module'=>$module,
            ]);
    ?>
   
</div>

    <?=
    ModalForm::widget([
        'id' => 'modal-ezmodule-menu',
        'size' => 'modal-xxl',
        'tabindexEnable' => FALSE,
    ]);
    ?>
    
    <?php $this->registerJs("

    $('#ezmodule-menu').on('click', '.btn-menu', function() {
        modalEzmoduleMenu($(this).attr('href'));
        return false;
    });
    
    function getMenuContent(url) {
        $.ajax({
            method: 'GET',
            url: url,
            dataType: 'HTML',
            success: function(result, textStatus) {
                $('#ezmodule-menu').html(result);
            }
        });
    }
    
    function modalEzmoduleMenu(url) {
        $('#modal-ezmodule-menu .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
        $('#modal-ezmodule-menu').modal('show')
        .find('.modal-content')
        .load(url);
    }

"); ?>
