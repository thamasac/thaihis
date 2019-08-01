<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\helpers\Url;
use appxq\sdii\widgets\GridView;
use appxq\sdii\widgets\ModalForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use backend\modules\ezmodules\classes\ModuleFunc;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\ezmodules\models\EzmoduleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('ezmodule', 'EzModule Management');
$this->params['breadcrumbs'][] = ['label' => Yii::t('ezmodule', 'EzModule'), 'url' => ['/ezmodules/default/index']];
$this->params['breadcrumbs'][] = $this->title;
//$this->params['breadcrumbs'][] = $this->title;

$user_id = Yii::$app->user->id;
$template = \backend\modules\ezmodules\classes\ModuleQuery::getTemplate($user_id);
?>
<div class="ezmodule-index">

    <div class="sdbox-header">
        <h3><?= Html::encode($this->title) ?></h3>
    </div>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php Pjax::begin(['id' => 'ezmodule-grid-pjax', 'timeout' => FALSE]); ?>
    <?=
    GridView::widget([
        'id' => 'ezmodule-grid',
        'panelBtn' => Html::button(SDHtml::getBtnAdd(), ['data-url' => Url::to(['ezmodule/create']), 'class' => 'btn btn-success btn-sm', 'id' => 'modal-addbtn-ezmodule']) . ' ' .
        Html::button(SDHtml::getBtnDelete(), ['data-url' => Url::to(['ezmodule/deletes']), 'class' => 'btn btn-danger btn-sm', 'id' => 'modal-delbtn-ezmodule', 'disabled' => true]),
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'class' => 'yii\grid\CheckboxColumn',
                'checkboxOptions' => [
                    'class' => 'selectionEzmoduleIds'
                ],
                'headerOptions' => ['style' => 'text-align: center;'],
                'contentOptions' => ['style' => 'width:40px;text-align: center;'],
            ],
            [
                'class' => 'yii\grid\SerialColumn',
                'headerOptions' => ['style' => 'text-align: center;'],
                'contentOptions' => ['style' => 'width:60px;text-align: center;'],
            ],
            [
                'attribute' => 'ezm_icon',
                'value' => function ($data) {
                    if (isset($data['ezm_icon']) && !empty($data['ezm_icon'])) {
                        $storagePath = isset($data['icon_base_url'])?$data['icon_base_url']:Yii::getAlias('@storageUrl/module');
                        
                        return Html::img($storagePath . '/' . $data['ezm_icon'], ['width' => 25, 'class' => 'img-rounded']);
                    } else {
                        return Html::img(ModuleFunc::getNoIconModule(), ['width' => 25, 'class' => 'img-rounded']);
                    }
                },
                'format' => 'raw',
                'headerOptions' => ['style' => 'text-align: center;'],
                'contentOptions' => ['style' => 'width:40px; text-align: center;'],
                'filter' => '',
            ],
            'ezm_name',
            [
                'attribute' => 'ezf_id',
                'value' => function ($data) {
                    return $data['form_name'];
                },
                'contentOptions' => ['style' => 'width:200px; '],
                'filter' => '',
            ],
            [
                'attribute' => 'ezm_type',
                'value' => function ($data) {
                    return ModuleFunc::itemAlias('type', $data['ezm_type']);
                },
                'headerOptions' => ['style' => 'text-align: center;'],
                'contentOptions' => ['style' => 'width:140px; text-align: center;'],
                'filter' => Html::activeDropDownList($searchModel, 'ezm_type', ModuleFunc ::itemAlias('type'), ['class' => 'form-control', 'prompt' => Yii::t('ezmodule', 'All')]),
            ],
//            [
//                'attribute' => 'template_id',
//                'value' => function ($data) {
//                    return $data['template_name'];
//                },
//                'contentOptions' => ['style' => 'width:140px; '],
//                'filter' => Html::activeDropDownList($searchModel, 'template_id', ArrayHelper::map($template, 'template_id', 'template_name'), ['class' => 'form-control', 'prompt' => Yii::t('ezmodule', 'All')]),
//            ],
            [
                'attribute' => 'ezm_system',
                'value' => function ($data) {
                    return ($data['ezm_system'] == 1 ? ModuleFunc ::itemAlias('system', 1) : ModuleFunc ::itemAlias('system', 0));
                },
                'headerOptions' => ['style' => 'text-align: center;'],
                'contentOptions' => ['style' => 'width:120px; text-align: center;'],
                'visible' => Yii::$app->user->can('administrator'),
                'filter' => Html::activeDropDownList($searchModel, 'ezm_system', ModuleFunc ::itemAlias('system'), ['class' => 'form-control', 'prompt' => Yii::t('ezmodule', 'All')]),
            ],
            // 'ezm_devby:ntext',
            // 'ezm_link:ntext',
            // 'ezm_tag:ntext',
            // 'ezm_icon:ntext',
            // 'icon_base_url:ntext',
            // 'ezm_js:ntext',
            // 'ezf_id',
            // 'sitecode',
            // 'ezm_builder:ntext',
            [
                'attribute' => 'public',
                'value' => function ($data) {
                    return ($data['public'] == 1 ? ModuleFunc ::itemAlias('public', 1) : ModuleFunc ::itemAlias('public', 0));
                },
                'headerOptions' => ['style' => 'text-align: center;'],
                'contentOptions' => ['style' => 'width:100px; text-align: center;'],
                'filter' => Html::activeDropDownList($searchModel, 'public', ModuleFunc ::itemAlias('public'), ['class' => 'form-control', 'prompt' => Yii::t('ezmodule', 'All')]),
            ],
            [
                'attribute' => 'approved',
                'value' => function ($data) {
                    if ($data['public'] == 1) {
                        $btn = 'default';
                        $icon = 'remove';
                        if (isset($data['approved']) && $data['approved'] == 1) {
                            $btn = 'warning';
                            $icon = 'ok';
                        }
                        if(Yii::$app->user->can('administrator')){
                            return Html::a('<span class="glyphicon glyphicon-' . $icon . '"></span> ' . ModuleFunc ::itemAlias('approved', $data['approved']), Url::to(['/ezmodules/ezmodule-admin/approve',
                                            'id' => $data['ezm_id'],
                                        ]), [
                                    'data-action' => 'approve',
                                    'class' => "btn btn-$btn btn-xs",
                            ]);
                        } else {
                            return ModuleFunc ::itemAlias('approved', $data['approved']);
                        }
                        
                    } else {
                        return '';
                    }
                },
                'format' => 'raw',
                'headerOptions' => ['style' => 'text-align: center;'],
                'contentOptions' => ['style' => 'width:150px; text-align: center;'],
                'filter' => Html::activeDropDownList($searchModel, 'approved', ModuleFunc ::itemAlias('approved'), ['class' => 'form-control', 'prompt' => Yii::t('ezmodule', 'All')]),
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
            // 'share:ntext',
            // 'active',
            // 'options:ntext',
            // 'order_module',
            // 'created_by',
            // 'created_at',
            // 'updated_by',
            // 'updated_at',
            [
                'class' => 'appxq\sdii\widgets\ActionColumn',
                'contentOptions' => ['style' => 'width:360px;'],
                'template' => '{approve} {view} {clone} {export} {update} {delete}',
                'buttons' => [
                    'view' => function ($url, $data, $key) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span> ' . Yii::t('yii', 'View'), Url::to(['/ezmodules/ezmodule/view',
                                            'id' => $data->ezm_id,
                                        ]), [
                                    'data-action' => 'view',
                                    'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                                    'class' => 'btn btn-info btn-xs',
                        ]);
                    },
                    'update' => function ($url, $data, $key) {
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span> ' . Yii::t('yii', 'Update'), $url, [
                                    'data-action' => 'update',
                                    'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                                    'class' => 'btn btn-primary btn-xs',
                        ]);
                    },
                    'delete' => function ($url, $data, $key) {
                        return Html::a('<span class="glyphicon glyphicon-trash"></span> ' . Yii::t('yii', 'Delete'), $url, [
                                    'data-action' => 'delete',
                                    'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                    'data-method' => 'post',
                                    'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                                    'class' => 'btn btn-danger btn-xs',
                        ]);
                    },
                    'clone' => function ($url, $data, $key) {
                        return Html::a('<span class="fa fa-clone"></span> ' . Yii::t('ezform', 'Clone'), Url::to(['/ezmodules/ezmodule/clone', 'ezm_id'=>$data['ezm_id']]), [
                                    'data-action' => 'clone',
                                    'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                                    'class' => 'btn btn-default btn-xs',
                        ]);
                    },
                    'export' => function ($url, $data, $key) {
                        return Html::a('<span class="glyphicon glyphicon-export"></span> ' . Yii::t('ezform', 'Backup'), Url::to(['/ezmodules/ezmodule/export', 'ezm_id'=>$data['ezm_id']]), [
                                    'data-action' => 'export',
                                    'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                                    'class' => 'btn btn-default btn-xs',
                        ]);
                    },
                ],
            ],
        ],
    ]);
    ?>
    <?php Pjax::end(); ?>

</div>

<?=
ModalForm::widget([
    'id' => 'modal-ezmodule',
    'size' => 'modal-xxl',
    'tabindexEnable' => FALSE,
]);
?>

<?=  ModalForm::widget([
    'id' => 'modal-add-widget',
    'size' => 'modal-lg',
    'tabindexEnable' => false,
]);
?>

<?=  ModalForm::widget([
    'id' => 'modal-ezmodule-widget',
    'size' => 'modal-lg',
    'tabindexEnable' => false,
]);
?>
<?php $this->registerJs("
$('#modal-ezmodule-widget').on('hidden.bs.modal', function(e){
    var hasmodal = $('body .modal').hasClass('in');
    if(hasmodal){
        $('body').addClass('modal-open');
    } 
});

$('#modal-add-widget').on('hidden.bs.modal', function(e){
    var hasmodal = $('body .modal').hasClass('in');
    if(hasmodal){
        $('body').addClass('modal-open');
    } 
});

$('#modal-add-widget').on('click', '.btn-widget', function() {
    $('#ezmoduletab-template').froalaEditor('html.insert', $(this).attr('data-widget'), false);
});

$('#ezmodule-grid-pjax').on('click', '#modal-addbtn-ezmodule', function() {
    modalEzmodule($(this).attr('data-url'));
});

$('#ezmodule-grid-pjax').on('click', '#modal-delbtn-ezmodule', function() {
    selectionEzmoduleGrid($(this).attr('data-url'));
});

$('#ezmodule-grid-pjax').on('click', '.select-on-check-all', function() {
    window.setTimeout(function() {
	var key = $('#ezmodule-grid').yiiGridView('getSelectedRows');
	disabledEzmoduleBtn(key.length);
    },100);
});

$('#ezmodule-grid-pjax').on('click', '.selectionEzmoduleIds', function() {
    var key = $('input:checked[class=\"'+$(this).attr('class')+'\"]');
    disabledEzmoduleBtn(key.length);
});

$('#ezmodule-grid-pjax').on('dblclick', 'tbody tr', function() {
    var id = $(this).attr('data-key');
    modalEzmodule('" . Url::to(['ezmodule/update', 'id' => '']) . "'+id);
});	

$('#ezmodule-grid-pjax').on('click', 'tbody tr td a', function() {
    var url = $(this).attr('href');
    var action = $(this).attr('data-action');

    if(action === 'update') {
	modalEzmodule(url);
        return false;
    } else if(action === 'clone') {
        yii.confirm('".Yii::t('ezform', 'Are you sure you want to clone this item?')."', function() {
            location.href=url;
        });
        return false;
    } else if(action === 'approve') {
        $.post(
            url
        ).done(function(result) {
            if(result.status == 'success') {
                " . SDNoty::show('result.message', 'result.status') . "
                $.pjax.reload({container:'#ezmodule-grid-pjax'});
            } else {
                " . SDNoty::show('result.message', 'result.status') . "
            }
        }).fail(function() {
            " . SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') . "
            console.log('server error');
        });
        return false;
    } else if(action === 'delete') {
	yii.confirm('" . Yii::t('app', 'Are you sure you want to delete this item?') . "', function() {
	    $.post(
		url
	    ).done(function(result) {
		if(result.status == 'success') {
		    " . SDNoty::show('result.message', 'result.status') . "
		    $.pjax.reload({container:'#ezmodule-grid-pjax'});
		} else {
		    " . SDNoty::show('result.message', 'result.status') . "
		}
	    }).fail(function() {
		" . SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') . "
		console.log('server error');
	    });
	});
        return false;
    }
    
});

function disabledEzmoduleBtn(num) {
    if(num>0) {
	$('#modal-delbtn-ezmodule').attr('disabled', false);
    } else {
	$('#modal-delbtn-ezmodule').attr('disabled', true);
    }
}

function selectionEzmoduleGrid(url) {
    yii.confirm('" . Yii::t('app', 'Are you sure you want to delete these items?') . "', function() {
	$.ajax({
	    method: 'POST',
	    url: url,
	    data: $('.selectionEzmoduleIds:checked[name=\"selection[]\"]').serialize(),
	    dataType: 'JSON',
	    success: function(result, textStatus) {
		if(result.status == 'success') {
		    " . SDNoty::show('result.message', 'result.status') . "
		    $.pjax.reload({container:'#ezmodule-grid-pjax'});
		} else {
		    " . SDNoty::show('result.message', 'result.status') . "
		}
	    }
	});
    });
}

function modalEzmodule(url) {
    $('#modal-ezmodule .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
    $('#modal-ezmodule').modal('show')
    .find('.modal-content')
    .load(url);
}

"); ?>