<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\modules\core\models\CoreOptions */

$this->title = 'Core Options#'.$model->option_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('core', 'Core Options'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="core-options-view">

    <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h4 class="modal-title" id="itemModalLabel"><?= Html::encode($this->title) ?></h4>
    </div>
    <div class="modal-body">
        <?= DetailView::widget([
			'model' => $model,
			'attributes' => [
				'option_id',
				'option_name',
				'option_value:ntext',
				'autoload',
				'input_label',
				'input_hint:ntext',
				'input_field',
				'input_data:ntext',
				'input_required',
				'input_validate:ntext',
				'input_meta:ntext',
				'input_order',
			],
		]) ?>
    </div>
</div>
