<?php

use \yii\helpers\Html;

use backend\modules\usfinding\classes\QueryHospital;
use backend\modules\usfinding\classes\QueryUrine;

$ur = new QueryUrine();
$ur->ustime = $ustime;
$ur->getUrineResult();

if( FALSE ){
    echo '<pre align=left>';
    echo $ur->rawSql;
    print_r($ur->summary);
    print_r($ur->ghosp);
    echo '</pre>';
}


$request = Yii::$app->request;

if( $ur->summary['urine']>0 ){
?>
<div class="panel panel-default" style="width: 1100px; margin-left: 10px; margin-right: 10px;">
    <div class="panel-heading"><h4><b>ผลตรวจ Urine จากการออกสัญจรครั้งที่ <?php echo $ustime; ?></b></h4></div>
    <div id="tablePanelBody" class="panel-body">
        <table class="table table-condensed">
            <thead>
                <tr>
                    <th style="text-align: right;">ตรวจ Ultrasound ทั้งหมด</th>
                    <th style="text-align: right;">Urine</th>
                    <th style="text-align: right;">พบ PDF ทั้งหมด</th>
                    <th style="text-align: right;">PDF+Urine</th>
                    <th style="text-align: right;">ผลตรวจเป็น Positive (+)</th>
                    <th style="text-align: right;">ผลตรวจเป็น Negative (-)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align: right;">
                        <?php
                        //echo number_format($urinesum[0]['count(*)']);
//                        echo Html::a(number_format($urinesum[0]['count(*)']),
                        echo Html::a(number_format($ur->summary['us']),
                                Null,
                                [
                                    'id' => 'ovlistpatientreport',
                                    'data-url' => '/usfinding/default/show-list-patient-report',
                                    'keyStore' => 'OVU',
                                    'startdate' => substr($ur->exdate_start,0,10),
                                    'enddate' => substr($ur->exdate_end,0,10),
                                    'zone' => $ur->zonecode,
                                    'province' => $ur->provincecode,
                                    'amphur' => $ur->amphurcode,
                                    'hospital' => $ur->hsitecode,
                                    'style' => 'cursor: pointer; color:blue; ',
                                ]);
                        ?>
                    </td>
                    <td style="text-align: right;">
                        <?php
                        //echo number_format($urinesum[0]['urine']);
//                        echo Html::a(number_format($urinesum[0]['urine']),
                        echo Html::a(number_format($ur->summary['urine']),
                                Null,
                                [
                                    'id' => 'ovlistpatientreport',
                                    'data-url' => '/usfinding/default/show-list-patient-report',
                                    'keyStore' => 'OVUUrine',
                                    'startdate' => substr($ur->exdate_start,0,10),
                                    'enddate' => substr($ur->exdate_end,0,10),
                                    'zone' => $ur->zonecode,
                                    'province' => $ur->provincecode,
                                    'amphur' => $ur->amphurcode,
                                    'hospital' => $ur->hsitecode,
                                    'style' => 'cursor: pointer; color:blue;',
                                ]);
                        ?>
                    </td>
                    <td style="text-align: right;" title='กลุ่มที่ตรวจ Ultrasound พบ PDF'>
                        <?php
                        //echo number_format($urinesum[0]['pdf']);
                        echo Html::a(number_format($ur->summary['pdf']),
                                Null,
                                [
                                    'id' => 'ovlistpatientreport',
                                    'data-url' => '/usfinding/default/show-list-patient-report',
                                    'keyStore' => 'OVUPDF',
                                    'startdate' => substr($ur->exdate_start,0,10),
                                    'enddate' => substr($ur->exdate_end,0,10),
                                    'zone' => $ur->zonecode,
                                    'province' => $ur->provincecode,
                                    'amphur' => $ur->amphurcode,
                                    'hospital' => $ur->hsitecode,
                                    'style' => 'cursor: pointer; color:blue;',
                                ]);

                        ?>
                    </td>
                    
                    <td style="text-align: right;" title='กลุ่มที่ตรวจเจอ PDF และได้รับการตรวจ Urine ด้วย'>
                        <?php
                        //echo number_format($urinesum[0]['urinePDF']);
                        echo Html::a(number_format($ur->summary['urinePDF']),
                                Null,
                                [
                                    'id' => 'ovlistpatientreport',
                                    'data-url' => '/usfinding/default/show-list-patient-report',
                                    'keyStore' => 'OVUUrinePDF',
                                    'startdate' => substr($ur->exdate_start,0,10),
                                    'enddate' => substr($ur->exdate_end,0,10),
                                    'zone' => $ur->zonecode,
                                    'province' => $ur->provincecode,
                                    'amphur' => $ur->amphurcode,
                                    'hospital' => $ur->hsitecode,
                                    'style' => 'cursor: pointer; color:blue;',
                                ]);
                        ?>
                    </td>
                    <td style="text-align: right;">
                        <?php
                        //echo number_format($urinesum[0]['urine_positive_pdf']);
                        echo Html::a(number_format($ur->summary['urine_positive_pdf']),
                                Null,
                                [
                                    'id' => 'ovlistpatientreport',
                                    'data-url' => '/usfinding/default/show-list-patient-report',
                                    'keyStore' => 'OVUUrinePDFUrPos',
                                    'startdate' => substr($ur->exdate_start,0,10),
                                    'enddate' => substr($ur->exdate_end,0,10),
                                    'zone' => $ur->zonecode,
                                    'province' => $ur->provincecode,
                                    'amphur' => $ur->amphurcode,
                                    'hospital' => $ur->hsitecode,
                                    'style' => 'cursor: pointer; color:blue;',
                                ]);
                        ?>
                    </td>
                    <td style="text-align: right;">
                        <?php
                        //echo number_format($urinesum[0]['urine_negative_pdf']);
                        echo Html::a(number_format($ur->summary['urine_negative_pdf']),
                                Null,
                                [
                                    'id' => 'ovlistpatientreport',
                                    'data-url' => '/usfinding/default/show-list-patient-report',
                                    'keyStore' => 'OVUUrinePDFUrNeg',
                                    'startdate' => substr($ur->exdate_start,0,10),
                                    'enddate' => substr($ur->exdate_end,0,10),
                                    'zone' => $ur->zonecode,
                                    'province' => $ur->provincecode,
                                    'amphur' => $ur->amphurcode,
                                    'hospital' => $ur->hsitecode,
                                    'style' => 'cursor: pointer; color:blue;',
                                ]);
                        ?>
                    </td>
                </tr>
            </tbody>
            <thead>
                <tr>
                    <th style="text-align: right;"></th>
                    <th style="text-align: right;"></th>
                    <th style="text-align: right;">Suspected</th>
                    <th style="text-align: right;">Suspected+Urine</th>
                    <th style="text-align: right;">ผลตรวจเป็น Positive (+)</th>
                    <th style="text-align: right;">ผลตรวจเป็น Negative (-)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align: right;">
                        
                    </td>
                    <td style="text-align: right;">
                        
                    </td>
                    <td style="text-align: right;" title='กลุ่มที่ตรวจ Ultrasound แล้ว สงสัยมะเร็งท่อน้ำดี'>
                        <?php
                        //echo number_format($urinesum[0]['pdf']);
                        echo Html::a(number_format($ur->summary['suspected']),
                                Null,
                                [
                                    'id' => 'ovlistpatientreport',
                                    'data-url' => '/usfinding/default/show-list-patient-report',
                                    'keyStore' => 'OVUSusp',
                                    'startdate' => substr($ur->exdate_start,0,10),
                                    'enddate' => substr($ur->exdate_end,0,10),
                                    'zone' => $ur->zonecode,
                                    'province' => $ur->provincecode,
                                    'amphur' => $ur->amphurcode,
                                    'hospital' => $ur->hsitecode,
                                    'style' => 'cursor: pointer; color:blue;',
                                ]);

                        ?>
                    </td>
                    
                    <td style="text-align: right;" title='กลุ่ม Suspected และได้รับการตรวจ Urine ด้วย'>
                        <?php
                        //echo number_format($urinesum[0]['urinePDF']);
                        echo Html::a(number_format($ur->summary['urineSuspected']),
                                Null,
                                [
                                    'id' => 'ovlistpatientreport',
                                    'data-url' => '/usfinding/default/show-list-patient-report',
                                    'keyStore' => 'OVUUrineSusp',
                                    'startdate' => substr($ur->exdate_start,0,10),
                                    'enddate' => substr($ur->exdate_end,0,10),
                                    'zone' => $ur->zonecode,
                                    'province' => $ur->provincecode,
                                    'amphur' => $ur->amphurcode,
                                    'hospital' => $ur->hsitecode,
                                    'style' => 'cursor: pointer; color:blue;',
                                ]);
                        ?>
                    </td>
                    <td style="text-align: right;">
                        <?php
                        //echo number_format($urinesum[0]['urine_positive_pdf']);
                        echo Html::a(number_format($ur->summary['urine_positive_Suspected']),
                                Null,
                                [
                                    'id' => 'ovlistpatientreport',
                                    'data-url' => '/usfinding/default/show-list-patient-report',
                                    'keyStore' => 'OVUUrineSuspUrPos',
                                    'startdate' => substr($ur->exdate_start,0,10),
                                    'enddate' => substr($ur->exdate_end,0,10),
                                    'zone' => $ur->zonecode,
                                    'province' => $ur->provincecode,
                                    'amphur' => $ur->amphurcode,
                                    'hospital' => $ur->hsitecode,
                                    'style' => 'cursor: pointer; color:blue;',
                                ]);
                        ?>
                    </td>
                    <td style="text-align: right;">
                        <?php
                        //echo number_format($urinesum[0]['urine_negative_pdf']);
                        echo Html::a(number_format($ur->summary['urine_negative_Suspected']),
                                Null,
                                [
                                    'id' => 'ovlistpatientreport',
                                    'data-url' => '/usfinding/default/show-list-patient-report',
                                    'keyStore' => 'OVUUrineSuspUrNeg',
                                    'startdate' => substr($ur->exdate_start,0,10),
                                    'enddate' => substr($ur->exdate_end,0,10),
                                    'zone' => $ur->zonecode,
                                    'province' => $ur->provincecode,
                                    'amphur' => $ur->amphurcode,
                                    'hospital' => $ur->hsitecode,
                                    'style' => 'cursor: pointer; color:blue;',
                                ]);
                        ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<?php
if(count($urinesumhosp)>0 ){
    foreach ($urinesumhosp as $kvalue ){
        if(strlen($kvalue['hsitecode'])>0){
            if( strlen($strSql)>0 ){
                $strSql.="OR hcode='".$kvalue['hsitecode']."' ";
            }else{
                $strSql="hcode='".$kvalue['hsitecode']."' ";
            }
        }
    }
}
if( strlen($strSql)>0 ){
    $hospital = QueryHospital::getSummray($strSql);
}
?>
<div class="panel panel-default" style="width: 1100px; margin-left: 10px; margin-right: 10px;">
    <div class="panel-heading"><h4><b>แสดงผลตามโรงพยาบาล ที่ส่ง Urine เข้าตรวจได้ดังนี้ </b></h4></div>
    <div id="tablePanelBody" class="panel-body">
        <table class="table table-condensed">
            <thead>
                <tr>
                    <th style="text-align: left; max-width: 300; min-width: 300px;">โรงพยาบาล</th>
                    <th style="text-align: right;">Ultrasound</th>
                    <th style="text-align: right;">Urine (+)</th>
                    <th style="text-align: right;">Urine (-)</th>
                    <th style="text-align: right;">พบ PDF</th>
                    <th style="text-align: right;">PDF&Urine (+)</th>
                    <th style="text-align: right;">PDF&Urine (-)</th>
                </tr>
            </thead>
            <tbody>
                <?php
//                if(count($urinesumhosp)>0 ){
                if(count($ur->ghosp)>0 ){
//                    foreach ($urinesumhosp as $kvalue ){
                    foreach ($ur->ghosp as $kvalue ){
                        if(strlen($kvalue['hsitecode'])>0){
                            $hospdet = QueryHospital::getHospitalDet($kvalue['hsitecode']);
                ?>
                <tr>
                    <td style="text-align: left; max-width: 300; min-width: 300px;">
                        <?php
                        echo $kvalue['hsitecode'];
                        echo ": ";
//                        echo $hospital[$kvalue['hsitecode']];
                        echo $hospdet['name'];
                        ?>
                    </td>
                    <td style="text-align: right;" title="คนที่ได้ส่ง Urine ตรวจ">
                        <?php
                        //echo $kvalue['usall'];
                        $vsum['usall'] = $vsum['usall'] + $kvalue['usall'];
                        echo Html::a(number_format($kvalue['usall']),
                                Null,
                                [
                                    'id' => 'ovlistpatientreport',
                                    'data-url' => '/usfinding/default/show-list-patient-report',
                                    'keyStore' => 'OVUUrine',
                                    'startdate' => substr($ur->exdate_start,0,10),
                                    'enddate' => substr($ur->exdate_end,0,10),
                                    'zone' => $ur->zonecode,
                                    'province' => $ur->provincecode,
                                    'amphur' => $ur->amphurcode,
                                    'hospital' => $ur->hsitecode,
                                    'ovuhospital' => $kvalue['hsitecode'],
                                    'style' => 'cursor: pointer; color:blue;',
                                ]);
                        ?>
                    </td>
                    <td style="text-align: right;">
                        <?php
                        $vsum['urine_positive'] = $vsum['urine_positive'] + $kvalue['urine_positive'];
                        echo $kvalue['urine_positive'];
                        ?>
                    </td>
                    <td style="text-align: right;">
                        <?php
                        $vsum['urine_negative'] = $vsum['urine_negative'] + $kvalue['urine_negative'];
                        echo $kvalue['urine_negative'];
                        ?>
                    </td>
                    <td style="text-align: right;" title="คนที่ตรวจเจอทั้ง PDF">
                        <?php
                        //echo $kvalue['pdf'];
                        $vsum['pdf'] = $vsum['pdf'] + $kvalue['pdf'];
                        echo Html::a(number_format($kvalue['pdf']),
                                Null,
                                [
                                    'id' => 'ovlistpatientreport',
                                    'data-url' => '/usfinding/default/show-list-patient-report',
                                    'keyStore' => 'OVUPDF',
                                    'startdate' => substr($ur->exdate_start,0,10),
                                    'enddate' => substr($ur->exdate_end,0,10),
                                    'zone' => $ur->zonecode,
                                    'province' => $ur->provincecode,
                                    'amphur' => $ur->amphurcode,
                                    'hospital' => $ur->hsitecode,
                                    'ovuhospital' => $kvalue['hsitecode'],
                                    'style' => 'cursor: pointer; color:blue;',
                                ]);
                        ?>
                    </td>
                    <td style="text-align: right;">
                        <?php
                        //echo $kvalue['urine_positive_pdf'];
                        $vsum['urine_positive_pdf'] = $vsum['urine_positive_pdf'] + $kvalue['urine_positive_pdf'];
                        echo Html::a(number_format($kvalue['urine_positive_pdf']),
                                Null,
                                [
                                    'id' => 'ovlistpatientreport',
                                    'data-url' => '/usfinding/default/show-list-patient-report',
                                    'keyStore' => 'OVUUrinePDFUrPos',
                                    'startdate' => substr($ur->exdate_start,0,10),
                                    'enddate' => substr($ur->exdate_end,0,10),
                                    'zone' => $ur->zonecode,
                                    'province' => $ur->provincecode,
                                    'amphur' => $ur->amphurcode,
                                    'hospital' => $ur->hsitecode,
                                    'ovuhospital' => $kvalue['hsitecode'],
                                    'style' => 'cursor: pointer; color:blue;',
                                ]);
                        ?>
                    </td>
                    <td style="text-align: right;">
                        <?php
                        //echo $kvalue['urine_negative_pdf'];
                        $vsum['urine_negative_pdf'] = $vsum['urine_negative_pdf'] + $kvalue['urine_negative_pdf'];
                        echo Html::a(number_format($kvalue['urine_negative_pdf']),
                                Null,
                                [
                                    'id' => 'ovlistpatientreport',
                                    'data-url' => '/usfinding/default/show-list-patient-report',
                                    'keyStore' => 'OVUUrinePDFUrNeg',
                                    'startdate' => substr($ur->exdate_start,0,10),
                                    'enddate' => substr($ur->exdate_end,0,10),
                                    'zone' => $ur->zonecode,
                                    'province' => $ur->provincecode,
                                    'amphur' => $ur->amphurcode,
                                    'hospital' => $ur->hsitecode,
                                    'ovuhospital' => $kvalue['hsitecode'],
                                    'style' => 'cursor: pointer; color:blue;',
                                ]);
                        ?>
                    </td>
                </tr>
                <?php
                        }
                        
                    }
                    
                    
                    # รวม
                ?>
                <tr>
                    <td align='left'>
                        <h4><b>รวม</b></h4>
                    </td>
                    <td style="text-align: right;">
                        <b>
                        <?php
                        //echo number_format($vsum['usall']);
                        echo Html::a(number_format($vsum['usall']),
                                Null,
                                [
                                    'id' => 'ovlistpatientreport',
                                    'data-url' => '/usfinding/default/show-list-patient-report',
                                    'keyStore' => 'OVUUrine',
                                    'startdate' => substr($ur->exdate_start,0,10),
                                    'enddate' => substr($ur->exdate_end,0,10),
                                    'zone' => $ur->zonecode,
                                    'province' => $ur->provincecode,
                                    'amphur' => $ur->amphurcode,
                                    'hospital' => $ur->hsitecode,
                                    'style' => 'cursor: pointer; color:black; font-size: 16px; font_weight: bold;',
                                ]);
                        ?>
                        </b>
                    </td>
                    <td style="text-align: right;">
                        <b>
                        <?php
                        echo number_format($vsum['urine_positive']);
                        ?>
                        </b>
                    </td>
                    <td style="text-align: right;">
                        <b>
                        <?php
                        echo number_format($vsum['urine_negative']);
                        ?>
                        </b>
                    </td>
                    <td style="text-align: right;">
                        <b>
                        <?php
                        //echo number_format($vsum['pdf']);
                        echo Html::a(number_format($vsum['pdf']),
                                Null,
                                [
                                    'id' => 'ovlistpatientreport',
                                    'data-url' => '/usfinding/default/show-list-patient-report',
                                    'keyStore' => 'OVUUrinePDF',
                                    'startdate' => substr($ur->exdate_start,0,10),
                                    'enddate' => substr($ur->exdate_end,0,10),
                                    'zone' => $ur->zonecode,
                                    'province' => $ur->provincecode,
                                    'amphur' => $ur->amphurcode,
                                    'hospital' => $ur->hsitecode,
                                    'style' => 'cursor: pointer; color:black; font-size: 16px; font_weight: bold;',
                                ]);
                        ?>
                        </b>
                    </td>
                    <td style="text-align: right;">
                        <b>
                        <?php
                        //echo number_format($vsum['urine_positive_pdf']);
                        echo Html::a(number_format($vsum['urine_positive_pdf']),
                                Null,
                                [
                                    'id' => 'ovlistpatientreport',
                                    'data-url' => '/usfinding/default/show-list-patient-report',
                                    'keyStore' => 'OVUUrinePDFUrPos',
                                    'startdate' => substr($ur->exdate_start,0,10),
                                    'enddate' => substr($ur->exdate_end,0,10),
                                    'zone' => $ur->zonecode,
                                    'province' => $ur->provincecode,
                                    'amphur' => $ur->amphurcode,
                                    'hospital' => $ur->hsitecode,
                                    'style' => 'cursor: pointer; color:black; font-size: 16px; font_weight: bold;',
                                ]);
                        ?>
                        </b>
                    </td>
                    <td style="text-align: right;">
                        <b>
                        <?php
                        //echo number_format($vsum['urine_negative_pdf']);
                        echo Html::a(number_format($vsum['urine_negative_pdf']),
                                Null,
                                [
                                    'id' => 'ovlistpatientreport',
                                    'data-url' => '/usfinding/default/show-list-patient-report',
                                    'keyStore' => 'OVUUrinePDFUrNeg',
                                    'startdate' => substr($ur->exdate_start,0,10),
                                    'enddate' => substr($ur->exdate_end,0,10),
                                    'zone' => $ur->zonecode,
                                    'province' => $ur->provincecode,
                                    'amphur' => $ur->amphurcode,
                                    'hospital' => $ur->hsitecode,
                                    'style' => 'cursor: pointer; color:black; font-size: 16px; font_weight: bold;',
                                ]);
                        ?>
                        </b>
                    </td>
                </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
<?php
}
//
if( 0 ){
    echo "<pre align='left'>";
    print_r($urinesum);
    print_r($urinesumhosp);
    echo "</pre>";
}