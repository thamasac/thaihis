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

$maintask_form = "1520711894072728800";
$ezform = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($maintask_form);
$maintaskData = SubjectManagementQuery::GetTableQuery($ezform)->all();

?>
<style>
    .active-select{
        background-color: #fbf069;
    }
    .list-group-horizontal .list-group-item {
        display: inline-block;
    }
    .list-group-horizontal .list-group-item {
        margin-bottom: 0;
        margin-left:-4px;
        margin-right: 0;
    }
    .list-group-horizontal .list-group-item:first-child {
        border-top-right-radius:0;
        border-bottom-left-radius:4px;
    }
    .list-group-horizontal .list-group-item:last-child {
        border-top-right-radius:4px;
        border-bottom-left-radius:0;
    }
</style>

    <div class="row col-md-12" id="show-subject-list" >
        <div class="list-group list-group-horizontal">
            <li class="list-group-item active" style="text-align: center;"><b>Select Main Task</b></li>

            <?php
            foreach ($maintaskData as $key => $value):
                ?>
                <a href="javascript:void(0)" class="list-group-item projectpay-item" data-id="<?= $value['id'] ?>"
                   data-group_name="<?= $value['project_name'] ?>" style="text-align:right;font-size:16px;"><i class="fa fa-cube"></i> <?= $value['project_name'] ?></a>
            <?php endforeach; ?>
        </div>
        <div class="clearfix"></div>

    </div>

    <div class="col-md-12 sdbox-col" id="show-visit">

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
        $(document).on('click', '.projectpay-item', function () {
            if(evenClick==1)return;
            evenClick = 1;
            var url = '<?=
                yii\helpers\Url::to([
                    '/subjects/subject-management/other-payment',
                    'module_id' => $module_id,
                    'options' => $options,
                    'reloadDiv' => $reloadDiv,
                    'view' => 'other-payment',
                ]);
                ?>';
            var data_id = $(this).attr('data-id');
            var showVisit = $('#show-visit');
            showVisit.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
            $.ajax({
                url: url,
                method: "get",
                type: "html",
                data: {data_id: data_id},
                success: function (result) {
                    evenClick = 0;
                    showVisit.empty();
                    showVisit.html(result);
                }
            })
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
