<?php

use appxq\sdii\helpers\SDNoty;
use backend\modules\ezforms2\classes\EzfAuthFunc;

$reloadDiv = "step3-grid";
$modal = "modal-ezform-main3";
?>
<?php
echo \appxq\sdii\widgets\ModalForm::widget([
    'id' => $modal,
    'size' => 'modal-lg',
    'tabindexEnable' => FALSE,
]);
?>
<div class="row">
    <div class="col-md-12 text-right">
        <?php \backend\modules\ezforms2\classes\EzfStarterWidget::begin(); ?>
            <?= backend\modules\ezforms2\classes\BtnBuilder::btn()
                                    ->ezf_id("1520514351069551000")
                                    //->options(['data-id'=>$_GET['id'], 'class'=>'btnManaAuth btn btn-success'])
                                    ->modal($modal)
                                    ->reloadDiv($reloadDiv)
                                    ->buildBtnAdd();?>
        <?php \backend\modules\ezforms2\classes\EzfStarterWidget::end();?>
    </div>
</div>
 
    <?php
    $ezfReadWrite = backend\modules\ezforms2\classes\EzfHelper::ui('1520514351069551000')
            ->data_column(['site_name','site_detail'])
            ->default_column(0)
            ->reloadDiv($reloadDiv)
            ->modal($modal)
            ->buildGrid();
    echo $ezfReadWrite;
    ?>
 

<?php
$this->registerJs("
 $('button[type=\"submit\"][value=\"1\"]').on('click',function(){
    alert('ok');
 });    
 $('#$reloadDiv-emr-grid tbody tr td a').on('click', function() {
    
    var url = $(this).attr('href');
    var action = $(this).attr('data-action');
    
    if(action === 'update' || action === 'create'){
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
                                " . SDNoty::show('result.message', 'result.status') . "
                            var urlreload =  $('#$reloadDiv').attr('data-url');        
                            getUiAjax(urlreload, '$reloadDiv');        
                        } else {
                                " . SDNoty::show('result.message', 'result.status') . "
                        }
                }).fail(function(){
                        " . SDNoty::show("'" . "Server Error'", '"error"') . "
                        console.log('server error');
                });
        });
    }
    return false;
});

$('#$reloadDiv-emr-grid').on('beforeFilter', function(e) {
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

$('#$reloadDiv-emr-grid .pagination a').on('click', function() {
    getUiAjax($(this).attr('href'), '$reloadDiv');
    return false;
});

$('#$reloadDiv-emr-grid thead tr th a').on('click', function() {
    getUiAjax($(this).attr('href'), '$reloadDiv');
    return false;
});
    function getUiAjax(url, divid) { 
    $.ajax({ 
        method: 'GET', 
        url: url, 
        dataType: 'HTML', 
        success: function(result, textStatus) { 
            $('#'+divid).html(result); 
        } 
    }); 
}
 
");
?>