<?php


use backend\modules\subjects\classes\SubjectManagementQuery;


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
            <li class="list-group-item active" style="text-align: center;"><b>Select Main Task >>></b></li>

            <?php
            foreach ($maintaskData as $key => $value):
                $active ='';
                if($value['id'] == $pms_tab){
                    $active= 'list-group-item-info';
                }
                ?>
                <a href="javascript:void(0)" class="list-group-item <?= $active?> project-task " data-id="<?= $value['id'] ?>"
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


    ?>
    <script>
        var evenClick = 0;
        $(document).on('click', '.project-task', function () {
            $('#show-subject-list').find('.list-group-item-info').removeClass('list-group-item-info');
            $(this).addClass('list-group-item-info');
            
            if(evenClick==1)return;
            evenClick = 1;

            var url = '<?=
                yii\helpers\Url::to(['/subjects/subject-management/financial',
                    'view'=>'pms-financial',
                    'options' => $options,
                    'module_id'=>$module_id,
                    'user_create' => $user_create,
                    'user_update' => $user_update,
                    'reloadDiv' => 'show-financial',
                ]);
                ?>';
            var data_id = $(this).attr('data-id');
            var data_url = $('#display-pms').attr('data-url')+'&pms_tab='+data_id;
            $('#display-pms').attr('data-url',data_url);
            var showVisit = $('#show-visit');
            showVisit.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
            $.ajax({
                url: url,
                method: "get",
                type: "html",
                data: {data_id: data_id,data_url:data_url},
                success: function (result) {
                    evenClick = 0;
                    showVisit.empty();
                    showVisit.html(result);
                }
            })
        });
        $(function () {
            var pms_tab = '<?=$pms_tab?>';
            if(!pms_tab)return;
                if(evenClick==1)return;
                evenClick = 1;

                var url = '<?=
                    yii\helpers\Url::to(['/subjects/subject-management/financial',
                        'view'=>'pms-financial',
                        'options' => $options,
                        'module_id'=>$module_id,
                        'user_create' => $user_create,
                        'user_update' => $user_update,
                        'reloadDiv' => 'show-financial',
                    ]);
                    ?>';
                var data_id = pms_tab;
                var data_url = $('#display-pms').attr('data-url')+'&pms_tab='+data_id;
                $('#display-pms').attr('data-url',data_url);

                var showVisit = $('#show-visit');
                showVisit.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
                $.ajax({
                    url: url,
                    method: "get",
                    type: "html",
                    data: {data_id: data_id,data_url:data_url},
                    success: function (result) {
                        evenClick = 0;
                        showVisit.empty();
                        showVisit.html(result);
                    }
                })


        })


        function getReloadDiv(url, div) {
            $.get(url, {}, function (result) {
                $('#' + div).empty();
                $('#' + div).html(result);
            });
        }
    </script>
    <?php \richardfan\widget\JSRegister::end(); ?>
</div>
