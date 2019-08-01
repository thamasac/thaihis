<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\widgets\Select2;
use appxq\sdii\helpers\SDHtml;
use yii\helpers\Url;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model backend\models\UserSearch */
/* @var $form yii\widgets\ActiveForm */
?> 
<div class="user-search" >
    <?php
    $form = ActiveForm::begin([
                'id' => $model->formName(),
                'action' => ['index', 'tab' => $tab],
                'method' => 'get',
                'options' => ['data-pjax' => true, ],
    ]);
    ?>
        <?php
//        $form->field($model, 'category_id')->widget(kartik\tree\TreeViewInput::classname(), [
//            'id' => 'category_id_find',
//            'dropdownConfig' => [
//                'input'=>['placeholder' => Yii::t('ezform', 'Forms Category Filter')],
//                //'options'=>['style'=>'width: 400px;'],
//                ],
//            'query' => \backend\modules\ezforms2\models\EzformTree::find()->where('readonly=1 or userid=' . Yii::$app->user->id . ' or id IN (select distinct root from ezform_tree where userid=' . Yii::$app->user->id . ')')->addOrderBy('root, lft'),
//            'headingOptions' => ['label' => 'Categories'],
//            'asDropdown' => true,
//            'multiple' => false,
//            'fontAwesome' => true,
//            'rootOptions' => [
//                'label' => '<i class="fa fa-home"></i> ',
//                'class' => 'text-success',
//                'options' => ['disabled' => false]
//            ],
//        ])->label(false)
        ?>
  <div class="form-inline">
        <?php
        $categData = \backend\modules\ezforms2\models\EzformTree::find()->where('readonly=1 or userid=' . Yii::$app->user->id . ' or id IN (select distinct root from ezform_tree where userid=' . Yii::$app->user->id . ')')->addOrderBy('root, lft')->all();
        $categItem = \yii\helpers\ArrayHelper::map($categData, 'id', 'name');
        
        echo $form->field($model, 'category_id')->dropDownList($categItem, ['placeholder' => Yii::t('ezform', 'Select Category...'), 'prompt'=>Yii::t('ezform', 'Show All Category')])->label(false);
        ?>
    
        <?= $form->field($model, 'ezf_name')->textInput(['class' => 'form-control', 'placeholder' => Yii::t('ezform', 'Find the form name.')])->label(false); ?>

        <?php
            $ezf_initValue = '';
            if(!empty($model->created_by)){
                $userprofile = \common\modules\user\models\Profile::findOne($model->created_by);
                $ezf_initValue = $userprofile->firstname .' '.$userprofile->lastname;
            }
            
            echo $form->field($model, 'created_by')->widget(kartik\widgets\Select2::classname(), [
                'initValueText' => $ezf_initValue,
                'options' => ['placeholder' => Yii::t('ezform', 'Find the creator name.'), ],
                'pluginOptions' => [
                    'minimumInputLength' => 0,
                    'allowClear' => true,
                    'ajax' => [
                        'url' => Url::to(['/ezforms2/ezform/get-user']),
                        'dataType' => 'json',
                        'data' => new JsExpression('function(params) { return {q:params.term}; }'),
                        'error' => new JsExpression('function(jqXHR,error, errorThrown) { 
                            if(jqXHR.status&&jqXHR.status==403){
                                window.location.href = "'. Url::to(['/user/login']).'"
                            }
                        }'),
                    ],
                    'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                    'templateResult' => new JsExpression('function(result) { return result.text; }'),
                    'templateSelection' => new JsExpression('function (selection) { return selection.text; }'),
                ],
            ])->label(false);
            ?>
        
    <div class="form-group" style="margin-bottom: 10px;">
        <?= Html::button(SDHtml::getBtnSearch(), ['class' => 'btn btn-default', 'type' => 'submit']); ?>
        
    </div>
        </div>
    <?php ActiveForm::end(); ?>
</div>

<?php
$this->registerJs(" 
$('form#{$model->formName()}').on('change', function(e) {
    $(this).submit();   
});
");
?>
