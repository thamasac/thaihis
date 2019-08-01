<!DOCTYPE html>
<?php

use yii\helpers\Html;
use yii\helpers\Url;

$url = Url::to([
            '/gantt/care-plan/main-care-plan',
        ]);
?>
<div id="care-plan-content" class="col-md-4">
    
</div>
<div id="care-plan-procedure" class="col-md-8">

</div>
<div class="clearfix"></div>
<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);

//$project_id = "1520742111042203500";
?>
<script>
    $(function () {

    });

</script>
<?php \richardfan\widget\JSRegister::end(); ?>    

