<?php

namespace backend\modules\patient\classes;

use yii\helpers\Html;
use yii\helpers\Url;
use backend\modules\ezforms2\classes\EzfFunc;

/**
 * Description of EzfHelper
 *
 * @author appxq
 */
class Order2Helper {

    public static function uiQue($reloadDiv) {
        $url = Url::to(['/patient/order2/queue-view', 'reloadDiv' => $reloadDiv]);
        $html = Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-url' => $url]);
        $view = \Yii::$app->getView();

        $view->registerJs("
            getUiAjax('$url', '$reloadDiv');                       
        ");

        return $html;
    }

    public static function uiOrderLists($visit_id, $visit_type, $dept, $order_status, $reloadDiv) {
        $url = Url::to(['/patient/order2/order-lists', 'visit_id' => $visit_id
                    , 'visit_type' => $visit_type, 'dept' => $dept, 'reloadDiv' => $reloadDiv, 'order_status' => $order_status]);
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

    public static function uiOrderHistoryVisit($pt_id, $dept, $reloadDiv) {
        $url = Url::to(['/patient/order2/order-history', 'pt_id' => $pt_id, 'dept' => $dept, 'reloadDiv' => $reloadDiv]);
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

