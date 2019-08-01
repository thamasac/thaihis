<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\modules\ezforms2\models\RandomCode */

$this->title = 'Random Code : '.$model->name;
$this->params['breadcrumbs'][] = ['label' => 'Random Codes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="random-code-view">

    <div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h4 class="modal-title" id="itemModalLabel"><?= Html::encode($this->title) ?></h4>
    </div>
    <div class="modal-body">
        <?= DetailView::widget([
	    'model' => $model,
	    'attributes' => [
//		'id',
		'name',
		'code_random:ntext',
	    ],
	]) ?>
    </div>
</div>
