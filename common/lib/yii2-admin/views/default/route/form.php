<?php

use yii\web\View;

//use yii\helpers\Html;

/* @var $this View */
?>

<div class="modal-header">
    <button type="button" class="close" ng-click="cancel()"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title">Add Route(s)</h4>
</div>
<div class="modal-body">
    <div class="form-group">
	<label >Route (sparate with new line)</label>
	<textarea class="form-control" ng-model="route" rows="6"></textarea>
    </div>
</div>
<div class="modal-footer">
    <button class="btn btn-primary" ng-click="ok()"><span class="fa fa-plus"></span></button>
    <button class="btn btn-danger" ng-click="cancel()"><span class="fa fa-remove"></span></button>
</div>
