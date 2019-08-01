<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\modules\gantt\models\InvProject */

$this->title = 'Create Inv Project';
$this->params['breadcrumbs'][] = ['label' => 'Inv Projects', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="inv-project-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
