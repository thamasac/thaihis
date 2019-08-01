<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\modules\ezmodules\models\EzmoduleWidget */

$this->title = Yii::t('ezmodule', 'Create Widget');
$this->params['breadcrumbs'][] = ['label' => Yii::t('ezmodule', 'Widget'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ezmodule-widget-create">

    <?= $this->render('_form', [
        'model' => $model,
        'modal' => $modal,
        'ezm_id' => $ezm_id,
    ]) ?>

</div>
