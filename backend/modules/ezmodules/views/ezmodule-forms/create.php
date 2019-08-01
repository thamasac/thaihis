<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\modules\ezmodules\models\EzmoduleForms */

$this->title = Yii::t('ezmodule', 'Create Ezmodule Forms');
$this->params['breadcrumbs'][] = ['label' => Yii::t('ezmodule', 'Ezmodule Forms'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ezmodule-forms-create">

    <?= $this->render('_form', [
        'model' => $model,
        'ezf_id'=>$ezf_id,
                    'reloadDiv'=>$reloadDiv,
    ]) ?>

</div>
