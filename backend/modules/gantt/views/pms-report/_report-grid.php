<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use backend\modules\subjects\classes\SubjectManagementQuery;
use yii\helpers\ArrayHelper;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$gridColumns = [
    ['class' => 'yii\grid\SerialColumn', 'headerOptions' => ['width' => '50px',]],
    [
        'attribute' => 'task_name',
        'value' => function ($model, $key, $index, $widget) {
            return $model['task_name'];
        },
        'format' => 'raw',
        'headerOptions' => ['width' => '250px',]
    ],
    [
        'attribute' => 'start_date',
        'value' => function ($model, $key, $index, $widget) {
            return $model['start_date'];
        },
        'format' => 'raw',
        'headerOptions' => ['width' => '100px',]
    ],
    [
        'attribute' => 'end_date',
        'value' => function ($model, $key, $index, $widget) {
            return $model['end_date'];
        },
        'format' => 'raw',
        'headerOptions' => ['width' => '100px',]
    ],
    [
        'attribute' => 'Last Progress Date',
        'value' => function ($model, $key, $index, $widget) use ($ezformResponse) {
            //$data = SubjectManagementQuery::GetTableData($ezformResponse, ['target' => $model['id']], 'one');
            return isset($model['actual_date']) ? $model['actual_date'] : '';
        },
        'format' => 'raw',
        'headerOptions' => ['width' => '100px',]
    ],
    [
        'attribute' => 'Assignees',
        'value' => function ($model, $key, $index, $widget) use($ezformTask) {
            //$data = SubjectManagementQuery::GetTableData($ezformTask, ['id' => $model['dataid']], 'one');
            $person = appxq\sdii\utils\SDUtility::string2Array($model['assign_user']);
            $result = "";
            if ($person) {
                foreach ($person as $key => $val) {
                    $userData = \backend\modules\ezforms2\classes\EzfQuery::getUserProfile($val);
                    if ($result == "")
                        $result = $userData['firstname'] . ' ' . $userData['lastname'];
                    else
                        $result .= ',' . $userData['firstname'] . ' ' . $userData['lastname'];
                }
            }

            return $result;
        },
        'format' => 'raw',
        'headerOptions' => ['width' => '100px',]
    ],
    [
        'attribute' => '%Progress',
        'value' => function ($model, $key, $index, $widget)use ($ezformResponse) {
            //$data = SubjectManagementQuery::GetTableData($ezformResponse, ['target' => $model['id']], 'one');
            $progress_style = "progress-bar-info";
            if ($model['progress'] < 25) {
                $progress_style = "progress-bar-danger";
            } else if ($model['progress'] >= 25 && $model['progress'] < 50) {
                $progress_style = "progress-bar-warning";
            } else if ($model['progress'] == 100) {
                $progress_style = "progress-bar-success";
            }
            $progress = 0;
            if (isset($model['progress']) && $model['progress'] != '')
                $progress = number_format($model['progress'], 1);
            else
                $progress = isset($model['progress']) ? $model['progress'] : 0;

            if ($progress == '')
                $progress = 0;
            $html = '<div class="col-md-12 "><div class="progress">
                    <div class="progress-bar ' . $progress_style . '" role="progressbar" aria-valuenow="' . $model['progress'] . '"
                         aria-valuemin="0" aria-valuemax="100" style="width:' . $model['progress'] . '%">
                    </div>
                    
                </div><label style="position: absolute;top:0px;margin-left: 40%;">' . $progress . ' % </label></div>';
            return $html;
        },
        'format' => 'raw',
        'headerOptions' => ['width' => '150px',]
    ],
];
?>
<div class="panel panel-info">
    <div class="panel-heading">
        <h4>Summary Report by Task Items</h4>
    </div>
    <div class="panel-body">
        <div class="col-md-6 sdbox-col">
            <?= Html::label('Sub-task filter:', '')?>
            <?=
            kartik\select2\Select2::widget([
                'name' => 'subtask_filter',
                'value' => $sub_filter,
                'options' => ['placeholder' => Yii::t('ezmodule', 'Sub-task select...'), 'id' => 'config_subtask_filter'],
                'data' => ArrayHelper::map($subtask_list, 'id', 'task_name'),
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
        </div>
        <div class="clearfix"></div>
        <?php
        echo GridView::widget([
            'id' => 'pms-grid-report',
            'dataProvider' => $dataProvider,
            'columns' => $gridColumns, // check the configuration for grid columns by clicking button above
        ]);
        ?>
    </div>
</div>
<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>

<script>
    $('#pms-grid-report .pagination a').on('click', function () {
        getReloadDiv($(this).attr('href'), '<?= $reloadDiv ?>');
        return false;
    });

    $('#pms-grid-report table tr th a').on('click', function () {
        getReloadDiv($(this).attr('href'), '<?= $reloadDiv ?>');
        return false;
    });
    
    $('#config_subtask_filter').on('change',function(){
        var sub_filter = $(this).val();
        var url = $('#<?= $reloadDiv ?>').attr('data-url')+"&sub_filter="+sub_filter;

        $.get(url,function(result){
            $('#<?= $reloadDiv ?>').empty();
            $('#<?= $reloadDiv ?>').html(result);
        });
        return false;
    });
</script>
<?php \richardfan\widget\JSRegister::end(); ?>