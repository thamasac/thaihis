<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\modules\ezmodules\models\EzmoduleFields */

$this->title = Yii::t('ezmodule', 'Create Ezmodule Fields');
$this->params['breadcrumbs'][] = ['label' => Yii::t('ezmodule', 'Ezmodule Fields'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ezmodule-fields-create">

    <?= $this->render('_form', [
        'model' => $model,
        'inform'=>$inform,
        'reloadDiv'=>$reloadDiv,
    ]) ?>

</div>
