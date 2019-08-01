<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\modules\ezforms2\models\EzformFields */

$this->title = Yii::t('ezform', 'Create Ezform Fields');
$this->params['breadcrumbs'][] = ['label' => Yii::t('ezform', 'Ezform Fields'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ezform-fields-create">

    <?= $this->render('_form', [
        'model' => $model,
        'modal' => $modal,
        'dataEzf' => $dataEzf,
        'reloadDiv' => $reloadDiv,
    ]) ?>

</div>
