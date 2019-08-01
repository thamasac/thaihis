<?php
$labelColor = '';
if($color==1){
    $labelColor = 'color: #DD0000;';
}
?>
<div class="row" style="margin-bottom: 10px;">
    <div class="col-md-5">
        <label><span style="<?=$labelColor?>"><?=$form_name?></span></label>
    </div>
    <div class="col-md-3 sdbox-col">
        <?= $btnItems['emrBtn'] ?>
        <?=$btnadd?>
    </div>
    <div class="col-md-4 sdbox-col">
        <?=$progress?>
    </div>
    <div class="col-md-12">
        <?= $btnItems['itemsBtn'] ?>
    </div>
</div>
