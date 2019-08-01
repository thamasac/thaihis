<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\modules\ezforms2\models\EzformVersion */

$this->title = Yii::t('ezform', 'Create Ezform Version');
$this->params['breadcrumbs'][] = ['label' => Yii::t('ezform', 'Ezform Versions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ezform-version-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
