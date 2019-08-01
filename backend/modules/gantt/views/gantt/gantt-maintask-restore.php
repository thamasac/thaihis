<?php

use yii\helpers\Url;
use \frontend\modules\api\v1\classes\MainQuery;
use yii\helpers\Html;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\subjects\classes\SubjectManagementQuery;
use yii\helpers\ArrayHelper;

?>
<div class="modal-header" style="border-radius:5px 5px 0px 0px ;background: #5bc0de;color:#fff;">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title"><strong>Restore main task from excel</strong></h4>
</div>
<div class="modal-body"  >
    <div class="container-fluid">
        <form id="excel-form" name="excel-form" enctype="multipart/form-data">
             <?= Html::hiddenInput('project_ezf_id', $project_ezf_id) ?>
             <?= Html::hiddenInput('subtask_ezf_id', $subtask_ezf_id) ?>
             <?= Html::hiddenInput('task_ezf_id', $task_ezf_id) ?>
             <?= Html::hiddenInput('response_ezf_id', $response_ezf_id) ?>
            <div class="col-md-12 sdbox-col">
                <label> Excel file : </label><input class="form-control" type="file" name="excel_file" required>
                <br/>
                <button type="submit" class="btn btn-primary btn_submit" ><i class="glyphicon glyphicon-import"></i> Import Now!</button>
                <div id="loading-text"></div>
            </div>
        </form>
    </div>
</div>
<?php
$this->registerJs("
    $('#excel-form').on('submit',function(e){
        e.preventDefault();
        $('.btn-submit').attr('disabled',true);
        $('#loading-text').html('<i class=\"\"></i><label>" . '<div class="sdloader"><i class="sdloader-icon"></i></div> ' . Yii::t('subjects', "Importing file. Please wait...") . "</label>');
        $.ajax({
            url:'" . Url::to('/gantt/gantt/restore-maintask') . "',
            type: 'post',             // Type of request to be send, called as method
            data: new FormData(this), // Data sent to server, a set of key/value pairs (i.e. form fields and values)
            contentType: false,       // The content type used when sending data to the server.
            cache: false,             // To unable request pages to be cached
            processData:false,        // To send DOMDocument or non processed data file it is set to false
            success: function(data)   // A function to be called if request succeeds
            {
               $('#loading-text').html('<br/>'+data);
               $('.btn-submit').attr('disabled',false);
               if(data === 'success')
                location.reload();
            },
            error:function(err){
                
            }
        });
    });
");
?>
