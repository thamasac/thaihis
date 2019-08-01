<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\modules\ezmodules\models\EzmoduleWidget */

$this->title = Yii::t('ezmodule', 'Widget').'#'.$model->widget_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('ezmodule', 'Widgets'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ezmodule-widget-view">

    <div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h4 class="modal-title" id="itemModalLabel"><?= Html::encode($this->title) ?></h4>
    </div>
    <div class="modal-body">
        <?= DetailView::widget([
	    'model' => $model,
	    'attributes' => [
		'widget_id',
		'widget_name',
		'widget_varname',
		'widget_detail:ntext',
		'widget_example:ntext',
		'options:ntext',
		'enable',
	    ],
	]) ?>
    </div>
</div>
