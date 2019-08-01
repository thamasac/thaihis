<?php

use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use backend\modules\ezforms2\classes\EzfAuthFuncManage;
use yii\helpers\Url;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="itemModalLabel">Ezform</h4>
</div>
<div class="modal-body">
    <div class="row">
        <div class="container-fluid">
            <div class="col-md-6">

                <?= Html::label(Yii::t('ezmodule', 'Select Sub-Task'), 'category_name', ['class' => ' control-label']) ?>
                <?php
                echo kartik\select2\Select2::widget([
                    'name' => 'category_name',
                    'value' => $subData[0]['id'],
                    'options' => ['placeholder' => Yii::t('ezmodule', 'Select Main Task'), 'class' => 'form-control', 'id' => 'config_sub_task'],
                    'data' => ArrayHelper::map($subData, 'id', 'cate_name'),
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
                ?>
            </div>
            <div class="col-md-2" style="padding-top: 25px;">
                <?= backend\modules\ezforms2\classes\EzfHelper::btn($subtask_ezf_id)->target($project_id)->label('Add new Sub-task')->buildBtnAdd() ?>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    <br/>
    <div class="row">
        <div class="container-fluid">
            <div class="col-md-6">
                <?php
                if (!empty($forms)) {
                    $html = '';
                    foreach ($forms as $key => $value) {
                        if (isset($value['ezf_id']) && isset($value['subject']) && isset($value['start']) && isset($value['end'])) {
                            $dataset = [
                                $value['end'] => $end_date,
                                $value['start'] => $start_date,
                                $value['allday'] => $allDay == 'true' ? 1 : 0,
                            ];
                            //if (EzfAuthFuncManage::auth()->accessBtn($module_id, 1)) {
                                echo \yii\helpers\Html::button(Yii::t('ezform', '<i class="fa fa-plus"></i> ' . $value['label']), ['id' => 'btn_add_newtask', 'class' => 'btn btn-primary','data-ezf_id'=>$value['ezf_id']]);
                            //}
                        }
                    }
                    echo $html;
                }
                ?>
            </div>
        </div>
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
    $('#btn_add_newtask').click(function () {
        var cate_id = $('#config_sub_task').val();
        var ezf_id = $(this).attr('data-ezf_id');
        var url = '<?=
Url::to(['/ezforms2/ezform-data/ezform',
    'modal' => 'modal-ezform-task',
    'require' => 'new_task',
    'reloadDiv'=>'display-calendar',
]);
?>';
        var initdata = {'category_id': cate_id,start_date:'<?=$start_date?>',finish_date:'<?=$end_date?>'};
        var data = {ezf_id: ezf_id, initdata: btoa(JSON.stringify(initdata)), target: '<?=$project_id?>'};
        $('#modal-ezform-task').modal();
        $('#modal-ezform-task').find('.modal-content').html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        $.ajax({
            url: url,
            method: 'get',
            data: data,
            cache: true,
            success: function (data) {
                $('#modal-ezform-task').find('.modal-content').empty();
                $('#modal-ezform-task').find('.modal-content').html(data);
            }
        });
    });

</script>
<?php \richardfan\widget\JSRegister::end(); ?>