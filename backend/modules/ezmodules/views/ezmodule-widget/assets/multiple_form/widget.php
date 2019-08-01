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

$reloadDiv = "evalution-form-$module";
if(isset($options['target']) && !empty($options['target']) && $options['target']!='target'){
    $target = isset($_GET[$options['target']])?$_GET[$options['target']]:'';
}
$disabled = 0;

$url = Url::to(['/ezforms2/ezform-data/evalution-form', 'category'=>$category_id, 'target'=>$target, 'reloadDiv'=>$reloadDiv, 'modal'=>'modal-ezform-main', 'disabled'=>$disabled]);
$html = Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-url' => $url]);

?>

<div class="alert alert-<?=$theme?>">
	<h3 class="page-header"><?=$title?></h3>
        
        <?php
        echo kartik\select2\Select2::widget([
            'name' => 'ezf-select-'.$module,
            'value'=> '',
            'options' => ['placeholder' => Yii::t('ezform', 'Form'), 'id'=>'ezf-select-'.$module],
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

                   $('#ezf-select-$module').val('');
                   $('#ezf-select-$module').trigger('change');
                }",
            ]
        ]);
        ?>
        
        <?=$html?>
</div>

<?=$html?>
<?php \richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]); ?>
<script>
    // JS script
    initWidget();
    
    function initWidget(){
        $.ajax({
            method: 'POST',
            url: '<?=$url?>',
            dataType: 'HTML',
            success: function(result, textStatus) {
                $('#<?=$reloadDiv?>').html(result);
            }
        });
    }
    
    function modalEzformEvalution(url) {
        $('#modal-ezform-main .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
        $('#modal-ezform-main').modal('show')
        .find('.modal-content')
        .load(url);
    }
</script>
<?php \richardfan\widget\JSRegister::end(); ?>
