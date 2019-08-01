<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\modules\core\models\CoreGenerate */

$this->title = 'Core Generate#'.$model->gen_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('core', 'Core Generates'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="core-generate-view">

    <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h4 class="modal-title" id="itemModalLabel"><?= Html::encode($this->title) ?></h4>
    </div>
    <div class="modal-body">
        <?= DetailView::widget([
			'model' => $model,
			'attributes' => [
				'gen_id',
				'gen_group',
				'gen_name',
				'gen_tag:ntext',
				'gen_link:ntext',
				'gen_process:ntext',
				//'gen_ui:ntext',
				'template_php:ntext',
				'template_html:ntext',
				'template_js:ntext',
			],
		]) ?>
    </div>
</div>
