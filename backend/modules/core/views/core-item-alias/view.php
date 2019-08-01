<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\modules\core\models\CoreItemAlias */

$this->title = 'Core Item Alias#'.$model->item_code;
$this->params['breadcrumbs'][] = ['label' => Yii::t('core', 'Core Item Aliases'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="core-item-alias-view">

    <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h4 class="modal-title" id="itemModalLabel"><?= Html::encode($this->title) ?></h4>
    </div>
    <div class="modal-body">
        <?= DetailView::widget([
			'model' => $model,
			'attributes' => [
				'item_code',
				'item_name',
				'item_data:ntext',
			],
		]) ?>
    </div>
</div>
