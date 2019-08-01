<?php
use yii\helpers\Html;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use yii\helpers\Url;
use backend\modules\ezforms2\classes\EzfHelper;
use backend\modules\ezforms2\classes\EzfQuery;
use yii\helpers\ArrayHelper;
use backend\modules\ezforms2\classes\EzfStarterWidget;


backend\modules\ezforms2\assets\ListdataAsset::register($this);

$ezformAll = backend\modules\ezforms2\classes\EzfQuery::getEzformCoDevAll();

$modelFields = EzfQuery::getFieldAllVersion($modelEzf->ezf_id);

if(!isset(Yii::$app->session['ezf_input'])){
    Yii::$app->session['ezf_input'] = backend\modules\ezforms2\classes\EzfQuery::getInputv2All();
}
?>
<ul class="nav nav-tabs" style="margin: 10px 0;">
  <li role="presentation" class="<?=$popup==2?'active':''?>"><a href="<?= Url::to(['/ezforms2/data-lists/index', 'ezf_id'=>$ezf_id, 'target'=>$target, 'view'=>2])?>">Data Entry</a></li>
  <li role="presentation" class="<?=$popup==0?'active':''?>"><a href="<?= Url::to(['/ezforms2/data-lists/index', 'ezf_id'=>$ezf_id, 'target'=>$target , 'view'=>0])?>">Data of All Forms</a></li>
  <li role="presentation" class="<?=$popup==1?'active':''?>"><a href="<?= Url::to(['/ezforms2/data-lists/index', 'ezf_id'=>$ezf_id, 'target'=>$target, 'view'=>1])?>">List of All Forms</a></li>
  <?php if(isset($modelEzf->ezf_db2) && $modelEzf->ezf_db2==1):?>
  <li role="presentation" class="<?=$popup==3?'active':''?>"><a href="<?= Url::to(['/ezforms2/data-lists/index', 'ezf_id'=>$ezf_id, 'target'=>$target, 'view'=>3])?>">Key Operator 2</a></li>
  <?php endif;?>
</ul>
<?php EzfStarterWidget::begin(); ?>

<?php $targetField = EzfQuery::getTargetOne($modelEzf->ezf_id); 
  if($targetField){
  ?>
<div class="panel-body">
    <?php echo $this->render('_emr_target', [
        'ezf_id' => $ezf_id,
        'modelEzf' => $modelEzf,
        'modelFields' =>$modelFields,
        'model' => $searchModel,
        'targetField' => $targetField,
        'modal' => $modal,
        'reloadDiv' => $reloadDiv,
        'target' => $target,
        'showall' => $showall,
        ]);  ?>
  </div>
  <?php }?>

<?php
$title = '{auto}';

$uiView = \backend\modules\ezforms2\classes\EzfHelper::ui($ezf_id)
        ->default_column(1)
        ->reloadDiv('div-key-operator')
        ->targetField($targetField)
        ->target($target)
        ->addbtn($popup==2)
        ->title($title)
        ->pageSize(50);
    
    if($popup==3){
        echo $uiView->buildDb2Grid();
    } else {
        echo $uiView->buildGrid();
    }
    

?>
<?php EzfStarterWidget::end(); ?>