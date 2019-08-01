<?php

use yii\helpers\Html;
use yii\helpers\Url;
use backend\modules\ezmodules\models\EzmoduleMenu;
use appxq\sdii\widgets\ModalForm;
use backend\modules\ezmodules\classes\ModuleFunc;
use backend\modules\ezmodules\classes\ModuleQuery;

$userId = Yii::$app->user->id;

?>

<div id="ezmodule-tab-items">
    <?php
    
    $items = [];
    
    $modelList = ModuleQuery::getTabList($module, $userId);
    $limit = 8;
    $moreItems = [];
    $moreId=[];
    $ftab = 0;
    $reload = 0;
    $objTab = NULL;
    $dropdownEdit = NULL;
    if(isset($modelList) && !empty($modelList)){
        foreach ($modelList as $key => $value) {
            $gname = yii\helpers\Html::encode($value['label']);
            $checkthai = ModuleFunc::checkthai($gname);
//            $len = 12;
//            if ($checkthai != '') {
//                $len = $len * 3;
//            }
//            if (strlen($gname) > $len) {
//                $gname = substr($gname, 0, $len) . '...';
//            }
            
            if($key==0){
                $ftab = $value['tab_id'];
            }
            
            if($tab==0){
                Yii::$app->getResponse()->redirect(['/ezmodules/ezmodule/view', 'id'=>$module, 'addon'=>$addon, 'tab'=>$ftab]);
            }
            
            if($tab == $value['tab_id']){
                $objTab = $value;
            }
            
            $modelSubTab = backend\modules\ezmodules\models\EzmoduleTab::find()->where(['ezm_id' => $module, 'parent' => $value['tab_id']])->orderBy('order')->all();
            if ($modelSubTab) {
                $subItems = [];
                $subId = [];
                foreach ($modelSubTab as $subKey => $subValue) {
                    if($tab == $subValue['tab_id']){
                        $objTab = $subValue;
                    }

                    $subItems[] = [
                        'label' => $subValue['label'],
                        'url' => Url::to(['/ezmodules/ezmodule/view', 'id' => $module, 'tab'=>$subValue['tab_id'], 'addon' => $addon]), 
                        'active' => $controllerID == 'ezmodule' && $actionID == 'view' && $tab == $subValue['tab_id'],
                    ];
                    $subId[] = $subValue['tab_id'];
                }
                
                
                if (in_array($tab, $subId)) {
                    $dropdownEdit = [
                        'label' => '<i class="glyphicon glyphicon-edit"></i>',
                        'url' => Url::to(['/ezmodules/ezmodule-tab/save', 'id' => $value['tab_id'], 'module' => $module, 'user_module'=>$model['created_by']]),
                        'linkOptions'=>['class'=>'add-tab-list']
                    ];
                            
                }

                $items[] = [
                    'label' => $value['label'],
                    'url' => '#',
                    'items' => $subItems,
                    'dropDownOptions' => ['id' => \appxq\sdii\utils\SDUtility::getMillisecTime()],
                    'active' => $controllerID == 'ezmodule' && $actionID == 'view' && in_array($tab, $subId),
                ];
            } else {
                $items[] = [
                    'label' => $gname,
                    'url' => Url::to(['/ezmodules/ezmodule/view', 'id' => $module, 'tab'=>$value['tab_id'], 'addon' => $addon]), 
                    'active' => $controllerID == 'ezmodule' && $actionID == 'view' && $tab == $value['tab_id'],
                ];
            }
        }
        
    }
    $ezm_builder = explode(',', $model['ezm_builder']);
    if ((Yii::$app->user->can('administrator')) || $model['created_by'] == $userId || in_array($userId, $ezm_builder)) {
        $items[] = [
            'label' => '<i class="glyphicon glyphicon-plus"></i>',
            'url' => Url::to(['/ezmodules/ezmodule-tab/save', 'module' => $module, 'user_module'=>$model['created_by']]),
            'linkOptions'=>['class'=>'add-tab-list']
        ];
    }
    
    if($tab>0){
        if ((Yii::$app->user->can('administrator')) || $model['created_by'] == $userId || in_array($userId, $ezm_builder)) {
            $items[] = [
                'label' => '<i class="glyphicon glyphicon-pencil"></i>',
                'url' => Url::to(['/ezmodules/ezmodule-tab/save', 'id' => $tab, 'module' => $module, 'user_module'=>$model['created_by']]),
                'linkOptions'=>['class'=>'add-tab-list']
            ];
            
            if($dropdownEdit){
                $items[] = $dropdownEdit;
            }
            
            $items[] = [
                'label' => '<i class="glyphicon glyphicon-trash"></i>',
                'url' => Url::to(['/ezmodules/ezmodule-tab/delete',  'id' => $tab, 'module' => $module, 'ftab'=>$ftab]),
                'linkOptions'=>['id'=>'del-tab-list' , 'class'=>'del-list',
                    'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to delete this tab?'),
                    'method' => 'post',
                    ],
                ]
            ];
        }
    }
    
    ?>
    
    <?=
    \yii\bootstrap\Nav::widget([
        'id'=>\appxq\sdii\utils\SDUtility::getMillisecTime(),
        'items' => $items,
        'encodeLabels'=>false,
        'options' => ['class' => 'nav nav-tabs', 'id' => 'ezmodule_tab_menu'],
    ]);
    ?>
  
  <div style="margin-top: 15px;">
    <?php 
    if($objTab){
        
        if($objTab->widget!='dropdown'){
            $userId = Yii::$app->user->id;
            $modelModule = ModuleQuery::getModuleOne($module, $userId);
            $dataFilter = ModuleQuery::getFilterList($modelModule->ezm_id, $userId);

            $modelFilter = NULL;
            if($filter==0 && $filter!=''){
               $modelFilter = \backend\modules\ezmodules\models\EzmoduleFilter::find()->where('ezm_id = :ezm_id AND `ezm_default`=1', [':ezm_id'=>$modelModule->ezm_id])->one();
               $filter = isset($modelFilter->filter_id)?$modelFilter->filter_id:0;
            } else {
                $modelFilter = \backend\modules\ezmodules\models\EzmoduleFilter::find()->where('filter_id = :filter_id', [':filter_id'=>$filter])->one();
            }

            $op_params = [
                'model'=>$modelModule,
                'modelOrigin'=>$modelModule,
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
            $options = isset($objTab->options)?\appxq\sdii\utils\SDUtility::string2Array($objTab->options):[];
           
            if($options['render']=='/ezmodule/module_widget'){
                if(isset($options['params']['id']) && !empty($options['params']['id'])){
                    $path['{tab-widget}'] = $this->render($options['render'], isset($options['params'])? yii\helpers\ArrayHelper::merge($op_params, $options['params']):[]);
                } else {
                    $path['{tab-widget}'] = '<div class="alert alert-warning" role="alert">Please select a module.</div>';
                }
            } else {
                $path['{tab-widget}'] = $this->render($options['render'], isset($options['params'])? yii\helpers\ArrayHelper::merge($op_params, $options['params']):[]);
            }
            
            $params_all = [];
            if(isset($_GET)){
                $params_all = $_GET;
            }
            
            foreach ($params_all as $key_get => $value_get) {
                $path["{{$key_get}}"] = $value_get;
            }
            
            $modelWidget = \backend\modules\ezmodules\models\EzmoduleWidget::find()->where("widget_type='core' OR ezm_id=:ezm_id", [':ezm_id'=>$module])->all();
            foreach ($modelWidget as $key => $widget) {
                try {
                    if (strpos($objTab->template, "{{$widget['widget_varname']}}") !== false) {
                        if($widget['widget_attribute'] == 1){
                            $path["{{$widget['widget_varname']}}"] = $modelModule[$widget['widget_render']];
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
            
            echo strtr($objTab->template, $path);
            
        }
    }
    ?>
    </div>
  
</div>