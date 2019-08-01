<?php

namespace backend\modules\cpoe\classes;

use yii\helpers\Html;
use yii\helpers\Url;

/**
 * Description of EzfHelper
 *
 * @author appxq
 */
class CpoeHelper {

    public static function uiQue($pt_id, $reloadDiv, $que_type, $options = null) {

        $url = Url::to(['/cpoe/default/queue-view', 'ptid' => $pt_id, 'reloadDiv' => $reloadDiv, 'que_type' => $que_type, '', 'options' => \appxq\sdii\utils\SDUtility::array2String($options)]);
        $html = Html::tag('div', '<div class="sdloader "><i class="sdloader-icon"></i></div>', ['id' => $reloadDiv, 'data-url' => $url]);
        $view = \Yii::$app->getView();

        $view->registerJs("
            getUiAjax('$url', '$reloadDiv');
            
            /*setInterval(function(){              
                todo();
            }, 15000);          
            
            async function todo() {
                getUiAjax('$url', '$reloadDiv');  
                let datakey = $('#que-list-view').attr('data-keyselect');
                $('#$reloadDiv a[data-key=\"'+datakey+'\"]').addClass('active');
                return ;
            }*/
        ");

        return $html;
    }

    public static function uiAppoint($reloadDiv) {
        $url = Url::to(['/cpoe/default/appoint-view', 'reloadDiv' => $reloadDiv]);
        $html = Html::tag('div', '<div class="sdloader "><i class="sdloader-icon"></i></div>', ['id' => $reloadDiv, 'data-url' => $url]);
        $view = \Yii::$app->getView();

        $view->registerJs("
            getUiAjax('$url', '$reloadDiv');
            /*setInterval(function(){
                getUiAjax('$url', '$reloadDiv');
            }, 15000);*/
        ");

        return $html;
    }

    public static function uiCpoe($pt_id, $action, $action_id, $visit_type, $visit_tran_id, $reloadDiv) {
        $url = Url::to(['/cpoe/cpoe/pt-select', 'ptid' => $pt_id, 'action' => $action, 'actionid' => $action_id
                    , 'visit_type' => $visit_type, 'visit_tran_id' => $visit_tran_id, 'reloadDiv' => $reloadDiv]);
        $html = Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-url' => $url]);
        $view = \Yii::$app->getView();

        $view->registerJs("
            getUiAjax('$url', '$reloadDiv');
        ");

        return $html;
    }

    public static function uiRadt($pt_id, $visit_id, $reloadDiv, $btnDisabled = FALSE) {
        $url = Url::to(['/cpoe/cpoe/radt', 'reloadDiv' => $reloadDiv,
                    'visit_id' => $visit_id, 'view_type' => 'R', 'pt_id' => $pt_id, 'btnDisabled' => $btnDisabled]);
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

    public static function uiResultOrderCpoe($pt_id, $pt_hn, $visit_id, $reloadDiv) {
        $url = Url::to(['/cpoe/cpoe/result-order', 'visit_id' => $visit_id, 'pt_id' => $pt_id,
                    'view_type' => 'lab', 'reloadDiv' => $reloadDiv, 'pt_hn' => $pt_hn]);
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

    public static function uiQueReportCheckup($target, $report_status, $que_type, $reloadDiv, $mode = null, $page = 1, $module_id = null) {
        if ($mode == 'doctor') {
            $url = Url::to(['/reports/report-checkup/queue-view', 'target' => $target, 'report_status' => $report_status,
                        'reloadDiv' => $reloadDiv, 'que_type' => $que_type, 'page' => $page, 'module_id' => $module_id]);
        } else {
            $url = Url::to(['/reports/report-checkup/que-view-r2d', 'target' => $target, 'report_status' => $report_status, 'reloadDiv' => $reloadDiv, 'module_id' => $module_id]);
        }

        $html = Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-url' => $url]);
        $view = \Yii::$app->getView();

        $view->registerJs("
            getUiAjax('$url', '$reloadDiv');                       
        ");

        return $html;
    }

    public static function uiQueReportCashier($reloadDiv, $receipt_status, $qname, $date_now) {
        $url = Url::to(['/patient/cashier/queue-view', 'reloadDiv' => $reloadDiv, 'receipt_status' => $receipt_status, 'qname' => $qname, 'date_now' => $date_now]);
        $html = Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-url' => $url]);
        $view = \Yii::$app->getView();

        $view->registerJs("
            getUiAjax('$url', '$reloadDiv');                       
        ");

        return $html;
    }

    public static function uiQueCashier2($reloadDiv) {
        $url = Url::to(['/patient/cashier2/que-view', 'reloadDiv' => $reloadDiv,]);
        $html = Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-url' => $url]);
        $view = \Yii::$app->getView();

        $view->registerJs("
            getUiAjax('$url', '$reloadDiv');                       
        ");

        return $html;
    }

    public static function uiReportCheckup($ezf_id, $report_id, $initdata, $reloadDiv, $visit_id = null, $ezm_id = null, $que_type = 1) {
//        $url = Url::to(['/cpoe/report-checkup/next-pt', 'dataid' => '']);
//        $html = Html::tag('div', '', ['id' => $reloadDiv . '-next', 'data-url' => $url]);
        $url = Url::to(['/reports/report-checkup/report-approve', 'dataid' => '', 'ezm_id' => $ezm_id, 'que_type' => $que_type]);
        $html = Html::tag('div', '', ['id' => $reloadDiv . '-next', 'data-url' => $url]);

        $url = Url::to(['/ezforms2/ezform-data/ezform', 'ezf_id' => $ezf_id,
                    'modal' => 'modal-ezform-main', 'reloadDiv' => $reloadDiv . '-next', 'initdata' => $initdata, 'dataid' => $report_id, 'target' => $visit_id]);
        $html .= Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-url' => $url]);

        $view = \Yii::$app->getView();

        $view->registerJs("
            getUiAjax('$url', '$reloadDiv');                       
        ");

        return $html;
    }

}
?>

