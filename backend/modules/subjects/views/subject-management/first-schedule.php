<?php

use yii\helpers\Html;
use yii\helpers\Url;
use backend\modules\ezforms2\classes\EzfAuthFuncManage;
use backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\subjects\classes\JKDate;

$ezf_field = EzfQuery::getFieldByName($options['11111']['main_ezf_id'], 'type_visit');
$ezf_data = \appxq\sdii\utils\SDUtility::string2Array($ezf_field['ezf_field_data']);

$columns = [
    [
        'class' => 'yii\grid\SerialColumn',
        'headerOptions' => ['style' => 'text-align: center;'],
        'contentOptions' => ['style' => 'min-width:60px;width:60px;text-align: center;'],
    ],
];

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
    } else {
        $field = EzfQuery::getFieldByName($profile_ezf, $value);

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

foreach ($detail_column as $key => $value) {
    $headType = 'attribute';
    $label = $value;
    if ($value == 'type_visit') {
        $headType = 'label';
        $label = "Subject Status";
    }

    $columns[] = [
        $headType => $label,
        'format' => 'raw',
        'value' => function ($data) use($value, $ezf_data, $visitSchedule) {
            $explode = explode(' ', $data[$value]);
            if ($value == 'type_visit' && isset($ezf_data['items'][$data[$value]])) {
                return "<span class=\"label label-default\" data-toggle=\"tooltip\" title=\"{$ezf_data['items'][$data[$value]]}\">{$ezf_data['items'][$data[$value]]}</span>";
            } else if ($value == 'visit_name' || $value == 'next_visit_name') {
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

<br/>
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

<?=
\appxq\sdii\widgets\ModalForm::widget([
    'id' => 'modal-activity-detail',
    'size' => 'modal-xl',
]);
?>

<?=
\appxq\sdii\widgets\ModalForm::widget([
    'id' => 'modal-grid-detail',
    'size' => 'modal-xl',
]);
?>

<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);

//$project_id = "1520742111042203500";
?>

<script>
    $('.view-activity').click(function () {

        var showDetail = $('#modal-activity-detail');
        var data_id = $(this).attr('data-id');
        var url = '/subjects/open-activity/activity-detail?data_id=' + data_id + '&reloadDiv=display-activity-detail&modal=modal-grid-detail&subject_profile_ezf=<?= $subject_profile_ezf ?>&field_subject=<?= $field_subject ?>&schedule_id=<?= $schedule_id ?>&subject_detail_ezf=<?= $subject_detail_ezf ?>&profile_column=<?= base64_encode(json_encode($profile_column)) ?>&detail_column=<?= base64_encode(json_encode($detail_column)) ?>';
        showDetail.modal('show');
        showDetail.find('.modal-content').html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        showDetail.find('.modal-content').load(url);
    });

    $('#<?= $reloadDiv ?>-subject-grid .pagination a').on('click', function () {
        getReloadDiv($(this).attr('href'), '<?= $reloadDiv ?>');
        return false;
    });
</script>
<?php \richardfan\widget\JSRegister::end(); ?>
