<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\modules\core\models\CoreFields */

$this->title = Yii::t('core', 'Create Core Fields');
$this->params['breadcrumbs'][] = ['label' => Yii::t('core', 'Core Fields'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="core-fields-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
