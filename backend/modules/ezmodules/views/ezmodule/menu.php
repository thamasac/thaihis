<?php
use yii\helpers\Html;
use appxq\sdii\widgets\ModalForm;
use backend\modules\ezmodules\classes\ModuleFunc;

/* @var $this yii\web\View */
/* @var $model backend\modules\ezmodules\models\Ezmodule */
\backend\modules\ezmodules\assets\ModuleAsset::register($this);
$this->title = $model->ezm_name;
$userId = Yii::$app->user->id;
$icon = Html::img(ModuleFunc::getNoIconModule(), ['width' => 30, 'class' => 'img-rounded']);
if (isset($model->ezm_icon) && !empty($model->ezm_icon)) {
    $icon = Html::img($model['icon_base_url'] . '/' . $model['ezm_icon'], ['width' => 30, 'class' => 'img-rounded']);
} 
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

?>
<div class="ezmodule-view">

    <div class="modal-header">
        <?php
        $ezm_builder = explode(',', $model['ezm_builder']);
        if((Yii::$app->user->can('administrator')) || $model['created_by']==$userId || in_array($userId, $ezm_builder)){
            echo Html::a('', ["/ezmodules/ezmodule/update", 'id'=>$module], [
                'id'=>'modal-btn-ezmodule',
                'class'=>'fa fa-cog fa-2x pull-right underline',
                'data-toggle'=>'tooltip',
                'title'=>Yii::t('ezmodule', 'Module'),
            ]);
        }
        ?>
	<h4 class="modal-title"><?=$icon?> <?= Html::encode($this->title) ?></h4>
    </div>
    <div class="modal-body" >
        <?=$this->render('_widget_menu', [
            'modelOrigin'=>$model->attributes,
            'menu'=>$modelMenu->menu_id,
            'module'=>$module,
        ])?>
    </div>
    
</div>
<?=
ModalForm::widget([
    'id' => 'modal-ezmodule',
    'size' => 'modal-lg',
    'tabindexEnable' => FALSE,
]);
?>

<?php $this->registerJs("
    
$('#modal-btn-ezmodule').on('click', function() {
    modalEzmodule($(this).attr('href'));
    return false;
});

$('#modal-ezmodule').on('hidden.bs.modal', function (e) {
  location.reload();
});

function modalEzmodule(url) {
    $('#modal-ezmodule .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
    $('#modal-ezmodule').modal('show')
    .find('.modal-content')
    .load(url);
}

"); ?>
