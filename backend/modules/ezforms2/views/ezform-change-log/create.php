<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\modules\ezforms2\models\EzformChangeLog */

$this->title = Yii::t('ezform', 'Create Ezform Change Log');
$this->params['breadcrumbs'][] = ['label' => Yii::t('ezform', 'Ezform Change Logs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ezform-change-log-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
