<?php

use dee\angular\NgView;

/* @var $this yii\web\View */
/* @var $widget NgView */

?>

<div class="modal-header">
    <button type="button" class="close" ng-click="close()"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title">{{name}}</h4>
</div>
<div class="modal-body" style="overflow-x: auto;overflow-y: auto;">
    <code ng-bind-html="content"></code>
</div>