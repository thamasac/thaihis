<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\helpers\Url;
use appxq\sdii\widgets\GridView;
use appxq\sdii\widgets\ModalForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use backend\modules\core\classes\CoreFunc;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\core\models\CoreTermsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('core', str_replace('_', ' ', ucfirst($taxonomy)));
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="tags-index">

    <div class="sdbox-header">
	<h3><?=  Html::encode($this->title) ?></h3>
    </div>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p style="padding-top: 10px;">
	<span class="label label-primary">Notice</span>
	<?= Yii::t('app', 'You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b> or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.') ?>
    </p>

    <?php  Pjax::begin(['id'=>'tags-grid-pjax']);?>
    <?= GridView::widget([
	'id' => 'tags-grid',
	'panelBtn' => Html::button(SDHtml::getBtnAdd(), ['data-url'=>Url::to(['tags/create', 'taxonomy'=>$taxonomy]), 'class' => 'btn btn-success btn-sm', 'id'=>'modal-addbtn-tags']). ' ' .
		      Html::button(SDHtml::getBtnDelete(), ['data-url'=>Url::to(['tags/deletes', 'taxonomy'=>$taxonomy]), 'class' => 'btn btn-danger btn-sm', 'id'=>'modal-delbtn-tags', 'disabled'=>true]),
	'dataProvider' => $dataProvider,
	'filterModel' => $searchModel,
        'columns' => [
	    [
		'class' => 'yii\grid\CheckboxColumn',
		'checkboxOptions' => [
		    'class' => 'selectionCoreTermIds'
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
		'attribute'=>'name',
		'label'=>CoreFunc::t('Name'),
		'contentOptions'=>['style'=>'width:300px;'],
	    ],
	    [
		'attribute'=>'description',
		'label'=>CoreFunc::t('Description'),
		'filter'=>'',
	    ],
	    [
		'attribute'=>'slug',
		'label'=>CoreFunc::t('Slug'),
		'contentOptions'=>['style'=>'width:250px;'],
		'filter'=>'',
	    ],
	    [
		'attribute'=>'count',
		'label'=>CoreFunc::t('Posts'),
		'headerOptions'=>['style'=>'text-align: center;'],
		'contentOptions'=>['style'=>'width:80px;text-align: center;'],
		'filter'=>'',
	    ],
	    [
		'class' => 'appxq\sdii\widgets\ActionColumn',
		'contentOptions' => ['style'=>'width:50px;'],
		'template' => '{update} {delete}',
		'buttons' => [
		    'delete' => function ($url, $data, $key) {
			if($data['term_id'] != 1){
			    return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
				'data-action' => 'delete',
				'title' => Yii::t('yii', 'Delete'),
				'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
				'data-method' => 'post',
				'data-pjax' => isset($this->pjax_id)?$this->pjax_id:'0',
			    ]);
			}
		    },
		]
	    ],
        ],
    ]); ?>
    <?php  Pjax::end();?>

    
</div>

<?=  ModalForm::widget([
    'id' => 'modal-tags',
]);
?>

<?php  $this->registerJs("
$('#tags-grid-pjax').on('click', '#modal-addbtn-tags', function() {
    modalCoreTerm($(this).attr('data-url'));
});

$('#tags-grid-pjax').on('click', '#modal-delbtn-tags', function() {
    selectionCoreTermGrid($(this).attr('data-url'));
});

$('#tags-grid-pjax').on('click', '.select-on-check-all', function() {
    window.setTimeout(function() {
	var key = $('#tags-grid').yiiGridView('getSelectedRows');
	disabledCoreTermBtn(key.length);
    },100);
});

$('#tags-grid-pjax').on('click', '.selectionCoreTermIds', function() {
    var key = $('input:checked[class=\"'+$(this).attr('class')+'\"]');
    disabledCoreTermBtn(key.length);
});

$('#tags-grid-pjax').on('dblclick', 'tbody tr', function() {
    var id = $(this).attr('data-key');
    modalCoreTerm('".Url::to(['tags/update', 'id'=>''])."'+id);
});	

$('#tags-grid-pjax').on('click', 'tbody tr td a', function() {
    var url = $(this).attr('href');
    var action = $(this).attr('data-action');

    if(action === 'update' || action === 'view') {
	modalCoreTerm(url);
    } else if(action === 'delete') {
	yii.confirm('".Yii::t('app', 'Are you sure you want to delete this item?')."', function() {
	    $.post(
		url
	    ).done(function(result) {
		if(result.status == 'success') {
		    ". SDNoty::show('result.message', 'result.status') ."
		    $.pjax.reload({container:'#tags-grid-pjax'});
		} else {
		    ". SDNoty::show('result.message', 'result.status') ."
		}
	    }).fail(function() {
		". SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') ."
		console.log('server error');
	    });
	});
    }
    return false;
});

function disabledCoreTermBtn(num) {
    if(num>0) {
	$('#modal-delbtn-tags').attr('disabled', false);
    } else {
	$('#modal-delbtn-tags').attr('disabled', true);
    }
}

function selectionCoreTermGrid(url) {
    yii.confirm('".Yii::t('app', 'Are you sure you want to delete these items?')."', function() {
	$.ajax({
	    method: 'POST',
	    url: url,
	    data: $('.selectionCoreTermIds:checked[name=\"selection[]\"]').serialize(),
	    dataType: 'JSON',
	    success: function(result, textStatus) {
		if(result.status == 'success') {
		    ". SDNoty::show('result.message', 'result.status') ."
		    $.pjax.reload({container:'#tags-grid-pjax'});
		} else {
		    ". SDNoty::show('result.message', 'result.status') ."
		}
	    }
	});
    });
}

function modalCoreTerm(url) {
    $('#modal-tags .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
    $('#modal-tags').modal('show')
    .find('.modal-content')
    .load(url);
}

");?>