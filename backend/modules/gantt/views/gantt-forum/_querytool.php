<?php

use yii\helpers\Url;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$start = 20;

$query_box = "query-box-$object_id";
$query_field_box = "query-field-box-$object_id";
$query_field_list = "query-field-list-$object_id";
$query_view = "query-view-$object_id";
?>

<div id="<?= $query_box ?>" class="row" style="margin-bottom: 15px;">
  <div class="col-md-5" id="<?= $query_field_box ?>">
    <div class="panel panel-info" style="margin-bottom: 10px;position: relative;">
      <a class="btn btn-default btn-sm btn-refresh" style="position: absolute; right: 5px; top: 5px;"><i class="glyphicon glyphicon-refresh"></i></a>
      <div class="panel-heading">
        <strong><?= Yii::t('ezmodule', 'Total') ?> <span id="count-query-<?= $object_id ?>"></span> <?= Yii::t('ezmodule', 'items') ?></strong>
      </div>
    </div>

    <div class="list-group" id="<?=$query_field_list?>">
      
    </div>
    
  </div>
  <div class="col-md-7" id="<?= $query_view ?>">
    <h1 class="text-center" style="font-size: 45px; color: #ccc; margin: 100px 0;"><?= Yii::t('ezmodule', 'Please select a item') ?></h1>
  </div>
</div>



<?php
$this->registerJs("
getQueryList();

$('#$query_field_box').on('click', '.btn-refresh', function(){
    getQueryList();
    $('#$query_view').html('<h1 class=\"text-center\" style=\"font-size: 45px; color: #ccc; margin: 100px 0;\">".Yii::t('ezmodule', 'Please select a item')."</h1>');
});

$('#$query_field_list').on('click', '.query-item', function(){
    $('#$query_field_list .query-item').removeClass('active');
    $(this).addClass('active');
    
    getQueryComment($(this).attr('data-url'));
});

function getQueryComment(url) {
    $.ajax({
        method: 'GET',
        url: url,
        dataType: 'HTML',
        success: function(result, textStatus) {
            $('#$query_view').html(result);
        }
    });
}

function getQueryList() {
        $('#more_query-$object_id').attr('data-start', '$limit');
            
            $.ajax({
                method: 'GET',
                url: '" . Url::to(['/ezforms2/ezform-community/query-list',
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
                    $('#$query_field_list').html(result);
                }
            });
        }
        
        
$('#$query_field_box').on('click', '#more_query-$object_id', function(){
    var start = parseInt($(this).attr('data-start'));
    $(this).remove();
    getMoreQueryList(start);
});

function getMoreQueryList(start) {
    $.ajax({
        method: 'GET',
        url: '" . yii\helpers\Url::to(['/ezforms2/ezform-community/query-list',
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
            $('#$query_field_list').append(result);
        }
    });
}
        
$('#$query_field_box').on('click', '.query-del-btn', function(){
    var id = $(this).attr('data-id');
    var btn = $(this);
    yii.confirm('" . Yii::t('app', 'Are you sure you want to delete this item?') . "', function() {
        delQuery(id)
        btn.parent().remove();
    });
    
});

function delQuery(id) {
    $.post(
       '" . yii\helpers\Url::to(['/ezforms2/ezform-community/delete']) . "',
       {id:id}
    ).done(function(result) {
    if(result.status == 'success') {
        " . SDNoty::show('result.message', 'result.status') . "
        var count = Number($('#count-query-$object_id').html())-1;
            $('#count-query-$object_id').html(count);    
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