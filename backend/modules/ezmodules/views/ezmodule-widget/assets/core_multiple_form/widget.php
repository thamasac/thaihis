<?php
// start widget builder
use yii\helpers\Url;
use yii\web\JsExpression;
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

   
$category_id = isset($options['category_id'])?$options['category_id']:0;
$title = isset($options['title'])?$options['title']:'';
$theme = isset($options['theme'])?$options['theme']:'default';
$show = isset($options['show'])?$options['show']:0;
$order_by = isset($options['order_by'])?$options['order_by']:3;
$placeholder = isset($options['placeholder'])?$options['placeholder']:'Search Form ...';
$disabled = isset($options['disabled'])?$options['disabled']:0;

$reloadDiv = "evalution-form-{$widget_config['widget_varname']}";
if(isset($options['target']) && !empty($options['target']) && $options['target']!='target'){
    $target = isset($_GET[$options['target']])?$_GET[$options['target']]:'';
}

$url = Url::to(['/ezforms2/ezform-data/evalution-form', 'category'=>$category_id, 'target'=>$target, 'reloadDiv'=>$reloadDiv, 'modal'=>'modal-ezform-main', 'disabled'=>$disabled, 'orderby'=>$order_by]);
$html = Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-url' => $url]);

$echo_content = FALSE;
if($show){
    if(!empty($target)){
        $echo_content = true;
    }
} else {
    $echo_content = true;
}
?>
<?php if($echo_content):?>
<div class="alert alert-<?=$theme?>">
	<h3 class="page-header"><?=$title?></h3>
        <div style="margin-bottom: 5px;">
        <?php
        if($disabled==0){
        echo kartik\select2\Select2::widget([
            'name' => 'ezf-select-'.$widget_config['widget_id'],
            'value'=> '',
            'options' => ['placeholder' => $placeholder, 'id'=>'ezf-select-'.$widget_config['widget_id']],
            'pluginOptions' => [
                'allowClear' => true,
                'ajax' => [
                    'url' => Url::to(['/ezforms2/ezform/get-forms-group', 'category_id'=>$category_id]),
                    'dataType' => 'json',
                    'data' => new JsExpression('function(params) { return {q:params.term}; }'),
                    'error' => new JsExpression('function(jqXHR,error, errorThrown) { 
                        if(jqXHR.status&&jqXHR.status==403){
                            window.location.href = "'.Url::to(['/user/login']).'"
                        }
                    }'),
                ],
            ],
            'pluginEvents' => [
                "select2:select" => "function(e) { 
                    let ezf_id = e.params.data.id;
                    let url = '".Url::to(['/ezforms2/ezform-data/ezform', 'target'=>$target, 'reloadDiv'=>$reloadDiv, 'modal'=>'modal-ezform-main', 'popup'=>1, 'ezf_id'=>''])."'+ezf_id;

                    modalEzformEvalution(url);

                   $('#ezf-select-{$widget_config['widget_id']}').val('').trigger('change');
                }",
            ]
        ]);
        }     
        ?>
        </div>
        
        <?=$html?>
</div>

<?=$html?>
<?php \richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]); ?>
<script>
    // JS script
    $.ajax({
        method: 'POST',
        url: '<?=$url?>',
        dataType: 'HTML',
        success: function(result, textStatus) {
            $('#<?=$reloadDiv?>').html(result);
        }
    });
    
    function modalEzformEvalution(url) {
        $('#modal-ezform-main .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
        $('#modal-ezform-main').modal('show')
        .find('.modal-content')
        .load(url);
    }
</script>
<?php \richardfan\widget\JSRegister::end(); ?>
<?php endif;?>