<?php

use yii\helpers\Html;
use backend\modules\ezforms2\classes\MyWorkbenchFunc;
$ezf_id = $options['ezf_id'];
$ezf_match_id = $options['ezf_match_id'];
$ezf_name_id = $options['ezf_name_id'];
$field_value = $options['field_value'];
$field_label = $options['field_label'];

$field_display = $options['field_display'];
$field_download = $options['field_download'];
$field_status = $options['field_status'];

echo backend\modules\ezforms2\classes\MyWorkbenchWidget::ui()->action('view')->buildUiWorkbench();
echo Html::tag('div','',['class'=>'table-responsive','id'=>'grid-workbench']);
echo \backend\modules\ezforms2\classes\MyWorkbenchFunc::workbenchUI($ezf_id)
        ->ezf_match_id($ezf_match_id)
        ->ezf_name_id($ezf_name_id)
        ->target($target)
        ->reloadDiv('grid-workbench')
        ->columnDownload($field_download)
        ->columnStatus($field_status)
        ->data_column($field_display)
        ->default_column(false)
        ->buildGrid();
//echo \backend\modules\ezforms2\classes\EzfHelper::ui($ezf_id)->target($target)->reloadDiv('grid-workbench')->default_column(false)->buildGrid();