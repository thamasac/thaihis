<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\ezforms2\models\RandomCodeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Random Codes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="random-code-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Random Code', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            'code_random:ntext',
            'max_index',
            'code_index',
            // 'seed',
            // 'treatment',
            // 'block_size',
            // 'list_length',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
