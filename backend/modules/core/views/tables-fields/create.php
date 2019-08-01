<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\modules\core\models\TablesFields */

$this->title = Yii::t('core', 'Create Tables Fields');
$this->params['breadcrumbs'][] = ['label' => Yii::t('core', 'Tables Fields'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tables-fields-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
