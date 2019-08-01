<?php
// start widget builder
/** @var \yii\web\View $this */

/* Params widget สามารถใช้งาน ตัวแปรต่อไปนี้ได้
'options' => $options,
'widget_config' => $widget_config,
'model' => $model,
'modelOrigin'=>$modelOrigin,
'menu' => $menu,
'module' => $module,
'addon' => $addon,
'filter' => $filter,
'reloadDiv' => $reloadDiv,
'dataFilter' => $dataFilter,
'modelFilter' => $modelFilter,
'target' => $target,
    */

use backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\patient\classes\PatientQuery;
use backend\modules\thaihis\models\HealthType;
use yii\helpers\Url;

function getPrice($orderListRecords, $key)
{
    return isset($orderListRecords[$key]) ? $orderListRecords[$key] : 0;
}

$url = Url::to(['/thaihis/patient-visit/submit-visit-checkup']);
$healthTypeRecords = HealthType::find()->where(['not in', 'rstat', [0, 3]])->asArray()->all();
$tempOrderListRecords = (new \yii\db\Query)->select(['id', 'unit_price_checkup'])->from('zdata_order_lists')->where(['not in', 'rstat', [0, 3]])->all();
$orderListRecords = [];
$target = Yii::$app->request->get('target', null);
$projectName = '-';
if ($target != null) {
    $projectRecord = PatientQuery::getprojectidByptid($target);
    if ($projectRecord) {
        $projectName = $projectRecord['project_name'];
    }
}
$projectName = trim(preg_replace('/\s+/', ' ', $projectName));


foreach ($tempOrderListRecords as $value) {
    $orderListRecords[$value['id']] = $value['unit_price_checkup'];
}
foreach ($healthTypeRecords as $key => $value) {
    $total_price = 0;
    $total_price += getPrice($orderListRecords, $healthTypeRecords[$key]['order_ref']);
    $total_price += getPrice($orderListRecords, $healthTypeRecords[$key]['order_ref_02']);
    $total_price += getPrice($orderListRecords, $healthTypeRecords[$key]['order_ref_03']);
    $total_price += getPrice($orderListRecords, $healthTypeRecords[$key]['order_ref_04']);
    $total_price += getPrice($orderListRecords, $healthTypeRecords[$key]['order_ref_05']);
    $total_price += getPrice($orderListRecords, $healthTypeRecords[$key]['order_ref_06']);
    $total_price += getPrice($orderListRecords, $healthTypeRecords[$key]['order_ref_07']);
    $total_price += getPrice($orderListRecords, $healthTypeRecords[$key]['order_ref_08']);
    $healthTypeRecords[$key]['total_price'] = $total_price;
}
$errorNotify = \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"');
$jsonData = json_encode($healthTypeRecords);
$target = Yii::$app->request->get("target");
// Patient Profile
$modelEzfTarget = EzfQuery::getEzformOne('1503378440057007100');
$dataTarget = EzfQuery::getTargetNotRstat($modelEzfTarget->ezf_table, $target);
$visit_id = Yii::$app->request->get("visit_id");
$gender = $dataTarget['pt_sex'];
$insurePlan = Yii::$app->request->get("insure_plan", 2);
$callbackFunc = isset($options['callback']) ? $options['callback'] : 'null';

$age = 0;//$dataTarget['pt_bdate'];
if ($dataTarget['pt_bdate'] != null) {
    $birthDate = explode("-", $dataTarget['pt_bdate']);
    $age = (date("md", date("U", mktime(0, 0, 0, $birthDate[2], $birthDate[1], $birthDate[0]))) > date("md") ? ((date("Y") - $birthDate[0]) - 1) : (date("Y") - $birthDate[0]));
}


?>
    <div class="modal fade" id="myModal3" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped active" role="progressbar"
                             aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width:100%">
                            Loading
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalPanicRequire" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped active" role="progressbar"
                             aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width:100%">

                        </div>
                    </div>
                    <h3><u>ต้องการ อายุ และ เพศ ของผู้ใช้งาน</u> กรุณาตรวจสอบวันเกิด และ เพศ ในหน้าลงทะเบียน
                    </h3>
                    <p>ถอดบัตร และ ลองอีกครั้ง หรือ ปิดหน้านี้</p>
                </div>
            </div>
        </div>
    </div>


    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.7.5/angular.min.js"></script>
    <style>
        .insurePlanText {
            background-color: rgb(155, 187, 89);
            color: rgb(255, 255, 255);
        }
    </style>
    <div class="panel panel-info" ng-app="myApp" ng-controller="myCtrl" ng-cloak>
        <div class="panel-heading" style="padding: 4px 15px;">
            <div class="row">
                <div class="col-md-6">
                    <h4>Checkup Selector</h4>
                </div>
                <div class="col-md-6 text-right" style="padding-top: 5px;">
                    <button ng-if="permission" class="btn btn-success btn-sm ezform-main-open"
                            data-modal="modal-ezform-main"
                            data-url="/ezforms2/ezform-data/ezform?ezf_id=1537423458060997900&modal=modal-ezform-main&reloadDiv=&target=&dataid=&targetField=&version=&db2=0&initdata=">
                        <span class="glyphicon glyphicon-plus"></span></button>
                </div>
            </div>
        </div>
        <div class="panel-body">
            <h4 style="text-align:right;" ng-if="healthInsurePlan == '1'">หน่วยงาน: <?= $projectName ?></h4>
            <div class="form-group">
                <label class="control-label" for="">สิทธิที่ใช้การตรวจสุขภาพครั้งนี้</label>
                <br>
                <label class="radio-inline">
                    <input type="radio" ng-model="healthInsurePlan" value="2">
                    <span>ชำระเงินเอง  </span>
                </label>
                <label class="radio-inline">
                    <input type="radio" ng-model="healthInsurePlan" value="3">
                    <span>ข้าราชการ ส่วนท้องถิ่น  </span>
                </label>
                <label class="radio-inline">
                    <input type="radio" ng-model="healthInsurePlan" value="4">
                    <span>รัฐวิสาหกิจ  </span> </label>
                <label class="radio-inline">
                    <input type="radio" ng-model="healthInsurePlan" value="5">
                    <span>ต้นสังกัด (ชำระเงินเอง)  </span>
                </label>
                <label class="radio-inline">
                    <input type="radio" ng-model="healthInsurePlan" value="1">
                    <span>ตรวจสุขภาพหน่วยงาน  </span>
                </label>
            </div>
            <hr>
            <div class="row">
                <div ng-repeat="(key, value) in healthData"
                     ng-if="value['enable_gender'] == '3' || value['enable_gender'] == gender "
                     class='col-md-6 check-record'>
                    <label for='health-{{value["id"]}}'>
                        <input type='checkbox' id='health-{{value["id"]}}' ng-change="onChecked(value)"
                               ng-model="value.isChecked" data-type='checkbox'>
                        <span>{{value['type_name']}} {{value['total_price']}} บาท</span>
                        <span class="insurePlanText"
                              ng-if="healthInsurePlan == '2' && value.isCover">{{value['enable_cash_text']}}</span>
                        <!--                        <span style="background-color:red;color: white"-->
                        <!--                              ng-if="(healthInsurePlan == '3' || healthInsurePlan == '1' ) && !value.isCover"><del>{{value['enable_officer_text']}}</del></span>-->
                        <span class="insurePlanText"
                              ng-if="(healthInsurePlan == '3' || healthInsurePlan == '1' ) && value.isCover">{{value['enable_officer_text']}}</span>
                        <span class="insurePlanText" ng-if="healthInsurePlan == '4'">{{value['enable_gov_text']}}</span>
                        <span class="insurePlanText"
                              ng-if="healthInsurePlan == '5' && value.isCover">{{value['enable_company_text']}}</span>
                    </label>
                    <button ng-if="permission" class="btn btn-primary btn-xs ezform-main-open"
                            data-modal="modal-ezform-main"
                            data-url="/ezforms2/ezform-data/ezform?ezf_id=1537423458060997900&modal=modal-ezform-main&reloadDiv=&target=&dataid={{value['id']}}&targetField=&version=&db2=0&initdata=">
                        <span class="glyphicon glyphicon-pencil"></span>
                    </button>
                </div>

            </div>
            <br>
            <h1 style="text-align:right; color: #208437"> เบิกได้ <u>{{currentSavePrice}}</u> บาท</h1>
            <h1 style="text-align:right;">ค่าใช้จ่ายทั้งหมด <u>{{currentPrice}}</u> บาท</h1>
            <button class="btn btn-primary" id="button-submit-checkup" ng-click="prepareData()"> ยืนยัน</button>
        </div>
    </div>

    <script>
        var app = angular.module('myApp', []);
        app.directive('myCustomer', function () {
            let html = '';
            return {
                restrict: 'E',
                scope: {
                    id: '=id'
                },
                template: function ($scope) {
                    return html;
                    // $element.append('asd');
                }
            };
        });
        app.controller('myCtrl', function ($scope) {
            let userAge = '<?=$age?>';
            // gender // 1 both // 2 male // 3 female
            $scope.permission = true;
            $scope.userId = '<?=Yii::$app->user->id;?>';
            $scope.userAge = userAge;
            $scope.permission = $scope.userId === '1';
            $scope.gender = '<?=$gender?>';
            $scope.healthData = <?=$jsonData?>;
            $scope.healthInsurePlan = '<?=$insurePlan?>';
            $scope.currentPlan = '';
            $scope.currentProject = '<?=$projectName?>';
            console.log($scope.currentProject);
            // console.log( $scope.healthData );
            $scope.currentPrice = 0;
            console.log($scope.healthData);
            if ($scope.gender == null || $scope.gender === '' || $scope.userAge == null || $scope.userAge === 0 || $scope.userAge === '') {
                $("#modalPanicRequire").modal({backdrop: "static"});
            }

            if ($scope.currentProject != '-') {
                $scope.healthInsurePlan = '1';
            }
            $scope.onChecked = function (value) {
                if (value.isChecked) {
                    const resPrices = checkPrice(value);
                    $scope.currentPrice += resPrices[0];
                    $scope.currentSavePrice += resPrices[1];
                    if (value['require_id'] != null && value['require_id'] !== '') {
                        for (let key in $scope.healthData) {
                            if ($scope.healthData[key]['id'] === value['require_id']) {
                                if (!$scope.healthData[key]['isChecked']) {
                                    $scope.healthData[key]['isChecked'] = true;
                                    $scope.onChecked($scope.healthData[key]);
                                    break;
                                }
                            }
                        }
                    }

                    if (value['unique_code'] != null && value['unique_code'] !== '') {
                        for (let key in $scope.healthData) {
                            if ($scope.healthData[key]['id'] === value['id']) {
                                continue;
                            }
                            if ($scope.healthData[key]['unique_code'] === value['unique_code']) {
                                if ($scope.healthData[key]['isChecked']) {
                                    $scope.healthData[key]['isChecked'] = false;
                                    $scope.onChecked($scope.healthData[key]);
                                }
                            }
                        }
                    }

                    if (value['minimum_default'] != null && value['minimum_default'] !== '') {
                        for (let key in $scope.healthData) {
                            if ($scope.healthData[key]['id'] === value['minimum_default']) {
                                $scope.healthData[key]['isChecked'] = true;
                                console.log($scope.healthData[key])
                                $scope.onChecked($scope.healthData[key]);
                                break;
                            }
                        }
                    }
                } else {
                    const resPrices = checkPrice(value);
                    $scope.currentPrice -= resPrices[0];
                    $scope.currentSavePrice -= resPrices[1];
                    for (let key in $scope.healthData) {
                        if ($scope.healthData[key]['require_id'] === value['id']) {
                            if ($scope.healthData[key]['isChecked']) {
                                $scope.healthData[key]['isChecked'] = false;
                                $scope.onChecked($scope.healthData[key]);
                                break;
                            }
                        }
                    }
                }
                console.log(value.type_name, value.isChecked, $scope.currentPrice)
            };

            $scope.$watch('healthInsurePlan', function (value) {
                // console.log(value);
                switch (value) {
                    case "1":
                        switchPlan('officer');
                        break;
                    case "2":
                        switchPlan('cash');
                        break;
                    case "3":
                        switchPlan('officer');
                        break;
                    case "4":
                        switchPlan('gov');
                        break;
                    case "5":
                        switchPlan('company');
                        break;
                }
            });

            function checkPrice(checkupItem) {
                let type = $scope.currentPlan;
                let price = 0;
                let savePrice = 0;
                let tempCheckupPrice = checkupItem['total_price'];
                let additionalSavePrice = 0;
                if (type == 'cash' || type == 'gov') {
                    return [tempCheckupPrice, 0];
                }

                // if this Checkup not cover it can be substained by other unique cover
                if (!checkupItem['isCover']) {
                    for (let key in $scope.healthData) {
                        if ($scope.healthData[key]['id'] === checkupItem['id']) {
                            continue;
                        }
                        if (checkupItem['unique_code'] != '' && $scope.healthData[key]['unique_code'] === checkupItem['unique_code'] ) {
                            if ($scope.healthData[key]['isCover'] && tempCheckupPrice > $scope.healthData[key]['total_price']) {
                                tempCheckupPrice -= $scope.healthData[key]['total_price'];
                                additionalSavePrice = $scope.healthData[key]['total_price'];
                            }
                        }
                    }
                }

                console.log(checkupItem['total_price']);
                price = checkupItem['isCover'] ? 0 : tempCheckupPrice;
                savePrice = checkupItem['isCover'] ? tempCheckupPrice : additionalSavePrice;

                return [price, savePrice];

            }

            function switchPlan(type) {
                $scope.currentPlan = type;
                $scope.currentPrice = 0;
                $scope.currentSavePrice = 0;
                let isMatch = false;
                for (let key in $scope.healthData) {
                    let checkupItem = $scope.healthData[key];
                    checkupItem.isChecked = false;
                }

                console.log('userAge', userAge, 'gender', $scope.gender);
                for (let key in $scope.healthData) {
                    let checkupItem = $scope.healthData[key];
                    if (userAge > 50) {
                        isMatch = (checkupItem['init_' + type + '_over_50'] === "1" || checkupItem['init_' + type + '_over_35'] === "1") && (checkupItem['enable_gender'] === "3" || checkupItem['enable_gender'] === $scope.gender);
                    } else if (userAge > 35) {
                        isMatch = checkupItem['init_' + type + '_over_35'] === "1" && (checkupItem['enable_gender'] === "3" || checkupItem['enable_gender'] === $scope.gender);
                    } else {
                        isMatch = checkupItem['init_' + type + '_below_35'] === "1" && (checkupItem['enable_gender'] === "3" || checkupItem['enable_gender'] === $scope.gender);
                    }
                    if (isMatch) checkupItem.isChecked = true;
                    if (checkupItem.isChecked && isMatch) {
                        checkupItem['isCover'] = true;
                        $scope.onChecked(checkupItem);
                    } else {
                        checkupItem['isCover'] = false;
                    }
                }
            }

            $scope.prepareData = function () {

                let obj = [];
                $("#myModal3").modal({backdrop: "static"});
                for (let key in $scope.healthData) {
                    if ($scope.healthData[key]['isChecked'] === true) {
                        obj.push({
                            order_id: $scope.healthData[key]['order_ref'],
                            enable: $scope.healthData[key]['isChecked']
                        });
                    }
                    for (let i = 2; i < 9; i++) {
                        if ($scope.healthData[key]['order_ref_0' + i] != null && $scope.healthData[key]['order_ref_0' + i] !== '') {
                            if ($scope.healthData[key]['isChecked'] === true) {
                                obj.push({
                                    order_id: $scope.healthData[key]['order_ref_0' + i],
                                    enable: $scope.healthData[key]['isChecked']
                                });
                            }
                        }
                    }
                }
                let totalPrice = $scope.currentPrice + $scope.currentSavePrice;
                let printText = '';
                switch ($scope.currentPlan) {
                    case "company":
                        printText = `ค่าใช้จ่ายทั้งหมด: ${totalPrice} บาท,เบิกได้: ${$scope.currentSavePrice} บาท<br>ต้องชำระ/สำรองจ่าย: ${totalPrice} บาท`;
                        //printText = `<tr><td colspan='2'>ค่าใช้จ่ายทั้งหมด: ${totalPrice} ,เบิกได้: ${$scope.currentSavePrice}</td></tr><tr><td colspan='2'>ต้องชำระ/สำรองจ่าย: ${totalPrice} บาท</td></tr>`;
                        break;
                    case "gov":
                        printText = `ค่าใช้จ่ายทั้งหมด: ${totalPrice} บาท,เบิกได้: ${totalPrice} บาท<br>ต้องชำระ/สำรองจ่าย: 0 บาท`;

                        //printText = `<tr><td colspan='2'>ค่าใช้จ่ายทั้งหมด: ${totalPrice} ,เบิกได้: ${totalPrice}</td></tr><tr><td colspan='2'>ต้องชำระ/สำรองจ่าย: 0 บาท</td></tr>`;

                        break;
                    case "cash":
                    case "officer":
                        printText = `ค่าใช้จ่ายทั้งหมด: ${totalPrice} บาท,เบิกได้: ${$scope.currentSavePrice} บาท <br>ต้องชำระ/สำรองจ่าย: ${$scope.currentPrice} บาท`;
                        //printText = `<tr> <td colspan='2'>ค่าใช้จ่ายทั้งหมด: ${totalPrice} ,เบิกได้: ${$scope.currentSavePrice}</td></tr><tr><td colspan='2'>ต้องชำระ/สำรองจ่าย: ${$scope.currentPrice} บาท</td></tr>`;
                        break;
                }
                $.post('<?=$url?>', {
                        checkup_item: JSON.stringify(obj),
                        pt_id: '<?=$target?>',
                        patient_age: userAge,
                        current_price: printText,
                        insure_type: $scope.healthInsurePlan,
                        visit_id: '<?=$visit_id?>'
                    }
                ).done(function (result) {
                    try {
                        let func = null;
                        func = window['<?=$callbackFunc?>'];
                        $('#myModal3').modal('hide');
                        if (func != null) {
                            func(result);
                        }
                    } catch (e) {
                        $('#myModal3').modal('hide');
                        console.warn('callback', e)
                    }

                }).fail(function (e) {
                    $('#myModal3').modal('hide');
                    console.error(e);
                });
            };
        });
    </script>
<?php
