<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\helpers\Url;
use appxq\sdii\widgets\GridView;
use appxq\sdii\widgets\ModalForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use backend\modules\ezmodules\classes\ModuleFunc;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\ezforms2\models\EzformVersionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('ezform', 'EzForm Version Management');

$this->params['breadcrumbs'][] = ['label' => Yii::t('ezform', 'EzForms'), 'url' => ['/ezforms2/ezform/index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="ezform-version-index">

    <div class="sdbox-header">
	<h3><?=  Html::encode($this->title) ?></h3>
    </div>
  
  <ul class="nav nav-tabs" style="margin-bottom: 10px; margin-top: 10px;">
  <li role="presentation" class="<?=$tab==4?'active':''?>"><a href="<?=yii\helpers\Url::to(['/ezforms2/ezform-version/index'])?>"><i class="glyphicon glyphicon-eye-open"></i> Under Reviewing</a></li>
  <li role="presentation" class="<?=$tab==1?'active':''?>"><a href="<?=yii\helpers\Url::to(['/ezforms2/ezform-version/index', 'tab'=>1])?>"><i class="glyphicon glyphicon-log-in"></i> Submit for Approval</a></li>
  <li role="presentation" class="<?=$tab==2?'active':''?>"><a href="<?=yii\helpers\Url::to(['/ezforms2/ezform-version/index', 'tab'=>2])?>"><i class="glyphicon glyphicon-ok"></i> Approved</a></li>
  <li role="presentation" class="<?=$tab==3?'active':''?>"><a href="<?=yii\helpers\Url::to(['/ezforms2/ezform-version/index', 'tab'=>3])?>"><i class="glyphicon glyphicon-ban-circle"></i> Retired</a></li>
  
</ul>
  
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php  Pjax::begin(['id'=>'ezform-version-grid-pjax']);?>
    <?= GridView::widget([
	'id' => 'ezform-version-grid',
	'panelBtn' => '',
	'dataProvider' => $dataProvider,
	'filterModel' => $searchModel,
        'columns' => [
	    [
		'class' => 'yii\grid\CheckboxColumn',
		'checkboxOptions' => [
		    'class' => 'selectionEzformVersionIds'
		],
		'headerOptions' => ['style'=>'text-align: center;'],
		'contentOptions' => ['style'=>'width:40px;text-align: center;'],
	    ],
	    [
		'class' => 'yii\grid\SerialColumn',
		'headerOptions' => ['style'=>'text-align: center;'],
		'contentOptions' => ['style'=>'width:60px;text-align: center;'],
	    ],
            [
                'attribute' => 'ezf_id',
                'value' => function ($data) {
                    return $data['ezf_name'];
                },
                
                'filter' => Html::activeTextInput($searchModel, 'ezf_name', ['class'=>'form-control']),
            ],
                        [
                'attribute' => 'ver_code',
                'contentOptions' => ['style' => 'width:180px; '],
            ],   
            [
                'attribute' => 'ver_for',
                'value' => function ($data) {
                    if(isset($data['ver_for'])){
                        return $data['ver_for'];
                    }else {
                        return 'Original';
                    }
                    
                },
                'contentOptions' => ['style' => 'width:180px; '],
            ],             
            [
                'attribute' => 'ver_active',
                'value' => function ($data) {
                    if($data['ver_active']==1){
                        return 'Yes';
                    }
                    return 'No';
                },
                        'headerOptions' => ['style' => 'text-align: center;'],
                'contentOptions' => ['style' => 'width:100px; text-align: center;'],
                'filter' => Html::activeDropDownList($searchModel, 'ver_active', ['No', 'Yes'], ['class'=>'form-control', 'prompt'=>'All']),
            ],             
            [
                'attribute' => 'fullname',
                'label' => Yii::t('ezform', 'Approved By'),
                'value' => function ($data) {
                    if(isset($data['approved_by'])){
                        return $data['fullname'];
                    } else {
                        return 'na';
                    }
                },
                'contentOptions' => ['style' => 'width:250px; '],
            ],
            [
                'attribute' => 'approved_date',
                'value' => function ($data) {
                    if(isset($data['approved_date']) && !empty($data['approved_date'])){
                        return \appxq\sdii\utils\SDdate::mysql2phpDate($data['approved_date']);
                    }
                    return 'na';
                },
                        'headerOptions' => ['style' => 'text-align: center;'],
                'contentOptions' => ['style' => 'width:100px; text-align: center;'],
                'filter' => '',
            ],             
            [
                'attribute' => 'created_at',
                'value' => function ($data) {
                    return \appxq\sdii\utils\SDdate::mysql2phpDate($data['created_at']);
                },
                        'headerOptions' => ['style' => 'text-align: center;'],
                'contentOptions' => ['style' => 'width:100px; text-align: center;'],
                'filter' => '',
            ], 
            // 'approved_date',
            // 'ver_options:ntext',
            // 'ezf_id',
            // 'field_detail:ntext',
            // 'ezf_sql:ntext',
            // 'ezf_js:ntext',
            // 'ezf_error:ntext',
            // 'ezf_options:ntext',
            // 'updated_by',
            // 'updated_at',
            // 'created_by',
            // 'created_at',
	    [
		'class' => 'appxq\sdii\widgets\ActionColumn',
		'contentOptions' => ['style'=>'width:300px;text-align: center;'],
		'template' => '{approval} {active} {approve} {cancel} {restore}',
                'buttons' => [
                    'restore' => function ($url, $data, $key) {
                        if(Yii::$app->user->can('administrator') && $data['ver_approved'] == 3){
                            return Html::a('<span class="glyphicon glyphicon-repeat"></span> ' . Yii::t('yii', 'Recall'), Url::to(['/ezforms2/ezform-version/cancel',
                                                'v' => $data['ver_code'],
                                                'ezf_id' => $data['ezf_id'],
                                            ]), [
                                        'data-action' => 'restore',
                                        'data-confirm' => Yii::t('yii', 'Are you sure you want to recall this item to previous version?'),
                                        'data-method' => 'post',
                                        'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                                        'class' => 'btn btn-info btn-xs',
                            ]);
                        }
                    },
                    'cancel' => function ($url, $data, $key) {
                        if(Yii::$app->user->can('administrator') && $data['ver_active'] != 1 && $data['ver_approved'] != 3){
                            return Html::a('<span class="glyphicon glyphicon-ban-circle"></span> ' . Yii::t('yii', 'Retire'), Url::to(['/ezforms2/ezform-version/approv-cancel',
                                                'v' => $data['ver_code'],
                                                'ezf_id' => $data['ezf_id'],
                                            ]), [
                                        'data-action' => 'delete',
                                        'data-confirm' => Yii::t('yii', 'Are you sure you want to retire this version?'),
                                        'data-method' => 'post',
                                        'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                                        'class' => 'btn btn-danger btn-xs',
                            ]);
                        }
                    },
                    'approve' => function ($url, $data, $key) {
                        if(Yii::$app->user->can('administrator') && $data['ver_approved'] == 1){
                            return Html::a('<span class="glyphicon glyphicon-ok"></span> ' . Yii::t('yii', 'Approve'), Url::to(['/ezforms2/ezform-version/approved',
                                                'v' => $data['ver_code'],
                                                'ezf_id' => $data['ezf_id'],
                                            ]), [
                                        'data-action' => 'approve',
                                        'data-confirm' => Yii::t('yii', 'Are you sure you want to approve this version?'),
                                        'data-method' => 'post',
                                        'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                                        'class' => 'btn btn-warning btn-xs',
                            ]);
                        }
                    },    
                    'approval' => function ($url, $data, $key) {
                        if(Yii::$app->user->can('administrator') && $data['ver_approved'] == 4){
                            return Html::a('<span class="glyphicon glyphicon-ok"></span> ' . Yii::t('yii', 'Submit for Approval'), Url::to(['/ezforms2/ezform-version/approv',
                                                'v' => $data['ver_code'],
                                                'ezf_id' => $data['ezf_id'],
                                            ]), [
                                        'data-action' => 'approval',
                                        'data-confirm' => Yii::t('yii', 'Are you sure you want to submit for approval this version?'),
                                        'data-method' => 'post',
                                        'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                                        'class' => 'btn btn-warning btn-xs',
                            ]);
                        }
                    },         
                    'active' => function ($url, $data, $key) {
                        if(Yii::$app->user->can('administrator') && $data['ver_active'] != 1 && $data['ver_approved'] == 2){
                            return Html::a('<span class="glyphicon glyphicon-star"></span> ' . Yii::t('yii', 'Active'), Url::to(['/ezforms2/ezform-version/active',
                                                'v' => $data['ver_code'],
                                                'ezf_id' => $data['ezf_id'],
                                            ]), [
                                        'data-action' => 'active',
                                        'data-confirm' => Yii::t('yii', 'Are you sure you want to active this version?'),
                                        'data-method' => 'post',
                                        'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                                        'class' => 'btn btn-success btn-xs',
                            ]);
                        }
                    },         
                ],
	    ],
        ],
    ]); ?>
    <?php  Pjax::end();?>

</div>

<?=  ModalForm::widget([
    'id' => 'modal-ezform-version',
    'size'=>'modal-lg',
]);
?>

<?php  $this->registerJs("
$('#ezform-version-grid-pjax').on('click', '#modal-addbtn-ezform-version', function() {
    modalEzformVersion($(this).attr('data-url'));
});

$('#ezform-version-grid-pjax').on('click', '#modal-delbtn-ezform-version', function() {
    selectionEzformVersionGrid($(this).attr('data-url'));
});

$('#ezform-version-grid-pjax').on('click', '.select-on-check-all', function() {
    window.setTimeout(function() {
	var key = $('#ezform-version-grid').yiiGridView('getSelectedRows');
	disabledEzformVersionBtn(key.length);
    },100);
});

$('#ezform-version-grid-pjax').on('click', '.selectionEzformVersionIds', function() {
    var key = $('input:checked[class=\"'+$(this).attr('class')+'\"]');
    disabledEzformVersionBtn(key.length);
});

$('#ezform-version-grid-pjax').on('click', 'tbody tr td a', function() {
    var url = $(this).attr('href');
    var action = $(this).attr('data-action');

    if(action === 'update' || action === 'view') {
	modalEzformVersion(url);
        return false;
    } else if(action === 'active') {
        yii.confirm('".Yii::t('app', 'Are you sure you want to active this version?')."', function() {
            $.post(
                url
            ).done(function(result) {
                if(result.status == 'success') {
                    " . SDNoty::show('result.message', 'result.status') . "
                    $.pjax.reload({container:'#ezform-version-grid-pjax'});
                } else {
                    " . SDNoty::show('result.message', 'result.status') . "
                }
            }).fail(function() {
                " . SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') . "
                console.log('server error');
            });
        });
        return false;
    } else if(action === 'approve') {
        yii.confirm('".Yii::t('app', 'Are you sure you want to approve this version?')."', function() {
            $.post(
                url
            ).done(function(result) {
                if(result.status == 'success') {
                    " . SDNoty::show('result.message', 'result.status') . "
                    $.pjax.reload({container:'#ezform-version-grid-pjax'});
                } else {
                    " . SDNoty::show('result.message', 'result.status') . "
                }
            }).fail(function() {
                " . SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') . "
                console.log('server error');
            });
        });
        return false;
    } else if(action === 'approval') {
        yii.confirm('".Yii::t('app', 'Are you sure you want to submit for approval this version?')."', function() {
            $.post(
                url
            ).done(function(result) {
                if(result.status == 'success') {
                    " . SDNoty::show('result.message', 'result.status') . "
                    $.pjax.reload({container:'#ezform-version-grid-pjax'});
                } else {
                    " . SDNoty::show('result.message', 'result.status') . "
                }
            }).fail(function() {
                " . SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') . "
                console.log('server error');
            });
        });
        return false;
    } else if(action === 'delete') {
	yii.confirm('".Yii::t('app', 'Are you sure you want to retire this version?')."', function() {
	    $.post(
		url
	    ).done(function(result) {
		if(result.status == 'success') {
		    ". SDNoty::show('result.message', 'result.status') ."
		    $.pjax.reload({container:'#ezform-version-grid-pjax'});
		} else {
		    ". SDNoty::show('result.message', 'result.status') ."
		}
	    }).fail(function() {
		". SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') ."
		console.log('server error');
	    });
	});
        return false;
    } else if(action === 'restore') {
	yii.confirm('".Yii::t('app', 'Are you sure you want to recall this item to previous version?')."', function() {
	    $.post(
		url
	    ).done(function(result) {
		if(result.status == 'success') {
		    ". SDNoty::show('result.message', 'result.status') ."
		    $.pjax.reload({container:'#ezform-version-grid-pjax'});
		} else {
		    ". SDNoty::show('result.message', 'result.status') ."
		}
	    }).fail(function() {
		". SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') ."
		console.log('server error');
	    });
	});
        return false;
    }
    
});

function disabledEzformVersionBtn(num) {
    if(num>0) {
	$('#modal-delbtn-ezform-version').attr('disabled', false);
    } else {
	$('#modal-delbtn-ezform-version').attr('disabled', true);
    }
}

function selectionEzformVersionGrid(url) {
    yii.confirm('".Yii::t('app', 'Are you sure you want to delete these items?')."', function() {
	$.ajax({
	    method: 'POST',
	    url: url,
	    data: $('.selectionEzformVersionIds:checked[name=\"selection[]\"]').serialize(),
	    dataType: 'JSON',
	    success: function(result, textStatus) {
		if(result.status == 'success') {
		    ". SDNoty::show('result.message', 'result.status') ."
		    $.pjax.reload({container:'#ezform-version-grid-pjax'});
		} else {
		    ". SDNoty::show('result.message', 'result.status') ."
		}
	    }
	});
    });
}

function modalEzformVersion(url) {
    $('#modal-ezform-version .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
    $('#modal-ezform-version').modal('show')
    .find('.modal-content')
    .load(url);
}

");?>