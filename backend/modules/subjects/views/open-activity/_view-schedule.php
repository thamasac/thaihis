<?php

use backend\modules\subjects\classes\SubjectManagementQuery;
use backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\subjects\classes\JKDate;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$schedule_widget = SubjectManagementQuery::getWidgetById($schedule_id);
$visitSchedule = SubjectManagementQuery::getVisitScheduleByWidget($schedule_id, $group_id);

$schedule_options = \appxq\sdii\utils\SDUtility::string2Array($schedule_widget['options']);
$main_form = backend\modules\ezforms2\classes\EzfQuery::getEzformOne($schedule_options['11111']['main_ezf_id']);
$main_field_visit = $schedule_options['11111']['main_visit_name'];
$main_visit_name = $schedule_options['11111']['form_name'];
$main_field_actual = $schedule_options['11111']['main_actual_date'];


$random_form = backend\modules\ezforms2\classes\EzfQuery::getEzformOne($schedule_options['22222']['random_ezf_id']);
$random_field_visit = $schedule_options['22222']['random_visit_name'];
$random_visit_name = $schedule_options['22222']['form_name'];
$random_field_actual = $schedule_options['22222']['random_actual_date'];

$dataSchedule = [];
$actual_date = '';
$plan_date = '';
$earliest_date = '';
$latest_date = '';
$planDate = "";
$earDate = "";
$latestDate = "";
foreach ($visitSchedule as $key => $val) {
    if (!isset($val['ezf_id']) || $val['ezf_id'] == '')
        $val['ezf_id'] = $visitSchedule['11111']['ezf_id'];

    $ezform = EzfQuery::getEzformOne($val['ezf_id']);

    if ($ezform)
        $data = SubjectManagementQuery::GetTableData($ezform, ['target' => $target, 'visit_name' => $val['id']], 'one');

    if (isset($val['actual_date']) || $val['actual_date'] == '')
        $val['actual_date'] = isset ($visitSchedule['11111']['actual_date'])?$visitSchedule['11111']['actual_date']:'date_visit';
    
    $date = null;
    if ($data) {
        $actual_date = $data[$val['actual_date']];
        $date = new DateTime($actual_date);
    }
    $planDate = "";
    if (isset($val['plan_date']) && $date) {
        $planDate = $date->modify('+' . $val['plan_date'] . ' day');
        $planDate = $date->format('Y-m-d');
    }

    $dataSchedule[$key]['actual_date'] = $actual_date;
    $dataSchedule[$key]['plan_date'] = $planDate;
}

if ($plan_date == '') {
    if ($visitSchedule) {
        foreach ($visitSchedule as $key => $val) {
            if ($visit_id == $val['id']) {
                if ($last_visit_id == '11111' || $last_visit_id == '22222')
                    $actual_date = $actual_this_date;
                else {
                    if ($val['visit_cal_date'] == $last_visit_id) {
                        $actual_date = $actual_this_date;
                    } else {
                        if ($val['field_cal_date'] == 'actual_date')
                            $actual_date = $dataSchedule[$val['visit_cal_date']]['actual_date'];
                        else {
                            $actual_date = $dataSchedule[$val['visit_cal_date']]['plan_date'];
                        }
                    }
                }

                if ($actual_date == null) {
                    $actual_date = $actual_this_date;
                }
                $plan_date = $val['plan_date'];
                $earliest_date = $val['earliest_date'];
                $latest_date = $val['latest_date'];
            }
        }
    }
}

if (isset($actual_date) && $actual_date != '') {
    $date = new DateTime($actual_date);
    if ($plan_date != '') {
        $date->modify('+' . $plan_date . ' day');
        $planDate = $date->format('Y-m-d');
    }
    $plan_date = new DateTime($planDate);
    if ($earliest_date != '') {
        $plan_date->modify('+' . $earliest_date . ' day');
        $earDate = $plan_date->format('Y-m-d');
    }

    $plan_date = new DateTime($planDate);
    if ($latest_date != '') {
        $plan_date->modify('+' . $latest_date . ' day');
        $latestDate = $plan_date->format('Y-m-d');
    }
}
?>

<?php if ($actual_date != null && $visit_id != null && $last_visit_id != null): ?>
    <br/>
    <div class="col-md-8" id="view-grid-schedule">
        <h4>Schedule For Next Visit.</h4>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-schedule">
                <thead>
                    <tr>
                        <td>Plan Date</td>
                        <td>Earliest Date</td>
                        <td>Latest Date</td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?= JKDate::convertDate($planDate) ?></td>
                        <td><?= JKDate::convertDate($earDate) ?></td>
                        <td><?= JKDate::convertDate($latestDate) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="clearfix"></div>
<?php endif; ?>