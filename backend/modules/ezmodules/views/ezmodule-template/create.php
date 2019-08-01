<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\modules\ezmodules\models\EzmoduleTemplate */

$this->title = Yii::t('ezmodule', 'Create Template');
$this->params['breadcrumbs'][] = ['label' => Yii::t('ezmodule', 'Template'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ezmodule-template-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
