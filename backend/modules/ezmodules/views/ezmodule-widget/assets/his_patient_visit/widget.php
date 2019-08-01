<?php

use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use backend\modules\thaihis\classes\ThaiHisQuery;


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
$this->registerCss("
    .nav-tabs {
        border-bottom: 0 solid #ddd;
    }
    .tab-primary{
        color:#ffffff;
        border-color: #ddd;
    }
    
    .tab-active {
        background-color: #ffffff;
        color:#337ab7;
	border-color: #ddd;
	border-bottom-color: transparent;
    }
    .nav > .nav-item > a:focus {
        background-color: #ffffff;
    }
    ");

$visit_id = isset($visitid)?$visitid:Yii::$app->request->get('visitid');
$target = isset($target)?$target:Yii::$app->request->get('target');
$visit_type = isset($visit_type)?$visit_type:Yii::$app->request->get('visit_type');
$visit_tran_id = Yii::$app->request->get('visit_tran_id');
$modelVisit = null;
$jsAddon = '';

echo appxq\sdii\widgets\ModalForm::widget([
    'id' => 'modal-content-widget' . $widget_config['widget_id'],
    'size' => 'modal-xxl',
    'tabindexEnable' => false,
]);
$firstTab = null;
$param_con=null;
$check_con = true;
if(isset($options['condition_display']) && $options['condition_display'] == '1'){
    $check_con = false;
    $param_con = Yii::$app->request->get($options['param_name']);
    if($options['condition']==1 && $param_con == $options['value']){
        $check_con = true;
    }else if($options['condition']==2 && $param_con > $options['value']){
        $check_con = true;
    }else if($options['condition']==3 && $param_con < $options['value']){
        $check_con = true;
    }else if($options['condition']==4 && $param_con != $options['value']){
        $check_con = true;
    }
}


if ($target != '' && $check_con) {

    if (isset($options['tabs'])) {
        foreach ($options['tabs'] as $key => $val) {
            if (!$firstTab)
                $firstTab = $key;
        }
        if (isset($options['action_visit']) && $options['action_visit'] && empty($visit_tran_id)) {
            if (isset($options['tabs'][$firstTab]['ezf_id']) && isset($options['tabs'][$firstTab]['tran_ezf_id'])) {
                $modelVisit = ThaiHisQuery::getCurrentVisit($target, $options['tabs'][$firstTab]['ezf_id'], $options['tabs'][$firstTab]['tran_ezf_id']);
            }
        }
    }
//\appxq\sdii\utils\VarDumper::dump($modelVisit);
    echo backend\modules\thaihis\classes\ThaiHisContentBuilder::contentDisplay()
            ->target($target)
            ->visitid($visit_id)
            ->initdata(isset($options['initdata']) ? $options['initdata'] : [])
            ->disabled_box(isset($options['disabled_box']) ? $options['disabled_box'] : 1)
            ->action(isset($options['action']) ? $options['action'] : [])
            ->image_field(isset($options['image_field']) ? $options['image_field'] : '')
            ->template_content(isset($options['template_content']) ? $options['template_content'] : '')
            ->template_box(isset($options['template_box']) ? $options['template_box'] : '')
            ->display(isset($options['display']) ? $options['display'] : '')
            ->theme(isset($options['theme']) ? $options['theme'] : '')
            ->tabs(isset($options['tabs']) ? $options['tabs'] : '')
            ->widget_id($widget_config['widget_id'])
            ->warnEnabled(isset($options['warning_enabled']) ? $options['warning_enabled'] : null)
            ->warnEzfId(isset($options['warning_ezf_id']) ? $options['warning_ezf_id'] : null)
            ->warnLevel(isset($options['field_warn_level']) ? $options['field_warn_level'] : null)
            ->warnText(isset($options['field_warn_text']) ? $options['field_warn_text'] : null)
            ->warnCheck(isset($options['field_warn_check']) ? $options['field_warn_check'] : null)
            ->readonly(isset($readonly)?$readonly:null)
            ->dataid($visit_id)
            ->options($options)
            ->buildBox('/thaihis/patient-visit/visit-content');
} else {
    if (isset($options['action_visit']) && $options['action_visit']) {
        echo '<h1 class="text-center" style="font-size: 45px; color: #ccc; height:450px">' . Yii::t('thaihis', 'Please choose patient') . '</h1>';
    }
}
if (!$firstTab)
    $firstTab = 0;
$url = \yii\helpers\Url::to(['/thaihis/patient-visit/save-visit'
            , 'pt_id' => $target
            , 'ezf_id' => isset($options['tabs'][$firstTab]['ezf_id']) ? $options['tabs'][$firstTab]['ezf_id'] : 0
        ]);
if (isset($options['action_visit']) && $options['action_visit']) {
    if ($modelVisit) {
//        \appxq\sdii\utils\VarDumper::dump($modelVisit);
        if (!isset($modelVisit['visit_tran_id']) && empty($modelVisit['visit_tran_id']) && empty($visit_tran_id)) {
            $jsAddon = "var txtConfirm = '<strong>ต้องการรับเข้า ผู้มารับบริการรายนี้ ?</strong>'";
//\yii\helpers\Url::to(['/ezbuilder/ezform-builder/update','id'=>$module,'addon'=>$addon,'target'=>$target]);
            $jsAddon .= "
            yii.confirm(txtConfirm, function () {
                onLoadBlock('body');
//                var visit_type = $('input[name=\"visit_type\"]:checked').val();
                $.get('$url',{visit_type:'" . $modelVisit['visit_type'] . "',dept:'" . Yii::$app->user->identity->profile->department . "',modelVisit:'" . \appxq\sdii\utils\SDUtility::array2String($modelVisit) . "'}).done(function(result) {
                    console.log(result);
                    if(result.data.visit_tran_id != ''){
                         " . SDNoty::show('result.message', 'result.status') . "
                             window.location.href = '".\yii\helpers\Url::to(['/ezmodules/ezmodule/view','id'=>$module,'addon'=>$addon,'target'=>$target])."' + 
                        '&visitid=' + result.data.id+
                        '&visit_tran_id='+result.data.visit_tran_id+
                        '&visit_type='+result.data.visit_type+
                        '&que_type=" . Yii::$app->request->get('que_type', '1') . "&action=" . Yii::$app->request->get('action', 'que') . "';
                    }else{
                         hideLoadBlock('body');
                         let txt_alert = '';
                         if(result.data.visit_type == 2){
                            txt_alert = '<h3>".Yii::t('patient', 'ไม่พบข้อมูลการนัด')."</h3><div class=\'clearfix\'></div>".Yii::t('patient', 'คุณต้องการเปิด Visit หรือไม่?')."';
                         }else{
                            txt_alert = '<h3>".Yii::t('patient', 'ไม่สามารถเปิด Visit ได้')."</h3><div class=\'clearfix\'></div>".Yii::t('patient', 'คุณต้องการเปิด Visit อีกครั้งหรือไม่?')."';
                         }
                          yii.confirm(txt_alert, function () {
                                onLoadBlock('body');
                                $.get('$url',{visit_type:'4',dept:'" . Yii::$app->user->identity->profile->department . "',modelVisit:'" . \appxq\sdii\utils\SDUtility::array2String($modelVisit) . "'}).done(function(result) {
                                    if(result.data.visit_tran_id != ''){
                                         " . SDNoty::show('result.message', 'result.status') . "
                                             window.location.href = '".\yii\helpers\Url::to(['/ezmodules/ezmodule/view','id'=>$module,'addon'=>$addon,'target'=>$target])."' + 
                                        '&visitid=' + result.data.id+
                                        '&visit_tran_id='+result.data.visit_tran_id+
                                        '&visit_type='+result.data.visit_type+
                                        '&que_type=" . Yii::$app->request->get('que_type', '1') . "&action=" . Yii::$app->request->get('action', 'que') . "';
                                    }else{
                                        hideLoadBlock('body');
                                        " . SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') . "
                                    }
                                }).fail(function() {
                                    hideLoadBlock('body');
                                    " . SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') . "
                                    console.log('server error');
                                });
                                
                            });
                    }
                }).fail(function() {
                    hideLoadBlock('body');
                    " . SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') . "
                    console.log('server error');
                });
            });
        ";
        } else {
            if (empty($visit_id) || ($visit_tran_id == '' && $modelVisit['visit_tran_id'] != '')) {
                $jsAddon .= "
                onLoadBlock('body');
                window.location.href = window.location.href +
                '&visitid={$modelVisit['id']}'+
                '&visit_tran_id={$modelVisit['visit_tran_id']}'+
                '&visit_type={$modelVisit['visit_type']}';";
            }
        }
    } else if (isset($target) && !empty($target) && empty($visit_tran_id)) {
        $jsAddon = "var txtConfirm = '<strong>ต้องการรับเข้า ผู้มารับบริการรายนี้ ?</strong>'+
        '<div><strong>" . Yii::t('patient', 'Service') . "</<strong>'+
        '  <label class=\"radio-inline\"><input type=\"radio\" name=\"visit_type\" value=\"2\"> <span>" . Yii::t('patient', 'Follow up') . "  </span></label>'+
        '  <label class=\"radio-inline\"><input type=\"radio\" name=\"visit_type\" value=\"3\"> <span>" . Yii::t('patient', 'Refer') . "  </span></label>'+
        '  <label class=\"radio-inline\"><input type=\"radio\" name=\"visit_type\" value=\"4\" checked> <span>" . Yii::t('patient', 'Treatment') . "  </span> </label>'+
        '</div>'";


        $jsAddon .= "
        yii.confirm(txtConfirm, function () {
            onLoadBlock('body');
            var visit_type = $('input[name=\"visit_type\"]:checked').val();
            $.get('$url',{visit_type:visit_type,dept:'" . Yii::$app->user->identity->profile->department . "',modelVisit:'" . \appxq\sdii\utils\SDUtility::array2String($modelVisit) . "'}).done(function(result) {
                console.log(result);
                if(result.data.visit_tran_id != ''){
                     " . SDNoty::show('result.message', 'result.status') . "
                        window.location.href = '".\yii\helpers\Url::to(['/ezmodules/ezmodule/view','id'=>$module,'addon'=>$addon,'target'=>$target])."' + 
                        '&visitid=' + result.data.id+
                        '&visit_tran_id='+result.data.visit_tran_id+
                        '&visit_type='+result.data.visit_type+
                        '&que_type=" . Yii::$app->request->get('que_type', '1') . "&action=" . Yii::$app->request->get('action', 'que') . "';
                }else{
                    hideLoadBlock('body');
                     if(result.data.visit_type == 2){
                            txt_alert = '<h3>".Yii::t('patient', 'ไม่พบข้อมูลการนัด')."</h3><div class=\'clearfix\'></div>".Yii::t('patient', 'คุณต้องการเปิด Visit หรือไม่?')."';
                         }else{
                            txt_alert = '<h3>".Yii::t('patient', 'ไม่สามารถเปิด Visit ได้')."</h3><div class=\'clearfix\'></div>".Yii::t('patient', 'คุณต้องการเปิด Visit อีกครั้งหรือไม่?')."';
                         }
                    yii.confirm(txt_alert, function () {
                        onLoadBlock('body');
                        $.get('$url',{visit_type:'4',dept:'" . Yii::$app->user->identity->profile->department . "',modelVisit:'" . \appxq\sdii\utils\SDUtility::array2String($modelVisit) . "'}).done(function(result) {
                            if(result.data.visit_tran_id != ''){
                                 " . SDNoty::show('result.message', 'result.status') . "
                                     window.location.href = '".\yii\helpers\Url::to(['/ezmodules/ezmodule/view','id'=>$module,'addon'=>$addon,'target'=>$target])."' + 
                                '&visitid=' + result.data.id+
                                '&visit_tran_id='+result.data.visit_tran_id+
                                '&visit_type='+result.data.visit_type+
                                '&que_type=" . Yii::$app->request->get('que_type', '1') . "&action=" . Yii::$app->request->get('action', 'que') . "';
                            }else{
                                hideLoadBlock('body');
                                " . SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') . "
                            }
                        }).fail(function() {
                            hideLoadBlock('body');
                            " . SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') . "
                            console.log('server error');
                        });
                        
                    });
                }
            }).fail(function() {
                hideLoadBlock('body');
                " . SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') . "
                console.log('server error');
            });
        });
    ";
    }
}

$this->registerJS("  
    function onLoadBlock(ele){
        $(ele).waitMe({
            effect : 'facebook',
            text : 'Please wait...',
            bg : 'rgba(255,255,255,0.8)',
            color : '#000',
            maxSize : '',
            waitTime : -1,
            textPos : 'vertical',
            fontSize : '',
            source : '',
            onClose : function() {}
        });
    }
    function hideLoadBlock(ele){
         $(ele).waitMe(\"hide\");
    }      
        $jsAddon
    ");
?>


