<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\modules\manageproject\models\SystemLog */

$this->title = Yii::t('app', 'Create System Log');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'System Logs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="system-log-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
