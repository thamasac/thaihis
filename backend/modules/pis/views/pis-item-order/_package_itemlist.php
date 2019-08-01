<?php
$urlSelect = \yii\helpers\Url::to(['/pis/pis-item-order/package-show-items',
            'item_dataid' => $model['id']//package_id
            , 'visit_id' => $visit_id
            , 'right_code' => $right_code
            , 'options' => $options
            , 'order_id' => $order_id, 'mode' => 'PACKAGE']);
?>
<div class="list-group-item" data-id="<?= $model['id'] ?>" data-url="<?= $urlSelect . '&action=SELECT' ?>"> 
    <?php
    if ($model['user_create'] == Yii::$app->user->identity->profile->user_id)
        echo \yii\helpers\Html::button('<i class="glyphicon glyphicon-pencil"></i>'
                , ['class' => 'btn btn-primary btn-xs pull-right btn-package-edit',
            'data-url' => $urlSelect . '&action=EDIT',
        ]);
    ?>
  <div class="click-package-item">
    <span>Package : </span>
    <strong class="list-group-item-heading text-primary">
        <?= $model['package_name']; ?> 

    </strong> 
    <strong class="pull-right" style="color:#999;"><?= ($model['package_shared_status'] == '2' ? 'Public' : '') ?></strong>
    <div class="list-group-item-text" style="margin-left: 5px;">
      <strong style="color:#999;"><?= $model['package_detail']; ?></strong>
    </div>  
  </div>

</div>