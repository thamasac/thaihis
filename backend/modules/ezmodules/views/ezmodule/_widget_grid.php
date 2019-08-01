<?php
use yii\helpers\Url;
use appxq\sdii\widgets\ModalForm;
use yii\helpers\Html;
use appxq\sdii\helpers\SDHtml;
use appxq\sdii\helpers\SDNoty;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$modal = 'modal-ezform-main';
$filter_custom = isset($modelFilter->filter_type) && $modelFilter->filter_type==0?1:0;
if(isset($modelFilter)){
    $modelFilter = backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String($modelFilter->attributes);
}
        
$url = Url::to(['/ezmodules/ezmodule/grid', 
    'modal' => $modal, 
    'reloadDiv' => $reloadDiv, 
    'module' => $module,
    'addon'=>$addon,
    'filter'=>$filter,
    'modelFilter'=>$modelFilter,
]);

?>
<p>
    <div class="row">
        <div class="col-md-6 ">
            
            <button id="import-filter" data-url="<?= Url::to(['/ezmodules/ezmodule-filter/list-filter', 'filter'=>$filter, 'module'=>$model->ezm_id])?>" class="btn btn-info" disabled="disabled"><i class="glyphicon glyphicon-plus"></i> <?= Yii::t('ezmodule', 'Import to the Manual Filter')?> <code class="filter-count">0</code></button>
            <?php if($filter>0 && $filter_custom==1){?>
                <button id="del-filter" data-id="<?=$filter?>" class="btn btn-danger" disabled="disabled"><i class="glyphicon glyphicon-remove"></i> <?= Yii::t('ezmodule', 'Remove from the Manual Filter')?> <code class="filter-count">0</code></button>
            <?php } ?>
            <?=Html::button('<i class="glyphicon glyphicon-plus"></i> '.Yii::t('ezmodule', 'Show Parent Fields'), ['data-url'=>Url::to(['/ezmodules/ezmodule-fields/create', 
                'reloadDiv' => $reloadDiv, 
                'module' => $module,
                'addon'=>$addon,
                'ezf_id'=>$model['ezf_id'],
            ]), 'class' => 'btn btn-default', 'id'=>'modal-addbtn-fields'])?>    
        </div>
        <div class="col-md-6 sdbox-col text-right">
            <?=Html::button('<i class="glyphicon glyphicon-plus"></i> '.Yii::t('ezmodule', 'Add Child Form'), ['data-url'=>Url::to(['/ezmodules/ezmodule-forms/create', 
                'reloadDiv' => $reloadDiv, 
                'module' => $module,
                'addon'=>$addon,
                'ezf_id'=>$model['ezf_id'],
            ]), 'class' => 'btn btn-default', 'id'=>'modal-addbtn-forms'])?>
        </div>
    </div>
</p>

<div id="<?=$reloadDiv?>" data-url="<?=$url?>" data-reload="" >
    <div class="sdloader "><i class="sdloader-icon"></i></div>
</div>

<?=  ModalForm::widget([
    'id' => 'modal-ezmodule-fields',
    //'size'=>'modal-lg',
    'tabindexEnable'=>false,
]);
?>
<?=  ModalForm::widget([
    'id' => 'modal-ezmodule-forms',
    'size'=>'modal-xxl',
    'tabindexEnable'=>false,
]);
?>
<?=  ModalForm::widget([
    'id' => 'modal-filter-list',
    'size'=>'modal-sm',
    'tabindexEnable'=>false,
]);
?>

<?=  ModalForm::widget([
    'id' => 'modal-ezmodule-emr',
    'size'=>'modal-xxl',
    'tabindexEnable'=>false,
]);
?>
<?php
Yii::$app->session['main-div'] = $reloadDiv;
$this->registerJs("
    $('#modal-ezmodule-emr').on('hidden.bs.modal', function (e) {
        $('#modal-ezmodule-emr .modal-content').html('');
        var div = $('#$reloadDiv').attr('data-reload');
        if(div!=''){
            var url = $('#'+div).attr('data-url');
            $.ajax({
                method: 'POST',
                url: url,
                dataType: 'HTML',
                success: function(result, textStatus) {
                    $('#'+div).html(result);
                    $('#$reloadDiv').attr('data-reload', '');
                }
            });
        }
    });
        
    getGridAjax('$url', '$reloadDiv');
        
    function getGridAjax(url, divid) {
        $.ajax({
            method: 'POST',
            url: url,
            dataType: 'HTML',
            success: function(result, textStatus) {
                $('#'+divid).html(result);
            }
        });
    }
    
$('#modal-addbtn-fields').on('click', function() {
    modalEzmoduleField($(this).attr('data-url'));
});

$('#$reloadDiv').on('click', '.modal-updatebtn-fields', function(){
    var url = $(this).attr('data-url');
    modalEzmoduleField(url);
});

$('#$reloadDiv').on('click', '.modal-delbtn-fields', function(){
    var url = $(this).attr('data-url');
    yii.confirm('" . Yii::t('app', 'Are you sure you want to delete this item?') . "', function() {
        $.post(
            url
        ).done(function(result) {
            if(result.status == 'success') {
                " . SDNoty::show('result.message', 'result.status') . "
                 getGridAjax('$url', '$reloadDiv');
            } else {
                " . SDNoty::show('result.message', 'result.status') . "
            }
        }).fail(function() {
            " . SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') . "
            console.log('server error');
        });
    });
});

$('#modal-addbtn-forms').on('click', function() {
    modalEzmoduleForm($(this).attr('data-url'));
});

$('#$reloadDiv').on('click', '.modal-updatebtn-forms', function(){
    var url = $(this).attr('data-url');
    modalEzmoduleForm(url);
});

$('#$reloadDiv').on('click', '.modal-delbtn-forms', function(){
    var url = $(this).attr('data-url');
    yii.confirm('" . Yii::t('app', 'Are you sure you want to delete this item?') . "', function() {
        $.post(
            url
        ).done(function(result) {
            if(result.status == 'success') {
                " . SDNoty::show('result.message', 'result.status') . "
                 getGridAjax('$url', '$reloadDiv');
            } else {
                " . SDNoty::show('result.message', 'result.status') . "
            }
        }).fail(function() {
            " . SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') . "
            console.log('server error');
        });
    });
});

$('#$reloadDiv').on('click', '.select-on-check-all', function() {
    window.setTimeout(function() {
	var key = $('#$reloadDiv-emr-grid').yiiGridView('getSelectedRows');
	disabledItemsBtn(key.length);
    },100);
});

$('#$reloadDiv').on('click', '.selectionDataIds', function() {
    var key = $('input:checked[class=\"'+$(this).attr('class')+'\"]');
    disabledItemsBtn(key.length);
});

function disabledItemsBtn(num) {
    if(num>0) {
	$('#import-filter').attr('disabled', false);
        $('#del-filter').attr('disabled', false);
        $('.filter-count').html(num);
    } else {
	$('#import-filter').attr('disabled', true);
        $('#del-filter').attr('disabled', true);
        $('.filter-count').html(0);
    }
}

$('#import-filter').click(function(){
    var url = $(this).attr('data-url');
    modalFilterList(url);
});

$('#modal-filter-list').on('click', '.add-filter',function(){
    var filter = $(this).attr('data-filter');
    selectionAdd(filter);
    return false;
});

$('#del-filter').click(function(){
    selectionDel();
});

function selectionAdd(filter) {
    $.ajax({
        method: 'POST',
        url: '".Url::to(['/ezmodules/ezmodule-filter/add-filter', 'module'=>$model->ezm_id, 'filter'=>''])."'+filter,
        data: $('.selectionDataIds:checked[name=\"selection[]\"]').serialize(),
        dataType: 'JSON',
        success: function(result, textStatus) {
            if(result.status == 'success') {
                " . SDNoty::show('result.message', 'result.status') . "
                location.reload();
            } else {
                " . SDNoty::show('result.message', 'result.status') . "
            }
        }
    });
}

function selectionDel() {
    yii.confirm('" . Yii::t('app', 'Are you sure you want to delete these items?') . "', function() {
	$.ajax({
	    method: 'POST',
	    url: '".Url::to(['/ezmodules/ezmodule-filter/del-filter', 'filter'=>$filter, 'module'=>$model->ezm_id])."',
	    data: $('.selectionDataIds:checked[name=\"selection[]\"]').serialize(),
	    dataType: 'JSON',
	    success: function(result, textStatus) {
		if(result.status == 'success') {
		    " . SDNoty::show('result.message', 'result.status') . "
		    location.reload();
		} else {
		    " . SDNoty::show('result.message', 'result.status') . "
		}
	    }
	});
    });
}

function modalFilterList(url) {
    $('#modal-filter-list .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
    $('#modal-filter-list').modal('show')
    .find('.modal-content')
    .load(url);
}

function modalEzmoduleField(url) {
    $('#modal-ezmodule-fields .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
    $('#modal-ezmodule-fields').modal('show')
    .find('.modal-content')
    .load(url);
}


$('#modal-ezmodule-forms').on('hidden.bs.modal', function (e) {
  $('.sp-container').remove();
})

function modalEzmoduleForm(url) {
    $('#modal-ezmodule-forms .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
    $('#modal-ezmodule-forms').modal('show')
    .find('.modal-content')
    .load(url);
}

function modalEzformMain(url, modal) {
    $('#'+modal+' .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
    $('#'+modal).modal('show')
    .find('.modal-content')
    .load(url);
}

");
?>
