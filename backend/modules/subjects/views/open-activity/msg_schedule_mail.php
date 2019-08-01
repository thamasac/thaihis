<?php

use \cpn\chanpan\classes\CNServerConfig;
use backend\modules\subjects\classes\ReportQuery;
use backend\modules\ezforms2\classes\EzfQuery;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$url_curr = \cpn\chanpan\classes\CNServerConfig::getDomainName();
//$url_curr = "udon.work.ncrc.in.th";
$projectData = ReportQuery::getProjectData($url_curr);
$userdata = EzfQuery::getUserProfile(Yii::$app->user->id);
$visit_cal_date = "";
if ($visit_id == '22222') {
    $visit_cal_date = "11111";
} else {
    $visit_cal_date = $visit_data['visit_cal_date'];
}
?>
<h3>Schedule visit out of window!!!!  Study: <?= $projectData['projectacronym'] ?> Site: <?= $userdata['sitecode'] ?></h3>

<p>Dear Related Staff,</p>
<p><h4>THE DATE OF VISIT IS OUT OF WINDOW PERIOD.</h4></p>
<p>Protocol: <?= $projectData['projectacronym'] ?>  </p>    
<p>Site Number: <?= $userdata['sitecode'] ?></p>
<br/>
<p>Subject Number: <?= $subject_number ?></p>
<p>Date Of Birth: <?= $birth_date ?></p>
<br/>
<p>Visit: <?= $visit_name ?></p>
<p>Date of Visit:  <?= backend\modules\subjects\classes\SubjectManagementQuery::convertDate($date_visit) ?></p>
<p>Visit window for examination: </p>
<?=
$this->renderAjax('_view-schedule_mail', [
    'schedule_id' => $schedule_id,
    'visit_id' => $visit_id,
    'last_visit_id' => $visit_cal_date,
    'actual_this_date' => $date_visit,
    'target' => $target,
    'group_id' => $group_id,
]);
?>

<p>Please DO NOT REPLY to the sender of this system-generated e-mail. Please verify by click the following link
    <a href="<?= 'https://' . $url_curr . $url ?>">
        <?= 'https://' . $url_curr . $url ?>
    </a>


