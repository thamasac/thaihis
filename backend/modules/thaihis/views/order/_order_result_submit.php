<?php

if (Yii::$app->user->can('doctor')) {
    $status = '2';
} else {
    $status = '1';
}

$urlRedirect = \yii\helpers\Url::to(['/ezmodules/ezmodule/view', 'id' => $ezm_id,
            'search_field[order_tran_status]' => $status]);

$script = "
    window.location.href = '$urlRedirect';
    ";

$this->registerJS($script);
?>