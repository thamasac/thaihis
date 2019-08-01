<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\modules\ezmodules\models\EzmoduleFilter */

$this->title = Yii::t('ezmodule', 'Create Ezmodule Filter');
$this->params['breadcrumbs'][] = ['label' => Yii::t('ezmodule', 'Ezmodule Filters'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ezmodule-filter-create">

    <?= $this->render('_form', [
        'model' => $model,
        'user_module'=>$user_module,
                    'userId'=>$userId,
        'ezf_id'=>$ezf_id,
    ]) ?>

</div>
