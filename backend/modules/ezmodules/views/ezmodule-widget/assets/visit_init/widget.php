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
use yii\db\Query;
use yii\helpers\Url;

$divId = SDUtility::getMillisecTime();
$optionsVisitJson = isset($options['visit_type']) ? json_encode($options['visit_type']) : '[]';

$layout = Yii::$app->request->get('layout', '1');
$ezf_id = isset($options['ezf_id']) ? $options['ezf_id'] : '0';
if ($target == '')
    $target = '0';
$query = new Query;
$visitTypeList = $query->select(['visit_type_code', 'visit_type_name', 'xdepartmentx'])->from('zdata_visit_type')->where(['visit_type_code' => $options['visit_type']])->all();
$callbackFunc = isset($options['callback']) ? $options['callback'] : null;

if ($callbackFunc != null) {
    $callbackFunc = 'function ' . $callbackFunc;
} else {
    $callbackFunc = 'null';
}

try {

    Yii::$app->db->createCommand('UPDATE user_print_queue SET rstat = 3 WHERE user_id = :user_id', [':user_id' => Yii::$app->user->id])->execute();
    Yii::$app->db->createCommand('INSERT INTO user_print_queue (user_id) VALUES (:user_id)', [':user_id' => Yii::$app->user->id])->execute();
} catch (\yii\db\Exception $e) {
    var_dump($e);
}
?>
<div class="modal fade" id="notValid" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body">
        <h3 style="color:#4a4a4a;text-align: center">ไม่มีพบข้อมูลนัดวันนี้กรุณาติดต่อเคาเตอร์พยาบาล</h3>
        <h3 style="color:#4a4a4a;text-align: center">Sorry, Your appointment not found please contact counter.</h3>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="loadingModal" role="dialog">
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
<div class="panel panel-info">
  <div class="panel-heading" style="padding: 4px 15px;">
    <div class="row">
      <div class="col-md-12">
        <h4>ขอรับบริการ</h4>
      </div>
    </div>
  </div>
  <div class="panel-body">
    <form id="form-visit-choice" class="form-group">
        <?php
        foreach ($visitTypeList as $key => $value) {
            $visitTypeCode = $value['visit_type_code'];
            $visitTypeName = $value['visit_type_name'];
            $department = $value['xdepartmentx'];
            echo "<div class='visit-record'>
        <input class='visit-checkbox' id='visit-opt-$visitTypeCode' data-code='$visitTypeCode' data-depart='$department' type='radio' name='visit_option' value='$visitTypeCode'><label for='visit-opt-$visitTypeCode'>$visitTypeName</label></div>";
        }
        ?>
      <div class='visit-record'>
        <input class='visit-checkbox' id='visit-open-form' type='radio' name='visit_option' value='open'>
        <?php
        $ezfData = backend\modules\thaihis\classes\ThaiHisQuery::getEzfNameByTarget('1503378440057007100');

        echo kartik\select2\Select2::widget([
            'name' => 'ezf_id',
            'id' => 'ezf_id_select',
            'data' => yii\helpers\ArrayHelper::map($ezfData, 'ezf_id', 'ezf_name'),
            'options' => ['placeholder' => 'Select a state ...'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
      </div>
      <input class="btn btn-primary" id="button-submit-visit" type="submit" value="Submit">
      <?=
              backend\modules\ezforms2\classes\BtnBuilder::btn()
              ->ezf_id('1503378440057007100')
              ->target('1545035377040483400')
              ->tag('a')
              ->options(['class' => 'btn btn-primary btn-lg btn-block hidden', 'id' => 'btn-open-form', 'style' => 'margin-top:5px;'])
              ->label('Submit')
              ->buildBtnAdd();
      ?>
    </form>
  </div>
</div>
<?php
$errorNotify = \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"');
$urlSaveVisit = Url::to(['/thaihis/patient-visit/save-visit']);
$urlSaveCheckup = Url::to(['/ezmodules/ezmodule/view'], true);
$this->registerJs(<<<JS
function getInsurePlan(){
       let insurePlan = 2;
          const insuranceTag = $('#insure-hos');
    const maininscl = insuranceTag.attr('maininscl');
    const subinscl = insuranceTag.attr('subinscl');
    if (maininscl === 'LGO' || maininscl === 'OFC') {
        if (maininscl === 'LGO' && subinscl === 'L1') {
            insurePlan = 3;
        }
        else if(subinscl === 'E1' || subinscl === 'E2') {
            insurePlan = 4;
        }
        else if(maininscl === 'LGO') {
            insurePlan = 2;
        }
        else if(maininscl === 'OFC' && subinscl === 'O4') {
            insurePlan = 2;
        }
        else if(maininscl === 'OFC' && subinscl === 'O3') {
            insurePlan = 5;
        }
        else if(maininscl === 'OFC')
        {
            insurePlan = 5;
        }
    }
    else if(maininscl === 'PVT') {
        insurePlan = 5;
    }
    return insurePlan;
}
        
$('#form-visit-choice input[type="radio"]').on('change',function (){
    if($('#visit-open-form').is(':checked')){
        //add url
        changeUrl();
        $('#button-submit-visit').addClass('hidden');
        $('#btn-open-form').removeClass('hidden');
    }else{
        $('#button-submit-visit').removeClass('hidden');
        $('#btn-open-form').addClass('hidden');
    }
});
        
$('#ezf_id_select').on('change',function(){
        console.log($(this).val());
    changeUrl();
});
        
function changeUrl(){
    let url = $('#btn-open-form').attr('data-url');
    let ezf_id = $('#ezf_id_select').val();
    url = url + "&ezf_id="+ezf_id+'&target={$target}';
        
    $('#btn-open-form').attr('data-url',url);  
}

$('#form-visit-choice').submit(function (e) {
    if($('#visit-open-form').is(':checked')){
        console.log('Open Form');        
        return false;
    }
        
    const layout = '$layout';
    let insurePlan = getInsurePlan();
    e.preventDefault();
    var radioChecked = $('input[name=visit_option]:checked', '#form-visit-choice');
    let visit_code = radioChecked.val();
    let dept = radioChecked.attr('data-depart');
    let ptid = '$target';
    let ezf_id = '$ezf_id';
    console.log(visit_code, dept, ptid, ezf_id);
    $("#loadingModal").modal({backdrop: "static"});
    $.get('$urlSaveVisit', {visit_type: visit_code, ezf_id: ezf_id, dept: null, pt_id: ptid}
    ).done(function (result) {
        $("#loadingModal").modal('hide');
        console.log(result);
         var url = new URL(window.location.href);
            var layout = url.searchParams.get("layout");
        if (result['status'] == 'success') {
            // Show Module Checkup
            if (result['data']['visit_type'] == '1') {
                let visitId = result['data']['id'].toString();
                 window.location = '$urlSaveCheckup?id=1537948520044756200&target=' + ptid + '&visit_id=' + visitId + '&insure_plan=' + insurePlan +'&layout='+layout;
            }else if (result['data']['visit_type'] == '2' && result['data']['visit_tran_id'] == "") {
                  $("#notValid").modal();
            }else{
                try{
                    if(layout == 'nolayout'){
                    }
                    let func;
                         func = $callbackFunc;
                        if( func != null )
                            func(result);
                    }catch (e) {
                        alert('Save visit failed. Error');
                        console.warn('callback',e)
                    }
            }
        }else{
             alert('Save visit failed.');
        }
    }).fail(function () {
        $("#loadingModal").modal('hide');
        console.log('server error');
    });
});
JS
);
