<?php
use backend\modules\ezmodules\classes\ModuleQuery;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$userId = Yii::$app->user->id;

$modelMyModules = ModuleQuery::getMyModule($userId);
//$modelFavModules = ModuleQuery::getFavModule($userId);
//$modelSystemModules = ModuleQuery::getSystemModule();

?>

<div class="modal-header" style="margin-bottom: 10px;">
    <h3 class="modal-title" id="itemModalLabel"><?= Yii::t('app', 'My Modules') ?> </h3>
</div>
<div class="modal-body">
    <div class="row">
        <?php 
        echo $this->render('_item', [
            'model' => $modelMyModules,
            'mode' => 0,
        ]);
        ?>
    </div>
</div>
