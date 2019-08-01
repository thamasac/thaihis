<!-- /*
 * Module US Finding.
 * Developed by Mark.
 * Date : 2016-03-01
*/ -->
<style>
    .panel-success2 .panel-heading2{
        background: #00A21E;
        color: #fff;
        padding-top:20px;
        padding-left:20px;
        padding-right:20px;
    }
</style>
<?php

use yii\helpers\Url;
use yii\jui\DatePicker;

$active = \Yii::$app->request->get('active');

//$this->title = Yii::t('', 'US Finding');
//echo Yii::$app->getRequest()->url;
if (Yii::$app->getRequest()->url == '/usfinding/default/usfinding') {
//    $this->registerCssFile('/css/usfinding.css');
//    $this->registerCssFile('/assets/931d41cc/css/bootstrap.css');
//    $this->registerCssFile('/assets/c77899e1/themes/smoothness/jquery-ui.css');
//    $this->registerCssFile('/assets/382c7ca3/css/font-awesome.min.css');
//    $this->registerCssFile('/assets/5da5963e/css/AdminLTE.min.css');
//    $this->registerCssFile('/assets/5da5963e/css/skins/_all-skins.min.css');
//    $this->registerCssFile('/assets/7da9ecf3/css/jquery.noty.css');
//    $this->registerCssFile('/assets/7da9ecf3/css/noty_theme_twitter.css');
    $this->registerCssFile('https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css');
}

$loadIconData = '\'<i class="fa fa-spinner fa-spin fa-3x" style="margin:50px 0;"></i>\'';
$this->registerCssFile('/css/usfinding.css');
$this->registerJsFile('/js/jsdad/jquery.min.js', ['position' => \yii\web\View::POS_BEGIN]);
$this->registerJsFile('/js/FileSaver.js', ['position' => \yii\web\View::POS_BEGIN]);
$this->registerJsFile('/js/jquery.wordexport.js', ['position' => \yii\web\View::POS_BEGIN]);
$this->registerJsFile('/js/excellentexport.js', ['position' => \yii\web\View::POS_BEGIN]);
$this->registerJsFile('https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('https://files.codepedia.info/files/uploads/iScripts/html2canvas.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJs("
    var active = '$active';
        
    $('#tab-one').on('click', function(){
        var monDiv = $('#reportUSFinding');
        monDiv.empty();
        active = 'usfinding';
        $(this).addClass('active');
        $('#tab-two').removeClass('active');
        $('#tab-three').removeClass('active');
       // window.location.href='/usfinding?active=usfinding'
    });
    
    $('#tab-two').on('click', function(){
        var monDiv = $('#reportUSFinding');
        monDiv.empty();
        active = 'monitoring';
        $(this).addClass('active');
        $('#tab-one').removeClass('active');
        $('#tab-three').removeClass('active');
        monitorActive();
        //window.location.href='/usfinding?active=monitoring'
    });
    ");
$this->registerJs('
    var summaryZone = null;
     
    function getValueDiv(){
        $("[divReportUSFinding]").click(function(){
            $("#listPatient").html(\'<div class="row text-center"><i class="fa fa-spinner fa-spin fa-3x" style="margin:50px 0;"></i></div>\');
            var listPatientTopPosition = jQuery("#listPatient").offset().top;
            jQuery("html, body").animate({scrollTop:listPatientTopPosition}, "slow");
            
            var keystore = $(this).attr("keyStore");
            var startdate = $(this).attr("startdate");
            var enddate = $(this).attr("enddate");
            var zone = $(this).attr("zone");
            var province = $(this).attr("province");
            var amphur = $(this).attr("amphur");
            var hospital = $(this).attr("hospital");
            $.ajax({
                type    : "GET",
                cache   : false,
                url     : "' . Url::to('/usfinding/default/show-list-patient-report') . '",
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
                    $("#listPatient").html(response);
                    exportToExcel();
                    var listPatientTopPosition = jQuery("#listPatient").offset().top;
                    jQuery("html, body").animate({scrollTop:listPatientTopPosition}, "slow");
                },
                error : function(){
                    $("#listPatient").html("");
                }
            });
        });
    }

    function getValueSpanResult(){
        $("[spanReportResultUSFinding]").click(function(){
            $("#listPatient").html(\'<div class="row text-center"><i class="fa fa-spinner fa-spin fa-3x" style="margin:50px 0;"></i></div>\');
            var listPatientTopPosition = jQuery("#listPatient").offset().top;
            jQuery("html, body").animate({scrollTop:listPatientTopPosition}, "slow");
            
            var keystore = $(this).attr("keyStore");
            var startdate = $(this).attr("startdate");
            var enddate = $(this).attr("enddate");
            var zone = $(this).attr("zone");
            var province = $(this).attr("province");
            var amphur = $(this).attr("amphur");
            var hospital = $(this).attr("hospital");
            $.ajax({
                type    : "GET",
                cache   : false,
                url     : "' . Url::to('/usfinding/default/show-list-patient-report-result') . '",
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
                    $("#listPatient").html(response);
                    exportToExcel();
                    var listPatientTopPosition = jQuery("#listPatient").offset().top;
                    jQuery("html, body").animate({scrollTop:listPatientTopPosition}, "slow");
                },
                error : function(){
                    $("#listPatient").html("");
                }
            });
        });
    }
    
    function exportToExcel(){
        $(".exportToExcel").click(function(){
            var tableClone = $("#tableClone");
            var tableLP = $("#table-listPatiant");
            var copy = tableLP.clone();
            copy.attr(\'id\', \'tableClone\');
            tableClone.replaceWith(copy);
            $("#tableClone").hide();
            var usimg = $("img#usimage");
            var atag = $("a#atag");
            
            for(var i=0;i<usimg.length/2;i++){
                usimg[i].remove();
            }
            for(var i=0;i<atag.length/2;i++){
                atag[i].replaceWith(atag[i].text)
            }
            this.download = "usfinding-report.xls";
            ExcellentExport.excel(this,tableClone.attr(\'id\'), \'US Finding Report\');
 
        });
    }

    function resetTableResultUSFinding(){
        $("#listPatient").html("");
    }

    function getProvince(zoneCode,value){
        $.ajax({
            type    : "GET",
            cache   : false,
            url     : "' . Url::to('/usfinding/default/province') . '",
            data    : {
                zoneCode: zoneCode
            },
            success  : function(response) {
                $("#inputProvence").html(response);
                if(value!=""){
                    $("#inputProvence").val(value);
                }
            },
            error : function(){
                $("#inputProvence").html("");
            }
        });
    }

    function getAmphur(provinceCode,value){
        $.ajax({
            type    : "GET",
            cache   : false,
            url     : "' . Url::to('/usfinding/default/amphur') . '",
            data    : {
                provinceCode: provinceCode
            },
            success  : function(response) {
                $("#inputAmphur").html(response);
                if(value!=""){
                    $("#inputAmphur").val(value);
                }
            },
            error : function(){
                $("#inputAmphur").html("");
            }
        });
    }

    function getHospital(provinceCode,amphurCode, value){
        $.ajax({
            type    : "GET",
            cache   : false,
            url     : "' . Url::to('/usfinding/default/all-hospital-thai') . '",
            data    : {
                provinceCode: provinceCode,
                amphurCode: amphurCode
            },
            success  : function(response) {
                $("#inputHospital").html(response);
                if(value!=""){
                    $("#inputHospital").val(value);
                }
            },
            error : function(){
                $("#inputHospital").html("");
            }
        });
    }
', yii\web\View::POS_BEGIN);
$this->registerJs('
    $("#inputZone").change(function(){
        if( $("#inputZone").val() != "" ){
            getProvince($("#inputZone").val(),"");
        }
    });

    $("#inputProvence").change(function(){
        if( $("#inputProvence").val() != "" ){
            getAmphur($("#inputProvence").val(),"");
        }
    });

    $("#inputAmphur").change(function(){
        if( $("#inputProvence").val()!="" && $("#inputAmphur").val()!="" ){
            getHospital($("#inputProvence").val(),$("#inputAmphur").val(),"");
        }
    });
    
    $("#btnShowReportByZone").click(function(){
        if(active=="monitoring"){
            showMonitorOfZone();
        }else{
            var reportUSFindingTopPosition = jQuery("#reportUSFinding").offset().top;
            jQuery("html, body").animate({scrollTop:reportUSFindingTopPosition}, "slow");
            var startDate = $("#inputStartDate").val();
            var endDate = $("#inputEndDate").val();
            var zoneCode = $("#inputZone").val();
            if( zoneCode==""){
                alert("กรุณาเลือก เขต");
            }else if((summaryZone == "SummaryZone") && (startDate != "") && (endDate != "") && (zoneCode != "")){           
                    summaryArea(zoneCode,null,null);
            }else if((summaryZone == "Cca01Zone") && (startDate != "") && (endDate != "") && (zoneCode != "")){  
                    cca01Area(zoneCode,null,null);
            }else if((startDate != "") && (endDate != "") && (zoneCode != "")){
                $("#reportUSFinding").html(\'<div class="row text-center"><i class="fa fa-spinner fa-spin fa-3x" style="margin:50px 0;"></i></div>\');
                $.ajax({
                    type    : "GET",
                    cache   : false,
                    url     : "' . Url::to('/usfinding/default/show-report') . '",
                    data    : {
                        startDate: startDate,
                        endDate: endDate,
                        zoneCode: zoneCode
                    },
                    success  : function(response) {
                        $("#reportUSFinding").html(response);
                        getValueDiv();
                        getValueSpanResult();
                        resetTableResultUSFinding();

                        var reportUSFindingTopPosition = jQuery("#reportUSFinding").offset().top;
                        jQuery("html, body").animate({scrollTop:reportUSFindingTopPosition}, "slow");
                    },
                    error : function(){
                        $("#reportUSFinding").html("Error");
                    }
                });
            }
        }
    });

    $("#btnShowReportByProvince").click(function(){
        if(active=="monitoring"){
            showMonitorOfProvince();
        }else{
            var reportUSFindingTopPosition = jQuery("#reportUSFinding").offset().top;
            jQuery("html, body").animate({scrollTop:reportUSFindingTopPosition}, "slow");
            var startDate = $("#inputStartDate").val();
            var endDate = $("#inputEndDate").val();
            var provinceCode = $("#inputProvence").val();
            if( provinceCode == "" ){
                alert("กรุณาเลือกจังหวัด");
            } else if((summaryZone == "SummaryZone") && (startDate != "") && (endDate != "") && (provinceCode != "")){
               summaryArea(null,provinceCode,null);
            } else if((summaryZone == "Cca01Zone") && (startDate != "") && (endDate != "") && (provinceCode != "")){
                cca01Area(null,provinceCode,null);
            }else if((startDate != "") && (endDate != "") && (provinceCode != "")){
                $("#reportUSFinding").html(\'<div class="row text-center"><i class="fa fa-spinner fa-spin fa-3x" style="margin:50px 0;"></i></div>\');
                $.ajax({
                    type    : "GET",
                    cache   : false,
                    url     : "' . Url::to('/usfinding/default/show-report') . '",
                    data    : {
                        startDate: startDate,
                        endDate: endDate,
                        provinceCode: provinceCode
                    },
                    success  : function(response) {
                        $("#reportUSFinding").html(response);
                        getValueDiv();
                        getValueSpanResult();
                        resetTableResultUSFinding();

                        var reportUSFindingTopPosition = jQuery("#reportUSFinding").offset().top;
                        jQuery("html, body").animate({scrollTop:reportUSFindingTopPosition}, "slow");
                    },
                    error : function(){
                        $("#reportUSFinding").html("Error");
                    }
                });
            }
        }
    });

    $("#btnShowReportByAmphur").click(function(){
        if(active=="monitoring"){
            showMonitorOfAmphur();
        }else{
            var reportUSFindingTopPosition = jQuery("#reportUSFinding").offset().top;
            jQuery("html, body").animate({scrollTop:reportUSFindingTopPosition}, "slow");
            var startDate = $("#inputStartDate").val();
            var endDate = $("#inputEndDate").val();
            var provinceCode = $("#inputProvence").val();
            var amphurCode = $("#inputAmphur").val();
            if( amphurCode == "" ){
                alert("กรุณาเลือก อำเภอที่ต้องการดูรายงาน ");
            } else if((summaryZone == "SummaryZone") && (startDate != "") && (endDate != "") && (provinceCode != "") && (amphurCode != "")){
                summaryArea(null,provinceCode,amphurCode);
            } else if((summaryZone == "Cca01Zone") && (startDate != "") && (endDate != "") && (provinceCode != "") && (amphurCode != "")){
                cca01Area(null,provinceCode,amphurCode);
            }else if((startDate != "") && (endDate != "") && (provinceCode != "") && (amphurCode != "")){
                $("#reportUSFinding").html(\'<div class="row text-center"><i class="fa fa-spinner fa-spin fa-3x" style="margin:50px 0;"></i></div>\');
                $.ajax({
                    type    : "GET",
                    cache   : false,
                    url     : "' . Url::to('/usfinding/default/show-report') . '",
                    data    : {
                        startDate: startDate,
                        endDate: endDate,
                        provinceCode: provinceCode,
                        amphurCode: amphurCode
                    },
                    success  : function(response) {
                        $("#reportUSFinding").html(response);
                        getValueDiv();
                        getValueSpanResult();
                        resetTableResultUSFinding();

                        var reportUSFindingTopPosition = jQuery("#reportUSFinding").offset().top;
                        jQuery("html, body").animate({scrollTop:reportUSFindingTopPosition}, "slow");
                    },
                    error : function(){
                        $("#reportUSFinding").html("Error");
                    }
                });
            }
        }
    });

    function showReportWithChangeOrClick(){
        var reportUSFindingTopPosition = jQuery("#reportUSFinding").offset().top;
        jQuery("html, body").animate({scrollTop:reportUSFindingTopPosition}, "slow");
        var startDate = $("#inputStartDate").val();
        var endDate = $("#inputEndDate").val();
        var provinceCode = $("#inputProvence").val();
        var amphurCode = $("#inputAmphur").val();
        var hospitalCode = $("#inputHospital").val();
        if( hospitalCode == ""){
            // "";
            alert("กรุณาเลือก โรงพยาบาล");
        }else if((startDate != "") && (endDate != "") && (provinceCode != "") && (amphurCode != "") && (hospitalCode != "")){
            $("#reportUSFinding").html(\'<div class="row text-center"><i class="fa fa-spinner fa-spin fa-3x" style="margin:50px 0;"></i></div>\');
            $.ajax({
                type    : "GET",
                cache   : false,
                url     : "' . Url::to('/usfinding/default/show-report') . '",
                data    : {
                    startDate: startDate,
                    endDate: endDate,
                    provinceCode: provinceCode,
                    amphurCode: amphurCode,
                    hospitalCode: hospitalCode
                },
                success  : function(response) {
                    $("#reportUSFinding").html(response);
                    getValueDiv();
                    getValueSpanResult();
                    resetTableResultUSFinding();

                    var reportUSFindingTopPosition = jQuery("#reportUSFinding").offset().top;
                    jQuery("html, body").animate({scrollTop:reportUSFindingTopPosition}, "slow");
                },
                error : function(){
                    $("#reportUSFinding").html("Error");
                }
            });
        }
    }

    $("#btnShowReport").click(function(){
        
        summaryZone = null;
        if(active=="monitoring"){
            showMonitoringFillter();
        }else{
            showReportWithChangeOrClick();
        }
    });

    $("#inputHospital").change(function(){
       if(summaryZone == "SummaryZone")
            showSummaryReport();
       else if(summaryZone == "Cca01Zone")
            showCca01Report();
       else     
            showReportWithChangeOrClick();
    });

    $("#selectUsTour").change(function(){
        $("#reportUSFinding").html(\'<div class="row text-center"><i class="fa fa-spinner fa-spin fa-3x" style="margin:50px 0;"></i></div>\');
        var reportUSFindingTopPosition = jQuery("#reportUSFinding").offset().top;
        jQuery("html, body").animate({scrollTop:reportUSFindingTopPosition}, "slow");
        var hSiteCode = $("#selectUsTour").val();
        //var times = $("#selectUsTour").val(allValue.attr("times"));
        //alert(times);
        if(hSiteCode!=""){
            var thisOption = $(this).val();
            var allValue = $(this).children("option[value=\'"+thisOption+"\']");

            //$("#inputStartDate").val(allValue.attr("sdate").split(" ")[0]);
            //$("#inputEndDate").val(allValue.attr("edate").split(" ")[0]);
            $("#inputStartDate").val(allValue.attr("sdatex").split(" ")[0]);
            $("#inputEndDate").val(allValue.attr("edatex").split(" ")[0]);

            $("#inputZone").val(allValue.attr("zonecode"));

            getProvince(allValue.attr("zonecode"),allValue.attr("provcode"));
            getAmphur(allValue.attr("provcode"), allValue.attr("ampcode"));
            getHospital(allValue.attr("provcode"), allValue.attr("ampcode"), allValue.attr("hsitecode"));

            if(active=="monitoring"){
               setTimeout(function(){showMonitoringFillter();}, 1000);
            }else{
                if((summaryZone == "SummaryZone")||(summaryZone == "Cca01Zone"))
                        stateChange(summaryZone);
                else  {                 
                    $.ajax({
                        type    : "GET",
                        cache   : false,
                        url     : "' . Url::to('/usfinding/default/show-report-in-us-tour') . '",
                        data    : {
                            hSiteCode: hSiteCode,
                        },
                        success  : function(response) {
                            $("#reportUSFinding").html(response);
                            getValueDiv();
                            getValueSpanResult();
                            resetTableResultUSFinding();

                            var reportUSFindingTopPosition = jQuery("#reportUSFinding").offset().top;
                            jQuery("html, body").animate({scrollTop:reportUSFindingTopPosition}, "slow");
                        },
                        error : function(){
                            $("#reportUSFinding").html("Error");
                        }
                    });   
                }
            }
        }
    });
    
    $("#selectSiteUSDist").change(function(){
        
        $("#reportUSFinding").html(\'<div class="row text-center"><i class="fa fa-spinner fa-spin fa-3x" style="margin:50px 0;"></i></div>\');
        var reportUSFindingTopPosition = jQuery("#reportUSFinding").offset().top;
        jQuery("html, body").animate({scrollTop:reportUSFindingTopPosition}, "slow");
        var hSiteCode = $("#selectSiteUSDist").val();
        if(hSiteCode!=""){
            var thisOption = $(this).val();
            var allValue = $(this).children("option[value=\'"+thisOption+"\']");

            //$("#inputStartDate").val(allValue.attr("sdate").split(" ")[0]);
            //$("#inputEndDate").val(allValue.attr("edate").split(" ")[0]);
            $("#inputStartDate").val(allValue.attr("sdatex").split(" ")[0]);
            $("#inputEndDate").val(allValue.attr("edatex").split(" ")[0]);

            $("#inputZone").val(allValue.attr("zonecode"));
            getProvince(allValue.attr("zonecode"),allValue.attr("provcode"));
            getAmphur(allValue.attr("provcode"), allValue.attr("ampcode"));
            getHospital(allValue.attr("provcode"), allValue.attr("ampcode"), allValue.attr("hsitecode"));
            if(active=="monitoring"){
                setTimeout(function(){showMonitoringFillter();}, 1000);
            }else{
                if((summaryZone == "SummaryZone")||(summaryZone == "Cca01Zone"))
                        stateChange(summaryZone);
                else  {
                    $.ajax({
                        type    : "GET",
                        cache   : false,
                        url     : "' . Url::to('/usfinding/default/show-report-in-us-site') . '",
                        data    : {
                            hSiteCode: hSiteCode
                        },
                        success  : function(response) {
                            $("#reportUSFinding").html(response);
                            getValueDiv();
                            getValueSpanResult();
                            resetTableResultUSFinding();

                            var reportUSFindingTopPosition = jQuery("#reportUSFinding").offset().top;
                            jQuery("html, body").animate({scrollTop:reportUSFindingTopPosition}, "slow");
                        },
                        error : function(){
                            $("#reportUSFinding").html("Error selectSiteUSDist");
                        }
                    }); 
                }
            }
        }
        
    });
    
    $(document).on("click", "*[id^=ovlistpatientreport]", function() {
    //alert("x");
        //console.log("x");
        $("#listPatient").html(\'<div class="row text-center"><i class="fa fa-spinner fa-spin fa-3x" style="margin:50px 0;"></i></div>\');
        var listPatientTopPosition = jQuery("#listPatient").offset().top;
        jQuery("html, body").animate({scrollTop:listPatientTopPosition}, "slow");

        var keystore = $(this).attr("keyStore");
        var startdate = $(this).attr("startdate");
        var enddate = $(this).attr("enddate");
        var zone = $(this).attr("zone");
        var province = $(this).attr("province");
        var amphur = $(this).attr("amphur");
        var hospital = $(this).attr("hospital");
        var ovuhospital = $(this).attr("ovuhospital");

        //console.log(keystore);

        $.ajax({
            type    : "GET",
            cache   : false,
            url     : "' . Url::to('/usfinding/default/show-list-patient-report') . '",
            data    : {
                keystore: keystore,
                startdate: startdate,
                enddate: enddate,
                zone: zone,
                province: province,
                amphur: amphur,
                hospital: hospital,
                ovuhospital:ovuhospital,
            },
            success  : function(response) {
                //console.log(response);
                $("#listPatient").html(response);
                exportToExcel();
                var listPatientTopPosition = jQuery("#listPatient").offset().top;
                jQuery("html, body").animate({scrollTop:listPatientTopPosition}, "slow");
            },
            error : function(){
                $("#listPatient").html("");
            }
        });
    });
    $("#btnUsSummary").click(function(){
        summaryZone = "SummaryZone";
 //       console.log(summaryZone);
        showSummaryReport();
    });
    $("#btnCca01").click(function(){
        summaryZone = "Cca01Zone";
  //      console.log(summaryZone);
        showCca01Report();
    });
    function showSummaryReport(){
        var reportUSFindingTopPosition = jQuery("#reportUSFinding").offset().top;
        jQuery("html, body").animate({scrollTop:reportUSFindingTopPosition}, "slow");
        var startDate = $("#inputStartDate").val();
        var endDate = $("#inputEndDate").val();
        var zoneCode = $("#inputZone").val();
        var provinceCode = $("#inputProvence").val();
        var amphurCode = $("#inputAmphur").val();
        var hospitalCode = $("#inputHospital").val();
    //    console.log(startDate+" | "+endDate+" | "+zoneCode+" | "+provinceCode+" | "+amphurCode+" | "+hospitalCode);
        if( hospitalCode == ""){
            // "";
            alert("กรุณาเลือก โรงพยาบาล");
        }else if((startDate != "") && (endDate != "") && (provinceCode != "") && (amphurCode != "") && (hospitalCode != "")){
            $("#reportUSFinding").html(\'<div class="row text-center"><i class="fa fa-spinner fa-spin fa-3x" style="margin:50px 0;"></i></div>\');
            $.ajax({
                type    : "GET",
                cache   : false,
                url     : "' . Url::to('/usfinding/default/summary-report') . '",
                data    : {
                    startDate: startDate,
                    endDate: endDate,
                    zoneCode: zoneCode,
                    provinceCode: provinceCode,
                    amphurCode: amphurCode,
                    hospitalCode: hospitalCode
                },
                success  : function(response) {
                    $("#reportUSFinding").html(response);
                    var reportUSFindingTopPosition = jQuery("#reportUSFinding").offset().top;
                    jQuery("html, body").animate({scrollTop:reportUSFindingTopPosition}, "slow");
                },
                error : function(){
                    $("#reportUSFinding").html("Error");
                }
            });
        } 
    }
    function showCca01Report(){
        var reportUSFindingTopPosition = jQuery("#reportUSFinding").offset().top;
        jQuery("html, body").animate({scrollTop:reportUSFindingTopPosition}, "slow");
        var startDate = $("#inputStartDate").val();
        var endDate = $("#inputEndDate").val();
        var zoneCode = $("#inputZone").val();
        var provinceCode = $("#inputProvence").val();
        var amphurCode = $("#inputAmphur").val();
        var hospitalCode = $("#inputHospital").val();
     //   console.log(startDate+" | "+endDate+" | "+zoneCode+" | "+provinceCode+" | "+amphurCode+" | "+hospitalCode);
        if( hospitalCode == ""){
            // "";
            alert("กรุณาเลือก โรงพยาบาล");
        }else if((startDate != "") && (endDate != "") && (provinceCode != "") && (amphurCode != "") && (hospitalCode != "")){
            $("#reportUSFinding").html(\'<div class="row text-center"><i class="fa fa-spinner fa-spin fa-3x" style="margin:50px 0;"></i></div>\');
            $.ajax({
                type    : "GET",
                cache   : false,
                url     : "' . Url::to('/usfinding/default/cca01-report') . '",
                 data    : {
                    startDate: startDate,
                    endDate: endDate,
                    zoneCode: zoneCode,
                    provinceCode: provinceCode,
                    amphurCode: amphurCode,
                    hospitalCode: hospitalCode
                },
                success  : function(response) {
                    $("#reportUSFinding").html(response);
            

                    var reportUSFindingTopPosition = jQuery("#reportUSFinding").offset().top;
                    jQuery("html, body").animate({scrollTop:reportUSFindingTopPosition}, "slow");
                },
                error : function(){
                    $("#reportUSFinding").html("Error");
                }
            });
            }
        }
        function summaryArea(zoneCode ,provinceCode,amphurCode){
            var startDate = $("#inputStartDate").val();
            var endDate = $("#inputEndDate").val(); 
         $("#reportUSFinding").html(\'<div class="row text-center"><i class="fa fa-spinner fa-spin fa-3x" style="margin:50px 0;"></i></div>\');
            $.ajax({
                type    : "GET",
                cache   : false,
                url     : "' . Url::to('/usfinding/default/doctor-zone') . '",
                data    : {
                    startDate : startDate,
                    endDate : endDate,
                    zoneCode : zoneCode,
                    provinceCode : provinceCode,
                    amphurCode : amphurCode,
                    summaryZone : summaryZone
                },
                success  : function(response) {
                    $("#reportUSFinding").html(response);
                    var reportUSFindingTopPosition = jQuery("#reportUSFinding").offset().top;
                    jQuery("html, body").animate({scrollTop:reportUSFindingTopPosition}, "slow");
                },
                error : function(){
                    $("#reportUSFinding").html("Error");
                }
            });
        }
         function cca01Area(zoneCode ,provinceCode,amphurCode){
            var startDate = $("#inputStartDate").val();
            var endDate = $("#inputEndDate").val(); 
//            console.log("zoneCode | "+zoneCode+" | provinceCode | "+provinceCode+" | amphurCode | "+amphurCode);
         $("#reportUSFinding").html(\'<div class="row text-center"><i class="fa fa-spinner fa-spin fa-3x" style="margin:50px 0;"></i></div>\');
            $.ajax({
                type    : "GET",
                cache   : false,
                url     : "' . Url::to('/usfinding/default/cca01-zone') . '",
                data    : {
                    startDate : startDate,
                    endDate : endDate,
                    zoneCode : zoneCode,
                    provinceCode : provinceCode,
                    amphurCode : amphurCode,
                    summaryZone : summaryZone
                },
                success  : function(response) {
                    $("#reportUSFinding").html(response);
                    var reportUSFindingTopPosition = jQuery("#reportUSFinding").offset().top;
                    jQuery("html, body").animate({scrollTop:reportUSFindingTopPosition}, "slow");
                },
                error : function(){
                    $("#reportUSFinding").html("Error");
                }
            });
        }
       function stateChange(summaryZone) {
            setTimeout(function () {
                if (summaryZone == "SummaryZone")
                    showSummaryReport();
                else 
                    showCca01Report();
            }, 1000); 
        }
');

if (0) {
    echo "<pre align='left'>";
    //print_r($_GET);

    print_r($dfUSFinding);
    echo "</pre>";
}
?>
<?php
$this->registerJs("
$(document).on('click','#ccaModal',function(){
    $('#body-cca').empty();
    var ezf_id = $(this).attr('ezf_id');
    var dataid = $(this).attr('dataid');
    var comp_target = $(this).attr('comp_target');
    var target = $(this).attr('target');
    var comp_id_target = $(this).attr('comp_id_target');
    var readonly = $(this).attr('read');
//     var resultaa = ' ezf_id = ' + ezf_id+
//                    ' dataid = ' + dataid+
//                    ' comp_target = ' + comp_target+
//                    ' target = ' + target+
//                    ' comp_id_target = ' + comp_id_target+
//                    ' reado = ' + readonly;
//console.log(resultaa);
        $('#body-cca').html('<div style=\'text-align:center;color:#fff;\'><i class=\"fa fa-circle-o-notch fa-spin fa-fw fa-3x\"></i></div>');
         $.ajax({
                url : '" . Url::to('/inv/inv-person/ezform-print') . "',
                method :'GET',
                data : {
                    ezf_id : ezf_id,
                    dataid : dataid,
                    comp_target : comp_target,
                    target : target,
                    comp_id_target : comp_id_target,
                    readonly : readonly,
                },
                success:function(result){
                   $('#body-cca').html(result);
                   $('#cca-modal').modal();
                }
            });
    });
");
?>     
<?php
$this->registerJs("
$(document).on('click','#editcca',function(){
    var ezf_id = $(this).attr('ezf_id');
    var dataid = $(this).attr('dataid');
    var data_url = $(this).attr('data_url');
//     var resultaa = ' ezf_id = ' + ezf_id+ ' dataid = ' + dataid;
//console.log(resultaa);
         var win = window.open(data_url,'_blank');
         win.focus();
    });
    
$(document).on('mouseover', '*[id^=data-f2v1-]', function() {
    var controlid = $(this).attr('id');
    var id = $(this).attr('data-id');
    var f2v1 = $(this).attr('data-f2v1');
    var lastid = $('#last-id').attr('id');
    
    $('#last-id').attr('id');
    //console.log(controlid+': '+id+': '+f2v1);
    // check สิทธิ์ //
    if( $('#last-id').attr('data-id')!=id ){
        privilegecheck(id,f2v1);
    }
    
    $('#last-id').attr('data-id',id);
});

function privilegecheck(id,f2v1){
    $.ajax({
        type: 'GET',    
        url: '".Url::to(['default/record-us-privilege'])."', 
        data: { 
            id: id,
            f2v1: f2v1
        }, 
        dataType: 'json',
        success: function (data) {
            //console.log(data);
            var dataid=data.id;
            //var cca02id = data.cca02id;
            var url='".Url::to(['/inputdata/redirect-page','ezf_id'=>'1437619524091524800'])."';
            url = url+'&dataid='+data.cca02id; 
            //console.log(url);
            if( dataid>0 ){
                var objId = 'data-f2v1-'+data.cca02id;
                //console.log(objId);
                
                var txt = '';
                txt = txt+'<a href=\"'+url+'\" ';
                txt = txt+'title=\"'+data.title+' '+data.name+' '+data.surname+'\" ';
                txt = txt+'target=\"_blank\">';
                txt = txt+data.f2v1;
                txt = txt+'</a>';
                $('#'+objId).html(txt);
                //console.log('มีสิทธิ์เข้าดูข้อมูล');
            }else{
                //console.log('ไม่มีสิทธิ์เข้าดูข้อมูล');
            }
        }
    });
}
");
?> 
<?php
$startDate_b = strtotime($startDate);
$startDate = date('Y-m-d', $startDate_b);
$endDate_b = strtotime($endDate);
$endDate = date('Y-m-d', $endDate_b);

$session = \Yii::$app->session;
$table_us = $session['table_us'];
$refresh_time = $session['refresh_time'];
$auto_reload = $session['auto_reload'];

if ($table_us == '')
    $table_us = 'tb_data_3';
if ($refresh_time == '')
    $refresh_time = '5';
if ($auto_reload == null)
    $auto_reload = 'false';


$userId = \Yii::$app->user->identity->id;

//if ($userId == "1435745159010043375" || $userId == "149362211072317500" || $userId == "1435745159010043377" || $userId == "1435745159010041100" || $userId=="1493632211072317500") {
    ?>
    <ul class="nav nav-pills nav-justified">
        <li id="tab-one" style="font-size:18px;" class="<?= $active == "usfinding" || $active == null ? 'active' : '' ?>"><a  href="#" id="tab1"><i class="fa fa-line-chart fa-lg"></i> US Finding</a></li>
        <li id="tab-two" style="font-size:18px;" class="<?= $active == "monitoring" ? 'active' : '' ?>"><a  href="#" id="tab2"><i class="fa fa-desktop fa-lg"></i> Monitoring System</a></li>
        <li id="tab-three" style="font-size:18px;"><a  href="<?= Url::to(['/usfinding/worklist']) ?>" id="tab3"><i class="glyphicon glyphicon-tasks fa-lg"></i> Worklist</a></li>
        <?php 
        if( Yii::$app->user->identity->userProfile->sitecode == '10705' ){
        ?>
        <li id="tab-worklistreg" style="font-size:18px;"><a  href="<?php echo Url::to(['/usfinding/us-mng/monitor', 'usmobile'=>4, 'sort'=>'-update_date' ] ); ?>" id="tab2"><i class="fa fa-desktop fa-lg"></i> Work-list & ข้อมูลที่ลงทะเบียนใหม่</a></li>
        <?php
        }
        ?>
    </ul>

<div class="row formUsFinding">
    <h1><p class="text-center">US Finding</p></h1>
    <form id="formUSFindingReport" class="form-horizontal">
        <div class="col-md-6">
            <h3><p class="text-center">ทำการคัดกรองระหว่างวันที่</p></h3>
            <!--div class="form-group">
                <label for="inputStartDate" class="col-sm-3 col-md-3 control-label">เริ่มวันที่</label>
                <div class="col-sm-9 col-md-9">
                    <input type="date" class="form-control" id="inputStartDated" min="2013-02-09" max="<?= date("Y-m-d"); ?>" value="<?= $dfUSFinding['inputStartDate']; //2013-02-09    ?>" required >
                </div>
                
            </div-->
            <div class="form-group">
                <label for="inputStartDate" class="col-sm-3 col-md-3 control-label">เริ่มวันที่</label>
                <div class="col-sm-9 col-md-9">
                    <?php
                    echo DatePicker::widget([
                        'id' => 'inputStartDate',
                        'name' => 'inputStartDate',
                        'language' => 'th',
                        'dateFormat' => 'dd/MM/yyyy',
                        'value' => Yii::$app->formatter->asDate($dfUSFinding['inputStartDate'], 'php:d/m/Y'),
                        'options' => [
                            'class' => 'form-control',
                        ],
                        'clientOptions' => [
                            'defaultDate' => Yii::$app->formatter->asDate('now', 'php:d/m/Y'),
                            'minDate' => '09/02/2013',
                            'maxDate' => Yii::$app->formatter->asDate('now', 'php:d/m/Y'),
                        ],
                    ]);
                    ?>
                </div>
            </div>
            <!--div class="form-group">
                <label for="inputEndDate" class="col-sm-3 col-md-3 control-label">ถึงวันที่</label>
                <div class="col-sm-9 col-md-9">
                    <input type="date" class="form-control" id="inputEndDate" min="2013-02-09" max="<?= date("Y-m-d") ?>" value="<?= $dfUSFinding['inputEndDate']; //date("Y-m-d")    ?>" required>
                </div>
            </div-->
            <div class="form-group">
                <label for="inputEndDate" class="col-sm-3 col-md-3 control-label">ถึงวันที่</label>
                <div class="col-sm-9 col-md-9">
                    <?php
                    echo DatePicker::widget([
                        'id' => 'inputEndDate',
                        'name' => 'inputEndDate',
                        'language' => 'th',
                        'dateFormat' => 'dd/MM/yyyy',
                        'value' => Yii::$app->formatter->asDate($dfUSFinding['inputEndDate'], 'php:d/m/Y'),
                        'options' => [
                            'class' => 'form-control',
                        ],
                        'clientOptions' => [
                            'defaultDate' => Yii::$app->formatter->asDate('now', 'php:d/m/Y'),
                            'minDate' => '09/02/2013',
                            'maxDate' => Yii::$app->formatter->asDate('now', 'php:d/m/Y'),
                        ],
                    ]);
                    ?>
                </div>
            </div>
            <div class="form-group">
                <label for="inputZone" class="col-sm-3 col-md-3 control-label">เขต</label>
                <div class="col-sm-5 col-md-5">
                    <select class="form-control" id="inputZone">
                        <option value="">เลือกเขต</option>
                        <option value="0">ทุกเขต</option>
                        <?php
                        foreach ($zone as $item) {
                            if ($dfUSFinding['zone_code'] == $item['zone_code']) {
                                echo '<option value="' . $item['zone_code'] . '" selected>เขต ' . $item['zone_code'] . ' : ' . $item['zone_name'] . '</option>';
                            } else {
                                echo '<option value="' . $item['zone_code'] . '">เขต ' . $item['zone_code'] . ' : ' . $item['zone_name'] . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="col-sm-4 col-md-4">
                    <button type="button" class="btn btn-default form-control" id="btnShowReportByZone">แสดงรายงานในเขต</button>
                </div>
            </div>
            <div class="form-group">
                <label for="inputProvence" class="col-sm-3 col-md-3 control-label">จังหวัด</label>
                <div class="col-sm-5 col-md-5">
                    <select class="form-control" id="inputProvence">
                        <option value="">เลือกจังหวัด</option>
                        <?php
                        foreach ($province as $item) {
                            if ($dfUSFinding['provincecode'] == $item['PROVINCE_CODE']) {
                                echo '<option value="' . $item['PROVINCE_CODE'] . '" selected>' . trim($item['PROVINCE_NAME']) . '</option>';
                            } else {
                                echo '<option value="' . $item['PROVINCE_CODE'] . '">' . trim($item['PROVINCE_NAME']) . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="col-sm-4 col-md-4">
                    <button type="button" class="btn btn-default form-control" id="btnShowReportByProvince">แสดงรายงานในเขตจังหวัด</button>
                </div>
            </div>
            <div class="form-group">
                <label for="inputAmphur" class="col-sm-3 col-md-3 control-label">อำเภอ</label>
                <div class="col-sm-5 col-md-5">
                    <select class="form-control" id="inputAmphur" required>
                        <option value="">เลือกอำเภอ</option>
                        <?php
                        if (count($dfUSFinding['amphurlist']) > 0) {
                            foreach ($dfUSFinding['amphurlist'] as $key => $value) {
                                ?>
                                <option value="<?= $dfUSFinding['provincecode'] . $dfUSFinding['amphurlist'][$key]['amphurcode']; ?>" <?php if ($dfUSFinding['amphurcode'] == $dfUSFinding['amphurlist'][$key]['amphurcode']) echo "selected"; ?> >
                                    <?= $dfUSFinding['amphurlist'][$key]['amphur']; ?>
                                </option>
                                <?php
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="col-sm-4 col-md-4">
                    <button type="button" class="btn btn-default form-control" id="btnShowReportByAmphur">แสดงรายงานในเขตอำเภอ</button>
                </div>
            </div>
            <div class="form-group">
                <label for="inputHospital" class="col-sm-3 col-md-3 control-label">หน่วยบริการ</label>
                <div class="col-sm-9 col-md-9">
                    <select class="form-control" id="inputHospital" required>
                        <option value="">เลือกหน่วยบริการ</option>
                        <?php
                        if (count($dfUSFinding['hospitallist']) > 0) {
                            foreach ($dfUSFinding['hospitallist'] as $key => $value) {
                                ?>
                                <option value="<?= $dfUSFinding['hospitallist'][$key]['hcode']; ?>" <?php if ($dfUSFinding['sitecode'] == $dfUSFinding['hospitallist'][$key]['hcode']) echo "selected"; ?> >
                                    <?= $dfUSFinding['hospitallist'][$key]['hcode'] . ": " . $dfUSFinding['hospitallist'][$key]['name']; ?>
                                </option>
                                <?php
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="inputHospital" class="col-sm-3 col-md-3 control-label"></label>
                <div class="col-sm-9 col-md-9">
                    <button type="button" class="btn btn-primary form-control" id="btnShowReport">Show report</button>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 col-md-3 control-label"></label>
                <div class="col-sm-3 col-md-3">
                    <button type="button" class="btn btn-primary form-control" id="btnUsSummary">US Summary</button>
                </div>
                <div class="col-sm-3 col-md-3">
                    <button type="button" class="btn btn-primary form-control" id="btnCca01">CCA01</button>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <?php
            if (0) {
                echo "<pre align='left'>";
                print_r($usTour);
                echo "</pre>";
            }
            ?>
            <h3><p class="text-center">เลือกตามการออกสัญจร</p></h3>
            <div class="form-group">
                <label for="selectUsTour" class="col-sm-3 col-md-3 control-label">การออกสัญจร</label>
                <div class="col-sm-9 col-md-9">
                    <select class="form-control" id="selectUsTour" >
                        <option value="">สามารถเลือกได้ตามครั้งการออกสัญจร</option>
                        <?php
                        foreach ($usTour as $item) {
                            echo '<option value="' . $item['hcode'] . ':' . $item['times'];
                            echo '" times="' . $item['times'];
                            echo '" hsitecode="' . $item['hcode'];
                            echo '" zonecode="' . $item['zonecode'];
                            echo '" provcode="' . $item['provcode'];
                            echo '" ampcode="' . $item['ampcode'];
                            echo '" sdate="' . $item['sdate'];
                            echo '" sdatex="' . Yii::$app->formatter->asDate($item['sdate'], 'php:d/m/Y');
                            echo '" edate="' . $item['edate'];
                            echo '" edatex="' . Yii::$app->formatter->asDate($item['edate'], 'php:d/m/Y');
                            echo '">';
                            echo iconv('tis620', 'UTF-8', $item['name']);
                            echo '</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>
            <?php
            if (Yii::$app->user->can('doctorcascap') == TRUE || Yii::$app->user->can('administrator') == TRUE || Yii::$app->user->can('sitemanager') == TRUE
            ):
                ?>
                <h3><p class="text-center">หน่วยบริการที่ได้รับสนับสนุนเครื่อง US</p></h3>
                <div class="form-group">
                    <label for="selectSiteUSDist" class="col-sm-3 col-md-3 control-label">หน่วยบริการ</label>
                    <div class="col-sm-9 col-md-9">
                        <select class="form-control" id="selectSiteUSDist" >
                            <option value="">เลือกหน่วยบริการ</option>
                            <?php
                            $irow = 1;
                            foreach ($usSite as $item) {
                                echo '<option value="' . $item['hcode'] . ':' . $item['No'];
                                echo '" hsitecode="' . $item['hcode'];
                                echo '" zonecode="' . $item['zonecode'];
                                echo '" provcode="' . $item['provcode'];
                                echo '" ampcode="' . $item['ampcode'];
                                echo '" sdate="' . $item['dateatsite'];
                                echo '" sdatex="' . Yii::$app->formatter->asDate($item['dateatsite'], 'php:d/m/Y');
                                echo '" edate="' . substr($item['edate'], 0, 10);
                                echo '" edatex="' . Yii::$app->formatter->asDate(substr($item['edate'], 0, 10), 'php:d/m/Y');
                                echo '">';
                                echo $irow . '. ' . $item['hcode'] . ': ' . $item['hospitalname'] . ' ';
                                echo '</option>';

                                $irow++;
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div style="alignment-adjust: right;">
                    <center>
                        <button style="alignment-adjust: central;" type="button" class="btn btn-info" onclick="javascript:window.open('/teleradio/siteus/list', '_self')">
                            <i class="fa fa-file-excel-o" aria-hidden="true" style="color: green"></i>
                            แสดงสถิติภาพรวม
                        </button>

                        <button style="alignment-adjust: central;" type="button" class="btn btn-info" onclick="javascript:window.open('/teleradio/suspected/list-suspected', '_self')">
                            <i class="glyphicon glyphicon-pushpin" aria-hidden="true" style="color: red"></i>
                            แสดงข้อมูล Suspected ทั้งหมด
                        </button>

                        <button style="alignment-adjust: central;" type="button" class="btn btn-info" onclick="javascript:window.open('/teleradio/suspected/data', '_blank')">
                            <i class="glyphicon glyphicon-compressed" aria-hidden="true" style="color: blue"></i>
                            จัดการข้อมูล
                        </button>
                    </center>
                </div>
                <?php
            endif;
            ?>
        </div>
    </form>
</div>
<div class="allReport" id="allReport">
    <div class="row reportUSFinding" id="reportUSFinding">
        <!--    <div class="valueOfUltrasonoTableFinding">-->
        <!--        <div class="tableReportUltrasonoTableFinding">-->
        <!--            <table border="1" class="table-hover reportUltrasonoTableFinding">-->
        <!--                <thead class="tHeadReportUltrasonoTableFinding">-->
        <!--                <tr>-->
        <!--                    <th rowspan="2">รูปแบบที่</th>-->
        <!--                    <th colspan="3">ครั้งที่ตรวจ</th>-->
        <!--                    <th rowspan="2">จำนวน</th>-->
        <!--                </tr>-->
        <!--                <tr>-->
        <!--                    <th>1</th>-->
        <!--                    <th>2</th>-->
        <!--                    <th>3</th>-->
        <!--                </tr>-->
        <!--                </thead>-->
        <!--                <tbody class="tBodyReportUltrasonoTableFinding">-->
        <!--                <tr>-->
        <!--                    <td>1</td>-->
        <!--                    <td>-</td>-->
        <!--                    <td>-</td>-->
        <!--                    <td>-</td>-->
        <!--                    <td>-</td>-->
        <!--                </tr>-->
        <!--                <tr>-->
        <!--                    <td>2</td>-->
        <!--                    <td>-</td>-->
        <!--                    <td>-</td>-->
        <!--                    <td>-</td>-->
        <!--                    <td>-</td>-->
        <!--                </tr>-->
        <!--                <tr>-->
        <!--                    <td>3</td>-->
        <!--                    <td>-</td>-->
        <!--                    <td>-</td>-->
        <!--                    <td>-</td>-->
        <!--                    <td>-</td>-->
        <!--                </tr>-->
        <!--                </tbody>-->
        <!--            </table>-->
        <!--        </div>-->
        <!--        <div class="annotation">-->
        <!--            <dl class="dl-horizontal">-->
        <!--                <dt>No :</dt><dd>Normal</dd>-->
        <!--                <dt>F1 :</dt><dd>Mild fatty liver (Abnormal)</dd>-->
        <!--                <dt>F2 :</dt><dd>Moderate fatty liver (Abnormal)</dd>-->
        <!--                <dt>F3 :</dt><dd>Severe fatty liver (Abnormal)</dd>-->
        <!--                <dt>P1 :</dt><dd>PDF1 (Abnormal)</dd>-->
        <!--                <dt>P2 :</dt><dd>PDF2 (Abnormal)</dd>-->
        <!--                <dt>P3 :</dt><dd>PDF3 (Abnormal)</dd>-->
        <!--                <dt> C :</dt><dd>Cirrhosis (Abnormal)</dd>-->
        <!--                <dt>Pa :</dt><dd>Parenchymal change (Abnormal)</dd>-->
        <!--            </dl>-->
        <!--        </div>-->
        <!--    </div>-->
    </div>
    <div class="row listPatientOfUltrasonoGraphicFinding" id="listPatientOfUltrasonoGraphicFinding">
        <div class="btnControl"></div>
        <div class="listPatient" id="listPatient"></div>
    </div>
</div>

<!-- Modal Setting-->
<div id="modal-setting" class="modal fade" role="dialog" >
    <div class="modal-dialog">
        <div class="modal-content">

        </div>
    </div>
</div>

<!-- Modal choose doctor-->
<div id="modal-doctor" class="modal fade" role="dialog" >
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:5px 5px 5px 5px ;">

        </div>
    </div>
</div>
<!-- Modal choose personal-->
<div id="modal-personal-detail" class="modal fade" role="dialog" >
    <div class="modal-dialog">
        <div class="modal-content">
        </div>
    </div>
</div>

<div id="load-spinner" class="modal fade " data-backdrop="static" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">แจ้งเตือน!</h4>
            </div>
            <div class="modal-body">
                <label class="load-text" style="width: 100%;font-size: 20px;">

                </label>
            </div>
            <div class="modal-footer">

            </div>
        </div>
    </div>
</div>
<?php

$this->registerJs("
        function monitorActive(){
            console.log(active);
            if(active=='monitoring'){
                window.isActive = true;
                var intervalObj;
                var intervalTime = ($refresh_time)*1000;
                var isMon = '$isMonitor';
                
                    $(window).focus(function() { 
                        this.isActive = true; 
                        clearInterval(intervalObj);
                    });
                    $(window).blur(function() { 
                        this.isActive = false; 
                        if(isMon=='true'){
                            intervalObj= setInterval(function(){reloadMonitor();}, intervalTime);
                        }else{
                             clearInterval(intervalObj);
                        }
                    });

                showMonitoringFillter();

                $('#btnShowMonitor').click(function(){
                   showMonitoringFillter();
                   clearInterval(intervalObj);
                });
                $('#tab1').click(function(){
                    tabCurrent=1;
                });
              
            }
        }
        $(function(){
            monitorActive();
        });

    function reloadMonitor(){
        var isMon = '$isMonitor';
        var monDiv = $('#reportUSFinding');
        var startdate = $('#inputStartDate').val();
        var enddate = $('#inputEndDate').val();
        var hospital = $('#inputHospital').val();
        if(isMon=='true'){
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
    }
    
    function showMonitoringFillter(){
        var monDiv = $('#reportUSFinding');
        var startdate = $('#inputStartDate').val();
        var enddate = $('#inputEndDate').val();
        var hospital = $('#inputHospital').val();
        $('#listPatient').empty();
        
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
        var hospital = $('#inputHospital').val();
        $('#listPatient').empty();
        
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
    
    function showMonitorOfZone(){
    var startdate = $('#inputStartDate').val();
        var enddate = $('#inputEndDate').val();
        var monDiv = $('#reportUSFinding');
        var zone = $('#inputZone').val();
        monDiv.html('<div style=\'text-align:center;\'><i class=\"fa fa-spinner fa-pulse fa-fw fa-3x\"></i></div>');
        $.ajax({
            url:'" . Url::to('/usfinding/monitoring/ultrasound-data') . "',
            method:'post',
            data:{
                startDate:startdate,
                endDate:enddate,
                zone:zone
            },
            type:'HTML',
            success:function(result){
                monDiv.empty();
                monDiv.html(result);
            }
        });
    }
    
    function showMonitorOfProvince(){
    var startdate = $('#inputStartDate').val();
        var enddate = $('#inputEndDate').val();
        var monDiv = $('#reportUSFinding');
        var province = $('#inputProvince').val();
        monDiv.html('<div style=\'text-align:center;\'><i class=\"fa fa-spinner fa-pulse fa-fw fa-3x\"></i></div>');
        $.ajax({
            url:'" . Url::to('/usfinding/monitoring/ultrasound-data') . "',
            method:'post',
            data:{
                startDate:startdate,
                endDate:enddate,
                province:province
            },
            type:'HTML',
            success:function(result){
                monDiv.empty();
                monDiv.html(result);
            }
        });
    }
    function showMonitorOfAmphur(){
        var startdate = $('#inputStartDate').val();
            var enddate = $('#inputEndDate').val();
            var monDiv = $('#reportUSFinding');
            var amphur = $('#inputAmphur').val();
            var province = $('#inputProvince').val();
            monDiv.html('<div style=\'text-align:center;\'><i class=\"fa fa-spinner fa-pulse fa-fw fa-3x\"></i></div>');
            $.ajax({
                url:'" . Url::to('/usfinding/monitoring/ultrasound-data') . "',
                method:'post',
                data:{
                    startDate:startdate,
                    endDate:enddate,
                    amphur:amphur,
                    province:province
                },
                type:'HTML',
                success:function(result){
                    monDiv.empty();
                    monDiv.html(result);
                }
            });
        }
       
    ");
?>
