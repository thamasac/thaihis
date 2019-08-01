<?php
use yii\helpers\Url;
use yii\jui\DatePicker;


$parenchymal = $allDataUsFindingReport["Parenchymal-ECHO"]["count"];
$normal = $allDataUsFindingReport['Normal']['count'];
$abnormal = $allDataUsFindingReport['Abnormal']['count'];
$PDF = $allDataUsFindingReport['PDF']['count'];
$cirrhosis = $allDataUsFindingReport['Cirrhosis']['count'];
$fatty_liver = $allDataUsFindingReport['Fatty-liver']['count'];
$parenchymal_change = $parenchymalChange;

$parenchymalArr = explode(',', $parenchymal);
$parenchymalNum = $parenchymalArr[0].$parenchymalArr[1];

$maxPer = $parenchymalNum/100000;
$susPer = ($suspected_case/$parenchymalNum) * 100;

if($maxPer >= 1)
    $susPerMax = $suspected_case/$maxPer;
else{
    $susPerMax = $suspected_case*$maxPer;
}
$susPerMax = ($suspected_case/$parenchymalNum)*100000;



//$liver_mass = $allDataUsFindingReport['Normal-Liver-mass']['count'] + $allDataUsFindingReport['Mild-Liver-mass']['count'] +
//        $allDataUsFindingReport['Moderate-Liver-mass']['count'] + $allDataUsFindingReport['Severe-Liver-mass']['count'] +
//        $allDataUsFindingReport['PDF1-Liver-mass']['count'] + $allDataUsFindingReport['PDF2-Liver-mass']['count'] +
//        $allDataUsFindingReport['PDF3-Liver-mass']['count'] + $allDataUsFindingReport['Cirrhosis-Liver-mass']['count']
//;
$dilated_duct = $allDataUsFindingReport['Normal-Duct-dilate']['count'] + $allDataUsFindingReport['Mild-Duct-dilate']['count'] +
        $allDataUsFindingReport['Moderate-Duct-dilate']['count'] + $allDataUsFindingReport['Severe-Duct-dilate']['count'] +
        $allDataUsFindingReport['PDF1-Duct-dilate']['count'] + $allDataUsFindingReport['PDF2-Duct-dilate']['count'] +
        $allDataUsFindingReport['PDF3-Duct-dilate']['count'] + $allDataUsFindingReport['Cirrhosis-Duct-dilate']['count'];

$dilated_duct = $DilatedBileDuct;
//var_dump($allDataUsFindingReport);

?>

<div class="panel panel-default" style="width: 90%;margin-left: 10px;color:blue;">
    <div class="panel-heading">
        <h3>Suspected case</h3>
    </div>
    <div class="panel-body">
        <div style="padding-left:20px;width:95%;">

            <h4>  1. Parenchymal Echo <a href="javascript:void(0)" startdate="<?=$startDate?>" enddate="<?=$endDate?>" onclick="onloadPatient(null,this)" style="color:red;"><?= is_numeric($parenchymal)?number_format($parenchymal):$parenchymal ?></a> ราย</h4>
            <div id="parenchymal-show" style="color:#333;"></div>
            <h4>  &nbsp;&nbsp;a. Normal <a href="javascript:void(0)" startdate="<?=$startDate?>" enddate="<?=$endDate?>" onclick="onloadPatient('normal',this)" style="color:red;"><?= is_numeric($normal)?number_format($normal):$normal ?></a> ราย</h4>
            <div id="normal-show" style="color:#333;"></div>
            <h4>  &nbsp;&nbsp;b. Abnormal <a href="javascript:void(0)" startdate="<?=$startDate?>" enddate="<?=$endDate?>" onclick="onloadPatient('abnormal',this)" style="color:red;"><?= is_numeric($abnormal)?number_format($abnormal):$abnormal ?></a> ราย</h4>
            <div id="abnormal-show" style="color:#333;"></div>
            <h4>  &nbsp;&nbsp;&nbsp;&nbsp;i. Fatty liver <a href="javascript:void(0)" startdate="<?=$startDate?>" enddate="<?=$endDate?>"  onclick="onloadPatient('fatty-liver',this)"style="color:red;"><?= is_numeric($fatty_liver)?number_format($fatty_liver):$fatty_liver ?></a> ราย</h4>
            <div id="fatty-liver-show" style="color:#333;"></div>
            <h4>  &nbsp;&nbsp;&nbsp;&nbsp;ii. PDF <a href="javascript:void(0)" startdate="<?=$startDate?>" enddate="<?=$endDate?>" onclick="onloadPatient('pdf',this)" style="color:red;"><?= is_numeric($PDF)?number_format($PDF):$PDF ?></a> ราย</h4>
            <div id="pdf-show" style="color:#333;"></div>
            <h4>  &nbsp;&nbsp;&nbsp;&nbsp;iii. Cirrhosis <a href="javascript:void(0)" startdate="<?=$startDate?>" enddate="<?=$endDate?>" onclick="onloadPatient('cirrhosis',this)" style="color:red;"><?= is_numeric($cirrhosis)?number_format($cirrhosis):$cirrhosis ?> </a>ราย</h4>
            <div id="cirrhosis-show" style="color:#333;"></div>
            <h4>  &nbsp;&nbsp;&nbsp;&nbsp;iv. Parenchymal change <a href="javascript:void(0)" startdate="<?=$startDate?>" enddate="<?=$endDate?>" onclick="onloadPatient('parenchymal-change',this)" style="color:red;"><?= is_numeric($parenchymal_change)?number_format($parenchymal_change):$parenchymal_change ?> </a>ราย</h4>
            <div id="parenchymal-change-show" style="color:#333;"></div>
            <h4>  2. Suspected case <a href="javascript:void(0)" startdate="<?=$startDate?>" enddate="<?=$endDate?>" onclick="onloadPatient('suspected-case',this)" style="color:red;">
            <?= is_numeric($suspected_case)?number_format($suspected_case):$suspected_case ?> </a>ราย (คิดเป็น <span style="color:red;"><?= number_format($susPer,'2')?> %</span> หรือ <span style="color:red;"><?=number_format($susPerMax).'/100,000  ประชากร'?></span>)
            </h4>
            <div id="suspected-case-show" style="color:#333;"></div>
            <h4>  &nbsp;&nbsp;a. Dilated bile duct <a href="javascript:void(0)" startdate="<?=$startDate?>" enddate="<?=$endDate?>" onclick="onloadPatient('dilated-duct',this)" style="color:red;"><?= is_numeric($dilated_duct)?number_format($dilated_duct):$dilated_duct ?> </a>ราย</h4>
            <div id="dilated-duct-show" style="color:#333;"></div>
            <h4>  &nbsp;&nbsp;b. Liver mass <a href="javascript:void(0)" startdate="<?=$startDate?>" enddate="<?=$endDate?>" onclick="onloadPatient('liver-mass',this)" style="color:red;"><?= is_numeric($liver_mass)?number_format($liver_mass):$liver_mass ?> </a>ราย</h4>
            <div id="liver-mass-show" style="color:#333;"></div>
            <h4>  &nbsp;&nbsp;c. Both <a href="javascript:void(0)" startdate="<?=$startDate?>" enddate="<?=$endDate?>" onclick="onloadPatient('both',this)" style="color:red;"><?= is_numeric($both)?number_format($both):$both ?> </a>ราย</h4>
            <div id="both-show" style="color:#333;"></div>
            <h4>  &nbsp;&nbsp;2.1 Parenchymal Echo <a href="javascript:void(0)" startdate="<?=$startDate?>" enddate="<?=$endDate?>" onclick="onloadPatient('suspected-parenchymal',this)" style="color:red;"><?= is_numeric($suspected_parenchymal)?number_format($suspected_parenchymal):$suspected_parenchymal ?> </a>ราย</h4>
            <div id="suspected-parenchymal-show" style="color:#333;"></div>
            <h4>  &nbsp;&nbsp;&nbsp; 2.1.1 Normal <a href="javascript:void(0)" startdate="<?=$startDate?>" enddate="<?=$endDate?>" onclick="onloadPatient('suspected-normal',this)" style="color:red;"><?= is_numeric($suspected_normal)?number_format($suspected_normal):$suspected_normal ?> </a>ราย</h4>
            <div id="suspected-normal-show" style="color:#333;"></div>
            <h4>  &nbsp;&nbsp;&nbsp; 2.1.2 Abnormal <a href="javascript:void(0)" startdate="<?=$startDate?>" enddate="<?=$endDate?>" onclick="onloadPatient('suspected-abnormal',this)" style="color:red;"><?= is_numeric($suspected_abnormal)?number_format($suspected_abnormal):$suspected_abnormal ?> </a>ราย</h4>
            <div id="suspected-abnormal-show" style="color:#333;"></div>
            <h4>  &nbsp;&nbsp;&nbsp;&nbsp; 1) Fatty Liver <a href="javascript:void(0)" startdate="<?=$startDate?>" enddate="<?=$endDate?>" onclick="onloadPatient('suspected-fatty-liver',this)" style="color:red;"><?= is_numeric($suspected_fatty_liver)?number_format($suspected_fatty_liver):$suspected_fatty_liver ?> </a>ราย</h4>
            <div id="suspected-fatty-liver-show" style="color:#333;"></div>
            <h4>  &nbsp;&nbsp;&nbsp;&nbsp; 2) PDF <a href="javascript:void(0)" startdate="<?=$startDate?>" enddate="<?=$endDate?>" onclick="onloadPatient('suspected-pdf',this)" style="color:red;"><?= is_numeric($suspected_pdf)?number_format($suspected_pdf):$suspected_pdf ?> </a>ราย</h4>
            <div id="suspected-pdf-show" style="color:#333;"></div>
            <h4>  &nbsp;&nbsp;&nbsp;&nbsp; 3) Cirrhosis <a href="javascript:void(0)" startdate="<?=$startDate?>" enddate="<?=$endDate?>" onclick="onloadPatient('suspected-cirrhosis',this)" style="color:red;"><?= is_numeric($suspected_cirrhosis)?number_format($suspected_cirrhosis):$suspected_cirrhosis ?> </a>ราย</h4>
            <div id="suspected-cirrhosis-show" style="color:#333;"></div>
            <h4>  &nbsp;&nbsp;&nbsp;&nbsp; 4) Parenchymal change <a href="javascript:void(0)" startdate="<?=$startDate?>" enddate="<?=$endDate?>" onclick="onloadPatient('suspected-parenchymal-change',this)" style="color:red;"><?= is_numeric($suspected_parenchymal_change)?number_format($suspected_parenchymal_change):$suspected_parenchymal_change ?> </a>ราย</h4>
            <div id="suspected-parenchymal-change-show" style="color:#333;"></div>
            <h4>  3. Refer case <a href="javascript:void(0)" onclick="onloadPatient('refer-case',this)" startdate="<?=$startDate?>" enddate="<?=$endDate?>" style="color:red;"><?= is_numeric($refer_case)?number_format($refer_case):$refer_case ?> </a>ราย</h4>
            <div id="refer-case-show" style="color:#333;"></div>
            <h4>  &nbsp;&nbsp;a. ส่งต่อด้วยสาเหตุ Suspected cca <a href="javascript:void(0)" startdate="<?=$startDate?>" enddate="<?=$endDate?>" onclick="onloadPatient('refer-suspected-cca',this)" style="color:red;"><?= is_numeric($refer_suspected_case)?number_format($refer_suspected_case):$refer_suspected_case ?> </a>ราย</h4>
            <div id="refer-suspected-cca-show" style="color:#333;"></div>
            <h4>  &nbsp;&nbsp;b. ส่งต่อด้วยสาเหตุอื่น <a href="javascript:void(0)" startdate="<?=$startDate?>" enddate="<?=$endDate?>" onclick="onloadPatient('refer-other',this)" style="color:red;"><?= is_numeric($refer_other)?number_format($refer_other):$refer_other ?> </a>ราย</h4>
            <div id="refer-other-show" style="color:#333;"></div>
        </div>
    </div>
</div>

<?php
$this->registerJs("
    $(function(){
       
    });
    
    
    function onloadPatient(divCase, tag){
        var divshow = $('#'+divCase+'-show');
         $('#listPatient').html('<div class=\'row text-center\'><i class=\'fa fa-spinner fa-spin fa-3x\' style=\'margin:50px 0;\'></i></div>');
        //divshow.html('<div style=\'text-align:center;\'><i class=\"fa fa-circle-o-notch fa-spin fa-fw fa-2x\"></i></div>');
        var listPatientTopPosition = jQuery('#listPatient').offset().top;
        jQuery('html, body').animate({scrollTop:listPatientTopPosition}, 'slow');

        var keystore = divCase;
        var startdate = $(tag).attr('startdate');
        var enddate = $(tag).attr('enddate');
        var hospital = '$province';
        var zone = '$zone';
        var province = '$province';
        var amphur = '$amphur';
        var hospital = '$hospital';
        $.ajax({
            type    : 'GET',
            cache   : false,
            url     : '" . Url::to('/usfinding/default/show-list-patient-report') . "',
            data    : {
                keystore: keystore,
                startdate: startdate,
                enddate: enddate,
                zone: zone,
                province: province,
                amphur: amphur,
                hospital: hospital
            },
            success  : function(response) {
                $('#listPatient').html(response);
                exportToExcel();
                var listPatientTopPosition = jQuery('#listPatient').offset().top;
                jQuery('html, body').animate({scrollTop:listPatientTopPosition}, 'slow');
            },
            error : function(){

            }
        });
        
    }
");
?>
