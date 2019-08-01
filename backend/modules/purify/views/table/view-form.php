<?php

use Yii;
use yii\helpers\Html;
use appxq\sdii\widgets\ModalForm;

$request = Yii::$app->request;
echo $request->get('data');
echo base64_decode($request->get('data'));
echo base64_decode($request->get('data'));
echo '<pre align=left>';
var_dump(json_decode(base64_decode($request->get('data')), true));

$data = json_decode(base64_decode($request->get('data')), true);

print_r($data);
echo '</pre>';
?>
<?php

$options = [
    'class' => 'btn btn-primary btn-open-form',
    'data' => [
        'ezf_id' => $data['ezf_id'],
        'data_id' => $data['data_id'],
        'target' => $data['target'],
        'ezf_version' => $data['ezf_version'],
    ]
];
echo Html::button('Open form', $options);
?>
<div id="show-form-list">
    <div class="panel panel-info">
        
        <div class="panel-body">
            <div id="display-ezform">
                <div class="modal-content">
                    
                </div>
            </div>
        </div>
    </div>
</div>
<div class="clearfix"></div>
<br/>
<?php

echo ModalForm::widget([
    'id' => 'modal-content-history',
    'size' => 'modal-xl',
]);
?>
<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);

//$project_id = "1520742111042203500";
?>
<script>
//    function onRemoveBackdrop(){
//         $(document).find(".modal-backdrop").remove();
//         $(document).find(".modal-open").removeClass('modal-open');
//         setTimeout(function(){
//             
//         },300);
//    }
//
//    $(document).on('click','.ezform-main-open',function(){
//        onRemoveBackdrop();
//    });
    
    $('.btn-open-form').click(function () {
        $(document).find('.btn-open-form').removeClass('active');
        $(this).addClass('active');
        var url = '/ezforms2/ezform-data/ezform-view';
        var ezf_id = $(this).attr('data-ezf_id');
        var data_id = $(this).attr('data-data_id');
        var target = $(this).attr('data-target');
        var ezf_version = $(this).attr('data-ezf_version');
        var divData = $('#display-ezform');
        
        var initdata= {visit_name:'<?=$visit_id?>'};
        divData.find('.modal-content').html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        
        var data = {
            ezf_id: ezf_id,
            version: ezf_version,
            reloadDiv: 'show-data-table', //show-data-table',
            dataid: data_id,
            target: target,
            readonly: 1,
            modal:'display-ezform',
            initdata:btoa(JSON.stringify(initdata)),
        }

        $.ajax({
            url: url,
            type: 'html',
            method: 'get',
            data: data,
            success: function (result) {
                divData.find('.modal-content').html(result);
                $(document).find(".glyphicon-remove").parent().remove();
                $(document).find(".close").remove();
            }
        });
    });
    $( document ).ready(function() {
        console.log( "document loaded" );
        $(document).find('.btn-open-form').removeClass('active');
        $(this).addClass('active');
        var url = '/ezforms2/ezform-data/ezform-view';
        var ezf_id = $('.btn-open-form').attr('data-ezf_id');
        var data_id = $('.btn-open-form').attr('data-data_id');
        var target = $('.btn-open-form').attr('data-target');
        var ezf_version = $('.btn-open-form').attr('data-ezf_version');
        var divData = $('#display-ezform');
        
        var initdata= {visit_name:'<?=$visit_id?>'};
        divData.find('.modal-content').html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
        
        var data = {
            ezf_id: ezf_id,
            version: ezf_version,
            reloadDiv: 'show-data-table', //show-data-table',
            dataid: data_id,
            target: target,
            readonly: 1,
            modal:'display-ezform',
            initdata:btoa(JSON.stringify(initdata)),
        }

        $.ajax({
            url: url,
            type: 'html',
            method: 'get',
            data: data,
            success: function (result) {
                divData.find('.modal-content').html(result);
                $(document).find(".glyphicon-remove").parent().remove();
                $(document).find(".close").remove();
            }
        });
    });
</script>
<?php \richardfan\widget\JSRegister::end(); ?>