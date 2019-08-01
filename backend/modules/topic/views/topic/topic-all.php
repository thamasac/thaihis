<?php

use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use backend\modules\ezforms2\classes\EzfAuthFunc;

// appxq\sdii\utils\VarDumper::dump($options);
 $panel_type = isset($options['panel_type']) ? $options['panel_type'] : 'primary';
 $panel = isset($options['panel']) && $options['panel'] == 1 ? 'panel panel-'.$panel_type : '';
 $panel_heading = isset($options['panel']) && $options['panel'] == 1 ? 'panel-heading' : '';
 $panel_title = isset($options['panel']) && $options['panel'] == 1 ? 'panel-title' : '';
 $panel_body = isset($options['panel']) && $options['panel'] == 1 ? 'panel-body' : '';
 $show = isset($value['show']) ? $value['show'] : 1;
 $id = isset($value['id']) ? $value['id'] : 1;
?>

<?php if (!empty($value)): ?>
<button id="btnCallapse" type="button" class="btn btn-default btnCallapse" style="margin-bottom:10px;float: right;">
        <i class="fa fa-info-circle"></i> <?= Yii::t('chanpan', 'Show') ?>
</button>
<div class="clearfix"></div>
 
<div class="<?= $panel?>"  id="collapse" style="display:none">    
        <div class="<?= $panel_heading?>">
            <div class="<?= $panel_title?>">
                <div class="row">
                    <div class="col-md-12">
                        <span class="pull-left"><i class="fa <?= isset($options['icon']) ? $options['icon'] : ''?>"></i> <?= isset($value['name']) ? $value['name'] : '' ?></span>
                        <span class="pull-right">
                            
                            <?php if (backend\modules\ezforms2\classes\EzfAuthFuncManage::auth()->accessBtn($options['module_id'])) { ?>
                                <?=
                                \yii\helpers\Html::button('<i class="glyphicon glyphicon-pencil"></i>', [
                                    'data-id' => $value['id'],
                                    'class' => 'btn btn-info btn-sm btnEdit',
                                    'data-url' => yii\helpers\Url::to(['/topic/topic/update', 'id' => $value['id'], 'options' => $options])
                                ])
                                ?>

                            <?php } ?>
                             
                        </span>
                    </div>          
                </div>
            </div>
        </div>
        <div class="<?= $panel_body?>">            
            <div> <?= $value['detail'] ?></div>
        </div>
    </div>
 
<?php endif; ?> 

<?php
$this->registerJs("
    var show = '".$show."';  
    $('.btnCallapse').click(function(){     
           let url = '".yii\helpers\Url::to(['/topic/topic/set-show'])."';
           let id = '".$id."';
           
           //alert(show);    
           if(show == 1){
                show = 2;   
                init(show);
//                $.post(url, {show:2, id:id}, function(data){
//                    //console.log(data); 
//                }); 
           }else if(show == 2){
                show = 1;   
                init(show);
                $.post(url, {show:1, id:id}, function(data){
                    //console.log(data); 
                }); 
           }    
    });
    init=function(show){
        if(show == 3){
            show = '".$show."';
        }
        if(show == 1){
            $('#collapse').show();
            $('#btnCallapse').html('<i class=\"fa fa-info-circle\"></i> ".Yii::t('chanpan', 'Hide')."');
            let url = '".yii\helpers\Url::to(['/topic/topic/set-show'])."';
            let id = '".$id."';    
            $.post(url, {show:2, id:id}, function(data){
                    //console.log(data); 
            });     
        }else{
            $('#collapse').hide();
            $('#btnCallapse').html('<i class=\"fa fa-info-circle\"></i> ".Yii::t('chanpan', 'Show')."');
               
        }
    }
    init(show);
    $('.btnEdit').on('click', function() {
         //let url = $(this).attr('data-url'); 
          
         modalTopic($(this).attr('data-url'));
    });
    function modalTopic(url) {
        $('#modal-topic .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
        $('#modal-topic').modal('show')
        .find('.modal-content')
        .load(url);
    }
    $('.btnDelete').on('click', function() {
        let url = $(this).attr('data-url');            
        yii.confirm('" . Yii::t('app', 'Are you sure you want to delete this item?') . "', function() {
	    $.post(
		url
	    ).done(function(result) {
		if(result.status == 'success') {
		    " . SDNoty::show('result.message', 'result.status') . "
		   showTopicAll();
		} else {
		    " . SDNoty::show('result.message', 'result.status') . "
		}
	    }).fail(function() {
		" . SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') . "
		console.log('server error');
	    });
	});
     
        return false;
});
");
?>
