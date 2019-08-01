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
    [
        'class' => 'yii\grid\SerialColumn',
        'headerOptions' => ['style' => 'text-align: center;width:3%;'],
        'contentOptions' => ['style' => 'width:60px;text-align: center;'],
    ],
    [
        'attribute' => 'ezf_icon',
        'value' => function ($data) {
            return backend\modules\ezforms2\classes\EzfUiFunc::getEzformIcon($data, 25);
        },
        'format' => 'raw',
        'headerOptions' => ['style' => 'text-align: center;', 'width' => '3%'],
        'contentOptions' => ['style' => 'width:40px; text-align: center;'],
        'filter' => '',
    ],
    [
        'attribute' => 'ezf_name',
        'format' => 'raw',
        'headerOptions' => ['style' => 'text-align: center;', 'width' => '20%'],
        'contentOptions' => ['style' => 'width:40px; text-align: left;'],
        'filter' => '',
    ],
    [
        'attribute' => 'ezf_detail',
        'format' => 'raw',
        'headerOptions' => ['style' => 'text-align: center;', 'width' => '20%'],
        'contentOptions' => ['style' => 'width:40px; text-align: left;'],
        'filter' => '',
    ],
    [
        'header' => 'Latest Update',
        'value' => function ($data) {
            $update_log = backend\modules\study_manage\classes\UpdateQuery::getLatestDate($data['ezf_id']);
            return $update_log['latestDate'] . ' ' ;
        },
        'format' => 'raw',
        'headerOptions' => ['style' => 'text-align: center;', 'width' => '20%'],
        'contentOptions' => ['style' => 'width:40px; text-align: left;'],
        'filter' => '',
    ],
    [
        'header' => 'Action',
        'value' => function ($data) {
            return Html::button('Updated history', ['class' => 'btn btn-info btn_update_history', 'data-ezf_id' => $data['ezf_id'], 'data-pjax' => 'w0']).' '.Html::button('Start to update', ['class' => 'btn btn-success btn_start_update', 'data-ezf_id' => $data['ezf_id'], 'data-pjax' => 'w0']);
        },
        'format' => 'raw',
        'headerOptions' => ['style' => 'text-align: center;', 'width' => '20%'],
        'contentOptions' => ['style' => 'width:40px; text-align: center;'],
        'filter' => '',
    ],
];

//$this->params['breadcrumbs'][] = $this->title;

$user_id = Yii::$app->user->id;
?>
<?php backend\modules\ezforms2\classes\EzfStarterWidget::begin() ?>

<div class="clearfix"></div>
<div class="ezmodule-index" >

<?php // echo $this->render('_search', ['model' => $searchModel]);       ?>

    <?php //Pjax::begin(['id' => 'ezform-grid-pjax']); ?>
    <?=
    GridView::widget([
        'id' => 'ezform-grid',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $column,
    ]);
    ?>
    <?php //Pjax::end(); ?>
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

"); ?>
<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>
<script>
    $(document).on('click', '.btn_start_update', function () {
        var url = '/study_manage/update-tool/update-ezform';
        var ezf_id = $(this).attr('data-ezf_id');
        var btn = $(this);
        btn.html('<span class="sdloader"><i class="sdloader-icon"></i></span> Waiting...');
        btn.addClass('btn-warning');
        btn.prop('disabled', true);
        btn.removeClass('btn-success');
        $.get(url, {ezf_id: ezf_id}, function (result) {
            btn.html('Start to Update');
            btn.prop('disabled', false);
            btn.removeClass('btn-warning');
            btn.addClass('btn-success');
        });

    });

    $(document).on('click', '.btn_ezm_name', function () {
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
        window.location.href = '/study_manage/ezmodule-study/index';
    });
    
    $('#ezform-grid .pagination a').on('click', function () {
        getReloadDiv($(this).attr('href'), 'display-content');
        return false;
    });
    
    $('#ezform-grid tr a').on('click', function () {
        getReloadDiv($(this).attr('href'), 'display-content');
        return false;
    });
    
    function getReloadDiv(url, div) {
        $.get(url, {}, function (result) {
            $('#' + div).html(result);
        });
    }
</script>
<?php \richardfan\widget\JSRegister::end(); ?>