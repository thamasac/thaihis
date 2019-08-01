<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\modules\core\models\TablesFields */

$this->title = 'Tables Fields#'.$model->table_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('core', 'Tables Fields'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tables-fields-view">

    <div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h4 class="modal-title" id="itemModalLabel"><?= Html::encode($this->title) ?></h4>
    </div>
    <div class="modal-body">
        <?= DetailView::widget([
			'model' => $model,
			'attributes' => [
				'table_id',
				'table_name',
				'table_varname',
				'table_field_type',
				'table_length',
				'table_default:ntext',
				'table_index',
				'input_field',
				'input_label',
				'input_hint:ntext',
				'input_specific:ntext',
				'input_data:ntext',
				'input_required',
				'input_validate:ntext',
				'input_meta:ntext',
				'input_order',
				'update_time',
				'update_by',
				'create_time',
				'create_by',
			],
		]) ?>
    </div>
</div>
