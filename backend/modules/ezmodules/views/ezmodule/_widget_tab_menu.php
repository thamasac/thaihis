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

<div id="ezmodule-tab-widget-menu" class="widget-tab-menu" data-url="<?= Url::to(['/ezmodules/ezmodule-tab/get-tab', 
    'moduleID'=>$moduleID,
    'controllerID'=>$controllerID,
    'actionID'=>$actionID,
    'menu'=>$menu, 
    'module'=>$module, 
    'addon'=>$addon,
    'tab'=>$tab,
    'filter'=>$filter,
    'target'=>$target,
    ])?>" style="margin-bottom: 15px;">
    
    <?php
    
    echo $this->render('/ezmodule/_widget_tab_items', [
                'moduleID'=>$moduleID,
                'controllerID'=>$controllerID,
                'actionID'=>$actionID,
                'model' => $modelOrigin,
                'menu'=>$menu,
                'module'=>$module,
                'addon'=>$addon,
                'tab'=>$tab,
                'filter'=>$filter,
                'target'=>$target,
            ]);
    ?>
   
</div>
    
    <?php $this->registerJs("

    $('#ezmodule-tab-widget-menu').on('click', '.add-tab-list', function() {
        modalEzmoduleTab($(this).attr('href'));
        return false;
    });
    
    $('#ezmodule-tab-widget-menu').on('click', '.edit-tab', function() {
        modalEzmoduleTab($(this).attr('href'));
        return false;
    });
    
    function getTabContent(url) {
        $.ajax({
            method: 'GET',
            url: url,
            dataType: 'HTML',
            success: function(result, textStatus) {
                $('#ezmodule-tab-widget-menu').html(result);
            }
        });
    }
    
    function modalEzmoduleTab(url) { console.log(url);
        $('.sp-container').remove();
        
        $('#modal-ezmodule .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
        $('#modal-ezmodule').modal('show')
        .find('.modal-content')
        .load(url);
    }

"); ?>
