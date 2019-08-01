<?php

use kartik\tabs\TabsX;

$items = [];

foreach ($dataOther as $key => $value) {
    $items[] = [
        'label' => $value['ezf_name'],
        'headerOptions' => ['style' => 'font-weight:bold', 'id' => 'pms_main_form'
            , 'class' => 'pms_task_form'
            , 'data-ezf_id' => $value['ezf_id']
            , 'data-dataid' => isset($value['dataid']) ? $value['dataid'] : ''
        ],
    ];
}
?>
<style>
    .modal-content{
        box-shadow:none;
    }
</style>
<div class="modal-header">
    <button type="button" class="close-modal pull-right btn btn-default" data-dismiss="modal">Close</button>
    <h4>Task Item Settings and Assignments</h4>
</div>
<div class="modal-body">
    <?=
    \kartik\tabs\TabsX::widget([
        'position' => TabsX::POS_ABOVE,
        'align' => TabsX::ALIGN_LEFT,
        'encodeLabels' => false,
        //'enableStickyTabs' => true,
        'items' => $items,
    ]);
    ?>
</div>
<div class="display_task_form">
    <div class="modal-content">

    </div>
</div>

<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);

//$project_id = "1520742111042203500";
?>
<script>
    $(function () {
        var url = '/ezforms2/ezform-data/ezform';
        var dataid = '<?= $dataid ?>';
        var ezf_id = '<?= $ezf_id ?>';
        var modal = '<?=$modal?>';
        var div = $('.display_task_form').find('.modal-content');
        div.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        var data = {ezf_id: ezf_id,modal:modal, dataid: dataid,initdata:'<?=$initdata ?>'};
        if (dataid && dataid !== '') {
            data = {dataid: dataid,modal:modal, ezf_id: ezf_id,initdata:'<?=$initdata ?>'};
        }

        $.get(url, data, function (result) {
            div.empty();
            div.html(result);
        });
    });

    $('.pms_task_form').on('click', function () {
        var url = '/ezforms2/ezform-data/ezform';
        var dataid = $(this).attr('data-dataid');
        var target = '<?= $dataid ?>';
        var modal = '<?=$modal?>';
        var ezf_id = $(this).attr('data-ezf_id');
        var data = {ezf_id: ezf_id,modal:modal, target: target,initdata:'<?=$initdata ?>'};
        if (dataid && dataid !== '') {
            data = {dataid: dataid,modal:modal, ezf_id: ezf_id,initdata:'<?=$initdata ?>'};
        }
        var div = $('.display_task_form').find('.modal-content');
        div.html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        $.get(url, data, function (result) {
            div.empty();
            div.html(result);
        });
    });

</script>
<?php \richardfan\widget\JSRegister::end(); ?>