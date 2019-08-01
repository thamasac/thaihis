<?php

use yii\helpers\Url;
use \frontend\modules\api\v1\classes\MainQuery;
use yii\helpers\Html;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\subjects\classes\SubjectManagementQuery;
use yii\helpers\ArrayHelper;

$ezform_group = EzfQuery::getEzformOne($options['group_ezf_id']);
$groupList = SubjectManagementQuery::getGroupScheduleByWidget($widget_id, $ezform_group, $options['group_field']);
$groupItems[] = ['id'=>'0','group_name'=>'All Group'];
foreach ($groupList as $key => $val) {
    if($val['id'] <> '1')
        $groupItems[] = ['id'=>$val['id'],'group_name'=>$val['group_name']];
}
?>
<div class="modal-header" style="border-radius:5px 5px 0px 0px ;background: #00A21E;color:#fff;">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title"><strong>Import visit schedule from excel</strong></h4>
</div>
<div class="modal-body"  >
    <div class="container-fluid">
        <form id="excel-form" name="excel-form" enctype="multipart/form-data">
            <div class="col-md-6 sdbox-col">
                <?= Html::hiddenInput('group_id', $group_id) ?>
                <?= Html::hiddenInput('widget_id', $widget_id) ?>
                <?= Html::hiddenInput('schedule_id', $schedule_id) ?>
                <?= Html::hiddenInput('module_id', $module_id) ?>
                
                <?= Html::label(Yii::t('ezmodule', 'For what study group?'), 'options[group_name]', ['class' => 'control-label']) ?>
                <?php
                $attrname_group_name = 'group_id';
                $value_group_name = $group_id != '' ? $group_id : '';
                echo kartik\select2\Select2::widget([
                    'name' => $attrname_group_name,
                    'value' => $value_group_name,
                    'options' => ['placeholder' => Yii::t('ezmodule', 'Group select...'), 'id' => 'config_group_name'],
                    'data' => ArrayHelper::map($groupItems, 'id', 'group_name'),
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
                ?>
            </div>
            <div class="clearfix"></div>

            <div class="col-md-12 sdbox-col">
                <label> Excel file : </label><input class="form-control" type="file" name="excel_file" required>
                <br/>
                <button type="submit" class="btn btn-success" ><i class="glyphicon glyphicon-import"></i> Import Now!</button>
                <div id="loading-text"></div>
            </div>
        </form>
    </div>
</div>
<?php
$this->registerJs("
    $('#excel-form').on('submit',function(e){
        e.preventDefault();
        var group_id = '$group_id';
        $('#btn-submit').attr('disabled',true);
        $('#loading-text').html('<i class=\"\"></i><label>" . '<div class="sdloader"><i class="sdloader-icon"></i></div> ' . Yii::t('subjects', "Importing file. Please wait...") . "</label>');
        $.ajax({
            url:'" . Url::to('/subjects/subject-management/excel-save/') . "',
            type: 'post',             // Type of request to be send, called as method
            data: new FormData(this), // Data sent to server, a set of key/value pairs (i.e. form fields and values)
            contentType: false,       // The content type used when sending data to the server.
            cache: false,             // To unable request pages to be cached
            processData:false,        // To send DOMDocument or non processed data file it is set to false
            success: function(data)   // A function to be called if request succeeds
            {
               $('#loading-text').html(data);
            },
            error:function(err){
                
            }
        });
    });
");
?>
