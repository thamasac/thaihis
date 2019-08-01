<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\helpers\Url;
use appxq\sdii\widgets\GridView;
use appxq\sdii\widgets\ModalForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use backend\modules\ezmodules\classes\ModuleFunc;
use yii\helpers\ArrayHelper;
use backend\modules\ezforms2\classes\EzfHelper;

Yii::$app->controller->id = "myworkbench";
?>
<hr/>
<div class="modal-body">
    <h3 class="alert alert-success"><i class="glyphicon glyphicon-th"></i> Assigned Ezforms and Modules</h3>
    <div class="clearfix"></div>
    <div class="col-md-12">
        <h4><i class="fa fa-cubes"></i> Modules</h4><hr/>
        <div class="row">
            <?php
            echo $this->render('_item', [
                'model' => $modelAssignModules,
                'item_type' => '1',
                'mode' => 0,
            ]);
            ?>
        </div>
        <h4><i class="fa fa-archive"></i> Ezforms</h4><hr/>
        <div class="row">
            <?php
            echo $this->render('_item', [
                'model' => $modelAssignForm,
                'item_type' => '2',
                'mode' => 0,
            ]);
            ?>
        </div>
    </div>
    <div class="clearfix"></div>
    <h3 class="alert alert-success"><i class="glyphicon glyphicon-th"></i> Favorite Ezforms and Modules</h3>
    <div class="col-md-12">
        <h4><i class="fa fa-cubes"></i> Modules</h4><hr/>
        <div class="row">
            <?php
            echo $this->render('_item', [
                'model' => $modelFavoriteModules,
                'item_type' => '1',
                'mode' => 0,
            ]);
            ?>
        </div>
        <h4><i class="fa fa-archive"></i> Ezforms</h4><hr/>
        <div class="row">
            <?php
            echo $this->render('_item', [
                'model' => $modelFavoriteForm,
                'item_type' => '2',
                'mode' => 0,
            ]);
            ?>
        </div>
    </div>
</div>
<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>
<script>

</script>
<?php \richardfan\widget\JSRegister::end(); ?>