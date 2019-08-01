
<?php
echo \yii\widgets\ListView::widget([
    'id' => 'visit-list',
    'dataProvider' => $dataProvider,
    'itemOptions' => ['tag' => false],
    'layout' => '<div class="list-group">{items}</div><div class="list-pager">{pager}</div>',
    'itemView' => function ($model)use($orderTranPt) {
        return $this->render('_itemlist', [
                    'model' => $model,
                    'orderTranPt' => $orderTranPt
        ]);
    },
    'showOnEmpty' => false,
    'emptyText' => "<a href=\"#\" class=\"list-group-item\"><span><i class=\"fa \"></i> ไม่พบผลลัพธ์</span></a>",
]);
?>