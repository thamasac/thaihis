<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div id="ezf-search" style="padding: 5px;">
<?php
$form = ActiveForm::begin([
            'id' => 'search-'.$model->formName(),
            'action' => ['index'],
            'method' => 'get',
]);
?>
    <?= Html::activeTextInput($model, 'ezm_name', ['class'=>'form-control search-query', 'placeholder'=> Yii::t('ezform', 'Search the form name.')]) ?>
<?php ActiveForm::end(); ?>
</div>  
<?php
$this->registerJs(" 
$('form#search-{$model->formName()}').on('change', function(e) {
    $(this).submit();   
});
");
?>
