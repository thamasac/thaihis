<?php

use appxq\sdii\utils\SDdate;
use yii\helpers\Html;

//appxq\sdii\utils\VarDumper::dump($model);
//if ($que_type == '1' || empty($que_type)) {
$url = \yii\helpers\Url::to([$current_url . '&target=' . $model['target'] . '&visitid=' . $model['id'] . '&visit_tran_id=' . $model['visit_tran_id']
            . '&visit_type=' . $model['visit_type'] . '&action=que&que_type=' . $que_type]);
//} else {
//    $url = \yii\helpers\Url::to(['/ezmodules/ezmodule/view', 'id' => '1536036701059546400', 'target' => $model['pt_id'], 'action' => 'search','que_type' => $que_type]);
//}
$template_item = '<div id="box-{id}" class="row">
                    <div class="col-md-4 sdcol-label">
                      <strong>{label}</strong>
                    </div>
                    <div class="col-md-8 sdbox-col">
                      {value}
                    </div>
               </div>';

if (isset($template_custom) && !empty($template_custom)) {
    $template_item = '<span id="{id}">{value}</span>';
}

$template_content = '<div class="row" >';
$modelFields = appxq\sdii\utils\SDUtility::string2Array($modelFields);
$image_field_name = null;
$bdate_field_name = null;
if (isset($modelFields) && is_array($modelFields)) {
    foreach ($modelFields as $key => $value) {
        if ($value['ezf_field_id'] == $bdate_field) {
            $bdate_field_name = $value['ezf_field_name'];
        }
        if ($value['ezf_field_id'] == $image_field) {
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

            $path_data = [
                '{id}' => 'quebox-' . $fieldName,
                '{label}' => $label,
                '{value}' => backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $value, $model),
            ];

            $path_items["{{$fieldName}}"] = strtr($template_item, $path_data);
        }
    }
}

if (isset($image_field_name) && !empty($image_field_name)) {
    $image_val = Html::img(Yii::getAlias('@storageUrl/images/nouser.png'), ['class' => 'media-object img-rounded', 'style' => 'width: 60px;']);
    if (isset($model[$image_field_name]) && !empty($model[$image_field_name])) {
        $image_val = Html::img($model[$image_field_name], ['class' => 'media-object img-rounded', 'style' => 'width: 60px;']);
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

if (isset($bdate_field_name) && $bdate_field_name != '') {
    $path_data = [
        '{id}' => 'quebox-age',
        '{label}' => '',
        '{value}' => \backend\modules\thaihis\classes\ThaiHisQuery::calAge($model[$bdate_field_name]),
    ];
    $path_items["{age}"] = strtr($template_item, $path_data);
}

if (isset($template_custom) && !empty($template_custom)) {
    $template_content = $template_custom;
}

$content = strtr($template_content, $path_items);
?>
<a href="<?= $url ?>" data-key="<?= $model['target'] ?>" class="list-group-item item <?= ($pt_id == $model['target'] ? 'active' : '') ?>" style="padding: 5px 5px 5px;<?= isset($model['doc']) ? 'background-color: #ffec87;' : '' ?>">
    <?= $content ?>
</a>
