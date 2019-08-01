<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\modules\ezforms2\models\EzformAutonum */

$this->title = 'Ezform Autonum#'.$model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('ezform', 'Ezform Autonums'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ezform-autonum-view">

    <div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h4 class="modal-title" id="itemModalLabel"><?= Html::encode($this->title) ?></h4>
    </div>
    <div class="modal-body">
        <?= DetailView::widget([
	    'model' => $model,
	    'attributes' => [
		'id',
		'label',
		'ezf_id',
		'ezf_field_id',
		'digit',
		'prefix',
		'count',
		'suffix',
		'status',
		'created_by',
		'created_at',
		'updated_by',
		'updated_at',
	    ],
	]) ?>
    </div>
</div>
