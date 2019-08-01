<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
 
$this->title = Yii::t('app','System Error');
//$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="informations-view">

    <div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h4 class="modal-title" id="itemModalLabel"><?= Html::encode($this->title) ?></h4>
    </div>
    <div class="modal-body">
         <?= DetailView::widget([
	    'model' => $model,
	    'attributes' => [
		'code',
		'file',
		'line',
		'message',
		'name',
		'trace_string',
		'created_by',
		'created_at',
	    ],
	]) ?>
    </div>
</div>
