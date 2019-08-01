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

?>
<h3>Reminder your schedule visit is in the window period!! Study: <?=$projectData['projectacronym']?> Site: <?=$userdata['sitecode']?></h3>

<p>Dear Related Staff,</p>
<p><h4>This is a reminder for your subject scheduled visit.</h4></p>
<p>Protocol: <?=$projectData['projectacronym']?>  </p>    
<p>Site Number: <?=$userdata['sitecode']?></p>
<br/>
<p>Subject Number: <?=$subject_number?></p>
<p>Date Of Birth: <?=$birth_date?></p>
<br/>
<p>Visit: <?=$visit_name?></p>
<p>Visit window for examination: </p>
<br/>
<?= $this->renderAjax('_view-schedule_mail', [
                    'schedule_id' => $schedule_id,
                    'visit_id' => $next_visit_id,
                    'last_visit_id' => $visit_id,
                    'actual_this_date' => $date_visit,
                    'target' => $target,
                    'group_id' => $group_id,
        ]);        
?>

<p>Please DO NOT REPLY to the sender of this system-generated e-mail. Please verify by click the following link
    <a href="<?='https://'.$url_curr.$url?>">
        <?='https://'.$url_curr.$url?>
    </a>


