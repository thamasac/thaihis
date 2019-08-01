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
$tab = isset($_GET['tab'])?$_GET['tab']:'';
$initdate = (isset($options['initdate']) && !empty($options['initdate']) && !empty($target))?$options['initdate']:'';
$show = isset($options['show'])?$options['show']:0;
$hide = isset($options['hide'])?$options['hide']:0;
$ezf_box = isset($options['ezf_box'])?$options['ezf_box']:1;

$reloadDiv = 'ezform-'.$widget_config['widget_varname'];
$reloadWidget = isset($options['reloadWidget'])?$options['reloadWidget']:$reloadDiv;

$dataid = '';
if(isset($options['dataid']) && !empty($options['dataid'])){
    $dataid = isset($_GET[$options['dataid']])?$_GET[$options['dataid']]:'';
} 

$current_url = \yii\helpers\Url::to(['/ezmodules/ezmodule/view','id'=>$module,'addon'=>$addon,'tab'=>$tab])."&$query_params";

if($initdate!=''){
    $modelEzf = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($ezf_id);
    if($modelEzf){
        $modelLastRecord = backend\modules\ezforms2\classes\EzfUiFunc::loadLastDateRecordNotModel($modelEzf->ezf_table, $target, $initdate);
        if($modelLastRecord){
            $dataid = isset($modelLastRecord['id'])?$modelLastRecord['id']:'';
        }
    }
    
}

if($hide == 1 && $dataid!=''){
    
} else {
    if($show){
        if(!empty($target)){
            echo yii\helpers\Html::tag('div', '', [
                'id'=>$reloadDiv,
                'data-modal'=>'modal-ezform-main',
                'data-url' => \yii\helpers\Url::to(['/ezforms2/ezform-data/ezform',
                    'ezf_id'=>$ezf_id,
                    'modal'=>'modal-ezform-main',
                    'reloadDiv'=>$reloadWidget,
                    'target'=>$target,
                    'dataid'=>$dataid,
                    'ezf_box' => $ezf_box,
                    'reloadPage'=> ($query_params!='')?base64_encode($current_url):'',
                    ]),
                'data-dataid'=>$dataid,
            ]);
        } 
    } else {
        echo yii\helpers\Html::tag('div', '', [
                'id'=>$reloadDiv,
                'data-modal'=>'modal-ezform-main',
                'data-url' => \yii\helpers\Url::to(['/ezforms2/ezform-data/ezform',
                    'ezf_id'=>$ezf_id,
                    'modal'=>'modal-ezform-main',
                    'reloadDiv'=>$reloadWidget,
                    'target'=>$target,
                    'dataid'=>$dataid,
                    'ezf_box' => $ezf_box,
                    'reloadPage'=> ($query_params!='')?base64_encode($current_url):'',
                    ]),
                'data-dataid'=>$dataid,
            ]);
    }
}


?>

<?php \richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]); ?>
<script>
    // JS script
    getEzformAjax($('#<?=$reloadDiv?>').attr('data-url'), '<?=$reloadDiv?>');
    
    function getEzformAjax(url, divid) {
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