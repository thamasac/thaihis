<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\modules\core\models\CoreGenerate */

$this->title = Yii::t('core', 'Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('core', 'Core Generates'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="core-generate-create">

    <?= $this->render('_form', [
        'model' => $model,
	'modelUi' => $modelUi,
    ]) ?>

</div>
