
<?=

\yii\widgets\ListView::widget([
    'id' => 'visit-list',
    'dataProvider' => $dataProvider,
    'itemOptions' => ['tag' => false],
    'layout' => '<div class="list-group">{items}</div><div class="list-pager">{pager}</div>',
    'itemView' => function ($model)use($ezf_id, $v) {
        return $this->render('_lib_itemlist', [
                    'model' => $model,
                    'ezf_id' => $ezf_id,
                    'v' => $v
        ]);
    },
    'showOnEmpty' => false,
    'emptyText' => "<a href=\"#\" class=\"list-group-item\"><span><i class=\"fa \"></i> ไม่พบผลลัพธ์</span></a>",
]);

$this->registerJs("
    $('#visit-list .list-group-item').mouseover(function() {
        $(this).children('.btn-add-input-lib').removeClass('hidden');
    }).mouseout(function() {
        $(this).children('.btn-add-input-lib').addClass('hidden');
    });
    $('#lib-item-lists .view-item').removeClass('dads-children');
    $('#lib-item-lists .view-item .button-item').addClass('hide');    
    ");
?>       