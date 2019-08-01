<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\helpers\Url;
use appxq\sdii\widgets\GridView;
use appxq\sdii\widgets\ModalForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\gantt\models\InvProjectSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Inv Projects';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="inv-project-index">

    <div class="sdbox-header">
	<h3><?=  Html::encode($this->title) ?></h3>
    </div>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p style="padding-top: 10px;">
	<span class="label label-primary">Notice</span>
	<?= Yii::t('app', 'You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b> or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.') ?>
    </p>

    <?php  Pjax::begin(['id'=>'inv-project-grid-pjax']);?>
    <?= GridView::widget([
	'id' => 'inv-project-grid',
	'panelBtn' => Html::button('<span class="glyphicon glyphicon-plus"> เพิ้มโปรเจค</span>', ['data-url'=>Url::to(['inv-project/create']), 'class' => 'btn btn-success btn-sm', 'id'=>'modal-addbtn-inv-project']),
	'dataProvider' => $dataProvider,
        'columns' => [
	    [
		'class' => 'yii\grid\CheckboxColumn',
		'checkboxOptions' => [
		    'class' => 'selectionInvProjectIds'
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
                    'attribute' => 'project',
                    'label' => 'ชื่อโปรเจค',
                    'headerOptions' => ['style'=>'text-align: center;'],
                    'contentOptions'=>['style'=>'min-width:400px; text-align: center;'],
            ],            
            [
                    'attribute' => 'status',
                    'label' => 'การแชร์',
                    'headerOptions' => ['style'=>'text-align: center;'],
                    'contentOptions'=>['style'=>'min-width:30px; text-align: center;'],
            ], 
            [
                'label' => 'สร้างโดย',
                'attribute' => 'created_by',
                'headerOptions' => ['style'=>'text-align: center;'],
                'format' => 'raw',
                'value' => function ($data) {
                    $profile = \backend\models\UserProfile::findOne(['user_id'=>$data->created_by]);
                    return $profile->firstname;
                }
            ],
            [
                    'attribute' => 'created_date',
                    'label' => 'สร้างเมื่อ',
                    'contentOptions'=>['style'=>'min-width:30px; text-align: center;'],
                    'headerOptions' => ['style'=>'text-align: center;'],
                    'format' => ['date', 'php:d/m/Y'],
            ],
            [
                'class' => 'common\lib\sdii\widgets\SDActionColumn',
                'headerOptions' => ['style'=>'text-align: center;'],
                'contentOptions' => ['style'=>'text-align: center;'],
                'template' => '{view}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        $url = "/gantt/?mid=" . $model->id;
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span> จัดการโปรเจค', $url, [
                            'data-action' => 'manage',
                            'title' => Yii::t('yii', 'AddTask'),
                            'class' => 'btn btn-warning btn-xs',
                            'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                        ]);
                    }],
            ],
        ],
    ]); ?>
    <?php  Pjax::end();?>

</div>

<?=  ModalForm::widget([
    'id' => 'modal-inv-project',
    'size'=>'modal-lg',
]);
?>

<?php  $this->registerJs("
$('#inv-project-grid-pjax').on('click', '#modal-addbtn-inv-project', function() {
    modalInvProject($(this).attr('data-url'));
});

$('#inv-project-grid-pjax').on('click', '#modal-delbtn-inv-project', function() {
    selectionInvProjectGrid($(this).attr('data-url'));
});

$('#inv-project-grid-pjax').on('click', '.select-on-check-all', function() {
    window.setTimeout(function() {
	var key = $('#inv-project-grid').yiiGridView('getSelectedRows');
	disabledInvProjectBtn(key.length);
    },100);
});

$('#inv-project-grid-pjax').on('click', '.selectionInvProjectIds', function() {
    var key = $('input:checked[class=\"'+$(this).attr('class')+'\"]');
    disabledInvProjectBtn(key.length);
});

$('#inv-project-grid-pjax').on('dblclick', 'tbody tr', function() {
    var id = $(this).attr('data-key');
    modalInvProject('".Url::to(['inv-project/update', 'id'=>''])."'+id);
});	

$('#inv-project-grid-pjax').on('click', 'tbody tr td a', function() {
    var url = $(this).attr('href');
    var action = $(this).attr('data-action');

    if(action === 'update' || action === 'view') {
	modalInvProject(url);
    } else if(action === 'delete') {
	yii.confirm('".Yii::t('app', 'Are you sure you want to delete this item?')."', function() {
	    $.post(
		url
	    ).done(function(result) {
		if(result.status == 'success') {
		    ". SDNoty::show('result.message', 'result.status') ."
		    $.pjax.reload({container:'#inv-project-grid-pjax'});
		} else {
		    ". SDNoty::show('result.message', 'result.status') ."
		}
	    }).fail(function() {
		". SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') ."
		console.log('server error');
	    });
	});
    } else if(action === 'manage') {
        return true;
    }
    return false;
});

function disabledInvProjectBtn(num) {
    if(num>0) {
	$('#modal-delbtn-inv-project').attr('disabled', false);
    } else {
	$('#modal-delbtn-inv-project').attr('disabled', true);
    }
}

function selectionInvProjectGrid(url) {
    yii.confirm('".Yii::t('app', 'Are you sure you want to delete these items?')."', function() {
	$.ajax({
	    method: 'POST',
	    url: url,
	    data: $('.selectionInvProjectIds:checked[name=\"selection[]\"]').serialize(),
	    dataType: 'JSON',
	    success: function(result, textStatus) {
		if(result.status == 'success') {
		    ". SDNoty::show('result.message', 'result.status') ."
		    $.pjax.reload({container:'#inv-project-grid-pjax'});
		} else {
		    ". SDNoty::show('result.message', 'result.status') ."
		}
	    }
	});
    });
}

function modalInvProject(url) {
    $('#modal-inv-project .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
    $('#modal-inv-project').modal('show')
    .find('.modal-content')
    .load(url);
}

");?>