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

<div id="ezmodule-module-menu" class="widget-module-menu" data-url="<?= Url::to(['/ezmodules/ezmodule-addon/get-module', 
    'moduleID'=>$moduleID,
    'controllerID'=>$controllerID,
    'actionID'=>$actionID,
    'menu'=>$menu, 
    'module'=>$module, 
    'addon'=>$addon,
    'filter'=>$filter,
    ])?>" style="margin-bottom: 15px;">
    
    <?php
    echo $this->render('/ezmodule/_widget_module_items', [
                'moduleID'=>$moduleID,
                'controllerID'=>$controllerID,
                'actionID'=>$actionID,
                'model' => $modelOrigin,
                'menu'=>$menu,
                'module'=>$module,
                'addon'=>$addon,
                'filter'=>$filter,
            ]);
    ?>
   
</div>

    <?=
    ModalForm::widget([
        'id' => 'modal-ezmodule-module-menu',
        //'size' => 'modal-xxl',
        'tabindexEnable' => FALSE,
    ]);
    ?>
    
    <?php $this->registerJs("

    $('#ezmodule-module-menu').on('click', '#add-module-list', function() {
        modalEzmoduleModule($(this).attr('href'));
        return false;
    });
    
    function getModuleMenuContent(url) {
        $.ajax({
            method: 'GET',
            url: url,
            dataType: 'HTML',
            success: function(result, textStatus) {
                $('#ezmodule-module-menu').html(result);
            }
        });
    }
    
    function modalEzmoduleModule(url) {
        $('#modal-ezmodule-module-menu .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
        $('#modal-ezmodule-module-menu').modal('show')
        .find('.modal-content')
        .load(url);
    }

"); ?>
