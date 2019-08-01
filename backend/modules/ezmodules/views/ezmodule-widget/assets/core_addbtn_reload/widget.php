<?php
// start widget builder

/* Params widget สามารถใช้งาน ตัวแปรต่อไปนี้ได้
'options' => $options,
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

$params_all = [];
if(isset($_GET)){
    $params_all = $_GET;
    
    unset($params_all['target']);
    unset($params_all['dataid']);
}
$params_all = backend\modules\ezforms2\classes\EzfFunc::array2PathTemplate($params_all);

if(isset($options['target']) && !empty($options['target']) && $options['target']!='target'){
    $target = isset($_GET[$options['target']])?$_GET[$options['target']]:'';
}
$label = isset($options['label'])?$options['label']:'';
$query_params = isset($options['query_params'])? strtr($options['query_params'], $params_all):'target={target}&dataid={dataid}';
$ezf_id = isset($options['ezf_id'])?$options['ezf_id']:'';
$size = isset($options['size'])?$options['size']:'';
$theme = isset($options['theme'])?$options['theme']:'';
$tab = isset($_GET['tab'])?$_GET['tab']:'';
$initdate = (isset($options['initdate']) && !empty($options['initdate']) && !empty($target))?$options['initdate']:'';
$show = isset($options['show'])?$options['show']:0;

$dataid = '';
if(isset($options['dataid']) && !empty($options['dataid'])){
    $dataid = isset($_GET[$options['dataid']])?$_GET[$options['dataid']]:'';
} 

$current_url = \yii\helpers\Url::to(['/ezmodules/ezmodule/view','id'=>$module,'addon'=>$addon,'tab'=>$tab])."&$query_params";

$class = "btn $size $theme";

if($initdate!=''){
    $modelEzf = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($ezf_id);
    if($modelEzf){
        $options_ezf = \appxq\sdii\utils\SDUtility::string2Array($modelEzf->ezf_options);
        $create_date_field = isset($options_ezf['create_date_field']) && !empty($options_ezf['create_date_field'])?$options_ezf['create_date_field']:'create_date';
        $unit_field = isset($options_ezf['unit_field']) && !empty($options_ezf['unit_field'])?$options_ezf['unit_field']:'';
        $enable_field = isset($options_ezf['enable_field']) && !empty($options_ezf['enable_field'])?$options_ezf['enable_field']:'';
        
        $modelLastRecord = backend\modules\ezforms2\classes\EzfUiFunc::loadLastDateRecordNotModel($modelEzf->ezf_table, $target, $create_date_field, $unit_field, $enable_field);
        if($modelLastRecord){
            $dataid = isset($modelLastRecord['id'])?$modelLastRecord['id']:'';
        }
    }
    
}

if($show){
    if(!empty($target) && empty($dataid)){
        echo \backend\modules\ezforms2\classes\BtnBuilder::btn()
        ->ezf_id($ezf_id)
        ->target($target)
        ->label($label)
        ->options(['class'=>$class])
        ->reloadPage($current_url)
        ->buildBtnAdd()
        ;
    } else {
        if($initdate!='' && !isset($_GET[$options['dataid']])){
            $url = strtr($current_url, ['{target}'=>$target, '{dataid}'=>$dataid]);
            $link_ui = yii\helpers\Html::a('Open', \yii\helpers\Url::to([$url]));
            echo '<span class="alert alert-warning" role="alert"> <strong>'.$options['dataid'].'</strong> Can be created once a day '.$link_ui.' </span>';
        }
    }
} else {
    if(empty($dataid)){
        echo \backend\modules\ezforms2\classes\BtnBuilder::btn()
            ->ezf_id($ezf_id)
            ->label($label)
            ->target($target)
            ->options(['class'=>$class])
            ->reloadPage($current_url)
            ->buildBtnAdd()
            ;
    } else {
        if($initdate!='' && !isset($_GET[$options['dataid']])){
            $url = strtr($current_url, ['{target}'=>$target, '{dataid}'=>$dataid]);
            $link_ui = yii\helpers\Html::a('Open', \yii\helpers\Url::to([$url]));
            echo '<span class="alert alert-warning" role="alert"> <strong>'.$options['dataid'].'</strong> Can be created once a day '.$link_ui.' </span>';
        }
    }
}



?>
