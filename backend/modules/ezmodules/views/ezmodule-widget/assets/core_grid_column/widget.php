<?php
// start widget builder
use yii\helpers\Url;
use yii\helpers\Html;

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
   
$pagesize = isset($options['pagesize'])?$options['pagesize']:50;
$key_id = isset($options['key_id'])?$options['key_id']:'';
$fields = isset($options['fields'])?$options['fields']:[];
$sql_id = isset($options['sql'])?$options['sql']:0;
$header = isset($options['header'])?$options['header']:[];
$title_parnel = isset($options['title'])?$options['title']:'';
$actions = isset($options['actions'])?$options['actions']:[];
//$header = \yii\helpers\ArrayHelper::map($header, 'varname', 'label');

$header_now = [];
foreach ($header as $key => $value) {
    $index = isset($value['varname']) && $value['varname']!=''?$value['varname']:$key;
    $header_now[$index] = $value;
}

$params = [];
if(isset($_GET)){
    $params = $_GET;
}

$reloadDiv = 'grid-custom-'.$widget_config['widget_varname'];
$modal = 'modal-ezform-main';

$data_column = backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String($fields);
$header_now = backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String($header_now);
$actions = backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String($actions);
$params = backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String($params);
?>

    <?php
    $url = Url::to(['/ezforms2/ezform-data/view-sql', 'sql_id' => $sql_id, 'key_id' => $key_id, 'pagesize' => $pagesize, 'modal' => $modal, 'reloadDiv' => $reloadDiv, 'data_column' => $data_column, 'header' => $header_now, 'actions' => $actions, 'params' => $params, 'title'=>$title_parnel]);
    $html = Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-url' => $url]);

    $this->registerJs("
        getUiAjax('$url', '$reloadDiv');
    ");
    
    echo $html;
    
    $initdata = backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String(['payment_status'=>0]);
    //appxq\sdii\utils\VarDumper::dump($initdata);
    
    ?>
