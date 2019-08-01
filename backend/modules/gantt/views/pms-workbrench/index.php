<?php

use yii\helpers\Html;
use yii\helpers\Url;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$image_field = Html::img(Yii::getAlias('@storageUrl/images/pms_icon.png'), [ 'style' => 'width: 30px;']);

?>

<div class="row ">
    <?= $image_field ?> <span style="font-size: 18px;">  My Assignments </span>
</div>
<div class="clearfix"></div>
<hr/>
<div class="row ">
    <h4> Assigned modules from Project Management System (PMS) </h4>
    <?=
    $this->render('_module_list', [
        'project_ezf_id' => $project_ezf_id,
        'reloadDiv' => $reloadDiv,
        'pmsOptions' => $pmsOptions,
        'dataModule' => $dataModule,
    ]);
    ?>
</div>
<div class="clearfix"></div>
<hr/>
<div class="row ">
    <h4> Assigned tasks from Project Management System (PMS) </h4>
    <?=
    $this->render('_grid_pms', [
        'project_ezf_id' => $project_ezf_id,
        'reloadDiv' => $reloadDiv,
        'pmsOptions' => $pmsOptions,
        'dataProvider' => $dataProvider,
        'activity_ezf_id' => isset($pmsOptions['activity_ezf_id'])?$pmsOptions['activity_ezf_id']:'',
        'response_ezf_id' => isset($pmsOptions['response_ezf_id'])?$pmsOptions['response_ezf_id']:'' ,
        'other_ezforms' =>isset($pmsOptions['other_ezforms'])?$pmsOptions['other_ezforms']:[],
    ]);
    ?>
</div>
<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);

//$project_id = "1520742111042203500";
?>
<script>

    function getReloadDiv(url, div) {
        $.get(url, {}, function (result) {
            $('#' + div).empty()
            $('#' + div).html(result);
        });
    }

</script>
<?php \richardfan\widget\JSRegister::end(); ?>
