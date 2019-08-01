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
use kartik\tabs\TabsX;

Yii::$app->controller->id = "update-tool";
$this->title = Yii::t('ezmodule', 'Update tools');
//$this->params['breadcrumbs'][] = ['label' => Yii::t('ezmodule', 'System Config'), 'url' => ['/ezmodules/default/index']];
$this->params['breadcrumbs'][] = $this->title;
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

echo ModalForm::widget([
    'id' => 'modal-loading-update',
    'size' => 'modal-xl',
    'tabindexEnable' => FALSE,
    'clientOptions' => ['backdrop' => 'static', 'keyboard' => false],
]);

$activetab = 1;
$items = [
    [
        'label' => 'Ezforms',
        'headerOptions' => ['style' => 'font-weight:bold', 'class' => 'tab_update_tool', 'id' => 'update_ezforms', 'url' => Url::to(['/study_manage/update-tool/ezforms'])],
        'active' => $activetab == 1 ? true : false,
    ],
    [
        'label' => 'Ezmodules',
        'headerOptions' => ['style' => 'font-weight:bold', 'class' => 'tab_update_tool', 'id' => 'update_modules', 'url' => Url::to(['/study_manage/update-tool/ezmodules'])],
        'active' => $activetab == 2 ? true : false,
    ],
    [
        'label' => 'Ezwidgets',
        'headerOptions' => ['style' => 'font-weight:bold', 'class' => 'tab_update_tool', 'id' => 'update_widgets', 'url' => Url::to(['/study_manage/update-tool/ezmodules'])],
        'active' => $activetab == 3 ? true : false,
    ]
];
?>
<?=
\kartik\tabs\TabsX::widget([
    'position' => TabsX::POS_ABOVE,
    'align' => TabsX::ALIGN_LEFT,
    'encodeLabels' => false,
    //'enableStickyTabs' => true,
    'items' => $items,
]);
?>
<div class="col-md-6 sdbox-col"> 
    <?= Html::textInput('search_input', '', ['class' => 'form-control', 'id' => 'search_input', 'placeholder' => 'Search input']) ?>
</div>
<div class="clearfix"></div><br/>
<div id="display-content">

</div>
<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>
<script>
    $(function () {
        var url = "/study_manage/update-tool/ezforms";
        var div = $('#display-content');
        div.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        $.get(url, {}, function (result) {
            div.html(result);
        });
    });

    var myVar = setInterval(countProcess, 1000);
    
    $(document).on('click', '.btn_start_update', function () {
        var url = '/study_manage/update-tool/update-ezform';
        var ezf_id = $(this).attr('data-ezf_id');
        var ezf_version = $(this).attr('data-ezf_version');
        var modal = $('#modal-loading-update');
        var btn = $(this);
        btn.html('<span class="sdloader"><i class="sdloader-icon"></i></span> Waiting...');
        btn.addClass('btn-warning');
        btn.prop('disabled', true);
        btn.removeClass('btn-success');
        modal.modal();
        modal.find('.modal-content').html('Timeimg on process <label id="count-prodess">0</label> ... <div class="sdloader"><i class="sdloader-icon"></i></div>');
        $.get(url, {ezf_id: ezf_id, ezf_version: ezf_version}, function (result) {
            clearInterval(countProcess);
            btn.html('Start to Update');
            btn.prop('disabled', false);
            btn.removeClass('btn-warning');
            btn.addClass('btn-success');
            modal.find('.modal-content').html(result);
        });

    });
    
    $(document).on('click', '.btn_start_update_module', function () {
        var url = '/study_manage/update-tool/update-ezmodule';
        var ezm_id = $(this).attr('data-ezm_id');
        var modal = $('#modal-loading-update');
        var btn = $(this);
        btn.html('<span class="sdloader"><i class="sdloader-icon"></i></span> Waiting...');
        btn.addClass('btn-warning');
        btn.prop('disabled', true);
        btn.removeClass('btn-success');
        modal.modal();
        modal.find('.modal-content').html('Timeimg on process <label id="count-prodess">0</label> ... <div class="sdloader"><i class="sdloader-icon"></i></div>');
        $.get(url, {ezm_id: ezm_id}, function (result) {
            clearInterval(countProcess);
            var time = $('#count-prodess').html();
            btn.html('Start to Update');
            btn.prop('disabled', false);
            btn.removeClass('btn-warning');
            btn.addClass('btn-success');
            modal.find('.modal-content').html(result);
        });

    });
    
    function countProcess(){
        var old = $('#count-prodess').html();
        var newV = Number(old)+1;
        $('#count-prodess').html(newV);
    }

    $('#search_input').on('change', function () {
        var url = "/study_manage/update-tool/ezforms";
        $('.tab_update_tool').each(function (i, e) {
            if ($(e).hasClass('active')) {
                url = $(e).attr('url');
            }
        });


        var div = $('#display-content');
        var search_input = $(this).val();
        div.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        $.get(url, {search_input: search_input}, function (result) {
            div.empty();
            div.html(result);
        });
    });

    $('#update_ezforms').click(function () {
        var url = "/study_manage/update-tool/ezforms";
        var div = $('#display-content');
        div.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        $.get(url, {}, function (result) {
            div.empty();
            div.html(result);
        });
    });
    $('#update_modules').click(function () {
        var url = "/study_manage/update-tool/ezmodules";
        var div = $('#display-content');
        div.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        $.get(url, {}, function (result) {
            div.empty();
            div.html(result);
        });
    });
    $('#update_widgets').click(function () {
        var url = "/study_manage/update-tool/ezwidgets";
        var div = $('#display-content');
        div.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        $.get(url, {}, function (result) {
            div.empty();
            div.html(result);
        });
    });

    $(document).on('click', '#btn_module_management', function () {
        var modal = $('#modal-module-view');
        var url = '/study_manage/ezmodule-study/view';
        modal.modal();
        modal.find('.modal-content').html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        modal.find('.modal-content').load(url);

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


</script>
<?php \richardfan\widget\JSRegister::end(); ?>