<?php

use yii\helpers\Html;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use yii\helpers\Url;
use backend\modules\ezforms2\classes\EzfHelper;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use backend\modules\ezforms2\classes\EzfFunc;

$template_item = '<strong>{label}</strong> {value}';
$path_items = [];
$path_value = [];
$path_var = [];

foreach ($modelFields as $key => $value) {
    $var = $value['ezf_field_name'];
    $label = $value['ezf_field_label'];
    
    $dataInput;
    if (isset(Yii::$app->session['ezf_input'])) {
        $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($value['ezf_field_type'], Yii::$app->session['ezf_input']);
    }
    $data_value = backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $value, $model);
            
    $item_value = "' + repo.$var+'";
    $path_data = [
        '{label}' => $label,
        '{value}' => $item_value,
    ];
    

    $path_items["{{$var}}"] = strtr($template_item, $path_data);
    $path_value["{{$var}}"] = $data_value;
    $path_var["{{$var}}"] = $item_value;
}

//start content
$template_content = '';
if(isset($options['template_items']) && !empty($options['template_items'])){
    $template_content = $options['template_items'];
    $path_items = $path_var;
} else {
    foreach ($fields as $field) {
        $fieldName = $field;
        $template_content .= "{{$fieldName}} ";
    }
}

$template_selection = '';

if(isset($options['template_selection']) && !empty($options['template_selection'])){
    $template_selection = $options['template_selection'];
} else {
    foreach ($fields as $field) {
        $fieldName = $field;
        $template_selection .= "{{$fieldName}} ";
    }
}

if(isset($options['image_field']) && !empty($options['image_field'])){
    $image_field = "<img src=\"' + getImage(repo.{$options['image_field']}) + '\" class=\"media-object img-rounded\" style=\"width:50px;\" />";
    //$image_field = "<img src=\"' + repo.{$options['image_field']} + '\" class=\"media-object img-rounded\" style=\"width:50px;\" />";

    $template_content = "<div class=\"media\"><div class=\"media-left\">$image_field</div><div class=\"media-body\">$template_content</div></div>";
}

$content = strtr($template_content, $path_items);
$selection = strtr($template_selection, $path_var);
$initValueText = strtr($template_selection, $path_value);
//end content
?>


<?php
$this->registerJs(" 
    function getImage(img) {
        var url = '".Yii::getAlias('@storageUrl/images/nouser.png')."';
       if(img){
            url = '".Yii::getAlias('@storageUrl/ezform/fileinput/')."'+img;
       }
       return url;
    }    

");

$this->registerJs(" 
    var formatRepo = function (repo) {
        if (repo.loading) {
            return repo.text;
        }   

        var markup = '$content';

        return '<div style=\"overflow:hidden;\">' + markup + '</div>';
    };
    
    var formatRepoSelection = function (repo) {
        var fullname = repo.text;
        if(repo.rstat){
            fullname = '$selection';
        }
        return fullname;
    }
", yii\web\View::POS_HEAD);
     

$resultsJs = <<< JS
function (data, params) {
    params.page = params.page || 1;
    return {
        results: data.items,
        pagination: {
            more: (params.page * 10) < data.total_count
        }
    };
}
JS;

?>
<?php
$form = ActiveForm::begin([
            'id' => 'jump-menu-'.$reloadDiv,
            'action' => [$current_url],
            'method' => 'get',
            //'layout' => 'inline',
            //'options' => ['style' => 'display: inline-block;',]
]);
?>

<?php
echo Select2::widget([
      'id' => 'target-search-'.$reloadDiv,  
      'name' => 'target',
      'value' => "$dataid",
      'initValueText' => $initValueText,
      'options' => ['placeholder' => Yii::t('patient', 'Search') . ' ...', 'onChange' => '$("#jump-menu-'.$reloadDiv.'").submit()'],
      'pluginOptions' => [
          'allowClear' => FALSE,
          'minimumInputLength' => 0,
          'ajax' => [
              'url' => Url::to(['/ezforms2/ezform-data/search', 
                    'fields'=>EzfFunc::arrayEncode2String($fields),
                    'fields_search'=>EzfFunc::arrayEncode2String($fields_search),
                    'ezf_id' => "$ezf_id",
                    'dataid'=>"$dataid",
                    'target' => "$target",
                    'targetField' => $targetField,
                  ]),
              'dataType' => 'json',
              'delay' => 10,
              'data' => new JsExpression('function(params) { return {q:params.term, page: params.page}; }'),
              'processResults' => new JsExpression($resultsJs),
              'cache' => true
          ],
          'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
          'templateResult' => new JsExpression('formatRepo'),
          'templateSelection' => new JsExpression('formatRepoSelection'),
      ],
  ]);
?>

<?php ActiveForm::end(); ?>