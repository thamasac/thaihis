<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\helpers\Url;
use appxq\sdii\widgets\GridView;
use appxq\sdii\widgets\ModalForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\manageproject\models\SystemLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'System Logs');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="system-log-index">

    <div class="sdbox-header">
	<h3><?=  Html::encode($this->title) ?></h3>
    </div>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php  Pjax::begin(['id'=>'system-log-grid-pjax']);?>
    <?= GridView::widget([
	'id' => 'system-log-grid',
	'panelBtn' => 
		      Html::button(SDHtml::getBtnDelete(), ['data-url'=>Url::to(['/manageproject/system-log/deletes']), 'class' => 'btn btn-danger btn-sm', 'id'=>'modal-delbtn-system-log', 'disabled'=>true]),
	'dataProvider' => $dataProvider,
	'filterModel' => $searchModel,
        'columns' => [
	    [
		'class' => 'yii\grid\CheckboxColumn',
		'checkboxOptions' => [
		    'class' => 'selectionSystemLogIds'
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
                'contentOptions' => ['style'=>'width:180px;text-align: center;'],
                'attribute'=>'create_date', 
                'value'=>function($model){
                    return $model->create_date;
                },
                'filter' => \kartik\daterange\DateRangePicker::widget([
                        'model' => $searchModel,
                        'attribute' => 'create_date',
                        'convertFormat' => true,
                        'pluginOptions' => [
                            'locale' => [
                                'format' => 'Y-m-d'
                            ],
                        ],
                ]),
            ], 
            [
                'contentOptions' => ['style'=>'width:150px;text-align: center;'],
                'attribute'=>'create_by',
                'value'=>function($model){
                    $name = '';
                    if(isset($model->profiles->name) && $model->profiles->name == ''){
                        $name = $model->profiles->name;
                    }else{
                        if(isset($model->profiles->firstname) && isset($model->profiles->lastname) && ($model->profiles->firstname != '' && $model->profiles->lastname != '')){
                            $name = $model->profiles->firstname.' '.$model->profiles->lastname;
                        }else{
                            $name = isset($model->profiles->firstname)?$model->profiles->firstname:'';
                        }
                        
                    }
                    return $name;
                }
            ], 
            'action',
            [
                'contentOptions' => ['style'=>'width:500px;text-align: left;overflow-y:scroll;'],
                'attribute'=>'detail',
                'format'=>'raw',
                'value'=>function($model){
                    return "<div style='width:500px;max-height: 100px;'>".Html::encode($model->detail)."</div>";
                }
            ],  
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
    'id' => 'modal-system-log',
    'size'=>'modal-lg',
]);
?>

<?php  \richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]); ?>
<script>
// JS script
$('#system-log-grid-pjax').on('click', '#modal-addbtn-system-log', function() {
    modalSystemLog($(this).attr('data-url'));
});

$('#system-log-grid-pjax').on('click', '#modal-delbtn-system-log', function() {
    selectionSystemLogGrid($(this).attr('data-url'));
});

$('#system-log-grid-pjax').on('click', '.select-on-check-all', function() {
    window.setTimeout(function() {
	var key = $('#system-log-grid').yiiGridView('getSelectedRows');
	disabledSystemLogBtn(key.length);
    },100);
});

$('#system-log-grid-pjax').on('click', '.selectionSystemLogIds', function() {
    var key = $('input:checked[class=\"'+$(this).attr('class')+'\"]');
    disabledSystemLogBtn(key.length);
});

$('#system-log-grid-pjax').on('dblclick', 'tbody tr', function() {
    var id = $(this).attr('data-key');
    modalSystemLog('<?= Url::to(['system-log/update', 'id'=>''])?>'+id);
});	

$('#system-log-grid-pjax').on('click', 'tbody tr td a', function() {
    var url = $(this).attr('href');
    var action = $(this).attr('data-action');

    if(action === 'update' || action === 'view') {
	modalSystemLog(url);
    } else if(action === 'delete') {
	yii.confirm('<?= Yii::t('app', 'Are you sure you want to delete this item?')?>', function() {
	    $.post(
		url
	    ).done(function(result) {
		if(result.status == 'success') {
		    <?= SDNoty::show('result.message', 'result.status')?>
		    $.pjax.reload({container:'#system-log-grid-pjax'});
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

function disabledSystemLogBtn(num) {
    if(num>0) {
	$('#modal-delbtn-system-log').attr('disabled', false);
    } else {
	$('#modal-delbtn-system-log').attr('disabled', true);
    }
}

function selectionSystemLogGrid(url) {
    yii.confirm('<?= Yii::t('app', 'Are you sure you want to delete these items?')?>', function() {
	$.ajax({
	    method: 'POST',
	    url: url,
	    data: $('.selectionSystemLogIds:checked[name=\"selection[]\"]').serialize(),
	    dataType: 'JSON',
	    success: function(result, textStatus) {
		if(result.status == 'success') {
		    <?= SDNoty::show('result.message', 'result.status')?>
		    $.pjax.reload({container:'#system-log-grid-pjax'});
		} else {
		    <?= SDNoty::show('result.message', 'result.status')?>
		}
	    }
	});
    });
}

function modalSystemLog(url) {
    $('#modal-system-log .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
    $('#modal-system-log').modal('show')
    .find('.modal-content')
    .load(url);
}
</script>
<?php  \richardfan\widget\JSRegister::end(); ?>