  <?php

  echo \yii\widgets\ListView::widget([
      'id' => 'package-list',
      'dataProvider' => $dataProvider,
      'itemOptions' => ['tag' => false],
      'layout' => '<div class="list-group">{items}</div><div class="list-pager">{pager}</div>',
      'itemView' => function ($model) use($visit_id, $order_id, $options, $right_code) {
          return $this->render('_package_itemlist', [
                      'model' => $model
                      , 'visit_id' => $visit_id
                      , 'order_id' => $order_id
                      , 'options' => $options
                      , 'right_code' => $right_code
          ]);
      },
      'showOnEmpty' => false,
      'emptyText' => "<a href=\"#\" class=\"list-group-item\"><span><i class=\"fa \"></i> ไม่พบผลลัพธ์</span></a>",
  ]);
  ?>