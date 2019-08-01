<?php
// start widget builder

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

use appxq\sdii\utils\SDUtility;
use backend\modules\ezforms2\classes\EzfQuery;
use yii\db\Query;

$divId = SDUtility::getMillisecTime();
$target = Yii::$app->request->get("target", '');
//$ezf_id = isset($options['ezf_id']) ? $options['ezf_id'] : '0';
//$ezf_field = isset($options['fields']) ? $options['fields'] : '';
$table = EzfQuery::getEzformOne('1503378440057007100');
$cid = (new Query())->select('pt_cid')->from($table["ezf_table"])->where(['ptid' => $target])->scalar();

?>

    <div id='insurance-plan-<?= $divId ?>'>
        <div class="panel panel-info">
            <div class="panel-heading" style="padding: 4px 15px;">
                <div class="row">
                    <div class="col-md-6">
                        <h4>Insurance Plan</h4>
                    </div>
                </div>
            </div>
            <div class="panel-body" id="panel-insure-data">
                <table class="table">
                    <tbody>
                    <tr>
                        <td class="text-right info" id="insure-title">โรงพยาบาลต้นสิทธิ</td>
                        <td class="info"><label class="insurance-text-info" id="insure-detail"> - </label>
                        </td>
                    </tr>
                    <tr>
                        <td class=" text-right info"> โรงพยาบาลต้นสิทธิ :</td>
                        <td class="info"><label class="insurance-text-info" id="insure-hos"> - </label>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<?php

$this->registerJs(<<<JS
   setInsure();
function setInsure(){
   let cid = '$cid';
   console.log(cid)
    var settings = {
        "async": true,
      "crossDomain": true,
      "url": "/api/public-thai-his/get-insure?cid="+cid,
      "method": "GET",
      "headers": {
            "Cache-Control": "no-cache"
      }
    };

    $.ajax(settings).done(function (response) {
        if(response['success'] != null && response['success'] == false){
            $("#panel-insure-data").html("<h3 style='text-align:center;color:red'>"+response['message']+"</h3>");
        }else{
            if (response['maininscl'] == 'LGO' || response['maininscl'] == 'OFC') {
            $("#insure-title").html('สิทธิย่อย');
        } else {
            $("#insure-title").html('โรงพยาบาลต้นสิทธิ');
        }
            let insuranceTag = $('#insure-hos');
        if (response['maininscl'] == 'LGO' || response['maininscl'] == 'OFC') {
           insuranceTag.html(response['subinscl_name']);
        } else {
           insuranceTag.html(response['hmain_name']);
        }
        $("#insure-detail").html(response['maininscl_name']);
        insuranceTag.attr( 'maininscl', response['maininscl'] );
        insuranceTag.attr( 'subinscl', response['subinscl']);
        }
        
    });
}
JS
);