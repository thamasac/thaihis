<?php
use Yii;
use yii\helpers\Url;
$this->registerJsFile('@web/js/excellentexport.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$title_cca01 = "ตารางที่ 1 ข้อมูลพื้นฐานของผู้เข้าร่วมโครงการ CASCAP: Cholangiocarcinoma Screening and Care Program ที่ทำการตรวจคัดกรองด้วยอัลตราซาวด์ระหว่าง ". $startDate." ถึง ".$endDate." ในเขตพื้นที่ ".$hospitalName;
$user_Site_Code = \Yii::$app->user->identity->userProfile->sitecode;
if(explode(',', $zone)[0]!=null)
    $this->registerJs("
        document.getElementById('data-report-table').style.display = 'block';
        document.getElementById('primeTable').style.display = 'none';
    ");
function checkLiCense($user_Site_Code,$hospitalCode){
                if ($user_Site_Code!=$hospitalCode){
                    return TRUE;}
                else 
                    return FALSE;
            }
?>

<div class="panel-success" > 
    <div class="container">
        <div class="pull-right">
            <a id="report-excel" data-toggle='tooltip' data-original-title='Export รายชื่อทั้งหมด เป็น Excel' class="btn btn-info"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Export to Excel</a>
        </div>
        <label style="font-size:20px"><?= $title_cca01 ?></label>
        <table class="table table-bordered" style="font-size:16px;" id="primeTable"> 
            <tr>
                <td rowspan="3" style="vertical-align:middle"><strong><center>ข้อมูลพื้นฐาน</center></strong></td>
                <td rowspan="2" style="vertical-align:middle"><strong><center>รวมทั้งหมดใน CASCAP</center></strong></td>
                <td colspan="3"><strong><center>เฉพาะในพื้นที่</center></strong></td>
            </tr>
            <tr align="center">
                <td><strong>รวมในพื้นที่</strong></td>
                <td><strong>ชาย</strong></td>
                <td><strong>หญิง</strong></td>
            </tr>
             <tr align="center">
                <td><strong>n (%)</strong></td>
                <td><strong>n (%)</strong></td>
                <td><strong>n (%)</strong></td>
                <td><strong>n (%)</strong></td>
            </tr>
            <tr>
                <td><strong>1. จำนวนผู้เข้าร่วมโครงการ</strong></td>
                <td align="right"><strong><?= number_format($total[all_person]).$testD[0][na] ?></strong></td>
                <td align="right"><strong><?= number_format($total_hcode[all_person])." (".number_format($total_hcode[all_person]*100/$total[all_person],2).")" ?></strong></td>
                <td align="right"><strong><?= number_format($total_male[all_person])." (".number_format($total_male[all_person]*100/$total_hcode[all_person],1).")" ?></strong></td>
                <td align="right"><strong><?= number_format($total_female[all_person])." (".number_format($total_female[all_person]*100/$total_hcode[all_person],1).")" ?></strong></td>
            </tr>
            <tr>
                <td><strong>2. อายุ (ปี)</strong><br>&emsp;20 - 29<br>&emsp;30 - 39<br>&emsp;40 - 49<br>&emsp;50 - 59<br>&emsp;60 - 69<br>&emsp;70 - 79<br>&emsp;80+<br>&emsp;รวม<br>&emsp;ค่าเฉลี่ย ± ส่วนเบี่ยงเบนมาตรฐาน<br>&emsp;ค่ามัธยฐาน (ต่ำสุด : สูงสุด)</td>
                <td align="right">
                    <br><?php  $r = array($total_age[age_20],$total_age[age_30],$total_age[age_40],$total_age[age_50],$total_age[age_60],$total_age[age_70],$total_age[age_80],$total_age[person]);
                    foreach($r as $value) echo number_format($value)."&emsp;"."(".number_format($value*100/$total[all_person],2).")<br>"; 
                    echo number_format($total_age[avg],1)." ± ".number_format($total_age[sd],1)."<br>".number_format($total_median[median])." ( ".number_format($total_age[min])." : ".number_format($total_age[max])." )"?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_age_hcode[age_20],$total_age_hcode[age_30],$total_age_hcode[age_40],$total_age_hcode[age_50],$total_age_hcode[age_60],$total_age_hcode[age_70],$total_age_hcode[age_80],$total_age_hcode[person]);
                    foreach($r as $key => $value) 
                    if(($value==0)||checkLiCense($user_Site_Code,$hospitalCode))
                        echo number_format($value)."&emsp;"."(".number_format($value*100/$total_hcode[all_person],1).")<br>"; 
                    else { ?>
                     <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($value) ?>
                             <input id="data" type="hidden" value="age">
                             <?php switch ($key) { case 0: ?>
                                       <input id="cca02" type="hidden" value="20,29"> 
                                       <input id="doctorname" type="hidden" value="2. อายุ (20,29 ปี) [รวมในพื้นที่]">
                                    <?php break; case 1: ?>
                                       <input id="cca02" type="hidden" value="30,39">
                                       <input id="doctorname" type="hidden" value="2. อายุ (30,39 ปี) [รวมในพื้นที่]">
                                    <?php break; case 2: ?>
                                       <input id="cca02" type="hidden" value="40,49"> 
                                       <input id="doctorname" type="hidden" value="2. อายุ (40,49 ปี) [รวมในพื้นที่]">
                                    <?php break; case 3: ?>
                                       <input id="cca02" type="hidden" value="50,59">  
                                       <input id="doctorname" type="hidden" value="2. อายุ (50,59 ปี) [รวมในพื้นที่]">
                                    <?php break; case 4: ?>
                                       <input id="cca02" type="hidden" value="60,69"> 
                                       <input id="doctorname" type="hidden" value="2. อายุ (60,69 ปี) [รวมในพื้นที่]">
                                    <?php break; case 5: ?>
                                       <input id="cca02" type="hidden" value="70,79">
                                       <input id="doctorname" type="hidden" value="2. อายุ (70,79 ปี) [รวมในพื้นที่]">
                                    <?php break; case 6: ?>
                                       <input id="cca02" type="hidden" value="80,200">
                                       <input id="doctorname" type="hidden" value="2. อายุ (80 ปี ขึ้นไป) [รวมในพื้นที่]">
                                    <?php break; default: ?>
                             <input id="cca02" type="hidden" value="0,200"> 
                             <input id="doctorname" type="hidden" value="2. อายุ (รวม) [รวมในพื้นที่]"><?php } ?>
                             <input id="gender" type="hidden" value="1,2">
                             <input id="zonedata" type="hidden" value="<?= $zone ?>">
                             <input id="state" type="hidden" value="<?php if ($summaryZone=="Cca01Zone") echo cca01zone; else echo cca01; ?>">
                             </a><?= "&emsp;"."(".number_format($value*100/$total_hcode[all_person],1).")" ?><br>
                    <?php  }
                    echo number_format($total_age_hcode[avg],1)." ± ".number_format($total_age_hcode[sd],1)."<br>".number_format($total_median_hcode)." ( ".number_format($total_age_hcode[min])." : ".number_format($total_age_hcode[max])." )"?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_age_male[age_20],$total_age_male[age_30],$total_age_male[age_40],$total_age_male[age_50],$total_age_male[age_60],$total_age_male[age_70],$total_age_male[age_80],$total_age_male[person]);
                    foreach($r as $key => $value) 
                    if(($value==0)||checkLiCense($user_Site_Code,$hospitalCode))  
                        echo number_format($value)."&emsp;"."(".number_format($value*100/$total_male[all_person],1).")<br>"; 
                     else { ?>
                     <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($value) ?>
                             <input id="data" type="hidden" value="age">
                            <?php switch ($key) { case 0: ?>
                                       <input id="cca02" type="hidden" value="20,29"> 
                                       <input id="doctorname" type="hidden" value="2. อายุ (20,29 ปี) [ชาย]">
                                    <?php break; case 1: ?>
                                       <input id="cca02" type="hidden" value="30,39">
                                       <input id="doctorname" type="hidden" value="2. อายุ (30,39 ปี) [ชาย]">
                                    <?php break; case 2: ?>
                                       <input id="cca02" type="hidden" value="40,49"> 
                                       <input id="doctorname" type="hidden" value="2. อายุ (40,49 ปี) [ชาย]">
                                    <?php break; case 3: ?>
                                       <input id="cca02" type="hidden" value="50,59">  
                                       <input id="doctorname" type="hidden" value="2. อายุ (50,59 ปี) [ชาย]">
                                    <?php break; case 4: ?>
                                       <input id="cca02" type="hidden" value="60,69"> 
                                       <input id="doctorname" type="hidden" value="2. อายุ (60,69 ปี) [ชาย]">
                                    <?php break; case 5: ?>
                                       <input id="cca02" type="hidden" value="70,79">
                                       <input id="doctorname" type="hidden" value="2. อายุ (70,79 ปี) [ชาย]">
                                    <?php break; case 6: ?>
                                       <input id="cca02" type="hidden" value="80,200">
                                       <input id="doctorname" type="hidden" value="2. อายุ (80 ปี ขึ้นไป) [ชาย]">
                                    <?php break; default: ?>
                             <input id="cca02" type="hidden" value="0,200"> 
                             <input id="doctorname" type="hidden" value="2. อายุ (รวม) [ชาย]"><?php } ?>
                             <input id="gender" type="hidden" value="1">
                             <input id="zonedata" type="hidden" value="<?= $zone ?>">
                             <input id="state" type="hidden" value="<?php if ($summaryZone=="Cca01Zone") echo cca01zone; else echo cca01; ?>">
                             </a><?= "&emsp;"."(".number_format($value*100/$total_male[all_person],1).")" ?><br>
                    <?php  }
                    echo number_format($total_age_male[avg],1)." ± ".number_format($total_age_male[sd],1)."<br>".number_format($total_median_male)." ( ".number_format($total_age_male[min])." : ".number_format($total_age_male[max])." )"?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_age_female[age_20],$total_age_female[age_30],$total_age_female[age_40],$total_age_female[age_50],$total_age_female[age_60],$total_age_female[age_70],$total_age_female[age_80],$total_age_female[person]);
                    foreach($r as $key => $value) 
                    if(($value==0)||checkLiCense($user_Site_Code,$hospitalCode))
                        echo number_format($value)."&emsp;"."(".number_format($value*100/$total_female[all_person],1).")<br>"; 
                     else { ?>
                     <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($value) ?>
                             <input id="data" type="hidden" value="age">
                            <?php switch ($key) { case 0: ?>
                                       <input id="cca02" type="hidden" value="20,29"> 
                                       <input id="doctorname" type="hidden" value="2. อายุ (20,29 ปี) [หญิง]">
                                    <?php break; case 1: ?>
                                       <input id="cca02" type="hidden" value="30,39">
                                       <input id="doctorname" type="hidden" value="2. อายุ (30,39 ปี) [หญิง]">
                                    <?php break; case 2: ?>
                                       <input id="cca02" type="hidden" value="40,49"> 
                                       <input id="doctorname" type="hidden" value="2. อายุ (40,49 ปี) [หญิง]">
                                    <?php break; case 3: ?>
                                       <input id="cca02" type="hidden" value="50,59">  
                                       <input id="doctorname" type="hidden" value="2. อายุ (50,59 ปี) [หญิง]">
                                    <?php break; case 4: ?>
                                       <input id="cca02" type="hidden" value="60,69"> 
                                       <input id="doctorname" type="hidden" value="2. อายุ (60,69 ปี) [หญิง]">
                                    <?php break; case 5: ?>
                                       <input id="cca02" type="hidden" value="70,79">
                                       <input id="doctorname" type="hidden" value="2. อายุ (70,79 ปี) [หญิง]">
                                    <?php break; case 6: ?>
                                       <input id="cca02" type="hidden" value="80,200">
                                       <input id="doctorname" type="hidden" value="2. อายุ (80 ปี ขึ้นไป) [หญิง]">
                                    <?php break; default: ?>
                             <input id="cca02" type="hidden" value="0,200"> 
                             <input id="doctorname" type="hidden" value="2. อายุ (รวม)[หญิง]"><?php } ?>
                             <input id="gender" type="hidden" value="2">
                             <input id="zonedata" type="hidden" value="<?= $zone ?>">
                             <input id="state" type="hidden" value="<?php if ($summaryZone=="Cca01Zone") echo cca01zone; else echo cca01; ?>">
                             </a><?= "&emsp;"."(".number_format($value*100/$total_female[all_person],1).")" ?><br>
                    <?php  }
                    echo number_format($total_age_female[avg],1)." ± ".number_format($total_age_female[sd],1)."<br>".number_format($total_median_female)." ( ".number_format($total_age_female[min])." : ".number_format($total_age_female[max])." )"?>
                </td>
               </tr>
            <tr>
                <td><strong>3. การศึกษา</strong><br>&emsp;ไม่ได้รับการศึกษา<br>&emsp;ประถมศึกษา<br>&emsp;มัธยมศึกษาตอนต้น<br>&emsp;มัธยมศึกษาตอนปลาย<br>&emsp;ประกาศนียบัตร<br>&emsp;ปริญญาตรี<br>&emsp;สูงกว่าปริญญาตรี<br>&emsp;รวม</td>
                <td align="right">
                    <br><?php  $r = array($total[non_edu],$total[primary_edu],$total[junior_edu],$total[senior_edu],$total[vocational_edu],$total[bachelor_edu],$total[master_edu],$total[total_edu]);
                    foreach($r as $value) echo number_format($value)."&emsp;"."(".number_format($value*100/$total[all_person],2).")<br>"; ?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_hcode[non_edu],$total_hcode[primary_edu],$total_hcode[junior_edu],$total_hcode[senior_edu],$total_hcode[vocational_edu],$total_hcode[bachelor_edu],$total_hcode[master_edu],$total_hcode[total_edu]);
                    foreach($r as $key => $value) 
                     if(($value==0)||checkLiCense($user_Site_Code,$hospitalCode))
                        echo number_format($value)."&emsp;"."(".number_format($value*100/$total_hcode[all_person],1).")<br>"; 
                     else { ?>
                     <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($value) ?>
                             <input id="data" type="hidden" value="f1v4">
                             <?php  if($key==0){ ?>
                                <input id="cca02" type="hidden" value="<?= $key+1 ?>">
                                <input id="doctorname" type="hidden" value="3. การศึกษา (ไม่ได้รับการศึกษา) [รวมในพื้นที่]">
                             <?php  } else if($key==1) {?>
                                <input id="cca02" type="hidden" value="<?= $key+1 ?>">
                                <input id="doctorname" type="hidden" value="3. การศึกษา (ประถมศึกษา) [รวมในพื้นที่]">
                             <?php  } else if($key==2) { ?>
                                <input id="cca02" type="hidden" value="<?= $key+1 ?>">
                                <input id="doctorname" type="hidden" value="3. การศึกษา (มัธยมศึกษาตอนต้น) [รวมในพื้นที่]">
                             <?php  } else if($key==3) {?>
                                <input id="cca02" type="hidden" value="<?= $key+1 ?>">
                                <input id="doctorname" type="hidden" value="3. การศึกษา (มัธยมศึกษาตอนปลาย) [รวมในพื้นที่]">
                             <?php  } else if($key==4) { ?>
                                <input id="cca02" type="hidden" value="<?= $key+1 ?>">
                                <input id="doctorname" type="hidden" value="3. การศึกษา (ประกาศนียบัตร) [รวมในพื้นที่]">
                             <?php  } else if($key==5) { ?>
                                <input id="cca02" type="hidden" value="<?= $key+1 ?>">
                                <input id="doctorname" type="hidden" value="3. การศึกษา (ปริญญาตรี) [รวมในพื้นที่]">
                              <?php  } else if($key==6) { ?>
                                <input id="cca02" type="hidden" value="<?= $key+1 ?>">
                                <input id="doctorname" type="hidden" value="3. การศึกษา (สูงกว่าปริญญาตรี) [รวมในพื้นที่]">
                              <?php  } else { ?>
                                <input id="cca02" type="hidden" value="1,2,3,4,5,6,7">
                                <input id="doctorname" type="hidden" value="3. การศึกษา (รวม) [รวมในพื้นที่]">
                              <?php  } ?>
                             <input id="gender" type="hidden" value="1,2">
                             <input id="zonedata" type="hidden" value="<?= $zone ?>">
                             <input id="state" type="hidden" value="<?php if ($summaryZone=="Cca01Zone") echo cca01zone; else echo cca01; ?>">
                             </a><?= "&emsp;"."(".number_format($value*100/$total_hcode[all_person],1).")" ?><br>
                    <?php  } ?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_male[non_edu],$total_male[primary_edu],$total_male[junior_edu],$total_male[senior_edu],$total_male[vocational_edu],$total_male[bachelor_edu],$total_male[master_edu],$total_male[total_edu]);
                    foreach($r as $key => $value) 
                     if(($value==0)||checkLiCense($user_Site_Code,$hospitalCode))
                        echo number_format($value)."&emsp;"."(".number_format($value*100/$total_male[all_person],1).")<br>"; 
                     else { ?>
                     <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($value) ?>
                             <input id="data" type="hidden" value="f1v4">
                             <?php  if($key==0){ ?>
                                <input id="cca02" type="hidden" value="<?= $key+1 ?>">
                                <input id="doctorname" type="hidden" value="3. การศึกษา (ไม่ได้รับการศึกษา) [ชาย]">
                             <?php  } else if($key==1) {?>
                                <input id="cca02" type="hidden" value="<?= $key+1 ?>">
                                <input id="doctorname" type="hidden" value="3. การศึกษา (ประถมศึกษา) [ชาย]">
                             <?php  } else if($key==2) { ?>
                                <input id="cca02" type="hidden" value="<?= $key+1 ?>">
                                <input id="doctorname" type="hidden" value="3. การศึกษา (มัธยมศึกษาตอนต้น) [ชาย]">
                             <?php  } else if($key==3) {?>
                                <input id="cca02" type="hidden" value="<?= $key+1 ?>">
                                <input id="doctorname" type="hidden" value="3. การศึกษา (มัธยมศึกษาตอนปลาย) [ชาย]">
                             <?php  } else if($key==4) { ?>
                                <input id="cca02" type="hidden" value="<?= $key+1 ?>">
                                <input id="doctorname" type="hidden" value="3. การศึกษา (ประกาศนียบัตร) [ชาย]">
                             <?php  } else if($key==5) { ?>
                                <input id="cca02" type="hidden" value="<?= $key+1 ?>">
                                <input id="doctorname" type="hidden" value="3. การศึกษา (ปริญญาตรี) [ชาย]">
                              <?php  } else if($key==6) { ?>
                                <input id="cca02" type="hidden" value="<?= $key+1 ?>">
                                <input id="doctorname" type="hidden" value="3. การศึกษา (สูงกว่าปริญญาตรี) [ชาย]">
                              <?php  } else { ?>
                                <input id="cca02" type="hidden" value="1,2,3,4,5,6,7">
                                <input id="doctorname" type="hidden" value="3. การศึกษา (รวม) [ชาย]">
                              <?php  } ?>
                             <input id="gender" type="hidden" value="1">
                             <input id="zonedata" type="hidden" value="<?= $zone ?>">
                             <input id="state" type="hidden" value="<?php if ($summaryZone=="Cca01Zone") echo cca01zone; else echo cca01; ?>">
                             </a><?= "&emsp;"."(".number_format($value*100/$total_male[all_person],1).")" ?><br>
                    <?php  } ?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_female[non_edu],$total_female[primary_edu],$total_female[junior_edu],$total_female[senior_edu],$total_female[vocational_edu],$total_female[bachelor_edu],$total_female[master_edu],$total_female[total_edu]);
                    foreach($r as $key => $value) 
                    if(($value==0)||checkLiCense($user_Site_Code,$hospitalCode))
                        echo number_format($value)."&emsp;"."(".number_format($value*100/$total_female[all_person],1).")<br>"; 
                    else { ?>
                     <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($value) ?>
                             <input id="data" type="hidden" value="f1v4">
                            <?php  if($key==0){ ?>
                                <input id="cca02" type="hidden" value="<?= $key+1 ?>">
                                <input id="doctorname" type="hidden" value="3. การศึกษา (ไม่ได้รับการศึกษา) [หญิง]">
                             <?php  } else if($key==1) {?>
                                <input id="cca02" type="hidden" value="<?= $key+1 ?>">
                                <input id="doctorname" type="hidden" value="3. การศึกษา (ประถมศึกษา) [หญิง]">
                             <?php  } else if($key==2) { ?>
                                <input id="cca02" type="hidden" value="<?= $key+1 ?>">
                                <input id="doctorname" type="hidden" value="3. การศึกษา (มัธยมศึกษาตอนต้น) [หญิง]">
                             <?php  } else if($key==3) {?>
                                <input id="cca02" type="hidden" value="<?= $key+1 ?>">
                                <input id="doctorname" type="hidden" value="3. การศึกษา (มัธยมศึกษาตอนปลาย) [หญิง]">
                             <?php  } else if($key==4) { ?>
                                <input id="cca02" type="hidden" value="<?= $key+1 ?>">
                                <input id="doctorname" type="hidden" value="3. การศึกษา (ประกาศนียบัตร) [หญิง]">
                             <?php  } else if($key==5) { ?>
                                <input id="cca02" type="hidden" value="<?= $key+1 ?>">
                                <input id="doctorname" type="hidden" value="3. การศึกษา (ปริญญาตรี) [หญิง]">
                              <?php  } else if($key==6) { ?>
                                <input id="cca02" type="hidden" value="<?= $key+1 ?>">
                                <input id="doctorname" type="hidden" value="3. การศึกษา (สูงกว่าปริญญาตรี) [หญิง]">
                              <?php  } else { ?>
                                <input id="cca02" type="hidden" value="1,2,3,4,5,6,7">
                                <input id="doctorname" type="hidden" value="3. การศึกษา (รวม) [หญิง]">
                              <?php  } ?>
                             <input id="gender" type="hidden" value="2">
                             <input id="zonedata" type="hidden" value="<?= $zone ?>">
                             <input id="state" type="hidden" value="<?php if ($summaryZone=="Cca01Zone") echo cca01zone; else echo cca01; ?>">
                             </a><?= "&emsp;"."(".number_format($value*100/$total_female[all_person],1).")" ?><br>
                    <?php  } ?>
                </td>
              </tr>
            <tr>
                <td><strong>4. อาชีพ</strong><br>&emsp;ว่างงาน<br>&emsp;เกษตรกรรม<br>&emsp;รับจ้าง<br>&emsp;ธุรกิจส่วนตัว<br>&emsp;รับราชการ / รัฐวิสาหกิจ<br>&emsp;อื่นๆ<br>&emsp;รวม</td>
                <td align="right">
                    <br><?php  $r = array($total[non_job],$total[farmer_job],$total[emp_job],$total[business_job],$total[gov_job],$total[other_job],$total[total_work]);
                    foreach($r as $value) echo number_format($value)."&emsp;"."(".number_format($value*100/$total[all_person],2).")<br>"; ?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_hcode[non_job],$total_hcode[farmer_job],$total_hcode[emp_job],$total_hcode[business_job],$total_hcode[gov_job],$total_hcode[other_job],$total_hcode[total_work]);
                    foreach($r as $key => $value) 
                    if(($value==0)||checkLiCense($user_Site_Code,$hospitalCode))
                        echo number_format($value)."&emsp;"."(".number_format($value*100/$total_hcode[all_person],1).")<br>";
                    else { ?>
                     <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($value) ?>
                             <input id="data" type="hidden" value="f1v5">
                             <?php  if($key==0){ ?>
                                <input id="cca02" type="hidden" value="<?= $key+1 ?>">
                                <input id="doctorname" type="hidden" value="4. อาชีพ (ว่างงาน) [รวมในพื้นที่]">
                             <?php  } else if($key==1) {?>
                                <input id="cca02" type="hidden" value="<?= $key+1 ?>">
                                <input id="doctorname" type="hidden" value="4. อาชีพ (เกษตรกรรม) [รวมในพื้นที่]">
                             <?php  } else if($key==2) { ?>
                                <input id="cca02" type="hidden" value="<?= $key+1 ?>">
                                <input id="doctorname" type="hidden" value="4. อาชีพ (รับจ้าง) [รวมในพื้นที่]">
                             <?php  } else if($key==3) {?>
                                <input id="cca02" type="hidden" value="<?= $key+1 ?>">
                                <input id="doctorname" type="hidden" value="4. อาชีพ (ธุรกิจส่วนตัว) [รวมในพื้นที่]">
                             <?php  } else if($key==4) { ?>
                                <input id="cca02" type="hidden" value="<?= $key+1 ?>">
                                <input id="doctorname" type="hidden" value="4. อาชีพ (รับราชการ / รัฐวิสาหกิจ) [รวมในพื้นที่]">
                             <?php  } else if($key==5) { ?>
                                <input id="cca02" type="hidden" value="<?= $key+1 ?>">
                                <input id="doctorname" type="hidden" value="4. อาชีพ (อื่นๆ) [รวมในพื้นที่]">
                              <?php  } else { ?>
                                <input id="cca02" type="hidden" value="1,2,3,4,5,6">
                                <input id="doctorname" type="hidden" value="4. อาชีพ (รวม) [รวมในพื้นที่]">
                              <?php  } ?>
                             <input id="gender" type="hidden" value="1,2">
                             <input id="zonedata" type="hidden" value="<?= $zone ?>">
                             <input id="state" type="hidden" value="<?php if ($summaryZone=="Cca01Zone") echo cca01zone; else echo cca01; ?>">
                             </a><?= "&emsp;"."(".number_format($value*100/$total_hcode[all_person],1).")" ?><br>
                    <?php  } ?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_male[non_job],$total_male[farmer_job],$total_male[emp_job],$total_male[business_job],$total_male[gov_job],$total_male[other_job],$total_male[total_work]);
                    foreach($r as $key => $value) 
                     if(($value==0)||checkLiCense($user_Site_Code,$hospitalCode))
                        echo number_format($value)."&emsp;"."(".number_format($value*100/$total_male[all_person],1).")<br>"; 
                     else { ?>
                     <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($value) ?>
                             <input id="data" type="hidden" value="f1v5">
                              <?php  if($key==0){ ?>
                                <input id="cca02" type="hidden" value="<?= $key+1 ?>">
                                <input id="doctorname" type="hidden" value="4. อาชีพ (ว่างงาน) [ชาย]">
                             <?php  } else if($key==1) {?>
                                <input id="cca02" type="hidden" value="<?= $key+1 ?>">
                                <input id="doctorname" type="hidden" value="4. อาชีพ (เกษตรกรรม) [ชาย]">
                             <?php  } else if($key==2) { ?>
                                <input id="cca02" type="hidden" value="<?= $key+1 ?>">
                                <input id="doctorname" type="hidden" value="4. อาชีพ (รับจ้าง) [ชาย]">
                             <?php  } else if($key==3) {?>
                                <input id="cca02" type="hidden" value="<?= $key+1 ?>">
                                <input id="doctorname" type="hidden" value="4. อาชีพ (ธุรกิจส่วนตัว) [ชาย]">
                             <?php  } else if($key==4) { ?>
                                <input id="cca02" type="hidden" value="<?= $key+1 ?>">
                                <input id="doctorname" type="hidden" value="4. อาชีพ (รับราชการ / รัฐวิสาหกิจ) [ชาย]">
                             <?php  } else if($key==5) { ?>
                                <input id="cca02" type="hidden" value="<?= $key+1 ?>">
                                <input id="doctorname" type="hidden" value="4. อาชีพ (อื่นๆ) [ชาย]">
                              <?php  } else { ?>
                                <input id="cca02" type="hidden" value="1,2,3,4,5,6">
                                <input id="doctorname" type="hidden" value="4. อาชีพ (รวม) [ชาย]">
                              <?php  } ?>
                             <input id="gender" type="hidden" value="1">
                             <input id="zonedata" type="hidden" value="<?= $zone ?>">
                             <input id="state" type="hidden" value="<?php if ($summaryZone=="Cca01Zone") echo cca01zone; else echo cca01; ?>">
                             </a><?= "&emsp;"."(".number_format($value*100/$total_male[all_person],1).")" ?><br>
                    <?php  } ?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_female[non_job],$total_female[farmer_job],$total_female[emp_job],$total_female[business_job],$total_female[gov_job],$total_female[other_job],$total_female[total_work]);
                    foreach($r as $key => $value) 
                     if(($value==0)||checkLiCense($user_Site_Code,$hospitalCode))
                        echo number_format($value)."&emsp;"."(".number_format($value*100/$total_female[all_person],1).")<br>"; 
                     else { ?>
                     <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($value) ?>
                             <input id="data" type="hidden" value="f1v5">
                             <?php  if($key==0){ ?>
                                <input id="cca02" type="hidden" value="<?= $key+1 ?>">
                                <input id="doctorname" type="hidden" value="4. อาชีพ (ว่างงาน) [หญิง]">
                             <?php  } else if($key==1) {?>
                                <input id="cca02" type="hidden" value="<?= $key+1 ?>">
                                <input id="doctorname" type="hidden" value="4. อาชีพ (เกษตรกรรม) [หญิง]">
                             <?php  } else if($key==2) { ?>
                                <input id="cca02" type="hidden" value="<?= $key+1 ?>">
                                <input id="doctorname" type="hidden" value="4. อาชีพ (รับจ้าง) [หญิง]">
                             <?php  } else if($key==3) {?>
                                <input id="cca02" type="hidden" value="<?= $key+1 ?>">
                                <input id="doctorname" type="hidden" value="4. อาชีพ (ธุรกิจส่วนตัว) [หญิง]">
                             <?php  } else if($key==4) { ?>
                                <input id="cca02" type="hidden" value="<?= $key+1 ?>">
                                <input id="doctorname" type="hidden" value="4. อาชีพ (รับราชการ / รัฐวิสาหกิจ) [หญิง]">
                             <?php  } else if($key==5) { ?>
                                <input id="cca02" type="hidden" value="<?= $key+1 ?>">
                                <input id="doctorname" type="hidden" value="4. อาชีพ (อื่นๆ) [หญิง]">
                              <?php  } else { ?>
                                <input id="cca02" type="hidden" value="1,2,3,4,5,6">
                                <input id="doctorname" type="hidden" value="4. อาชีพ (รวม) [หญิง]">
                              <?php  } ?>
                             <input id="gender" type="hidden" value="2">
                             <input id="zonedata" type="hidden" value="<?= $zone ?>">
                             <input id="state" type="hidden" value="<?php if ($summaryZone=="Cca01Zone") echo cca01zone; else echo cca01; ?>">
                             </a><?= "&emsp;"."(".number_format($value*100/$total_female[all_person],1).")" ?><br>
                    <?php  } ?>
                </td>
            </tr>
            <tr>
                <td><strong>5. ประวัติการได้รับการตรวจหาไข่พยาธิใบไม้ตับ</strong><br>&emsp;ไม่เคย<br>&emsp;เคย 1 ครั้ง<br>&emsp;เคย 2 ครั้ง<br>&emsp;เคย 3 ครั้ง<br>&emsp;เคยมากกว่า 3 ครั้ง<br>&emsp;จำไม่ได้<br>&emsp;รวม</td>
                <td align="right">
                    <br><?php  $r = array($total[non_f1v6],$total[first_f1v6],$total[second_f1v6],$total[third_f1v6],$total[more_f1v6],$total[forget_f1v6],$total[total_f1v6]);
                    foreach($r as $value) 
                        echo number_format($value)."&emsp;"."(".number_format($value*100/$total[all_person],2).")<br>"; ?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_hcode[non_f1v6],$total_hcode[first_f1v6],$total_hcode[second_f1v6],$total_hcode[third_f1v6],$total_hcode[more_f1v6],$total_hcode[forget_f1v6],$total_hcode[total_f1v6]);
                    //foreach($r as $value) echo number_format($value)."&emsp;"."(".number_format($value*100/$total_hcode[all_person],1).")<br>"; 
                     foreach($r as $key => $value) 
                     if(($value==0)||checkLiCense($user_Site_Code,$hospitalCode))
                        echo number_format($value)."&emsp;"."(".number_format($value*100/$total_hcode[all_person],1).")<br>";
                     else { ?>
                     <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($value) ?>
                             <input id="data" type="hidden" value="f1v6">
                              <?php  if($key==0){ ?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="5. ประวัติการได้รับการตรวจหาไข่พยาธิใบไม้ตับ (ไม่เคย) [รวมในพื้นที่]">
                             <?php  } else if($key==1) {?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="5. ประวัติการได้รับการตรวจหาไข่พยาธิใบไม้ตับ (เคย 1 ครั้ง) [รวมในพื้นที่]">
                             <?php  } else if($key==2) { ?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="5. ประวัติการได้รับการตรวจหาไข่พยาธิใบไม้ตับ (เคย 2 ครั้ง) [รวมในพื้นที่]">
                             <?php  } else if($key==3) {?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="5. ประวัติการได้รับการตรวจหาไข่พยาธิใบไม้ตับ (เคย 3 ครั้ง) [รวมในพื้นที่]">
                             <?php  } else if($key==4) { ?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="5. ประวัติการได้รับการตรวจหาไข่พยาธิใบไม้ตับ (เคยมากกว่า 3 ครั้ง) [รวมในพื้นที่]">
                             <?php  } else if($key==5) { ?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="5. ประวัติการได้รับการตรวจหาไข่พยาธิใบไม้ตับ (จำไม่ได้) [รวมในพื้นที่]">
                              <?php  } else { ?>
                                <input id="cca02" type="hidden" value="0,1,2,3,4,5">
                                <input id="doctorname" type="hidden" value="5. ประวัติการได้รับการตรวจหาไข่พยาธิใบไม้ตับ (รวม) [รวมในพื้นที่]">
                              <?php  } ?>
                             <input id="gender" type="hidden" value="1,2">
                             <input id="zonedata" type="hidden" value="<?= $zone ?>">
                             <input id="state" type="hidden" value="<?php if ($summaryZone=="Cca01Zone") echo cca01zone; else echo cca01; ?>">
                             </a><?= "&emsp;"."(".number_format($value*100/$total_hcode[all_person],1).")" ?><br>
                    <?php  } ?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_male[non_f1v6],$total_male[first_f1v6],$total_male[second_f1v6],$total_male[third_f1v6],$total_male[more_f1v6],$total_male[forget_f1v6],$total_male[total_f1v6]);
                   // foreach($r as $value) echo number_format($value)."&emsp;"."(".number_format($value*100/$total_male[all_person],1).")<br>"; 
                     foreach($r as $key => $value) 
                     if(($value==0)||checkLiCense($user_Site_Code,$hospitalCode))
                        echo number_format($value)."&emsp;"."(".number_format($value*100/$total_male[all_person],1).")<br>";
                     else { ?>
                     <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($value) ?>
                             <input id="data" type="hidden" value="f1v6">
                            <?php  if($key==0){ ?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="5. ประวัติการได้รับการตรวจหาไข่พยาธิใบไม้ตับ (ไม่เคย) [ชาย]">
                             <?php  } else if($key==1) {?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="5. ประวัติการได้รับการตรวจหาไข่พยาธิใบไม้ตับ (เคย 1 ครั้ง) [ชาย]">
                             <?php  } else if($key==2) { ?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="5. ประวัติการได้รับการตรวจหาไข่พยาธิใบไม้ตับ (เคย 2 ครั้ง) [ชาย]">
                             <?php  } else if($key==3) {?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="5. ประวัติการได้รับการตรวจหาไข่พยาธิใบไม้ตับ (เคย 3 ครั้ง) [ชาย]">
                             <?php  } else if($key==4) { ?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="5. ประวัติการได้รับการตรวจหาไข่พยาธิใบไม้ตับ (เคยมากกว่า 3 ครั้ง) [ชาย]">
                             <?php  } else if($key==5) { ?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="5. ประวัติการได้รับการตรวจหาไข่พยาธิใบไม้ตับ (จำไม่ได้) [ชาย]">
                              <?php  } else { ?>
                                <input id="cca02" type="hidden" value="0,1,2,3,4,5">
                                <input id="doctorname" type="hidden" value="5. ประวัติการได้รับการตรวจหาไข่พยาธิใบไม้ตับ (รวม) [ชาย]">
                              <?php  } ?>
                             <input id="gender" type="hidden" value="1">
                             <input id="zonedata" type="hidden" value="<?= $zone ?>">
                             <input id="state" type="hidden" value="<?php if ($summaryZone=="Cca01Zone") echo cca01zone; else echo cca01; ?>">
                             </a><?= "&emsp;"."(".number_format($value*100/$total_male[all_person],1).")" ?><br>
                    <?php  } ?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_female[non_f1v6],$total_female[first_f1v6],$total_female[second_f1v6],$total_female[third_f1v6],$total_female[more_f1v6],$total_female[forget_f1v6],$total_female[total_f1v6]);
                   // foreach($r as $value) echo number_format($value)."&emsp;"."(".number_format($value*100/$total_female[all_person],1).")<br>"; 
                     foreach($r as $key => $value) 
                     if(($value==0)||checkLiCense($user_Site_Code,$hospitalCode))
                        echo number_format($value)."&emsp;"."(".number_format($value*100/$total_female[all_person],1).")<br>";
                     else { ?>
                     <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($value) ?>
                             <input id="data" type="hidden" value="f1v6">
                            <?php  if($key==0){ ?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="5. ประวัติการได้รับการตรวจหาไข่พยาธิใบไม้ตับ (ไม่เคย) [หญิง]">
                             <?php  } else if($key==1) {?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="5. ประวัติการได้รับการตรวจหาไข่พยาธิใบไม้ตับ (เคย 1 ครั้ง) [หญิง]">
                             <?php  } else if($key==2) { ?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="5. ประวัติการได้รับการตรวจหาไข่พยาธิใบไม้ตับ (เคย 2 ครั้ง) [หญิง]">
                             <?php  } else if($key==3) {?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="5. ประวัติการได้รับการตรวจหาไข่พยาธิใบไม้ตับ (เคย 3 ครั้ง) [หญิง]">
                             <?php  } else if($key==4) { ?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="5. ประวัติการได้รับการตรวจหาไข่พยาธิใบไม้ตับ (เคยมากกว่า 3 ครั้ง) [หญิง]">
                             <?php  } else if($key==5) { ?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="5. ประวัติการได้รับการตรวจหาไข่พยาธิใบไม้ตับ (จำไม่ได้) [หญิง]">
                              <?php  } else { ?>
                                <input id="cca02" type="hidden" value="0,1,2,3,4,5">
                                <input id="doctorname" type="hidden" value="5. ประวัติการได้รับการตรวจหาไข่พยาธิใบไม้ตับ (รวม) [หญิง]">
                              <?php  } ?>
                             <input id="gender" type="hidden" value="2">
                             <input id="zonedata" type="hidden" value="<?= $zone ?>">
                             <input id="state" type="hidden" value="<?php if ($summaryZone=="Cca01Zone") echo cca01zone; else echo cca01; ?>">
                             </a><?= "&emsp;"."(".number_format($value*100/$total_female[all_person],1).")" ?><br>
                    <?php  } ?>
                </td>
                </tr>
            <tr>
                <td><strong>6. ประวัติการตรวจพบไข่พยาธิใบไม้ตับ</strong><br>&emsp;ไม่เคย<br>&emsp;ตรวจแต่ไม่พบ<br>&emsp;ตรวจแล้วพบไข่พยาธิ<br>&emsp;จำไม่ได้<br>&emsp;รวม</td>
                 <td align="right">
                    <br><?php  $r = array($total[non_f1v7],$total[non_meet_f1v7],$total[meet_f1v7],$total[forget_f1v7],$total[total_f1v7]);
                    foreach($r as $value) echo number_format($value)."&emsp;"."(".number_format($value*100/$total[all_person],2).")<br>"; ?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_hcode[non_f1v7],$total_hcode[non_meet_f1v7],$total_hcode[meet_f1v7],$total_hcode[forget_f1v7],$total_hcode[total_f1v7]);
                   // foreach($r as $value) echo number_format($value)."&emsp;"."(".number_format($value*100/$total_hcode[all_person],1).")<br>"; 
                    foreach($r as $key => $value) 
                     if(($value==0)||checkLiCense($user_Site_Code,$hospitalCode))
                        echo number_format($value)."&emsp;"."(".number_format($value*100/$total_hcode[all_person],1).")<br>";
                     else { ?>
                     <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($value) ?>
                             <input id="data" type="hidden" value="f1v7">
                             <?php  if($key==0){ ?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="6. ประวัติการตรวจพบไข่พยาธิใบไม้ตับ (ไม่เคย) [รวมในพื้นที่]">
                             <?php  } else if($key==1) {?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="6. ประวัติการตรวจพบไข่พยาธิใบไม้ตับ (ตรวจแต่ไม่พบ) [รวมในพื้นที่]">
                             <?php  } else if($key==2) { ?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="6. ประวัติการตรวจพบไข่พยาธิใบไม้ตับ (ตรวจแล้วพบไข่พยาธิ) [รวมในพื้นที่]">
                             <?php  } else if($key==3) {?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="6. ประวัติการตรวจพบไข่พยาธิใบไม้ตับ (จำไม่ได้) [รวมในพื้นที่]">
                              <?php  } else { ?>
                                <input id="cca02" type="hidden" value="0,1,2,3">
                                <input id="doctorname" type="hidden" value="6. ประวัติการตรวจพบไข่พยาธิใบไม้ตับ (รวม) [รวมในพื้นที่]">
                              <?php  } ?>
                             <input id="gender" type="hidden" value="1,2">
                             <input id="zonedata" type="hidden" value="<?= $zone ?>">
                             <input id="state" type="hidden" value="<?php if ($summaryZone=="Cca01Zone") echo cca01zone; else echo cca01; ?>">
                             </a><?= "&emsp;"."(".number_format($value*100/$total_hcode[all_person],1).")" ?><br>
                    <?php  } ?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_male[non_f1v7],$total_male[non_meet_f1v7],$total_male[meet_f1v7],$total_male[forget_f1v7],$total_male[total_f1v7]);
                   // foreach($r as $value) echo number_format($value)."&emsp;"."(".number_format($value*100/$total_male[all_person],1).")<br>"; 
                    foreach($r as $key => $value) 
                     if(($value==0)||checkLiCense($user_Site_Code,$hospitalCode))
                        echo number_format($value)."&emsp;"."(".number_format($value*100/$total_male[all_person],1).")<br>";
                     else { ?>
                     <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($value) ?>
                             <input id="data" type="hidden" value="f1v7">
                            <?php  if($key==0){ ?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="6. ประวัติการตรวจพบไข่พยาธิใบไม้ตับ (ไม่เคย) [ชาย]">
                             <?php  } else if($key==1) {?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="6. ประวัติการตรวจพบไข่พยาธิใบไม้ตับ (ตรวจแต่ไม่พบ) [ชาย]">
                             <?php  } else if($key==2) { ?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="6. ประวัติการตรวจพบไข่พยาธิใบไม้ตับ (ตรวจแล้วพบไข่พยาธิ) [ชาย]">
                             <?php  } else if($key==3) {?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="6. ประวัติการตรวจพบไข่พยาธิใบไม้ตับ (จำไม่ได้) [ชาย]">
                              <?php  } else { ?>
                                <input id="cca02" type="hidden" value="0,1,2,3">
                                <input id="doctorname" type="hidden" value="6. ประวัติการตรวจพบไข่พยาธิใบไม้ตับ (รวม) [ชาย]">
                              <?php  } ?>
                             <input id="gender" type="hidden" value="1">
                             <input id="zonedata" type="hidden" value="<?= $zone ?>">
                             <input id="state" type="hidden" value="<?php if ($summaryZone=="Cca01Zone") echo cca01zone; else echo cca01; ?>">
                             </a><?= "&emsp;"."(".number_format($value*100/$total_male[all_person],1).")" ?><br>
                    <?php  } ?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_female[non_f1v7],$total_female[non_meet_f1v7],$total_female[meet_f1v7],$total_female[forget_f1v7],$total_female[total_f1v7]);
                   // foreach($r as $value) echo number_format($value)."&emsp;"."(".number_format($value*100/$total_female[all_person],1).")<br>"; 
                    foreach($r as $key => $value) 
                     if(($value==0)||checkLiCense($user_Site_Code,$hospitalCode))
                        echo number_format($value)."&emsp;"."(".number_format($value*100/$total_female[all_person],1).")<br>";
                     else { ?>
                     <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($value) ?>
                             <input id="data" type="hidden" value="f1v7">
                            <?php  if($key==0){ ?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="6. ประวัติการตรวจพบไข่พยาธิใบไม้ตับ (ไม่เคย) [หญิง]">
                             <?php  } else if($key==1) {?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="6. ประวัติการตรวจพบไข่พยาธิใบไม้ตับ (ตรวจแต่ไม่พบ) [หญิง]">
                             <?php  } else if($key==2) { ?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="6. ประวัติการตรวจพบไข่พยาธิใบไม้ตับ (ตรวจแล้วพบไข่พยาธิ) [หญิง]">
                             <?php  } else if($key==3) {?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="6. ประวัติการตรวจพบไข่พยาธิใบไม้ตับ (จำไม่ได้) [หญิง]">
                              <?php  } else { ?>
                                <input id="cca02" type="hidden" value="0,1,2,3">
                                <input id="doctorname" type="hidden" value="6. ประวัติการตรวจพบไข่พยาธิใบไม้ตับ (รวม) [หญิง]">
                              <?php  } ?>
                             <input id="gender" type="hidden" value="2">
                             <input id="zonedata" type="hidden" value="<?= $zone ?>">
                             <input id="state" type="hidden" value="<?php if ($summaryZone=="Cca01Zone") echo cca01zone; else echo cca01; ?>">
                             </a><?= "&emsp;"."(".number_format($value*100/$total_female[all_person],1).")" ?><br>
                    <?php  } ?>
                </td>
                </tr>
            <tr>
                <td><strong>7. ประวัติการได้รับการรักษาด้วย ยาฆ่าพยาธิใบไม้ตับ</strong><br>&emsp;ไม่เคย<br>&emsp;เคย 1 ครั้ง<br>&emsp;เคย 2 ครั้ง<br>&emsp;เคย 3 ครั้ง<br>&emsp;เคยมากกว่า 3 ครั้ง<br>&emsp;จำไม่ได้<br>&emsp;รวม</td>
                 <td align="right">
                    <br><?php  $r = array($total[non_f1v8],$total[first_f1v8],$total[second_f1v8],$total[third_f1v8],$total[more_f1v8],$total[forget_f1v8],$total[total_f1v8]);
                    foreach($r as $value) echo number_format($value)."&emsp;"."(".number_format($value*100/$total[all_person],2).")<br>"; ?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_hcode[non_f1v8],$total_hcode[first_f1v8],$total_hcode[second_f1v8],$total_hcode[third_f1v8],$total_hcode[more_f1v8],$total_hcode[forget_f1v8],$total_hcode[total_f1v8]);
                   // foreach($r as $value) echo number_format($value)."&emsp;"."(".number_format($value*100/$total_hcode[all_person],1).")<br>"; 
                     foreach($r as $key => $value) 
                     if(($value==0)||checkLiCense($user_Site_Code,$hospitalCode))
                        echo number_format($value)."&emsp;"."(".number_format($value*100/$total_hcode[all_person],1).")<br>";
                     else { ?>
                     <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($value) ?>
                             <input id="data" type="hidden" value="f1v8">
                             <?php  if($key==0){ ?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="7. ประวัติการได้รับการรักษาด้วย ยาฆ่าพยาธิใบไม้ตับ (ไม่เคย) [รวมในพื้นที่]">
                             <?php  } else if($key==1) {?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="7. ประวัติการได้รับการรักษาด้วย ยาฆ่าพยาธิใบไม้ตับ (เคย 1 ครั้ง) [รวมในพื้นที่]">
                             <?php  } else if($key==2) { ?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="7. ประวัติการได้รับการรักษาด้วย ยาฆ่าพยาธิใบไม้ตับ (เคย 2 ครั้ง) [รวมในพื้นที่]">
                             <?php  } else if($key==3) {?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="7. ประวัติการได้รับการรักษาด้วย ยาฆ่าพยาธิใบไม้ตับ (เคย 3 ครั้ง) [รวมในพื้นที่]">
                             <?php  } else if($key==4) { ?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="7. ประวัติการได้รับการรักษาด้วย ยาฆ่าพยาธิใบไม้ตับ (เคยมากกว่า 3 ครั้ง) [รวมในพื้นที่]">
                             <?php  } else if($key==5) { ?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="7. ประวัติการได้รับการรักษาด้วย ยาฆ่าพยาธิใบไม้ตับ (จำไม่ได้) [รวมในพื้นที่]">
                              <?php  } else { ?>
                                <input id="cca02" type="hidden" value="0,1,2,3,4,5">
                                <input id="doctorname" type="hidden" value="7. ประวัติการได้รับการรักษาด้วย ยาฆ่าพยาธิใบไม้ตับ (รวม) [รวมในพื้นที่]">
                              <?php  } ?>
                             <input id="gender" type="hidden" value="1,2">
                             <input id="zonedata" type="hidden" value="<?= $zone ?>">
                             <input id="state" type="hidden" value="<?php if ($summaryZone=="Cca01Zone") echo cca01zone; else echo cca01; ?>">
                             </a><?= "&emsp;"."(".number_format($value*100/$total_hcode[all_person],1).")" ?><br>
                    <?php  } ?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_male[non_f1v8],$total_male[first_f1v8],$total_male[second_f1v8],$total_male[third_f1v8],$total_male[more_f1v8],$total_male[forget_f1v8],$total_male[total_f1v8]);
                   // foreach($r as $value) echo number_format($value)."&emsp;"."(".number_format($value*100/$total_male[all_person],1).")<br>"; 
                    foreach($r as $key => $value) 
                     if(($value==0)||checkLiCense($user_Site_Code,$hospitalCode))
                        echo number_format($value)."&emsp;"."(".number_format($value*100/$total_male[all_person],1).")<br>";
                     else { ?>
                     <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($value) ?>
                             <input id="data" type="hidden" value="f1v8">
                             <?php  if($key==0){ ?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="7. ประวัติการได้รับการรักษาด้วย ยาฆ่าพยาธิใบไม้ตับ (ไม่เคย) [ชาย]">
                             <?php  } else if($key==1) {?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="7. ประวัติการได้รับการรักษาด้วย ยาฆ่าพยาธิใบไม้ตับ (เคย 1 ครั้ง) [ชาย]">
                             <?php  } else if($key==2) { ?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="7. ประวัติการได้รับการรักษาด้วย ยาฆ่าพยาธิใบไม้ตับ (เคย 2 ครั้ง) [ชาย]">
                             <?php  } else if($key==3) {?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="7. ประวัติการได้รับการรักษาด้วย ยาฆ่าพยาธิใบไม้ตับ (เคย 3 ครั้ง) [ชาย]">
                             <?php  } else if($key==4) { ?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="7. ประวัติการได้รับการรักษาด้วย ยาฆ่าพยาธิใบไม้ตับ (เคยมากกว่า 3 ครั้ง) [ชาย]">
                             <?php  } else if($key==5) { ?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="7. ประวัติการได้รับการรักษาด้วย ยาฆ่าพยาธิใบไม้ตับ (จำไม่ได้) [ชาย]">
                              <?php  } else { ?>
                                <input id="cca02" type="hidden" value="0,1,2,3,4,5">
                                <input id="doctorname" type="hidden" value="7. ประวัติการได้รับการรักษาด้วย ยาฆ่าพยาธิใบไม้ตับ (รวม) [ชาย]">
                              <?php  } ?>
                             <input id="gender" type="hidden" value="1">
                             <input id="zonedata" type="hidden" value="<?= $zone ?>">
                             <input id="state" type="hidden" value="<?php if ($summaryZone=="Cca01Zone") echo cca01zone; else echo cca01; ?>">
                             </a><?= "&emsp;"."(".number_format($value*100/$total_male[all_person],1).")" ?><br>
                    <?php  } ?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_female[non_f1v8],$total_female[first_f1v8],$total_female[second_f1v8],$total_female[third_f1v8],$total_female[more_f1v8],$total_female[forget_f1v8],$total_female[total_f1v8]);
                   // foreach($r as $value) echo number_format($value)."&emsp;"."(".number_format($value*100/$total_female[all_person],1).")<br>"; 
                    foreach($r as $key => $value) 
                     if(($value==0)||checkLiCense($user_Site_Code,$hospitalCode))
                        echo number_format($value)."&emsp;"."(".number_format($value*100/$total_female[all_person],1).")<br>";
                     else { ?>
                     <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($value) ?>
                             <input id="data" type="hidden" value="f1v8">
                             <?php  if($key==0){ ?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="7. ประวัติการได้รับการรักษาด้วย ยาฆ่าพยาธิใบไม้ตับ (ไม่เคย) [หญิง]">
                             <?php  } else if($key==1) {?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="7. ประวัติการได้รับการรักษาด้วย ยาฆ่าพยาธิใบไม้ตับ (เคย 1 ครั้ง) [หญิง]">
                             <?php  } else if($key==2) { ?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="7. ประวัติการได้รับการรักษาด้วย ยาฆ่าพยาธิใบไม้ตับ (เคย 2 ครั้ง) [หญิง]">
                             <?php  } else if($key==3) {?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="7. ประวัติการได้รับการรักษาด้วย ยาฆ่าพยาธิใบไม้ตับ (เคย 3 ครั้ง) [หญิง]">
                             <?php  } else if($key==4) { ?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="7. ประวัติการได้รับการรักษาด้วย ยาฆ่าพยาธิใบไม้ตับ (เคยมากกว่า 3 ครั้ง) [หญิง]">
                             <?php  } else if($key==5) { ?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="7. ประวัติการได้รับการรักษาด้วย ยาฆ่าพยาธิใบไม้ตับ (จำไม่ได้) [หญิง]">
                              <?php  } else { ?>
                                <input id="cca02" type="hidden" value="0,1,2,3,4,5">
                                <input id="doctorname" type="hidden" value="7. ประวัติการได้รับการรักษาด้วย ยาฆ่าพยาธิใบไม้ตับ (รวม) [หญิง]">
                              <?php  } ?>
                             <input id="gender" type="hidden" value="2">
                             <input id="zonedata" type="hidden" value="<?= $zone ?>">
                             <input id="state" type="hidden" value="<?php if ($summaryZone=="Cca01Zone") echo cca01zone; else echo cca01; ?>">
                             </a><?= "&emsp;"."(".number_format($value*100/$total_female[all_person],1).")" ?><br>
                    <?php  } ?>
                </td>
            </tr>
            <tr>
                <td><strong>8. ประวัติการมีญาติป่วยเป็นมะเร็งท่อน้ำดี</strong><br>&emsp;มี<br>&emsp;ไม่มี<br>&emsp;รวม</td>
                 <td align="right">
                    <br><?php  $r = array($total[no_f1v9],$total[yes_f1v9],$total[total_f1v9]);
                    foreach($r as $value) echo number_format($value)."&emsp;"."(".number_format($value*100/$total[all_person],2).")<br>"; ?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_hcode[no_f1v9],$total_hcode[yes_f1v9],$total_hcode[total_f1v9]);
                     foreach($r as $key => $value) 
                     if(($value==0)||checkLiCense($user_Site_Code,$hospitalCode))
                        echo number_format($value)."&emsp;"."(".number_format($value*100/$total_hcode[all_person],1).")<br>";
                     else { ?>
                     <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($value) ?>
                             <input id="data" type="hidden" value="f1v9">
                            <?php  if($key==2){ ?>
                                <input id="cca02" type="hidden" value="0,1">
                                <input id="doctorname" type="hidden" value="8. ประวัติการมีญาติป่วยเป็นมะเร็งท่อน้ำดี (รวม) [รวมในพื้นที่]">
                             <?php  } else if($key==0) {?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="8. ประวัติการมีญาติป่วยเป็นมะเร็งท่อน้ำดี (มี) [รวมในพื้นที่]">
                             <?php  } else if($key==1) { ?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="8. ประวัติการมีญาติป่วยเป็นมะเร็งท่อน้ำดี (ไม่มี) [รวมในพื้นที่]">
                              <?php  } ?>
                             <input id="gender" type="hidden" value="1,2">
                             <input id="zonedata" type="hidden" value="<?= $zone ?>">
                             <input id="state" type="hidden" value="<?php if ($summaryZone=="Cca01Zone") echo cca01zone; else echo cca01; ?>">
                             </a><?= "&emsp;"."(".number_format($value*100/$total_hcode[all_person],1).")" ?><br>
                    <?php  } ?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_male[no_f1v9],$total_male[yes_f1v9],$total_male[total_f1v9]);
                     foreach($r as $key => $value) 
                     if(($value==0)||checkLiCense($user_Site_Code,$hospitalCode))
                        echo number_format($value)."&emsp;"."(".number_format($value*100/$total_male[all_person],1).")<br>";
                     else { ?>
                     <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($value) ?>
                             <input id="data" type="hidden" value="f1v9">
                             <?php  if($key==2){ ?>
                                <input id="cca02" type="hidden" value="0,1">
                                <input id="doctorname" type="hidden" value="8. ประวัติการมีญาติป่วยเป็นมะเร็งท่อน้ำดี (รวม) [ชาย]">
                             <?php  } else if($key==0) {?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="8. ประวัติการมีญาติป่วยเป็นมะเร็งท่อน้ำดี (มี) [ชาย]">
                             <?php  } else if($key==1) { ?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="8. ประวัติการมีญาติป่วยเป็นมะเร็งท่อน้ำดี (ไม่มี) [ชาย]">
                              <?php  } ?>
                             <input id="gender" type="hidden" value="1">
                             <input id="zonedata" type="hidden" value="<?= $zone ?>">
                             <input id="state" type="hidden" value="<?php if ($summaryZone=="Cca01Zone") echo cca01zone; else echo cca01; ?>">
                             </a><?= "&emsp;"."(".number_format($value*100/$total_male[all_person],1).")" ?><br>
                    <?php  } ?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_female[no_f1v9],$total_female[yes_f1v9],$total_female[total_f1v9]);
                     foreach($r as $key => $value) 
                     if(($value==0)||checkLiCense($user_Site_Code,$hospitalCode))
                        echo number_format($value)."&emsp;"."(".number_format($value*100/$total_female[all_person],1).")<br>";
                     else { ?>
                     <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($value) ?>
                             <input id="data" type="hidden" value="f1v9">
                             <?php  if($key==2){ ?>
                                <input id="cca02" type="hidden" value="0,1">
                                <input id="doctorname" type="hidden" value="8. ประวัติการมีญาติป่วยเป็นมะเร็งท่อน้ำดี (รวม) [หญิง]">
                             <?php  } else if($key==0) {?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="8. ประวัติการมีญาติป่วยเป็นมะเร็งท่อน้ำดี (มี) [หญิง]">
                             <?php  } else if($key==1) { ?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="8. ประวัติการมีญาติป่วยเป็นมะเร็งท่อน้ำดี (ไม่มี) [หญิง]">
                              <?php  } ?>
                             <input id="gender" type="hidden" value="2">
                             <input id="zonedata" type="hidden" value="<?= $zone ?>">
                             <input id="state" type="hidden" value="<?php if ($summaryZone=="Cca01Zone") echo cca01zone; else echo cca01; ?>">
                             </a><?= "&emsp;"."(".number_format($value*100/$total_female[all_person],1).")" ?><br>
                    <?php  } ?>
                </td>
               </tr>
            <tr>
                <td><strong>ความสัมพันธ์</strong><br>&emsp;ปู่ ย่า<br>&emsp;ตา ยาย<br>&emsp;ลุง ป้า<br>&emsp;น้า อา<br>&emsp;พ่อ แม่<br>&emsp;ลูกชาย ลูกสาว<br>&emsp;พี่ชายน้องชาย พี่สาวน้องสาว<br>&emsp;หลานๆ<br>&emsp;คู่สมรส<br>&emsp;รวม (ความสัมพันธ์อย่างใดอย่างหนึ่ง)</td>
                <td align="right">
                    <br><?php  $r = array($total[f1v9a1b1],$total[f1v9a1b2],$total[f1v9a1b3],$total[f1v9a1b4],$total[f1v9a1b5],$total[f1v9a1b6],$total[f1v9a1b7],$total[f1v9a1b8],$total[f1v9a1b9]);
                    foreach($r as $value) echo number_format($value)."&emsp;"."(".number_format($value*100/$total[all_person],2).")<br>"; echo number_format($relation[person])."&emsp;"."(".number_format($relation[person]*100/$total[all_person],2).")"; ?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_hcode[f1v9a1b1],$total_hcode[f1v9a1b2],$total_hcode[f1v9a1b3],$total_hcode[f1v9a1b4],$total_hcode[f1v9a1b5],$total_hcode[f1v9a1b6],$total_hcode[f1v9a1b7],$total_hcode[f1v9a1b8],$total_hcode[f1v9a1b9]);
                    foreach($r as $key => $value)
                    if(($value==0)||checkLiCense($user_Site_Code,$hospitalCode))
                        echo number_format($value)."&emsp;"."(".number_format($value*100/$total_hcode[all_person],1).")<br>"; 
                     else { ?>
                             <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($value) ?>
                             <input id="data" type="hidden" value="<?= "f1v9a1b".($key+1) ?>">
                             <input id="cca02" type="hidden" value="1">
                             <?php if($key==0) { ?>
                                <input id="doctorname" type="hidden" value="ความสัมพันธ์ (ปู่ ย่า) [รวมในพื้นที่]">
                             <?php } else if($key==1) {?>
                                <input id="doctorname" type="hidden" value="ความสัมพันธ์ (ตา ยาย) [รวมในพื้นที่]">
                             <?php } else if($key==2) {?>
                                <input id="doctorname" type="hidden" value="ความสัมพันธ์ (ลุง ป้า) [รวมในพื้นที่]">
                             <?php } else if($key==3) {?>
                                <input id="doctorname" type="hidden" value="ความสัมพันธ์ (น้า อา) [รวมในพื้นที่]">
                             <?php } else if($key==4) {?>
                                <input id="doctorname" type="hidden" value="ความสัมพันธ์ (พ่อ แม่) [รวมในพื้นที่]">
                             <?php } else if($key==5) {?>
                                <input id="doctorname" type="hidden" value="ความสัมพันธ์ (ลูกชาย ลูกสาว) [รวมในพื้นที่]">
                             <?php } else if($key==6) {?>
                                <input id="doctorname" type="hidden" value="ความสัมพันธ์ (พี่ชายน้องชาย พี่สาวน้องสาว) [รวมในพื้นที่]">
                             <?php } else if($key==7) {?>
                                <input id="doctorname" type="hidden" value="ความสัมพันธ์ (หลานๆ) [รวมในพื้นที่]">
                             <?php } else if($key==8) {?>
                                <input id="doctorname" type="hidden" value="ความสัมพันธ์ (คู่สมรส) [รวมในพื้นที่]">
                             <?php } ?>
                             <input id="gender" type="hidden" value="1,2">
                             <input id="zonedata" type="hidden" value="<?= $zone ?>">
                             <input id="state" type="hidden" value="<?php if ($summaryZone=="Cca01Zone") echo cca01zone; else echo cca01; ?>">
                             </a><?= "&emsp;"."(".number_format($value*100/$total_hcode[all_person],1).")" ?><br>
                    <?php  }
                    if((sizeof($relationHos)==0)||checkLiCense($user_Site_Code,$hospitalCode))    //echo number_format(array_sum($r))."&emsp;"."(".number_format(array_sum($r)*100/$total_hcode[all_person],1).")";
                        echo number_format(sizeof($relationHos))."&emsp;"."(".number_format(sizeof($relationHos)*100/$total_hcode[all_person],1).")";
                    else { ?>
                             <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format(sizeof($relationHos)) ?>
                             <input id="data" type="hidden" value="f1v9a1">
                             <input id="doctorname" type="hidden" value="ความสัมพันธ์ (รวม) [รวมในพื้นที่]">
                             <input id="gender" type="hidden" value="1,2">
                             <input id="zonedata" type="hidden" value="<?= $zone ?>">
                             <input id="state" type="hidden" value="<?php if ($summaryZone=="Cca01Zone") echo cca01zone; else echo cca01; ?>">
                             </a><?= "&emsp;"."(".number_format(sizeof($relationHos)*100/$total_hcode[all_person],1).")" ?><br>
                    <?php  } ?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_male[f1v9a1b1],$total_male[f1v9a1b2],$total_male[f1v9a1b3],$total_male[f1v9a1b4],$total_male[f1v9a1b5],$total_male[f1v9a1b6],$total_male[f1v9a1b7],$total_male[f1v9a1b8],$total_male[f1v9a1b9]);
                    foreach($r as $key => $value) 
                    if(($value==0)||checkLiCense($user_Site_Code,$hospitalCode))
                        echo number_format($value)."&emsp;"."(".number_format($value*100/$total_male[all_person],1).")<br>";
                     else { ?>
                             <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($value) ?>
                             <input id="data" type="hidden" value="<?= "f1v9a1b".($key+1) ?>">
                             <input id="cca02" type="hidden" value="1">
                            <?php if($key==0) { ?>
                                <input id="doctorname" type="hidden" value="ความสัมพันธ์ (ปู่ ย่า) [ชาย]">
                             <?php } else if($key==1) {?>
                                <input id="doctorname" type="hidden" value="ความสัมพันธ์ (ตา ยาย) [ชาย]">
                             <?php } else if($key==2) {?>
                                <input id="doctorname" type="hidden" value="ความสัมพันธ์ (ลุง ป้า) [ชาย]">
                             <?php } else if($key==3) {?>
                                <input id="doctorname" type="hidden" value="ความสัมพันธ์ (น้า อา) [ชาย]">
                             <?php } else if($key==4) {?>
                                <input id="doctorname" type="hidden" value="ความสัมพันธ์ (พ่อ แม่) [ชาย]">
                             <?php } else if($key==5) {?>
                                <input id="doctorname" type="hidden" value="ความสัมพันธ์ (ลูกชาย ลูกสาว) [ชาย]">
                             <?php } else if($key==6) {?>
                                <input id="doctorname" type="hidden" value="ความสัมพันธ์ (พี่ชายน้องชาย พี่สาวน้องสาว) [ชาย]">
                             <?php } else if($key==7) {?>
                                <input id="doctorname" type="hidden" value="ความสัมพันธ์ (หลานๆ) [ชาย]">
                             <?php } else if($key==8) {?>
                                <input id="doctorname" type="hidden" value="ความสัมพันธ์ (คู่สมรส) [ชาย]">
                             <?php } ?>
                             <input id="gender" type="hidden" value="1">
                             <input id="zonedata" type="hidden" value="<?= $zone ?>">
                             <input id="state" type="hidden" value="<?php if ($summaryZone=="Cca01Zone") echo cca01zone; else echo cca01; ?>">
                             </a><?= "&emsp;"."(".number_format($value*100/$total_hcode[all_person],1).")" ?><br>
                    <?php  }
                    if((sizeof($relationMale)==0)||checkLiCense($user_Site_Code,$hospitalCode))    //echo number_format(array_sum($r))."&emsp;"."(".number_format(array_sum($r)*100/$total_male[all_person],1).")";
                        echo number_format(sizeof($relationMale))."&emsp;"."(".number_format(sizeof($relationMale)*100/$total_hcode[all_person],1).")";
                    else { ?>
                             <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format(sizeof($relationMale)) ?>
                             <input id="data" type="hidden" value="f1v9a1">
                             <input id="doctorname" type="hidden" value="ความสัมพันธ์ (รวม) [ชาย]">
                             <input id="gender" type="hidden" value="1">
                             <input id="zonedata" type="hidden" value="<?= $zone ?>">
                             <input id="state" type="hidden" value="<?php if ($summaryZone=="Cca01Zone") echo cca01zone; else echo cca01; ?>">
                             </a><?= "&emsp;"."(".number_format(sizeof($relationMale)*100/$total_male[all_person],1).")" ?><br>
                    <?php  } ?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_female[f1v9a1b1],$total_female[f1v9a1b2],$total_female[f1v9a1b3],$total_female[f1v9a1b4],$total_female[f1v9a1b5],$total_female[f1v9a1b6],$total_female[f1v9a1b7],$total_female[f1v9a1b8],$total_female[f1v9a1b9]);
                    foreach($r as $key => $value) 
                    if(($value==0)||checkLiCense($user_Site_Code,$hospitalCode))
                        echo number_format($value)."&emsp;"."(".number_format($value*100/$total_female[all_person],1).")<br>"; 
                     else { ?>
                             <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($value) ?>
                             <input id="data" type="hidden" value="<?= "f1v9a1b".($key+1) ?>">
                             <input id="cca02" type="hidden" value="1">
                             <?php if($key==0) { ?>
                                <input id="doctorname" type="hidden" value="ความสัมพันธ์ (ปู่ ย่า)">
                             <?php } else if($key==1) {?>
                                <input id="doctorname" type="hidden" value="ความสัมพันธ์ (ตา ยาย) [หญิง]">
                             <?php } else if($key==2) {?>
                                <input id="doctorname" type="hidden" value="ความสัมพันธ์ (ลุง ป้า) [หญิง]">
                             <?php } else if($key==3) {?>
                                <input id="doctorname" type="hidden" value="ความสัมพันธ์ (น้า อา) [หญิง]">
                             <?php } else if($key==4) {?>
                                <input id="doctorname" type="hidden" value="ความสัมพันธ์ (พ่อ แม่) [หญิง]">
                             <?php } else if($key==5) {?>
                                <input id="doctorname" type="hidden" value="ความสัมพันธ์ (ลูกชาย ลูกสาว) [หญิง]">
                             <?php } else if($key==6) {?>
                                <input id="doctorname" type="hidden" value="ความสัมพันธ์ (พี่ชายน้องชาย พี่สาวน้องสาว) [หญิง]">
                             <?php } else if($key==7) {?>
                                <input id="doctorname" type="hidden" value="ความสัมพันธ์ (หลานๆ) [หญิง]">
                             <?php } else if($key==8) {?>
                                <input id="doctorname" type="hidden" value="ความสัมพันธ์ (คู่สมรส) [หญิง]">
                             <?php } ?>
                             <input id="gender" type="hidden" value="2">
                             <input id="zonedata" type="hidden" value="<?= $zone ?>">
                             <input id="state" type="hidden" value="<?php if ($summaryZone=="Cca01Zone") echo cca01zone; else echo cca01; ?>">
                             </a><?= "&emsp;"."(".number_format($value*100/$total_hcode[all_person],1).")" ?><br>
                    <?php  }
                    if((sizeof($relationFemale)==0)||checkLiCense($user_Site_Code,$hospitalCode))    //echo number_format(array_sum($r))."&emsp;"."(".number_format(array_sum($r)*100/$total_female[all_person],1).")";
                        echo number_format(sizeof($relationFemale))."&emsp;"."(".number_format(sizeof($relationFemale)*100/$total_hcode[all_person],1).")";
                    else { ?>
                             <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format(sizeof($relationFemale)) ?>
                             <input id="data" type="hidden" value="f1v9a1">
                             <input id="doctorname" type="hidden" value="ความสัมพันธ์ (รวม) [หญิง]">
                             <input id="gender" type="hidden" value="2">
                             <input id="zonedata" type="hidden" value="<?= $zone ?>">
                             <input id="state" type="hidden" value="<?php if ($summaryZone=="Cca01Zone") echo cca01zone; else echo cca01; ?>">
                             </a><?= "&emsp;"."(".number_format(sizeof($relationFemale)*100/$total_female[all_person],1).")" ?><br>
                    <?php  } ?>
                </td>
              </tr>
            <tr>
                <td><strong>9. ประวัติการสูบบุหรี่</strong><br>&emsp;ไม่สูบ<br>&emsp;สูบ/เคยสูบ<br>&emsp;รวม</td>
                 <td align="right">
                    <br><?php  $r = array($total[no_f1v10],$total[yes_f1v10],$total[total_f1v10]);
                    foreach($r as $value) echo number_format($value)."&emsp;"."(".number_format($value*100/$total[all_person],2).")<br>"; ?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_hcode[no_f1v10],$total_hcode[yes_f1v10],$total_hcode[total_f1v10]);
                    foreach($r as $key => $value) 
                     if(($value==0)||checkLiCense($user_Site_Code,$hospitalCode))
                        echo number_format($value)."&emsp;"."(".number_format($value*100/$total_hcode[all_person],1).")<br>";
                     else { ?>
                     <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($value) ?>
                             <input id="data" type="hidden" value="f1v10">
                             <?php  if($key==2){ ?>
                                <input id="cca02" type="hidden" value="0,1">
                                <input id="doctorname" type="hidden" value="9. ประวัติการสูบบุหรี่ (รวม) [รวมในพื้นที่]">
                             <?php  } else if($key==0) {?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="9. ประวัติการสูบบุหรี่ (ไม่สูบ) [รวมในพื้นที่]">
                             <?php  } else if($key==1) { ?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="9. ประวัติการสูบบุหรี่ (สูบ/เคยสูบ) [รวมในพื้นที่]">
                              <?php  } ?>
                             <input id="gender" type="hidden" value="1,2">
                             <input id="zonedata" type="hidden" value="<?= $zone ?>">
                             <input id="state" type="hidden" value="<?php if ($summaryZone=="Cca01Zone") echo cca01zone; else echo cca01; ?>">
                             </a><?= "&emsp;"."(".number_format($value*100/$total_hcode[all_person],1).")" ?><br>
                    <?php  } ?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_male[no_f1v10],$total_male[yes_f1v10],$total_male[total_f1v10]);
                    foreach($r as $key => $value) 
                     if(($value==0)||checkLiCense($user_Site_Code,$hospitalCode))
                        echo number_format($value)."&emsp;"."(".number_format($value*100/$total_male[all_person],1).")<br>"; 
                     else { ?>
                     <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($value) ?>
                             <input id="data" type="hidden" value="f1v10">
                             <?php  if($key==2){ ?>
                                <input id="cca02" type="hidden" value="0,1">
                                <input id="doctorname" type="hidden" value="9. ประวัติการสูบบุหรี่ (รวม) [ชาย]">
                             <?php  } else if($key==0) {?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="9. ประวัติการสูบบุหรี่ (ไม่สูบ) [ชาย]">
                             <?php  } else if($key==1) { ?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="9. ประวัติการสูบบุหรี่ (สูบ/เคยสูบ) [ชาย]">
                              <?php  } ?>
                             <input id="gender" type="hidden" value="1">
                             <input id="zonedata" type="hidden" value="<?= $zone ?>">
                             <input id="state" type="hidden" value="<?php if ($summaryZone=="Cca01Zone") echo cca01zone; else echo cca01; ?>">
                             </a><?= "&emsp;"."(".number_format($value*100/$total_hcode[all_person],1).")" ?><br>
                    <?php  } ?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_female[no_f1v10],$total_female[yes_f1v10],$total_female[total_f1v10]);
                    foreach($r as $key => $value) 
                     if(($value==0)||checkLiCense($user_Site_Code,$hospitalCode))  
                        echo number_format($value)."&emsp;"."(".number_format($value*100/$total_female[all_person],1).")<br>"; 
                     else { ?>
                     <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($value) ?>
                             <input id="data" type="hidden" value="f1v10">
                             <?php  if($key==2){ ?>
                                <input id="cca02" type="hidden" value="0,1">
                                <input id="doctorname" type="hidden" value="9. ประวัติการสูบบุหรี่ (รวม) [หญิง]">
                             <?php  } else if($key==0) {?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="9. ประวัติการสูบบุหรี่ (ไม่สูบ) [หญิง]">
                             <?php  } else if($key==1) { ?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="9. ประวัติการสูบบุหรี่ (สูบ/เคยสูบ) [หญิง]">
                              <?php  } ?>
                             <input id="gender" type="hidden" value="2">
                             <input id="zonedata" type="hidden" value="<?= $zone ?>">
                             <input id="state" type="hidden" value="<?php if ($summaryZone=="Cca01Zone") echo cca01zone; else echo cca01; ?>">
                             </a><?= "&emsp;"."(".number_format($value*100/$total_hcode[all_person],1).")" ?><br>
                    <?php  } ?>
                </td>
              </tr>
            <tr>
                 <td><strong>10. ประวัติการดื่มเครื่องดื่มแอลกอฮอล์</strong><br>&emsp;ไม่ดื่ม<br>&emsp;ดื่ม/เคยดื่ม<br>&emsp;รวม</td>
                <td align="right">
                    <br><?php  $r = array($total[no_f1v11],$total[yes_f1v11],$total[total_f1v11]);
                    foreach($r as $value) echo number_format($value)."&emsp;"."(".number_format($value*100/$total[all_person],2).")<br>"; ?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_hcode[no_f1v11],$total_hcode[yes_f1v11],$total_hcode[total_f1v11]);
                    foreach($r as $key => $value) 
                     if(($value==0)||checkLiCense($user_Site_Code,$hospitalCode))
                        echo number_format($value)."&emsp;"."(".number_format($value*100/$total_hcode[all_person],1).")<br>"; 
                     else { ?>
                     <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($value) ?>
                             <input id="data" type="hidden" value="f1v11">
                             <?php  if($key==2){ ?>
                                <input id="cca02" type="hidden" value="0,1">
                                <input id="doctorname" type="hidden" value="10. ประวัติการดื่มเครื่องดื่มแอลกอฮอล์ (รวม) [รวมในพื้นที่]">
                             <?php  } else if($key==0) {?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="10. ประวัติการดื่มเครื่องดื่มแอลกอฮอล์ (ไม่ดื่ม) [รวมในพื้นที่]">
                             <?php  } else if($key==1) { ?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="10. ประวัติการดื่มเครื่องดื่มแอลกอฮอล์ (ดื่ม/เคยดื่ม) [รวมในพื้นที่]">
                              <?php  } ?>
                             <input id="gender" type="hidden" value="1,2">
                             <input id="zonedata" type="hidden" value="<?= $zone ?>">
                             <input id="state" type="hidden" value="<?php if ($summaryZone=="Cca01Zone") echo cca01zone; else echo cca01; ?>">
                             </a><?= "&emsp;"."(".number_format($value*100/$total_hcode[all_person],1).")" ?><br>
                    <?php  } ?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_male[no_f1v11],$total_male[yes_f1v11],$total_male[total_f1v11]);
                    foreach($r as $key => $value) 
                        if(($value==0)||checkLiCense($user_Site_Code,$hospitalCode))
                            echo number_format($value)."&emsp;"."(".number_format($value*100/$total_male[all_person],1).")<br>"; 
                     else { ?>
                     <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($value) ?>
                             <input id="data" type="hidden" value="f1v11">
                              <?php  if($key==2){ ?>
                                <input id="cca02" type="hidden" value="0,1">
                                <input id="doctorname" type="hidden" value="10. ประวัติการดื่มเครื่องดื่มแอลกอฮอล์ (รวม) [ชาย]">
                             <?php  } else if($key==0) {?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="10. ประวัติการดื่มเครื่องดื่มแอลกอฮอล์ (ไม่ดื่ม) [ชาย]">
                             <?php  } else if($key==1) { ?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="10. ประวัติการดื่มเครื่องดื่มแอลกอฮอล์ (ดื่ม/เคยดื่ม) [ชาย]">
                              <?php  } ?>
                             <input id="gender" type="hidden" value="1">
                             <input id="zonedata" type="hidden" value="<?= $zone ?>">
                             <input id="state" type="hidden" value="<?php if ($summaryZone=="Cca01Zone") echo cca01zone; else echo cca01; ?>">
                             </a><?= "&emsp;"."(".number_format($value*100/$total_hcode[all_person],1).")" ?><br>
                    <?php  } ?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_female[no_f1v11],$total_female[yes_f1v11],$total_female[total_f1v11]);
                    foreach($r as $key => $value) 
                        if(($value==0)||checkLiCense($user_Site_Code,$hospitalCode))
                            echo number_format($value)."&emsp;"."(".number_format($value*100/$total_female[all_person],1).")<br>"; 
                         else { ?>
                            <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($value) ?>
                             <input id="data" type="hidden" value="f1v11">
                              <?php  if($key==2){ ?>
                                <input id="cca02" type="hidden" value="0,1">
                                <input id="doctorname" type="hidden" value="10. ประวัติการดื่มเครื่องดื่มแอลกอฮอล์ (รวม) [หญิง]">
                             <?php  } else if($key==0) {?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="10. ประวัติการดื่มเครื่องดื่มแอลกอฮอล์ (ไม่ดื่ม) [หญิง]">
                             <?php  } else if($key==1) { ?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="10. ประวัติการดื่มเครื่องดื่มแอลกอฮอล์ (ดื่ม/เคยดื่ม) [หญิง]">
                              <?php  } ?>
                             <input id="gender" type="hidden" value="2">
                             <input id="zonedata" type="hidden" value="<?= $zone ?>">
                             <input id="state" type="hidden" value="<?php if ($summaryZone=="Cca01Zone") echo cca01zone; else echo cca01; ?>">
                             </a><?= "&emsp;"."(".number_format($value*100/$total_hcode[all_person],1).")" ?><br>
                    <?php  } ?>
                </td>
                </tr>
            <tr>
                <td><strong>11. คุณเป็นโรคพิษสุราเรื้อรังหรือไม่</strong><br>&emsp;ไม่เป็น<br>&emsp;เป็น/เคยเป็น<br>&emsp;รวม</td>
                <td align="right">
                    <br><?php  $r = array($total[no_f1v12],$total[yes_f1v12],$total[total_f1v12]);
                    foreach($r as $value) echo number_format($value)."&emsp;"."(".number_format($value*100/$total[all_person],2).")<br>"; ?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_hcode[no_f1v12],$total_hcode[yes_f1v12],$total_hcode[total_f1v12]);
                    foreach($r as $key => $value) 
                        if(($value==0)||checkLiCense($user_Site_Code,$hospitalCode))
                            echo number_format($value)."&emsp;"."(".number_format($value*100/$total_hcode[all_person],1).")<br>";
                         else { ?>
                        <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($value) ?>
                             <input id="data" type="hidden" value="f1v12">
                             <?php  if($key==2){ ?>
                                <input id="cca02" type="hidden" value="0,1">
                                <input id="doctorname" type="hidden" value="11. คุณเป็นโรคพิษสุราเรื้อรังหรือไม่ (รวม) [รวมในพื้นที่]">
                             <?php  } else if($key==0) {?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="11. คุณเป็นโรคพิษสุราเรื้อรังหรือไม่ (ไม่เป็น) [รวมในพื้นที่]">
                             <?php  } else if($key==1) { ?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="11. คุณเป็นโรคพิษสุราเรื้อรังหรือไม่ (เป็น/เคยเป็น) [รวมในพื้นที่]">
                              <?php  } ?>
                             <input id="gender" type="hidden" value="1,2">
                             <input id="zonedata" type="hidden" value="<?= $zone ?>">
                             <input id="state" type="hidden" value="<?php if ($summaryZone=="Cca01Zone") echo cca01zone; else echo cca01; ?>">
                             </a><?= "&emsp;"."(".number_format($value*100/$total_hcode[all_person],1).")" ?><br>
                    <?php  } ?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_male[no_f1v12],$total_male[yes_f1v12],$total_male[total_f1v12]);
                    foreach($r as $key => $value) 
                        if(($value==0)||checkLiCense($user_Site_Code,$hospitalCode))
                            echo number_format($value)."&emsp;"."(".number_format($value*100/$total_male[all_person],1).")<br>"; 
                        else { ?>
                         <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($value) ?>
                             <input id="data" type="hidden" value="f1v12">
                              <?php  if($key==2){ ?>
                                <input id="cca02" type="hidden" value="0,1">
                                <input id="doctorname" type="hidden" value="11. คุณเป็นโรคพิษสุราเรื้อรังหรือไม่ (รวม) [ชาย]">
                             <?php  } else if($key==0) {?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="11. คุณเป็นโรคพิษสุราเรื้อรังหรือไม่ (ไม่เป็น) [ชาย]">
                             <?php  } else if($key==1) { ?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="11. คุณเป็นโรคพิษสุราเรื้อรังหรือไม่ (เป็น/เคยเป็น) [ชาย]">
                              <?php  } ?>
                             <input id="gender" type="hidden" value="1">
                             <input id="zonedata" type="hidden" value="<?= $zone ?>">
                             <input id="state" type="hidden" value="<?php if ($summaryZone=="Cca01Zone") echo cca01zone; else echo cca01; ?>">
                             </a><?= "&emsp;"."(".number_format($value*100/$total_male[all_person],1).")" ?><br>
                    <?php  } ?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_female[no_f1v12],$total_female[yes_f1v12],$total_female[total_f1v12]);
                    foreach($r as $key => $value)
                        if(($value==0)||checkLiCense($user_Site_Code,$hospitalCode))
                            echo number_format($value)."&emsp;"."(".number_format($value*100/$total_female[all_person],1).")<br>"; 
                        else { ?>
                             <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($value) ?>
                             <input id="data" type="hidden" value="f1v12">
                              <?php  if($key==2){ ?>
                                <input id="cca02" type="hidden" value="0,1">
                                <input id="doctorname" type="hidden" value="11. คุณเป็นโรคพิษสุราเรื้อรังหรือไม่ (รวม) [หญิง]">
                             <?php  } else if($key==0) {?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="11. คุณเป็นโรคพิษสุราเรื้อรังหรือไม่ (ไม่เป็น) [หญิง]">
                             <?php  } else if($key==1) { ?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="11. คุณเป็นโรคพิษสุราเรื้อรังหรือไม่ (เป็น/เคยเป็น) [หญิง]">
                              <?php  } ?>
                             <input id="gender" type="hidden" value="2">
                             <input id="zonedata" type="hidden" value="<?= $zone ?>">
                             <input id="state" type="hidden" value="<?php if ($summaryZone=="Cca01Zone") echo cca01zone; else echo cca01; ?>">
                             </a><?= "&emsp;"."(".number_format($value*100/$total_female[all_person],1).")" ?><br>
                    <?php  } ?>
                </td>
            </tr>
            <tr>
                <td><strong>12. ประวัติการรับประทานปลาน้ำจืดที่มีเกล็ดดิบๆสุกๆ หรือปลาร้าไม่ต้มสุก</strong><br>&emsp;ไม่เคย<br>&emsp;เคย<br>&emsp;รวม</td>
                <td align="right">
                    <br><?php  $r = array($total[no_f1v13],$total[yes_f1v13],$total[total_f1v13]);
                    foreach($r as $value) echo number_format($value)."&emsp;"."(".number_format($value*100/$total[all_person],2).")<br>"; ?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_hcode[no_f1v13],$total_hcode[yes_f1v13],$total_hcode[total_f1v13]);
                    foreach($r as $key => $value) 
                        if(($value==0)||checkLiCense($user_Site_Code,$hospitalCode))
                            echo number_format($value)."&emsp;"."(".number_format($value*100/$total_hcode[all_person],1).")<br>"; 
                        else { ?>
                        <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($value) ?>
                             <input id="data" type="hidden" value="f1v13">
                              <?php  if($key==2){ ?>
                                <input id="cca02" type="hidden" value="0,1">
                                <input id="doctorname" type="hidden" value="12. ประวัติการรับประทานปลาน้ำจืดที่มีเกล็ดดิบๆสุกๆ หรือปลาร้าไม่ต้มสุก (รวม) [รวมในพื้นที่]">
                             <?php  } else if($key==0) {?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="12. ประวัติการรับประทานปลาน้ำจืดที่มีเกล็ดดิบๆสุกๆ หรือปลาร้าไม่ต้มสุก (ไม่เคย) [รวมในพื้นที่]">
                             <?php  } else if($key==1) { ?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="12. ประวัติการรับประทานปลาน้ำจืดที่มีเกล็ดดิบๆสุกๆ หรือปลาร้าไม่ต้มสุก (เคย) [รวมในพื้นที่]">
                              <?php  } ?>
                             <input id="gender" type="hidden" value="1,2">
                             <input id="zonedata" type="hidden" value="<?= $zone ?>">
                             <input id="state" type="hidden" value="<?php if ($summaryZone=="Cca01Zone") echo cca01zone; else echo cca01; ?>">
                             </a><?= "&emsp;"."(".number_format($value*100/$total_hcode[all_person],1).")" ?><br>
                    <?php  } ?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_male[no_f1v13],$total_male[yes_f1v13],$total_male[total_f1v13]);
                    foreach($r as $key => $value) 
                        if(($value==0)||checkLiCense($user_Site_Code,$hospitalCode))
                            echo number_format($value)."&emsp;"."(".number_format($value*100/$total_male[all_person],1).")<br>";
                        else { ?>
                         <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($value) ?>
                             <input id="data" type="hidden" value="f1v13">
                              <?php  if($key==2){ ?>
                                <input id="cca02" type="hidden" value="0,1">
                                <input id="doctorname" type="hidden" value="12. ประวัติการรับประทานปลาน้ำจืดที่มีเกล็ดดิบๆสุกๆ หรือปลาร้าไม่ต้มสุก (รวม) [ชาย]">
                             <?php  } else if($key==0) {?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="12. ประวัติการรับประทานปลาน้ำจืดที่มีเกล็ดดิบๆสุกๆ หรือปลาร้าไม่ต้มสุก (ไม่เคย) [ชาย]">
                             <?php  } else if($key==1) { ?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="12. ประวัติการรับประทานปลาน้ำจืดที่มีเกล็ดดิบๆสุกๆ หรือปลาร้าไม่ต้มสุก (เคย) [ชาย]">
                              <?php  } ?>
                             <input id="gender" type="hidden" value="1">
                             <input id="zonedata" type="hidden" value="<?= $zone ?>">
                             <input id="state" type="hidden" value="<?php if ($summaryZone=="Cca01Zone") echo cca01zone; else echo cca01; ?>">
                             </a><?= "&emsp;"."(".number_format($value*100/$total_male[all_person],1).")" ?><br>
                    <?php  } ?>
                </td>
                <td align="right">                   
                    <br><?php  $r=null; $r = array($total_female[no_f1v13],$total_female[yes_f1v13],$total_female[total_f1v13]);
                    foreach($r as $key => $value) 
                        if(($value==0)||checkLiCense($user_Site_Code,$hospitalCode))
                            echo number_format($value)."&emsp;"."(".number_format($value*100/$total_female[all_person],1).")<br>"; 
                        else { ?>
                             <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($value) ?>
                             <input id="data" type="hidden" value="f1v13">
                              <?php  if($key==2){ ?>
                                <input id="cca02" type="hidden" value="0,1">
                                <input id="doctorname" type="hidden" value="12. ประวัติการรับประทานปลาน้ำจืดที่มีเกล็ดดิบๆสุกๆ หรือปลาร้าไม่ต้มสุก (รวม) [หญิง]">
                             <?php  } else if($key==0) {?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="12. ประวัติการรับประทานปลาน้ำจืดที่มีเกล็ดดิบๆสุกๆ หรือปลาร้าไม่ต้มสุก (ไม่เคย) [หญิง]">
                             <?php  } else if($key==1) { ?>
                                <input id="cca02" type="hidden" value="<?= $key ?>">
                                <input id="doctorname" type="hidden" value="12. ประวัติการรับประทานปลาน้ำจืดที่มีเกล็ดดิบๆสุกๆ หรือปลาร้าไม่ต้มสุก (เคย) [หญิง]">
                              <?php  } ?>
                             <input id="gender" type="hidden" value="2">
                             <input id="zonedata" type="hidden" value="<?= $zone ?>">
                             <input id="state" type="hidden" value="<?php if ($summaryZone=="Cca01Zone") echo cca01zone; else echo cca01; ?>">
                             </a><?= "&emsp;"."(".number_format($value*100/$total_female[all_person],1).")" ?><br>
                    <?php  } ?>
                </td>
               </tr>
            <tr>
                <td><strong>13. คุณเคยได้รับการวินิจฉัยจากแพทย์ว่าเป็นโรคใดบ้างต่อไปนี้</strong><br>&emsp;ไม่เป็น<br>&emsp;ตับอักเสบ บี<br>&emsp;ตับอักเสบ ซี<br>&emsp;เบาหวาน<br>&emsp;อื่นๆ<br>&emsp;รวม (โรคอย่างใดอย่างหนึ่ง)</td>
                <td align="right">
                    <br><?php  $r = array($total[f1v14a0],$total[f1v14a1],$total[f1v14a2],$total[f1v14a3],$total[f1v14a4]);
                    foreach($r as $value) echo number_format($value)."&emsp;"."(".number_format($value*100/$total[all_person],2).")<br>"; echo number_format($f1v14[person])."&emsp;"."(".number_format($f1v14[person]*100/$total[all_person],2).")"; ?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_hcode[f1v14a0],$total_hcode[f1v14a1],$total_hcode[f1v14a2],$total_hcode[f1v14a3],$total_hcode[f1v14a4]);
                    foreach($r as $key => $value) 
                    if(($value==0)||checkLiCense($user_Site_Code,$hospitalCode))
                        echo number_format($value)."&emsp;"."(".number_format($value*100/$total_hcode[all_person],1).")<br>"; 
                    else { ?>
                             <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($value) ?>
                             <input id="data" type="hidden" value="<?= "f1v14a".$key ?>">
                             <input id="cca02" type="hidden" value="1">
                              <?php if($key==0) { ?>
                                <input id="doctorname" type="hidden" value="13. คุณเคยได้รับการวินิจฉัยจากแพทย์ว่าเป็นโรคใดบ้างต่อไปนี้ (ไม่เป็น) [รวมในพื้นที่]">
                             <?php } else if($key==1) {?>
                                <input id="doctorname" type="hidden" value="13. คุณเคยได้รับการวินิจฉัยจากแพทย์ว่าเป็นโรคใดบ้างต่อไปนี้ (ตับอักเสบ บี) [รวมในพื้นที่]">
                             <?php } else if($key==2) {?>
                                <input id="doctorname" type="hidden" value="13. คุณเคยได้รับการวินิจฉัยจากแพทย์ว่าเป็นโรคใดบ้างต่อไปนี้ (ตับอักเสบ ซี) [รวมในพื้นที่]">
                             <?php } else if($key==3) {?>
                                <input id="doctorname" type="hidden" value="13. คุณเคยได้รับการวินิจฉัยจากแพทย์ว่าเป็นโรคใดบ้างต่อไปนี้ (เบาหวาน) [รวมในพื้นที่]">
                             <?php } else if($key==4) {?>
                                <input id="doctorname" type="hidden" value="13. คุณเคยได้รับการวินิจฉัยจากแพทย์ว่าเป็นโรคใดบ้างต่อไปนี้ (อื่นๆ) [รวมในพื้นที่]">
                             <?php } ?>
                             <input id="gender" type="hidden" value="1,2">
                             <input id="zonedata" type="hidden" value="<?= $zone ?>">
                             <input id="state" type="hidden" value="<?php if ($summaryZone=="Cca01Zone") echo cca01zone; else echo cca01; ?>">
                             </a><?= "&emsp;"."(".number_format($value*100/$total_hcode[all_person],1).")" ?><br>
                    <?php  }
                    if(($f1v14hos[diagnose]==0)||checkLiCense($user_Site_Code,$hospitalCode))
                        echo number_format($f1v14hos[diagnose])."&emsp;"."(".number_format($f1v14hos[diagnose]*100/$total_hcode[all_person],1).")";
                    else { ?>
                             <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($f1v14hos[diagnose]) ?>
                             <input id="data" type="hidden" value="f1v14">
                             <input id="doctorname" type="hidden" value="13. คุณเคยได้รับการวินิจฉัยจากแพทย์ว่าเป็นโรคใดบ้างต่อไปนี้ (รวม) [รวมในพื้นที่]">
                             <input id="gender" type="hidden" value="1,2">
                             <input id="zonedata" type="hidden" value="<?= $zone ?>">
                             <input id="state" type="hidden" value="<?php if ($summaryZone=="Cca01Zone") echo cca01zone; else echo cca01; ?>">
                             </a><?= "&emsp;"."(".number_format($f1v14hos[diagnose]*100/$total_hcode[all_person],1).")" ?><br>
                    <?php  } ?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_male[f1v14a0],$total_male[f1v14a1],$total_male[f1v14a2],$total_male[f1v14a3],$total_male[f1v14a4]);
                    foreach($r as $key =>$value) 
                    if(($value==0)||checkLiCense($user_Site_Code,$hospitalCode))
                        echo number_format($value)."&emsp;"."(".number_format($value*100/$total_male[all_person],1).")<br>"; 
                    else { ?>
                             <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($value) ?>
                             <input id="data" type="hidden" value="<?= "f1v14a".$key ?>">
                             <input id="cca02" type="hidden" value="1">
                              <?php if($key==0) { ?>
                                <input id="doctorname" type="hidden" value="13. คุณเคยได้รับการวินิจฉัยจากแพทย์ว่าเป็นโรคใดบ้างต่อไปนี้ (ไม่เป็น) [ชาย]">
                             <?php } else if($key==1) {?>
                                <input id="doctorname" type="hidden" value="13. คุณเคยได้รับการวินิจฉัยจากแพทย์ว่าเป็นโรคใดบ้างต่อไปนี้ (ตับอักเสบ บี) [ชาย]">
                             <?php } else if($key==2) {?>
                                <input id="doctorname" type="hidden" value="13. คุณเคยได้รับการวินิจฉัยจากแพทย์ว่าเป็นโรคใดบ้างต่อไปนี้ (ตับอักเสบ ซี) [ชาย]">
                             <?php } else if($key==3) {?>
                                <input id="doctorname" type="hidden" value="13. คุณเคยได้รับการวินิจฉัยจากแพทย์ว่าเป็นโรคใดบ้างต่อไปนี้ (เบาหวาน) [ชาย]">
                             <?php } else if($key==4) {?>
                                <input id="doctorname" type="hidden" value="13. คุณเคยได้รับการวินิจฉัยจากแพทย์ว่าเป็นโรคใดบ้างต่อไปนี้ (อื่นๆ) [ชาย]">
                             <?php } ?>
                             <input id="gender" type="hidden" value="1">
                             <input id="zonedata" type="hidden" value="<?= $zone ?>">
                             <input id="state" type="hidden" value="<?php if ($summaryZone=="Cca01Zone") echo cca01zone; else echo cca01; ?>">
                             </a><?= "&emsp;"."(".number_format($value*100/$total_male[all_person],1).")" ?><br>
                    <?php  }
                     if(($f1v14hosmale[diagnose]==0)||checkLiCense($user_Site_Code,$hospitalCode))
                        echo number_format($f1v14hosmale[diagnose])."&emsp;"."(".number_format($f1v14hosmale[diagnose]*100/$total_male[all_person],1).")";
                     else { ?>
                             <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($f1v14hosmale[diagnose]) ?>
                             <input id="data" type="hidden" value="f1v14">
                             <input id="doctorname" type="hidden" value="13. คุณเคยได้รับการวินิจฉัยจากแพทย์ว่าเป็นโรคใดบ้างต่อไปนี้ (รวม) [ชาย]">
                             <input id="gender" type="hidden" value="1">
                             <input id="zonedata" type="hidden" value="<?= $zone ?>">
                             <input id="state" type="hidden" value="<?php if ($summaryZone=="Cca01Zone") echo cca01zone; else echo cca01; ?>">
                             </a><?= "&emsp;"."(".number_format($f1v14hosmale[diagnose]*100/$total_male[all_person],1).")" ?><br>
                    <?php  } ?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_female[f1v14a0],$total_female[f1v14a1],$total_female[f1v14a2],$total_female[f1v14a3],$total_female[f1v14a4]);
                    foreach($r as $key => $value) 
                    if(($value==0)||checkLiCense($user_Site_Code,$hospitalCode))
                        echo number_format($value)."&emsp;"."(".number_format($value*100/$total_female[all_person],1).")<br>"; 
                    else { ?>
                             <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($value) ?>
                             <input id="data" type="hidden" value="<?= "f1v14a".$key ?>">
                             <input id="cca02" type="hidden" value="1">
                              <?php if($key==0) { ?>
                                <input id="doctorname" type="hidden" value="13. คุณเคยได้รับการวินิจฉัยจากแพทย์ว่าเป็นโรคใดบ้างต่อไปนี้ (ไม่เป็น) [หญิง]">
                             <?php } else if($key==1) {?>
                                <input id="doctorname" type="hidden" value="13. คุณเคยได้รับการวินิจฉัยจากแพทย์ว่าเป็นโรคใดบ้างต่อไปนี้ (ตับอักเสบ บี) [หญิง]">
                             <?php } else if($key==2) {?>
                                <input id="doctorname" type="hidden" value="13. คุณเคยได้รับการวินิจฉัยจากแพทย์ว่าเป็นโรคใดบ้างต่อไปนี้ (ตับอักเสบ ซี) [หญิง]">
                             <?php } else if($key==3) {?>
                                <input id="doctorname" type="hidden" value="13. คุณเคยได้รับการวินิจฉัยจากแพทย์ว่าเป็นโรคใดบ้างต่อไปนี้ (เบาหวาน) [หญิง]">
                             <?php } else if($key==4) {?>
                                <input id="doctorname" type="hidden" value="13. คุณเคยได้รับการวินิจฉัยจากแพทย์ว่าเป็นโรคใดบ้างต่อไปนี้ (อื่นๆ) [หญิง]">
                             <?php } ?>
                             <input id="gender" type="hidden" value="2">
                             <input id="zonedata" type="hidden" value="<?= $zone ?>">
                             <input id="state" type="hidden" value="<?php if ($summaryZone=="Cca01Zone") echo cca01zone; else echo cca01; ?>">
                             </a><?= "&emsp;"."(".number_format($value*100/$total_female[all_person],1).")" ?><br>
                    <?php  }
                   if(($f1v14hosfemale[diagnose]==0)||checkLiCense($user_Site_Code,$hospitalCode))
                        echo number_format($f1v14hosfemale[diagnose])."&emsp;"."(".number_format($f1v14hosfemale[diagnose]*100/$total_male[all_person],1).")";
                     else { ?>
                             <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($f1v14hosfemale[diagnose]) ?>
                             <input id="data" type="hidden" value="f1v14">
                             <input id="doctorname" type="hidden" value="13. คุณเคยได้รับการวินิจฉัยจากแพทย์ว่าเป็นโรคใดบ้างต่อไปนี้ (รวม) [หญิง]">
                             <input id="gender" type="hidden" value="2">
                             <input id="zonedata" type="hidden" value="<?= $zone ?>">
                             <input id="state" type="hidden" value="<?php if ($summaryZone=="Cca01Zone") echo cca01zone; else echo cca01; ?>">
                             </a><?= "&emsp;"."(".number_format($f1v14hosfemale[diagnose]*100/$total_female[all_person],1).")" ?><br>
                    <?php  } ?>
                </td>
            </tr>
        </table>
    </div>
</div>

<!-- Table for export to excellllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllll -->
<div id="data-report-table" class="container" style="display: none">
<table class="table table-bordered" style="font-size:16px;" >
            <tr>
                <td rowspan="3" style="vertical-align:middle"><strong><center>ข้อมูลพื้นฐาน</center></strong></td> <td rowspan="2" style="vertical-align:middle"><strong><center>รวมทั้งหมดใน CASCAP</center></strong></td><td colspan="3"><strong><center>เฉพาะในพื้นที่</center></strong></td>
            </tr>
            <tr align="center">
                <td><strong>รวมในพื้นที่</strong></td> <td><strong>ชาย</strong></td> <td><strong>หญิง</strong></td>
            </tr>
             <tr align="center">
                <td><strong>n (%)</strong></td> <td><strong>n (%)</strong></td><td><strong>n (%)</strong></td><td><strong>n (%)</strong></td>
            </tr>
            <tr>
                <td><strong>1. จำนวนผู้เข้าร่วมโครงการ</strong></td>
                <td align="right"><strong><?= number_format($total[all_person]).$testD[0][na] ?></strong></td>
                <td align="right"><strong><?= number_format($total_hcode[all_person])." (".number_format($total_hcode[all_person]*100/$total[all_person],2).")" ?></strong></td>
                <td align="right"><strong><?= number_format($total_male[all_person])." (".number_format($total_male[all_person]*100/$total_hcode[all_person],1).")" ?></strong></td>
                <td align="right"><strong><?= number_format($total_female[all_person])." (".number_format($total_female[all_person]*100/$total_hcode[all_person],1).")" ?></strong></td>
            </tr>
            <tr>
                <td><strong>2. อายุ (ปี)</strong><br>&emsp;20 - 29<br>&emsp;30 - 39<br>&emsp;40 - 49<br>&emsp;50 - 59<br>&emsp;60 - 69<br>&emsp;70 - 79<br>&emsp;80+<br>&emsp;รวม<br>&emsp;ค่าเฉลี่ย ± ส่วนเบี่ยงเบนมาตรฐาน<br>&emsp;ค่ามัธยฐาน (ต่ำสุด : สูงสุด)</td>
                <td align="right">
                    <br><?php  $r = array($total_age[age_20],$total_age[age_30],$total_age[age_40],$total_age[age_50],$total_age[age_60],$total_age[age_70],$total_age[age_80],$total_age[person]);
                    foreach($r as $value) echo number_format($value)."&emsp;"."(".number_format($value*100/$total[all_person],2).")<br>"; 
                    echo number_format($total_age[avg],1)." ± ".number_format($total_age[sd],1)."<br>".number_format($total_median[median])." ( ".number_format($total_age[min])." : ".number_format($total_age[max])." )"?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_age_hcode[age_20],$total_age_hcode[age_30],$total_age_hcode[age_40],$total_age_hcode[age_50],$total_age_hcode[age_60],$total_age_hcode[age_70],$total_age_hcode[age_80],$total_age_hcode[person]);
                    foreach($r as $value) 
                        echo number_format($value)."&emsp;"."(".number_format($value*100/$total_hcode[all_person],1).")<br>"; 
                        echo number_format($total_age_hcode[avg],1)." ± ".number_format($total_age_hcode[sd],1)."<br>".number_format($total_median_hcode)." ( ".number_format($total_age_hcode[min])." : ".number_format($total_age_hcode[max])." )"?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_age_male[age_20],$total_age_male[age_30],$total_age_male[age_40],$total_age_male[age_50],$total_age_male[age_60],$total_age_male[age_70],$total_age_male[age_80],$total_age_male[person]);
                    foreach($r as $value) 
                        echo number_format($value)."&emsp;"."(".number_format($value*100/$total_male[all_person],1).")<br>"; 
                        echo number_format($total_age_male[avg],1)." ± ".number_format($total_age_male[sd],1)."<br>".number_format($total_median_male)." ( ".number_format($total_age_male[min])." : ".number_format($total_age_male[max])." )"?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_age_female[age_20],$total_age_female[age_30],$total_age_female[age_40],$total_age_female[age_50],$total_age_female[age_60],$total_age_female[age_70],$total_age_female[age_80],$total_age_female[person]);
                    foreach($r as $value) 
                        echo number_format($value)."&emsp;"."(".number_format($value*100/$total_female[all_person],1).")<br>"; 
                        echo number_format($total_age_female[avg],1)." ± ".number_format($total_age_female[sd],1)."<br>".number_format($total_median_female)." ( ".number_format($total_age_female[min])." : ".number_format($total_age_female[max])." )"?>
                </td>
               </tr>
            <tr>
                <td><strong>3. การศึกษา</strong><br>&emsp;ไม่ได้รับการศึกษา<br>&emsp;ประถมศึกษา<br>&emsp;มัธยมศึกษาตอนต้น<br>&emsp;มัธยมศึกษาตอนปลาย<br>&emsp;ประกาศนียบัตร<br>&emsp;ปริญญาตรี<br>&emsp;สูงกว่าปริญญาตรี<br>&emsp;รวม</td>
                <td align="right">
                    <br><?php  $r = array($total[non_edu],$total[primary_edu],$total[junior_edu],$total[senior_edu],$total[vocational_edu],$total[bachelor_edu],$total[master_edu],$total[total_edu]);
                    foreach($r as $value) echo number_format($value)."&emsp;"."(".number_format($value*100/$total[all_person],2).")<br>"; ?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_hcode[non_edu],$total_hcode[primary_edu],$total_hcode[junior_edu],$total_hcode[senior_edu],$total_hcode[vocational_edu],$total_hcode[bachelor_edu],$total_hcode[master_edu],$total_hcode[total_edu]);
                    foreach($r as $value)   echo number_format($value)."&emsp;"."(".number_format($value*100/$total_hcode[all_person],1).")<br>";  ?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_male[non_edu],$total_male[primary_edu],$total_male[junior_edu],$total_male[senior_edu],$total_male[vocational_edu],$total_male[bachelor_edu],$total_male[master_edu],$total_male[total_edu]);
                    foreach($r as $value)   echo number_format($value)."&emsp;"."(".number_format($value*100/$total_male[all_person],1).")<br>";  ?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_female[non_edu],$total_female[primary_edu],$total_female[junior_edu],$total_female[senior_edu],$total_female[vocational_edu],$total_female[bachelor_edu],$total_female[master_edu],$total_female[total_edu]);
                    foreach($r as $value)   echo number_format($value)."&emsp;"."(".number_format($value*100/$total_female[all_person],1).")<br>";  ?>
                </td>
             </tr>
            <tr>
                <td><strong>4. อาชีพ</strong><br>&emsp;ว่างงาน<br>&emsp;เกษตรกรรม<br>&emsp;รับจ้าง<br>&emsp;ธุรกิจส่วนตัว<br>&emsp;รับราชการ / รัฐวิสาหกิจ<br>&emsp;อื่นๆ<br>&emsp;รวม</td>
                <td align="right">
                    <br><?php  $r = array($total[non_job],$total[farmer_job],$total[emp_job],$total[business_job],$total[gov_job],$total[other_job],$total[total_work]);
                    foreach($r as $value) echo number_format($value)."&emsp;"."(".number_format($value*100/$total[all_person],2).")<br>"; ?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_hcode[non_job],$total_hcode[farmer_job],$total_hcode[emp_job],$total_hcode[business_job],$total_hcode[gov_job],$total_hcode[other_job],$total_hcode[total_work]);
                    foreach($r as $value)   echo number_format($value)."&emsp;"."(".number_format($value*100/$total_hcode[all_person],1).")<br>"; ?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_male[non_job],$total_male[farmer_job],$total_male[emp_job],$total_male[business_job],$total_male[gov_job],$total_male[other_job],$total_male[total_work]);
                    foreach($r as $value)   echo number_format($value)."&emsp;"."(".number_format($value*100/$total_male[all_person],1).")<br>";  ?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_female[non_job],$total_female[farmer_job],$total_female[emp_job],$total_female[business_job],$total_female[gov_job],$total_female[other_job],$total_female[total_work]);
                    foreach($r as $value)   echo number_format($value)."&emsp;"."(".number_format($value*100/$total_female[all_person],1).")<br>";  ?>
                </td>
            </tr>
            <tr>
                <td><strong>5. ประวัติการได้รับการตรวจหาไข่พยาธิใบไม้ตับ</strong><br>&emsp;ไม่เคย<br>&emsp;เคย 1 ครั้ง<br>&emsp;เคย 2 ครั้ง<br>&emsp;เคย 3 ครั้ง<br>&emsp;เคยมากกว่า 3 ครั้ง<br>&emsp;จำไม่ได้<br>&emsp;รวม</td>
                <td align="right">
                    <br><?php  $r = array($total[non_f1v6],$total[first_f1v6],$total[second_f1v6],$total[third_f1v6],$total[more_f1v6],$total[forget_f1v6],$total[total_f1v6]);
                    foreach($r as $value)  echo number_format($value)."&emsp;"."(".number_format($value*100/$total[all_person],2).")<br>"; ?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_hcode[non_f1v6],$total_hcode[first_f1v6],$total_hcode[second_f1v6],$total_hcode[third_f1v6],$total_hcode[more_f1v6],$total_hcode[forget_f1v6],$total_hcode[total_f1v6]);
                    foreach($r as $value)  echo number_format($value)."&emsp;"."(".number_format($value*100/$total_hcode[all_person],1).")<br>"; ?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_male[non_f1v6],$total_male[first_f1v6],$total_male[second_f1v6],$total_male[third_f1v6],$total_male[more_f1v6],$total_male[forget_f1v6],$total_male[total_f1v6]);
                    foreach($r as $value)  echo number_format($value)."&emsp;"."(".number_format($value*100/$total_male[all_person],1).")<br>"; ?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_female[non_f1v6],$total_female[first_f1v6],$total_female[second_f1v6],$total_female[third_f1v6],$total_female[more_f1v6],$total_female[forget_f1v6],$total_female[total_f1v6]);
                     foreach($r as $value)  echo number_format($value)."&emsp;"."(".number_format($value*100/$total_female[all_person],1).")<br>"; ?>
                </td>
                </tr>
            <tr>
                <td><strong>6. ประวัติการตรวจพบไข่พยาธิใบไม้ตับ</strong><br>&emsp;ไม่เคย<br>&emsp;ตรวจแต่ไม่พบ<br>&emsp;ตรวจแล้วพบไข่พยาธิ<br>&emsp;จำไม่ได้<br>&emsp;รวม</td>
                 <td align="right">
                    <br><?php  $r = array($total[non_f1v7],$total[non_meet_f1v7],$total[meet_f1v7],$total[forget_f1v7],$total[total_f1v7]);
                    foreach($r as $value) echo number_format($value)."&emsp;"."(".number_format($value*100/$total[all_person],2).")<br>"; ?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_hcode[non_f1v7],$total_hcode[non_meet_f1v7],$total_hcode[meet_f1v7],$total_hcode[forget_f1v7],$total_hcode[total_f1v7]);
                    foreach($r as $value)    echo number_format($value)."&emsp;"."(".number_format($value*100/$total_hcode[all_person],1).")<br>"; ?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_male[non_f1v7],$total_male[non_meet_f1v7],$total_male[meet_f1v7],$total_male[forget_f1v7],$total_male[total_f1v7]);
                    foreach($r as $value)    echo number_format($value)."&emsp;"."(".number_format($value*100/$total_male[all_person],1).")<br>"; ?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_female[non_f1v7],$total_female[non_meet_f1v7],$total_female[meet_f1v7],$total_female[forget_f1v7],$total_female[total_f1v7]);
                   // foreach($r as $value) echo number_format($value)."&emsp;"."(".number_format($value*100/$total_female[all_person],1).")<br>"; 
                    foreach($r as $value)  echo number_format($value)."&emsp;"."(".number_format($value*100/$total_female[all_person],1).")<br>"; ?>
                </td>
                </tr>
            <tr>
                <td><strong>7. ประวัติการได้รับการรักษาด้วย ยาฆ่าพยาธิใบไม้ตับ</strong><br>&emsp;ไม่เคย<br>&emsp;เคย 1 ครั้ง<br>&emsp;เคย 2 ครั้ง<br>&emsp;เคย 3 ครั้ง<br>&emsp;เคยมากกว่า 3 ครั้ง<br>&emsp;จำไม่ได้<br>&emsp;รวม</td>
                 <td align="right">
                    <br><?php  $r = array($total[non_f1v8],$total[first_f1v8],$total[second_f1v8],$total[third_f1v8],$total[more_f1v8],$total[forget_f1v8],$total[total_f1v8]);
                    foreach($r as $value) echo number_format($value)."&emsp;"."(".number_format($value*100/$total[all_person],2).")<br>"; ?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_hcode[non_f1v8],$total_hcode[first_f1v8],$total_hcode[second_f1v8],$total_hcode[third_f1v8],$total_hcode[more_f1v8],$total_hcode[forget_f1v8],$total_hcode[total_f1v8]);
                     foreach($r as $value)  echo number_format($value)."&emsp;"."(".number_format($value*100/$total_hcode[all_person],1).")<br>"; ?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_male[non_f1v8],$total_male[first_f1v8],$total_male[second_f1v8],$total_male[third_f1v8],$total_male[more_f1v8],$total_male[forget_f1v8],$total_male[total_f1v8]);
                    foreach($r as $value) echo number_format($value)."&emsp;"."(".number_format($value*100/$total_male[all_person],1).")<br>";?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_female[non_f1v8],$total_female[first_f1v8],$total_female[second_f1v8],$total_female[third_f1v8],$total_female[more_f1v8],$total_female[forget_f1v8],$total_female[total_f1v8]);
                    foreach($r as $value) echo number_format($value)."&emsp;"."(".number_format($value*100/$total_female[all_person],1).")<br>"; ?>
                </td>
            </tr>
            <tr>
                <td><strong>8. ประวัติการมีญาติป่วยเป็นมะเร็งท่อน้ำดี</strong><br>&emsp;มี<br>&emsp;ไม่มี<br>&emsp;รวม</td>
                 <td align="right">
                    <br><?php  $r = array($total[no_f1v9],$total[yes_f1v9],$total[total_f1v9]);
                    foreach($r as $value) echo number_format($value)."&emsp;"."(".number_format($value*100/$total[all_person],2).")<br>"; ?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_hcode[no_f1v9],$total_hcode[yes_f1v9],$total_hcode[total_f1v9]);
                     foreach($r as $value)  echo number_format($value)."&emsp;"."(".number_format($value*100/$total_hcode[all_person],1).")<br>"; ?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_male[no_f1v9],$total_male[yes_f1v9],$total_male[total_f1v9]);
                     foreach($r as $value) echo number_format($value)."&emsp;"."(".number_format($value*100/$total_male[all_person],1).")<br>"; ?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_female[no_f1v9],$total_female[yes_f1v9],$total_female[total_f1v9]);
                     foreach($r as $value)  echo number_format($value)."&emsp;"."(".number_format($value*100/$total_female[all_person],1).")<br>";?>
                </td>
               </tr>
            <tr>
                <td><strong>ความสัมพันธ์</strong><br>&emsp;ปู่ ย่า<br>&emsp;ตา ยาย<br>&emsp;ลุง ป้า<br>&emsp;น้า อา<br>&emsp;พ่อ แม่<br>&emsp;ลูกชาย ลูกสาว<br>&emsp;พี่ชายน้องชาย พี่สาวน้องสาว<br>&emsp;หลานๆ<br>&emsp;คู่สมรส<br>&emsp;รวม (ความสัมพันธ์อย่างใดอย่างหนึ่ง)</td>
                <td align="right">
                    <br><?php  $r = array($total[f1v9a1b1],$total[f1v9a1b2],$total[f1v9a1b3],$total[f1v9a1b4],$total[f1v9a1b5],$total[f1v9a1b6],$total[f1v9a1b7],$total[f1v9a1b8],$total[f1v9a1b9]);
                    foreach($r as $value) echo number_format($value)."&emsp;"."(".number_format($value*100/$total[all_person],2).")<br>"; echo number_format($relation[person])."&emsp;"."(".number_format($relation[person]*100/$total[all_person],2).")"; ?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_hcode[f1v9a1b1],$total_hcode[f1v9a1b2],$total_hcode[f1v9a1b3],$total_hcode[f1v9a1b4],$total_hcode[f1v9a1b5],$total_hcode[f1v9a1b6],$total_hcode[f1v9a1b7],$total_hcode[f1v9a1b8],$total_hcode[f1v9a1b9]);
                    foreach($r as $value)
                        echo number_format($value)."&emsp;"."(".number_format($value*100/$total_hcode[all_person],1).")<br>"; 
                        echo number_format(sizeof($relationHos))."&emsp;"."(".number_format(sizeof($relationHos)*100/$total_hcode[all_person],1).")"; ?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_male[f1v9a1b1],$total_male[f1v9a1b2],$total_male[f1v9a1b3],$total_male[f1v9a1b4],$total_male[f1v9a1b5],$total_male[f1v9a1b6],$total_male[f1v9a1b7],$total_male[f1v9a1b8],$total_male[f1v9a1b9]);
                    foreach($r as $value) 
                        echo number_format($value)."&emsp;"."(".number_format($value*100/$total_male[all_person],1).")<br>";
                        echo number_format(sizeof($relationMale))."&emsp;"."(".number_format(sizeof($relationMale)*100/$total_hcode[all_person],1).")";?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_female[f1v9a1b1],$total_female[f1v9a1b2],$total_female[f1v9a1b3],$total_female[f1v9a1b4],$total_female[f1v9a1b5],$total_female[f1v9a1b6],$total_female[f1v9a1b7],$total_female[f1v9a1b8],$total_female[f1v9a1b9]);
                    foreach($r as  $value) 
                        echo number_format($value)."&emsp;"."(".number_format($value*100/$total_female[all_person],1).")<br>"; 
                        echo number_format(sizeof($relationFemale))."&emsp;"."(".number_format(sizeof($relationFemale)*100/$total_hcode[all_person],1).")"; ?>
                </td>
              </tr>
            <tr>
                <td><strong>9. ประวัติการสูบบุหรี่</strong><br>&emsp;ไม่สูบ<br>&emsp;สูบ/เคยสูบ<br>&emsp;รวม</td>
                 <td align="right">
                    <br><?php  $r = array($total[no_f1v10],$total[yes_f1v10],$total[total_f1v10]);
                    foreach($r as $value) echo number_format($value)."&emsp;"."(".number_format($value*100/$total[all_person],2).")<br>"; ?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_hcode[no_f1v10],$total_hcode[yes_f1v10],$total_hcode[total_f1v10]);
                    foreach($r as $value) echo number_format($value)."&emsp;"."(".number_format($value*100/$total_hcode[all_person],1).")<br>"; ?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_male[no_f1v10],$total_male[yes_f1v10],$total_male[total_f1v10]);
                    foreach($r as $value) echo number_format($value)."&emsp;"."(".number_format($value*100/$total_male[all_person],1).")<br>";  ?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_female[no_f1v10],$total_female[yes_f1v10],$total_female[total_f1v10]);
                    foreach($r as $value) echo number_format($value)."&emsp;"."(".number_format($value*100/$total_female[all_person],1).")<br>"; ?>
                </td>
              </tr>
            <tr>
                 <td><strong>10. ประวัติการดื่มเครื่องดื่มแอลกอฮอล์</strong><br>&emsp;ไม่ดื่ม<br>&emsp;ดื่ม/เคยดื่ม<br>&emsp;รวม</td>
                <td align="right">
                    <br><?php  $r = array($total[no_f1v11],$total[yes_f1v11],$total[total_f1v11]);
                    foreach($r as $value) echo number_format($value)."&emsp;"."(".number_format($value*100/$total[all_person],2).")<br>"; ?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_hcode[no_f1v11],$total_hcode[yes_f1v11],$total_hcode[total_f1v11]);
                    foreach($r as $value)  echo number_format($value)."&emsp;"."(".number_format($value*100/$total_hcode[all_person],1).")<br>";  ?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_male[no_f1v11],$total_male[yes_f1v11],$total_male[total_f1v11]);
                    foreach($r as $value)  echo number_format($value)."&emsp;"."(".number_format($value*100/$total_male[all_person],1).")<br>";  ?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_female[no_f1v11],$total_female[yes_f1v11],$total_female[total_f1v11]);
                    foreach($r as $value)   echo number_format($value)."&emsp;"."(".number_format($value*100/$total_female[all_person],1).")<br>";  ?>
                </td>
                </tr>
            <tr>
                <td><strong>11. คุณเป็นโรคพิษสุราเรื้อรังหรือไม่</strong><br>&emsp;ไม่เป็น<br>&emsp;เป็น/เคยเป็น<br>&emsp;รวม</td>
                <td align="right">
                    <br><?php  $r = array($total[no_f1v12],$total[yes_f1v12],$total[total_f1v12]);
                    foreach($r as $value) echo number_format($value)."&emsp;"."(".number_format($value*100/$total[all_person],2).")<br>"; ?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_hcode[no_f1v12],$total_hcode[yes_f1v12],$total_hcode[total_f1v12]);
                    foreach($r as $value)  echo number_format($value)."&emsp;"."(".number_format($value*100/$total_hcode[all_person],1).")<br>"; ?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_male[no_f1v12],$total_male[yes_f1v12],$total_male[total_f1v12]);
                    foreach($r as $value)  echo number_format($value)."&emsp;"."(".number_format($value*100/$total_male[all_person],1).")<br>";  ?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_female[no_f1v12],$total_female[yes_f1v12],$total_female[total_f1v12]);
                    foreach($r as $value)  echo number_format($value)."&emsp;"."(".number_format($value*100/$total_female[all_person],1).")<br>";  ?>
                </td>
            </tr>
            <tr>
                <td><strong>12. ประวัติการรับประทานปลาน้ำจืดที่มีเกล็ดดิบๆสุกๆ หรือปลาร้าไม่ต้มสุก</strong><br>&emsp;ไม่เคย<br>&emsp;เคย<br>&emsp;รวม</td>
                <td align="right">
                    <br><?php  $r = array($total[no_f1v13],$total[yes_f1v13],$total[total_f1v13]);
                    foreach($r as $value) echo number_format($value)."&emsp;"."(".number_format($value*100/$total[all_person],2).")<br>"; ?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_hcode[no_f1v13],$total_hcode[yes_f1v13],$total_hcode[total_f1v13]);
                    foreach($r as $value)  echo number_format($value)."&emsp;"."(".number_format($value*100/$total_hcode[all_person],1).")<br>";  ?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_male[no_f1v13],$total_male[yes_f1v13],$total_male[total_f1v13]);
                    foreach($r as $value)  echo number_format($value)."&emsp;"."(".number_format($value*100/$total_male[all_person],1).")<br>"; ?>
                </td>
                <td align="right">                   
                    <br><?php  $r=null; $r = array($total_female[no_f1v13],$total_female[yes_f1v13],$total_female[total_f1v13]);
                    foreach($r as $value) echo number_format($value)."&emsp;"."(".number_format($value*100/$total_female[all_person],1).")<br>";  ?>
                </td>
               </tr>
            <tr>
                <td><strong>13. คุณเคยได้รับการวินิจฉัยจากแพทย์ว่าเป็นโรคใดบ้างต่อไปนี้</strong><br>&emsp;ไม่เป็น<br>&emsp;ตับอักเสบ บี<br>&emsp;ตับอักเสบ ซี<br>&emsp;เบาหวาน<br>&emsp;อื่นๆ<br>&emsp;รวม (โรคอย่างใดอย่างหนึ่ง)</td>
                <td align="right">
                    <br><?php  $r = array($total[f1v14a0],$total[f1v14a1],$total[f1v14a2],$total[f1v14a3],$total[f1v14a4]);
                    foreach($r as $value) echo number_format($value)."&emsp;"."(".number_format($value*100/$total[all_person],2).")<br>"; echo number_format($f1v14[person])."&emsp;"."(".number_format($f1v14[person]*100/$total[all_person],2).")"; ?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_hcode[f1v14a0],$total_hcode[f1v14a1],$total_hcode[f1v14a2],$total_hcode[f1v14a3],$total_hcode[f1v14a4]);
                    foreach($r as $value) 
                        echo number_format($value)."&emsp;"."(".number_format($value*100/$total_hcode[all_person],1).")<br>"; 
                        echo number_format($f1v14hos[diagnose])."&emsp;"."(".number_format($f1v14hos[diagnose]*100/$total_hcode[all_person],1).")";  ?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_male[f1v14a0],$total_male[f1v14a1],$total_male[f1v14a2],$total_male[f1v14a3],$total_male[f1v14a4]);
                    foreach($r as $value) 
                        echo number_format($value)."&emsp;"."(".number_format($value*100/$total_male[all_person],1).")<br>"; 
                        echo number_format($f1v14hosmale[diagnose])."&emsp;"."(".number_format($f1v14hosmale[diagnose]*100/$total_male[all_person],1).")"; ?>
                </td>
                <td align="right">
                    <br><?php  $r=null; $r = array($total_female[f1v14a0],$total_female[f1v14a1],$total_female[f1v14a2],$total_female[f1v14a3],$total_female[f1v14a4]);
                    foreach($r as $value) 
                        echo number_format($value)."&emsp;"."(".number_format($value*100/$total_female[all_person],1).")<br>"; 
                        echo number_format($f1v14hosfemale[diagnose])."&emsp;"."(".number_format($f1v14hosfemale[diagnose]*100/$total_female[all_person],1).")"; ?>
                </td>
            </tr>
        </table>
</div>
<?php
$this->registerJs("
        $('tr').on('click','#patient-drop',function(){
        $('.trdrop').remove();
        var doctorcode = $(this).children('#doctorcode').val();
        var startDate = $('#inputStartDate').val();
        var endDate = $('#inputEndDate').val();
        var hospitalcode = $('#inputHospital').val();
        var doctorname = $(this).children('#doctorname').val();
        var state = $(this).children('#state').val();
        var gender = $(this).children('#gender').val();
        var data = $(this).children('#data').val();
        var cca02 = $(this).children('#cca02').val();
        var zonedata = $(this).children('#zonedata').val();
    //    console.log('state : '+state+' |gender : '+gender +' |data : '+data + ' |cca02 : '+cca02);
         $(this).parent().parent().after(`
            <tr class=\'trdrop\'> 
                <td style=\'background:#81F7F3;\' colspan=\'8\'>
                    <div style=\'padding-right:15px;\'>
                        <button class=\'btn btn-primary pull-right\' onclick=\'closeTr(this);\' >
                            <span class=\'glyphicon glyphicon-remove\'></span> <strong>Close</strong>
                        </button>
                    </div><br/><br/>
                    <div id=\'patient-show\'></div>
                </td> 
            </tr>`
         );
            $('#patient-show').html('<div style=\'text-align:center;color:#fff;\'><i class=\"fa fa-circle-o-notch fa-spin fa-fw fa-3x\"></i></div>');
            $.ajax({
                url:'" . Url::to('/usfinding/default/summary-drilldown/') . "',
                method:'GET',
                data:{
                    startDate : startDate,
                    endDate : endDate,
                    hospitalcode : hospitalcode,
                    doctorcode : doctorcode,
                    doctorname : doctorname,
                    state : state,
                    gender : gender,
                    data : data,
                    cca02 : cca02,
                    zonedata : zonedata
                },
                type:'HTML',
                success:function(result){
                   $('#patient-show').empty();
                   $('#patient-show').html(result);
                }
            });
        });
     function closeTr(t){
        $(t).parent().parent().parent().remove();
     }
");
?>
<?php
$this->registerJs("
    $('#report-excel').click(function(e){
    var dataTable = $('#data-report-table');
    var headerName = ('<tr>< th colspan=\"5\">$title_cca01</th></tr>').replace(/< /g,'<');
        dataTable.prepend(headerName);
        this.download='Report-CCA01.xls' 
        ExcellentExport.excel(this, \"data-report-table\");
    });
");
?>

