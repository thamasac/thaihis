<?php

echo $this->render('_search', ['model' => $searchModel, 'reloadDiv' => $reloadDiv]);
echo \yii\widgets\ListView::widget([
    'id' => 'que-list',
    'dataProvider' => $dataProvider,
    'itemOptions' => ['tag' => false],
    'layout' => '<div class="sidebar-nav-title text-center"><i class="fa fa-user-circle-o"></i> ' . Yii::t('patient', 'Patients Queue') . ' ({summary})</div>'
    . '<div class="list-group">{items}</div><div class="list-pager">{pager}</div>',
    'summary' => '{totalCount}',
    'itemView' => function ($model) use( $dept, $reloadDiv) {
        return $this->render('_item_que', [
                    'model' => $model, 'dept' => $dept, 'reloadDiv' => $reloadDiv
        ]);
    },
]);
?>
