<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\modules\core\models\CoreFields */

$this->title = 'Core Fields#'.$model->field_code;
$this->params['breadcrumbs'][] = ['label' => Yii::t('core', 'Core Fields'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="core-fields-view">

    <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h4 class="modal-title" id="itemModalLabel"><?= Html::encode($this->title) ?></h4>
    </div>
    <div class="modal-body">
        <?= DetailView::widget([
			'model' => $model,
			'attributes' => [
				'field_code',
				'field_internal',
				'field_class',
				'field_name',
				'field_meta:ntext',
				'field_description:ntext',
			],
		]) ?>
    </div>
</div>
