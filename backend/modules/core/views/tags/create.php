<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\modules\core\models\CoreTerms */

$this->title = Yii::t('app', 'Create Core Terms');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Core Terms'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tags-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
