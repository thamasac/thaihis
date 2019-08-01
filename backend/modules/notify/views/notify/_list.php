<?php

?>

<div class="panel panel-default">
    <div class="panel-heading">
        <?= $model['forum_title']?>
    </div>
    <div class="panel-body">
        <div class="col-md-4 text-center">
            <?=
            \yii\helpers\Html::img(
                    $model['avatar_base_url'] . "/" .
                    $model['avatar_path'], [
                'width' => '100', 'height' => '100'
            ])
            ?>
            <br>
            <i class="fa fa-user-circle-o"></i> <?= $model['name'] ?>
            <br>
            <i class="fa fa-envelope"></i> <?= $model['public_email'] ?> 
            <i class="fa fa-clock-o"></i> <?= $model['created_at'] ?> 
        </div>
        <div class="col-md-8">
            </i> <?= $model['reply_comment'] ?> 
        </div>
    </div>
</div>
