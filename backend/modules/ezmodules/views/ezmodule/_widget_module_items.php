<?php

use yii\helpers\Html;
use yii\helpers\Url;
use backend\modules\ezmodules\models\EzmoduleMenu;
use appxq\sdii\widgets\ModalForm;
use backend\modules\ezmodules\classes\ModuleFunc;
use backend\modules\ezmodules\classes\ModuleQuery;

$userId = Yii::$app->user->id;
$iconSize = 18;
$icon = Html::img(ModuleFunc::getNoIconModule(), ['width' => $iconSize, 'class' => 'img-rounded']);
if (isset($model->ezm_icon) && !empty($model->ezm_icon)) {
    $icon = Html::img($model['icon_base_url'] . '/' . $model['ezm_icon'], ['width' => $iconSize, 'class' => 'img-rounded']);
} 

?>

<div id="ezmodule-modules-items">
    <?php
    
    $items = [
        [
            'label' => $icon.' '.$model['ezm_name'],
            'url' => Url::to(["/ezmodules/ezmodule/view", 'id' => $module]),
            'active' => $controllerID == 'ezmodule' && $actionID == 'view' && $module==$model['ezm_id'] && $addon ==0, 
        ],
    ];
    
    $modelList = ModuleQuery::getModuleList($module, $userId);
    $limit = 8;
    $moreItems = [];
    $moreId=[];
    
    $owner = 0;
    if(isset($modelList) && !empty($modelList)){
        foreach ($modelList as $key => $value) {
            $gname = yii\helpers\Html::encode($value['ezm_name']);
            $checkthai = ModuleFunc::checkthai($gname);
            $len = 12;
            if ($checkthai != '') {
                $len = $len * 3;
            }
            if (strlen($gname) > $len) {
                $gname = substr($gname, 0, $len) . '...';
            }
            
            if($addon == $value['module_id'] && $userId == $value['user_id']){
                $owner = 1;
            }
            
            $info = Html::button('<i class="glyphicon glyphicon-info-sign"></i>', ['class'=>'btn btn-link btn-xs btn-info info-app', 'data-url'=> yii\helpers\Url::to(['/ezmodules/default/info-app', 'id'=>$value['ezm_id']])]);
            $icon = Html::img(ModuleFunc::getNoIconModule(), ['width' => $iconSize, 'class' => 'img-rounded']);
            if (isset($value['ezm_icon']) && !empty($value['ezm_icon'])) {
                $icon = Html::img($value['icon_base_url'] . '/' . $value['ezm_icon'], ['width' => $iconSize, 'class' => 'img-rounded']);
            } 
            if($key>=$limit){
                $moreId[]=$value['module_id'];
                $moreItems[] = [
                    'label' => $icon.' '.$gname.' '.$info,
                    'url' => Url::to(['/ezmodules/ezmodule/view', 'id' => $module, 'addon' => $value['module_id']]),
                    'active' => $controllerID == 'ezmodule' && $actionID == 'view' && $addon == $value['module_id'],
                ];
            } else {
                $items[] = [
                    'label' => $icon.' '.$gname.' '.$info,
                    'url' => Url::to(['/ezmodules/ezmodule/view', 'id' => $module, 'addon' => $value['module_id']]),
                    'active' => $controllerID == 'ezmodule' && $actionID == 'view' && $addon == $value['module_id'],
                ];
            }
        }
        if(isset($moreItems) && !empty($moreItems)){
            $items[] = [
                'label' => Yii::t('ezmodule', 'More'),
                'url' => '#',
                'items' => $moreItems,
                'dropDownOptions' => ['id' => \appxq\sdii\utils\SDUtility::getMillisecTime()],
                'active' => $controllerID == 'ezmodule' && $actionID == 'view' && in_array($addon, $moreId),
            ];
        }
    }
    
    $items[] = [
        'label' => '<i class="glyphicon glyphicon-plus"></i>',
        'url' => Url::to(['/ezmodules/ezmodule-addon/save', 'module' => $module, 'user_module'=>$model['created_by']]),
        'linkOptions'=>['id'=>'add-module-list']
    ];
    
    if($addon>0){
        $ezm_builder = explode(',', $model['ezm_builder']);
        if ((Yii::$app->user->can('administrator')) || $owner==1) {
            $items[] = [
                'label' => '<i class="glyphicon glyphicon-trash"></i>',
                'url' => Url::to(['/ezmodules/ezmodule-addon/delete',  'id' => $addon, 'module' => $module]),
                'linkOptions'=>['id'=>'del-module-list' , 'class'=>'del-list',
                    'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to delete these items?'),
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
        'options' => ['class' => 'nav nav-tabs', 'id' => 'ezmodule_tab_modules'],
    ]);
    ?>
</div>