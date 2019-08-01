<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\modules\ezmodules\models\Ezmodule */

$this->title = Yii::t('ezmodule', 'Create Ezmodule');
$this->params['breadcrumbs'][] = ['label' => Yii::t('ezmodule', 'Ezmodules'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ezmodule-create">

    <?= $this->render('_form', [
        'model' => $model,
        'tab' => 1,
    ]) ?>

</div>
