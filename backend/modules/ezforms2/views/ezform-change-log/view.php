<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\modules\ezforms2\models\EzformChangeLog */

$this->title = 'Ezform Change Log#'.$model->log_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('ezform', 'Ezform Change Logs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ezform-change-log-view">

    <div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h4 class="modal-title" id="itemModalLabel"><?= Html::encode($this->title) ?></h4>
    </div>
    <div class="modal-body">
        <?= DetailView::widget([
	    'model' => $model,
	    'attributes' => [
		'log_id',
		'ezf_id',
		'ezf_field_id',
		'ezf_version',
		'log_type:ntext',
		'log_event',
		'log_count',
		'log_ref_id',
		'log_detail:ntext',
		'log_ref_table',
		'log_ref_version',
		'log_ref_varname',
		'created_by',
		'created_at',
		'updated_by',
		'updated_at',
	    ],
	]) ?>
    </div>
</div>
