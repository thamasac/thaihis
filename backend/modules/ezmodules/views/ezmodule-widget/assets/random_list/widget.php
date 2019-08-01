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
$reloadDiv = isset($options['reloadDiv']) && $options['reloadDiv'] != '' ? $options['reloadDiv'] :'random-'.appxq\sdii\utils\SDUtility::getMillisecTime();
$color = isset($options['color']) && $options['color'] != '' ? $options['color'] :'panel-primary';
//   $options['reloadDiv'] = 'random-'.appxq\sdii\utils\SDUtility::getMillisecTime();
   //\appxq\sdii\utils\VarDumper::dump($data);
//    $config = [
//        ['id' => 'index','icon' => 'fa fa-table', 'title' => Yii::t('chanpan', 'RCode editor'), 'url' => yii\helpers\Url::to(['/random/randomization/index', 'options'=>$options]),'active' => true],
//        ['id'=>'setting','icon' => 'fa fa-cog', 'title' => Yii::t('chanpan', 'RCode generation'), 'url' => yii\helpers\Url::to(['/random/randomization/setting', 'options'=>$options])],
//    ];
echo \backend\modules\random\classes\RandomWidget::ui()->reloadDiv($reloadDiv)->color($color)->buildUi();
//    echo backend\modules\random\classes\CNTab::getTab($config);
//    echo \cpn\chanpan\widgets\CNTabWidget::widget([
//        'options'=>$config
//    ]);
//
?>



