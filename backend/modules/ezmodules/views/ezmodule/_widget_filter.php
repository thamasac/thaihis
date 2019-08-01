<?php
use yii\helpers\Url;
use appxq\sdii\widgets\ModalForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;

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

<div id="ezmodule-filter" class="widget-filter" data-url="<?= Url::to(['/ezmodules/ezmodule-filter/get-filter', 
    'moduleID'=>$moduleID,
    'controllerID'=>$controllerID,
    'actionID'=>$actionID,
    'menu'=>$menu, 
    'module'=>$module, 
    'addon'=>$addon,
    'filter'=>$filter,
    //'dataFilter'=> \backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String($dataFilter),
    ])?>" >
    
    <?php
    echo $this->render('/ezmodule/_widget_filter_items', [
                'moduleID'=>$moduleID,
                'controllerID'=>$controllerID,
                'actionID'=>$actionID,
                'model' => $model,
                'menu'=>$menu,
                'module'=>$module,
                'addon'=>$addon,
                'filter'=>$filter,
                'dataFilter'=>$dataFilter,
            ]);
    ?>
   
</div>

    <?=
    ModalForm::widget([
        'id' => 'modal-ezmodule-filter',
        'size' => 'modal-lg',
        'tabindexEnable' => FALSE,
    ]);
    ?>
    
    <?php $this->registerJs("

    $('#ezmodule-filter').on('click', '.action-filter-list', function() {
        modalEzmoduleFilter($(this).attr('data-url'));
        return false;
    });
    
    $('#ezmodule-filter').on('click', '#del-filter-list', function() {
        var url = $(this).attr('href');
        yii.confirm('" . Yii::t('app', 'Are you sure you want to delete this item?') . "', function() {
            $.ajax({
                method: 'GET',
                url: url,
                dataType: 'HTML',
                success: function(result, textStatus) {
                }
            });
	});
        return false;
    });
    
    function getFilterContent(url) {
        $.ajax({
            method: 'GET',
            url: url,
            dataType: 'HTML',
            success: function(result, textStatus) {
                $('#ezmodule-filter').html(result);
            }
        });
    }
    
    function modalEzmoduleFilter(url) {
        $('#modal-ezmodule-filter .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
        $('#modal-ezmodule-filter').modal('show')
        .find('.modal-content')
        .load(url);
    }

"); ?>
