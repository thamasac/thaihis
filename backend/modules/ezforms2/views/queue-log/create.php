<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\modules\ezforms2\models\QueueLog */

$this->title = Yii::t('ezform', 'Create Queue Log');
$this->params['breadcrumbs'][] = ['label' => Yii::t('ezform', 'Queue Logs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="queue-log-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
