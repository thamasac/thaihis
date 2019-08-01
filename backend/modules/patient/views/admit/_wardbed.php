
<div class="row">           
  <div id="listview-beds">
    <?= $this->render('_searchbed', ['model' => $searchModel, 'ezf_id' => $ezf_id, 'dept' => $dept, 'reloadDiv' => $reloadDiv]) ?>
    <?php
    echo \yii\widgets\ListView::widget([
        'id' => 'listview-bed',
        'dataProvider' => $dataProvider,
        //itemOptions' => ['style' => 'float: left;margin-left:0px;margin-right:15px;'],
        'itemOptions' => ['class' => 'col-xs-3'],
        'layout' => '<div class="list-items">{items}</div><div class="list-pager">{pager}</div>',
        'itemView' => function ($model) use($dept, $reloadDiv, $tab, $module) {
            return $this->render('_itembed', ['model' => $model, 'dept' => $dept, 'reloadDiv' => $reloadDiv, 'module' => $module, 'tab' => $tab]);
        },
        //'showOnEmpty' => false,
        'emptyText' => '<h1 class="text-center" style="font-size: 45px; color: #ccc;">'
        . Yii::t('patient', 'Find not found.') . '</h1>',
    ])
    ?>
  </div> 
</div>