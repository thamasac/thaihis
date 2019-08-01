<?php
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;
use yii\helpers\Html;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$this->title = $model->ezm_name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('ezmodule', 'EzModule'), 'url' => ['/ezmodules/default/index']];
$this->params['breadcrumbs'][] = ['label' => $model->ezm_name, 'url' => ['/ezmodules/ezmodule/view', 'id'=>$model->ezm_id]];
$this->params['breadcrumbs'][] = Yii::t('ezmodule', 'Template');

?>
<div class="modal-header" style="margin-bottom: 15px;">
        <?php
        $icon = Html::img(backend\modules\ezmodules\classes\ModuleFunc::getNoIconModule(), ['width' => 30, 'class' => 'img-rounded']);
if (isset($model->ezm_icon) && !empty($model->ezm_icon)) {
    $icon = Html::img($model['icon_base_url'] . '/' . $model['ezm_icon'], ['width' => 30, 'class' => 'img-rounded']);
} 
         echo ' '.yii\helpers\Html::a('<i class="glyphicon glyphicon-arrow-left"></i> '.Yii::t('ezmodule', 'Back to module'), \yii\helpers\Url::to(['/ezmodules/ezmodule/view', 'id'=>$module]), ['class'=>'btn btn-default btn-sm pull-right']); 
        ?>
	<h4 class="modal-title "><?=$icon?> <?= Html::encode($this->title) ?></h4>
    </div>
<div class="row">
  <div class="col-md-8">
    
      <?php
      echo '<div class="well">';
      $form = ActiveForm::begin([
	'id'=>'form-'.$template,
          'action' => ['/ezmodules/ezmodule/template', 'id' => $module, 'template' => $template],
            'method' => 'get',
            'layout' => 'inline',
            'options' => ['style' => 'display: inline-block;',]
     ]);
     echo '<strong>Template</strong>  ';
     echo yii\helpers\Html::dropDownList('template', $template, ArrayHelper::map($modelDataTmp, 'template_id', 'template_name'), ['class'=>'form-control', 'onChange' => '$("#form-'.$template.'").submit()']);
      
      
     echo ' '.yii\helpers\Html::button('<i class="glyphicon glyphicon-plus"></i>', ['class'=>'btn btn-success', 'id'=>'add-template-btn']);
    
      
      ActiveForm::end();
      echo '</div>';
      
      
       ?>
  </div>
  <div class="col-md-4 sdbox-col">
    
  </div>
</div>


<?php  

if(isset($modelTemplate) ){
          echo $this->render('_form_template', [
                'model' => $modelTemplate,
                'userId' => $userId,
                'module'=>$module,
                 'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
      }

$this->registerJs("
    
$('#add-template-btn').on('click', function() {
    $.ajax({
        method: 'POST',
        url: '".\yii\helpers\Url::to(['/ezmodules/ezmodule/add-tmp'])."',
        dataType: 'Json',
        success: function(result, textStatus) {
            window.location.href = '".\yii\helpers\Url::to(['/ezmodules/ezmodule/template', 'id' => $module, 'template' => ''])."' + result.template_id;
        }
    });
});

");?>