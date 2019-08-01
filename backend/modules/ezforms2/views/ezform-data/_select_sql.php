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
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$width = isset($options['width'])?$options['width']:450;
$template_item = '<strong>{label}</strong> {value}';
$path_items = [];
$path_value = [];
$path_var = [];

foreach ($sql_builder['variable'] as $key => $value) {
    $var = $value;
    $label = $value;
    
    $data_value = isset($data[$value])?$data[$value]:'';
            
    $item_value = "' + repo.$var+'";
    $path_data = [
        '{label}' => $label,
        '{value}' => $item_value,
    ];
    
    
    if(isset($options['age_field']) && $options['age_field']==$var){
        $path_items["{fix_age}"] = strtr($template_item, [
            '{label}' => $label,
            '{value}' => "' + repo.fix_age+'",
        ]);
        $path_value["{fix_age}"] = isset($data_value) && $data_value!=''?\appxq\sdii\utils\SDdate::getAgeMysqlDate($data_value):'';//
        $path_var["{fix_age}"] = "' + repo.fix_age+'";
    }
    
    $path_items["{{$var}}"] = strtr($template_item, $path_data);
    $path_value["{{$var}}"] = $data_value;
    $path_var["{{$var}}"] = $item_value;
    
    $key_id = substr($options['key_id'], 1, -1);
    if($key_id==$var){
        $path_value['{key_id}'] = $data_value;
    }
    
}

//start content
$template_content = '';
if(isset($options['template_items']) && !empty($options['template_items'])){
    $template_content = $options['template_items'];
    $path_items = $path_var;
} else {
    foreach ($sql_builder['variable'] as $field) {
        $fieldName = $field;
        $template_content .= "{{$fieldName}} ";
    }
    if(isset($options['age_field']) && $options['age_field']!=''){
        $template_content .= "{fix_age} ";
    }
}


$template_selection = '';

if(isset($options['template_selection']) && !empty($options['template_selection'])){
    $template_selection = $options['template_selection'];
} else {
    foreach ($sql_builder['variable'] as $field) {
        $fieldName = $field;
        $template_selection .= "{{$fieldName}} ";
    }
//    if(isset($options['age_field']) && $options['age_field']!=''){
//        $template_selection .= "{fix_age} ";
//    }
}

$image = isset($options['image'])?substr($options['image'], 1, -1):'';

$image_wigth = isset($options['image_wigth'])?$options['image_wigth']:64;
if(isset($options['image']) && !empty($options['image'])){
    $image_field = "<img src=\"' + getImage(repo.{$image}) + '\" class=\"media-object img-rounded\" style=\"width:{$image_wigth}px;\" />";
    //$image_field = "<img src=\"' + repo.{$options['image']} + '\" class=\"media-object img-rounded\" style=\"width:50px;\" />";

    $template_content = "<div class=\"media\"><div class=\"media-left\">$image_field</div><div class=\"media-body\">$template_content</div></div>";
}

$content = strtr($template_content, $path_items);
$selection = strtr($template_selection, $path_var);
$initValueText = strtr($template_selection, $path_value);
//end content

$query_params = [];
$current_url = isset($options['current_url'])?$options['current_url']:'';
$query_content = '';
if(isset($options['query_params']) && !empty($options['query_params']) ){
    $query_content = $options['query_params'];
    $path_value['{key_id}']='';
    if(isset($_GET[$options['key_name']])){
        
    } else {
        $path_value['{target}']='';
    }
    $query_params = strtr($options['query_params'], $path_value);
}

$url = $current_url;

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
            more: (params.page * {$options['page_size']}) < data.total_count
        }
    };
}
JS;

?>
<?php
$input_width = isset($options['width'])?$options['width']:'450';
$reload_widget = isset($options['reload_widget'])?$options['reload_widget']:'not_widget';
$ezf_id = isset($options['ezf_id'])?$options['ezf_id']:'';
$save_ezf_id = isset($options['save_ezf_id'])?$options['save_ezf_id']:'';
$after_save_url = isset($options['after_save_url'])?$options['after_save_url']:'';
$after_save_js = '';
if($after_save_url!=''){
    $option_json = \appxq\sdii\utils\SDUtility::array2String($options);
    $option_json = $option_json==''?'{}':$option_json;
    $after_save_js = "
        $.ajax({
            method: 'POST',
            url: '$after_save_url',
            data:{id:e.params.data.id, options:$option_json}
            dataType: 'JSON',
            success: function(result, textStatus) {
                ".SDNoty::show('result.message', 'result.status')."
            }
        });
    ";
}

$reload_widget_js = '';
if($reload_widget!=''){
    $reload_widget_js = "
        let url = $('#$reload_widget').attr('data-url');
        $.ajax({
            method: 'POST',
            url: url,
            dataType: 'HTML',
            success: function(result, textStatus) {
                $('#$reload_widget').html(result);
            }
        });
    ";
}

$parent = $target;
//if(isset($options['parent_name']) && !empty($options['parent_name'])){
//    $parent = isset($get_params[$options['parent_name']])?$get_params[$options['parent_name']]:$target;
//}

$unset_fields = '';
if(isset($options['data_column']) && !empty($options['data_column'])){
    $unset_fields = \appxq\sdii\utils\SDUtility::array2String($options['data_column']);
}

echo Select2::widget([
      'id' => 'select-search-'.$reloadDiv,  
      'name' => $reloadDiv,
      //'initValueText' => $initValueText,
      'options' => ['placeholder' => $placeholder, ],
      'pluginOptions' => [
          'allowClear' => true,
          'minimumInputLength' => 0,
          'ajax' => [
              'url' => Url::to(['/ezforms2/ezform-data/search-ezsql', 
                  
                    'options'=>EzfFunc::arrayEncode2String($options),
                    'sql_id' => "$sql_id",
                    'target' => "$parent",
                  ]),
              'dataType' => 'json',
              'delay' => 500,
              'data' => new JsExpression('function(params) { return {q:params.term, page: params.page}; }'),
              'processResults' => new JsExpression($resultsJs),
              'cache' => true,
              'error' => new JsExpression('function(jqXHR,error, errorThrown) { 
                        if(jqXHR.status&&jqXHR.status==403){
                            window.location.href = "'.Url::to(['/user/login']).'"
                        }
                    }'),
          ],
          'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
          'templateResult' => new JsExpression('formatRepo'),
          'templateSelection' => new JsExpression('formatRepoSelection'),
      ],
      'pluginEvents' => [
          "select2:select" => new JsExpression('function(e) { 
                let data = e.params.data;
                let path = {};
                let content = "'.$query_content.'";
                let key_id = "'.(isset($options['key_id'])?$options['key_id']:'').'";
                for (var key in data) {
                  path["{"+key+"}"] = data[key];
                }
                path["{key_id}"] = data["'.substr($options['key_id'], 1, -1).'"];

                $.ajax({
                    method: "POST",
                    url: "'.Url::to(['/ezforms2/ezform/multi-save', 'target'=>"$parent", 'ezf_id'=>"$ezf_id", 'save_ezf_id'=>"$save_ezf_id", 'unset_fields'=>$unset_fields]).'",
                    data:{id:e.params.data.id},    
                    dataType: "JSON",
                    success: function(result, textStatus) {
                        if(result.status == "success") {
                            '.$after_save_js.'
                                
                            '.$reload_widget_js.'
                        } 
                        
                        '.SDNoty::show('result.message', 'result.status').'
                        
                    }
                });
                
                $("#select-search-'.$reloadDiv.'").val("").trigger("change");
              }'),
      ],
  ]);
?>

<?php \richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]); ?>
<script>
    // JS script
    String.prototype.strtr = function ( dic ) { 
    const str = this.toString(),
          makeToken = ( inx ) => `{{###~${ inx }~###}}`,
          
          tokens = Object.keys( dic )       
            .map( ( key, inx ) => ({
              key,
              val: dic[ key ],
              token: makeToken( inx )
            })),
              
          tokenizedStr = tokens.reduce(( carry, entry ) => 
            carry.replace( entry.key, entry.token ), str );
            
    return tokens.reduce(( carry, entry ) => 
            carry.replace( entry.token, entry.val ), tokenizedStr );
};

</script>
<?php \richardfan\widget\JSRegister::end(); ?>