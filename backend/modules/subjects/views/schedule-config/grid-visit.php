<?php

use yii\helpers\Html;
use yii\helpers\Url;
use backend\modules\ezforms2\classes\EzfAuthFuncManage;
use backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\subjects\classes\SubjectManagementQuery;
use backend\modules\subjects\classes\JKDate;
use appxq\sdii\helpers\SDNoty;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 
 */

$subject_profile_ezf = $options['subject_ezf_id'];
$visitSchedule = SubjectManagementQuery::getVisitScheduleByWidget($schedule_id);

$columns = [
    [
        'class' => 'yii\grid\SerialColumn',
        'headerOptions' => ['style' => 'text-align: center;'],
        'contentOptions' => ['style' => 'min-width:60px;width:60px;text-align: center;'],
    ],
];

$disabled = false;
if (isset($disabled) && !$disabled) {
    if (EzfAuthFuncManage::auth()->accessBtn($module_id)) {
        $columns[] = [
            'class' => 'appxq\sdii\widgets\ActionColumn',
            'contentOptions' => ['style' => 'width:110px;min-width:110px;text-align: center;'],
            'template' => '{view} {update} {delete} ',
            'buttons' => [
                'view' => function ($url, $data, $key) use($schedule_id, $visitSchedule) {
                    if ($data['id'] == '11111' || $data['id'] == '22222') {
                        return Html::button("<i class='fa fa-eye'></i>", ['class' => 'btn btn-default btn-xs btn-add-visit', 'data-data_id' => $data['id'], 'data-widget_id' => $schedule_id]);
                    } else {
                        return Html::button("<i class='fa fa-eye'></i>", ['class' => 'btn btn-default btn-xs btn-view-form', 'data-data_id' => $data['id'], 'data-widget_id' => $schedule_id]);
                    }
                    //}
                },
                'update' => function ($url, $data, $key)use($schedule_id) {
                    //if (EzfAuthFuncManage::auth()->accessBtn($module_id)) {
                    return Html::button("<i class='fa fa-pencil'></i>", ['class' => 'btn btn-primary btn-xs btn-edit-form', 'data-data_id' => $data['id'], 'data-widget_id' => $schedule_id]);
                    //}
                },
                'delete' => function ($url, $data, $key)use($schedule_id) {
                    //if (EzfAuthFuncManage::auth()->accessBtn($module_id)) {
                    return Html::button("<i class='fa fa-trash'></i>", ['class' => 'btn btn-danger btn-xs btn-delete-form', 'data-data_id' => $data['id'], 'data-widget_id' => $schedule_id]);
                    //}
                },
            ],
        ];
    }
}

$columns[] = [
    'attribute' => "visit_name",
    'header' => "Visit Name",
    'format' => 'raw',
    'value' => function ($data) {
        return $data['visit_name'];
    },
    'headerOptions' => ['style' => 'text-align: center;'],
    'contentOptions' => ['style' => 'width:100px;text-align: center;'],
];

$columns[] = [
    'attribute' => "group_name",
    'header' => "Group Name",
    'format' => 'raw',
    'value' => function ($data)use($group_ezf_id) {
        $ezform_group = EzfQuery::getEzformOne($group_ezf_id);
        return SubjectManagementQuery::GetTableData($ezform_group,['id'=>$data['group_name']],'one')['group_name'] ;
    },
    'headerOptions' => ['style' => 'text-align: center;'],
    'contentOptions' => ['style' => 'width:100px;text-align: center;'],
];

$columns[] = [
    'attribute' => "visit_name_mapping",
    'header' => "The Index Visit",
    'format' => 'raw',
    'value' => function ($data) use($visitSchedule) {
        if ($data['id'] == '11111' || $data['id'] == '22222') {
            if ($data['id'] == '22222')
                return $visitSchedule['11111']['visit_name'];
            else
                return "";
        }else {
            return $visitSchedule[$data['visit_cal_date']]['visit_name'];
        }
    },
    'headerOptions' => ['style' => 'text-align: center;'],
    'contentOptions' => ['style' => 'width:100px;text-align: center;'],
];

$columns[] = [
    'attribute' => "visit_field",
    'header' => "Index Visit Field",
    'format' => 'raw',
    'value' => function ($data)use($visitSchedule) {
        if ($data['id'] == '22222') {
            return "Actual Date";
        } else {
            if (isset($visitSchedule[$data['id']]['field_cal_date']) && $visitSchedule[$data['id']]['field_cal_date'] == 'actual_date') {
                return "Actual Date";
            } elseif (isset($visitSchedule[$data['id']]['field_cal_date']) && $visitSchedule[$data['id']]['field_cal_date'] == 'plan_date') {
                return "Plan Date";
            }
        }
    },
    'headerOptions' => ['style' => 'text-align: center;'],
    'contentOptions' => ['style' => 'width:100px;text-align: center;'],
];
$columns[] = [
    'attribute' => "plan_date",
    'header' => "Plan Date of The Visit",
    'format' => 'raw',
    'value' => function ($data) {
        return isset($data['plan_date']) ? $data['plan_date'] : '';
    },
    'headerOptions' => ['style' => 'text-align: center;'],
    'contentOptions' => ['style' => 'width:100px;text-align: center;'],
];

$columns[] = [
    'attribute' => "earliest_date",
    'header' => "Earliest Visit",
    'format' => 'raw',
    'value' => function ($data) {
        return isset($data['earliest_date']) ? $data['earliest_date'] : '';
    },
    'headerOptions' => ['style' => 'text-align: center;'],
    'contentOptions' => ['style' => 'width:100px;text-align: center;'],
];

$columns[] = [
    'attribute' => "latest_date",
    'header' => "Latest Visit",
    'format' => 'raw',
    'value' => function ($data) {
        return isset($data['latest_date']) ? $data['latest_date'] : '';
    },
    'headerOptions' => ['style' => 'text-align: center;'],
    'contentOptions' => ['style' => 'width:100px;text-align: center;'],
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


<br/><br/>
<?php //\yii\widgets\Pjax::begin();    ?>

<div class="panel panel-primary">
    <div class="panel-heading">
        <label style="padding-bottom: 10px;">Visit Config</label>
        <?php
        if (EzfAuthFuncManage::auth()->accessBtn($module_id)) {
            echo \backend\modules\ezforms2\classes\EzfHelper::btn($subject_profile_ezf)
                    ->reloadDiv($reloadDiv)
                    ->label('<i class="fa fa-plus"></i> ' . Yii::t('subjects', 'Add Visit'))
                    ->modal('modal-activity-form')
                    ->options(['class' => 'btn btn-success pull-right btn-add-form'])
                    ->buildBtnAdd();
        }
        ?>
    </div>
    <div class="panel-body">
        <div class="table-responsive">
            <?=
            yii\grid\GridView::widget([
                'id' => "$reloadDiv-subject-grid",
                'dataProvider' => $dataProvider,
                'columns' => $columns,
            ]);
            ?>
        </div>
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
    $('#<?= $reloadDiv ?>-subject-grid .pagination a').on('click', function () {
        getReloadDiv($(this).attr('href'), '<?= $reloadDiv ?>');
        return false;
    });

    $('.btn-add-form').click(function () {
        var widget = $(this).attr('data-widget_id');
        var url = '<?=
yii\helpers\Url::to([
    '/subjects/subject-management/config-view',
    'reloadDiv' => $reloadDiv,
    'options' => $options,
    'widget_id' => $schedule_id,
])
?>';

        $('#modal-ezform-config .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
        $('#modal-ezform-config').modal('show')
                .find('.modal-content')
                .load(url);
    })

    $('.btn-edit-form').click(function () {
        var data_id = $(this).attr('data-data_id');
        var widget = $(this).attr('data-widget_id');
        var url = '<?=
yii\helpers\Url::to([
    '/subjects/subject-management/config-view',
    'reloadDiv' => $reloadDiv,
    'options' => $options,
    'widget_id' => $schedule_id,
])
?>';
        url += '&widget_id=' + widget + '&data_id=' + data_id;

        $('#modal-ezform-config .modal-content').html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        $('#modal-ezform-config').modal('show')
                .find('.modal-content')
                .load(url);
    });

    $('.btn-view-form').click(function () {
        var data_id = $(this).attr('data-data_id');
        var widget = $(this).attr('data-widget_id');
        var url = '<?=
yii\helpers\Url::to([
    '/subjects/subject-management/config-view',
    'reloadDiv' => $reloadDiv,
    'options' => $options,
    'widget_id' => $schedule_id,
])
?>';
        url += '&widget_id=' + widget + '&data_id=' + data_id + '&action=view';

        $('#modal-ezform-config .modal-content').html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        $('#modal-ezform-config').modal('show')
                .find('.modal-content')
                .load(url);
    });

    $('.btn-edit-form').click(function () {
        var data_id = $(this).attr('data-data_id');
        var widget = $(this).attr('data-widget_id');
        var url = '<?=
yii\helpers\Url::to([
    '/subjects/subject-management/config-view',
    'reloadDiv' => $reloadDiv,
    'options' => $options,
    'widget_id' => $schedule_id,
])
?>';
        url += '&widget_id=' + widget + '&data_id=' + data_id;

        $('#modal-ezform-config .modal-content').html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        $('#modal-ezform-config').modal('show')
                .find('.modal-content')
                .load(url);
    });


    $('.btn-delete-form').click(function () {
        var data_id = $(this).attr('data-data_id');
        var url = '<?=
yii\helpers\Url::to([
    '/subjects/subject-management/delete-visit',
    'reloadDiv' => $reloadDiv,
    'widget_id' => $schedule_id,
])
?>';
        yii.confirm('<?= Yii::t('app', 'Are you sure you want to delete this item?') ?>', function () {

            $.get(url, {data_id: data_id}
            ).done(function (result) {
                if (result.status == 'success') {
                    var url = $('#<?= $reloadDiv ?>').attr('data-url');
                    getReloadDiv(url, '<?= $reloadDiv ?>');
                } else {
<?= SDNoty::show('result.message', 'result.status') ?>
                }
            }).fail(function () {
<?= SDNoty::show("'" . "Server Error'", '"error"') ?>
            });
        });
    });

    $('.btn-delete-visit').click(function () {
        var visit_id = $(this).attr('data-data_id');
        var widget_id = '<?= $widget_id ?>';
        var enable_visit = '0';
        var url_ena = "/subjects/subject-management/enabled-visit";
        var data = {visit_id: visit_id, widget_id: widget_id, enable_visit: enable_visit};
        var url = $('#<?= $reloadDiv ?>').attr('data-url');
        yii.confirm('<?= Yii::t('app', 'Are you sure you want to delete this item?') ?>', function () {
            $.get(url_ena, data, function (result) {
                if (result.status == 'success') {
<?= SDNoty::show('result.message', 'result.status') ?>
                    getReloadDiv(url, '<?= $reloadDiv ?>');
                } else {
<?= SDNoty::show("'" . "Server Error'", '"error"') ?>
                }
            });

        });

    });
</script>
<?php \richardfan\widget\JSRegister::end(); ?>
