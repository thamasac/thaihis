<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\subjects\classes\SubjectManagementQuery;
use kartik\widgets\DepDrop;

$mainEzform = EzfQuery::getEzformOne($main_ezf);
$mainData = SubjectManagementQuery::GetTableData($mainEzform);

if(!$mainData)$mainData = [];
?>
<div class="modal-header">
    <h4 class="pull-left">Copy and Share</h4>
    <?= Html::button("x", ['class' => 'btn btn-default pull-right', 'data-dismiss' => "modal"]) ?>
</div>
<div class="modal-body">
    <div class="container-fluid">
        <div  class="row form-group">
            <div class="col-md-5">
                <?= Html::button('<i class="fa fa-files-o" aria-hidden="true"></i> ' . Yii::t('gantt', 'Copy task')
                        , ['class' => 'btn btn-primary', 'id' => 'btn_copy_task','data-dismiss' => "modal"
                            , 'data-taskid'=>$dataid,'data-ezf_id'=>$ezf_id,'data-task_type'=>$task_type]); ?>
            </div>
        </div>
        <div  class="row form-group">
            <?= Html::hiddenInput('cate_ezf_id', $cate_ezf, ['id' => 'cate_ezf_id']); ?>
            <div class="col-md-5">
                <?php
                echo Html::label(Yii::t('gantt', 'Main task'));
                echo kartik\select2\Select2::widget([
                    'name' => "maintask_sharing",
                    'value' => null,
                    'options' => ['placeholder' => Yii::t('ezmodule', 'Select a main task...'), 'id' => 'config_maintask_sharing'],
                    'data' => ArrayHelper::map($mainData, 'id', 'project_name'),
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
                ?>
            </div>
            <div class="col-md-5">
                <?php
                if($task_type == "task" || $task_type == "milestone"){
                    echo Html::label(Yii::t('gantt', 'Sub-task'));
                    echo kartik\widgets\DepDrop::widget([
                        'type' => DepDrop::TYPE_SELECT2,
                        'name' => "subtask_sharing",
                        'data' => [],
                        'options' => ['id' => 'subtask_sharing', 'placeholder' => 'Select a sub-task...'],
                        'select2Options' => ['pluginOptions' => ['allowClear' => true]],
                        'pluginOptions' => [
                            'initialize' => true,
                            'depends' => ['config_maintask_sharing'],
                            'url' => Url::to(['/gantt/gantt/get-subtask-sharing']),
                            'params' => ['cate_ezf_id']
                        ]
                    ]);
                }
                ?>
            </div>
            <div class="col-md-2">
                <?=Html::label(Yii::t('gantt', ' '));?>
                <?= Html::button('<i class="fa fa-share" aria-hidden="true"></i> ' . Yii::t('gantt', 'Share task'), ['class' => 'btn btn-success','id'=>'btn_share_task']); ?>
            </div>

        </div>
    </div>

</div>
<div class="modal-footer">
    <?= Html::button(Yii::t('gantt', 'Close'), ['class' => 'btn btn-default pull-right', 'data-dismiss' => "modal"]) ?>
</div>

<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);

//$project_id = "1520742111042203500";
?>
<script>
    $('#btn_copy_task').click(function(){
        onCopyTask(this);
    });
    
    $('#btn_share_task').click(function(){
        var dataid = '<?=$dataid?>';
        var ezf_id = '<?=$ezf_id?>';
        var task_type = '<?=$task_type?>';
        var target = $('#config_maintask_sharing').val();
        var parent = $('#subtask_sharing').val();
        var main_from = '<?=$main_from?>';
        if(!parent || parent == '' )parent = 0;
        onShareTask(dataid, ezf_id, task_type,target,parent,main_from);
    });


</script>
<?php \richardfan\widget\JSRegister::end(); ?>    

