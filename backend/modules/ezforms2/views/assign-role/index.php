<?php

use richardfan\widget\JSRegister;
use yii\helpers\Url;
use Yii;
//use appxq\sdii\helpers\SDNoty;
$reloadDiv = "ezf_main";
$modal = 'ezform-main2';
?>
<?php 
    echo appxq\sdii\widgets\ModalForm::widget([
       'id' => $modal,
       'size' => 'modal-lg',
   ]);
?>
<div class="modal-content">
    <div class="modal-body">
        <div id="ezf_main">
            <div class="text-right"><button class="btn btn-success" id="btnAddMain"><i class="fa fa-plus"></i></button></div>
            <hr />
            <div id="ezf_seconds" data-id='001'></div><hr />
            <div id="<?= $modal?>">
                <?php
                    echo \backend\modules\ezforms2\classes\EzfHelper::ui()
                            ->ezf_id($ezf_id)
                            //->reloadDiv($reloadDiv)
                            ->modal($modal)
                            ->buildGrid();
                ?>
            </div>    
        </div>   
    </div>
</div>
<?php
$this->registerJs("
        $('#$reloadDiv-view-grid tbody tr td a').on('click', function() { 

        var url = $(this).attr('href'); 
        var action = $(this).attr('data-action'); 

        if(action === 'update' || action === 'create'){ 
            $('#$modal .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>'); 
            $('#$modal').modal('show') 
            .find('.modal-content') 
            .load(url); 
        } else if(action === 'view') { 
            $('#$modal .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>'); 
            $('#$modal').modal('show') 
            .find('.modal-content') 
            .load(url); 
        } else if(action === 'delete') { 
            yii.confirm('" . Yii::t('app', 'Are you sure you want to delete this item?') . "', function(){ 
                    $.post( 
                            url, {'_csrf':'" . Yii::$app->request->getCsrfToken() . "'} 
                    ).done(function(result){ 
                            if(result.status == 'success'){ 
                                     
                                var urlreload = $('#$reloadDiv').attr('data-url');  
                                getUiAjax(urlreload, '$reloadDiv');  
                            } else { 
                                     
                            } 
                    }).fail(function(){ 
                             
                            console.log('server error'); 
                    }); 
            }); 
        } 
        return false; 
    }); 

    $('#$reloadDiv-view-grid').on('beforeFilter', function(e) { 
        var \$form = $(this).find('form'); 
        $.ajax({ 
     method: 'GET', 
     url: \$form.attr('action'), 
            data: \$form.serialize(), 
     dataType: 'HTML', 
     success: function(result, textStatus) { 
         $('#$reloadDiv').html(result); 
     } 
        }); 
        return false; 
    }); 

    $('#$reloadDiv-view-grid .pagination a').on('click', function() { 
        getUiAjax($(this).attr('href'), '$reloadDiv'); 
        return false; 
    }); 

    $('#$reloadDiv-view-grid thead tr th a').on('click', function() { 
        getUiAjax($(this).attr('href'), '$reloadDiv'); 
        return false; 
    });

");
?>
<?php JSRegister::begin(); ?>
<script>
    function getUiAjax(url, divid) {
        $.get(url, function (data) {
            $(`#${divid}`).html(data);
        });
    }
    $('#btnAddMain').click(function () {
        let url = '<?= Url::to(['/ezforms2/ezform-data/ezform?ezf_id=1519707087068015000&modal=modal-ezform-main&reloadDiv=view-emr-lists&initdata=&target=&targetField=']) ?>';
        $.get(url, function (data) {
            $('#ezf_seconds2').append(data);

        });
    });
    function onLoad() {
        let url = '<?= Url::to(['/ezforms2/ezform-data/ezform?ezf_id=1519707087068015000&modal=modal-ezform-main&reloadDiv=view-emr-lists&initdata=&target=&targetField=']) ?>';
        $.get(url, function (data) {
            $('#ezf_seconds').append(data);
            $('#modal-ezform-main .modal-footer, #modal-ezform-main .modal-header').hide();

        });
    }
    onLoad();


</script>
<?php JSRegister::end(); ?>