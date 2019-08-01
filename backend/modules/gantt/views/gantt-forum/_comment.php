<?php
use yii\helpers\Url;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$comment_box = "comment-box-$object_id-$query_tool";
$comment_list_box = "comment-list-box-$object_id-$query_tool";
$comment_list = "comment-list-$object_id-$query_tool";
?>


<div id="<?=$comment_box?>" class="well" style="background-color: skyblue;"></div>
<div id="<?=$comment_list_box?>">
    <ul id="<?=$comment_list?>" class="media-list"> 
      
    </ul>
</div>
<?php $this->registerJs("

getComment();
getCommentList();

function getComment() {
            $.ajax({
                method: 'GET',
                url: '" . Url::to(['/gantt/gantt-forum/comment', 
                    'dataid' => "$dataid",
                    'object_id' => "$object_id",
                    'query_tool' => $query_tool,
                    'parent_id' => "$parent_id",
                    'field' => $field,
                    'type' => $type,
                    'limit' => $limit,
                    'modal' => $modal,
                ]) . "',
                dataType: 'HTML',
                success: function(result, textStatus) {
                    $('#$comment_box').html(result);
                }
            });
        }
        
function getCommentList() {
    $('#more_comment-$object_id-$query_tool').attr('data-start', '$limit');
    
            $.ajax({
                method: 'GET',
                url: '" . Url::to(['/gantt/gantt-forum/comment-list', 
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
                    $('#$comment_list').html(result);
                }
            });
        }
        
$('#$comment_list_box').on('click', '#more_comment-$object_id-$query_tool', function(){
    var start = parseInt($(this).attr('data-start'));
    $('#$comment_list_box .more-item').remove();
    getMoreCommentList(start);
});

function getMoreCommentList(start) {
    $.ajax({
        method: 'GET',
        url: '" . yii\helpers\Url::to(['/gantt/gantt-forum/comment-list', 
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
            $('#$comment_list').prepend(result);
        }
    });
}
        
$('#$comment_list_box').on('click', '.commt-del-btn', function(){
    var id = $(this).attr('data-id');
    var btn = $(this);
    yii.confirm('" . Yii::t('app', 'Are you sure you want to delete this item?') . "', function() {
        delcommt(id)
        btn.parent().remove();
    });
    
});

function delcommt(id) {
    $.post(
       '" . yii\helpers\Url::to(['/gantt/gantt-forum/delete']) . "',
       {id:id}
    ).done(function(result) {
    if(result.status == 'success') {
        " . SDNoty::show('result.message', 'result.status') . "
        var count = Number($('#count-$object_id-$query_tool').html())-1;
            $('#count-$object_id-$query_tool').html(count);    
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