<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\modules\ezforms2\models\EzformVersion */

$this->title = 'Ezform Version#'.$model->ver_code;
$this->params['breadcrumbs'][] = ['label' => Yii::t('ezform', 'Ezform Versions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ezform-version-view">

    <div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h4 class="modal-title" id="itemModalLabel"><?= Html::encode($this->title) ?></h4>
    </div>
    <div class="modal-body">
        <?= DetailView::widget([
	    'model' => $model,
	    'attributes' => [
		'ver_code',
		'ver_for',
		'ver_approved',
		'ver_active',
		'approved_by',
		'approved_date',
		'ver_options:ntext',
		'ezf_id',
		'field_detail:ntext',
		'ezf_sql:ntext',
		'ezf_js:ntext',
		'ezf_error:ntext',
		'ezf_options:ntext',
		'updated_by',
		'updated_at',
		'created_by',
		'created_at',
	    ],
	]) ?>
    </div>
</div>
