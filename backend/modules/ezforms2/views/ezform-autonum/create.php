<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\modules\ezforms2\models\EzformAutonum */

$this->title = Yii::t('ezform', 'Create Ezform Autonum');
$this->params['breadcrumbs'][] = ['label' => Yii::t('ezform', 'Ezform Autonums'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ezform-autonum-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
