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
     <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h3 class="modal-title" id="itemModalLabel"><?= Yii::t('ezmodule', 'My Favorite') ?> 
    </h3>
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

<?=\appxq\sdii\widgets\ModalForm::widget([
    'id' => 'modal-info-app',
    //'size'=>'modal-lg',
]);
?>

<?php \richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]); ?>
<script>
    $('.info-app').on('click', function() {
        modalApp($(this).attr('data-url'));
    }); 
    function modalApp(url) {
        $('#modal-info-app .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
        $('#modal-info-app').modal('show')
        .find('.modal-content')
        .load(url);
    }
    
    function modalCreate(url) {
        $('#modal-create .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
        $('#modal-create').modal('show')
        .find('.modal-content')
        .load(url);
    }
</script>
<?php \richardfan\widget\JSRegister::end(); ?>    
 