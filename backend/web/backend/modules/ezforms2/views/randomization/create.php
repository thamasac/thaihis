<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\modules\ezforms2\models\RandomCode */

$this->title = 'Create Random Code';
$this->params['breadcrumbs'][] = ['label' => 'Random Codes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="random-code-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
