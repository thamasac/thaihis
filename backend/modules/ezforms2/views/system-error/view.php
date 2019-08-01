<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\modules\ezforms2\models\SystemError */

$this->title = 'System Error#'.$model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('ezform', 'System Errors'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="system-error-view">

    <div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h4 class="modal-title" id="itemModalLabel"><?= Html::encode($this->title) ?></h4>
    </div>
    <div class="modal-body">
        <?= DetailView::widget([
	    'model' => $model,
	    'attributes' => [
		'id',
		'code',
                'name',
                'line',
		'file:ntext',
		'message:ntext',
		'trace_string:ntext',
		'created_by',
		'created_at',
	    ],
	]) ?>
    </div>
</div>
