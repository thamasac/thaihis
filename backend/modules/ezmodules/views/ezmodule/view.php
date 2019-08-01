<?php

use yii\helpers\Html;
use backend\modules\ezforms2\classes\EzfStarterWidget;
use backend\modules\ezforms2\classes\EzfHelper;
use appxq\sdii\widgets\ModalForm;
use backend\modules\ezmodules\classes\ModuleFunc;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model backend\modules\ezmodules\models\Ezmodule */
\backend\modules\ezmodules\assets\ModuleAsset::register($this);
\backend\modules\ezforms2\classes\EzfAuthFuncManage::auth()->AccessRead($id); //access module
\backend\modules\ezmodules\classes\ModuleAccess::widget(['id'=>$id]);

$options = isset($model->options)?appxq\sdii\utils\SDUtility::string2Array($model->options):[];

$this->title = $model->ezm_name;
$userId = Yii::$app->user->id;

$this->params['breadcrumbs'][] = ['label' => Yii::t('ezmodule', 'EzModule'), 'url' => ['/ezmodules/default/index']];
$this->params['breadcrumbs'][] = $this->title;



$templateHtml = $modelModule->ezm_html;
$templateJs = isset($modelModule->ezm_js)?$modelModule->ezm_js:'';
$templateCss = isset($modelModule->ezm_css)?$modelModule->ezm_css:'';

$path = [];

$op_params = [
    'model'=>$modelModule,
    'modelOrigin'=>$model,
    'menu'=>$menu,
    'module'=>$module,
    'addon'=>$addon,
    'tab'=>$tab,
    'filter'=>$filter,
    'reloadDiv'=>'grid-widget',
    'dataFilter'=>$dataFilter,
    'modelFilter'=>$modelFilter,
    'target'=>$target,
];


$icon = Html::img(ModuleFunc::getNoIconModule(), ['width' => 30, 'class' => 'img-rounded']);
if (isset($model->ezm_icon) && !empty($model->ezm_icon)) {
    $icon = Html::img($model['icon_base_url'] . '/' . $model['ezm_icon'], ['width' => 30, 'class' => 'img-rounded']);
}  


$this->registerCss($templateCss);

?>

<div id="ezmodule-main-app" class="ezmodule-view">
    <?php echo backend\modules\ezmodules\classes\ModuleIconBtn::btnPermission($module);?>
    <div class="modal-header">
        <?php
        
        
        $ezm_builder = explode(',', $model['ezm_builder']);
        if((Yii::$app->user->can('administrator')) || $model['created_by']==$userId || in_array($userId, $ezm_builder)){
            echo Html::a('', ["/ezmodules/ezmodule/update", 'id'=>$module, 'tab'=>4], [
                'id'=>'modal-btn-ezmodule',
                'class'=>'fa fa-cog fa-2x pull-right underline',
                'data-toggle'=>'tooltip',
                'title'=>Yii::t('ezmodule', 'EzModule Settings'),
            ]);
            
//            echo Html::a('', ["/ezmodules/ezmodule/template", 'id'=>$module], [
//                'class'=>'fa fa-paint-brush fa-2x pull-right underline',
//                'data-toggle'=>'tooltip',
//                'title'=>Yii::t('ezmodule', 'Template (for advanced users)'),
//            ]);
        }
        
        echo Html::a('', '', [
            'id'=>'modal-btn-info-app',
            'class'=>'fa fa-info-circle fa-2x pull-right underline info-app',
            'data-url'=> yii\helpers\Url::to(['/ezmodules/default/info-app', 'id'=>$module]),
            'data-toggle'=>'tooltip',
            'title'=>Yii::t('ezmodule', 'Module Information'),
        ]);
        ?>
	<h4 class="modal-title"><?=$icon?> <?= Html::encode($this->title) ?></h4>
    </div>
  <?=
ModalForm::widget([
    'id' => 'modal-ezmodule',
    'size' => 'modal-xxl',
    'tabindexEnable' => FALSE,
]);
?>

<?=ModalForm::widget([
    'id' => 'modal-info-app',
    //'size'=>'modal-lg',
]);
?>

<?=  ModalForm::widget([
    'id' => 'modal-add-widget',
    'size' => 'modal-lg',
    'tabindexEnable' => false,
]);
?>

<?=  ModalForm::widget([
    'id' => 'modal-ezmodule-widget',
    'size' => 'modal-lg',
    'tabindexEnable' => false,
]);
?>

<div id="modal-widget-box">
    
</div>
  
  <div class="modal-body">
        <?php EzfStarterWidget::begin(); ?>
        <?php
        if(isset($options['menu']) && $options['menu']==1){
            echo $this->render('_widget_menu', $op_params);
        }

        if(isset($options['module_menu']) && $options['module_menu']==1){
            echo $this->render('_widget_module_menu', $op_params);
        } elseif(isset($options['module_menu']) && $options['module_menu']==2){
            echo $this->render('_widget_tab_menu', $op_params);
        }
        
        ?>
      
        
        <?php
        if(isset($options['module_menu']) && $options['module_menu']==2){
            
        } else {
            foreach ($modelWidget as $key => $widget) {
                try {
                    if (strpos($templateHtml, "{{$widget['widget_varname']}}") !== false) {
                        if($widget['widget_attribute'] == 1){
                            $path["{{$widget['widget_varname']}}"] = $model[$widget['widget_render']];
                        } else {
                            if(isset($widget['widget_render']) && !empty($widget['widget_render'])){
                                $path["{{$widget['widget_varname']}}"] = $this->render($widget['widget_render'], \yii\helpers\ArrayHelper::merge($op_params, ['widget_config'=>$widget]));
                            }
                        }
                    }
                } catch (\Exception $e) {
                    $path["{{$widget['widget_varname']}}"] = '<div class="alert alert-danger" role="alert"> <strong>Error Widget!</strong> '.$e->getMessage().' </div>';
                    \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
                }
            }
            
            echo strtr($templateHtml, $path);
        }
        ?>
        <?php EzfStarterWidget::end(); ?>
    </div>
    
</div>


<?php 
    if(isset(Yii::$app->params['project_setup_site_navigator_label'])){
        $this->params['breadcrumbs'][] = ['label' => isset(Yii::$app->params['project_setup_site_navigator_label'])?Yii::$app->params['project_setup_site_navigator_label']:'', 'url' => ['#'], 'class'=>'site-navigator'];
    }
?>
<?php $this->registerJs("
    
$('#modal-ezmodule-widget').on('hidden.bs.modal', function(e){
    var hasmodal = $('body .modal').hasClass('in');
    if(hasmodal){
        $('body').addClass('modal-open');
    } 
});

$('#modal-add-widget').on('hidden.bs.modal', function(e){
    var hasmodal = $('body .modal').hasClass('in');
    if(hasmodal){
        $('body').addClass('modal-open');
    } 
});

$('#modal-add-widget').on('click', '.btn-widget', function() {
    $('#ezmoduletab-template').froalaEditor('html.insert', $(this).attr('data-widget'), false);
    $('#ezmodule-ezm_html').froalaEditor('html.insert', $(this).attr('data-widget'), false);
});

$('#modal-btn-ezmodule2').on('click', function() {
    let url = $(this).attr('href');
    
    $('#modal-ezform-main .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
    $('#modal-ezform-main').modal('show')
    .find('.modal-content')
    .load(url);
    return false;
});

$('#modal-btn-ezmodule').on('click', function() {
    modalEzmodule($(this).attr('href'));
    return false;
});

$('#ezf-main-app').on('click', '.btn-ezmodule', function() {
    modalEzmodule($(this).attr('href'));
    return false;
});

$('#modal-ezmodule').on('hidden.bs.modal', function (e) {
  location.reload();
});

function modalEzmodule(url) {
    $('#modal-ezmodule .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
    $('#modal-ezmodule').modal('show')
    .find('.modal-content')
    .load(url);
}

$('#ezmodule-main-app').on('click', '.info-app', function() {
    modalApp($(this).attr('data-url'));
    return false;
});    

function modalApp(url) {
    $('#modal-info-app .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
    $('#modal-info-app').modal('show')
    .find('.modal-content')
    .load(url);
}
    

"); ?>

<?php
$this->registerJs($templateJs);
//backend\modules\ezmodules\classes\ModuleAccess::widget(['id'=>$id]);
//$accessButton = \backend\modules\ezforms2\classes\EzfAuthFuncManage::auth()->accessManage($id, 2);
//$accessButton = (empty($accessButton) || $accessButton == FALSE) ? 0 : 1; 
?>
 
<?php \richardfan\widget\JSRegister::begin();?>
<?php backend\modules\ezforms2\assets\JLoading::register($this);?>
<script>
    function onLoadings(ele){
        $(ele).waitMe({
            effect : 'facebook',
            text : 'Please wait...',
            bg : 'rgba(255,255,255,0.7)',
            color : '#000',
            maxSize : '',
            waitTime : -1,
            textPos : 'vertical',
            fontSize : '',
            source : '',
            onClose : function() {}
        });
    }
    function hideLoadings(ele){
         $(ele).waitMe("hide");
    } 
    $('.site-navigator').on('click', function(){
      let url = '<?= yii\helpers\Url::to(['/site/site-navigator'])?>';
      $.get(url, function(result){
          $('#modal-info-app .modal-content').html(result);
          $('#modal-info-app').modal('show');
      });
       return false;
    });
</script>
<?php    \richardfan\widget\JSRegister::end(); ?>