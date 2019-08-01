<?php

namespace backend\modules\pis\classes;

use yii\helpers\Html;
use yii\helpers\Url;

/**
 * Description of EzfHelper
 *
 * @author appxq
 */
class PisHelper {

    /**
     * 
     * @param type $right_code
     * @param type $reloadDiv
     * @param type $options
     * @param type $view
     * @return type
     */
    public static function uiOrderLists($right_code, $reloadDiv, $options = null, $view = 'ORDER', $ptid = null) {
        $url = Url::to(['/pis/pis-item-order/order-lists', 'view' => $view, 'right_code' => $right_code, 'reloadDiv' => $reloadDiv, 'options' => $options, 'ptid' => $ptid]);
        $html = Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-url' => $url]);
        $view = \Yii::$app->getView();

        $view->registerJs("
            getUiAjax('$url', '$reloadDiv');
        ");

        return $html;
    }

    public static function uiGridOrder($visit_id, $reloadDiv, $options = null, $right_code) {
        $url = Url::to(['/pis/pis-item-order/grid-order', 'visit_id' => $visit_id
                    , 'reloadDiv' => $reloadDiv, 'options' => $options
                    , 'right_code' => $right_code,]);
        $html = Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-url' => $url]);
        $view = \Yii::$app->getView();

        $view->registerJs("
            getUiAjax('$url', '$reloadDiv');
        ");

        return $html;
    }

    /**
     * 
     * @param type $options
     * @param type $reloadDiv
     * @return type
     */
    public static function uiPackageGridItem($item_dataid, $reloadDiv, $action, $mode) {
        $url = Url::to(['/pis/pis-item-order/package-grid-item'
                    , 'item_dataid' => $item_dataid
                    , 'reloadDiv' => $reloadDiv
                    , 'action' => $action
                    , 'mode' => $mode]);
        $html = Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-url' => $url]);
        $view = \Yii::$app->getView();

        $view->registerJs("
            getUiAjax('$url', '$reloadDiv');
        ");

        return $html;
    }

    public static function uiGridOrderCounter($order_id, $order_status, $reloadDiv) {
        $url = Url::to(['/pis/pis-item-order/grid-order-counter', 'order_id' => $order_id, 'order_status' => $order_status, 'reloadDiv' => $reloadDiv]);
        $html = Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-url' => $url]);
        $view = \Yii::$app->getView();

        $view->registerJs("
            getUiAjax('$url', '$reloadDiv');
        ");

        return $html;
    }

    public static function uiOrderQue($order_id, $reloadDiv) {
        $url = Url::to(['/pis/pis-order-counter/order-que', 'order_id' => $order_id, 'reloadDiv' => $reloadDiv]);
        $html = Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-url' => $url]);
        $view = \Yii::$app->getView();

        $view->registerJs("
            /*setInterval(function(){  
                var url = $('#$reloadDiv').attr('data-url');
                    console.log(url);
                $.ajax({
                    method: 'POST',
                    url: url,
                    dataType: 'HTML',
		success: function(result, textStatus) {
		$('#$reloadDiv').html(result);
		}
            });
            }, 8000);*/
            
            getUiAjax('$url', '$reloadDiv');
        ");

        return $html;
    }

    public static function uiDrugAllergy($pt_id, $reloadDiv, $btnDisabled = FALSE) {
        $url = Url::to(['/pis/pis-order-counter/drug-allergy-pt', 'reloadDiv' => $reloadDiv,
                    'pt_id' => $pt_id, 'btnDisabled' => $btnDisabled]);
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

    public static function uiPackageProfile($item_dataid, $reloadDiv, $modal) {
        $url = Url::to(['/pis/pis-item-order/package-profile'
                    , 'item_dataid' => $item_dataid
                    , 'reloadDiv' => $reloadDiv, 'modal' => $modal]);
        $html = Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-url' => $url]);
        $view = \Yii::$app->getView();

        $view->registerJs("
            getUiAjax('$url', '$reloadDiv');
        ");

        return $html;
    }

}
?>

