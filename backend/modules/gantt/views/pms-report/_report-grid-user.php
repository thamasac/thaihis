<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use backend\modules\subjects\classes\SubjectManagementQuery;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$gridColumns = [
    [
        'class' => 'yii\grid\SerialColumn',
        'headerOptions' => [
            'width' => '70px'
        ]
    ],
    [
        'header' => '#',
        'value' => function ($model, $key, $index, $widget) {
            $image_user = Html::img(Yii::getAlias('@storageUrl/images/nouser.png'), [
                        'class' => 'media-object img-rounded',
                        'style' => 'width: 30px;'
                    ]);
            if (isset($model ['avatar_path']) && !empty($model ['avatar_path'])) {
                $image_user = Html::img($model ['avatar_base_url'] . '/' . $model ['avatar_path'], [
                            'class' => 'media-object img-rounded',
                            'style' => 'width: 30px;'
                        ]);
            }

            return $image_user;
        },
        'format' => 'raw',
        'headerOptions' => [
            'width' => '50px'
        ]
    ],
    [
        'attribute' => 'fullname',
        // 'header' => 'Name',
        'value' => function ($model, $key, $index, $widget) {
            return $model ['fullname'];
        },
        'format' => 'raw',
        'headerOptions' => [
            'width' => '50px'
        ]
    ],
    [
        'attribute' => 'credit_points',
        'value' => function ($model, $key, $index, $widget) use ($ezformResponse) {
            return "<label style='color:blue;'>" . (isset($model ['credit_points']) ? $model ['credit_points'] : 0) . "</label>";
        },
        'format' => 'raw',
        'headerOptions' => [
            'width' => '50px'
        ]
    ],
    [
        'attribute' => 'reward_points',
        'value' => function ($model, $key, $index, $widget) use ($ezformResponse) {
            return "<label style='color:blue;'>" . (isset($model ['reward_points']) ? $model ['reward_points'] : 0) . "</label>";
        },
        'format' => 'raw',
        'headerOptions' => [
            'width' => '100px'
        ]
    ],
    [
        'attribute' => 'assigned_task',
        'value' => function ($model, $key, $index, $widget) use ($ezformResponse) {
            return "<label style='color:blue;'>" . (isset($model ['assigned_task'])?$model ['assigned_task']:'0') . "</label>";
        },
        'format' => 'raw',
        'headerOptions' => [
            'width' => '100px'
        ]
    ],
                [
        'header' => 'Review Task',
        'value' => function ($model, $key, $index, $widget) use ($ezformResponse) {
            return "<label style='color:blue;'>" . (isset($model ['review_amt'])?$model ['review_amt']:'0') . "</label>";
        },
        'format' => 'raw',
        'headerOptions' => [
            'width' => '100px'
        ]
    ],
                [
        'header' => 'Approved Task',
        'value' => function ($model, $key, $index, $widget) use ($ezformResponse) {
            return "<label style='color:blue;'>" . (isset($model ['approved_amt'])?$model ['approved_amt']:'0') . "</label>";
        },
        'format' => 'raw',
        'headerOptions' => [
            'width' => '100px'
        ]
    ],
    [
        'header' => 'Overdue',
        'value' => function ($model, $key, $index, $widget) {
            return "<label style='color:red;'>" . $model ['task_overdue'] . "</label>";
        },
        'format' => 'raw',
        'headerOptions' => [
            'width' => '100px'
        ]
    ],
    [
        'header' => 'Task Completed',
        'value' => function ($model, $key, $index, $widget) {
            return "<label style='color:green;'>" . $model ['task_completed'] . "</label>";
        },
        'format' => 'raw',
        'headerOptions' => [
            'width' => '100px'
        ]
    ],
    [
        'header' => 'Progress',
        'value' => function ($model, $key, $index, $widget) use ($progress) {
            $progress_style = "progress-bar-info";
            if ($model [$progress] < 25) {
                $progress_style = "progress-bar-danger";
            } else if ($model [$progress] >= 25 && $model [$progress] < 50) {
                $progress_style = "progress-bar-warning";
            } else if ($model [$progress] == 100) {
                $progress_style = "progress-bar-success";
            }

            $html = '<div class="col-md-12 "><div class="progress">
                    <div class="progress-bar ' . $progress_style . '" role="progressbar" aria-valuenow="' . $model [$progress] . '"
                         aria-valuemin="0" aria-valuemax="100" style="width:' . $model [$progress] . '%">
                    </div>
                    
                </div><label style="position: absolute;top:0px;margin-left: 40%;">' . number_format($model [$progress], 1) . ' % </label></div>';
            return $html;
        },
        'format' => 'raw',
        'headerOptions' => [
            'width' => '150px'
        ]
    ]
];
?>
<div class="panel panel-info">
    <div class="panel-heading">
        <h4>Summary Report by Users</h4>
    </div>
    <div class="panel-body">
        <?php
        echo GridView::widget([
            'id' => 'pms-grid-user-report',
            'dataProvider' => $dataProvider,
            'columns' => $gridColumns // check the configuration for grid columns by clicking button above
        ]);
        ?>
    </div>
</div>

<?php
\richardfan\widget\JSRegister::begin([
    // 'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>

<script>
    $('#pms-grid-user-report .pagination a').on('click', function () {
        getReloadDiv($(this).attr('href'), '<?= $reloadDiv ?>');
        return false;
    });

    $('#pms-grid-user-report table tr th a').on('click', function () {
        getReloadDiv($(this).attr('href'), '<?= $reloadDiv ?>');
        return false;
    });
</script>
<?php \richardfan\widget\JSRegister::end(); ?>
