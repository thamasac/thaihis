<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$reloadDiv = 'reloadDiv-tab-widget';
$default_column = isset($default_column)?$default_column:1;
$pagesize = isset($pagesize)?$pagesize:50;
$order = isset($order)?$order:[];
$order_by = isset($order_by)?$order_by:4;
$db2 = isset($db2)?$db2:0;
$header = isset($header)?$header:[];
$title = isset($title)?$title:'';

$header = \yii\helpers\ArrayHelper::map($header, 'varname', 'label');

?>

    <?php
    $uiView = \backend\modules\ezforms2\classes\EzfHelper::ui($ezf_id)
                ->data_column(isset($fields)?$fields:[])
                ->reloadDiv($reloadDiv)
                ->default_column($default_column)
                ->pageSize($pagesize)
                ->order_column($order)
                ->header($header)
                ->title($title)
                ->orderby($order_by);
    
    if($db2==1){
        echo $uiView->buildDb2Grid();
    } else {
        echo $uiView->buildGrid();
    }
 ?>