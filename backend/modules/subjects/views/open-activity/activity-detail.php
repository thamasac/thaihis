<?php

use yii\helpers\Html;
use yii\helpers\Url;
use backend\modules\ezforms2\classes\EzfHelper;
use backend\modules\subjects\classes\SubjectManagementQuery;
use backend\modules\ezforms2\classes\EzfAuthFuncManage;
use appxq\sdii\widgets\ModalForm;
use backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\subjects\classes\JKDate;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$profile_form = backend\modules\ezforms2\classes\EzfQuery::getEzformOne($subject_profile_ezf);
$data = SubjectManagementQuery::GetTableData($profile_form, ['id' => $data_id], 'one');

$dataSchedule= SubjectManagementQuery::getWidgetById($schedule_id);
$optionSchedule = \appxq\sdii\utils\SDUtility::string2Array($dataSchedule['options']);
$visit_random = $optionSchedule['22222'];
$detail_form = backend\modules\ezforms2\classes\EzfQuery::getEzformOne($subject_detail_ezf);
$dataDetail = SubjectManagementQuery::GetTableData($detail_form, ['target' => $data_id, 'visit_name'=>$visit_random['form_name']], 'one');

?>
<div class="modal-header">
    <h3>Subject Activity Detail</h3>
</div>
<div class="modal-body">
    <input type="hidden" value="<?= $schedule_id ?>" name="schedule_widget_id" id="schedule_widget_id">
    <input type="hidden" value="<?= $module_id ?>" name="data-module_id" id="data-module_id">
    <input type="hidden" value="<?= $data_id ?>" name="data-data_id" id="data-data_id">
    <input type="hidden" value="<?= $inform_date ?>" name="data-inform_date" id="data-inform_date">
    <input type="hidden" value="<?= $dataDetail['group_name'] ?>" name="data-data_id" id="data-group_id">
    <label>Subject Number : <?= $data[$field_subject] ?></label>
<br/>
    <?php

    if (EzfAuthFuncManage::auth()->accessManage($module_id,1)) {
        echo \backend\modules\ezforms2\classes\EzfHelper::btn($subject_detail_ezf)
                ->reloadDiv('display-detail')
                ->modal($modal)
                ->label('<i class="fa fa-plus"></i> ' . Yii::t('subjects', 'Open Activity'))
                ->target($data_id)
                ->options(['class' => 'btn btn-success pull-left', 'ezf_'])
                ->buildBtnAdd();
    }
    ?>
    <?php
//
//    backend\modules\ezforms2\classes\EzfStarterWidget::begin();
//    echo EzfHelper::ui($subject_profile_ezf)->reloadDiv($reloadDiv)->ezf_id($subject_profile_ezf)->target($data_id)->disabled(true)->data_column($profile_column)->default_column(false)->buildGrid();
//    backend\modules\ezforms2\classes\EzfStarterWidget::end();
//        
    $columns = [
        [
            'class' => 'yii\grid\SerialColumn',
            'headerOptions' => ['style' => 'text-align: center;'],
            'contentOptions' => ['style' => 'min-width:60px;width:60px;text-align: center;'],
        ],
    ];
    foreach ($profile_column as $key => $value) {
        $field = EzfQuery::getFieldByName($subject_profile_ezf, $value);
        $headName = $field['ezf_field_label'];
        $columns[] = [
            'attribute' => $value,
            'header'=>$headName,
            'format' => 'raw',
            'value' => function ($data) use($value) {
                if(JKDate::checkFormatDate($data[$value])){
                    return "<span>".JKDate::convertDate($data[$value])."</span>";
                }else{
                    return "<span data-toggle=\"tooltip\" title=\"{$data[$value]}\">{$data[$value]}</span>";
                }
            },
            'headerOptions' => ['style' => 'text-align: center;'],
            'contentOptions' => ['style' => 'width:100px;text-align: center;'],
        ];
    }

    if (isset($default_column) && $default_column) {
        $columns[] = [
            'attribute' => 'xsourcex',
            'format' => 'raw',
            'value' => function ($data) {
                return "<span class=\"label label-success\" data-toggle=\"tooltip\" title=\"{$data['sitename']}\">{$data['xsourcex']}</span>";
            },
            'headerOptions' => ['style' => 'text-align: center;'],
            'contentOptions' => ['style' => 'width:100px;text-align: center;'],
        ];
        $columns[] = [
            'attribute' => 'userby',
            'contentOptions' => ['style' => 'width:200px;'],
            'filter' => '',
        ];
        $columns[] = [
            'attribute' => 'rstat',
            'format' => 'raw',
            'value' => function ($data) {
                $alert = 'label-default';
                if ($data['rstat'] == 0) {
                    $alert = 'label-info';
                } elseif ($data['rstat'] == 1) {
                    $alert = 'label-warning';
                } elseif ($data['rstat'] == 2) {
                    $alert = 'label-success';
                } elseif ($data['rstat'] == 3) {
                    $alert = 'label-danger';
                }

                $rstat = backend\modules\core\classes\CoreFunc::itemAlias('rstat', $data['rstat']);
                return "<h4 style=\"margin: 0;\"><span class=\"label $alert\">$rstat</span></h4>";
            },
            'headerOptions' => ['style' => 'text-align: center;'],
            'contentOptions' => ['style' => 'width:120px;text-align: center;'],
            'filter' => Html::activeDropDownList($searchModel, 'rstat', backend\modules\core\classes\CoreFunc::itemAlias('rstat'), ['class' => 'form-control', 'prompt' => 'All']),
        ];
    }
//    
    ?>
    <br/><br/>
    <?=
    yii\grid\GridView::widget([
        'id' => "$reloadDiv-profile-grid",
        'dataProvider' => $dataProvider,
        'filterModel' => isset($searchModel)?$searchModel:null,
        'columns' => $columns,
    ]);
    ?>
    <br/><br/>
    <div id="display-detail" data-url="<?= Url::to([
        '/subjects/open-activity/grid-detail',
        'data_id'=>$data_id,
        'reloadDiv'=>'display-detail',
        'subject_profile_ezf'=>$subject_profile_ezf,
        'field_subject'=>$field_subject,
        'schedule_id'=>$schedule_id,
        'subject_detail_ezf'=>$subject_detail_ezf,
        'profile_column'=>base64_encode(json_encode($profile_column)),
        'detail_column'=>base64_encode(json_encode($detail_column2)),
        'detail_column2'=> base64_encode(json_encode($detail_column2)),
        'modal'=>$modal,
        'module_id'=>$module_id,
    ])?>" ></div>

</div>
<div class="modal-footer">
    <button data-dismiss="modal" class="btn btn-default pull-right"><?= Yii::t('subject', 'Close') ?></button>
</div>

<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>
<script>
    $(function () {
        var showDetail = $('#display-detail');
        showDetail.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        var data_id = '<?= $data_id ?>';
        var url = showDetail.attr('data-url');
        $.get(url, function (result) {
            showDetail.html(result);
        })
    });
    
    $('#modal-show-formlist').on('hidden.bs.modal',function(){
        getReloadDiv($('#display-detail').attr('data-url'), 'display-detail');
    });
    
    $('#<?=$modal?>').on('hidden.bs.modal',function(){
        $('body').addClass('modal-open');
    });
    
    $('#<?=$reloadDiv?>-profile-grid .pagination a').on('click', function () {
        getReloadDiv($(this).attr('href'), '<?=$reloadDiv?>');
        return false;
    });
</script>
<?php \richardfan\widget\JSRegister::end(); ?>