<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\modules\core\models\CoreItemAlias */

$this->title = Yii::t('core', 'Create Core Item Alias');
$this->params['breadcrumbs'][] = ['label' => Yii::t('core', 'Core Item Aliases'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="core-item-alias-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
