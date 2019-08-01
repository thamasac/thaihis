<?php

use backend\modules\ezforms2\classes\EzfAuthFunc;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;

if ($options == null) {
    echo '<code>This Widget has error.</code>';
    exit();
}
$ezf_id = $options['ezf_id'];
$default_column = isset($options['default_column']) ? $options['default_column'] : 1;
$pagesize = isset($options['pagesize']) ? $options['pagesize'] : 50;
$order = isset($options['order']) ? $options['order'] : [];
$order_by = isset($options['order_by']) ? $options['order_by'] : 4;
$db2 = isset($options['db2']) ? $options['db2'] : 0;
$dataid = isset($options['dataid']) ? $options['dataid'] : '';
$reloadDiv = 'grid-widget-custom';
?>
<div class="modal-header">
    <h3 class="modal-title "><div class="label label-success"><?=$options['city'];?></div></h3>
    <button type="button" class="close"  data-dismiss="modal" >
        <span aria-hidden="true">&times;</span>
    </button>
    
</div>
<div class="modal-body" >
    <div class="panel panel-primary" >
        <div class="panel-heading">
            <h3 class="panel-title">
                TCTR Registration
            </h3>
        </div>
        <div class="panel-body">
            <?php
            $uiView = \backend\modules\tctr\classes\TctrHelper::ui($ezf_id)
                    ->data_column($options['fields'])
                    ->reloadDiv($reloadDiv)
                    ->default_column($default_column)
                    ->pageSize($pagesize)
                    ->order_column($order)
                    ->dataid($dataid)
                    ->orderby($order_by);
                    
            if ($db2 == 1) {
                echo $uiView->buildDb2Grid();
            } else {
                echo $uiView->buildGrid();
            }
            ?>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" id="cancel" data-dismiss="modal">Cancel</button>
</div>

<?php
$this->registerJS("
    $('#submit-agree').on('click',function(){
        if($('#agree1').is(':checked')) { 
            $('.ezform-main-open').click();
            setTimeout(function() {
                $('body').addClass('modal-open');
            }, 1500);
        }else{
            $('#modal-Terms').modal('hide');
        }
    });
$('#modal-ezform-main').on('hidden.bs.modal', function () {
    $('body').addClass('modal-open');
});
function getUiAjax(url, divid) {
    $.ajax({
        method: 'POST',
        url: url,
        dataType: 'HTML',
        success: function(result, textStatus) {
            $('#'+divid).html(result);
        }
    });
}
");
?>
