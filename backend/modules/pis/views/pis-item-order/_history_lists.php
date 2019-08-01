
<?=

\yii\widgets\ListView::widget([
    'id' => 'visit-list',
    'dataProvider' => $dataProvider,
    'itemOptions' => ['tag' => FALSE],
    'layout' => '<div class="list-group">{items}</div><div class="list-pager">{pager}</div>',
    'itemView' => function ($model, $key, $index)use($ptid, $visit_id, $order_id, $options, $right_code) {
        return $this->render('_history_item', [
                    'model' => $model
                    , 'visit_id' => $visit_id
                    , 'order_id' => $order_id
                    , 'options' => $options
                    , 'right_code' => $right_code, 'ptid' => $ptid
        ]);
    },
    'showOnEmpty' => false,
    'emptyText' => "<a href=\"#\" class=\"list-group-item\"><span><i class=\"fa \"></i> ไม่พบผลลัพธ์</span></a>",
])
?>