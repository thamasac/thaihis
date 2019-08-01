<?php
use Yii;
use yii\helpers\Url;
use miloschuman\highcharts\Highcharts;
use yii\web\JsExpression;
$user_Site_Code = \Yii::$app->user->identity->userProfile->sitecode;
        function showChart($title,$render,$arr){
               return Highcharts::widget([
                 'setupOptions'=>[
                    'lang' => [
                        'thousandsSep' => ','
                            ],
                        ],
                'options' => [
                    'title' => ['text' => 'CASCAP : '.$title],
                    'chart' => [
                    'renderTo' => $render
                    ],
                   
                    'plotOptions' => [
                        'pie' => [
                            'cursor' => 'pointer',
                              'dataLabels' => [
                                'enabled' => true,
                                'format' => new JsExpression("'<b>{point.name}</b>: {point.y:,.0f}/{point.percentage:.1f} %'"),]
                        ],
                    ],
                    'series' => [
                        [
                            'type' => 'pie',
                            'name' => 'Elements',
                            'data' => $arr,  // $arr =array(['a',1], ['b',10],['c',5],['d',3],['e',2]);
                        ]
                    ],
                ],
            ]);
            }
            function checkLiCense($user_Site_Code,$hospitalCode){
                //\appxq\sdii\utils\VarDumper::dump($user_Site_Code."|-".$hospitalCode,0);
                if ($user_Site_Code!=$hospitalCode){
                    return TRUE;}
                else 
                    return FALSE;
            }
?>

<div class="panel-success" >
    <div class="container">
        <label style="font-size:30px"><strong><?= $header ?></strong></label>
        <ul class="nav nav-tabs span2 clearfix"></ul><br>
        <label style="font-size:20px">ตารางแสดงจำนวนการตรวจอัลตร้าซาวด์ตามแพทย์ผู้ตรวจ</label>
        <div class="row">
            <div class="col-md-10">
                 <p id="doctorChart" align="center"></p> 
            </div>
            <div class="pull-right">
                <div class="pull-right">
                    <button id="export-picture" onclick='pictureChart("#doctorChart")' type="button" class="btn btn-info" data-toggle="modal" data-target="#myModal" title="Export Chart to Picture">Chart to Picture</button>
                </div>
            </div>
        </div>
        <table class="table table-bordered" style="font-size:16px;">
            <tr>
                <td><strong><center>รายชื่อแพทย์ ที่ทำการตรวจอัลตราซาวด์ในโรงพยาบาล</center></strong></td>
                <td><strong><center>จำนวนที่ตรวจคนไข้</center></strong></td>
                <td><strong><center>%</center></strong></td>
            </tr>
        <?php $sum = 0; 
        foreach ($doctorAll as $value){
            $sum+=$value['numpatient'];
        }

        foreach ($doctorAll as $value) { ?>
            <tr>
                <td><?= $value['doctorfullname'] ?></td>
                <td align="right">
                    <?php if (checkLiCense($user_Site_Code,$hospitalCode))
                            echo number_format($value['numpatient']); 
                    else { ?>
                    <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($value['numpatient']) ?>
                        <input id="doctorcode" type="hidden" value="<?= $value['doctorcode'] ?>">
                        <input id="doctorname" type="hidden" value="<?= $value['doctorfullname'] ?>">
                        <input id="zonedata" type="hidden" value="<?= $zone ?>">
                        <input id="state" type="hidden" value="<?php if ($summaryZone=="SummaryZone") echo doctorzone; else echo doctor; ?>"> </a>
                    <?php } ?>
                            
                </td>
                <td align="right"><?= number_format($value['numpatient']*100/$sum,1)." %" ?></td>
            </tr>
        <?php } ?>
             <tr>
                <td><strong><center>รวมทั้งหมด</center></strong></td>
                <td align="right"><strong><?= number_format($sum) ?></strong></td>
                <td align="right"><strong>100 %</strong></td>
            </tr>
            <?php  $x=0;
            foreach ($doctorAll as $value) {
                $arr[$x][] = $value['doctorfullname'];
                 $arr[$x][] = intval($value['numpatient']);
                 $x++;
                }
                showChart('Diagnosed by Doctor','doctorChart',$arr); ?>
        </table>    
<!--||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||| -->
         <ul class="nav nav-tabs span2 clearfix"></ul><br>
        <label style="font-size:20px">ตารางแสดงจำนวนกลุ่มเสี่ยงที่เข้าร่วมการคัดกรอง จำแนกตามเพศ</label>
        <div class="row">
            <div class="col-md-10">
                 <p id="genderChart" align="center"></p> 
            </div>
            <div class="pull-right">
                <div class="pull-right">
                    <button id="export-picture" onclick='pictureChart("#genderChart")' type="button" class="btn btn-info" data-toggle="modal" data-target="#myModal" title="Export Chart to Picture">Chart to Picture</button>
                </div>
            </div>
        </div>
        <table class="table table-bordered" style="font-size:16px;">
            <tr>
                <td align="center"><strong>เพศ</strong></td>
                <td align="center"><strong>จำนวน (คน)</strong></td>
                <td align="center"><strong>%</strong></td>
            </tr>
            <tr>
                <td>ชาย</td>
                <td align="right">
                    <?php if($genderAll['person_sex_male']==0 || checkLiCense($user_Site_Code,$hospitalCode)) 
                            echo number_format($genderAll['person_sex_male']); 
                          else { ?>
                    <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($genderAll['person_sex_male']) ?>
                    <input id="gender" type="hidden" value="in ('1')">
                    <input id="zonedata" type="hidden" value="<?= $zone ?>">
                    <input id="state" type="hidden" value="<?php if ($summaryZone=="SummaryZone") echo genderzone; else echo gender; ?>"></a>
                    <?php  } ?>
                </td>
                <td align="right"><?= number_format($genderAll['person_sex_male']*100/$genderAll['person'],1)." %" ?></td>
            </tr>
             <tr>
                <td>หญิง</td>
                <td align="right">
                     <?php if($genderAll['person_sex_female']==0 || checkLiCense($user_Site_Code,$hospitalCode)) 
                            echo number_format($genderAll['person_sex_female']); 
                          else { ?>
                    <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($genderAll['person_sex_female']) ?>
                        <input id="gender" type="hidden" value="in ('2')">
                        <input id="zonedata" type="hidden" value="<?= $zone ?>">
                        <input id="state" type="hidden" value="<?php if ($summaryZone=="SummaryZone") echo genderzone; else echo gender; ?>"></a>
                    <?php  } ?>
                </td>
                <td align="right"><?= number_format($genderAll['person_sex_female']*100/$genderAll['person'],1)." %" ?></td>
            </tr>
             <tr>
                <td>ไม่ได้ระบุเพศ</td>
                <td align="right">
                    <?php if(($genderAll['person']-($genderAll['person_sex_female']+$genderAll['person_sex_male']))==0 || checkLiCense($user_Site_Code,$hospitalCode)) 
                            echo ($genderAll['person']-($genderAll['person_sex_female']+$genderAll['person_sex_male'])); 
                          else { ?>
                    <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= $genderAll['person']-($genderAll['person_sex_female']+$genderAll['person_sex_male']) ?>
                        <input id="gender" type="hidden" value="not in ('1','2')">
                        <input id="zonedata" type="hidden" value="<?= $zone ?>">
                        <input id="state" type="hidden" value="<?php if ($summaryZone=="SummaryZone") echo genderzone; else echo gender; ?>"></a>
                    <?php  } ?>
                </td>
                <td align="right"><?= number_format(($genderAll['person']-($genderAll['person_sex_female']+$genderAll['person_sex_male']))*100/$genderAll['person'],1)." %" ?></td>
            </tr>
            <tr>
                <td><strong>รวม</strong></td>
                <td align="right"><strong><?= number_format($genderAll['person']) ?></strong></td>
                <td align="right"><strong>100 %</strong></td>
            </tr>
             <?php  $arrcca02 = array(['ชาย', intval($genderAll['person_sex_male'])],['หญิง', intval($genderAll['person_sex_female'])],['ไม่ระบุเพศ', intval($genderAll['person']-($genderAll['person_sex_male']+$genderAll['person_sex_female']))]);
                    showChart("Gender","genderChart",$arrcca02); ?>
        </table>    
<!--||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||| -->
        <ul class="nav nav-tabs span2 clearfix"></ul><br>
        <label style="font-size:20px">ตารางแสดงผลการตรวจอัลตร้าซาวด์</label>
        <div class="row">
            <div class="col-md-10">
                 <p id="pdf" align="center"></p> 
            </div>
            <div class="pull-right">
                <div class="pull-right">
                    <button id="export-picture" onclick='pictureChart("#pdf")' type="button" class="btn btn-info" data-toggle="modal" data-target="#myModal" title="Export Chart to Picture">Chart to Picture</button>
                </div>
            </div>
        </div>
        <table class="table table-bordered" style="font-size:16px;">
            <tr>
                <td><strong><center>ผลการตรวจอัลตราซาวด์</center></strong></td>
                <td><strong><center>จำนวน (ครั้ง)</center></strong></td>
                <td><strong><center>%</center></strong></td>
            </tr>
            <tr>
                <td><strong>Parenchymal ECHO (Abnormal)</strong></td>
                <td align="right">
                     <?php if($parenchymal['count']==0 || checkLiCense($user_Site_Code,$hospitalCode)) 
                            echo number_format($parenchymal['count']); 
                          else { ?>
                    <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($parenchymal['count']) ?>
                        <input id="data" type="hidden" value="f2v2a1">
                        <input id="cca02" type="hidden" value="1">
                        <input id="zonedata" type="hidden" value="<?= $zone ?>">
                        <input id="state" type="hidden" value="<?php if ($summaryZone=="SummaryZone") echo cca02zone; else echo cca02; ?>"></a>
                    <?php  } ?>
                </td>
                <td align="right"><strong><?= number_format($parenchymal['count']*100/$genderAll['person'],1)." %" ?></strong></td>
            </tr>
            <tr>
                <td><strong>PDF</strong></td>
                <td align="right">
                     <?php if((($pdf1['count']+$pdf2['count']+$pdf3['count'])==0) || checkLiCense($user_Site_Code,$hospitalCode)) 
                            echo number_format($pdf1['count']+$pdf2['count']+$pdf3['count']); 
                          else { ?>
                    <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($pdf1['count']+$pdf2['count']+$pdf3['count']) ?>
                        <input id="data" type="hidden" value="f2v2a1b2">
                        <input id="cca02" type="hidden" value="1,2,3">
                        <input id="zonedata" type="hidden" value="<?= $zone ?>">
                        <input id="state" type="hidden" value="<?php if ($summaryZone=="SummaryZone") echo cca02zone; else echo cca02; ?>"></a>
                    <?php  } ?>
                </td>
                <td align="right"><strong><?= number_format(($pdf1['count']+$pdf2['count']+$pdf3['count'])*100/$genderAll['person'],1)." %"  ?></td>
            </tr>
            <tr>
                <td>&emsp;PDF1</td>
                <td align="right">
                     <?php if(($pdf1['count']==0) || checkLiCense($user_Site_Code,$hospitalCode)) 
                            echo number_format($pdf1['count']); 
                          else { ?>
                    <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($pdf1['count']) ?>
                        <input id="data" type="hidden" value="f2v2a1b2">
                        <input id="cca02" type="hidden" value="1">
                        <input id="zonedata" type="hidden" value="<?= $zone ?>">
                        <input id="state" type="hidden" value="<?php if ($summaryZone=="SummaryZone") echo cca02zone; else echo cca02; ?>"></a>
                    <?php  } ?>
                </td>
                <td align="right"><?= number_format($pdf1['count']*100/$genderAll['person'],1)." %"  ?></td>
            </tr>
             <tr>
                <td>&emsp;PDF2</td>
                <td align="right">
                    <?php if(($pdf2['count']==0) || checkLiCense($user_Site_Code,$hospitalCode)) 
                            echo number_format($pdf2['count']); 
                          else { ?>
                    <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($pdf2['count']) ?>
                        <input id="data" type="hidden" value="f2v2a1b2">
                        <input id="cca02" type="hidden" value="2">
                        <input id="zonedata" type="hidden" value="<?= $zone ?>">
                        <input id="state" type="hidden" value="<?php if ($summaryZone=="SummaryZone") echo cca02zone; else echo cca02; ?>"></a>
                    <?php  } ?>
                </td>
                <td align="right"><?= number_format($pdf2['count']*100/$genderAll['person'],1)." %"   ?></td>
            </tr>
             <tr>
                <td>&emsp;PDF3</td>
                <td align="right">
                <?php if(($pdf3['count']==0)|| checkLiCense($user_Site_Code,$hospitalCode)) 
                            echo number_format($pdf3['count']); 
                          else { ?>
                    <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($pdf3['count']) ?>
                        <input id="data" type="hidden" value="f2v2a1b2">
                        <input id="cca02" type="hidden" value="3">
                        <input id="zonedata" type="hidden" value="<?= $zone ?>">
                        <input id="state" type="hidden" value="<?php if ($summaryZone=="SummaryZone") echo cca02zone; else echo cca02; ?>"></a>
                <?php  } ?>
                </td>
                <td align="right"><?= number_format($pdf3['count']*100/$genderAll['person'],1)." %"   ?></td>
                <?php $otherdiagnose = $genderAll['person']-($pdf1['count']+$pdf2['count']+$pdf3['count']);
                $arrcca02 = array(['PDF1', intval($pdf1['count'])],['PDF2', intval($pdf2['count'])],['PDF3', intval($pdf3['count'])], ['Other Diagnosis', intval($otherdiagnose)]); 
                showChart("PDF","pdf",$arrcca02);?>
            </tr>
    </table>
       <div class="row">
            <div class="col-md-10">
                 <p id="fatty" align="center"></p> 
            </div>
            <div class="pull-right">
                <div class="pull-right">
                    <button id="export-picture" onclick='pictureChart("#fatty")' type="button" class="btn btn-info" data-toggle="modal" data-target="#myModal" title="Export Chart to Picture">Chart to Picture</button>
                </div>
            </div>
        </div>
    <table class="table table-bordered" style="font-size:16px;">
            <tr>
                <td><strong><center>ผลการตรวจอัลตราซาวด์</center></strong></td>
                <td><strong><center>จำนวน (ครั้ง)</center></strong></td>
                <td><strong><center>%</center></strong></td>
            </tr>
            <tr>
                <td><strong>Fatty Liver</strong></td>
                <td align="right">
                    <?php if((($fattyLiverMild['count'] + $fattyLiverModerate['count'] + $fattyLiverSevere['count'])==0)|| checkLiCense($user_Site_Code,$hospitalCode)) 
                            echo number_format($fattyLiverMild['count'] + $fattyLiverModerate['count'] + $fattyLiverSevere['count']); 
                          else { ?>
                    <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($fattyLiverMild['count'] + $fattyLiverModerate['count'] + $fattyLiverSevere['count']) ?>
                        <input id="data" type="hidden" value="f2v2a1b1">
                        <input id="cca02" type="hidden" value="1,2,3">
                        <input id="zonedata" type="hidden" value="<?= $zone ?>">
                        <input id="state" type="hidden" value="<?php if ($summaryZone=="SummaryZone") echo cca02zone; else echo cca02; ?>"></a>
                    <?php  } ?>
                </td>
                <td align="right"><strong><?= number_format(($fattyLiverMild['count'] + $fattyLiverModerate['count'] + $fattyLiverSevere['count'])*100/$genderAll['person'],1)." %" ?></strong></td>
            </tr>
            <tr>
                <td>&emsp;1. Mild Fatty Liver</td>
                <td align="right">
                    <?php if(($fattyLiverMild['count']==0)|| checkLiCense($user_Site_Code,$hospitalCode)) 
                            echo number_format($fattyLiverMild['count']); 
                          else { ?>
                    <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($fattyLiverMild['count']) ?>
                        <input id="data" type="hidden" value="f2v2a1b1">
                        <input id="cca02" type="hidden" value="1">
                        <input id="zonedata" type="hidden" value="<?= $zone ?>">
                        <input id="state" type="hidden" value="<?php if ($summaryZone=="SummaryZone") echo cca02zone; else echo cca02; ?>"></a>
                    <?php  } ?>
                </td>
                <td align="right"><?= number_format($fattyLiverMild['count']*100/$genderAll['person'],1)." %" ?></td>
            </tr>
             <tr>
                <td>&emsp;2. Moderate Fatty Liver</td>
                <td align="right">
                    <?php if(($fattyLiverModerate['count']==0)|| checkLiCense($user_Site_Code,$hospitalCode)) 
                            echo number_format($fattyLiverModerate['count']); 
                          else { ?>
                    <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($fattyLiverModerate['count']) ?>
                        <input id="data" type="hidden" value="f2v2a1b1">
                        <input id="cca02" type="hidden" value="2">
                        <input id="zonedata" type="hidden" value="<?= $zone ?>">
                        <input id="state" type="hidden" value="<?php if ($summaryZone=="SummaryZone") echo cca02zone; else echo cca02; ?>"></a>
                    <?php  } ?>
                </td>
                <td align="right"><?= number_format($fattyLiverModerate['count']*100/$genderAll['person'],1)." %" ?></td>
            </tr>
             <tr>
                <td>&emsp;3. Severe Fatty Liver</td>
                <td align="right">
                    <?php if(($fattyLiverSevere['count']==0)|| checkLiCense($user_Site_Code,$hospitalCode)) 
                            echo number_format($fattyLiverSevere['count']); 
                          else { ?>
                    <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($fattyLiverSevere['count']) ?>
                        <input id="data" type="hidden" value="f2v2a1b1">
                        <input id="cca02" type="hidden" value="3">
                        <input id="zonedata" type="hidden" value="<?= $zone ?>">
                        <input id="state" type="hidden" value="<?php if ($summaryZone=="SummaryZone") echo cca02zone; else echo cca02; ?>"></a>
                    <?php  } ?>
                </td>
                <td align="right"><?= number_format($fattyLiverSevere['count']*100/$genderAll['person'],1)." %" ?></td>
            </tr>
             <tr>
                <td><strong>Cirrhosis</strong></td>
                <td align="right">
                    <?php if(($cirrhosis['count']==0)|| checkLiCense($user_Site_Code,$hospitalCode)) 
                            echo number_format($cirrhosis['count']); 
                          else { ?>
                    <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($cirrhosis['count']) ?>
                        <input id="data" type="hidden" value="f2v2a1b3">
                        <input id="cca02" type="hidden" value="1">
                        <input id="zonedata" type="hidden" value="<?= $zone ?>">
                        <input id="state" type="hidden" value="<?php if ($summaryZone=="SummaryZone") echo cca02zone; else echo cca02; ?>"></a>
                    <?php  } ?>
                </td>
                <td align="right"><strong><?= number_format($cirrhosis['count']*100/$genderAll['person'],1)." %"  ?></strong></td>
                 <?php  $otherdiagnose = $genderAll['person']-($fattyLiverMild['count']+$fattyLiverModerate['count']+$fattyLiverSevere['count']+$cirrhosis['count']);
                 $arrcca02 = array(['Mild Fatty Liver', intval($fattyLiverMild['count'])],['Moderate Fatty Liver', intval($fattyLiverModerate['count'])],['Severe Fatty Liver', intval($fattyLiverSevere['count'])],['Cirrhosis', intval($cirrhosis['count'])], ['Other Diagnosis', intval($otherdiagnose)]);
                    showChart("Fatty Liver","fatty",$arrcca02); ?>
            </tr>
    </table>
         <div class="row">
            <div class="col-md-10">
                 <p id="liver" align="center"></p> 
            </div>
            <div class="pull-right">
                <div class="pull-right">
                    <button id="export-picture" onclick='pictureChart("#liver")' type="button" class="btn btn-info" data-toggle="modal" data-target="#myModal" title="Export Chart to Picture">Chart to Picture</button>
                </div>
            </div>
        </div>
    <table class="table table-bordered" style="font-size:16px;">
            <tr>
                <td><strong><center>ผลการตรวจอัลตราซาวด์</center></strong></td>
                <td><strong><center>จำนวน (ครั้ง)</center></strong></td>
                <td><strong><center>%</center></strong></td>
            </tr>
            <tr>
                <td><strong>Liver mass</strong></td>
                <td align="right">
                    <?php if((($liverMassSingle['count'] + $liverMassMultiple['count'])==0)|| checkLiCense($user_Site_Code,$hospitalCode)) 
                            echo number_format($liverMassSingle['count'] + $liverMassMultiple['count']); 
                          else { ?>
                    <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($liverMassSingle['count'] + $liverMassMultiple['count']) ?>
                        <input id="data" type="hidden" value="f2v2a2">
                        <input id="cca02" type="hidden" value="1,2">
                        <input id="zonedata" type="hidden" value="<?= $zone ?>">
                        <input id="state" type="hidden" value="<?php if ($summaryZone=="SummaryZone") echo cca02zone; else echo cca02; ?>"></a>
                    <?php  } ?>
                </td>
                <td align="right"><strong><?= number_format(($liverMassSingle['count'] + $liverMassMultiple['count'])*100/$genderAll['person'],1)." %"  ?></strong></td>
            </tr>
            <tr>
                <td>&emsp;1. Single Mass</td>
                <td align="right">
                     <?php if(($liverMassSingle['count']==0)|| checkLiCense($user_Site_Code,$hospitalCode)) 
                            echo number_format($liverMassSingle['count']); 
                          else { ?>
                    <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($liverMassSingle['count']) ?>
                         <input id="data" type="hidden" value="f2v2a2">
                        <input id="cca02" type="hidden" value="1">
                        <input id="zonedata" type="hidden" value="<?= $zone ?>">
                        <input id="state" type="hidden" value="<?php if ($summaryZone=="SummaryZone") echo cca02zone; else echo cca02; ?>"></a>
                    <?php  } ?>
                </td>
                <td align="right"><?= number_format($liverMassSingle['count']*100/$genderAll['person'],1)." %"  ?></td>
            </tr>
             <tr>
                <td>&emsp;2. Multiple Mass</td>
                <td align="right">
                     <?php if((($liverMassMultiple['count'])==0) || checkLiCense($user_Site_Code,$hospitalCode))
                            echo number_format($liverMassMultiple['count']); 
                          else { ?>
                    <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($liverMassMultiple['count']) ?>
                         <input id="data" type="hidden" value="f2v2a2">
                        <input id="cca02" type="hidden" value="2">
                        <input id="zonedata" type="hidden" value="<?= $zone ?>">
                        <input id="state" type="hidden" value="<?php if ($summaryZone=="SummaryZone") echo cca02zone; else echo cca02; ?>"></a>
                    <?php  } ?>
                </td>
                <td align="right"><?= number_format($liverMassMultiple['count']*100/$genderAll['person'],1)." %"  ?></td>
                <?php $otherdiagnose = $genderAll['person']-($liverMassSingle['count']+$liverMassMultiple['count']);  
                $arrcca02 = array(['Single Mass', intval($liverMassSingle['count'])],['Multiple Mass', intval($liverMassMultiple['count'])],['Other Diagnosis', intval($otherdiagnose)]);
                    showChart("Liver mass","liver",$arrcca02); ?>
            </tr>
    </table>
        <div class="row">
            <div class="col-md-10">
                 <p id="gallbladder" align="center"></p> 
            </div>
            <div class="pull-right">
                <div class="pull-right">
                    <button id="export-picture" onclick='pictureChart("#gallbladder")' type="button" class="btn btn-info" data-toggle="modal" data-target="#myModal" title="Export Chart to Picture">Chart to Picture</button>
                </div>
            </div>
        </div>
    <table class="table table-bordered" style="font-size:16px;">
            <tr>
                <td><strong><center>ผลการตรวจอัลตราซาวด์</center></strong></td>
                <td><strong><center>จำนวน (ครั้ง)</center></strong></td>
                <td><strong><center>%</center></strong></td>
            </tr>
             <tr>
                <td><strong>Gallbladder</strong></td>
                <td align="right">
                     <?php if((($gallWall['count'] + $gallStone['count'] + $gallPost['count'])==0) || checkLiCense($user_Site_Code,$hospitalCode))
                            echo number_format($gallWall['count'] + $gallStone['count'] + $gallPost['count']); 
                          else { ?>
                    <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($gallWall['count'] + $gallStone['count'] + $gallPost['count']) ?>
                         <input id="data" type="hidden" value="f2v3a2 OR f2v3a3 OR f2v3a4">
                        <input id="cca02" type="hidden" value="1">
                        <input id="zonedata" type="hidden" value="<?= $zone ?>">
                        <input id="state" type="hidden" value="<?php if ($summaryZone=="SummaryZone") echo cca02zone; else echo cca02; ?>"></a>
                    <?php  } ?>
                </td>
                <td align="right"><strong><?= number_format(($gallWall['count'] + $gallStone['count'] + $gallPost['count'])*100/$genderAll['person'],1)." %" ?></strong></td>
            </tr>
            <tr>
                <td>&emsp;1. Wall Thickening</td>
                <td align="right">
                    <?php if((($gallWall['count'])==0) || checkLiCense($user_Site_Code,$hospitalCode))
                            echo number_format($gallWall['count']); 
                          else { ?>
                    <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($gallWall['count']) ?>
                         <input id="data" type="hidden" value="f2v3a2">
                        <input id="cca02" type="hidden" value="1">
                        <input id="zonedata" type="hidden" value="<?= $zone ?>">
                        <input id="state" type="hidden" value="<?php if ($summaryZone=="SummaryZone") echo cca02zone; else echo cca02; ?>"></a>
                    <?php  } ?>
                </td>
                <td align="right"><?= number_format($gallWall['count']*100/$genderAll['person'],1)." %" ?></td>
            </tr>
             <tr>
                <td>&emsp;2. Gallstone</td>
                <td align="right">
                    <?php if((($gallStone['count'])==0) || checkLiCense($user_Site_Code,$hospitalCode))
                            echo number_format($gallStone['count']); 
                          else { ?>
                    <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($gallStone['count']) ?>
                         <input id="data" type="hidden" value="f2v3a3">
                        <input id="cca02" type="hidden" value="1">
                        <input id="zonedata" type="hidden" value="<?= $zone ?>">
                        <input id="state" type="hidden" value="<?php if ($summaryZone=="SummaryZone") echo cca02zone; else echo cca02; ?>"></a>
                    <?php  } ?>
                </td>
                <td align="right"><?= number_format($gallStone['count']*100/$genderAll['person'],1)." %" ?></td>
            </tr>
             <tr>
                <td>&emsp;3. Post Cholecystectomy</td>
                <td align="right">
                    <?php if((($gallPost['count'])==0) || checkLiCense($user_Site_Code,$hospitalCode))
                            echo number_format($gallPost['count']); 
                          else { ?>
                    <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($gallPost['count']) ?>
                         <input id="data" type="hidden" value="f2v3a4">
                        <input id="cca02" type="hidden" value="1">
                        <input id="zonedata" type="hidden" value="<?= $zone ?>">
                        <input id="state" type="hidden" value="<?php if ($summaryZone=="SummaryZone") echo cca02zone; else echo cca02; ?>"></a>
                    <?php  } ?>
                </td>
                <td align="right"><?= number_format($gallPost['count']*100/$genderAll['person'],1)." %" ?></td>
                <?php $otherdiagnose = $genderAll['person']-($gallWall['count']+$gallStone['count']+$gallPost['count']);  
                $arrcca02 = array(['Wall Thickening', intval($gallWall['count'])],['Gallstone', intval($gallStone['count'])],['Post Cholecystectomy', intval($gallPost['count'])],['Other Diagnosis', intval($otherdiagnose)]);
                    showChart("Gallbladder","gallbladder",$arrcca02); ?>
            </tr>
    </table>
         <div class="row">
            <div class="col-md-10">
                 <p id="dilated" align="center"></p> 
            </div>
            <div class="pull-right">
                <div class="pull-right">
                    <button id="export-picture" onclick='pictureChart("#dilated")' type="button" class="btn btn-info" data-toggle="modal" data-target="#myModal" title="Export Chart to Picture">Chart to Picture</button>
                </div>
            </div>
        </div>
    <table class="table table-bordered" style="font-size:16px;">
            <tr>
                <td><strong><center>ผลการตรวจอัลตราซาวด์</center></strong></td>
                <td><strong><center>จำนวน (ครั้ง)</center></strong></td>
                <td><strong><center>%</center></strong></td>
            </tr>
            <tr>
                <td><strong>Dilated Duct (อย่างใดอย่างหนึ่ง)</strong></td>
                <td align="right">
                    <?php if(($dilatedTotal[count]==0) || checkLiCense($user_Site_Code,$hospitalCode))
                            echo number_format($dilatedTotal[count]); 
                          else { ?>
                    <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($dilatedTotal[count]) ?>
                        <input id="data" type="hidden" value="f2v2a3b1 OR f2v2a3b2 OR f2v2a3b3">
                        <input id="cca02" type="hidden" value="1">
                        <input id="zonedata" type="hidden" value="<?= $zone ?>">
                        <input id="state" type="hidden" value="<?php if ($summaryZone=="SummaryZone") echo cca02zone; else echo cca02; ?>"></a>
                    <?php  } ?>
                </td>
                <td align="right"><strong><?= number_format($dilatedTotal[count]*100/$genderAll['person'],1)." %" ?></strong></td>
            </tr>
            <tr>
                <td>&emsp;1. Right Lobe</td>
                <td align="right">
                    <?php if((($dilatedRight['count'])==0) || checkLiCense($user_Site_Code,$hospitalCode))
                            echo number_format($dilatedRight['count']); 
                          else { ?>
                    <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($dilatedRight['count']) ?>
                        <input id="data" type="hidden" value="f2v2a3b1">
                        <input id="cca02" type="hidden" value="1">
                        <input id="zonedata" type="hidden" value="<?= $zone ?>">
                        <input id="state" type="hidden" value="<?php if ($summaryZone=="SummaryZone") echo cca02zone; else echo cca02; ?>"></a>
                    <?php  } ?>
                </td>
                <td align="right"><?= number_format($dilatedRight['count']*100/$genderAll['person'],1)." %" ?></td>
            </tr>
             <tr>
                <td>&emsp;2.  Left Lobe</td>
                <td align="right">
                    <?php if((($dilatedLeft['count'])==0) || checkLiCense($user_Site_Code,$hospitalCode))
                            echo number_format($dilatedLeft['count']); 
                          else { ?>
                    <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($dilatedLeft['count']) ?>
                        <input id="data" type="hidden" value="f2v2a3b2">
                        <input id="cca02" type="hidden" value="1">
                        <input id="zonedata" type="hidden" value="<?= $zone ?>">
                        <input id="state" type="hidden" value="<?php if ($summaryZone=="SummaryZone") echo cca02zone; else echo cca02; ?>"></a>
                    <?php  } ?>
                </td>
                <td align="right"><?= number_format($dilatedLeft['count']*100/$genderAll['person'],1)." %" ?></td>
            </tr>
             <tr>
                <td>&emsp;3. CBD</td>
                <td align="right">
                    <?php if((($dilatedCbd['count'])==0) || checkLiCense($user_Site_Code,$hospitalCode))
                            echo number_format($dilatedCbd['count']); 
                          else { ?>
                    <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($dilatedCbd['count']) ?>
                        <input id="data" type="hidden" value="f2v2a3b3">
                        <input id="cca02" type="hidden" value="1">
                        <input id="zonedata" type="hidden" value="<?= $zone ?>">
                        <input id="state" type="hidden" value="<?php if ($summaryZone=="SummaryZone") echo cca02zone; else echo cca02; ?>"></a>
                    <?php  } ?>
                </td>
                <td align="right"><?= number_format($dilatedCbd['count']*100/$genderAll['person'],1)." %" ?></td>
                 <?php $otherdiagnose = $genderAll['person']-($dilatedRight['count']+$dilatedLeft['count']+$dilatedCbd['count']);  
                 $arrcca02 = array(['Right Lobe', intval($dilatedRight['count'])],['Left Lobe', intval($dilatedLeft['count'])],['CBD', intval($dilatedCbd['count'])],['Other Diagnosis', intval($otherdiagnose)]);
                    showChart("Dilated Duct","dilated",$arrcca02); ?>
            </tr>
    </table>
         <div class="row">
            <div class="col-md-10">
                 <p id="otherfinding" align="center"></p> 
            </div>
            <div class="pull-right">
                <div class="pull-right">
                    <button id="export-picture" onclick='pictureChart("#otherfinding")' type="button" class="btn btn-info" data-toggle="modal" data-target="#myModal" title="Export Chart to Picture">Chart to Picture</button>
                </div>
            </div>
        </div>
    <table class="table table-bordered" style="font-size:16px;">
            <tr>
                <td><strong><center>ผลการตรวจอัลตราซาวด์</center></strong></td>
                <td><strong><center>จำนวน (ครั้ง)</center></strong></td>
                <td><strong><center>%</center></strong></td>
            </tr>
             <tr>
                <td><strong>Other Finding</strong></td>
                <td align="right">
                    <?php if((($ascites['count'] + $splenomegaly['count'] + $other['count'])==0) || checkLiCense($user_Site_Code,$hospitalCode))
                            echo number_format($ascites['count'] + $splenomegaly['count'] + $other['count']); 
                          else { ?>
                    <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($ascites['count'] + $splenomegaly['count'] + $other['count']) ?>
                        <input id="data" type="hidden" value="f2v5a1 OR f2v5a2 OR f2v5a3">
                        <input id="cca02" type="hidden" value="1">
                        <input id="zonedata" type="hidden" value="<?= $zone ?>">
                        <input id="state" type="hidden" value="<?php if ($summaryZone=="SummaryZone") echo cca02zone; else echo cca02; ?>"></a>
                    <?php  } ?>
                </td>
                <td align="right"><strong><?= number_format(($ascites['count'] + $splenomegaly['count'] + $other['count'])*100/$genderAll['person'],1)." %" ?></strong></td>
            </tr>
            <tr>
                <td>&emsp;1. Ascites</td>
                <td align="right">
                   <?php if((($ascites['count'])==0) || checkLiCense($user_Site_Code,$hospitalCode))
                            echo number_format($ascites['count']); 
                          else { ?>
                    <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($ascites['count']) ?>
                        <input id="data" type="hidden" value="f2v5a1">
                        <input id="cca02" type="hidden" value="1">
                        <input id="zonedata" type="hidden" value="<?= $zone ?>">
                        <input id="state" type="hidden" value="<?php if ($summaryZone=="SummaryZone") echo cca02zone; else echo cca02; ?>"></a>
                    <?php  } ?>
                </td>
                <td align="right"><?= number_format($ascites['count']*100/$genderAll['person'],1)." %" ?></td>
            </tr>
             <tr>
                <td>&emsp;2. Splenomegaly</td>
                <td align="right">
                    <?php if(($splenomegaly['count']==0) ||checkLiCense($user_Site_Code,$hospitalCode))
                            echo number_format($splenomegaly['count']); 
                          else { ?>
                    <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($splenomegaly['count']) ?>
                        <input id="data" type="hidden" value="f2v5a2">
                        <input id="cca02" type="hidden" value="1">
                        <input id="zonedata" type="hidden" value="<?= $zone ?>">
                        <input id="state" type="hidden" value="<?php if ($summaryZone=="SummaryZone") echo cca02zone; else echo cca02; ?>"></a>
                    <?php  } ?>
                </td>
                <td align="right"><?= number_format($splenomegaly['count']*100/$genderAll['person'],1)." %" ?></td>
            </tr>
             <tr>
                <td>&emsp;3. อื่นๆ (polyp, cyst)</td>
                <td align="right">
                    <?php if((($other['count'])==0)||checkLiCense($user_Site_Code,$hospitalCode))
                            echo number_format($other['count']); 
                          else { ?>
                    <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($other['count']) ?>
                        <input id="data" type="hidden" value="f2v5a3">
                        <input id="cca02" type="hidden" value="1">
                        <input id="zonedata" type="hidden" value="<?= $zone ?>">
                        <input id="state" type="hidden" value="<?php if ($summaryZone=="SummaryZone") echo cca02zone; else echo cca02; ?>"></a>
                    <?php  } ?>
                </td>
                <td align="right"><?= number_format($other['count']*100/$genderAll['person'],1)." %" ?></td>
                <?php $otherdiagnose = $genderAll['person']-($ascites['count']+$splenomegaly['count']+$other['count']);  
                $arrcca02 = array(['Ascites', intval($ascites['count'])],['Splenomegaly', intval($splenomegaly['count'])],['อื่นๆ (polyp, cyst)', intval($other['count'])],['Other Diagnosis', intval($otherdiagnose)]);
                    showChart("Other Finding","otherfinding",$arrcca02); ?>
            </tr>
    </table>
        <div class="row">
            <div class="col-md-10">
                 <p id="result" align="center"></p> 
            </div>
            <div class="pull-right">
                <div class="pull-right">
                    <button id="export-picture" onclick='pictureChart("#result")' type="button" class="btn btn-info" data-toggle="modal" data-target="#myModal" title="Export Chart to Picture">Chart to Picture</button>
                </div>
            </div>
        </div>
    <table class="table table-bordered" style="font-size:16px;">
            <tr>
                <td><strong><center>ผลการตรวจอัลตราซาวด์</center></strong></td>
                <td><strong><center>จำนวน (ครั้ง)</center></strong></td>
                <td><strong><center>%</center></strong></td>
            </tr>
            <tr>
                <td><strong>ผลตรวจปกติ (นัด 1 ปี)</strong></td>
                <td align="right">
                    <?php if((($oneYear['count'])==0) ||checkLiCense($user_Site_Code,$hospitalCode))
                            echo number_format($oneYear['count']); 
                          else { ?>
                    <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($oneYear['count']) ?>
                        <input id="data" type="hidden" value="f2v6">
                        <input id="cca02" type="hidden" value="1">
                        <input id="zonedata" type="hidden" value="<?= $zone ?>">
                        <input id="state" type="hidden" value="<?php if ($summaryZone=="SummaryZone") echo cca02zone; else echo cca02; ?>"></a>
                    <?php  } ?>
                </td>
                <td align="right"><strong><?= number_format($oneYear['count']*100/$genderAll['person'],1)." %" ?></strong></td>
            </tr>
            <tr>
                <td><strong>ผลตรวจผิดปกติ (นัด 6 เดือน)</strong></td>
                <td align="right">
                    <?php if((($sixMonth['count'])==0) ||checkLiCense($user_Site_Code,$hospitalCode))
                            echo number_format($sixMonth['count']); 
                          else { ?>
                    <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($sixMonth['count']) ?>
                        <input id="data" type="hidden" value="f2v6">
                        <input id="cca02" type="hidden" value="2">
                        <input id="zonedata" type="hidden" value="<?= $zone ?>">
                        <input id="state" type="hidden" value="<?php if ($summaryZone=="SummaryZone") echo cca02zone; else echo cca02; ?>"></a>
                    <?php  } ?>
                </td>
                <td align="right"><strong><?= number_format($sixMonth['count']*100/$genderAll['person'],1)." %" ?></strong></td>
                 <?php $otherdiagnose = $genderAll['person']-($oneYear['count']+$sixMonth['count']);
                 $arrcca02 = array(['ผลตรวจปกติ (นัด 1 ปี)', intval($oneYear['count'])],['ผลตรวจผิดปกติ (นัด 6 เดือน)', intval($sixMonth['count'])],['Other Diagnosis', intval($otherdiagnose)]);
                    showChart("ผลตรวจ","result",$arrcca02); ?>
            </tr>
    </table>
        <div class="row">
            <div class="col-md-10">
                 <p id="send" align="center"></p> 
            </div>
            <div class="pull-right">
                <div class="pull-right">
                    <button id="export-picture" onclick='pictureChart("#send")' type="button" class="btn btn-info" data-toggle="modal" data-target="#myModal" title="Export Chart to Picture">Chart to Picture</button>
                </div>
            </div>
        </div>
    <table class="table table-bordered" style="font-size:16px;">
            <tr>
                <td><strong><center>ผลการตรวจอัลตราซาวด์</center></strong></td>
                <td><strong><center>จำนวน (ครั้ง)</center></strong></td>
                <td><strong><center>%</center></strong></td>
            </tr>
            <tr>
                <td><strong>ส่งรักษาต่อ</strong></td>
                <td align="right">
                    <?php if((($send['count'])==0) ||checkLiCense($user_Site_Code,$hospitalCode))
                            echo number_format($send['count']); 
                          else { ?>
                    <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($send['count']) ?>
                        <input id="data" type="hidden" value="f2v6a3">
                        <input id="cca02" type="hidden" value="1">
                        <input id="zonedata" type="hidden" value="<?= $zone ?>">
                        <input id="state" type="hidden" value="<?php if ($summaryZone=="SummaryZone") echo cca02zone; else echo cca02; ?>"></a>
                    <?php  } ?>
                </td>
                <td align="right"><strong><?= number_format($send['count']*100/$genderAll['person'],1)." %" ?></strong></td>
            </tr>
             <tr>
                <td>&emsp;1. สงสัยมะเร็งท่อน้ำดี (Suspected CCA)</td>
                <td align="right">
                    <?php if((($suspectedCca['count'])==0) ||checkLiCense($user_Site_Code,$hospitalCode))
                            echo number_format($suspectedCca['count']); 
                          else { ?>
                    <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($suspectedCca['count']) ?>
                        <input id="data" type="hidden" value="f2v6a3b1">
                        <input id="cca02" type="hidden" value="1">
                        <input id="zonedata" type="hidden" value="<?= $zone ?>">
                        <input id="state" type="hidden" value="<?php if ($summaryZone=="SummaryZone") echo cca02zone; else echo cca02; ?>"></a>
                    <?php  } ?>
                </td>
                <td align="right"><strong><?= number_format($suspectedCca['count']*100/$genderAll['person'],1)." %" ?></strong></td>
            </tr>
             <tr>
                <td>&emsp;2. ส่งต่อเนื่องจากสาเหตุอื่น</td>
                <td align="right">
                    <?php if((($sendOther['count'])==0) ||checkLiCense($user_Site_Code,$hospitalCode))
                            echo number_format($sendOther['count']); 
                          else { ?>
                    <a href='javascript:void(0)' id="patient-drop<?=$drilldown?>"><?= number_format($sendOther['count']) ?>
                        <input id="data" type="hidden" value="f2v6a3b2">
                        <input id="cca02" type="hidden" value="1">
                        <input id="zonedata" type="hidden" value="<?= $zone ?>">
                        <input id="state" type="hidden" value="<?php if ($summaryZone=="SummaryZone") echo cca02zone; else echo cca02; ?>"></a>
                    <?php  } ?>
                </td>
                <td align="right"><strong><?= number_format($sendOther['count']*100/$genderAll['person'],1)." %" ?></strong></td>
                 <?php $otherdiagnose = $genderAll['person']-($suspectedCca['count']+$sendOther['count']+$sendOther['count']);
                 $arrcca02 = array(['สงสัยมะเร็งท่อน้ำดี (Suspected CCA)', intval($suspectedCca['count'])],['ส่งต่อเนื่องจากสาเหตุอื่น', intval($sendOther['count'])],['Other Diagnosis', intval($otherdiagnose)]);
                    showChart("ส่งรักษาต่อ","send",$arrcca02); ?>
            </tr>
        </table>
    <div id="myModal" class="modal fade" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-lg" role="document" style="width:55%">
        <div class="modal-content">
            <div class="modal-body" id="body-cca"></div>
            <div class="modal-footer">
                <button id="modalclose" type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>  
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
     //   console.log(state );
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
     
    function pictureChart (id) {
        var element = $(id); // global variable
        var getCanvas; // global variable
         html2canvas(element, {
         onrendered: function (canvas) {
                $('#body-cca').append(canvas);
                getCanvas = canvas;
                $('#body-cca').append(); 
             }
         });      
    }
    $(document).ready(function() {
        $('#myModal').on('hidden.bs.modal', function() {
            $('#body-cca').html('');
        });
    });
    ");
        ?>