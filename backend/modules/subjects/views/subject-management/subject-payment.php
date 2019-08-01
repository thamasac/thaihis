<?php

use backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\ezforms2\classes\EzfFunc;
use backend\modules\ezforms2\classes\EzfHelper;
use yii\helpers\Html;
use appxq\sdii\widgets\ModalForm;
use \appxq\sdii\helpers\SDNoty;
use backend\modules\subjects\classes\SubjectManagementQuery;
use kartik\tabs\TabsX;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$procedure_widget_ref = SubjectManagementQuery::getWidgetById($procedure_id);
$schedule_widget_ref = SubjectManagementQuery::getWidgetById($schedule_id);

$procedureOptions = appxq\sdii\utils\SDUtility::string2Array($procedure_widget_ref['options']);
$scheduleOptions = appxq\sdii\utils\SDUtility::string2Array($schedule_widget_ref['options']);

$groupForm = EzfQuery::getEzformOne($scheduleOptions['group_ezf_id']);
$groupData = SubjectManagementQuery::GetTableData($groupForm);
$groupList = [];
if ($groupData) {
    foreach ($groupData as $val) {
        $valNew['id'] = $val['id'];
        $valNew['group_name'] = $val['group_name'];
        $groupList[] = $valNew;
    }
}
?>
<style>
    .active-select{
        background-color: #fbf069;
    } 
</style>
<div id="display_subject_payment" data-url="<?=
\yii\helpers\Url::to([
    '/subjects/subject-management/subject-payment',
    'widget_id' => $widget_id,
    'schedule_id' => $schedule_id,
    'procedure_id' => $procedure_id,
    'financial_id' => $financial_id,
    'options' => $options,
    'reloadDiv' => $reloadDiv,
    'module_id' => $module_id,
    'ezform_main' => $ezform_main,
    'subDisplay' => $subDisplay,
    'subject_payment_widget' => $subject_payment_widget,
])
?>">
    <div class="col-md-2" id="show-subject-list" >
        <?=
        $this->render('subject-payment-subject', [
            'data' => $data,
            'subDisplay' => $subDisplay,
            'groupData' => $groupData,
            'thisPage' => $thisPage,
            'pageLimit' => $pageLimit,
            'pageAmt' => $pageAmt,
            'ezform_main' => $ezform_main,
            'reloadDiv' => 'display_subject_payment',
        ]);
        ?>
    </div>

    <div class="col-md-2 sdbox-col" id="show-visit">

    </div>

    <div class="col-md-8 sdbox-col" id="show-data-table">

    </div>
    <div class="clearfix"></div>
    <?php
    \richardfan\widget\JSRegister::begin([
        //'key' => 'bootstrap-modal',
        'position' => \yii\web\View::POS_READY
    ]);

//$project_id = "1520742111042203500";
    ?>
    <script>
        $('.daterangepicker').remove();
        var evenClick = 0;
        $(document).on('click', '.subject-item', function () {
            if (evenClick == 1)
                return;
            evenClick = 1;
            $('#show-subject-list').find('.list-group-item-info').removeClass('list-group-item-info');
            $(this).addClass('list-group-item-info');

            var url = '<?=
    yii\helpers\Url::to(['/subjects/subject-management/subject-visit',
        'options' => $options,
        'subject_payment_widget' => $subject_payment_widget,
    ])
    ?>';
            var data_id = $(this).attr('data-id');
            var subject_id = $(this).attr('data-subject');
            var group_name = $(this).attr('data-group_name');
            var group_id = $(this).attr('data-group_id');
            var showVisit = $('#show-visit');
            showVisit.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
            $.ajax({
                url: url,
                method: "get",
                type: "html",
                data: {data_id: data_id, subject_id: subject_id, group_name: group_name, group_id: group_id},
                success: function (result) {
                    evenClick = 0;
                    showVisit.empty();
                    showVisit.html(result);
                }
            })
        });

        $('#show-subject-list').on('change', '.subject-number-search', function () {
            var show_subject = $('#show-subject-list');
            var subjectSearch = $(this).val();
            var url = '<?=
    yii\helpers\Url::to(['/subjects/subject-management/subject-payment-search',
        'widget_id' => $widget_id,
        'module_id' => $module_id,
        'schedule_id' => $schedule_id,
        'financial_id' => $financial_id,
        'subject_payment_widget' => $subject_payment_widget,
        'groupData' => base64_encode(\appxq\sdii\utils\SDUtility::array2String($groupList)),
        'reloadDiv' => $reloadDiv,
    ])
    ?>';
            $.ajax({
                url: url,
                method: "get",
                type: "html",
                data: {subject_search: subjectSearch},
                success: function (result) {
                    show_subject.empty();
                    show_subject.html(result);
                }
            });
        });
        $('#show-subject-list').on('change', '.subject-number-group', function () {
            var show_subject = $('#show-subject-list');
            var group_id = $(this).val();
            var subjectSearch = $(this).val();
            var url = '<?=
    yii\helpers\Url::to(['/subjects/subject-management/subject-payment-search',
        'widget_id' => $widget_id,
        'schedule_id' => $schedule_id,
        'module_id' => $module_id,
        'financial_id' => $financial_id,
        'subject_payment_widget' => $subject_payment_widget,
        'groupData' => base64_encode(\appxq\sdii\utils\SDUtility::array2String($groupList)),
        'reloadDiv' => $reloadDiv,
    ])
    ?>';
            $.ajax({
                url: url,
                method: "get",
                type: "html",
                data: {subject_search: subjectSearch, group_id: group_id},
                success: function (result) {
                    show_subject.empty();
                    show_subject.html(result);
                }
            });
        });

        function getReloadDiv(url, div) {
            $.get(url, {}, function (result) {
                $('#' + div).empty();
                $('#' + div).html(result);
            });
        }
    </script>
    <?php \richardfan\widget\JSRegister::end(); ?>
</div>




