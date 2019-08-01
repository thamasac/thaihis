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

?>
<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$addmap = isset($_GET['addmap'])?$_GET['addmap']:0;
$sdate = isset($_GET['sdate'])?$_GET['sdate']:'2017-01-01';
$edate = isset($_GET['edate'])?$_GET['edate']:date('Y-m-d');

$lat_init = isset($options['lat_init'])?$options['lat_init']:'16.0148725';
$lng_init = isset($options['lng_init'])?$options['lng_init']:'101.8819517';
$zoom_init = isset($options['zoom_init'])?$options['zoom_init']:9;
$forms = isset($options['forms'])?backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String($options['forms']):'';

$reloadDiv = 'ezmap-'.$module;
$modal = 'modal-ezform-main';

$url = Url::to(['/ezforms2/ezform-data/ezmap', 
    'modal' => $modal,
    'reloadDiv' => $reloadDiv,
    'addmap' => $addmap,
    'target' => $target,
    'sdate' => $sdate,
    'edate' => $edate,
    'lat_init' => $lat_init,
    'lng_init' => $lng_init,
    'zoom_init' => $zoom_init,
    'forms' => $forms,
    ]);
$html = Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-url' => $url]);

$this->registerJs("
    getUiAjax('$url', '$reloadDiv');
");

$form = ActiveForm::begin([
                    'id' => 'jump_menu',
                    'action' => ['view', 'id'=>$module, 'addon'=>$addon],
                    'method' => 'get',
                    'layout' => 'inline',
                    'options' => ['style'=>'display: inline-block;', 'class'=>'col-md-12']	    
                ]); 

?>
<div class="row">
    <div class="col-md-9 ">
      <b><?= Yii::t('ezmodule', 'Display by time')?></b>
        <?= yii\jui\DatePicker::widget([
            'name' => 'sdate',
            'value' => $sdate,
            'language' => 'th',
            'dateFormat' => 'yyyy-MM-dd',
            'options'=>[
                'id' => 'wx1_datePicker',
                'class'=>'form-control'
            ]
        ])?>
         <b><?= Yii::t('ezmodule', 'To')?></b> 
        <?= yii\jui\DatePicker::widget([
            'name' => 'edate',
            'value' => $edate,
            'language' => 'th',
            'dateFormat' => 'yyyy-MM-dd',
            'options'=>[
                'id' => 'wx2_datePicker',
                'class'=>'form-control'
            ]
        ])?>
        <?= Html::hiddenInput('addmap', $addmap)?>
        <button class="btn btn-primary" type="submit"><i class="glyphicon glyphicon-search"></i> <?= Yii::t('ezmodule', 'Search')?></button> 
    </div>
    <div class="col-md-3 text-right">
        <?php if($addmap==1):?>
            <button name="addmap" value="0" class="btn btn-success" type="submit"><i class="glyphicon glyphicon-check"></i> <?= Yii::t('ezmodule', 'Edit Data')?></button>
        <?php else:?>
            <button name="addmap" value="1" class="btn btn-default" type="submit"><i class="glyphicon glyphicon-ban-circle"></i> <?= Yii::t('ezmodule', 'Edit Data')?></button>
        <?php endif;?>
    </div>
</div>
<?php ActiveForm::end(); ?>

<?php echo $html; ?>

