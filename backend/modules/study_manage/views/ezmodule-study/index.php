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
use backend\modules\ezforms2\classes\EzfHelper;

Yii::$app->controller->id = "ezmodule-study";
/* @var $this yii\web\View */
/* @var $searchModel backend\modules\ezmodules\models\EzmoduleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
echo ModalForm::widget([
    'id' => 'modal-ezform-study',
    'size' => 'modal-xl'
]);
echo ModalForm::widget([
    'id' => 'modal-module-view',
    'size' => 'modal-lg'
]);
echo ModalForm::widget([
    'id' => 'modal-app-info',
    'size' => 'modal-lg'
]);


$column = [
//    [
//        'class' => 'yii\grid\SerialColumn',
//        'headerOptions' => ['style' => 'text-align: center;'],
//        'contentOptions' => ['style' => 'width:60px;text-align: center;'],
//    ],
    [
        'header' => 'Order',
        'value' => function ($data) {
            $study_check = backend\modules\study_manage\classes\StudyQuery::getModuleStudyTemplates($data['ezm_id']);
            return $study_check['ezm_order'];
        },
        'format' => 'raw',
        'headerOptions' => ['style' => 'text-align: center;', 'width' => '2%'],
        'contentOptions' => ['style' => 'width:40px; text-align: center;'],
        'filter' => '',
    ],
    [
        'attribute' => 'ezm_icon',
        'value' => function ($data) {
            if (isset($data['ezm_icon']) && !empty($data['ezm_icon'])) {
                return Html::img(Yii::getAlias('@storageUrl/module') . '/' . $data['ezm_icon'], ['width' => 25, 'class' => 'img-rounded']);
            } else {
                return Html::img(ModuleFunc::getNoIconModule(), ['width' => 25, 'class' => 'img-rounded']);
            }
        },
        'format' => 'raw',
        'headerOptions' => ['style' => 'text-align: center;', 'width' => '3%'],
        'contentOptions' => ['style' => 'width:40px; text-align: center;'],
        'filter' => '',
    ],
    [
        'attribute' => 'ezm_name',
        'value' => function ($data) {
            return Html::a($data['ezm_name'], 'javascript:void(0)', ['class' => 'btn_ezm_name', 'data-ezm_id' => $data['ezm_id'],'data-pjax'=>'w0']);
        },
        'format' => 'raw',
        'headerOptions' => ['style' => 'text-align: center;', 'width' => '20%'],
        'contentOptions' => ['style' => 'width:40px; text-align: left;'],
        'filter' => '',
    ],
];

foreach ($studyAll as $key => $value) {
    
    $long_name = isset($value['template_title'])?$value['template_title']:'';
    $column[] = [
        'header' => '<label data-toggle="tooltip" title="' . $long_name . '">' . $value['acronym'] . '</label> ' . EzfHelper::btn('1529662517012089700')->label('<i class="fa fa-pencil"></i>')->options(['class' => 'btn btn-primary btn-xs'])->modal('modal-ezform-study')->reloadDiv('study_content')->buildBtnEdit($value['id']),
        'value' => function ($data)use($value) {
            $check = \backend\modules\study_manage\classes\StudyQuery::getModuleOneOfStudy($value['id'], $data['ezm_id'] . '');
            if ($check) {
                return "<label style='color:green;font-size:20px;'><i class='fa fa-check'></i></label>";
            } else {
                return "";
            }
        },
        'format' => 'raw',
        'headerOptions' => ['style' => 'text-align: center;', 'width' => '5%'],
        'contentOptions' => ['style' => 'width:40px; text-align: center;'],
    ];
}

$this->title = Yii::t('ezmodule', 'Study Templates: Recommended modules for each study design');
$this->params['breadcrumbs'][] = ['label' => Yii::t('ezmodule', 'EzModule'), 'url' => ['/ezmodules/default/index']];
$this->params['breadcrumbs'][] = $this->title;
//$this->params['breadcrumbs'][] = $this->title;

$user_id = Yii::$app->user->id;
$template = \backend\modules\ezmodules\classes\ModuleQuery::getTemplate($user_id);
?>
<?php backend\modules\ezforms2\classes\EzfStarterWidget::begin() ?>

<div class="clearfix"></div>
<div class="ezmodule-index" >

    <?php // echo $this->render('_search', ['model' => $searchModel]);     ?>

    <?php Pjax::begin(['id' => 'ezmodule-grid-pjax']); ?>
    <?=
    GridView::widget([
        'id' => 'ezmodule-grid',
        'panelBtn'=>EzfHelper::btn('1529662517012089700')->label('<i class="fa fa-plus"></i> Add Study Template')->options(['class' => 'btn btn-success pull-right'])->modal('modal-ezform-study')->reloadDiv('study_content')->buildBtnAdd().
        Html::button('<i class="fa fa-list"></i> Module Mangement ', ['class' => 'btn btn-primary pull-right', 'style' => 'margin-right:10px;', 'id' => 'btn_module_management']),
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $column,
    ]);
    ?>
    <?php Pjax::end(); ?>
    <?php backend\modules\ezforms2\classes\EzfStarterWidget::end() ?>
</div>

<?=
ModalForm::widget([
    'id' => 'modal-ezmodule',
    'size' => 'modal-lg',
    'tabindexEnable' => FALSE,
]);
?>

<?php $this->registerJs("
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
<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>
<script>
    $(document).on('click','#btn_module_management', function () {
        var modal = $('#modal-module-view');
        var url = '/study_manage/ezmodule-study/view';
        modal.modal();
        modal.find('.modal-content').html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        modal.find('.modal-content').load(url);

    });

    $(document).on('click','.btn_ezm_name', function () {
        var modal = $('#modal-app-info');
        var ezm_id = $(this).attr('data-ezm_id');
        var url = '/ezmodules/default/info-app?id=' + ezm_id;
        modal.modal();
        modal.find('.modal-content').html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        modal.find('.modal-content').load(url);

    });

    $('#modal-ezform-study').on('hidden.bs.modal', function () {
        $.pjax.reload({container: '#ezmodule-grid-pjax'});
    });

    $('#modal-module-view').on('hidden.bs.modal', function () {
        window.location.href='/study_manage/ezmodule-study/index';
    });
</script>
<?php \richardfan\widget\JSRegister::end(); ?>