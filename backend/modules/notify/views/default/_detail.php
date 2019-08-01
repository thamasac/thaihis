<?php

use yii\helpers\Html;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use yii\helpers\Url;
use appxq\sdii\widgets\GridView;
?>

<div class="ezform-fields-form">


    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true" >&times;</button>
        <h4 class="modal-title" id="itemModalLabel"><?= Yii::t('notify', 'Notifications') ?></h4>
    </div>

    <div class="modal-body">
        <?=
        GridView::widget([
            'id' => "detail-notify",
            'dataProvider' => $dataProvider,
            'panelBtn' => Html::button("<span class='glyphicon glyphicon-plus'></span> ".Yii::t('notify', 'Add notify'), [
                'class' => 'btn btn-success',
                'id' => 'btnAddNotifys',
                'data-url' => '/notify/default/create?ezf_id=' . $ezf_id . '&v=' . $version . '&modal=' . $reloadDiv . '-add-notify&reloadDiv=' . $reloadDiv
            ]),
            'columns' => [
                    [
                    'attribute' => 'ezf_field_label',
                    'label' => Yii::t('ezforms', 'Specify question statement.'),
//                    'headerOptions' => ['style' => 'min-width:110px;width:110px;text-align: center;'],
                ], [
                    'class' => 'appxq\sdii\widgets\ActionColumn',
                    'contentOptions' => ['style' => 'min-width:110px;width:110px;text-align: center;'],
                    'template' => '{update} {delete}', //'{view} {update} {delete} ',
                    'buttons' => [
                        'update' => function ($url, $data, $key) use ($reloadDiv, $modal) {
//                if (backend\modules\ezforms2\classes\EzfUiFunc::showViewDataEzf($ezform, Yii::$app->user->id, $data['user_create'])) {

                            return Html::a('<span class="glyphicon glyphicon-pencil"></span>', '/notify/default/update?id=' . $data['ezf_field_id'] . '&v=' . $data['ezf_version'] . '&modal=' . $reloadDiv . '-modal' . '&reloadDiv=' . $reloadDiv, [
                                        'title' => Yii::t('yii', 'Update'),
                                        'data-action' => 'update',
                                        'class' => 'btn btn-primary btn-xs btnUpdate',
                            ]);
                        },
                        'delete' => function ($url, $data, $key) use($reloadDiv, $modal) {
                            return Html::a('<span class="glyphicon glyphicon-trash"></span>', Url::to(['/ezbuilder/ezform-fields/delete']), [
                                        'reloadDiv' => $reloadDiv,
                                        'modal' => $modal,
                                        'data-id' => $data['ezf_field_id'],
                                        'data-action' => 'delete',
                                        'title' => Yii::t('yii', 'Delete'),
                                        'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                        'data-method' => 'post',
                                        'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                                        'class' => 'btn btn-danger btn-xs',
                            ]);
                        },
                    ],
                ]
            ]
        ]);
        ?>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true" ><?= Yii::t('app', 'Close') ?></button>
    </div>



</div>

<?php $this->registerJs("
    

$('#btnAddNotifys').click(function(){
    var url = $(this).attr('data-url');
    $('#$reloadDiv-add-notify .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
    $('#$reloadDiv-add-notify').modal('show')
    .find('.modal-content')
    .load(url);
    return false;
});

$('#$reloadDiv-modal tbody tr td a').on('click', function() {
    
    var url = $(this).attr('href');
    var action = $(this).attr('data-action');
    var id = $(this).attr('data-id');
    
    if(action === 'update' || action === 'create'){
        $('#$reloadDiv-add-notify .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
        $('#$reloadDiv-add-notify').modal('show')
        .find('.modal-content')
        .load(url);
        return false;
    } else if(action === 'delete') {
        yii.confirm('" . Yii::t('app', 'Are you sure you want to delete this item?') . "', function(){
                $.post(
                        url, {'_csrf':'" . Yii::$app->request->getCsrfToken() . "',id:id}
                ).done(function(result){
                        if(result.status == 'success'){
                                " . SDNoty::show('result.message', 'result.status') . "
//                                getUiAjax($('#$reloadDiv').attr('data-url'),'$reloadDiv','tool');
                                $('#$modal').modal('show')
                                .find('.modal-content')
                                .load('/notify/default/detail?ezf_id=$ezf_id&v=$version&modal=$reloadDiv-modal&reloadDiv=$reloadDiv');
                        } else {
                                " . SDNoty::show('result.message', 'result.status') . "
                        }
                }).fail(function(){
                        " . SDNoty::show("'" . "Server Error'", '"error"') . "
                        console.log('server error');
                });
        });
        return false;
    }
//    return false;
});
");
?>