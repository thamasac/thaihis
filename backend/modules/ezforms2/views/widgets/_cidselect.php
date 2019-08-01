<?php

use appxq\sdii\helpers\SDHtml;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
if($type==1){
?>
<div class="alert alert-success" role="alert" style="font-size: 20px;"><?=SDHtml::getMsgSuccess()?> <?= Yii::t('ezform', 'Do you want to create a new PID?')?> <?=\yii\helpers\Html::button('<i class="glyphicon glyphicon-ok"></i> '.Yii::t('ezform', 'Create PID'), ['class'=>'btn btn-primary btn-add-cid'])?></div>

<?php
$this->registerJs("
            $('.btn-add-cid').click(function(){
                var \$form = $('#ezform-$ezf_id'); 

                var url = \$form.attr('action');
                if(\$form.attr('data-modal')!=''){
                    $.ajax({
                        method: 'GET',
                        url: url,
                        data:{initdata:'$initdata'},
                        dataType: 'HTML',
                        success: function(result, textStatus) {
                            $('#'+\$form.attr('data-modal')+' .modal-content').html(result);
                        }
                    });
                } else {
                    location.href = url
                }
                
            });
        ");
} elseif($type==2) {
?>
<div class="alert alert-success" role="alert" style="font-size: 20px;"><?=SDHtml::getMsgSuccess()?> <?= Yii::t('ezform', 'Have this card number in the agency.')?> <code><?=$data['hsitecode']?></code> <?=\yii\helpers\Html::button('<i class="glyphicon glyphicon-ok"></i> '.Yii::t('ezform', 'Data transfer'), ['class'=>'btn btn-warning btn-add-cid'])?> <?=\yii\helpers\Html::button('<i class="glyphicon glyphicon-ok"></i> '.Yii::t('ezform', 'Create PID'), ['class'=>'btn btn-primary btn-add-cid-empty'])?></div>

<?php 
$this->registerJs("
    $('.btn-add-cid').click(function(){
        var \$form = $('#ezform-$ezf_id'); 

        var url = \$form.attr('action');
        $.ajax({
            method: 'GET',
            url: url,
            data:{initdata:'$initdata'},
            dataType: 'HTML',
            success: function(result, textStatus) {
                $('#'+\$form.attr('data-modal')+' .modal-content').html(result);
            }
        });

    });
    
    $('.btn-add-cid-empty').click(function(){
        var \$form = $('#ezform-$ezf_id'); 

        var url = \$form.attr('action');
        $.ajax({
            method: 'GET',
            url: url,
            data:{initdata:'$initdataEmpty'},
            dataType: 'HTML',
            success: function(result, textStatus) {
                $('#'+\$form.attr('data-modal')+' .modal-content').html(result);
            }
        });

    });
");

} elseif($type==3) {?>
    
    <div class="sdloader "><i class="sdloader-icon"></i></div>
    <?php
    $this->registerJs("
    
   openform();

    function openform(){
        var \$form = $('#ezform-$ezf_id'); 
        var url = \$form.attr('action');
        $.ajax({
            method: 'GET',
            url: url,
            data:{dataid:'$dataid', initdata:'$initdata'},
            dataType: 'HTML',
            success: function(result, textStatus) {
                $('#'+\$form.attr('data-modal')+' .modal-content').html(result);
            }
        });
    }

    $('.btn-open-cid').click(function(){
        openform();
    });
    
");
} ?>
