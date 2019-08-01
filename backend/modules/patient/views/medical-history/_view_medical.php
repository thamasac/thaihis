<?php

use yii\helpers\Html;
use yii\helpers\Url;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$contents = isset($options['contents']) ? $options['contents'] : [];

if(isset($options['widget_id'])){
    $container_widget = \backend\modules\subjects\classes\SubjectManagementQuery::getWidgetById($options['widget_id']);

    $options_container = $container_widget['options'];
    $options_container = \appxq\sdii\utils\SDUtility::string2Array($options_container);
    $url = \yii\helpers\Url::to(['/thaihis/container-widget/container-content', 'options' => $options_container, 'visitid' => $visit_id, 'target'=>$target,
                'visit_type' => $visit_type, 'visitdate' => $visit_date, 'modal' => $modal]);
}
$template_all = '';
$template_default = '';
$path_items = [];
if (is_array($modelFields)) {
    foreach ($modelFields as $key => $value) {
        foreach ($value as $keyVal => $val) {
            $fieldName = $val['ezf_field_name'];
            $fieldLabel = $val['ezf_field_label'];
            $dataInput;
            if (isset(Yii::$app->session['ezf_input'])) {
                $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($val['ezf_field_type'], Yii::$app->session['ezf_input']);
            }
            $valueContent = backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $val, $model_content[$key]);
            if(strlen($valueContent) > 100){
                $valueContent = substr($valueContent, 0,100)."...";
            }
            $path_data = [
                '{id}' => Html::getInputId($model_content[$key], $fieldName),
                '{value}' => $valueContent,
            ];
            
            $template_item = '<div id="box-{id}" >{value}</div>';
            $path_items["{{$fieldName}}"] = strtr($template_item, $path_data);

            $template_default .= "<label>" . $fieldLabel . ":</label> {{$fieldName}}";
        }
        $template = '';
        if (!isset($contents[$key]['template_box'])) {
            $template = "<div>";
            $template .= $template_default;
            $template .= "</div>";
        } else {
            $template = isset($contents[$key]['template_box']) ? $contents[$key]['template_box'] : null;
        }
        $template_all .= $template;
    }
}

$template = '<div id="view-tk-cpoe">
            <dl class="dl-horizontal">
              <dt style="width: 35px;">MD:</dt>
              <dd style="margin-left: 40px;">
               {md}
              </dd>
            </dl>
            </div>';

        $doctor_concat = backend\modules\thaihis\classes\ThaiHisQuery::getMedicalDoctor($visit_id);
        $path_data = [
                '{id}' => 'medical-md',
                '{value}' => isset($doctor_concat['doctor_concat'])?$doctor_concat['doctor_concat']:'' ,
            ];
        $template_item = '<div id="box-{id}" >{value}</div>';
        $path_items["{md}"] = strtr($template_item, $path_data);

$template_all .= $template;

$content = strtr($template_all, $path_items);
$action_name = "Electronic Medical Record";
if (isset($options['action_title']))
    $action_name = $options['action_title'];
?>
<div class="col-md-12">
    <div class="form-group row">
        <button type="button" class="btn btn-block btn-info" data-url="<?= $url ?>" data-modal="modal-history-cpoe" onclick="modalMedicalHistory('<?= $url ?>', '<?= $modal ?>')">
            <strong><?= $action_name ?></strong>
        </button>
    </div>
    <div class="form-group row">
        <?php
        echo $content;
        ?>
    </div>
</div>
<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>
<script>
    function modalMedicalHistory(url, modal) {
        $('#' + modal + ' .modal-body').html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        $('#' + modal).modal('show')
                .find('.modal-body')
                .load(url);
    }

    $('#modal-history-cpoe').on('hidden.bs.modal', function (e) {
        $('#modal-history-cpoe .modal-body .col-md-12').html(' ');
    });
</script>
<?php \richardfan\widget\JSRegister::end(); ?>