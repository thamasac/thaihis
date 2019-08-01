<?php
use backend\modules\ezmodules\classes\ModuleQuery;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$userId = Yii::$app->user->id;

$modelAssignModules = ModuleQuery::getAssignModule($userId);

?>
<div class="modal-body">
    <div class="row">
        <?php 
        echo $this->render('_item', [
            'model' => $modelAssignModules,
            'mode' => 0,
        ]);
        ?>
    </div>
</div>