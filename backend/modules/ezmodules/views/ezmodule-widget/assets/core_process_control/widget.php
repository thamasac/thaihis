<?php
// start widget builder
use backend\modules\ezmodules\classes\ModuleQuery;
use backend\modules\ezforms2\classes\EzfHelper;
use backend\modules\ezmodules\classes\ModuleFunc;
use yii\helpers\Url;

/* Params widget สามารถใช้งาน ตัวแปรต่อไปนี้ได้
  'options' => $options,
  'widget_config' => $widget_config,
  'model' => $model,
  'modelOrigin'=>$modelOrigin,
  'menu' => $menu,
  'module' => $module,
  'addon' => $addon,
  'filter' => $filter,
  'reloadDiv' => $reloadDiv,
  'dataFilter' => $dataFilter,
  'modelFilter' => $modelFilter,
  'target' => $target,
 */
$divname = 'process-'.$module;
?>

<div id="<?=$divname?>" class="widget-process" data-url="<?= Url::to(['/ezmodules/ezmodule-widget/process-control', 
    'module'=>$module, 
    ])?>" >
</div>

<?php $this->registerJs("

    getWorkingUnitContent($('#$divname').attr('data-url'));
    setTimeout(function(){ getWorkingUnitContent($('#$divname').attr('data-url')); }, 5000);
        
    function getWorkingUnitContent(url) {
        $.ajax({
            method: 'GET',
            url: url,
            dataType: 'HTML',
            success: function(result, textStatus) {
                $('#$divname').html(result);
            }
        });
    }

"); ?>