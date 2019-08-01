<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\modules\core\models\CorePosts */

$this->title = 'Core Posts#'.$model->ID;
$this->params['breadcrumbs'][] = ['label' => Yii::t('core', 'Core Posts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="posts-view">

    <div class="modal-header">
	<h4 class="modal-title" id="itemModalLabel"><?= Html::encode($this->title) ?></h4>
    </div>
    <div class="modal-body">
        <?= DetailView::widget([
	    'model' => $model,
	    'attributes' => [
		'ID',
		'post_author',
		'post_date',
		'post_date_gmt',
		'post_content:ntext',
		'post_title:ntext',
		'post_excerpt:ntext',
		'post_status',
		'comment_status',
		'ping_status',
		'post_password',
		'post_name',
		'to_ping:ntext',
		'pinged:ntext',
		'post_modified',
		'post_modified_gmt',
		'post_content_filtered:ntext',
		'post_parent',
		'guid',
		'menu_order',
		'post_type',
		'post_mime_type',
		'comment_count',
	    ],
	]) ?>
    </div>
</div>
