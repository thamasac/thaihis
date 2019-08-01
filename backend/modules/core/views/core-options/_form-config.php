<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use backend\modules\core\classes\CoreFunc;
/* @var $this yii\web\View */
/* @var $model backend\modules\core\models\CoreOptions */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="core-options-form">

    <?php $form = ActiveForm::begin([
		'id'=>$model->formName(),
		'layout' => 'horizontal',
		'fieldConfig' => [
			'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
			'horizontalCssClasses' => [
				'label' => 'col-sm-2',
				//'offset' => 'col-sm-offset-3',
				'wrapper' => 'col-sm-10',
				'error' => '',
				'hint' => '',
			],
		],
    ]); ?>
	
    <?php
    if(!empty($modelFields)){
		foreach ($modelFields as $key => $value) {
			echo CoreFunc::generateInput($value, $model, $form);
		}
    }
    ?>

    <div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
			<?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-primary']) ?>
			<?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
		</div>
    </div>
    
    <?php ActiveForm::end(); ?>

</div>