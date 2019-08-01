<?php

use Yii;
use yii\helpers\Url;
use yii\jui\DatePicker;
use appxq\sdii\utils\ToDate;
use kartik\widgets\Select2;
use yii\web\JsExpression;
?>
<style>
    #table2 thead tr th{
        background: #00A21E;
        color: #fff;
        font-size: 18px;
    }
    #table-patient thead tr th{
        background: #fff;
        color: #000;
        font-size: 18px;
    }
    .doctor:hover, .user:hover {
        background-color: #6FC36A;
        cursor:pointer;
    }
    
    .current-drilldown{
        width: 55px;
        border-style: solid;
        border-color: #FBD23F;
        border-width: 5px;
        border-radius: 10px
    }
</style>

<?php
if(count($duplicatedRecord) > 0){
    echo "<h3 style='color:red'>duplicated</h3><ul>";
    foreach ($duplicatedRecord as $value){
        $ptcodefull = $value["ptcodefull"];
        $hptcode = $value["hptcode"];
        $rstat = $value["rstat"];
        $update = $value["update_date"];
        echo "<li style='color:red'>$ptcodefull,$hptcode Status: $rstat (updateAt:$update)</li>";
    }
    echo "</ul>";
}


foreach ($doctorList as $key => $val) {
    //\appxq\sdii\utils\VarDumper::dump($val);
    $inspected += $val['inspected'];
    $inspecting += $val['inspecting'];
    $inspect_start += $val['startinspect'];
    $inspect_total += $val['total'];
}
$sitecode = \Yii::$app->user->identity->userProfile->sitecode;
if ($worklistno != $sitecode && $worklistno != '') {
    $manage_monitor = true;
} else {
    $manage_monitor = false;
}

if (($hospital == $sitecode && Yii::$app->user->can('adminsite')) || Yii::$app->user->can('administrator')) {
    $manage_monitor = true;
} else {
    $manage_monitor = false;
}

$session = \Yii::$app->session;
$filter_change = $session['filter_count'];

//$inspect_total = $inspected + $inspecting + $inspect_start;
$sdate = DateTime::createFromFormat('d/m/Y', $startDate);
$thdate_s = ToDate::ToThaiDate($sdate->format('Y-m-d'));

$edate = DateTime::createFromFormat('d/m/Y', $endDate);
$thdate_e = ToDate::ToThaiDate($edate->format('Y-m-d'));

$headMonitor = $sitename . " เลขที่ Worklist " . $worklistno . " ณ วันที่ " . $thdate_s['d'] . ' ' . $thdate_s['m'] . ' พ.ศ. ' . $thdate_s['y'] . ' ' . " ถึง " . $thdate_e['d'] . ' ' . $thdate_e['m'] . ' พ.ศ. ' . $thdate_e['y'];
?>
<div id="tableClone"></div>
<div id="monitoring-div" class="table-responsive">
    <div class="col-md-5">
        <label class="control-label">เลขที่ Worklist :</label>
        <?php
        echo Select2::widget([
            'name' => 'worklistno',
            'id' => 'worklistno',
            'options' => ['placeholder' => 'เลือกข้อมูล...'],
            'data' => [$resWorkDefault['id'] => $resWorkDefault['id'] . ' : ' . $resWorkDefault["sitecode"]],
            'value' => $resWorkDefault['id'],
            'pluginOptions' => [
                'allowClear' => true,
                'minimumInputLength' => 0, //ต้องพิมพ์อย่างน้อย 3 อักษร ajax จึงจะทำงาน
                'ajax' => [
                    'url' => '/usfinding/worklist/get-worklist-number',
                    'dataType' => 'json', //รูปแบบการอ่านคือ json
                    'data' => new JsExpression('function(params) { 
                            return {q:params.term}; 
                            }
                         '),
                ],
                'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                'templateResult' => new JsExpression('function(city) { return city.text; }'),
                'templateSelection' => new JsExpression('function (city) { return city.text; }'),
            ]
        ]);
        ?>
    </div>

    <div class="col-md-5">
        <label>จำแนกโดย :  </label>
        <select id="filter-change" name="filter-change" class="form-control">
            <option value="1" <?= $filter_change == '1' ? 'selected' : '' ?> >แพทย์ผู้ตรวจ</option>
            <option value="2" <?= $filter_change == '2' ? 'selected' : '' ?> >เจ้าหน้าที่ลงข้อมูล</option>
            <!--<option value="3" <?= $filter_change == '3' ? 'selected' : '' ?> >ห้องตรวจ</option>-->
        </select>
    </div>
    <div class="clearfix"></div>
    <br/>
    <table id="monitoring-table" class="table" width="100%">
        <tr>
            <td>
                <label style="font-size: 18px;padding: 5px 10px;margin-left: 8px"class="alert alert-warning pull-left"><strong ><?= $headMonitor ?></strong></label>

                <button id="btn-help" class="btn btn-warning pull-right" style="font-size:16px;margin-right: 8px;"><i class="glyphicon glyphicon-question-sign fa-2"> </i><strong> Help</strong></button> 

                <button id="btn-setting" class="btn btn-success pull-right" style="font-size:16px;margin-right: 5px"><i class="glyphicon glyphicon-cog fa-2"> </i><strong> Setting</strong></button>
                <?php if(\Yii::$app->user->can("administrator")) :?>
                <a id="btn-setting" target="_blank" href="<?=Url::to('/usfinding/monitoring/log-stash')?>" class="btn btn-info pull-right" style="font-size:16px;margin-right: 5px"><i class="glyphicon glyphicon-list fa-2"> </i><strong> Log Stash</strong></a> 
                <?php endif; ?>
                <div class="clearfix"></div>
                <div class="dropdown pull-right">
                    <button class="btn btn-info dropdown-toggle" type="button" data-toggle="dropdown"><i class="fa fa-download"></i> <b>Export US Monitor</b>
                        <span class="caret"></span></button>
                    <ul class="dropdown-menu">
                        <li><a id="word-export" href="#">Microsoft Word</a></li>
                        <li><a id="excel-export" href="#">Microsoft Excel</a></li>
                    </ul>
                </div>
                <div class="panel-success2" >

                    <div class="panel-heading2 " style="border-radius: 10px 10px 0 0;">

                        <table>
                            <tr>
                                <td rowspan="2">
                                    <i class="fa fa-desktop" style="font-size:70px;padding-right:15px;"></i>
                                </td>
                                <td>
                                    <label style="font-size:32px"><strong>Monitoring System</strong></label><br/>
                                    <label style="font-size:22px">ระบบติดตามกิจกรรมของสถานบริการ</label>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="col-md-12" style="padding:15px 0 15px 25px">
                    <label style="font-size:22px;">
                        <i class="glyphicon glyphicon-time"></i>
                        <strong>User online</strong>(<?= $online_amt == '' ? 0 : $onlne_amt ?>/<?= $online_max == '' ? 0 : $online_max ?>)
                        ,
                    </label>&nbsp;
                    <label style="font-size:22px;">
                        <strong>ในฐานข้อมูล</strong>(<?= $base_amt == '' ? 0 : $base_amt ?>)
                        ,
                    </label>&nbsp;
                    <label style="font-size:22px;" >
                        <strong>ตรวจเสร็จแล้ว</strong>(<a href="javascript:void(0)" amt-number="<?= $inspected == null ? 0 : $inspected ?>" id="inspected-all"><span style="font-size:22px;" class="label label-success"><?= $inspected == null ? 0 : $inspected ?></span></a>)
                        ,
                    </label>&nbsp;
                    <label style="font-size:22px;" >
                        <strong>กำลังตรวจ</strong>(<a href="javascript:void(0)" amt-number="<?= $inspecting == null ? 0 : $inspecting ?>" id="inspecting-all"><span style="font-size:22px;" class="label label-primary"><?= $inspecting == null ? 0 : $inspecting ?></span></a>)
                        ,
                    </label>&nbsp;
                    <label style="font-size:22px;" >
                        <strong>เริ่มตรวจ</strong>(<a href="javascript:void(0)" amt-number="<?= $inspect_start == null ? 0 : $inspect_start ?>" id="startinspect-all"><span style="font-size:22px;" class="label label-warning"><?= $inspect_start == null ? 0 : $inspect_start ?></span></a>)
                        ,
                    </label>&nbsp;
                    <label style="font-size:22px;" >
                        <strong>รวม</strong>(<a href="javascript:void(0)" amt-number="<?= $inspect_total == '' ? 0 : $inspect_total ?>" id="patient-total"><span style="font-size:22px;" class="label label-info"><?= $inspect_total == '' ? 0 : $inspect_total ?></span></a>)
                    </label>
                </div>
                <div id="docx">
                    <div class="table-hover WordSection1" id="table-monitor">
                        <table  id="table2" class="table table-bordered" width="100%" style="font-size:16px">
                            <thead>
                                <tr >
                                    <th style="text-align:center;"><label >Room</label></th>
                                    <th style="text-align:center;" width="25%" align="center"><label  >ชื่อแพทย์</label></th>
                                    <th style="text-align:center;" width="10%"><label >รูปภาพ</label></th>
                                    <th style="text-align:center;"><label >ชื่อ จนท. ลงข้อมูล</label></th>
                                    <th style="text-align:center;" width="15%"><label >จำนวนตรวจ</label><br/>
                                        <label style="font-size:14px">ตรวจเสร็จ / กำลังตรวจ / เริ่มตรวจ</label>
                                    </th>
                                    <th style="text-align:center;" width="13%"><label >จำนวนภาพ
                                            <br/>MIN/AVG/MAX
                                        </label></th>
                                    <th style="" width="10%"><label >จำนวนข้อผิดพลาด</label></th>

                                </tr>
                            </thead>
                            <tbody>
                                <tr id="filter-tr">
                                    <td colspan="8">

                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="8" style="background: #E6E6E6;">
                                        <i class="glyphicon glyphicon-flag fa-2x"></i><label style="font-size:20px;">ห้องตรวจ Ultrasound</label> 
                                        <?php if ($manageRoom) { ?>
                                            <a href="javascript:void(0)" id="btn-addroom" style="font-size:16px;" class="label label-success atag">+</a>
                                        <?php } ?>
                                    </td>
                                </tr>
                                <?php
                                $room = 1;

                                foreach ($doctorList as $key => $val) {
                                    $isValidate = 0;
                                    ?>
                                    <tr style="color:gray;font-size: 18px;">
                                        <td style="text-align:center;">
                                            <?= $val['room_name'] == '' ? ($key + 1) : $val['room_name'] ?><br/>
                                            <?php if (($val['doctor_code'] == '' && ($val['inspecting'] == '0' || $val['inspecting'] == '')) && $manageRoom) { ?>
                                                <a href="javascript:void(0)" onclick="removeRoom('<?= $val['room_name'] ?>', 'us-room')" id="btn-remove-room" style="font-size:16px;" class="label label-danger atag"> <i class="fa fa-trash"></i> </a>
                                            <?php } ?>
                                        </td>
                                        <?php
                                        if ($manage_monitor) { // สามารถจัดการ ข้อมูลแพทย์ และเจ้าหน้าที่ และสามารถ drilldown ข้อมูลได้
                                            $drilldown = "";
                                            ?>
                                            <td class="doctor" 
                                                onclick="onDoctor('<?= $val['doctor_code'] ?>', '<?= $val['room_name'] ?>', 'us-room')" >
                                                    <?= $val['doctor_name'] == '' ? 'ไม่ระบุ' : $val['doctor_name'] ?>
                                            </td>
                                            <td style="font-size:16px;text-align:center;" class="user" onclick="onViewUser('<?= $val['user_id'] ?>', '<?= $val['room_name'] ?>', 'us-room')">
                                                <img id="user_pic" src="<?= $val['user_image'] == '' || $val['user_image'] == '/' ? Yii::getAlias('@backendUrl') . "/img/anonymous.jpg" : $val['user_image']; ?>" width="80px" class="img-thumbnail" alt="Cinque">
                                            </td>

                                            <?php
                                        } else if (!$manage_monitor) {// ไม่สามารถจัดการ ข้อมูลแพทย์ และเจ้าหน้าที่ แต่สามารถ drilldown ข้อมูลได้ ในกรณีเป็นหน่วยงานของตัวเอง
                                            $drilldown = "false";
                                            ?>
                                            <td ><?= $val['doctor_name'] == '' ? 'ไม่ระบุ' : $val['doctor_name'] ?></td>
                                            <td style="font-size:16px;text-align:center;" class="user" >
                                                <img id="user_pic" src="<?= $val['user_image'] == '' || $val['user_image'] == '/' ? Yii::getAlias('@backendUrl') . "/img/anonymous.jpg" : $val['user_image']; ?>" width="80px" class="img-thumbnail" alt="Cinque">
                                            </td>
                                        <?php } else { // ไม่สามารถจัดการข้อมูลใดๆ ได้ ดูได้แค่ภาพรวมของการตรวจอัลตร้าซาวด์   ?> 
                                            <td ><?= $val['doctor_name'] == '' ? 'ไม่ระบุ' : $val['doctor_name'] ?></td>
                                            <td style="font-size:16px;text-align:center;"  class="user" >
                                                <img id="user_pic" src="<?= $val['user_image'] == '' || $val['user_image'] == '/' ? Yii::getAlias('@backendUrl') . "/img/anonymous.jpg" : $val['user_image']; ?>" width="80px" class="img-thumbnail" alt="Cinque">
                                            </td>
                                        <?php } ?>

                                        <td style="font-size:16px;"><?= $val['user_fullname'] == '' ? 'ไม่ระบุ' : $val['user_fullname'] ?></td>
                                        <td style="font-size:22px;text-align:center;font-size:22px;">
                                            <label ><a style="color:green" href='javascript:void(0)' 
                                                       data-val="<?= $val['room_name'] ?>"
                                                       doctor-code="<?= $val['doctor_code'] ?>"
                                                       amt-number ="<?= $val['inspected'] ?>"
                                                       id="patient-drop<?= $drilldown ?>"><?= $val['inspected'] ?>
                                                    <input id="user-id" type="hidden" value="<?= $val['user_id'] ?>">
                                                    <input id="inspect-type" type="hidden" value="inspected">
                                                </a></label> 
                                            / <label ><a style="color:blue" href='javascript:void(0)' data-val="<?= $val['room_name'] ?>" 
                                                         doctor-code="<?= $val['doctor_code'] ?>"
                                                         amt-number ="<?= $val['inspecting'] ?>"
                                                         id="patient-drop<?= $drilldown ?>"><?= $val['inspecting'] ?>
                                                    <input id="user-id" type="hidden" value="<?= $val['user_id'] ?>">
                                                    <input id="inspect-type" type="hidden" value="inspecting">
                                                </a></label>
                                            / <label ><a style="color:orange" href='javascript:void(0)' data-val="<?= $val['room_name'] ?>"
                                                         doctor-code="<?= $val['doctor_code'] ?>"
                                                         amt-number ="<?= $val['startinspect'] ?>"
                                                         id="patient-drop<?= $drilldown ?>"><?= $val['startinspect'] ?>
                                                    <input id="user-id" type="hidden" value="<?= $val['user_id'] ?>">
                                                    <input id="inspect-type" type="hidden" value="startinspect">
                                                </a></label>
                                        </td>
                                        <td style="text-align:center;font-size:22px;"><label style="color:gray"><?= $val['usmin'] ?></label> / <label style="color:gray"><?= $val['usavg'] ?></label> / <label style="color:gray"><?= $val['usmax'] ?></label></td>
                                        <td style="text-align:center;font-size:22px;">
                                            <label ><a style="color:red" href='javascript:void(0)' 
                                                       data-val="<?= $val['room_name'] ?>"
                                                       doctor-code="<?= $val['doctor_code'] ?>"
                                                       amt-number ="<?= $val['mistake'] ?>"
                                                       id="patient-drop<?= $drilldown ?>"><?= $val['mistake'] ?>
                                                    <input id="user-id" type="hidden" value="<?= $val['user_id'] ?>">
                                                    <input id="inspect-type" type="hidden" value="mistake">
                                                </a></label>
                                        </td>

                                    </tr>
                                    <?php
                                    $room ++;
                                }
                                ?>
                                <tr>
                                    <td colspan="8" style="background: #E6E6E6;">
                                        <i class="fa fa-user fa-2x" aria-hidden="true"></i>
                                        <label style="font-size:20px"><strong>Exit Nurse</strong></label>
                                        <?php if ($manageRoom) { ?>
                                            <a href="javascript:void(0)" id="btn-exitnurse" style="font-size:16px;" class="label label-success atag">+</a>
                                        <?php } ?>
                                        <br/>
                                    </td></tr>
                                <tr>
                                    <?php foreach ($nurseList as $key => $val) { ?>
                                    <tr>
                                        <td style="text-align:center;">
                                            <?= $val['room_name'] ?><br/>
                                            <?php if ($val['user_id'] == '' && $manageRoom) { ?>
                                                <a id="atag" href="javascript:void(0)" onclick="removeRoom('<?= $val['room_name'] ?>', 'exit-nurse')" id="btn-remove-room" style="font-size:16px;" class="label label-danger"> <i class="fa fa-trash"></i> </a>
                                            <?php } ?>
                                        </td>
                                        <?php
                                        if ($manage_monitor) {
                                            $drilldown = "";
                                            ?>
                                            <td class="doctor" 
                                                onclick="onDoctor('<?= $val['doctor_code'] ?>', '<?= $val['room_name'] ?>', 'exit-nurse')" >
                                                    <?= $val['doctor_name'] == '' ? 'ไม่ระบุ' : $val['doctor_name'] ?>
                                            </td>
                                            <?php
                                        } else {
                                            //$drilldown = "false";
                                            ?>
                                            <td ><?= $val['doctor_name'] == '' ? 'ไม่ระบุ' : $val['doctor_name'] ?></td>
                                        <?php } ?>
                                        <td style="font-size:16px;text-align:center;" class="user" onclick="onViewUser('<?= $val['user_id'] ?>', '<?= $val['room_name'] ?>', 'exit-nurse')">
                                            <img src="<?= $val['user_image'] == '' || $val['user_image'] == '/' ? Yii::getAlias('@backendUrl') . "/img/anonymous.jpg" : $val['user_image']; ?>" width="80px" class="img-thumbnail" alt="Cinque">
                                        </td>
                                        <td style="font-size:16px;"><?= $val['user_fullname'] == '' ? 'ไม่ระบุ' : $val['user_fullname'] ?></td>
                                        <td style="font-size:22px;text-align:center;font-size:26px;"><label style="color:green">0</label> / <label style="color:blue">0</label> / <label style="color:orange">0</label></td>
                                        <td style="font-size:22px;text-align:center;font-size:26px;"><label style="color:gray">0</label> / <label style="color:gray">0</label> / <label style="color:gray">0</label></td>
                                        <td style="font-size:22px;text-align:center;font-size:26px;"><label style="color:red">0</label> / <label style="color:blue">0</label></td>

                                    </tr>
                                <?php } ?>
                                <tr>
                                    <td colspan="8" style="background: #E6E6E6;">
                                        <i class="fa fa-truck fa-2x" aria-hidden="true"></i>
                                        <label style="font-size:20px"><strong>Refer</strong></label>
                                        <?php if ($manageRoom) { ?>
                                            <a href="javascript:void(0)" id="btn-refer" style="font-size:16px;" class="label label-success atag">+</a>
                                        <?php } ?>
                                    </td>
                                </tr>
                                <?php foreach ($referList as $key => $val) { ?>
                                    <tr>
                                        <td style="text-align:center;">
                                            <?= $val['room_name'] ?><br/>
                                            <?php if ($val['user_id'] == '' && $manageRoom) { ?>
                                                <a id="atag" href="javascript:void(0)" onclick="removeRoom('<?= $val['room_name'] ?>', 'refer')" id="btn-remove-room" style="font-size:16px;" class="label label-danger"> <i class="fa fa-trash"></i> </a>
                                            <?php } ?>
                                        </td>
                                        <?php
                                        if ($manage_monitor) {
                                            $drilldown = "";
                                            ?>
                                            <td class="doctor" 
                                                onclick="onDoctor('<?= $val['doctor_code'] ?>', '<?= $val['room_name'] ?>', 'refer')" >
                                                    <?= $val['doctor_name'] == '' ? 'ไม่ระบุ' : $val['doctor_name'] ?>
                                            </td>
                                            <?php
                                        } else {
                                            //$drilldown = "false";
                                            ?>
                                            <td ><?= $val['doctor_name'] == '' ? 'ไม่ระบุ' : $val['doctor_name'] ?></td>
                                        <?php } ?>
                                        <td style="font-size:16px;text-align:center;" class="user" onclick="onViewUser('<?= $val['user_id'] ?>', '<?= $val['room_name'] ?>', 'refer')">
                                            <img src="<?= $val['user_image'] == '' || $val['user_image'] == '/' ? Yii::getAlias('@backendUrl') . "/img/anonymous.jpg" : $val['user_image']; ?>" width="80px" class="img-thumbnail" alt="Cinque">
                                        </td>
                                        <td style="font-size:16px;"><?= $val['user_fullname'] == '' ? 'ไม่ระบุ' : $val['user_fullname'] ?></td>
                                        <td style="font-size:22px;text-align:center;font-size:26px;"><label style="color:green">0</label> / <label style="color:blue">0</label> / <label style="color:orange">0</label></td>
                                        <td style="font-size:22px;text-align:center;font-size:26px;"><label style="color:gray">0</label> / <label style="color:gray">0</label> / <label style="color:gray">0</label></td>
                                        <td style="font-size:22px;text-align:center;font-size:26px;"><label style="color:red">0</label> / <label style="color:blue">0</label></td>

                                    </tr>
                                <?php } ?>
                                <tr>
                                    <td colspan="8" style="background: #E6E6E6;">
                                        <i class="glyphicon glyphicon-exclamation-sign fa-2x" aria-hidden="true"></i>
                                        <label style="font-size:20px"><strong>อื่นๆ</strong></label>
                                        <?php if ($manageRoom) { ?>
                                            <a href="javascript:void(0)" id="btn-other" style="font-size:16px;" class="label label-success atag">+</a>
                                        <?php } ?>
                                        <br/>
                                    </td>
                                </tr>

                                <?php foreach ($otherList as $key => $val) { ?>
                                    <tr>
                                        <td style="text-align:center;">
                                            <?= $val['room_name'] ?><br/>
                                            <?php if ($val['user_id'] == '' && $manageRoom) { ?>
                                                <a id="atag" href="javascript:void(0)" onclick="removeRoom('<?= $val['room_name'] ?>', 'other')" id="btn-remove-room" style="font-size:16px;" class="label label-danger"> <i class="fa fa-trash"></i> </a>
                                            <?php } ?>
                                        </td>
                                        <?php
                                        if ($manage_monitor) {
                                            $drilldown = "";
                                            ?>
                                            <td class="doctor" 
                                                onclick="onDoctor('<?= $val['doctor_code'] ?>', '<?= $val['room_name'] ?>', 'other')" >
                                                    <?= $val['doctor_name'] == '' ? 'ไม่ระบุ' : $val['doctor_name'] ?>
                                            </td>
                                            <?php
                                        } else {
                                            //$drilldown = "false";
                                            ?>
                                            <td ><?= $val['doctor_name'] == '' ? 'ไม่ระบุ' : $val['doctor_name'] ?></td>
                                        <?php } ?>
                                        <td style="font-size:16px;text-align:center;" class="user" onclick="onViewUser('<?= $val['user_id'] ?>', '<?= $val['room_name'] ?>', 'other')">
                                            <img src="<?= $val['user_image'] == '' || $val['user_image'] == '/' ? Yii::getAlias('@backendUrl') . "/img/anonymous.jpg" : $val['user_image']; ?>" width="80px" class="img-thumbnail" alt="Cinque">
                                        </td>
                                        <td style="font-size:16px;"><?= $val['user_fullname'] == '' ? 'ไม่ระบุ' : $val['user_fullname'] ?></td>
                                        <td style="font-size:22px;text-align:center;font-size:26px;"><label style="color:green">0</label> / <label style="color:blue">0</label> / <label style="color:orange">0</label></td>
                                        <td style="font-size:22px;text-align:center;font-size:26px;"><label style="color:gray">0</label> / <label style="color:gray">0</label> / <label style="color:gray">0</label></td>
                                        <td style="font-size:22px;text-align:center;font-size:26px;"><label style="color:red">0</label> / <label style="color:blue">0</label></td>

                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </td>
        </tr>
    </table>
        
     <div class="modal" id="modal-patient-inspect" tabindex="-1" role="dialog">
           <div class="modal-dialog" style="width:70%;" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title"></h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal" id="modal-patient-error" tabindex="-2" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Error Message.</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    <?php
    $this->registerJs('
           
        ');
    $this->registerJs("


        $('#btn-addroom').on('click', function(){
            var worklist_id = '$worklistno';
            var room_type = 'us-room';
            var spinner = $('#load-spinner');
            spinner.modal();
            $('.load-text').html('<div class=\"text-center\"><i class=\"fa fa-spinner fa-spin fa-2x\" style=\"margin:20px 0;\"></i> กำลังสร้างห้อง Ultrasound ใหม่ </div>');
            $.ajax({
                url:'" . Url::to('/usfinding/monitoring/add-usroom/') . "',
                method:'POST',
                type:'HTML',
                data:{worklist_id:worklist_id},
                success:function(result){
                    showMonitoring();
                    spinner.modal('hide');
                }
            })
        });
        
        function removeRoom(room, room_type){
            var worklist_id = '$worklistno';
            var spinner = $('#load-spinner');
            spinner.modal();
            $('.load-text').html('<div class=\"text-center\"><i class=\"fa fa-spinner fa-spin fa-2x\" style=\"margin:20px 0;\"></i> กำลังลบห้อง Ultrasound ออก... </div>');
            $.ajax({
                url:'" . Url::to('/usfinding/monitoring/remove-usroom/') . "',
                method:'POST',
                type:'HTML',
                data:{room_name:room,
                room_type:room_type,
                worklist_id:worklist_id},
                success:function(result){
                    showMonitoring();
                    spinner.modal('hide');
                }
            })
        }
        
        $('#btn-exitnurse').on('click', function(e){
            e.preventDefault();
            var worklist_id = '$worklistno';
            var room_type = 'exit-nurse';
            var spinner = $('#load-spinner');
            spinner.modal();
            $('.load-text').html('<div class=\"text-center\"><i class=\"fa fa-spinner fa-spin fa-2x\" style=\"margin:20px 0;\"></i> กำลังสร้าง Exit Nurse ใหม่... </div>');
            $.ajax({
                url:'" . Url::to('/usfinding/monitoring/add-exitnurse/') . "',
                method:'POST',
                type:'HTML',
                data:{worklist_id:worklist_id,
                room_type:room_type},
                success:function(result){
                    showMonitoring();
                    
                }
            })
        });
        
        $('#btn-refer').on('click', function(e){
            e.preventDefault();
            var worklist_id = '$worklistno';
            var room_type = 'refer';
            var spinner = $('#load-spinner');
            spinner.modal();
            $('.load-text').html('<div class=\"text-center\"><i class=\"fa fa-spinner fa-spin fa-2x\" style=\"margin:20px 0;\"></i> กำลังสร้าง Refer ใหม่... </div>');
            $.ajax({
                url:'" . Url::to('/usfinding/monitoring/add-refer/') . "',
                method:'POST',
                type:'HTML',
                data:{worklist_id:worklist_id,
                room_type:room_type},
                success:function(result){
                    showMonitoring();
                    
                }
            })
        });
        
        $('#btn-other').on('click', function(e){
            e.preventDefault();
            var worklist_id = '$worklistno';
            var room_type = 'refer';
            var spinner = $('#load-spinner');
            spinner.modal();
            $('.load-text').html('<div class=\"text-center\"><i class=\"fa fa-spinner fa-spin fa-2x\" style=\"margin:20px 0;\"></i> กำลังสร้าง Other ใหม่... </div>');
            $.ajax({
                url:'" . Url::to('/usfinding/monitoring/add-other/') . "',
                method:'POST',
                type:'HTML',
                data:{worklist_id:worklist_id,
                room_type:room_type},
                success:function(result){
                    showMonitoring();
                    
                }
            })
        });
        
        function onDoctorChange(dcode,room, room_type){
            console.log(dcode);
            var worklist_id = '$worklistno';
                var spinner = $('#load-spinner');
            spinner.modal();
            $('.load-text').html('<div class=\"text-center\"><i class=\"fa fa-spinner fa-spin fa-2x\" style=\"margin:20px 0;\"></i> กำลังอัพเดทแพทย์ประจำห้องตรวจ... </div>');
            var doctor_code = dcode;
            $.ajax({
                url:'" . Url::to('/usfinding/monitoring/doctor-change/') . "',
                method:'post',
                data:{
                    doctor_code:dcode,
                    room_name:room,
                    room_type:room_type,
                    worklist_id:worklist_id
                },
                type:'HTML',
                success:function(result){
                    showMonitoring();
                }
            });
        }
        
        function onDoctor(dcode,room, room_type){
            $('#modal-doctor').modal();
            var divshow = $('#modal-doctor .modal-dialog .modal-content');
            var doctor_code = dcode;
            $.ajax({
                url:'" . Url::to('/usfinding/monitoring/doctor-ultrasound/') . "',
                method:'post',
                data:{
                    doctor_code:dcode,
                    room_name:room,
                    room_type:room_type
                },
                type:'HTML',
                success:function(result){
                   divshow.empty();
                   divshow.html(result);
                }
            });
        }
        
        function onViewUser(user_id,room, room_type){
            //console.log(user_id);
            $('#modal-personal-detail').modal();
            var divshow = $('#modal-personal-detail .modal-dialog .modal-content');
            $.ajax({
                url:'" . Url::to('/usfinding/monitoring/user-ultrasound/') . "',
                method:'post',
                data:{
                    user_id:user_id,
                    room_name:room,
                    room_type:room_type
                },
                type:'HTML',
                success:function(result){
                   divshow.empty();
                   divshow.html(result);
                }
            });
        }
        
        function onUserChange(user_id,room, room_type){
            var worklist_id = '$worklistno';
            var spinner = $('#load-spinner');
            spinner.modal('show');
            $('.load-text').html('<div class=\"text-center\"><i class=\"fa fa-spinner fa-spin fa-2x\" style=\"margin:20px 0;\"></i> กำลังอัพเดทเจ้าหน้าที่ประจำห้องตรวจ... </div>');
            
            $.ajax({
                url:'" . Url::to('/usfinding/monitoring/user-change/') . "',
                method:'post',
                data:{
                    user_id:user_id,
                    room_name:room,
                    room_type:room_type,
                    worklist_id:worklist_id
                },
                type:'HTML',
                success:function(result){
                    showMonitoring();
                }
            });
        }
        
        $('tr').on('click','#patient-drop',function(){
        $('.trdrop').remove();
        var user_id = $(this).children('#user-id').val();
        var inspect_type = $(this).children('#inspect-type').val();
        var room_name = $(this).attr('data-val');
        var doctorcode = $(this).attr('doctor-code');
        var amt_number = $(this).attr('amt-number');
        var hospital = $('#inputHospital').val();
        $('.current-drilldown').removeClass('current-drilldown');
        $(this).parent().addClass('current-drilldown');

         $(this).parent().parent().parent().after(`
            <tr class=\'trdrop\'> 
                <td style=\'background:#6FC36A;\' colspan=\'8\'>
                    <div style=\'padding-right:15px;\'>
                        <button class=\'btn btn-danger pull-right\' onclick=\'closeTr(this);\' >
                            <span class=\'glyphicon glyphicon-remove\'></span> <strong>Close</strong>
                        </button>
                        <button class=\'btn btn-primary pull-right btnReload\' onclick=\'reloadPatient(\"`+user_id+`\",\"`+inspect_type+`\",\"`+doctorcode+`\",\"`+amt_number+`\");\'>
                            <span class=\'glyphicon glyphicon-refresh\'></span> <strong>Reload</stromg>
                        </button> 
                    </div><br/><br/>
                    <div id=\'patient-show\'></div>
                </td> 
            </tr>`
           
         );
//          $('.btnReload').attr('onclick','reloadPatient('+user_id+','+inspect_type+','+doctorcode+','+amt_number+');');
            $('#patient-show').html('<div style=\'text-align:center;color:#fff;\'><i class=\"fa fa-circle-o-notch fa-spin fa-fw fa-3x\"></i></div>');
            var start_date = '$startDate';
            var end_date = '$endDate';
            var worklist_id = '$worklistno';
            
            $.ajax({
                url:'" . Url::to('/usfinding/monitoring/patient-inspected/') . "',
                method:'post',
                data:{
                    user_id:user_id,
                    worklist_id:worklist_id,
                    inspect_type:inspect_type,
                    startDate:start_date,
                    endDate:end_date,
                    doctorcode:doctorcode,
                    amt_number:amt_number,
                    hospital:hospital,
                },
                type:'HTML',
                success:function(result){
                   $('#patient-show').empty();
                   $('#patient-show').html(result);
                }
            });
            
        });
        
        $('#inspected-all').click(function(){
            var start_date = '$startDate';
            var end_date = '$endDate';
            var worklist_id = '$worklistno';
            var amt_number = $(this).attr('amt-number');
            var hospital = $('#inputHospital').val();
            $('#modal-patient-inspect').modal();
            $('#modal-patient-inspect').find('.modal-body').html('<center><i class=\"fa fa-spinner fa-spin fa-2x\" style=\"margin:20px 0;\"></i></center>');
            $('#modal-patient-inspect').find('.modal-header .modal-title').html('ตรวจแล้วทั้งหมด ' + amt_number +' ราย');
            
            $.ajax({
                url:'" . Url::to('/usfinding/monitoring/patient-inspected/') . "',
                method:'post',
                data:{
                    worklist_id:worklist_id,
                    inspect_type:'inspected-all',
                    startDate:start_date,
                    endDate:end_date,
                    amt_number:amt_number,
                    hospital:hospital,
                },
                type:'HTML',
                success:function(result){
                   $('#modal-patient-inspect').find('.modal-body').html(result);
                }
            });
        });
        
        $('#inspecting-all').click(function(){
            var start_date = '$startDate';
            var end_date = '$endDate';
            var worklist_id = '$worklistno';
            var amt_number = $(this).attr('amt-number');
            var hospital = $('#inputHospital').val();
            $('#modal-patient-inspect').modal();
            $('#modal-patient-inspect').find('.modal-body').html('<center><i class=\"fa fa-spinner fa-spin fa-2x\" style=\"margin:20px 0;\"></i></center>');
            $('#modal-patient-inspect').find('.modal-header .modal-title').html('กำลังตรวจอยู่ทั้งหมด ' + amt_number +' ราย');
            
            $.ajax({
                url:'" . Url::to('/usfinding/monitoring/patient-inspected/') . "',
                method:'post',
                data:{
                    worklist_id:worklist_id,
                    inspect_type:'inspecting-all',
                    startDate:start_date,
                    endDate:end_date,
                    amt_number:amt_number,
                    hospital:hospital,
                },
                type:'HTML',
                success:function(result){
                   $('#modal-patient-inspect').find('.modal-body').html(result);
                }
            });
        });
        
        $('#startinspect-all').click(function(){
            var start_date = '$startDate';
            var end_date = '$endDate';
            var worklist_id = '$worklistno';
            var amt_number = $(this).attr('amt-number');
            var hospital = $('#inputHospital').val();
            $('#modal-patient-inspect').modal();
            $('#modal-patient-inspect').find('.modal-body').html('<center><i class=\"fa fa-spinner fa-spin fa-2x\" style=\"margin:20px 0;\"></i></center>');
            $('#modal-patient-inspect').find('.modal-header .modal-title').html('เริ่มตรวจทั้งหมด ' + amt_number +' ราย');
            
            $.ajax({
                url:'" . Url::to('/usfinding/monitoring/patient-inspected/') . "',
                method:'post',
                data:{
                    worklist_id:worklist_id,
                    inspect_type:'startinspect-all',
                    startDate:start_date,
                    endDate:end_date,
                    amt_number:amt_number,
                    hospital:hospital,
                },
                type:'HTML',
                success:function(result){
                   $('#modal-patient-inspect').find('.modal-body').html(result);
                }
            });
        });
        
        $('#patient-total').click(function(){
            var start_date = '$startDate';
            var end_date = '$endDate';
            var worklist_id = '$worklistno';
            var amt_number = $(this).attr('amt-number');
            var hospital = $('#inputHospital').val();
            $('#modal-patient-inspect').modal();
            $('#modal-patient-inspect').find('.modal-body').html('<center><i class=\"fa fa-spinner fa-spin fa-2x\" style=\"margin:20px 0;\"></i></center>');
            $('#modal-patient-inspect').find('.modal-header .modal-title').html('รวมทั้งหมด ' + amt_number +' ราย');
            
            $.ajax({
                url:'" . Url::to('/usfinding/monitoring/patient-inspected/') . "',
                method:'post',
                data:{
                    worklist_id:worklist_id,
                    inspect_type:'patient-total',
                    startDate:start_date,
                    endDate:end_date,
                    amt_number:amt_number,
                    hospital:hospital,
                },
                type:'HTML',
                success:function(result){
                   $('#modal-patient-inspect').find('.modal-body').html(result);
                }
            });
        });
    
    function reloadPatient(user_id,inspect_type,doctorcode,amt_number){
            $('#patient-show').html('<div style=\'text-align:center;color:#fff;\'><i class=\"fa fa-circle-o-notch fa-spin fa-fw fa-3x\"></i></div>');
            var start_date = '$startDate';
            var end_date = '$endDate';
            var worklist_id = '$worklistno';
            var hospital = $('#inputHospital').val();
             
            //console.log(user_id+' '+inspect_type+' '+doctorcode+' '+amt_number+'  adasdasd');
            $.ajax({
                url:'" . Url::to('/usfinding/monitoring/patient-inspected/') . "',
                method:'post',
                data:{
                    user_id:user_id,
                    worklist_id:worklist_id,
                    inspect_type:inspect_type,
                    startDate:start_date,
                    endDate:end_date,
                    doctorcode:doctorcode,
                    amt_number:amt_number,
                    hospital:hospital,
                },
                type:'HTML',
                success:function(result){
                   $('#patient-show').empty();
                   $('#patient-show').html(result);
                }
            });
    }
     function closeTr(t){
        $('.current-drilldown').removeClass('current-drilldown');
        $(t).parent().parent().parent().remove();
     }
     
    $('#btn-setting').click(function(){
        var modal_setting = $('#modal-setting .modal-dialog .modal-content');
        $('#modal-setting').modal();
        $.ajax({
            url:'" . Url::to('/usfinding/monitoring/setting') . "',
            method:'POST',
            data:{
            
            },
            type:'HTML',
            success:function(result){
                modal_setting.empty();
                modal_setting.html(result);
            },
            error:function(){
            
            }
        });
    });
    
    function showMonitoringFillter(){
        var monDiv = $('#reportUSFinding');
        var startdate = $('#inputStartDate').val();
        var enddate = $('#inputEndDate').val();
        var hospital = $('#inputHospital').val();
        
        monDiv.html('<div style=\'text-align:center;\'><i class=\"fa fa-spinner fa-pulse fa-fw fa-3x\"></i></div>');
        $.ajax({
            url:'" . Url::to('/usfinding/monitoring/ultrasound-data') . "',
            method:'post',
            data:{
                startDate:startdate,
                endDate:enddate,
                hospital:hospital
            },
            type:'HTML',
            success:function(result){
                monDiv.empty();
                monDiv.html(result);
            }
        });
    }
    
    function showMonitoring(){
        var monDiv = $('#reportUSFinding');
        var startdate = $('#inputStartDate').val();
        var enddate = $('#inputEndDate').val();
        
        monDiv.html('<div style=\'text-align:center;\'><i class=\"fa fa-spinner fa-pulse fa-fw fa-3x\"></i></div>');
        $.ajax({
            url:'" . Url::to('/usfinding/monitoring/ultrasound-data') . "',
            method:'post',
            data:{
                startDate:startdate,
                endDate:enddate,
            },
            type:'HTML',
            success:function(result){
                monDiv.empty();
                monDiv.html(result);
            }
        });
    }
    
    $('#filter-change').on('change',function(){
       var filter_count= $('#filter-change').val();
       
       $.ajax({
            url:'" . Url::to('/usfinding/monitoring/set-setting') . "',
            method:'POST',
            data:{
                filter_count:filter_count
            },
            type:'HTML',
            success:function(result){
                showMonitoringFillter()
            },
            error:function(){
            
            }
        });
    });
    
    $('#worklistno').on('change',function(){
       var worklistno= $('#worklistno').val();
       
       $.ajax({
            url:'" . Url::to('/usfinding/monitoring/set-setting') . "',
            method:'POST',
            data:{
                worklistno:worklistno
            },
            type:'HTML',
            success:function(result){
                showMonitoring()
            },
            error:function(){
            
            }
        });
    });
 
");


    $this->registerJs('
    function beforeExport(){
        var tableClone = $("#tableClone");
        var tableMonitor = $("#docx");
        var copy = tableMonitor.clone();
        copy.attr(\'id\', \'tableClone\');
        tableClone.html("<b>' . $headMonitor . '</b><br/><br/>");
        tableClone.append(copy);
        $("#tableClone").hide();
        var usimg = $("#tableClone .user #user_pic");
        var atag = $("#tableClone a#atag");
        var filter_tr = $("#tableClone #filter-tr");
        filter_tr.remove();
        //console.log(tableClone);
        //console.log(atag);
        for(var i=0;i<usimg.length;i++){
           usimg[i].remove();
        }
        
        for(var i=0;i<atag.length;i++){
           atag[i].replaceWith(atag[i].text)
        }
    }
    
    $("#excel-export").click(function(e){ 
       var tableClone = $("#tableClone");
        var tableMonitor = $("#docx");
        var copy = tableMonitor.clone();
        copy.attr(\'id\', \'tableClone\');
        tableClone.html("<b>' . $headMonitor . '</b><br/><br/>");
        tableClone.append(copy);
        $("#tableClone").hide();
        var usimg = $("#tableClone .user #user_pic");
        var atag = $("#tableClone .atag");
        var filter_tr = $("#tableClone #filter-tr");
        filter_tr.remove();
        //console.log(tableClone);
        //console.log(atag);
        for(var i=0;i<usimg.length;i++){
           usimg[i].remove();
        }
        
        for(var i=0;i<atag.length;i++){
           atag[i].replaceWith(atag[i].text)
        }
       this.download="monitoring.xls" 
       ExcellentExport.excel(this, \'tableClone\', \'Report Monitoring Sheet\');
    });
    
    $("#word-export").click(function(e){
       beforeExport()
       this.download="monitoring.doc"
       ExcellentExport.word(this,"tableClone");
    });
    function onThrowError(text){
        $("#modal-patient-error").modal();
        $("#modal-patient-error .modal-dialog .modal-content .modal-body").html(text);
    }
    
');
    ?>
