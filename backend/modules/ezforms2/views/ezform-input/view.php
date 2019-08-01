<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\modules\ezforms2\models\EzformInput */

$this->title = 'Ezform Input#'.$model->input_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Ezform Inputs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ezform-input-view">

    <div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h4 class="modal-title" id="itemModalLabel"><?= Html::encode($this->title) ?></h4>
    </div>
    <div class="modal-body">
        <?= DetailView::widget([
	    'model' => $model,
	    'attributes' => [
		'input_id',
		'input_name',
		'input_class',
		'input_function',
		'input_specific:ntext',
		'input_option:ntext',
		'table_field_type',
		'table_field_length',
		'input_version',
		'input_order',
                'input_active'
	    ],
	]) ?>
    </div>
</div>
