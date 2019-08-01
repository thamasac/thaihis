
<?php

echo \yii\widgets\ListView::widget([
    'id' => 'order-list',
    'dataProvider' => $dataProvider,
    'itemOptions' => ['tag' => false],
    'layout' => '<div class="list-group">{items}</div><div class="list-pager">{pager}</div>',
    'itemView' => function ($model) use ($ezfOrderTran_id, $order_id, $ezf_id, $right_code, $view) {
        if ($view == 'ORDER') {
            $view = '_itemlist';
        } elseif ($view == 'PACKAGE') {
            $view = '_itemlist_package';
        }

        return $this->render($view, ['model' => $model
                    , 'ezfOrderTran_id' => $ezfOrderTran_id
                    , 'ezfPisItem_id' => $ezf_id
                    , 'order_id' => $order_id
                    , 'right_code' => $right_code
        ]);
    },
    'showOnEmpty' => false,
    'emptyText' => "<a href=\"#\" class=\"list-group-item\"><span><i class=\"fa \"></i> ไม่พบผลลัพธ์</span></a>",
]);
?>