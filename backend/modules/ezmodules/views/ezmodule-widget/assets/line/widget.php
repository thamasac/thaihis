
<?php
$reloadDiv = 'line-notify-'. \appxq\sdii\utils\SDUtility::getMillisecTime();
$widget = backend\modules\line\classes\LineWidget::ui()
        ->reloadDiv($reloadDiv)
        ->title(isset($options['title'])?$options['title']:'');
//if (backend\modules\ezforms2\classes\EzfAuthFuncManage::accessBtnGrid(isset($module) ? $module : '')) {
//    $widget->disabled(true);
//}
echo $widget->buildGrid();
?>

