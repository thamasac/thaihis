<?php
use yii\helpers\Html;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use yii\helpers\Url;
use backend\modules\ezforms2\classes\EzfHelper;
use backend\modules\ezforms2\classes\EzfQuery;
use yii\helpers\ArrayHelper;

backend\modules\ezforms2\assets\ListdataAsset::register($this);

$ezformAll = backend\modules\ezforms2\classes\EzfQuery::getEzformCoDevAll();

$modelFields = EzfQuery::getFieldAllVersion($modelEzf->ezf_id);

if(!isset(Yii::$app->session['ezf_input'])){
    Yii::$app->session['ezf_input'] = backend\modules\ezforms2\classes\EzfQuery::getInputv2All();
}

?>
<ul class="nav nav-tabs" style="margin: 10px 0;">
  <li role="presentation" class="<?=$popup==2?'active':''?>"><a href="<?= Url::to(['/ezforms2/data-lists/index', 'ezf_id'=>$ezf_id, 'target'=>$target, 'view'=>2])?>">Data Entry</a></li>
  <li role="presentation" class="<?=$popup==0?'active':''?>"><a href="<?= Url::to(['/ezforms2/data-lists/index', 'ezf_id'=>$ezf_id, 'target'=>$target , 'view'=>0])?>">Data of All Forms</a></li>
  <li role="presentation" class="<?=$popup==1?'active':''?>"><a href="<?= Url::to(['/ezforms2/data-lists/index', 'ezf_id'=>$ezf_id, 'target'=>$target, 'view'=>1])?>">List of All Forms</a></li>
  
  <?php if(isset($modelEzf->ezf_db2) && $modelEzf->ezf_db2==1):?>
  <li role="presentation" class="<?=$popup==3?'active':''?>"><a href="<?= Url::to(['/ezforms2/data-lists/index', 'ezf_id'=>$ezf_id, 'target'=>$target, 'view'=>3])?>">Key Operator 2</a></li>
  <?php endif;?>
</ul>

<ul class="list-group">
    <li class="list-group-item list-group-item-info">
        <div class="row">
            <div class="col-md-6"><h4><?= backend\modules\ezforms2\classes\EzfUiFunc::getEzformIcon($modelEzf, 20)?> <?=$modelEzf->ezf_name?></h4></div>
            <div class="col-md-6 text-right">
                <?= EzfHelper::btnAdd($ezf_id, $target, [], 'view-emr-lists') ?>
                <?php
                    $url = ['/ezforms2/data-lists/export'];
                    $queryParams = Yii::$app->request->getQueryParams();
                    $ezformTarget = ['EzformTarget'=>$searchModel->attributes]
                    ?>
                    <?= Html::a('<i class="glyphicon glyphicon-export"></i> '.Yii::t('ezform', 'Export'), 
                            Url::to(ArrayHelper::merge($url, $queryParams, $ezformTarget)),
                            ['class'=>'btn-export btn btn-warning', 'target'=>'_blank'])?>
            </div>
        </div>
    </li> 
  <li class="list-group-item">
    <?php echo $this->render('_search', ['model' => $searchModel, 'ezformAll'=>$ezformAll]);  ?>
  </li>
  <?php $targetField = EzfQuery::getTargetOne($modelEzf->ezf_id); 
  if($targetField){
  ?>
  <li class="list-group-item">
    <?php echo $this->render('_emr_target', [
        'ezf_id' => $ezf_id,
        'modelEzf' => $modelEzf,
        'modelFields' =>$modelFields,
        'model' => $searchModel,
        'targetField' => $targetField,
        'modal' => $modal,
        'reloadDiv' => $reloadDiv,
        'target' => $target,
        'showall' => $showall,
        ]);  ?>
  </li>
  <?php }?>
</ul>

<?=
  yii\widgets\ListView::widget([
        'id' => "$reloadDiv-emr-grid",
        'dataProvider' => $dataProvider,
        'options'=>['class'=>'list-group'],
        'itemOptions' => ['class' => 'item list-group-item'],
        'layout'=>'<div class="list-group-item disabled  text-right" >{summary}</div>{items}<div class="list-group-item list-pager">{pager}</div>',
        'itemView' => function ($model, $key, $index, $widget) use ($ezf_id, $reloadDiv, $modal, $modelFields, $modelEzf, $disabled) {
            return $this->render('_emr_view', [
                'model' => $model,
                'key' => $key,
                'index' => $index,
                'widget' => $widget,
                'ezf_id' => $ezf_id,
                'reloadDiv'=>$reloadDiv,
                'modal' =>$modal,
                'modelFields' =>$modelFields,
                'modelEzf' => $modelEzf,
                'disabled' =>$disabled,
            ]);
            
        },
    ])
    ?>

<?php
//$sub_modal = '<div id="modal-'.$ezf_id.'" class="fade modal" role="dialog"><div class="modal-dialog modal-xxl"><div class="modal-content"></div></div></div>';
$jsAddon = 'switch(result.ezf_id) {';
foreach ($ezformAll as $key_form => $ezform) {
    $options = appxq\sdii\utils\SDUtility::string2Array($ezform['ezf_options']);
    $enable_after_save = isset($options['after_save']['enable'])?$options['after_save']['enable']:0;
    if($enable_after_save){
        if(isset($options['after_delete']['js']) && $options['after_delete']['js']!=''){
            $jsAddon .= "case '{$ezform['ezf_id']}': ".$options['after_delete']['js'].' break;';
        }
    }
}
$jsAddon .= '}';

$this->registerJs("
$('.btn-showall').on('click', function(e) {
    var url = $(this).attr('href');
    var \$form = $('form#search-emr-{$searchModel->formName()}');
    $.ajax({
	method: 'GET',
	url: url,
	dataType: 'HTML',
	success: function(result, textStatus) {
	    $('#$reloadDiv').html(result);
	}
    });
    return false;
});

$('#$reloadDiv-emr-grid .item .media-body .action a').on('click', function() {
    
    var url = $(this).attr('href');
    var action = $(this).attr('data-action');
    
    if(action === 'update' || action === 'create'){
        $('#$modal .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
        $('#$modal').modal('show')
        .find('.modal-content')
        .load(url);
    } else if(action === 'delete') {
        yii.confirm('".Yii::t('app', 'Are you sure you want to delete this item?')."', function(){
                $.post(
                        url, {'_csrf':'".Yii::$app->request->getCsrfToken()."'}
                ).done(function(result){
                        if(result.status == 'success'){
                                ". SDNoty::show('result.message', 'result.status') ."
                                    $jsAddon
                            var urlreload =  $('#$reloadDiv').attr('data-url');        
                            getUiAjax(urlreload, '$reloadDiv');          
                        } else {
                                ". SDNoty::show('result.message', 'result.status') ."
                        }
                }).fail(function(){
                        ". SDNoty::show("'" . "Server Error'", '"error"') ."
                        console.log('server error');
                });
        });
    }
    return false;
});



$('form#search-emr-{$searchModel->formName()}').on('beforeSubmit', function(e) {
    var \$form = $(this);
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