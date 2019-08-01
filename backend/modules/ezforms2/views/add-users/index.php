<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\helpers\Url;
use appxq\sdii\widgets\GridView;
use appxq\sdii\widgets\ModalForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\ezforms2\models\ProfileTccSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
 
$this->title = Yii::t('rbac-admin', 'New user Thai Care Cloud');
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="profile-tcc-index">

    <div class="sdbox-header">
	 
    </div>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
<?= $this->render('_menu') ?>
    <?php  Pjax::begin(['id'=>'profile-tcc-grid-pjax']);?>
    <?= GridView::widget([
	'id' => 'profile-tcc-grid',
	'panelBtn' => Html::button(SDHtml::getBtnAdd(), ['data-url'=>Url::to(['add-users/create']), 'class' => 'btn btn-success btn-sm', 'id'=>'modal-addbtn-profile-tcc']). ' ' .
		      Html::button(SDHtml::getBtnDelete(), ['data-url'=>Url::to(['add-users/deletes']), 'class' => 'btn btn-danger btn-sm', 'id'=>'modal-delbtn-profile-tcc', 'disabled'=>true]),
	'dataProvider' => $dataProvider,
//	'filterModel' => $searchModel,
        'columns' => [
	    [
		'class' => 'yii\grid\CheckboxColumn',
		'checkboxOptions' => [
		    'class' => 'selectionProfileTccIds'
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
                'attribute'=>'firstname',
                'label'=> Yii::t('user', 'Name'),
                'value'=>function($model){
                    return $model->firstname.' '.$model->lastname;
                }
                
            ],
            [
                'attribute'=>'sitecode',
                'label'=> Yii::t('rbac-admin', 'Sitecode'),
                'value'=>function($model){
                    $sql="SELECT * FROM zdata_sitecode WHERE site_name=:site_name";
                    $data = Yii::$app->db->createCommand($sql, [":site_name"=>$model->sitecode])->queryOne();
                    return $data['site_detail'];
                }
                
            ],
            
            //'name',
            //'public_email:email',
            //'gravatar_email:email',
            //'gravatar_id',
            // 'location',
            // 'website',
            // 'bio:ntext',
            // 'title',
            // 'dob',
            // 'timezone',
            // 'sitecode',
            // 'firstname',
            // 'lastname',
            // 'department',
            // 'position',
            // 'avatar_path',
            // 'avatar_base_url:url',
            // 'certificate',
            // 'site',

	    [
		'class' => 'appxq\sdii\widgets\ActionColumn',
		'contentOptions' => ['style'=>'width:80px;text-align: center;'],
		'template' => '{update} {delete}',
                'buttons' => [
                    'update' => function($url, $model, $key) {     // render your custom button
                         return  Html::a("<i class='glyphicon glyphicon-pencil'></i>", Url::to(['/user/admin/update-profile','id'=>$model->user_id]), ['data-action'=>'update']);
                    }
                ]
	    ],
        ],
    ]); ?>
    <?php  Pjax::end();?>

</div>

<?=  ModalForm::widget([
    'id' => 'modal-profile-tcc',
    'size'=>'modal-lg',
    'tabindexEnable' => FALSE,
]);
?>

<?php  $this->registerJs("
$('#profile-tcc-grid-pjax').on('click', '#modal-addbtn-profile-tcc', function() {
    
    modalProfileTcc($(this).attr('data-url'));
});

$('#profile-tcc-grid-pjax').on('click', '#modal-delbtn-profile-tcc', function() {

    selectionProfileTccGrid($(this).attr('data-url'));
});

$('#profile-tcc-grid-pjax').on('click', '.select-on-check-all', function() {

    window.setTimeout(function() {
	var key = $('#profile-tcc-grid').yiiGridView('getSelectedRows');
	disabledProfileTccBtn(key.length);
    },100);
});

$('#profile-tcc-grid-pjax').on('click', '.selectionProfileTccIds', function() {
    
    var key = $('input:checked[class=\"'+$(this).attr('class')+'\"]');
    disabledProfileTccBtn(key.length);
});

//$('#profile-tcc-grid-pjax').on('dblclick', 'tbody tr', function() {
//    var id = $(this).attr('data-key');
//    modalProfileTcc('".Url::to(['add-users/update', 'id'=>''])."'+id);
//});	

$('#profile-tcc-grid-pjax').on('click', 'tbody tr td a', function() {
     
    var url = $(this).attr('href');
    var action = $(this).attr('data-action');

    if(action === 'update' || action === 'view') {
        location.href = url;return false;
	modalProfileTcc(url);
    } else if(action === 'delete') {
	yii.confirm('".Yii::t('app', 'Are you sure you want to delete this item?')."', function() {
	    $.post(
		url
	    ).done(function(result) {
		if(result.status == 'success') {
		    ". SDNoty::show('result.message', 'result.status') ."
		    $.pjax.reload({container:'#profile-tcc-grid-pjax'});
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

function disabledProfileTccBtn(num) {
    if(num>0) {
	$('#modal-delbtn-profile-tcc').attr('disabled', false);
    } else {
	$('#modal-delbtn-profile-tcc').attr('disabled', true);
    }
}

function selectionProfileTccGrid(url) {
    yii.confirm('".Yii::t('app', 'Are you sure you want to delete these items?')."', function() {
	$.ajax({
	    method: 'POST',
	    url: url,
	    data: $('.selectionProfileTccIds:checked[name=\"selection[]\"]').serialize(),
	    dataType: 'JSON',
	    success: function(result, textStatus) {
		if(result.status == 'success') {
		    ". SDNoty::show('result.message', 'result.status') ."
		    $.pjax.reload({container:'#profile-tcc-grid-pjax'});
		} else {
		    ". SDNoty::show('result.message', 'result.status') ."
		}
	    }
	});
    });
}

function modalProfileTcc(url) {
    $('#modal-profile-tcc .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
    $('#modal-profile-tcc').modal('show')
    .find('.modal-content')
    .load(url);
}

");?>