<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\core\models\CoreItemAlias */

$this->title = Yii::t('core', 'Update {modelClass}: ', [
    'modelClass' => 'Core Item Alias',
]) . ' ' . $model->item_code;
$this->params['breadcrumbs'][] = ['label' => Yii::t('core', 'Core Item Aliases'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->item_code, 'url' => ['view', 'id' => $model->item_code]];
$this->params['breadcrumbs'][] = Yii::t('core', 'Update');
?>
<div class="core-item-alias-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
