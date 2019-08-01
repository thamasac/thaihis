<?php

use yii\helpers\Html;
use yii\helpers\Url;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$progress_style = "progress-bar-info";
if ($sum_percen < 25) {
    $progress_style = "progress-bar-danger";
} else if ($sum_percen >= 25 && $sum_percen < 50) {
    $progress_style = "progress-bar-warning";
} else if ($sum_percen == 100) {
    $progress_style = "progress-bar-success";
}
?>
<div class="panel panel-info">
    <div class="panel-heading">
        <h4>PMS Main Task Status:</h4>
    </div>
    <div class="panel-body">
        <div class="container-fluid">
            <div class="col-md-12">
                <label>Creator: </label> <?= $ownPms['firstname'] . ' ' . $ownPms['lastname'] ?>
            </div>
            <div class="col-md-6">
                <label>Start date: </label> <?= $pms_start_date ?>
            </div>
            <div class="col-md-6">
                <label>Finish date: </label> <?= $pms_end_date ?>
            </div>
            <div class="col-md-6">
                <label>Number of Task Items: </label> <?= $taskAll ?>
            </div>
            <div class="col-md-6">
                <label>Completed Task Items: </label> <?= $taskCompleted ?>
            </div>
            <div class="col-md-2">
                <label>Overall progress: </label>
            </div>
            <div class="col-md-6">
                <div class="progress">
                    <div class="progress-bar <?= $progress_style ?>" role="progressbar" aria-valuenow="<?= $sum_percen ?>"
                         aria-valuemin="0" aria-valuemax="100" style="width:<?= $sum_percen ?>%">
                    </div>
                    
                </div>
                <label style="position: absolute;top:0px;margin-left: 40%; "><?= number_format($sum_percen, 1) ?> %</label>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
</div>