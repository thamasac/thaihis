<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\modules\ezforms2\models\Ezform */

$this->title = Yii::t('app', 'Create Ezform');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Ezforms'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ezform-create">

    <?= $this->render('_form', [
       'model' => $model,'modelFields'=>$modelFields , 'auto'=>$auto
    ]) ?>

</div>
