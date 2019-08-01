<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\modules\gantt\models\InvProject */

$this->title = 'Inv Project#'.$model->id;
$this->params['breadcrumbs'][] = ['label' => 'Inv Projects', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="inv-project-view">

    <div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h4 class="modal-title" id="itemModalLabel"><?= Html::encode($this->title) ?></h4>
    </div>
    <div class="modal-body">
        <?= DetailView::widget([
	    'model' => $model,
	    'attributes' => [
		'id',
		'project',
		'status',
		'share',
		'approve',
	    ],
	]) ?>
    </div>
</div>
