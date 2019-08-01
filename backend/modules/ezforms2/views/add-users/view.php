<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\modules\ezforms2\models\ProfileTcc */

$this->title = 'Profile Tcc#'.$model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Profile Tccs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="profile-tcc-view">
 
    <div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h4 class="modal-title" id="itemModalLabel"><?= Html::encode($this->title) ?></h4>
    </div>
    <div class="modal-body">
        <?= DetailView::widget([
	    'model' => $model,
	    'attributes' => [
		'user_id',
		'name',
		'public_email:email',
		'gravatar_email:email',
		'gravatar_id',
		'location',
		'website',
		'bio:ntext',
		'title',
		'dob',
		'timezone',
		'sitecode',
		'firstname',
		'lastname',
		'department',
		'position',
		'avatar_path',
		'avatar_base_url:url',
		'certificate',
		'site',
	    ],
	]) ?>
    </div>
</div>
