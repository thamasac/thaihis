<?php

use yii\helpers\Html;
use yii\helpers\Url;
use backend\modules\ezmodules\models\EzmoduleMenu;
use appxq\sdii\widgets\ModalForm;

$userId = Yii::$app->user->id;

?>

<div id="ezmodule-menu-item">
    <?php
    $ezm_builder = explode(',', $model['ezm_builder']);
    if ((Yii::$app->user->can('administrator')) || $model['created_by'] == $userId || in_array($userId, $ezm_builder)) {
        if (isset($menu) && $menu > 0) {

            echo Html::a('', ['/ezmodules/ezmodule-menu/delete',  'id' => $menu, 'module' => $module], ['class' => 'btn-menu-del fa fa-trash-o fa-2x pull-right underline',
                'data-toggle' => 'tooltip',
                'title' => Yii::t('app', 'Delete'),
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to delete these items?'),
                    'method' => 'post',
                ],
                'style' => 'margin-bottom: 15px;',
            ]); //'data-confirm'=>'คุณแน่ใจที่จะต้องการลบข้อมูลนี้หรือไม่?'
            echo Html::a('', ['/ezmodules/ezmodule-menu/save', 'id' => $menu, 'module' => $module], ['class' => 'btn-menu fa fa-pencil-square-o fa-2x pull-right underline',
                'data-toggle' => 'tooltip',
                'style' => 'margin-bottom: 15px;',
                'title' => Yii::t('app', 'Update')]);
        }

        echo Html::a('', ['/ezmodules/ezmodule-menu/save', 'module' => $module], ['class' => 'btn-menu fa fa-plus fa-2x pull-right underline',
            'data-toggle' => 'tooltip',
            'style' => 'margin-bottom: 15px;',
            'title' => Yii::t('ezmodule', 'New Menu')
        ]);
    }

    $items = [
        [
            'label' => '<i class="fa fa-tasks" aria-hidden="true"></i> '.Yii::t('ezmodule', 'Workbench'),
            'url' => Url::to(["/ezmodules/ezmodule/view", 'id' => $module]),
            'active' => $controllerID == 'ezmodule' && $actionID == 'view',
        ],
        [
            'label' => '<i class="fa fa-area-chart" aria-hidden="true"></i> '.Yii::t('ezmodule', 'Report'),
            'url' => Url::to(['/ezmodules/ezmodule/report', 'id' => $module]),
            'active' => $controllerID == 'ezmodule' && $actionID == 'report',
        ],
    ];

    $modelMenu = EzmoduleMenu::find()->where(['ezm_id' => $module, 'menu_parent' => 0, 'menu_active'=>1])->orderBy('menu_order')->all();
    if ($modelMenu) {
        foreach ($modelMenu as $key => $value) {
            $modelSubmenu = EzmoduleMenu::find()->where(['ezm_id' => $module, 'menu_parent' => $value['menu_id'], 'menu_active'=>1])->orderBy('menu_order')->all();
            if ($modelSubmenu) {
                $subItems = [];
                $subId = [];
                foreach ($modelSubmenu as $subKey => $subValue) {
                    $subItems[] = [
                        'label' => $subValue['menu_name'],
                        'url' => Url::to(['/ezmodules/ezmodule/menu', 'module' => $module, 'id' => $subValue['menu_id']]),
                        'active' => $controllerID == 'ezmodule' && $actionID == 'menu' && $menu == $subValue['menu_id'],
                    ];
                    $subId[] = $subValue['menu_id'];
                }
                
                $items[] = [
                    'label' => $value['menu_name'],
                    'url' => '#',
                    'items' => $subItems,
                    'dropDownOptions' => ['id' => \appxq\sdii\utils\SDUtility::getMillisecTime()],
                    'active' => $controllerID == 'ezmodule' && $actionID == 'menu' && in_array($menu, $subId),
                ];
            } else {
                $items[] = [
                    'label' => $value['menu_name'],
                    'url' => Url::to(['/ezmodules/ezmodule/menu', 'module' => $module, 'id' => $value['menu_id']]),
                    'active' => $controllerID == 'ezmodule' && $actionID == 'menu' && $menu == $value['menu_id'],
                ];
            }
        }
    }
    ?>

    <?=
    \yii\bootstrap\Nav::widget([
        'id'=>\appxq\sdii\utils\SDUtility::getMillisecTime(),
        'items' => $items,
        'encodeLabels'=>false,
        'options' => ['class' => 'nav nav-pills', 'id' => 'ezmodule_tab_menu'],
    ]);
    if($menu>0){
    ?>
    
    <div class="modal-body" >
        <?php
        $modelMenu = EzmoduleMenu::find()->where('menu_id=:id AND ezm_id=:ezm_id AND menu_active=1', [':id'=>$menu, ':ezm_id'=>$module])->one();
        ?>
        <?php
        if(isset($modelMenu->menu_content)){
            echo $modelMenu->menu_content;
        }
        ?>
    </div>
    <?php } ?>
</div>
