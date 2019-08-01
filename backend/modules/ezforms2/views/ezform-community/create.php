<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\modules\ezforms2\models\EzformCommunity */

$this->title = Yii::t('ezform', 'Create Ezform Community');
$this->params['breadcrumbs'][] = ['label' => Yii::t('ezform', 'Ezform Communities'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ezform-community-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
