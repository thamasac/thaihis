<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\helpers\Url;
use appxq\sdii\widgets\GridView;
use appxq\sdii\widgets\ModalForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\core\models\CorePostsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('core', 'Core Posts');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="posts-index">

    <div class="sdbox-header">
	<h3><?=  Html::encode($this->title) ?></h3>
    </div>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p style="padding-top: 10px;">
	<span class="label label-primary">Notice</span>
	<?= Yii::t('app', 'You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b> or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.') ?>
    </p>

    <?php  Pjax::begin(['id'=>'posts-grid-pjax']);?>
    <?= GridView::widget([
	'id' => 'posts-grid',
	'panelBtn' => Html::a(SDHtml::getBtnAdd(), Url::to(['posts/create']), ['class' => 'btn btn-success btn-sm']). ' ' .
		      Html::button(SDHtml::getBtnDelete(), ['data-url'=>Url::to(['posts/deletes']), 'class' => 'btn btn-danger btn-sm', 'id'=>'modal-delbtn-posts', 'disabled'=>true]),
	'dataProvider' => $dataProvider,
	'filterModel' => $searchModel,
        'columns' => [
	    [
		'class' => 'yii\grid\CheckboxColumn',
		'checkboxOptions' => [
		    'class' => 'selectionCorePostIds'
		],
		'headerOptions' => ['style'=>'text-align: center;'],
		'contentOptions' => ['style'=>'width:40px;text-align: center;'],
	    ],
	    [
		'class' => 'yii\grid\SerialColumn',
		'headerOptions' => ['style'=>'text-align: center;'],
		'contentOptions' => ['style'=>'width:60px;text-align: center;'],
	    ],

            'ID',
            'post_author',
            'post_date',
            'post_date_gmt',
            'post_content:ntext',
            // 'post_title:ntext',
            // 'post_excerpt:ntext',
            // 'post_status',
            // 'comment_status',
            // 'ping_status',
            // 'post_password',
            // 'post_name',
            // 'to_ping:ntext',
            // 'pinged:ntext',
            // 'post_modified',
            // 'post_modified_gmt',
            // 'post_content_filtered:ntext',
            // 'post_parent',
            // 'guid',
            // 'menu_order',
            // 'post_type',
            // 'post_mime_type',
            // 'comment_count',

	    [
		'class' => 'appxq\sdii\widgets\ActionColumn',
		'contentOptions' => ['style'=>'width:80px;text-align: center;'],
		'template' => '{view} {update} {delete}',
	    ],
        ],
    ]); ?>
    <?php  Pjax::end();?>

</div>

<?=  ModalForm::widget([
    'id' => 'modal-posts',
    'size'=>'modal-lg',
]);
?>

<?php  $this->registerJs("
$('#posts-grid-pjax').on('click', '#modal-addbtn-posts', function() {
    modalCorePost($(this).attr('data-url'));
});

$('#posts-grid-pjax').on('click', '#modal-delbtn-posts', function() {
    selectionCorePostGrid($(this).attr('data-url'));
});

$('#posts-grid-pjax').on('click', '.select-on-check-all', function() {
    window.setTimeout(function() {
	var key = $('#posts-grid').yiiGridView('getSelectedRows');
	disabledCorePostBtn(key.length);
    },100);
});

$('#posts-grid-pjax').on('click', '.selectionCorePostIds', function() {
    var key = $('input:checked[class=\"'+$(this).attr('class')+'\"]');
    disabledCorePostBtn(key.length);
});

$('#posts-grid-pjax').on('dblclick', 'tbody tr', function() {
    var id = $(this).attr('data-key');
    location.href = '".Url::to(['posts/update', 'id'=>''])."'+id;
});	

$('#posts-grid-pjax').on('click', 'tbody tr td a', function() {
    var url = $(this).attr('href');
    var action = $(this).attr('data-action');

    if(action === 'update' || action === 'view') {
	location.href = url;
    } else if(action === 'delete') {
	yii.confirm('".Yii::t('app', 'Are you sure you want to delete this item?')."', function() {
	    $.post(
		url
	    ).done(function(result) {
		if(result.status == 'success') {
		    ". SDNoty::show('result.message', 'result.status') ."
		    $.pjax.reload({container:'#posts-grid-pjax'});
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

function disabledCorePostBtn(num) {
    if(num>0) {
	$('#modal-delbtn-posts').attr('disabled', false);
    } else {
	$('#modal-delbtn-posts').attr('disabled', true);
    }
}

function selectionCorePostGrid(url) {
    yii.confirm('".Yii::t('app', 'Are you sure you want to delete these items?')."', function() {
	$.ajax({
	    method: 'POST',
	    url: url,
	    data: $('.selectionCorePostIds:checked[name=\"selection[]\"]').serialize(),
	    dataType: 'JSON',
	    success: function(result, textStatus) {
		if(result.status == 'success') {
		    ". SDNoty::show('result.message', 'result.status') ."
		    $.pjax.reload({container:'#posts-grid-pjax'});
		} else {
		    ". SDNoty::show('result.message', 'result.status') ."
		}
	    }
	});
    });
}

function modalCorePost(url) {
    $('#modal-posts .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
    $('#modal-posts').modal('show')
    .find('.modal-content')
    .load(url);
}

");?>