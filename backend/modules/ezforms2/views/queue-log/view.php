<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\modules\ezforms2\models\QueueLog */

$this->title = 'Queue Log#'.$model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('ezform', 'Queue Logs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="queue-log-view">

    <div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h4 class="modal-title" id="itemModalLabel"><?= Html::encode($this->title) ?></h4>
    </div>
    <div class="modal-body">
        <?= DetailView::widget([
	    'model' => $model,
	    'attributes' => [
		'id',
		'unit',
		'ezf_id',
		'dataid',
		'status',
		'enable',
		'setting_id',
		'module_id',
		'current_unit',
		'user_receive',
		'time_receive',
		'options:ntext',
		'updated_by',
		'updated_at',
		'created_by',
		'created_at',
	    ],
	]) ?>
    </div>
</div>
