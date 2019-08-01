<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\helpers\Url;
use appxq\sdii\widgets\GridView;
use appxq\sdii\widgets\ModalForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\ezforms2\models\RandomCodeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Random Codes';
$this->params['breadcrumbs'][] = $this->title;
?>
    <div class="random-code-index">

        <div class="sdbox-header">
            <h3><?= Html::encode($this->title) ?></h3>
        </div>
        <?php // echo $this->render('_search', ['model' => $searchModel]);  ?>

        <?php Pjax::begin(['id' => 'random-code-grid-pjax']); ?>
        <?=
        !\Yii::$app->user->isGuest ?
            GridView::widget([
                'id' => 'random-code-grid',
//        'panelBtn' => Html::button(SDHtml::getBtnAdd(), ['data-url' => Url::to(['/ezforms2/randomization/add-code']), 'class' => 'btn btn-success btn-sm', 'id' => 'modal-addbtn-random-code']),// . ' ' .
//        Html::button(SDHtml::getBtnDelete(), ['data-url' => Url::to(['/ezforms2/randomization/deletes']), 'class' => 'btn btn-danger btn-sm', 'id' => 'modal-delbtn-random-code', 'disabled' => true]),
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' =>
                    [
//                [
//                'class' => 'yii\grid\CheckboxColumn',
//                'checkboxOptions' => [
//                    'class' => 'selectionRandomCodeIds'
//                ],
//                'headerOptions' => ['style' => 'text-align: center;'],
//                'contentOptions' => ['style' => 'width:40px;text-align: center;'],
//            ],
                        [
                            'class' => 'yii\grid\SerialColumn',
                            'headerOptions' => ['style' => 'text-align: center;'],
                            'contentOptions' => ['style' => 'width:60px;text-align: center;'],
                        ],
//            'id',
                        [
                            'label' => 'Record Name',
                            'attribute' => 'name',
                            'format' => 'raw',
                            'value' => function ($data) {
                                return $data['name'];
                            }
                        ],
                        [
                            'label' => 'User Create',
//                        'attribute' => 'name',
                            'format' => 'raw',
                            'value' => function ($data) {
                                $user = backend\modules\tmf\classes\TmfFn::GetUserName($data['user_create']);
                                return $user['firstname'] . ' ' . $user['lastname'];
                            }
                        ],

                        [
                            'label' => 'Ezfrom Name',
                            'format' => 'raw',
                            'value' => function ($data) {
                                $ezf = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($data['ezf_id']);
                                return $ezf['ezf_name'];
                            }
                        ],
//                [
//                'attribute' => 'code_random',
//                'contentOptions' => ['style' => 'max-width:100px; white-space: nowrap;
// text-overflow: ellipsis;
// -o-text-overflow: ellipsis;
// -ms-text-overflow: ellipsis;
// overflow: hidden;'],
//            ],
//                [
//                'label' => 'View Data',
//                'format' => 'raw',
//                'contentOptions' => ['style' => 'text-align: center;'],
//                'value' => function($data) {
////                    $dataRandomSite = backend\modules\ezforms2\models\RandomCodeSite::find()->where(['random_id' => $data['id']])->groupBy(['sitecode'])->all();
//                    $text = '';
////                    if ($dataRandomSite) {
////                        foreach ($dataRandomSite as $value) {
//                    $text = Html::tag(
//                                    'div', '<i class="glyphicon glyphicon-list-alt"></i>', [
//                                'class' => 'btn btn-info btn-xs btnViewData',
//                                'title' => Yii::t('tmf', 'Detail'),
//                                'data-url' => '/ezforms2/randomization/view-data?random_id=' . $data['id'] . '&sitecode=' . Yii::$app->user->identity->profile->sitecode
//                            ]);
////                        }
////                    }
//                    return $text;
//                }
//            ],
                        // 'seed',
                        // 'treatment',
                        // 'block_size',
                        // 'list_length',
                        [
                            'class' => 'appxq\sdii\widgets\ActionColumn',
                            'contentOptions' => ['style' => 'text-align: center;'],
                            'template' => '{data} {view} {update} {delete}',
                            'buttons' => [
                                'data' => function ($url, $data, $key) {
//                        if (backend\modules\ezforms2\classes\EzfUiFunc::showEditDataEzf($ezform_detail, Yii::$app->user->id, $data['user_create'])) {
//                        $dataRandomSite = backend\modules\ezforms2\models\RandomCodeSite::find()->where(['random_id' => $data['id']])->groupBy(['sitecode'])->all();
//                        $text = '';
//                        if ($dataRandomSite) {
//                            foreach ($dataRandomSite as $value) {
                                    $text = Html::tag(
                                        'div', '<i class="glyphicon glyphicon-list-alt"></i>', [
                                        'class' => 'btn btn-info btn-xs btnViewData',
                                        'title' => Yii::t('tmf', 'Detail'),
                                        'data-url' => '/ezforms2/randomization/view-data?random_id=' . $data['id'] . '&sitecode=' . Yii::$app->user->identity->profile->sitecode
                                    ]);
//                            }
//                        }
                                    return $text;
//                        }
                                },
                                'view' => function ($url, $data, $key) {
                                    if (backend\modules\ezforms2\classes\RandomizationFunc::authUser($data['id'])) {
                                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', Url::to(['/ezforms2/randomization/view',
                                            'id' => $data['id'],
                                        ]), [
                                            'data-action' => 'view',
                                            'title' => Yii::t('yii', 'View'),
                                            'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                                            'class' => 'btnDetail btn btn-default btn-xs',
                                        ]);
                                    }
                                },
                                'update' => function ($url, $data, $key) {
                                    if (backend\modules\ezforms2\classes\RandomizationFunc::authUser($data['id'])) {
                                        return backend\modules\ezforms2\classes\RandomizationFunc::authUser($data['id']) ? Html::a('<span class="glyphicon glyphicon-pencil"></span>', Url::to(['/ezforms2/randomization/update-code',
                                            'id' => $data['id'],
                                        ]), [
                                            'data-action' => 'update',
                                            'title' => Yii::t('yii', 'Update'),
                                            'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                                            'class' => 'btnDetail btn btn-primary btn-xs',
                                        ]) : '';
                                    }
                                },
                                'delete' => function ($url, $data, $key) {
                                    if (backend\modules\ezforms2\classes\RandomizationFunc::authUser($data['id'])) {
                                        return backend\modules\ezforms2\classes\RandomizationFunc::authUser($data['id']) ? Html::a('<span class="glyphicon glyphicon-trash"></span>', Url::to(['/ezforms2/randomization/delete',

                                        ]), [
                                            'data-id' => $data['id'],
                                            'data-action' => 'delete',
                                            'title' => Yii::t('yii', 'Delete'),
                                            'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                            'data-method' => 'post',
                                            'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                                            'class' => 'btnDetail btn btn-danger btn-xs',
                                        ]) : '';
                                    }
                                },
                            ],
                        ],
                    ],
            ]) : "";
        ?>
        <?php Pjax::end(); ?>

    </div>

<?=
ModalForm::widget([
    'id' => 'modal-random-code',
    'size' => 'modal-lg',
]);
?>

<?php $this->registerJs("
$('#random-code-grid-pjax').on('click', '#modal-addbtn-random-code', function() {
    modalRandomCode($(this).attr('data-url'));
});

$('#random-code-grid-pjax').on('click', '#modal-delbtn-random-code', function() {
    selectionRandomCodeGrid($(this).attr('data-url'));
});

$('#random-code-grid-pjax').on('click', '.select-on-check-all', function() {
    window.setTimeout(function() {
	var key = $('#random-code-grid').yiiGridView('getSelectedRows');
	disabledRandomCodeBtn(key.length);
    },100);
});

$('#random-code-grid-pjax').on('click', '.selectionRandomCodeIds', function() {
    var key = $('input:checked[class=\"'+$(this).attr('class')+'\"]');
    disabledRandomCodeBtn(key.length);
});

$('#random-code-grid-pjax').on('dblclick', 'tbody tr', function() {
    var id = $(this).attr('data-key');
    modalRandomCode('" . Url::to(['/ezforms2/randomization/update-code', 'id' => '']) . "'+id);
});	

$('#random-code-grid-pjax').on('click', 'tbody tr td a', function() {
    var url = $(this).attr('href');
    var action = $(this).attr('data-action');
    var id = $(this).attr('data-id');

    if(action === 'update' || action === 'view') {
	modalRandomCode(url);
    } else if(action === 'delete') {
	yii.confirm('" . Yii::t('app', 'Are you sure you want to delete this item?') . "', function() {
	    $.post(
		url,{id:id}
	    ).done(function(result) {
		if(result == 1) {
		    " . SDNoty::show('"Success"', '"success"') . "
		    $.pjax.reload({container:'#random-code-grid-pjax'});
		} else {
		    " . SDNoty::show('"Error"', '"error"') . "
		}
	    }).fail(function() {
		" . SDNoty::show("'" . SDHtml::getMsgError() . " Server Error'", '"error"') . "
		console.log('server error');
	    });
	});
    }
    return false;
});

function disabledRandomCodeBtn(num) {
    if(num>0) {
	$('#modal-delbtn-random-code').attr('disabled', false);
    } else {
	$('#modal-delbtn-random-code').attr('disabled', true);
    }
}

function selectionRandomCodeGrid(url) {
    yii.confirm('" . Yii::t('app', 'Are you sure you want to delete these items?') . "', function() {
	$.ajax({
	    method: 'POST',
	    url: url,
	    data: $('.selectionRandomCodeIds:checked[name=\"selection[]\"]').serialize(),
	    dataType: 'JSON',
	    success: function(result, textStatus) {
		if(result.status == 'success') {
		    " . SDNoty::show('result.message', 'result.status') . "
		    $.pjax.reload({container:'#random-code-grid-pjax'});
		} else {
		    " . SDNoty::show('result.message', 'result.status') . "
		}
	    }
	});
    });
}

$('.btnViewData').click(function(){
    modalRandomCode($(this).attr('data-url'));
});

function modalRandomCode(url) {
    $('#modal-random-code .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
    $('#modal-random-code').modal('show')
    .find('.modal-content')
    .load(url);
}

"); ?>