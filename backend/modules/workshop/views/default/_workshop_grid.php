<?php

/**
 * Created by PhpStorm.
 * User: kawin
 * Date: 8/2/2018
 * Time: 12:08 PM
 */

use appxq\sdii\helpers\SDNoty;
use yii\helpers\Html;
use yii\helpers\Url;

$db2 = 0;
$ezf_id = "1532334018067132600";
$modal='modal-ezform-main';
$reloadDiv='workshop-grid-view';
/* @var $this \yii\web\View */

?>
<div class="table-responsive">
<?php
try {

    echo yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'id' => 'workshop-grid-view',

        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'headerOptions' => ['style' => 'text-align: center;'],
                'contentOptions' => ['style' => 'min-width:60px;width:60px;text-align: center;'],
            ],
            [
                'class' => 'appxq\sdii\widgets\ActionColumn',
                'contentOptions' => ['style' => 'min-width:110px;width:110px;text-align: center;'],
                'template' => '{view} {update} {delete} ',
                'buttons' => [
                    'view' => function ($url, $data, $key) use($ezf_id, $reloadDiv, $modal, $db2) {
                        if (backend\modules\ezforms2\classes\EzfUiFunc::showViewDataEzf($ezf_id, Yii::$app->user->id, $data['user_create']) && $db2==0) {
                            return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', Url::to(['/ezforms2/ezform-data/ezform-view',
                                'ezf_id' => $ezf_id,
                                'dataid' => $data['id'],
                                'modal' => $modal,
                                'reloadDiv' => $reloadDiv,
                            ]), [
                                'data-action' => 'update',
                                'title' => Yii::t('yii', 'View'),
                                'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                                'class' => 'btn btn-default btn-xs',
                            ]);
                        }
                    },
                    'update' => function ($url, $data, $key) use($ezf_id, $reloadDiv, $modal, $db2) {
                        if (!isset($data['user_create']) || backend\modules\ezforms2\classes\EzfUiFunc::showEditDataEzf($ezf_id, Yii::$app->user->id, $data['user_create'])) {
                            $color = ($db2==1 && !isset($data['id']))?'success':'primary';
                            $icon = $db2==1?'duplicate':'pencil';
                            return Html::a('<span class="glyphicon glyphicon-'.$icon.'"></span>', Url::to(['/ezforms2/ezform-data/ezform',
                                'ezf_id' => $ezf_id,
                                'dataid' => $db2==1?$data['id_ref']:$data['id'],
                                'modal' => $modal,
                                'reloadDiv' => $reloadDiv,
                                'db2' => $db2,
                            ]), [
                                'data-action' => 'update',
                                'title' => Yii::t('yii', 'Update'),
                                'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                                'class' => "btn btn-$color btn-xs btn-update",

                            ]);
                        }
                    },
                    'delete' => function ($url, $data, $key) use($ezf_id, $reloadDiv, $db2) {
                        if (backend\modules\ezforms2\classes\EzfUiFunc::showDeleteDataEzf($ezf_id, Yii::$app->user->id, $data['user_create']) && $db2==0) {
                            return Html::a('<span class="glyphicon glyphicon-trash"></span>', Url::to(['/ezforms2/ezform-data/delete',
                                'ezf_id' => $ezf_id,
                                'dataid' => $data['id'],
                                'reloadDiv' => $reloadDiv,
                            ]), [
                                'data-action' => 'delete',
                                'title' => Yii::t('yii', 'Delete'),
                                'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                'data-method' => 'post',
                                'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                                'class' => 'btn btn-danger btn-xs',
                            ]);
                        }
                    },
                ],
            ],
            [
                'attribute' => 'title',
                'headerOptions' => ['style' => 'text-align: center;'],
                'contentOptions' => ['style' => "min-width:100px;text-align: left;"],
            ],
            [
                'attribute' => 'user_create',
                'headerOptions' => ['style' => 'text-align: center;min-width:200px;width:25%;max-width:200px;'],
                'contentOptions' => ['style' => "max-width:100px;text-align: center;"],
                'value' => function ($data) {
                    $username = Yii::$app->db->createCommand("SELECT CONCAT(title,firstname,lastname) as username FROM profile WHERE user_id = :user_id"
                        ,[":user_id"=>$data["user_create"]])
                        ->queryScalar();
                    return $username;
                },
            ],
            [
                'attribute' => 'show_collaborative',
                'format' => "raw",
                'headerOptions' => ['style' => 'text-align: center;min-width:200px;width:10%;max-width:200px;'],
                'contentOptions' => ['style' => "max-width:100px;text-align: center;"],
                'value' => function ($data) {
                    if($data["show_collaborative"] == 1){
                        return "<i class=\"fa fa-check\"></i>";
                    }else{
                        return "<i class=\"fa fa-times\"></i>";
                    }
                },
            ],
            [
                'attribute' => 'show_harvesting_tool',
                'headerOptions' => ['style' => 'text-align: center;min-width:200px;width:10%;max-width:200px;'],
                'contentOptions' => ['style' => "text-align: center;"],
                'format' => "raw",
                'value' => function ($data) {
                    if($data["show_harvesting_tool"] == 1){
                        return "<i class=\"fa fa-check\"></i>";
                    }else{
                        return "<i class=\"fa fa-times\"></i>";
                    }
                },
            ],
        ]
    ]);
} catch (Exception $e) {
    $message = $e->getMessage();
    echo "<div class='alert alert-danger'>$message</div>";
}
?>
</div>
<?php
$this->registerJs("
$('#workshop-grid-view tbody tr td a').on('click', function() {
    var url = $(this).attr('href');
    var action = $(this).attr('data-action');

    if(action === 'update' || action === 'create'){
        $('#$modal .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
        $('#$modal').modal('show')
        .find('.modal-content')
        .load(url);
        return false;
    } else if(action === 'delete') {
        yii.confirm('" . Yii::t('app', 'Are you sure you want to delete this item?') . "', function(){
            $.post(
                url, {'_csrf':'" . Yii::$app->request->getCsrfToken() . "'}
                ).done(function(result){
                if(result.status == 'success'){
                    " . SDNoty::show('result.message', 'result.status') . "
                            var urlreload =  $('#$reloadDiv').attr('data-url');        
                            getUiAjax(urlreload, '$reloadDiv');        
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
});


$('#$reloadDiv .pagination a').on('click', function() {
    getUiAjax($(this).attr('href'), '$reloadDiv');
    return false;
});

$('#$reloadDiv thead tr th a').on('click', function() {
    getUiAjax($(this).attr('href'), '$reloadDiv');
    return false;
});

function getUiAjax(url, divid) {
    $.ajax({
        method: 'POST',
        url: url,
        dataType: 'HTML',
        success: function(result, textStatus) {
            $('#'+divid).html(result);
        }
    });
}

"
);