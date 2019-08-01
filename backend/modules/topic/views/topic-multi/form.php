<?php

use yii\helpers\Html;
use appxq\sdii\helpers\SDNoty;
use yii\helpers\Url;
use appxq\sdii\helpers\SDHtml;

/* @var $this yii\web\View */
/* @var $model backend\modules\ezforms2\models\Topic */
/* @var $form yii\bootstrap\ActiveForm */
$ids = [];
?>
<?php foreach ($model as $key => $model): ?>
<?php 
    array_push($ids, $model['id']);
?>
<div class="panel panel-primary" id="panel-<?= $model['id']?>">

    <div class="panel-heading">
        <div class="text-right">
            <?= Html::button('<i class="fa fa-minus"></i>', ['data-id'=>$model['id'],'data-url'=>yii\helpers\Url::to(['/topic/topic/delete','id'=>$model['id']]),'class'=>'btn btn-sm btn-danger btnDelete'])?>
        </div>
    </div>
        <div class="panel-body">

            <div class="form-group">
                <label><?= Yii::t('chanpan', 'message') ?></label>
                <?= Html::textInput('name', $model['name'], ['data-name'=>'name','class' => 'form-control', 'data-id'=>$model['id']]) ?>
            </div>
            <div class="form-group" id="group-<?= $model['id']?>">
                <label><?= Yii::t('chanpan', 'message') ?></label>
                <?=
                \vova07\imperavi\Widget::widget([
                    'name' => 'detail',
                    'value' => $model['detail'],
                    'id' => 'chanpan-'.$model['id'],
                    'options'=>['data-id'=>$model['id'],'data-name'=>'detail',],
                    'settings' => [
                        'class'=>'chanpan-xxx',
                        'minHeight' => 50,
                        'imageManagerJson' => Url::to(['/ezforms2/text-editor/images-get']),
                        'fileManagerJson' => Url::to(['/ezforms2/text-editor/files-get']),
                        'imageUpload' => Url::to(['/ezforms2/text-editor/image-upload']),
                        'fileUpload' => Url::to(['/ezforms2/text-editor/file-upload']),
                        'plugins' => [
                            'fontcolor',
                            'fontfamily',
                            'fontsize',
                            'textdirection',
                            'textexpander',
                            'counter',
                            'table',
                            'definedlinks',
                            'video',
                            'imagemanager',
                            'filemanager',
                            'limiter',
                            'fullscreen',
                        ],
                        'paragraphize' => false,
                        'replaceDivs' => false,
                    ],
                ]);
                ?>
                 
                
            </div>     

        </div>

    </div>
<?php endforeach; ?>

<?php  $this->registerJs("
    function getData(){
            let url = '".Url::to(['/topic/topic-multi/get-data','options'=>$options])."';
            $.get(url,function(data){
                    $('#multi-main-".$options['widget_id']."').html(data);
            });
    }
    function setClass(){
        let ids = '';
        ids = JSON.parse('". json_encode($ids)."');
        
        for(let i in ids){
            $('#group-'+ids[i]+' .redactor-editor').attr('data-id','');
            $('#group-'+ids[i]+' .redactor-editor').attr('data-id','chanpan-'+ids[i]);
        }
    } setClass();
    $('.redactor-editor').blur(function(){
        let data_id = $(this).attr('data-id');
        let value = $('#'+data_id).val();
        let id = $('#'+data_id).attr('data-id');
        let name = 'detail';        
        let params = {id:id,name:name,value:value};
        //console.log(params);
        Update(params);
    });
    $('input,textarea').blur(function(){
        let id = $(this).attr('data-id');
        let value = $(this).val();
        let name = $(this).attr('data-name');        
        let params = {id:id,name:name,value:value};
        Update(params);
        
    });
    function Update(params){
        let url ='".Url::to(['/topic/topic-multi/update'])."';
        $.post(url,params, function(result){                
		if(result.status == 'success') {
		    " . SDNoty::show('result.message', 'result.status') . ";
                     getData();
		} else {
		    " . SDNoty::show('result.message', 'result.status') . "
		}
        });
    }

    $('.btnDelete').click(function(){
        let url = $(this).attr('data-url'); 
        let id = $(this).attr('data-id');
        yii.confirm('".Yii::t('app', 'Are you sure you want to delete these items?')."', function() {
	$.ajax({
	    method: 'POST',
	    url: url,
	    success: function(result, textStatus) {
                //console.log(result);
		if(result.status == 'success') {
		    ". SDNoty::show('result.message', 'result.status') .";
                    $('#panel-'+id).remove();
                    getData();
		} else {
		    ". SDNoty::show('result.message', 'result.status') ."
		}
	    }
	});
    });
    });
 

");?>
 