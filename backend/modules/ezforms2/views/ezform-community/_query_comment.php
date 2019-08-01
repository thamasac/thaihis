<?php
use yii\helpers\Url;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use backend\modules\ezmodules\classes\ModuleFunc;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$qcomment_box = "qcomment-box-$object_id-$query_tool";
$qcomment_list_box = "qcomment-list-box-$object_id-$query_tool";
$qcomment_list = "qcomment-list-$object_id-$query_tool";

if($parent_obj->send_to!=''){
    $to_name = $parent_obj->send_to;
    $send_to_name = \backend\modules\ezforms2\classes\EzfQuery::getUserProfileIn($to_name);
    if($send_to_name){
        $send_to_name = yii\helpers\ArrayHelper::getColumn($send_to_name, 'fullname');
        $parent_obj->send_to_name = implode(', ', $send_to_name);
    }
}
?>

<div id="<?=$qcomment_list_box?>">
    
    <div  class="media-list"> 
      <div class="media"> 
            <?php
            if (Yii::$app->user->can('administrator') && 0) {
                ?>
                <button type="button" class="close commt-del-btn" data-id="<?= $parent_obj['id'] ?>" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <?php } ?>
            <div class="media-left"> 
                <a > 
                    <img class="media-object img-rounded" style="width: 64px; height: 64px;" src="<?= (isset($parent_obj->avatar_path) && $parent_obj->avatar_path != '') ? Yii::getAlias('@storageUrl/source') . '/' . $parent_obj['avatar_path'] : ModuleFunc::getNoUserImage() ?>" data-holder-rendered="true"> 
                </a> 
            </div> 
            <div class="media-body"> 
              <h4 class="media-heading">
                <i class="glyphicon glyphicon-user"></i> <?= $parent_obj['user_name'] ?> 
                <small><i class="glyphicon glyphicon-calendar"></i> <?= \appxq\sdii\utils\SDdate::mysql2phpDateTime($parent_obj['created_at']) ?></small> 
                <?= (isset($parent_obj->send_to_name)?'<code>@'.$parent_obj['send_to_name'].'</code>':'')?> 
                <?php if($parent_obj['type']=='ezform' && $parent_obj['dataid']>0 && $parent_obj['dataid']!=$dataid):?>
                <?= backend\modules\ezforms2\classes\EzfHelper::btn($parent_obj['object_id'])->modal($modal)->options(['class'=>'btn btn-info btn-xs'])->buildBtnView($parent_obj['dataid'])?>
                <?php endif;?>
              </h4> 
                <p><?= $parent_obj['content'] ?></p> 
                <div id="<?=$qcomment_list?>" class="media"></div>
            </div> 
        </div>
    </div>
</div>
<p class="lead" style="font-size: 16px;margin-bottom: 10px;"> <?=Yii::t('ezmodule', 'Total')?> <span id="qcount-<?=$object_id?>-<?=$query_tool?>"></span> <?=Yii::t('ezmodule', 'items')?></p>
<div id="<?=$qcomment_box?>" class="well" style="background-color: #fff;"></div>

<?php $this->registerJs("

getqComment();
getqCommentList();

function getqComment() {
            $.ajax({
                method: 'GET',
                url: '" . Url::to(['/ezforms2/ezform-community/qcomment', 
                    'dataid' => $dataid,
                    'object_id' => $object_id,
                    'query_tool' => $query_tool,
                    'parent_id' => $parent_id,
                    'field' => $field,
                    'type' => $type,
                    'limit' => $limit,
                    'modal' => $modal,
                ]) . "',
                dataType: 'HTML',
                success: function(result, textStatus) {
                    $('#$qcomment_box').html(result);
                }
            });
        }
        
function getqCommentList() {
    $('#more_qcomment-$object_id-$query_tool').attr('data-start', '$limit');
    
            $.ajax({
                method: 'GET',
                url: '" . Url::to(['/ezforms2/ezform-community/qcomment-list', 
                    'dataid' => $dataid,
                    'object_id' => $object_id,
                    'query_tool' => $query_tool,
                    'parent_id' => $parent_id,
                    'field' => $field,
                    'type' => $type,
                    'limit' => $limit,
                    'modal' => $modal,
                    ]) . "',
                dataType: 'HTML',
                success: function(result, textStatus) {
                    $('#$qcomment_list').html(result);
                }
            });
        }
        
$('#$qcomment_list_box').on('click', '#more_qcomment-$object_id-$query_tool', function(){
    var start = parseInt($(this).attr('data-start'));
    $('#$qcomment_list_box .more-item').remove();
    getMoreqCommentList(start);
});

function getMoreqCommentList(start) {
    $.ajax({
        method: 'GET',
        url: '" . yii\helpers\Url::to(['/ezforms2/ezform-community/qcomment-list', 
            'dataid' => $dataid,
            'object_id' => $object_id,
            'query_tool' => $query_tool,
            'parent_id' => $parent_id,
            'field' => $field,
            'type' => $type,
            'limit' => $limit,
            'modal' => $modal,
            'start' => '']) . "'+start,
        dataType: 'HTML',
        success: function(result, textStatus) {
            $('#$qcomment_list').prepend(result);
        }
    });
}
        
$('#$qcomment_list_box').on('click', '.commt-del-btn', function(){
    var id = $(this).attr('data-id');
    var btn = $(this);
    yii.confirm('" . Yii::t('app', 'Are you sure you want to delete this item?') . "', function() {
        delqcommt(id)
        btn.parent().remove();
    });
    
});

function delqcommt(id) {
    $.post(
       '" . yii\helpers\Url::to(['/ezforms2/ezform-community/delete']) . "',
       {id:id}
    ).done(function(result) {
    if(result.status == 'success') {
        " . SDNoty::show('result.message', 'result.status') . "
        var count = Number($('#qcount-$object_id-$query_tool').html())-1;
            $('#qcount-$object_id-$query_tool').html(count);    
    } else {
        " . SDNoty::show('result.message', 'result.status') . "
    } 
    }).fail(function() {
        " . SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') . "
        console.log('server error');
    });
}

    ");
?>