<?php

use Yii;
use yii\helpers\Url;
use yii\jui\DatePicker;
?>
<style>
    .panel-success2 .panel-heading2{
        background: #00A21E;
        color: #fff;
        padding-top:20px;
        padding-left:20px;
        padding-right:20px;
    }
</style>

<div class="col-md-12">
    <?php
//echo Yii::$app->getRequest()->url;
    if (Yii::$app->getRequest()->url == '/usfinding/default/usfinding') {
        $this->registerCssFile('https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css');
    }

    $loadIconData = '\'<i class="fa fa-spinner fa-spin fa-3x" style="margin:50px 0;"></i>\'';
    $this->registerCssFile('/css/usfinding.css');
    $this->registerJsFile('/js/jsdad/jquery.min.js', ['position' => \yii\web\View::POS_BEGIN]);
    $this->registerJsFile('/js/FileSaver.js', ['position' => \yii\web\View::POS_BEGIN]);
    $this->registerJsFile('/js/jquery.wordexport.js', ['position' => \yii\web\View::POS_BEGIN]);
    $this->registerJsFile('/js/excellentexport.js', ['position' => \yii\web\View::POS_BEGIN]);
    $this->registerJs('
    
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
                $("#inputProvince").html(response);
                if(value!=""){
                    $("#inputProvince").val(value);
                }
            },
            error : function(){
                $("#inputProvince").html("");
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

    $("#inputProvince").change(function(){
        if( $("#inputProvince").val() != "" ){
            getAmphur($("#inputProvince").val(),"");
        }
    });

    $("#inputAmphur").change(function(){
        if( $("#inputProvince").val()!="" && $("#inputAmphur").val()!="" ){
            getHospital($("#inputProvince").val(),$("#inputAmphur").val(),"");
        }
    });

    $("#btnShowReportByZone").click(function(){
        showMonitorOfZone();
    });

    $("#btnShowReportByProvince").click(function(){
        showMonitorOfProvince();
    });

    $("#btnShowReportByAmphur").click(function(){
        showMonitorOfAmphur();
    });

    function showReportWithChangeOrClick(){
        //var reportUSFindingTopPosition = jQuery("#reportUSFinding").offset().top;
        //jQuery("html, body").animate({scrollTop:reportUSFindingTopPosition}, "slow");
        var startDate = $("#inputStartDate").val();
        var endDate = $("#inputEndDate").val();
        var provinceCode = $("#inputProvince").val();
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
        showReportWithChangeOrClick();
    });

    $("#inputHospital").change(function(){
        showReportWithChangeOrClick();
    });

    $("#selectUsTour").change(function(){
        //$("#reportUSFinding").html(\'<div class="row text-center"><i class="fa fa-spinner fa-spin fa-3x" style="margin:50px 0;"></i></div>\');
        //var reportUSFindingTopPosition = jQuery("#reportUSFinding").offset().top;
        //jQuery("html, body").animate({scrollTop:reportUSFindingTopPosition}, "slow");
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
            
            setTimeout(function(){showMonitoringFillter();}, 1000);
            
        }
    });
    $("#selectSiteUSDist").change(function(){
        //$("#reportUSFinding").html(\'<div class="row text-center"><i class="fa fa-spinner fa-spin fa-3x" style="margin:50px 0;"></i></div>\');
        //var reportUSFindingTopPosition = jQuery("#reportUSFinding").offset().top;
        //jQuery("html, body").animate({scrollTop:reportUSFindingTopPosition}, "slow");
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

                    //var reportUSFindingTopPosition = jQuery("#reportUSFinding").offset().top;
                    //jQuery("html, body").animate({scrollTop:reportUSFindingTopPosition}, "slow");
                },
                error : function(){
                    $("#reportUSFinding").html("Error selectSiteUSDist");
                }
            });
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

');

    if (0) {
        echo "<pre align='left'>";
        //print_r($_GET);

        echo "</pre>";
    }
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
    ?>
    <ul class="nav nav-pills nav-justified">
        <li id="tab-one" style="font-size:18px;"><a  href="<?= Url::to(['/usfinding/']) ?>" id="tab1"><i class="fa fa-line-chart fa-lg"></i> US Finding</a></li>
        <li id="tab-two" style="font-size:18px;" ><a  href="<?= Url::to(['/usfinding/worklist']) ?>" id="tab0"><i class="glyphicon glyphicon-tasks fa-lg"></i>Worklist</a></li>
        <li id="tab-two" style="font-size:18px;" class="active"><a data-toggle="tab" href="#modules" id="tab2"><i class="fa fa-desktop fa-lg"></i> Monitoring System</a></li>

        <li id="tab-three" style="font-size:18px;"><a data-toggle="tab" href="#modules" id="tab3"><i class="fa fa-users fa-lg"></i> Patient</a></li>
        <li id="tab-four" style="font-size:18px;"><a data-toggle="tab" href="#refer_list" id="tab4"><i class="fa fa-truck fa-lg"></i> Refer List</a></li>
    </ul>
    <div class="row formUsFinding">
        <h1><p class="text-center">Monitoring System</p></h1>
        <form id="formUSFindingReport" class="form-horizontal">
            <div class="col-md-6">
                <h3><p class="text-center">ทำการคัดกรองระหว่างวันที่</p></h3>
                <!--div class="form-group">
                    <label for="inputStartDate" class="col-sm-3 col-md-3 control-label">เริ่มวันที่</label>
                    <div class="col-sm-9 col-md-9">
                        <input type="date" class="form-control" id="inputStartDated" min="2013-02-09" max="<?= date("Y-m-d"); ?>" value="<?= $dfUSFinding['inputStartDate']; //2013-02-09            ?>" required >
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
                            'value' => Yii::$app->formatter->asDate($startDate == '' ? $dfUSFinding['inputStartDate'] : $startDate, 'php:Y/m/d'),
                            'options' => [
                                'class' => 'form-control',
                            ],
                            'clientOptions' => [
                                'defaultDate' => Yii::$app->formatter->asDate('now', 'php:Y/m/d'),
                                'minDate' => '09/02/2013',
                                'maxDate' => Yii::$app->formatter->asDate('now', 'php:Y/m/d'),
                            ],
                        ]);
                        ?>
                    </div>
                </div>
                <!--div class="form-group">
                    <label for="inputEndDate" class="col-sm-3 col-md-3 control-label">ถึงวันที่</label>
                    <div class="col-sm-9 col-md-9">
                        <input type="date" class="form-control" id="inputEndDate" min="2013-02-09" max="<?= date("Y-m-d") ?>" value="<?= $dfUSFinding['inputEndDate']; //date("Y-m-d")            ?>" required>
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
                            'value' => Yii::$app->formatter->asDate($endDate == '' ? $dfUSFinding['inputEndDate'] : $endDate, 'php:Y/m/d'),
                            'options' => [
                                'class' => 'form-control',
                            ],
                            'clientOptions' => [
                                'defaultDate' => Yii::$app->formatter->asDate('now', 'php:Y/m/d'),
                                'minDate' => '09/02/2013',
                                'maxDate' => Yii::$app->formatter->asDate('now', 'php:Y/m/d'),
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
                    <label for="inputProvince" class="col-sm-3 col-md-3 control-label">จังหวัด</label>
                    <div class="col-sm-5 col-md-5">
                        <select class="form-control" id="inputProvince">
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
                                    <option value="<?= $dfUSFinding['amphurlist'][$key]['amphurcode']; ?>" <?php if ($dfUSFinding['amphurcode'] == $dfUSFinding['amphurlist'][$key]['amphurcode']) echo "selected"; ?> >
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
                        <button type="button" class="btn btn-primary form-control" id="btnShowMonitor">Show monitor</button>
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
    <br/>
    <!-- ส่วนแสดงผล -->

    <div id="contain-show" class="col-md-12" >

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

</div>

<?php
$isMonitor = $session['auto_reload'];
$this->registerJs("

    $(function(){
        window.isActive = true;
        var intervalObj;
        var tabCurrent=2;
        var intervalTime = ($refresh_time)*1000;
        var isMon = '$isMonitor';
        console.log(isMon);
            $(window).focus(function() { 
                this.isActive = true; 
                clearInterval(intervalObj);
            });
            $(window).blur(function() { 
                this.isActive = false; 
                if(tabCurrent==2 && isMon=='true'){
                    intervalObj= setInterval(function(){reloadMonitor();}, intervalTime);
                }else{
                     clearInterval(intervalObj);
                }
            });
        
        showMonitoring();
       
        $('#btnShowMonitor').click(function(){
           showMonitoringFillter();
           clearInterval(intervalObj);
        });
        $('#tab1').click(function(){
            tabCurrent=1;
            
        });

        $('#tab2').click(function(){
            tabCurrent=2;
            showMonitoringFillter();
            clearInterval(intervalObj);
        });

        $('#tab3').click(function(){
            tabCurrent=3;
            showPatients();
            clearInterval(intervalObj);
        });

        $('#tab4').click(function(){
            tabCurrent=4;
            showReferList();
            clearInterval(intervalObj);
        });
    });
    
    function reloadMonitor(){
        var isMon = '$isMonitor';
        var monDiv = $('#contain-show');
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
        var monDiv = $('#contain-show');
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
        var monDiv = $('#contain-show');
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
        var monDiv = $('#contain-show');
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
        var monDiv = $('#contain-show');
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
        var monDiv = $('#contain-show');
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
    
    function showOverview(){
        var monDiv = $('#contain-show');
        var startdate = $('#inputStartDate').val();
        var enddate = $('#inputEndDate').val();
        monDiv.html('<i class=\"fa fa-spinner fa-pulse fa-fw fa-3x\"></i>');
        $.ajax({
            url:'" . Url::to('/usfinding/monitoring/ultrasound-data') . "',
            method:'post',
            data:{
                startDate:startdate,
                endDate:enddate
            },
            type:'HTML',
            success:function(result){
                monDiv.empty();
                monDiv.html(result);
            }
        });
    }
    function showPatients(){
        var monDiv = $('#contain-show');
        var startdate = $('#inputStartDate').val();
        var enddate = $('#inputEndDate').val();
        monDiv.html('<div style=\'text-align:center;\'><i class=\"fa fa-spinner fa-pulse fa-fw fa-3x\"></i></div>');
        $.ajax({
            url:'" . Url::to('/usfinding/monitoring/patient-view') . "',
            method:'post',
            data:{
                startDate:startdate,
                endDate:enddate
            },
            type:'HTML',
            success:function(result){
                monDiv.empty();
                monDiv.html(result);
            }
        });
    }
    function showReferList(){
        var monDiv = $('#contain-show');
        var startdate = $('#inputStartDate').val();
        var enddate = $('#inputEndDate').val();
        monDiv.html('<div style=\'text-align:center;\'><i class=\"fa fa-spinner fa-pulse fa-fw fa-3x\"></i></center>');
        $.ajax({
            url:'" . Url::to('/usfinding/monitoring/refer-list') . "',
            method:'post',
            data:{
                startDate:startdate,
                endDate:enddate
            },
            type:'HTML',
            success:function(result){
                monDiv.empty();
                monDiv.html(result);
            }
        });
    }
");

