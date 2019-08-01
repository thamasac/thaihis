<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\ezmodules\models\EzmoduleForms */

$this->title = Yii::t('ezmodule', 'Update {modelClass}: ', [
    'modelClass' => 'Ezmodule Forms',
]) . ' ' . $model->form_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('ezmodule', 'Ezmodule Forms'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->form_id, 'url' => ['view', 'id' => $model->form_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ezmodule-forms-update">

    <?= $this->render('_form', [
        'model' => $model,
        'ezf_id'=>$ezf_id,
                    'reloadDiv'=>$reloadDiv,
    ]) ?>

</div>
