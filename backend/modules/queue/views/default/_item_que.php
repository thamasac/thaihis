<?php

use appxq\sdii\utils\SDdate;
use yii\helpers\Html;

$page = Yii::$app->request->get('page','');

$path_items = [];
$template_item = '<div id="box-{id}" class="row">
                    <div class="col-md-4 sdcol-label">
                      <strong>{label}</strong>
                    </div>
                    <div class="col-md-8 sdbox-col">
                      {value}
                    </div>
               </div>';

if (isset($template_custom) && !empty($template_custom)) {
    $template_item = '{value}';
}

$template_content = '<div class="row">';
$reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
if (!preg_match($reg_exUrl, $current_url)) {
    $http = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
    $current_url = $http . $_SERVER['SERVER_NAME'] . $current_url;
}
if ($action == '1' || $action == '3') {
    $current_url .= strrpos($current_url, '?') > 0 ? '&target=' . $model['targetMain'] : '?target=' . $model['targetMain'];
    if (strrpos($current_url, '&search_field') > 0) {
        $current_url = substr($current_url, 0, strrpos($current_url, '&search_field'));
    }
} else {
    $current_url = "/ezforms2/ezform-data/ezform-view?ezf_id=" . $ezf_main_id;
}
$active = '';
foreach ($param as $vParam) {
    if ($vParam['model_field'] == 'id') {
        $txtParam = '&' . $vParam['name'] . '=' . $model[$vParam['value'] . "_" . $vParam['model_field']];
    } else {
        $txtParam = '&' . $vParam['name'] . '=' . $model[$vParam['model_field']];
    }
    if (strrpos($current_url, $txtParam) <= 0) {
        $current_url .= $txtParam;
    }

    if (isset($vParam['param_active']) && $vParam['param_active'] == 1) {
        //ควรนำไปใช้กับ Que ทั่วไป
        if (isset($params_value[$vParam['name']]) && $params_value[$vParam['name']]) {
            if ($params_value[$vParam['name']] == $model[$vParam['model_field']]) {
                $active = ' active';
            }
        } else {
            if (isset($params_value['target']) && $params_value['target'] == $model['targetMain']) {
                $active = ' active';
            }
        }
    }
}
if (isset($data_columns) && is_array($data_columns)) {
    foreach ($data_columns as $k => $field) {
        foreach ($modelFields as $key => $value) {
            if ($field == $value['ezf_field_id']) {
                $labelCustom = '';
                $checkLable = false;
                if (isset($custom_label) && is_array($custom_label)) {
                    foreach ($custom_label as $vCustom) {
                        if ($vCustom['varname'] == $value['ezf_field_name']) {
                            $labelCustom = $vCustom['label'];
                            $checkLable = true;
                        }
                    }
                }
                if ($value['ezf_field_name'] == $bdate_field) {

                    $template_content .= "<div class=\"col-md-12\">{{$bdate_field}}</div>";
                    $path_data = [
                        '{id}' => 'quebox-' . $bdate_field,
                        '{label}' => $checkLable ? $labelCustom : 'อายุ ',
                        '{value}' => \backend\modules\queue\classes\QueueFunc::calAge($model[$bdate_field], true, false, false,isset($template_custom) && $template_custom != '' ? false : true),
                    ];
                    $path_items["{{$bdate_field}}"] = strtr($template_item, $path_data);
                } else if ($value['ezf_field_name'] == $pic_field) {
                    $image_field_name = $value['ezf_field_name'];
                } else {
                    $fieldName = $value['ezf_field_name'];
                    $template_content .= "<div class=\"col-md-12\">{{$value['ezf_field_name']}}</div>";

                    $var = $value['ezf_field_name'];
                    $label = $value['ezf_field_label'];

                    $dataInput;
                    if (isset(Yii::$app->session['ezf_input'])) {
                        $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($value['ezf_field_type'], Yii::$app->session['ezf_input']);
                    }
                    isset($model[$value['ezf_field_id'] . '_id']) ? $model['id'] = $model[$value['ezf_field_id'] . '_id'] : $model['id'] = \appxq\sdii\utils\SDUtility::getMillisecTime();
//                        \appxq\sdii\utils\VarDumper::dump($model);
                    $path_data = [
                        '{id}' => 'quebox-' . $fieldName,
                        '{label}' => $checkLable ? $labelCustom : $label,
                        '{value}' => backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $value, $model),
                    ];
                    $path_items["{{$fieldName}}"] = strtr($template_item, $path_data);
                }
            }
        }
    }
}


if (isset($image_field_name) && !empty($image_field_name)) {
    $image_val = Html::img(Yii::getAlias('@storageUrl/images/nouser.png'), ['class' => 'media-object img-rounded', 'style' => 'width: 60px;']);
    if (isset($model[$image_field_name]) && !empty($model[$image_field_name])) {
        $image_val = Html::img(Yii::getAlias('@storageUrl/ezform/fileinput/') . $model[$image_field_name], ['class' => 'media-object img-rounded', 'style' => 'width: 60px;']);
    }

    $path_items["{{$image_field_name}}"] = $image_val;

    $template_content = "<div class=\"media\"> 
                                <div class=\"media-left\"> 
                                      $image_val
                                </div> 
                                <div class=\"media-body\"> 
                                      $template_content
                                </div> 
                        </div>";
}
$template_content .= '</div>';

if (isset($template_custom) && !empty($template_custom)) {
    $template_content = $template_custom;
}
if ($action == '1' || $action == '3') {
    $current_url .= '&que_type=' . $que_type . $vParamSeach . '&queueDiv=' . $reloadDiv;
}
if($active == ''){
    if($model['targetMain'] == $target){
        $active = ' active ';
    }
}

$content = strtr($template_content, $path_items);
?>

<a href="<?= $current_url.'&page='.$page ?>" data-key="<?= $model['targetMain'] ?>" class="list-group-item item <?= $active ?>" style="padding: 5px 5px 5px;<?= isset($model['doc']) ? 'background-color: #ffec87;' : '' ?>">
  <?= $content ?>
</a>

