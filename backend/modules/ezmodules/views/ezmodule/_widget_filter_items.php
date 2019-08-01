<?php

use yii\helpers\Html;
use yii\helpers\Url;
use backend\modules\ezmodules\models\EzmoduleMenu;
use appxq\sdii\widgets\ModalForm;
use backend\modules\ezmodules\classes\ModuleFunc;
use backend\modules\ezmodules\classes\ModuleQuery;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;



        
$userId = Yii::$app->user->id;
?>

<div id="ezmodule-filter-items">
    <?php
    $form = ActiveForm::begin([
                'id' => 'jump_menu',
                'action' => Url::to(['/ezmodules/ezmodule/view', 'id' => $module, 'addon' => $addon]),
                'method' => 'get',
                'layout' => 'inline',
                'options' => ['style' => 'display: inline-block;',]
    ]);
    ?>
    <?= Html::button('<span class="fa fa-filter"></span> '.Yii::t('ezmodule', 'Create Filter'), ['data-url' => Url::to(['/ezmodules/ezmodule-filter/create', 'module' => $model['ezm_id']]), 'class' => 'btn btn-success action-filter-list']) ?>
    <div class="input-group">
        <span class="input-group-addon" style="background-color: inherit; border: inherit;"><label><?=Yii::t('ezmodule', 'List records based on the Filter: ')?></label></span>  
      <?= Html::dropDownList('filter', $filter, ArrayHelper::map($dataFilter, 'filter_id', 'filter_name'), ['class' => 'form-control', 'prompt' =>Yii::t('ezmodule', 'Show All'), 'onChange' => '$("#jump_menu").submit()']) ?>
      <span class="input-group-btn">
          <?php
            if ($filter > 0) {
                echo Html::button('<span class="glyphicon glyphicon-pencil"></span> ', ['data-url' => Url::to(['/ezmodules/ezmodule-filter/update', 'id' => $filter]), 'class' => 'btn btn-primary action-filter-list']);
                echo Html::a('<span class="glyphicon glyphicon-trash"></span> ', ['/ezmodules/ezmodule-filter/delete', 'id' => $filter, 'module'=>$module, 'addon'=>$addon], 
                    [ 'class' => 'btn btn-danger ',
                        'id'=>'del-filter-list',
                    ]);
            }
            ?>
      </span>
    </div><!-- /input-group -->
    
    
<?php ActiveForm::end(); ?>


</div>