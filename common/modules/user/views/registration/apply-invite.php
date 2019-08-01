<?php
/**
 * Created by PhpStorm.
 * User: kawin
 * Date: 7/11/2018
 * Time: 11:08 AM
 */
/** @var string $projectName */
/** @var \common\modules\user\classes\InvitationInfo $inviteInfo */

/** @var \yii\web\View $this */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="row">
    <div class="col-md-6 col-md-offset-3">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?= Html::encode("Invitation Confirmation") ?></h3>
            </div>
            <div class="panel-body">
                <div class="alert alert-info">
                    <p class="text-center">
                        <?=
                        Yii::t('user', 'In order to finish your invitation, Please confirm to accept invite for this information below ') ?>
                        .
                    </p>
                    <br>
                    <div class="text-center">
                        <span class="badge badge-pill badge-primary">Project Title</span>
                        <h2 style="margin-top: 2px;"> <?= $projectName ?> </h2>
                        <hr>
                        <span class="badge badge-pill badge-primary">e-Mail Address</span>
                        <h2 style="margin-top: 2px;"><?= Yii::$app->user->identity->profile->public_email ?></h2>
                    </div>

                </div>
                <?php $form = ActiveForm::begin([
                    'id' => 'connect-account-form',
                ]); ?>


                <?= Html::submitButton(Yii::t('user', 'Yes, Please proceed.'), ['class' => 'btn btn-success btn-block']) ?>

                <?= Html::button(Yii::t('user', 'No, I will login using other nCRC account of me.'), ['style' => ['margin-top' => '22px'], 'class' => 'btn btn-default btn-block', 'id' => "logout-from-apply"]) ?>
                <?= Html::submitButton(Yii::t('user', 'No, I reject this invitation. '), ['class' => 'btn btn-danger btn-block', 'name' => "reject-invite"]) ?>
                <?php ActiveForm::end(); ?>


            </div>
        </div>
    </div>
</div>

<?php
$url = "/user/security/logout?redirect=login&email=" . $inviteInfo->email . "&token=" . $inviteInfo->token . "&project_id=" . $inviteInfo->project_id;
$urlReject = "/user/registration/apply-invitation?email=" . $inviteInfo->email . "&token=" . $inviteInfo->token . "&project_id=" . $inviteInfo->project_id;
$this->registerJs(<<<JS
$( "#logout-from-apply" ).click(function() {
 $.post( "$url", function( data ) {
        $( ".result" ).html( data );
});
});
JS
);
?>

