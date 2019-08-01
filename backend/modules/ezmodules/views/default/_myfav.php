<?php
use backend\modules\ezmodules\classes\ModuleQuery;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$userId = Yii::$app->user->id;

//$modelMyModules = ModuleQuery::getMyModule($userId);
$modelFavModules = ModuleQuery::getFavModule($userId);
$modelSystemModules = ModuleQuery::getSystemModule();

?>

<div class="modal-header" style="margin-bottom: 10px;">
    <h3 class="modal-title" id="itemModalLabel"><?= Yii::t('ezmodule', 'My Favorite') ?> <?= \yii\helpers\Html::a('<i class="fa fa-shopping-cart"></i> ' . Yii::t('ezmodule', 'Shopping'), ['/ezmodules/default/index', 'tab' => 3], ['class' => 'btn btn-info btn-sm']) ?></h3>
</div>
<div class="modal-body">
    <div class="row">
        <?php 
        echo $this->render('_item', [
            'model' => $modelFavModules,
            'mode' => 0,
        ]);
        ?>
    </div>
</div>

<div class="modal-header" style="margin-bottom: 10px;">
    <h3 class="modal-title" id="itemModalLabel"><?= Yii::t('ezmodule', 'Assigned to me') ?> </h3>
</div>
<?= $this->render('_tome')?>

<div class="modal-header" style="margin-bottom: 10px;">
    <h3 class="modal-title" id="itemModalLabel"><?= Yii::t('ezmodule', 'Built-in Modules') ?></h3>
</div>
<div class="modal-body">
    <div class="row">
        <?php 
        echo $this->render('_item', [
            'model' => $modelSystemModules,
            'mode' => 0,
        ]);
        ?>
    </div>
</div>