<?php
// start widget builder

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
use yii\helpers\Url;

$tab = isset($_GET['tab']) ? $_GET['tab'] : 1;
echo \yii\bootstrap\Nav::widget([
    'items' => [
            [
            'label' => Yii::t('ezmodule', 'Data List'),
            'url' => Url::to([Url::current(['tab' => 1])]),
            'active' => $tab == 1,
        ],
            [
            'label' => Yii::t('ezmodule', 'Activity Timeline'),
            'url' => Url::to([Url::current(['tab' => 2])]),
            'active' => $tab == 2,
        ],
            [
            'label' => Yii::t('ezmodule', 'Personal Resource'),
            'url' => Url::to([Url::current(['tab' => 3])]),
            'active' => $tab == 3,
        ],
    ],
    'options' => ['class' => 'nav nav-tabs'],
]);
?>