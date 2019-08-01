<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\modules\core\models\CoreOptions */

$this->title = Yii::t('core', 'Create Core Options');
$this->params['breadcrumbs'][] = ['label' => Yii::t('core', 'Core Options'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="core-options-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
