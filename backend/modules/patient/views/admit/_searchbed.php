<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\widgets\Select2;
use appxq\sdii\helpers\SDHtml;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model backend\models\UserSearch */
/* @var $form yii\widgets\ActiveForm */
?> 
<div class="user-search col-md-12">
    <?php
    $form = ActiveForm::begin([
                'id' => $model->formName(),
                'action' => ['/patient/admit/ward-bed', 'ezf_id' => $ezf_id, 'dept' => $dept, 'reloadDiv' => $reloadDiv],
                'method' => 'post',
                'options' => ['style' => 'margin-bottom: 15px;'],
                'enableClientValidation' => false,
    ]);
    ?>
    <div class="input-group">
        <div class="input-group-addon">
            <span><i class="fa fa-user"></i> ค้นหาผู้ป่วย </span>
        </div>
        <?= Html::activeInput('text', $model, 'bed_name', ['id' => 'search_bed', 'class' => 'form-control', 'placeholder' => Yii::t('patient', 'Find the form name or hn.')]); ?>

        <div class="input-group-btn">
            <?= Html::button(SDHtml::getBtnSearch(), ['class' => 'btn btn-default', 'type' => 'submit']); ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<?php
$this->registerJs(" 
$('#search_bed').select();

$('form#{$model->formName()}').on('change', function(e) {
    var \$form = $(this);
    $.post(
	\$form.attr('action'), //serialize Yii2 form
	\$form.serialize()
    ).done(function(result) {
	$('#$reloadDiv').html(result);
    }).fail(function() {
	console.log('server error');
    });
    //return false;
});

$('form#{$model->formName()}').on('beforeSubmit', function(e) {
    var \$form = $(this);
    $.post(
	\$form.attr('action'), //serialize Yii2 form
	\$form.serialize()
    ).done(function(result) {
	$('#$reloadDiv').html(result);
    }).fail(function() {
	console.log('server error');
    });
    return false;
});
");
?>
