<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\helpers\Url;
use appxq\sdii\widgets\GridView;
use appxq\sdii\widgets\ModalForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\ezforms2\models\EzformAutonumSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('ezform', 'Auto Number');
$this->params['breadcrumbs'][] = $this->title;

?>
<?php \appxq\sdii\widgets\CSSRegister::begin([
    //'key' => 'bootstrap-modal',
    //'position' => []
]); ?>

<?php \appxq\sdii\widgets\CSSRegister::end(); ?>
<div class="ezform-autonum-index">

    <div class="sdbox-header">
	<h3><?=  Html::encode($this->title) ?></h3>
    </div>
  
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php  Pjax::begin(['id'=>'ezform-autonum-grid-pjax']);?>
    <?= GridView::widget([
	'id' => 'ezform-autonum-grid',
	'panelBtn' => Html::button(SDHtml::getBtnAdd(), ['data-url'=>Url::to(['ezform-autonum/create']), 'class' => 'btn btn-success btn-sm', 'id'=>'modal-addbtn-ezform-autonum']). ' ' .
		      Html::button(SDHtml::getBtnDelete(), ['data-url'=>Url::to(['ezform-autonum/deletes']), 'class' => 'btn btn-danger btn-sm', 'id'=>'modal-delbtn-ezform-autonum', 'disabled'=>true]),
	'dataProvider' => $dataProvider,
	'filterModel' => $searchModel,
        'columns' => [
	    [
		'class' => 'yii\grid\CheckboxColumn',
		'checkboxOptions' => [
		    'class' => 'selectionEzformAutonumIds'
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
            'label',
            //'ezf_id',
            //'ezf_field_id',
             'digit',
             'prefix',
             'count',
             'suffix',
             'status',
            // 'created_by',
            // 'created_at',
            // 'updated_by',
            // 'updated_at',

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
    'id' => 'modal-ezform-autonum',
    //'size'=>'modal-lg',
]);
?>

<?php  $this->registerJs("

$('#ezform-autonum-grid-pjax').on('click', '#modal-addbtn-ezform-autonum', function() {
    modalEzformAutonum($(this).attr('data-url'));
});

$('#ezform-autonum-grid-pjax').on('click', '#modal-delbtn-ezform-autonum', function() {
    selectionEzformAutonumGrid($(this).attr('data-url'));
});

$('#ezform-autonum-grid-pjax').on('click', '.select-on-check-all', function() {
    window.setTimeout(function() {
	var key = $('#ezform-autonum-grid').yiiGridView('getSelectedRows');
	disabledEzformAutonumBtn(key.length);
    },100);
});

$('#ezform-autonum-grid-pjax').on('click', '.selectionEzformAutonumIds', function() {
    var key = $('input:checked[class=\"'+$(this).attr('class')+'\"]');
    disabledEzformAutonumBtn(key.length);
});

$('#ezform-autonum-grid-pjax').on('dblclick', 'tbody tr', function() {
    var id = $(this).attr('data-key');
    modalEzformAutonum('".Url::to(['ezform-autonum/update', 'id'=>''])."'+id);
});	

$('#ezform-autonum-grid-pjax').on('click', 'tbody tr td a', function() {
    var url = $(this).attr('href');
    var action = $(this).attr('data-action');

    if(action === 'update' || action === 'view') {
	modalEzformAutonum(url);
    } else if(action === 'delete') {
	yii.confirm('".Yii::t('app', 'Are you sure you want to delete this item?')."', function() {
	    $.post(
		url
	    ).done(function(result) {
		if(result.status == 'success') {
		    ". SDNoty::show('result.message', 'result.status') ."
		    $.pjax.reload({container:'#ezform-autonum-grid-pjax'});
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

function disabledEzformAutonumBtn(num) {
    if(num>0) {
	$('#modal-delbtn-ezform-autonum').attr('disabled', false);
    } else {
	$('#modal-delbtn-ezform-autonum').attr('disabled', true);
    }
}

function selectionEzformAutonumGrid(url) {
    yii.confirm('".Yii::t('app', 'Are you sure you want to delete these items?')."', function() {
	$.ajax({
	    method: 'POST',
	    url: url,
	    data: $('.selectionEzformAutonumIds:checked[name=\"selection[]\"]').serialize(),
	    dataType: 'JSON',
	    success: function(result, textStatus) {
		if(result.status == 'success') {
		    ". SDNoty::show('result.message', 'result.status') ."
		    $.pjax.reload({container:'#ezform-autonum-grid-pjax'});
		} else {
		    ". SDNoty::show('result.message', 'result.status') ."
		}
	    }
	});
    });
}

function modalEzformAutonum(url) {
    $('#modal-ezform-autonum .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
    $('#modal-ezform-autonum').modal('show')
    .find('.modal-content')
    .load(url);
}

");?>