<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\modules\ezforms2\models\SystemError */

$this->title = Yii::t('ezform', 'Create System Error');
$this->params['breadcrumbs'][] = ['label' => Yii::t('ezform', 'System Errors'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="system-error-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
