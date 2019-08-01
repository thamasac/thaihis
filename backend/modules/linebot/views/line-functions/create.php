<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\modules\linebot\models\LineFunctions */

$this->title = Yii::t('linebot', 'Create Line Functions');
$this->params['breadcrumbs'][] = ['label' => Yii::t('linebot', 'Line Functions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="line-functions-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
