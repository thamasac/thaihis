<?php

use yii\helpers\Html;
use yii\helpers\Url;
use backend\modules\ezforms2\classes\EzfAuthFuncManage;
use backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\subjects\classes\SubjectManagementQuery;
use backend\modules\subjects\classes\JKDate;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 
 */
$ezf_field = EzfQuery::getFieldByName($detail_ezf, 'type_visit');
$ezf_data = \appxq\sdii\utils\SDUtility::string2Array($ezf_field['ezf_field_data']);

$columns = [
    [
        'class' => 'yii\grid\SerialColumn',
        'headerOptions' => ['style' => 'text-align: center;'],
        'contentOptions' => ['style' => 'min-width:60px;width:60px;text-align: center;'],
    ],
];

$disabled = false;
if (isset($disabled) && !$disabled) {
    if (EzfAuthFuncManage::auth()->accessManage($module_id, 1)) {
        $columns[] = [
            'class' => 'appxq\sdii\widgets\ActionColumn',
            'contentOptions' => ['style' => 'width:110px;min-width:110px;text-align: center;'],
            'template' => '{view} {update} {delete} ',
            'buttons' => [
                'view' => function ($url, $data, $key) use($subject_profile_ezf, $reloadDiv, $module_id) {
                    //if (backend\modules\ezforms2\classes\EzfUiFunc::showViewDataEzf($data, Yii::$app->user->id, $data['user_create'])) {

                    return \backend\modules\ezforms2\classes\EzfHelper::btn($subject_profile_ezf)
                                    ->reloadDiv($reloadDiv)
                                    ->modal('modal-activity-form')
                                    ->label('<i class="fa fa-eye"></i>')
                                    ->options(['class' => 'btn btn-default btn-xs'])
                                    ->buildBtnView($data['id']);
                    //}
                },
                'update' => function ($url, $data, $key) use($subject_profile_ezf, $reloadDiv, $module_id) {
                    //if (EzfAuthFuncManage::auth()->accessBtn($module_id)) {
                    return \backend\modules\ezforms2\classes\EzfHelper::btn($subject_profile_ezf)
                                    ->reloadDiv($reloadDiv)
                                    ->modal('modal-activity-form')
                                    ->label('<i class="fa fa-pencil"></i>')
                                    ->options(['class' => 'btn btn-primary btn-xs'])
                                    ->buildBtnEdit($data['id']);
                    //}
                },
                'delete' => function ($url, $data, $key) use($subject_profile_ezf, $reloadDiv, $module_id) {
                    //if (EzfAuthFuncManage::auth()->accessBtn($module_id)) {
                    return \backend\modules\ezforms2\classes\EzfHelper::btn($subject_profile_ezf)
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
foreach ($profile_column as $key => $value) {
    if ($value == $field_subject) {
        $field = EzfQuery::getFieldByName($profile_ezf, $value);

        $columns[] = [
            'attribute' => $value,
            'header' => $field['ezf_field_label'],
            'format' => 'raw',
            'value' => function ($data) use($value) {
                return "<a href='javascript:void(0)' class='view-activity' data-inform_date='{$data['inform_date']}' data-id='{$data['id']}'>{$data[$value]}</a>";
            },
            'headerOptions' => ['style' => 'text-align: center;'],
            'contentOptions' => ['style' => 'width:100px;text-align: center;'],
        ];
        $columns[] = [
            'attribute' => 'subject_no',
            'header' => 'Subject Number',
            'format' => 'raw',
            'value' => function ($data) use($value, $detail_ezf) {
                $ezform = EzfQuery::getEzformOne($detail_ezf);
                $subject = SubjectManagementQuery::GetTableData($ezform, " target='{$data['id']}' AND IFNULL(subject_no,'')<>'' ", 'one');
                return isset($subject['subject_no']) ? $subject['subject_no'] : '';
            },
            'headerOptions' => ['style' => 'text-align: center;'],
            'contentOptions' => ['style' => 'width:100px;text-align: center;'],
        ];
    } else {
        $field = EzfQuery::getFieldByName($profile_ezf, $value);
        $modelField = EzfQuery::getFieldByName($profile_ezf, $value);
        if ($value == "gender") {
            $columns[] = [
                'attribute' => $value,
                'header' => $field['ezf_field_label'],
                'format' => 'raw',
                'value' => function ($data) use($value,$modelField) {
                    
                    if($data[$value] == '1' || $data[$value] == '2'){
                        $fieldData = appxq\sdii\utils\SDUtility::string2Array($modelField['ezf_field_data']);
                        return $fieldData['items']['data'][$data[$value]];
                    }else{
                        return $data[$value];
                    }
                },
                'headerOptions' => ['style' => 'text-align: center;'],
                'contentOptions' => ['style' => 'width:100px;text-align: center;'],
            ];
        } else {
            $columns[] = [
                'attribute' => $value,
                'header' => $field['ezf_field_label'],
                'format' => 'raw',
                'value' => function ($data) use($value) {
                    if (JKDate::checkFormatDate($data[$value])) {
                        $fnVal = JKDate::convertDate($data[$value]);
                        return "<span >{$fnVal}</span>";
                    } else {
                        return "<span data-toggle=\"tooltip\" title=\"{$data[$value]}\">{$data[$value]}</span>";
                    }
                },
                'headerOptions' => ['style' => 'text-align: center;'],
                'contentOptions' => ['style' => 'width:100px;text-align: center;'],
            ];
        }
    }
}

foreach ($detail_column as $key => $value) {
    if ($value == 'subject_no')
        continue;
    $field = EzfQuery::getFieldByName($detail_ezf, $value);
    $headName = $field['ezf_field_label'];
    if ($value == 'visit_name') {
        $headName = "Latest Visit Name";
    }
    if ($value == 'type_visit') {
        $headType = 'label';
        $label = "Subject Status";
    }

    $columns[] = [
        'attribute' => $value,
        'header' => $headName,
        'format' => 'raw',
        'value' => function ($data) use($value, $ezf_data, $visitSchedule) {
            $explode = explode(' ', $data[$value]);
            if ($value == 'type_visit' && isset($ezf_data['items'][$data[$value]])) {
                return "<span class=\"label label-default\" data-toggle=\"tooltip\" title=\"{$ezf_data['items'][$data[$value]]}\">{$ezf_data['items'][$data[$value]]}</span>";
            } elseif ($value == 'visit_name' || $value == 'next_visit_name') {
                if (isset($visitSchedule[$data[$value]]))
                    return "<span class=\"label label-success\" data-toggle=\"tooltip\" title=\"{$visitSchedule[$data[$value]]['visit_name']}\">{$visitSchedule[$data[$value]]['visit_name']}</span>";
                else
                    return '';
            }elseif (isset($data[$value]) && JKDate::verifyDateTime($data[$value])) {
                $fnVal = JKDate::convertDateTime($data[$value]);
                return "<span>{$fnVal}</span>";
            } elseif (isset($data[$value]) && JKDate::checkFormatDate($explode[0])) {
                $fnVal = JKDate::convertDate(date($data[$value]));
                return "<span>{$fnVal}</span>";
            } elseif (isset($data[$value])) {
                return "<span class=\"label label-success\" data-toggle=\"tooltip\" title=\"{$data[$value]}\">{$data[$value]}</span>";
            } else {
                return '';
            }
        },
        'headerOptions' => ['style' => 'text-align: center;'],
        'contentOptions' => ['style' => 'width:90px;text-align: center;'],
    ];
}

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
<div class="form-group">
    <?php
    if (EzfAuthFuncManage::auth()->accessManage($module_id, 1)) {
        echo \backend\modules\ezforms2\classes\EzfHelper::btn($subject_profile_ezf)
                ->reloadDiv($reloadDiv)
                ->label('<i class="fa fa-plus"></i> ' . Yii::t('subjects', 'New Subject'))
                ->modal('modal-activity-form')
                ->options(['class' => 'btn btn-success pull-left'])
                ->buildBtnAdd();

        echo Html::button('<i class="fa fa-download"></i> Export data', ['class' => 'btn btn-info btn_export_data', 'style' => 'margin-left:10px;']);
    }
    ?>
</div>
<?php //\yii\widgets\Pjax::begin(); ?>
<?=
yii\grid\GridView::widget([
    'id' => "$reloadDiv-subject-grid",
    'dataProvider' => $dataProvider,
    'filterModel' => isset($searchModel) ? $searchModel : null,
    'columns' => $columns,
]);
?>
<?php //\yii\widgets\Pjax::end(); ?>
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

    $('.btn_export_data').click(function () {

        var url = '<?=
Url::to(['/subjects/open-activity/export-open-activity',
    'reloadDiv' => $reloadDiv,
    'module_id' => $module_id,
    'subject_profile_ezf' => $subject_profile_ezf,
    'subject_detail_ezf' => $subject_detail_ezf,
    'profile_column' => $profile_column,
    'detail_column' => $detail_column,
    'detail_column2' => $detail_column2,
    'field_subject' => $field_subject,
    'schedule_id' => $schedule_id,
    'profile_ezf' => $subject_profile_ezf,
    'detail_ezf' => $subject_detail_ezf,
    'modal' => $modal,
    'export' => true,
]);
?>';
        $.get(url, {export: true}, function (result) {
            var data = JSON.parse(result);
<?= \appxq\sdii\helpers\SDNoty::show('data.message', 'data.status') ?>;
            $('#modal-export .modal-content').html(data.html);
            $('#modal-export').modal('hide');
        });
    });
</script>
<?php \richardfan\widget\JSRegister::end(); ?>
