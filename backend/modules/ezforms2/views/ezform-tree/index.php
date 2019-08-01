<?php

use yii\helpers\Url;
use backend\modules\ezforms2\models\EzformTree;

$this->title = Yii::t('ezform', 'Form Category');
$this->params['breadcrumbs'][] = ['label' => Yii::t('ezform', 'EzForm'), 'url' => ['/ezforms2/ezform/index']];
$this->params['breadcrumbs'][] = $this->title;


if (EzformTree::find()->count() == 0) {
    $home = new EzformTree(['name' => 'Sharing within site', 'readonly' => '1', 'userid' => 1]);
    $home->makeRoot();
    $home = new EzformTree(['name' => 'CRF Example', 'readonly' => '1', 'userid' => 1]);
    $home->makeRoot();
    $home = new EzformTree(['name' => 'EzForm Example', 'readonly' => '1', 'userid' => 1]);
    $home->makeRoot();
    $home = new EzformTree(['name' => 'Templates', 'readonly' => '1', 'userid' => 1]);
    $home->makeRoot();
    
}
?>

<div class="box" style="margin-bottom: 15px;">
    <div class="box-body">
        <?php
        echo \kartik\tree\TreeView::widget([
            'query' => EzformTree::find()->where('readonly=1 or userid=' . Yii::$app->user->id . ' or id IN (select distinct root from ezform_tree where userid=' . Yii::$app->user->id . ')')->addOrderBy('root, lft'),
            'headingOptions' => ['label' => Yii::t('ezform', 'Form Category')],
            'rootOptions' => ['label' => '<span class="text-primary">'.Yii::t('ezform', 'Root').'</span>'],
            'fontAwesome' => true,
            'isAdmin' => false,
            'displayValue' => 1,
            'rootOptions' => [
                'label' => '<i class="fa fa-home"></i> '.Yii::t('ezform', 'Root'), // custom root label
                'class' => 'text-success'
            ],
            'iconEditSettings' => [
                'show' => 'list',
                'listData' => [
                    'institution' => Yii::t('ezform', 'Project'),
                    'user'=> Yii::t('ezform', 'User'),
//            'newspaper-o' => 'ฟอร์มบันทึกข้อมูล',
                ]
            ],
            'softDelete' => true,
            'cacheSettings' => ['enableCache' => true],
            'nodeAddlViews' => [
                kartik\tree\Module::VIEW_PART_2 => '@backend/modules/ezforms2/views/ezform-tree/extra',
//        kartik\tree\Module::VIEW_PART_1 => '@backend/views/tbl-tree/addnew',
            ],
            'nodeActions' => [
                kartik\tree\Module::NODE_MOVE => Url::to(['/treemanager/node/movex']),
            ],
//    'toolbar'  => [
//        TreeView::BTN_CREATE => [
//            'icon' => 'plus',
//            'options' => ['title' => Yii::t('kvtree', 'Add new'), 'disabled' => true]
//        ],
//        TreeView::BTN_CREATE_ROOT => [
//            'icon' => 'tree',
//            'options' => ['title' => Yii::t('kvtree', 'Add new root')]
//        ],
//        TreeView::BTN_REMOVE => [
//            'icon' => 'trash',
//            'options' => ['title' => Yii::t('kvtree', 'Delete'), 'disabled' => true]
//        ],
//        TreeView::BTN_SEPARATOR,
//        TreeView::BTN_MOVE_UP => [
//            'icon' => 'arrow-up',
//            'options' => ['title' => Yii::t('kvtree', 'Move Up'), 'disabled' => true]
//        ],
//        TreeView::BTN_MOVE_DOWN => [
//            'icon' => 'arrow-down',
//            'options' => ['title' => Yii::t('kvtree', 'Move Down'), 'disabled' => true]
//        ],
//        TreeView::BTN_MOVE_LEFT => [
//            'icon' => 'arrow-left',
//            'options' => ['title' => Yii::t('kvtree', 'Move Left'), 'disabled' => true]
//        ],
//        TreeView::BTN_MOVE_RIGHT => [
//            'icon' => 'arrow-right',
//            'options' => ['title' => Yii::t('kvtree', 'Move Right'), 'disabled' => true]
//        ],
//        TreeView::BTN_SEPARATOR,
//        TreeView::BTN_REFRESH => [
//            'icon' => 'refresh',
//            'options' => ['title' => Yii::t('kvtree', 'Refresh')],
//            'url' => Yii::$app->request->url
//        ],
//    ]
        ]);

        if (!Yii::$app->user->can('administrator')) {
            $this->registerJs("
        $('.kv-move-up').hide();
        $('.kv-move-down').hide();
        $('.kv-move-left').hide();
        $('.kv-move-right').hide();
        $('.kv-create-root').hide();
    ");
        }
        ?>
    </div></div>