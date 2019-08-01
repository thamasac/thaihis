<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$reloadDiv = 'reloadDiv-tab-widget';
$theme = isset($theme)?$theme:'default';
$tab = isset($_GET['tab'])?$_GET['tab']:(isset($_GET['id'])?$_GET['id']:0);
?>

<div class="panel panel-<?=$theme?>">
  <div class="panel-heading">
    <h3 class="panel-title"><?=$title?></h3>
  </div>
  <div class="panel-body">
    <?php
    $uiView = \backend\modules\ezforms2\classes\CommunityBuilder::Community()->type('tabwidget')->object_id($tab);
    echo $uiView->buildCommunity();
 ?>
  </div>
</div>