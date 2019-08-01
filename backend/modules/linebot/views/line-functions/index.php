<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\helpers\Url;
use appxq\sdii\widgets\GridView;
use appxq\sdii\widgets\ModalForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\linebot\models\LineFunctionsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('linebot', 'Line Functions');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="line-functions-index">

    <div class="sdbox-header">
	<h3><?=  Html::encode($this->title) ?></h3>
    </div>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php  Pjax::begin(['id'=>'line-functions-grid-pjax']);?>
    <?= GridView::widget([
	'id' => 'line-functions-grid',
	'panelBtn' => Html::button(SDHtml::getBtnAdd(), ['data-url'=>Url::to(['line-functions/create']), 'class' => 'btn btn-success btn-sm', 'id'=>'modal-addbtn-line-functions']). ' ' .
		      Html::button(SDHtml::getBtnDelete(), ['data-url'=>Url::to(['line-functions/deletes']), 'class' => 'btn btn-danger btn-sm', 'id'=>'modal-delbtn-line-functions', 'disabled'=>true]),
	'dataProvider' => $dataProvider,
	'filterModel' => $searchModel,
        'columns' => [
	    [
		'class' => 'yii\grid\CheckboxColumn',
		'checkboxOptions' => [
		    'class' => 'selectionLineFunctionIds'
		],
		'headerOptions' => ['style'=>'text-align: center;'],
		'contentOptions' => ['style'=>'width:40px;text-align: center;'],
	    ],
	    [
		'class' => 'yii\grid\SerialColumn',
		'headerOptions' => ['style'=>'text-align: center;'],
		'contentOptions' => ['style'=>'width:60px;text-align: center;'],
	    ],

            //'id',
            'channel_id',
            'command',
            //'api:ntext',
            //'template:ntext',
            // 'options:ntext',
             //'role:ntext',
            [
                            'attribute'=>'api',
                            'format'=>'raw',
                            'value'=>function ($data){ 
                                $api = \appxq\sdii\utils\SDUtility::string2Array($data['api']);
                                $label = 'None';
                                if($api['type']==1){
                                    $label = 'Api'; 
                                } elseif ($api['type']==2) {
                                    $label = 'EzForm'; 
                                } 
                                
                                return Html::a($label, Url::to(['/linebot/line-functions/view', 'id'=>$data['id'], 'show'=>'api']), [
                                    'class' => 'btn btn-default btn-xs',
                                    'data-action' => 'view',
                                    'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                                ]);
                            },
                            'headerOptions'=>['style'=>'text-align: center;'],
                            'contentOptions'=>['style'=>'width:100px; text-align: center;'],
                            'filter'=>'',
            ],
            [
                            'attribute'=>'role',
                            'format'=>'raw',
                            'value'=>function ($data){ 
                                $role = \appxq\sdii\utils\SDUtility::string2Array($data['role']);
                                $label = 'Private';
                                if($role['public']==1){
                                    $label = 'Public'; 
                                } 
                                return Html::a($label, Url::to(['/linebot/line-functions/view', 'id'=>$data['id'], 'show'=>'role']), [
                                    'class' => 'btn btn-default btn-xs',
                                    'data-action' => 'view',
                                    'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                                ]);
                            },
                            'headerOptions'=>['style'=>'text-align: center;'],
                            'contentOptions'=>['style'=>'width:100px; text-align: center;'],
                            'filter'=>'',
            ],
            [
                            'attribute'=>'active',
                            'format'=>'raw',
                            'value'=>function ($data){ return $data['active']==1?'<i class="glyphicon glyphicon-ok"></i>':'<i class="glyphicon glyphicon-remove"></i>'; },
                            'headerOptions'=>['style'=>'text-align: center;'],
                            'contentOptions'=>['style'=>'width:100px; text-align: center;'],
                            'filter'=> Html::activeDropDownList($searchModel, 'active', ['No', 'Yes'], ['class'=>'form-control', 'prompt'=>'All'])
            ],
            // 'updated_by',
            // 'updated_at',
            // 'created_by',
            // 'created_at',
            [
                'attribute' => 'created_at',
                'format' => ['date', 'php:d/m/Y'],
                'contentOptions' => ['style' => 'width:100px;text-align: center;'],
                'filter'=>'',
            ],
	    [
		'class' => 'appxq\sdii\widgets\ActionColumn',
		'contentOptions' => ['style'=>'width:300px;'],
		'template' => '{template} {view} {update} {delete}',
                'buttons' => [
                    'api' => function ($url, $data, $key) {
                        return Html::a('<i class="glyphicon glyphicon-eye-open"></i> Api', Url::to(['/linebot/line-functions/view', 'id'=>$data['id'], 'show'=>'api']), [
                                'class' => 'btn btn-default btn-xs',
                                'data-action' => 'view',
                                'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                            ]);
                    },
                    'template' => function ($url, $data, $key) {
                        return Html::a('<i class="glyphicon glyphicon-eye-open"></i> Template', Url::to(['/linebot/line-functions/view', 'id'=>$data['id'], 'show'=>'template']), [
                                'class' => 'btn btn-default btn-xs',
                                'data-action' => 'view',
                                'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                            ]);
                    },
                    'role' => function ($url, $data, $key) {
                        return Html::a('<i class="glyphicon glyphicon-eye-open"></i> Role', Url::to(['/linebot/line-functions/view', 'id'=>$data['id'], 'show'=>'role']), [
                                'class' => 'btn btn-default btn-xs',
                                'data-action' => 'view',
                                'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                            ]);
                    },
//                    'options' => function ($url, $data, $key) {
//                        return Html::a('<i class="glyphicon glyphicon-eye-open"></i> Options', Url::to(['/linebot/line-functions/view', 'id'=>$data['id'], 'show'=>'options']), [
//                                'class' => 'btn btn-default btn-xs',
//                                'data-action' => 'view',
//                                'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
//                            ]);
//                    },
                    'view' => function ($url, $data, $key) {
                        return Html::a('<i class="glyphicon glyphicon-eye-open"></i> View', $url, [
                                'class' => 'btn btn-default btn-xs',
                                'data-action' => 'view',
                                'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                            ]);
                    },
                    'update' => function ($url, $data, $key) {
                        return Html::a('<i class="glyphicon glyphicon-pencil"></i> Update', $url, [
                                'class' => 'btn btn-primary btn-xs',
                                'data-action' => 'update',
                                'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                            ]);
                    },
                    'delete' => function ($url, $data, $key) {
                        return Html::a('<i class="glyphicon glyphicon-trash"></i> Delete', $url, [
                                'class' => 'btn btn-danger btn-xs',
                                'data-action' => 'delete',
                                'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                            ]);
                    },        
                ]
	    ],
        ],
    ]); ?>
    <?php  Pjax::end();?>

</div>

<?=  ModalForm::widget([
    'id' => 'modal-line-functions',
    'size'=>'modal-lg',
]);
?>

<?php  \richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]); ?>
<script>
// JS script
$('#line-functions-grid-pjax').on('click', '#modal-addbtn-line-functions', function() {
    modalLineFunction($(this).attr('data-url'));
});

$('#line-functions-grid-pjax').on('click', '#modal-delbtn-line-functions', function() {
    selectionLineFunctionGrid($(this).attr('data-url'));
});

$('#line-functions-grid-pjax').on('click', '.select-on-check-all', function() {
    window.setTimeout(function() {
	var key = $('#line-functions-grid').yiiGridView('getSelectedRows');
	disabledLineFunctionBtn(key.length);
    },100);
});

$('#line-functions-grid-pjax').on('click', '.selectionLineFunctionIds', function() {
    var key = $('input:checked[class=\"'+$(this).attr('class')+'\"]');
    disabledLineFunctionBtn(key.length);
});

$('#line-functions-grid-pjax').on('dblclick', 'tbody tr', function() {
    var id = $(this).attr('data-key');
    modalLineFunction('<?= Url::to(['line-functions/update', 'id'=>''])?>'+id);
});	

$('#line-functions-grid-pjax').on('click', 'tbody tr td a', function() {
    var url = $(this).attr('href');
    var action = $(this).attr('data-action');

    if(action === 'update' || action === 'view') {
	modalLineFunction(url);
    } else if(action === 'delete') {
	yii.confirm('<?= Yii::t('app', 'Are you sure you want to delete this item?')?>', function() {
	    $.post(
		url
	    ).done(function(result) {
		if(result.status == 'success') {
		    <?= SDNoty::show('result.message', 'result.status')?>
		    $.pjax.reload({container:'#line-functions-grid-pjax'});
		} else {
		    <?= SDNoty::show('result.message', 'result.status')?>
		}
	    }).fail(function() {
		<?= SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"')?>
		console.log('server error');
	    });
	});
    }
    return false;
});

function disabledLineFunctionBtn(num) {
    if(num>0) {
	$('#modal-delbtn-line-functions').attr('disabled', false);
    } else {
	$('#modal-delbtn-line-functions').attr('disabled', true);
    }
}

function selectionLineFunctionGrid(url) {
    yii.confirm('<?= Yii::t('app', 'Are you sure you want to delete these items?')?>', function() {
	$.ajax({
	    method: 'POST',
	    url: url,
	    data: $('.selectionLineFunctionIds:checked[name=\"selection[]\"]').serialize(),
	    dataType: 'JSON',
	    success: function(result, textStatus) {
		if(result.status == 'success') {
		    <?= SDNoty::show('result.message', 'result.status')?>
		    $.pjax.reload({container:'#line-functions-grid-pjax'});
		} else {
		    <?= SDNoty::show('result.message', 'result.status')?>
		}
	    }
	});
    });
}

function modalLineFunction(url) {
    $('#modal-line-functions .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
    $('#modal-line-functions').modal('show')
    .find('.modal-content')
    .load(url);
}
</script>
<?php  \richardfan\widget\JSRegister::end(); ?>