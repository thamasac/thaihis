<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\modules\ezforms2\models\EzformInput */

$this->title = Yii::t('app', 'Create Ezform Input');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Ezform Inputs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ezform-input-create">

    <?= $this->render('_form', [
        'model' => $model,
        'content' => $content,
    ]) ?>

</div>
