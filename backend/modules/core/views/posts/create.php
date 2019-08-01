<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\modules\core\models\CorePosts */

$this->title = Yii::t('core', 'Create '.ucfirst($type));
$this->params['breadcrumbs'][] = ['label' => Yii::t('core', 'Core Posts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="posts-create">

    <?= $this->render('_form', [
        'model' => $model,
	'type'=>$type
    ]) ?>

</div>
