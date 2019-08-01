<?php

namespace backend\modules\patient\classes;

use yii\helpers\Html;
use yii\helpers\Url;
use backend\modules\ezforms2\classes\EzfHelper;
use backend\modules\ezforms2\classes\EzfFunc;

/**
 * Description of EzfHelper
 *
 * @author appxq
 */
class PatientHelper extends EzfHelper {

    public static function uiPatient($dataid, $reloadDiv) {
        $url = Url::to(['/patient/patient/view', 'dataid' => $dataid, 'reloadDiv' => $reloadDiv]);
        $html = Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-id' => $dataid, 'data-url' => $url]);
        $view = \Yii::$app->getView();

        $view->registerJs("
            $.ajax({
                method: 'POST',
                url: '$url',
                dataType: 'HTML',
                success: function(result, textStatus) {
                    $('#$reloadDiv').html(result);
                }
            });
        ");

        return $html;
    }

    public static function uiPatientShow($dataid, $target, $reloadDiv, $IO) {
        $url = Url::to(['/patient/patient/show', 'dataid' => $dataid, 'target' => $target, 'IO' => $IO, 'reloadDiv' => $reloadDiv]);
        $html = Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-id' => $dataid, 'data-url' => $url]);
        $view = \Yii::$app->getView();

        $view->registerJs("             
            $.ajax({
                method: 'POST',
                url: '$url',
                dataType: 'HTML',
                success: function(result, textStatus) {
                    $('#$reloadDiv').html(result);
                }
            });
        ");

        return $html;
    }

    public static function uiPatientCpoe($pt_id, $reloadDiv, $view = null, $btnDisabled = false) {
        $url = Url::to(['/patient/patient/cpoe', 'ptid' => $pt_id, 'reloadDiv' => $reloadDiv, 'view' => $view, 'btnDisabled' => $btnDisabled]);
        $html = Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-url' => $url]);
        $view = \Yii::$app->getView();

        $view->registerJs("
            $.ajax({
                method: 'POST',
                url: '$url',
                dataType: 'HTML',
                success: function(result, textStatus) {
                    $('#$reloadDiv').html(result);
                }
            });
        ");

        return $html;
    }

    public static function uiPatientPic($pt_id, $reloadDiv, $style) {
        $style = EzfFunc::arrayEncode2String($style);
        $url = Url::to(['/patient/patient/profile-pic', 'ptid' => $pt_id, 'reloadDiv' => $reloadDiv, 'style' => $style]);
        $html = Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-url' => $url]);
        $view = \Yii::$app->getView();

        $view->registerJs("
            $.ajax({
                method: 'POST',
                url: '$url',
                dataType: 'HTML',
                success: function(result, textStatus) {
                    $('#$reloadDiv').html(result);
                }
            });
        ");

        return $html;
    }

    public static function uiPatientEMR($dataid, $reloadDiv) {
        $url = Url::to(['/patient/emr/view', 'dataid' => $dataid, 'reloadDiv' => $reloadDiv]);
        $html = Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-id' => '&dataid=' . $dataid, 'data-url' => $url]);
        $view = \Yii::$app->getView();

        $view->registerJs("
            getUiAjax('$url', '$reloadDiv');
        ");

        return $html;
    }

    public static function uiVS($dataid, $target, $reloadDiv, $btnDisabled = FALSE) {
        $url = Url::to(['/patient/emr/vs', 'dataid' => $dataid, 'target' => $target, 'reloadDiv' => $reloadDiv, 'btnDisabled' => $btnDisabled]);
        $html = Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-url' => $url]);
        $view = \Yii::$app->getView();

        $view->registerJs("
            $.ajax({
                method: 'POST',
                url: '$url',
                dataType: 'HTML',
                success: function(result, textStatus) {
                    $('#$reloadDiv').html(result);
                }
            });
        ");

        return $html;
    }

    public static function uiBMI($dataid, $target, $reloadDiv, $btnDisabled = FALSE) {
        $url = Url::to(['/patient/emr/bmi', 'dataid' => $dataid, 'target' => $target, 'reloadDiv' => $reloadDiv, 'btnDisabled' => $btnDisabled]);
        $html = Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-url' => $url]);
        $view = \Yii::$app->getView();

        $view->registerJs("
            $.ajax({
                method: 'POST',
                url: '$url',
                dataType: 'HTML',
                success: function(result, textStatus) {
                    $('#$reloadDiv').html(result);
                }
            });
        ");

        return $html;
    }

    public static function uiTK($dataid, $target, $reloadDiv, $views = '_tk', $btnDisabled = FALSE) {
        $url = Url::to(['/patient/emr/tk', 'dataid' => $dataid, 'target' => $target,
                    'reloadDiv' => $reloadDiv, 'view' => $views, 'btnDisabled' => $btnDisabled]);
        $html = Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-url' => $url]);
        $view = \Yii::$app->getView();

        $view->registerJs("
            $.ajax({
                method: 'POST',
                url: '$url',
                dataType: 'HTML',
                success: function(result, textStatus) {
                    $('#$reloadDiv').html(result);
                }
            });

//            getUiAjax('$url', '$reloadDiv');
        ");

        return $html;
    }

    public static function uiPE($dataid, $target, $pt_id, $reloadDiv, $views = '_pe', $btnDisabled = FALSE) {
        $url = Url::to(['/patient/emr/pe', 'dataid' => $dataid, 'target' => $target, 'pt_id' => $pt_id,
                    'reloadDiv' => $reloadDiv, 'view' => $views, 'btnDisabled' => $btnDisabled]);
        $html = Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-url' => $url]);
        $view = \Yii::$app->getView();

        $view->registerJs("
            $.ajax({
                method: 'POST',
                url: '$url',
                dataType: 'HTML',
                success: function(result, textStatus) {
                    $('#$reloadDiv').html(result);
                }
            });
//            getUiAjax('$url', '$reloadDiv');
        ");

        return $html;
    }

    public static function uiDI($dataid, $target, $reloadDiv, $btnDisabled = FALSE) {
        $url = Url::to(['/patient/emr/di', 'dataid' => $dataid, 'target' => $target
                    , 'reloadDiv' => $reloadDiv, 'btnDisabled' => $btnDisabled]);
        $html = Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-url' => $url]);
        $view = \Yii::$app->getView();

        $view->registerJs("
            $.ajax({
                method: 'POST',
                url: '$url',
                dataType: 'HTML',
                success: function(result, textStatus) {
                    $('#$reloadDiv').html(result);
                }
            });
//            getUiAjax('$url', '$reloadDiv');
        ");

        return $html;
    }

    public static function uiTreatment($treat_id, $visit_id, $reloadDiv, $btnDisabled = FALSE) {
        $url = Url::to(['/patient/emr/treatment', 'treat_id' => $treat_id, 'visit_id' => $visit_id
                    , 'reloadDiv' => $reloadDiv, 'btnDisabled' => $btnDisabled]);
        $html = Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-url' => $url]);
        $view = \Yii::$app->getView();

        $view->registerJs("
            $.ajax({
                method: 'POST',
                url: '$url',
                dataType: 'HTML',
                success: function(result, textStatus) {
                    $('#$reloadDiv').html(result);
                }
            });
        ");

        return $html;
    }

    public static function uiDICpoe($dataid, $target, $reloadDiv) {
        $url = Url::to(['/patient/emr/di-cpoe', 'dataid' => $dataid, 'target' => $target
                    , 'reloadDiv' => $reloadDiv]);
        $html = Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-url' => $url]);
        $view = \Yii::$app->getView();

        $view->registerJs("
            $.ajax({
                method: 'POST',
                url: '$url',
                dataType: 'HTML',
                success: function(result, textStatus) {
                    $('#$reloadDiv').html(result);
                }
            });
//            getUiAjax('$url', '$reloadDiv');
        ");

        return $html;
    }

    public static function uiStaging($dataid, $target, $reloadDiv) {
        $url = Url::to(['/patient/emr/staging', 'dataid' => $dataid, 'target' => $target, 'reloadDiv' => $reloadDiv]);
        $html = Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-url' => $url]);
        $view = \Yii::$app->getView();

        $view->registerJs("
            getUiAjax('$url', '$reloadDiv');
        ");

        return $html;
    }

    public static function uiReferReceive($dataid, $target, $reloadDiv) {
        $url = Url::to(['/patient/emr/refer-receive', 'dataid' => $dataid, 'target' => $target, 'reloadDiv' => $reloadDiv]);
        $html = Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-url' => $url]);
        $view = \Yii::$app->getView();

        $view->registerJs("
            getUiAjax('$url', '$reloadDiv');
        ");

        return $html;
    }

    public static function uiSOAP($dataid, $target, $reloadDiv, $btnDisabled = FALSE) {
        $url = Url::to(['/patient/emr/soap', 'dataid' => $dataid, 'target' => $target,
                    'reloadDiv' => $reloadDiv, 'btnDisabled' => $btnDisabled]);
        $html = Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-url' => $url]);
        $view = \Yii::$app->getView();

        $view->registerJs("
            getUiAjax('$url', '$reloadDiv');
        ");

        return $html;
    }

    public static function uiGridOrder($ezf_id, $target, $reloadDiv, $btnDisabled = FALSE) {
        $url = Url::to(['/patient/order/grid-order', 'ezf_id' => $ezf_id, 'target' => $target
                    , 'reloadDiv' => $reloadDiv, 'btnDisabled' => $btnDisabled]);
        $html = Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-url' => $url]);
        $view = \Yii::$app->getView();

        $view->registerJs("
            $.ajax({
                method: 'POST',
                url: '$url',
                dataType: 'HTML',
                success: function(result, textStatus) {
                    $('#$reloadDiv').html(result);
                }
            });
        ");

        return $html;
    }

    public static function uiOrderList($target, $reloadDiv) {
        $url = Url::to(['/patient/order/order-search', 'target' => $target, 'reloadDiv' => $reloadDiv]);
        $html = Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-url' => $url]);
        $view = \Yii::$app->getView();

        $view->registerJs("
            $.ajax({
                method: 'POST',
                url: '$url',
                dataType: 'HTML',
                success: function(result, textStatus) {
                    $('#$reloadDiv').html(result);
                }
            });
        ");

        return $html;
    }

    public static function uiEmrDoctor($visit_id, $reloadDiv, $view) {
        $url = Url::to(['/patient/emr/doctor-treat', 'visit_id' => $visit_id
                    , 'reloadDiv' => $reloadDiv, 'view' => $view]);
        $html = Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-url' => $url]);
        $view = \Yii::$app->getView();

        $view->registerJs("
            $.ajax({
                method: 'POST',
                url: '$url',
                dataType: 'HTML',
                success: function(result, textStatus) {
                    $('#$reloadDiv').html(result);
                }
            });
        ");

        return $html;
    }

    public static function uiBtnADT($admit_id, $visit_id, $reloadDiv, $IO, $bedtran_id = null) {
        $url = Url::to(['/patient/admit/admit-btn-adt', 'admit_id' => $admit_id, 'visit_id' => $visit_id, 'reloadDiv' => $reloadDiv, 'IO' => $IO, 'bedtran_id' => $bedtran_id]);
        $reloadDiv = ($IO == 'O' ? $reloadDiv : $reloadDiv . 'ADT');
        $html = Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-url' => $url]);
        $view = \Yii::$app->getView();

        $view->registerJs("
            $.ajax({
                      method: 'POST',
                      url: '$url',
                      dataType: 'HTML',
                      success: function (result, textStatus) {
                        $('#$reloadDiv').html(result);
                      }
                    });
        ");

        return $html;
    }

    public static function uiWardBed($ezf_id, $dept, $reloadDiv, $module, $tab='') {
        $url = Url::to(['/patient/admit/ward-bed', 'ezf_id' => $ezf_id, 'dept' => $dept, 'reloadDiv' => $reloadDiv, 'module' => $module, 'tab' => $tab]);
        $html = Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-url' => $url]);
        $view = \Yii::$app->getView();

        $view->registerJs("
            getUiAjax('$url', '$reloadDiv');
        ");

        return $html;
    }

    public static function uiWardDash($ezf_id, $dept, $reloadDiv, $module, $tab='') {
        $url = Url::to(['/patient/admit/ward-dash', 'ezf_id' => $ezf_id, 'dept' => $dept, 'reloadDiv' => $reloadDiv, 'module' => $module, 'tab' => $tab]);
        $html = Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-url' => $url]);
        $view = \Yii::$app->getView();

        $view->registerJs("
            getUiAjax('$url', '$reloadDiv');
        ");

        return $html;
    }

    public static function uiBedTran($target, $visit_id, $reloadDiv) {
        $url = Url::to(['/patient/admit/bed-tran', 'target' => $target, 'visit_id' => $visit_id, 'reloadDiv' => $reloadDiv]);
        $html = Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-url' => $url]);
        $view = \Yii::$app->getView();

        $view->registerJs("
            //getUiAjax('$url', '$reloadDiv');
            $.ajax({
                method: 'POST',
                url: '$url',
                dataType: 'HTML',
                success: function(result, textStatus) {
                    $('#$reloadDiv').html(result);
                }
            });    
        ");

        return $html;
    }

    public static function uiSelect2Search($ezf_id, $dataid, $fullname, $reloadDiv) {
        $view = \Yii::$app->getView();

        return $view->renderAjax('_search', [
                    'ezf_id' => $ezf_id,
                    'dataid' => $dataid,
                    'fullname' => $fullname,
                    'reloadDiv' => $reloadDiv,
        ]);
    }

    /**
     * Creates data
     *
     * @param string $txt ข้อความปุ่ม
     * @param int $ezf_id ezform id
     * @param array $initdata เซ็ตค่าให้กับตัวแปรในฟอร์ม
     * @param int $target ezform target
     * @param string $reloadDiv id ของ tag html ที่ต้องการรีโหลด
     * @param string size btn-sm,md,xl,xxl
     * @param string $icon ex. glyphicon-plus
     * 
     * @return string html
     */
    public static function btnAddTxt($txt = '', $ezf_id, $target = '', $initdata = [], $reloadDiv = '', $modal = 'modal-ezform-main', $size = null, $icon = 'glyphicon glyphicon-plus',$color='btn-success') {

        $data = EzfFunc::arrayEncode2String($initdata);

        return self::btnOpenForm($ezf_id, '', '<i class="' . $icon . '"></i> ' . (isset($txt) ? $txt : Yii::t('app', 'New')), [
                    'class' => 'btn '.$color.' ezform-main-open ' . $size,
                    'data-modal' => $modal,
                    'data-url' => Url::to(['/ezforms2/ezform-data/ezform',
                        'ezf_id' => $ezf_id,
                        'modal' => $modal,
                        'reloadDiv' => $reloadDiv,
                        'initdata' => $data,
                        'target' => $target
                    ]),
        ]);
    }

    /**
     * Update data
     *
     * @param string $txt ข้อความปุ่ม
     * @param int $ezf_id ezform id
     * @param int $dataid zdata id
     * @param array $initdata เซ็ตค่าให้กับตัวแปรในฟอร์ม
     * @param string $reloadDiv id ของ tag html ที่ต้องการรีโหลด
     * @param string size btn-sm,md,xl,xxl
     * @param string $icon ex. glyphicon-plus
     * 
     * @return string html
     */
    public static function btnEditTxt($txt = '', $ezf_id, $dataid, $initdata = [], $reloadDiv = '', $modal = 'modal-ezform-main', $size = null, $icon = 'glyphicon glyphicon-pencil') {
        $data = EzfFunc::arrayEncode2String($initdata);
        return self::btnOpenForm($ezf_id, '', '<i class="' . $icon . '"></i> ' . (isset($txt) ? $txt : Yii::t('app', 'Update')), [
                    'class' => 'btn btn-primary ezform-main-open ' . $size,
                    'data-modal' => $modal,
                    'data-url' => Url::to(['/ezforms2/ezform-data/ezform',
                        'ezf_id' => $ezf_id,
                        'dataid' => $dataid,
                        'modal' => $modal,
                        'reloadDiv' => $reloadDiv,
                        'initdata' => $data,
                    ]),
        ]);
    }

    /**
     * Show Data
     *
     * @param string $txt ข้อความปุ่ม
     * @param int $ezf_id ezform id
     * @param int $dataid zdata id
     * @param array $initdata เซ็ตค่าให้กับตัวแปรในฟอร์ม
     * @param string $reloadDiv id ของ tag html ที่ต้องการรีโหลด
     * @param string size btn-sm,md,xl,xxl
     * @param string $icon ex. glyphicon glyphicon-plus
     * 
     * @return string html
     */
    public static function btnViewTxt($txt = '', $ezf_id, $target, $data_column = [], $modal = 'modal-ezform-main', $size = null, $icon = 'glyphicon glyphicon-th-list') {
        $data_column = EzfFunc::arrayEncode2String($data_column);
        return self::btnOpenForm($ezf_id, '', '<i class="' . $icon . '"></i> ' . (isset($txt) ? $txt : Yii::t('app', 'View')), [
                    'class' => 'btn btn-info ' . $size . ' ezform-main-open',
                    'data-modal' => $modal,
                    'data-url' => Url::to(['/ezforms2/ezform-data/view',
                        'ezf_id' => $ezf_id,
                        'popup' => 1,
                        'modal' => $modal,
                        'data_column' => $data_column,
                        'target' => $target,
                    ]),
        ]);
    }

    public static function uiMedicalHistory($dataid, $reloadDiv) {
        $url = Url::to(['/patient/medical-history/view', 'dataid' => $dataid, 'reloadDiv' => $reloadDiv]);
        $html = Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-id' => '&dataid=' . $dataid, 'data-url' => $url]);
        $view = \Yii::$app->getView();

        $view->registerJs("
            getUiAjax('$url', '$reloadDiv');
        ");

        return $html;
    }

    public static function listVisitHospital($dataid, $reloadDiv, $reloadChildDiv) {
        $url = Url::to(['/patient/medical-history/visit-hospital', 'dataid' => $dataid, 'reloadDiv' => $reloadDiv, 'reloadChildDiv' => $reloadChildDiv]);
        $html = Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-url' => $url]);
        $view = \Yii::$app->getView();

        $view->registerJs("
            getUiAjax('$url', '$reloadDiv');
        ");

        return $html;
    }

    public static function listVisit($target, $options, $render_view, $reloadDiv,$modal) {
        $url = Url::to(['/patient/medical-history/visit', 'target' => $target, 'view' => $render_view, 'options' => $options, 'reloadDiv' => $reloadDiv,'modal'=>$modal,]);
        $html = Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-url' => $url]);
        $view = \Yii::$app->getView();

        $view->registerJs("
            getUiAjax('$url', '$reloadDiv');
        ");

        return $html;
    }

    public static function uiAdmitCpoe($pt_id, $visit_id, $reloadDiv, $btnDisabled = false) {
        $url = Url::to(['/patient/admit/admit-cpoe', 'pt_id' => $pt_id, 'visit_id' => $visit_id, 'reloadDiv' => $reloadDiv, 'btnDisabled' => $btnDisabled]);
        $html = Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-url' => $url]);
        $view = \Yii::$app->getView();

        $view->registerJs(" 
            getUiAjax('$url', '$reloadDiv');
        ");

        return $html;
    }

    public static function uiDischargeShow($pt_id, $visit_id, $render_view, $reloadDiv, $btnDisabled = false) {
        $url = Url::to(['/patient/admit/discharge-cpoe', 'pt_id' => $pt_id, 'visit_id' => $visit_id, 'view' => $render_view
                    , 'reloadDiv' => $reloadDiv, 'btnDisabled' => $btnDisabled]);
        $html = Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-url' => $url]);
        $view = \Yii::$app->getView();

        $view->registerJs(" 
            getUiAjax('$url', '$reloadDiv');
        ");

        return $html;
    }

    public static function uiOrderAdmit($admit_id, $visit_id, $reloadDiv) {
        $url = Url::to(['/patient/order/order-admit-group', 'admit_id' => $admit_id, 'visit_id' => $visit_id, 'reloadDiv' => $reloadDiv]);
        $html = Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-url' => $url]);
        $view = \Yii::$app->getView();

        $view->registerJs("
            $.ajax({
                method: 'POST',
                url: '$url',
                dataType: 'HTML',
                success: function(result, textStatus) {
                    $('#$reloadDiv').html(result);
                }
            });    
        ");

        return $html;
    }

    public static function foodAdmit($admit_id, $visit_id, $reloadDiv) {
        $url = Url::to(['/patient/order/order-food-admit', 'admit_id' => $admit_id, 'visit_id' => $visit_id, 'reloadDiv' => $reloadDiv]);
        $html = Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-url' => $url]);
        $view = \Yii::$app->getView();

        $view->registerJs("
            $.ajax({
                method: 'POST',
                url: '$url',
                dataType: 'HTML',
                success: function(result, textStatus) {
                    $('#$reloadDiv').html(result);
                }
            });    
        ");

        return $html;
    }

    public static function uiTransferShow($pt_id, $visit_id, $render_view, $reloadDiv, $btnDisabled = false) {
        $url = Url::to(['/patient/admit/transfer', 'pt_id' => $pt_id, 'visit_id' => $visit_id, 'view' => $render_view
                    , 'reloadDiv' => $reloadDiv, 'btnDisabled' => $btnDisabled]);
        $html = Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-url' => $url]);
        $view = \Yii::$app->getView();

        $view->registerJs(" 
            getUiAjax('$url', '$reloadDiv');
        ");

        return $html;
    }

    public static function uiVsCpoe($pt_id, $visit_id, $reloadDiv) {
        $url = Url::to(['/patient/emr/vs-cpoe', 'pt_id' => $pt_id, 'visit_id' => $visit_id, 'reloadDiv' => $reloadDiv]);
        $html = Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-url' => $url]);
        $view = \Yii::$app->getView();

        $view->registerJs("
            getUiAjax('$url', '$reloadDiv');
        ");

        return $html;
    }

    public static function uiBmiCpoe($pt_id, $visit_id, $reloadDiv) {
        $url = Url::to(['/patient/emr/bmi-cpoe', 'pt_id' => $pt_id, 'visit_id' => $visit_id, 'reloadDiv' => $reloadDiv]);
        $html = Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-url' => $url]);
        $view = \Yii::$app->getView();

        $view->registerJs("
            getUiAjax('$url', '$reloadDiv');
        ");

        return $html;
    }

    public static function btnAppoint($dataid, $target, $reloadDiv, $dept) {
        $url = Url::to(['/patient/patient/appoint-save-visit-date', 'reloadDiv' => $reloadDiv]);
        $html = Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv . '-save', 'data-url' => $url]);

        $url = Url::to(['/patient/emr/appoint-btn', 'dataid' => $dataid, 'target' => $target, 'reloadDiv' => $reloadDiv, 'dept' => $dept]);
        $html .= Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-url' => $url]);
        $view = \Yii::$app->getView();

        $view->registerJs("
            getUiAjax('$url', '$reloadDiv');
                
            /*$('#ezf-modal-box').append('<div id=\"modal-1506908933027139000\" class=\"fade modal\" role=\"dialog\"><div class=\"modal-dialog modal-xxl\"><div class=\"modal-content\"></div></div></div>');*/
        
            $('#modal-ezform-main').on('hidden.bs.modal', function (e) {
                let url = $('#btn-appoint').attr('data-url');
                getUiAjax(url, 'btn-appoint');

                //$('#modal-1506908933027139000 .modal-content').html('');
            });
        ");

        return $html;
    }

    public static function btnCertificate($dataid, $target, $reloadDiv, $dept,$options) {
        $url = Url::to(['/patient/emr/cer-save-visit-date','target'=>$target, 'reloadDiv' => $reloadDiv,'options' => EzfFunc::arrayEncode2String($options)]);
        $html = Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv . '-save', 'data-url' => $url]);

        $url = Url::to(['/patient/emr/certificate-btn', 'dataid' => $dataid, 'target' => $target, 'reloadDiv' => $reloadDiv, 'dept' => $dept,'options' => EzfFunc::arrayEncode2String($options)]);
        $html .= Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-url' => $url]);
        $view = \Yii::$app->getView();

        $view->registerJs("
            getUiAjax('$url', '$reloadDiv');              
        ");

        return $html;
    }

    public static function uiAppoint($dataid, $target, $reloadDiv) {
        $url = Url::to(['/patient/emr/appoint', 'dataid' => $dataid, 'target' => $target, 'reloadDiv' => $reloadDiv]);
        $html = Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-url' => $url]);
        $view = \Yii::$app->getView();

        $view->registerJs("
            getUiAjax('$url', '$reloadDiv');
        ");

        return $html;
    }

    public static function uiResultLabChart($pt_id, $pt_hn, $visit_id, $date, $reloadDiv) {
        $url = Url::to(['/patient/order/result-lab-show', 'pt_id' => $pt_id, 'pt_hn' => $pt_hn, 'visit_id' => $visit_id,
                    'reloadDiv' => $reloadDiv, 'view' => 'cpoe', 'date' => $date]);
        $html = Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-url' => $url]);
        $view = \Yii::$app->getView();

        $view->registerJs(" 
            $.ajax({
                method: 'POST',
                url: '$url',
                dataType: 'HTML',
                success: function(result, textStatus) {
                    $('#$reloadDiv').html(result);
                }
            });   
        ");

        return $html;
    }

    public static function uiResultXray($pt_id, $pt_hn, $visit_id, $date, $reloadDiv) {
        $url = Url::to(['/patient/order/result-xray-show', 'pt_id' => $pt_id, 'pt_hn' => $pt_hn, 'visit_id' => $visit_id,
                    'reloadDiv' => $reloadDiv, 'view' => 'cpoe', 'date' => $date]);
        $html = Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-url' => $url]);
        $view = \Yii::$app->getView();

        $view->registerJs("
            getUiAjax('$url', '$reloadDiv');
        ");

        return $html;
    }

    public static function uiResultCyto($pt_id, $pt_hn, $visit_id, $date, $reloadDiv) {
        $url = Url::to(['/patient/order/result-cyto-show', 'pt_id' => $pt_id, 'pt_hn' => $pt_hn, 'visit_id' => $visit_id,
                    'reloadDiv' => $reloadDiv, 'view' => 'cpoe', 'date' => $date]);
        $html = Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-url' => $url]);
        $view = \Yii::$app->getView();

        $view->registerJs("
            getUiAjax('$url', '$reloadDiv');
        ");

        return $html;
    }

    public static function uiNurseNote($admit_id, $visit_id, $reloadDiv) {
        $url = Url::to(['/patient/admit/nurse-note', 'admit_id' => $admit_id, 'visit_id' => $visit_id, 'reloadDiv' => $reloadDiv]);
        $html = Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-url' => $url]);
        $view = \Yii::$app->getView();

        $view->registerJs("             
            $.ajax({
                method: 'POST',
                url: '$url',
                dataType: 'HTML',
                success: function(result, textStatus) {
                    $('#$reloadDiv').html(result);
                }
            });
        ");

        return $html;
    }

    public static function uiAdmitDrugShow($admit_id, $visit_id, $reloadDiv) {
        $url = Url::to(['/patient/admit/drug-show', 'admit_id' => $admit_id, 'visit_id' => $visit_id, 'reloadDiv' => $reloadDiv]);
        $html = Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-url' => $url]);
        $view = \Yii::$app->getView();

        $view->registerJs("             
            $.ajax({
                method: 'POST',
                url: '$url',
                dataType: 'HTML',
                success: function(result, textStatus) {
                    $('#$reloadDiv').html(result);
                }
            });
        ");

        return $html;
    }

    public static function uiReceiptNo($user_id, $reloadDiv) {
        $url = Url::to(['/patient/cashier2/receipt-no', 'user_id' => $user_id, 'reloadDiv' => $reloadDiv]);
        $html = Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-url' => $url]);
        $view = \Yii::$app->getView();

        $view->registerJs("              
            $.ajax({
                method: 'POST',
                url: '$url',
                dataType: 'HTML',
                success: function(result, textStatus) {
                    $('#$reloadDiv').html(result);
                }
            });
        ");

        return $html;
    }

}
?>

