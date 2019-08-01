<?php

use yii\helpers\Html;
use yii\helpers\Url;
use appxq\sdii\helpers\SDNoty;

//\appxq\sdii\utils\VarDumper::dump($dataProvider);
?>
<div class="">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
        <?=
        appxq\sdii\widgets\GridView::widget([
            'id' => "edit-line",
            'panelBtn' => Html::button('<span class="fa fa-plus"></span> ' . Yii::t('app', 'New'), ['class' => 'btn btn-success', 'id' => 'btnAddLine']),
            'dataProvider' => $dataProvider,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                [
                    'attribute' => 'line_qrcode',
                    'label' => 'QR Code',
                    'format' => 'raw',
                    'value' => function($data) {
                        return Html::img($data['line_qrcode'], ['width' => '80%', 'height' => '60%']);
                    },
                    'headerOptions' => ['class' => "text-center", 'style' => "min-width:100px;max-width:  200px;white-space: initial;"],
                    'contentOptions' => ['class' => "", 'style' => "min-width:100px;max-width:  200px;white-space: initial;word-wrap:break-word;"],
                ],
                [
                    'attribute' => 'line_name',
                    'label' => 'Name',
                    'headerOptions' => ['class' => "text-center", 'style' => "min-width:100px;max-width:  200px;white-space: initial;"],
                    'contentOptions' => ['class' => "", 'style' => "min-width:100px;max-width:  200px;white-space: initial;word-wrap:break-word;"],
                ],
                [
                    'attribute' => 'line_token',
                    'label' => 'Token',
                    'headerOptions' => ['class' => "text-center", 'style' => "min-width:100px;max-width:  200px;white-space: initial;"],
                    'contentOptions' => ['class' => "", 'style' => "min-width:100px;max-width:  200px;white-space: initial;word-wrap:break-word;"],
                ],
                [
                    'attribute' => 'line_secret',
                    'label' => 'Channel Secret',
                    'headerOptions' => ['class' => "text-center", 'style' => "min-width:100px;max-width:  200px;white-space: initial;"],
                    'contentOptions' => ['class' => "", 'style' => "min-width:100px;max-width:  200px;white-space: initial;word-wrap:break-word;"],
                ],
                [
                    'attribute' => 'line_status',
                    'label' => 'Line Ststus',
                    'format' => 'raw',
                    'value' => function($data) {
                        if ($data['line_status'] == 1) {
                            return Html::button('Active', ['class' => 'btn btn-success btn-xs btnStatusLine', 'data-url' => Url::to(['/line/default/update-status',
                                            'data-status' => '1',
                                            'dataid' => $data['id'],
                            ])]);
                        } else {
                            return Html::button('Not Active', ['class' => 'btn btn-danger btn-xs btnStatusLine', 'data-url' => Url::to(['/line/default/update-status',
                                            'dataid' => $data['id'],
                                            'data-status' => '0',
                            ])]);
                        }
                    },
                    'headerOptions' => ['class' => "text-center", 'style' => "min-width:100px;max-width:  200px;white-space: initial;"],
                    'contentOptions' => ['class' => "", 'style' => "min-width:100px;max-width:  200px;white-space: initial;word-wrap:break-word;"],
                ], [
                    'class' => 'appxq\sdii\widgets\ActionColumn',
                    'contentOptions' => ['style' => 'min-width:110px;width:110px;text-align: center;'],
                    'template' => '{view} {update} {delete} ',
                    'buttons' => [
                        'view' => function ($url, $data) use ($reloadDiv,$modal_line) {
                            return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', Url::to(['/line/default/view',
                                                'dataid' => $data['id'],
                                'modal' => $modal_line,
                                'reloadDiv' => $reloadDiv
                                            ]), [
                                        'data-action' => 'view',
                                        'title' => Yii::t('yii', 'View'),
//                                        'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
//                                        'data-method' => 'post',
                                        'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                                        'class' => 'btn btn-default btn-xs',
                            ]);
                        },
                        'update' => function ($url, $data)  use ($reloadDiv,$modal_line) {
                            return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Url::to(['/line/default/update',
                                                'dataid' => $data['id'],
                                'modal' => $modal_line,
                                'reloadDiv' => $reloadDiv
                                            ]), [
                                        'data-action' => 'update',
                                        'title' => Yii::t('yii', 'Update'),
//                                        'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
//                                        'data-method' => 'post',
                                        'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                                        'class' => 'btn btn-primary btn-xs',
                            ]);
                        },
                        'delete' => function ($url, $data) {
                            return Html::a('<span class="glyphicon glyphicon-trash"></span>', Url::to(['/line/default/delete'
                                            ]), [
                                        'data-action' => 'delete',
                                        'data-id' => $data['id'],
                                        'title' => Yii::t('yii', 'Delete'),
                                        'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                        'data-method' => 'post',
                                        'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                                        'class' => 'btn btn-danger btn-xs',
                            ]);
                        },
                    ]
                ]]
        ]);
        ?>
    </div>
    <div class="modal-footer">
        <div class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?= Yii::t('app', 'Close') ?></div>
    </div>
</div>


<script>

    $('.btnStatusLine').on('click', function () {
//        alert($('#<?=$reloadDiv?>').attr('data-url'));return false;
        $.get($(this).attr('data-url'), function (data) {
            if (data) {
                $('#<?=$modal_line?>').find('.modal-content')
                        .load('/line/default/edit?reloadDiv<?=$reloadDiv?>&modal=<?=$modal_line?>');
<?= SDNoty::show('"Success"', '"success"') ?>;
            } else {
<?= SDNoty::show('"Error"', '"error"') ?>;
            }
        });
        return false;
    });

    $('#edit-line tbody tr td a').on('click', function () {
        var url = $(this).attr('href');
        var dataid = $(this).attr('data-id');
        var action = $(this).attr('data-action');
        if (action === 'update' || action === 'create') {
            $('#<?=$modal_line?>').find('.modal-content')
                    .load(url);
        } else if (action === 'view') {
            $('#<?=$modal_line?>').find('.modal-content')
                    .load(url);
        } else if (action === 'delete') {
            yii.confirm('<?= Yii::t('app', 'Are you sure you want to delete this item?') ?>', function () {
                $.post(
                        url, {'_csrf': '<?= Yii::$app->request->getCsrfToken() ?>', dataid:dataid}
                ).done(function (result) {
                    if (result) {
                        $('#<?=$modal_line?>').find('.modal-content')
                                .load('/line/default/edit?reloadDiv<?=$reloadDiv?>&modal=<?=$modal_line?>');
<?= SDNoty::show('Success', '"success"') ?>;
                    } else {
<?= SDNoty::show('"Error"', '"error"') ?>;
                    }
                }).fail(function () {
<?= SDNoty::show('"Server Error"', '"error"') ?>;
                    console.log('server error');
                });
            });
        }
        return false;
    });

    $('#btnAddLine').on('click', function () {
        $('#<?=$modal_line?>')
                .find('.modal-content')
                .load('/line/default/add?reloadDiv<?=$reloadDiv?>&modal=<?=$modal_line?>');
        return false;
        return false;
    });
    $('#edit-line .pagination a').on('click', function () {
        $('#<?=$modal_line?>')
                .find('.modal-content')
                .load($(this).attr('href'));
        return false;
    });
    $('#edit-line thead tr th a').on('click', function () {
        $('#<?=$modal_line?>')
                .find('.modal-content')
                .load($(this).attr('href'));
        return false;
    });
</script>
