<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\modules\ezmodules\models\EzmoduleTemplate */

$this->title = 'Template#'.$model->template_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('ezmodule', 'Template'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ezmodule-template-view">

    <div class="modal-header">
	<h4 class="modal-title" id="itemModalLabel"><?= Html::encode($this->title) ?></h4>
    </div>
    <div class="modal-body">
        <?= DetailView::widget([
	    'model' => $model,
	    'attributes' => [
		'template_id',
		'template_name',
		'template_html:ntext',
		'template_js:ntext',
		'template_system',
		'public',
		'sitecode',
		'created_by',
		'created_at',
		'updated_by',
		'updated_at',
	    ],
	]) ?>
    </div>
</div>
