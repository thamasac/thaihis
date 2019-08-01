<?php
// start widget builder
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

$params = [];
if(isset($_GET)){
    $params = $_GET;
}
$reloadDiv = 'ajax-load-'.\appxq\sdii\utils\SDUtility::getMillisecTime();
$options_url = isset($options['options_url'])?$options['options_url']:'';
if($options_url!=''){
    $optionsArry = \appxq\sdii\utils\SDUtility::string2Array($options_url);
    if($optionsArry){
        $params['options'] = backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String($optionsArry);
    }
}

$fields = isset($options['fields'])?$options['fields']:'';
if($fields!=''){
    $fieldsArry = \appxq\sdii\utils\SDUtility::string2Array($fields);
    if($fieldsArry){
        $params['fields'] = backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String($fieldsArry);
    }
}

$params['module'] = $module;
$params['reloadDiv'] = $reloadDiv;

$path = [];
foreach ($params as $key => $value) {
    $path["{{$key}}"] = $value;
}

$url = isset($options['url'])?$options['url']:'';
$url = strtr($url, $path);

$html = Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-url' => $url]);

echo $html;
?>

<?php \richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]); ?>
<script>
    // JS script
    getContentAjax('<?=$url?>', '<?=$reloadDiv?>');
    
    function getContentAjax(url, divid) {
        $.ajax({
            method: 'POST',
            url: url,
            dataType: 'HTML',
            success: function(result, textStatus) {
                $('#'+divid).html(result);
            }
        });
    }
</script>
<?php \richardfan\widget\JSRegister::end(); ?>