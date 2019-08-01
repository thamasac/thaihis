<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('core', ucfirst($term));
$this->params['breadcrumbs'][] = ['label' => Yii::t('core', 'Core Options'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="core-options-config">
    
    <div class="sdbox-header" style="margin-bottom: 15px;">
		<h3><?=  Html::encode($this->title) ?></h3>
    </div>
    
    <?= $this->render('_form-config', [
        'model' => $model,
		'modelFields'=>$modelFields,
    ]) ?>

</div>