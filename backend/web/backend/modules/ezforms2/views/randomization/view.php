<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\modules\ezforms2\models\RandomCode */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Random Codes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="random-code-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'code_random:ntext',
            'max_index',
            'code_index',
            'seed',
            'treatment',
            'block_size',
            'list_length',
        ],
    ]) ?>

</div>
