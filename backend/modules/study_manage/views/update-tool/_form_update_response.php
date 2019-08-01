<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

?>
<div class="modal-header">
    <h3 class="pull-left">Update Response</h3>
    <button type="button" class="close pull-right" data-dismiss="modal" aria-hidden="true">x</button>
</div>
<h4><label class="label label-success">Alter table success <?=$response['all_success']?></label></h4>
<h4><label class="label label-danger">Alter table fail <?=$response['all_fail']?></label></h4>
<div class="modal-body">
    <h4><label class="label label-success">Success <?=count($response['success'])?></label></h4>
    <?php 
    foreach ($response['success'] as $key => $val){
        echo "<div class='col-md-3' style='overflow:hidden;'>$val</div>";
    }
    ?>
    <div class="clearfix"></div>
    <hr>
    <h4><label class="label label-danger">Fail ! <?=count($response['fail'])?></label></h4>
    <?php 
    foreach ($response['fail'] as $key => $val){
        echo "<div class='col-md-3' style='overflow:hidden;'>$val</div>";
    }
    ?>
    <div class="clearfix"></div>
</div>
<div class="modal-footer">
    <button type="button" class="close pull-right btn btn-warning" data-dismiss="modal" aria-hidden="true">Close</button>
</div>