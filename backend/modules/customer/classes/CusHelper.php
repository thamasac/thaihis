<?php

namespace backend\modules\customer\classes;

use yii\helpers\Html;
use yii\helpers\Url;

/**
 * Description of EzfHelper
 *
 * @author appxq
 */
class CusHelper {

    public static function uiOrderLists($order_id, $reloadDiv) {
        $url = Url::to(['/pis2/pis-item-order/order-lists', 'order_id' => $order_id, 'reloadDiv' => $reloadDiv]);
        $html = Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-url' => $url]);
        $view = \Yii::$app->getView();

        $view->registerJs("
            getUiAjax('$url', '$reloadDiv');
        ");

        return $html;
    }

    public static function uiGridOrder($visit_id, $reloadDiv) {
        $url = Url::to(['/pis2/pis-item-order/grid-order', 'visit_id' => $visit_id, 'reloadDiv' => $reloadDiv]);
        $html = Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-url' => $url]);
        $view = \Yii::$app->getView();

        $view->registerJs("
            getUiAjax('$url', '$reloadDiv');
        ");

        return $html;
    }

    public static function uiGridOrderCounter($order_id, $order_status, $reloadDiv) {
        $url = Url::to(['/pis2/pis-item-order/grid-order-counter', 'order_id' => $order_id, 'order_status' => $order_status, 'reloadDiv' => $reloadDiv]);
        $html = Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-url' => $url]);
        $view = \Yii::$app->getView();

        $view->registerJs("
            getUiAjax('$url', '$reloadDiv');
        ");

        return $html;
    }

    public static function uiOrderQue($order_id, $reloadDiv) {
        $url = Url::to(['/pis2/pis-order-counter/order-que', 'order_id' => $order_id, 'reloadDiv' => $reloadDiv]);
        $html = Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-url' => $url]);
        $view = \Yii::$app->getView();

        $view->registerJs("
            setInterval(function(){  
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
            }, 8000);
            
            getUiAjax('$url', '$reloadDiv');
        ");

        return $html;
    }

}
?>

