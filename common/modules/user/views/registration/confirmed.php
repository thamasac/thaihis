<?php
/**
 * Created by PhpStorm.
 * User: kawin
 * Date: 7/12/2018
 * Time: 1:46 PM
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>
<div class="row">
    <div class="col-md-6 col-md-offset-3">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?= Html::encode("Do you want to preceed to work at {$projectName}") ?></h3>
            </div>
            <div class="panel-body">
                <p class="text-center">
                    <?=
                    Yii::t('user', "Are you want to proceed to $projectName.") ?>
                </p>
                <br>

                <?php $form = ActiveForm::begin([
                    'id' => 'connect-account-form',
                ]); ?>

                <?= Html::button(Yii::t('user', 'Yes, go ahead!'), ['id'=>'proceed-btn','class' => 'btn btn-primary btn-block']) ?>
                <?= Html::button(Yii::t('user', 'No, remain working at the nCRC portal'), ['id'=>'stay-btn','style' => ['margin-top' => '22px'], 'class' => 'btn btn-default btn-block']) ?>
                <?php ActiveForm::end(); ?>


            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs(<<<JS
$( "#proceed-btn" ).click(function() {
   window.location.href = '$redirect'; 
});
$( "#stay-btn" ).click(function() {
   window.location.href = '/'; 
});
JS
);
?>