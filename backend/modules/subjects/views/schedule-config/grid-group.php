<?php

use yii\helpers\Html;
use yii\helpers\Url;
use backend\modules\ezforms2\classes\EzfAuthFuncManage;
use backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\subjects\classes\SubjectManagementQuery;
use backend\modules\subjects\classes\JKDate;
use appxq\sdii\helpers\SDNoty;
use yii\grid\GridView;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 
 */
$href = isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:'';

$columns = [
    [
        'class' => 'yii\grid\SerialColumn',
        'headerOptions' => ['style' => 'text-align: center','width'=>'50px'],
    ],
];
$url_action = Url::to(['/subjects/subject-management/schedule',
         'module_id' => $module_id,
         'widget_id' => $widget_id,
         'schedule_id' => $schedule_id,
         'procedure_id' => $procedure_id,
         'options' => $options,
         'user_create' => $user_create,
         'user_update' => $user_update,
         'reloadDiv' => $reloadDiv,
     ]);
$disabled = false;
if (isset($disabled) && !$disabled) {
    if (EzfAuthFuncManage::auth()->accessBtn($module_id)) {
        $columns[] = [
            'class' => 'appxq\sdii\widgets\ActionColumn',
            'headerOptions' => ['style' => 'text-align: center;','width'=>'150px'],
            'contentOptions' => ['style' => 'text-align: center;'],
            'template' => '{view} {update} {delete} ',
            'buttons' => [
                'view' => function ($url, $data, $key) use($group_ezf_id, $reloadDiv, $module_id) {
                    //if (backend\modules\ezforms2\classes\EzfUiFunc::showViewDataEzf($data, Yii::$app->user->id, $data['user_create'])) {

                    return \backend\modules\ezforms2\classes\EzfHelper::btn($group_ezf_id)
                                    ->reloadDiv($reloadDiv)
                                    ->modal('modal-ezform-main')
                                    ->label('<i class="fa fa-eye"></i>')
                                    ->options(['class' => 'btn btn-default btn-xs'])
                                    ->buildBtnView($data['id']);
                    //}
                },
                'update' => function ($url, $data, $key) use($group_ezf_id, $reloadDiv, $module_id) {
                    //if (EzfAuthFuncManage::auth()->accessBtn($module_id)) {
                    return \backend\modules\ezforms2\classes\EzfHelper::btn($group_ezf_id)
                                    ->reloadDiv($reloadDiv)
                                    ->modal('modal-ezform-main')
                                    ->label('<i class="fa fa-pencil"></i>')
                                    ->options(['class' => 'btn btn-primary btn-xs'])
                                    ->buildBtnEdit($data['id']);
                    //}
                },
                'delete' => function ($url, $data, $key) use($group_ezf_id, $reloadDiv, $module_id) {
                    //if (EzfAuthFuncManage::auth()->accessBtn($module_id)) {
                    return \backend\modules\ezforms2\classes\EzfHelper::btn($group_ezf_id)
                                    ->reloadDiv($reloadDiv)
                                    ->label('<i class="fa fa-trash"></i>')
                                    ->options(['class' => 'btn btn-danger btn-xs'])
                                    ->buildBtnDelete($data['id']);
                    //}
                },
            ],
        ];
    }
}

$columns[] = [
    'attribute' => "group_name",
    'header' => "Group Name",
    'format' => 'raw',
    'value' => function ($data) use($url_action) {
        return "<a class='btn-group-action' href='javascript:void(0)' data-group_name='".$data['group_name']."' data-dataid='".$data['id']."' data-url='".$url_action."' >".$data['group_name']."</a>";
    },
    'headerOptions' => ['style' => 'text-align: left;'],
    
];

if (isset($default_column) && $default_column) {
    $columns[] = [
        'attribute' => 'xsourcex',
        'format' => 'raw',
        'value' => function ($data) {
            return "<span class=\"label label-success\" data-toggle=\"tooltip\" title=\"{$data['sitename']}\">{$data['xsourcex']}</span>";
        },
        'headerOptions' => ['style' => 'text-align: center;'],
        'contentOptions' => ['style' => 'width:100px;text-align: center;'],
    ];
    $columns[] = [
        'attribute' => 'userby',
        'contentOptions' => ['style' => 'width:200px;'],
        'filter' => '',
    ];
    $columns[] = [
        'attribute' => 'rstat',
        'format' => 'raw',
        'value' => function ($data) {
            $alert = 'label-default';
            if ($data['rstat'] == 0) {
                $alert = 'label-info';
            } elseif ($data['rstat'] == 1) {
                $alert = 'label-warning';
            } elseif ($data['rstat'] == 2) {
                $alert = 'label-success';
            } elseif ($data['rstat'] == 3) {
                $alert = 'label-danger';
            }

            $rstat = backend\modules\core\classes\CoreFunc::itemAlias('rstat', $data['rstat']);
            return "<h4 style=\"margin: 0;\"><span class=\"label $alert\">$rstat</span></h4>";
        },
        'headerOptions' => ['style' => 'text-align: center;'],
        'contentOptions' => ['style' => 'width:120px;text-align: center;'],
        'filter' => Html::activeDropDownList($searchModel, 'rstat', backend\modules\core\classes\CoreFunc::itemAlias('rstat'), ['class' => 'form-control', 'prompt' => 'All']),
    ];
}
?>

<?php //\yii\widgets\Pjax::begin();  ?>
<div class="panel panel-primary">
    <div class="panel-heading">
        <?php

        if (EzfAuthFuncManage::auth()->accessBtn($module_id,1)) {
            echo \backend\modules\ezforms2\classes\EzfHelper::btn($group_ezf_id)
                    ->reloadDiv($reloadDiv)
                    ->label('<i class="fa fa-plus"></i> ' . Yii::t('subjects', 'Add Group'))
                    ->options(['class' => 'btn btn-success pull-left'])
                    ->buildBtnAdd();
        }
        ?>
        <label style="padding-top: 5px;padding-left: 10px;"> Group List</label>
    </div>
    <div class="panel-body">
        <?php
        echo GridView::widget([
            'id' => 'grid-group',
            'dataProvider' => $dataProvider,
            'columns' => $columns, // check the configuration for grid columns by clicking button above
        ]);
        ?>
    </div>
</div>

<?php //\yii\widgets\Pjax::end();  ?>
<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>

<script>
    $('#grid-group.pagination a').on('click', function () {
        getReloadDiv($(this).attr('href'), '<?= $reloadDiv ?>');
        return false;
    });
    
    $('#grid-group').on('dblclick', 'tbody tr', function() {  
        var btn_group = $(this).find('.btn-group-action');
        var url = btn_group.attr('data-url'); 
        var dataid = btn_group.attr('data-dataid');
        var group_name = btn_group.attr('data-group_name');
        
        var data = {dataid:dataid};
        window.history.replaceState({}, '', '<?= $href ?>&group_id='+dataid+"&group_name="+group_name);
        url = url+"&group_id="+dataid+"&group_name="+group_name;
        getReloadDiv(url, 'display-schedule');
         
    });
    $('.btn-group-action').on('click', function() {
        
        var url = $(this).attr('data-url'); 
        var dataid = $(this).attr('data-dataid');
        var group_name = $(this).attr('data-group_name');
        var data = {dataid:dataid};
        window.history.replaceState({}, '', '<?= $href ?>&group_id='+dataid+"&group_name="+group_name);
        url = url+"&group_id="+dataid+"&group_name="+group_name;
        getReloadDiv(url, 'display-schedule');
         
    });
</script>
<?php \richardfan\widget\JSRegister::end(); ?>
