<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\modules\manageproject\models\SystemLog */

$this->title = 'System Log#'.$model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'System Logs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="system-log-view">

    <div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h4 class="modal-title" id="itemModalLabel"><?= Html::encode($this->title) ?></h4>
    </div>
    <div class="modal-body">
        <?= DetailView::widget([
	    'model' => $model,
	    'attributes' => [
		'id',
		'create_date',
		'create_by',
		'action',
		'detail:ntext',
	    ],
	]) ?>
    </div>
</div>
