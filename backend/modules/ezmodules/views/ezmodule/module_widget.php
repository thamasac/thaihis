<?php

use yii\helpers\Html;
use backend\modules\ezforms2\classes\EzfStarterWidget;
use backend\modules\ezforms2\classes\EzfHelper;
use appxq\sdii\widgets\ModalForm;
use backend\modules\ezmodules\classes\ModuleFunc;
use appxq\sdii\helpers\SDHtml;
use backend\modules\ezmodules\classes\ModuleQuery;
use backend\modules\ezmodules\models\EzmoduleTemplate;


$module = $id;
$menu = Yii::$app->request->get('menu', 0);
$addon = Yii::$app->request->get('addon', 0);
$filter = Yii::$app->request->get('filter', '0');
$target = Yii::$app->request->get('target', '');

$userId = Yii::$app->user->id;
$model = ModuleQuery::getModuleOne($id, $userId);
if(!$model){
    echo Html::tag(
            'div', 
            SDHtml::getMsgSuccess() . Yii::t('ezmodule', 'You do not have right to use this page.'), 
            ['class' => 'alert-warning']
        );
        Yii::$app->end();
}

if($addon>0){
    $modelModule = ModuleQuery::getModuleOne($addon, $userId);
} else {
    $modelModule = $model;
}

if(!$modelModule && !isset($modelModule->ezf_id)){
    echo Html::tag(
            'div', 
            SDHtml::getMsgSuccess() . Yii::t('ezmodule', 'You do not have right to use this page.'), 
            ['class' => 'alert-warning']
        );
        Yii::$app->end();
}

$template = EzmoduleTemplate::findOne($modelModule->template_id);
if(!$template){
    echo Html::tag(
            'div', 
            SDHtml::getMsgSuccess() . Yii::t('ezmodule', 'Template not found.'), 
            ['class' => 'alert-warning']
        );
        Yii::$app->end();
}

$ezf_id = $modelModule->ezf_id;

$dataFilter = ModuleQuery::getFilterList($modelModule->ezm_id, $userId);

$modelFilter = NULL;
if($filter==0 && $filter!=''){
   $modelFilter = \backend\modules\ezmodules\models\EzmoduleFilter::find()->where('ezm_id = :ezm_id AND `ezm_default`=1', [':ezm_id'=>$modelModule->ezm_id])->one();
   $filter = isset($modelFilter->filter_id)?$modelFilter->filter_id:0;
} else {
    $modelFilter = \backend\modules\ezmodules\models\EzmoduleFilter::find()->where('filter_id = :filter_id', [':filter_id'=>$filter])->one();
}

$modelWidget = \backend\modules\ezmodules\models\EzmoduleWidget::find()->where("widget_type='core' OR ezm_id=:ezm_id", [':ezm_id'=>$module])->all();


$options = isset($model->options)?appxq\sdii\utils\SDUtility::string2Array($model->options):[];

$this->title = $model->ezm_name;
$userId = Yii::$app->user->id;
//$this->params['breadcrumbs'][] = ['label' => Yii::t('ezmodule', 'Ezmodule'), 'url' => ['index']];
//$this->params['breadcrumbs'][] = $this->title;
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
    'filter'=>$filter,
    'reloadDiv'=>'grid-widget',
    'dataFilter'=>$dataFilter,
    'modelFilter'=>$modelFilter,
    'target'=>$target,
];

foreach ($modelWidget as $key => $widget) {
    if($widget['widget_attribute'] == 1){
        $path["{{$widget['widget_varname']}}"] = $model[$widget['widget_render']];
    } else {
        if(isset($widget['widget_render']) && !empty($widget['widget_render'])){
            $path["{{$widget['widget_varname']}}"] = $this->render($widget['widget_render'], \yii\helpers\ArrayHelper::merge($op_params, ['widget_config'=>$widget]));
        }
    }
}

$content = strtr($templateHtml, $path);

?>
<div id="ezmodule-widget-app" class="ezmodule-view">
        <?php EzfStarterWidget::begin(); ?>
     <?php
        if(isset($options['module_menu']) && $options['module_menu']==1){
            echo $this->render('_widget_module_menu', $op_params);
        }
     ?>
        <?=$content?>
        <?php EzfStarterWidget::end(); ?>
    
</div>
