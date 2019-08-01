<?php
    use kartik\select2\Select2;
?>
<?php if(!empty($profile)): ?>
 
<?php endif; ?>     
<?php //$form->field($user, 'username')->textInput(['maxlength' => 25]) ?>
<?php //$form->field($user, 'email')->textInput(['maxlength' => 255]) ?>
<div class="form-group field-user-username required">
    <label class="control-label col-sm-3" for="user-username">Username</label>
    <div class="col-sm-9">
        <label style="margin-top:5px;"><?= isset($user->username)?$user->username:''; ?></label>
        <p class="help-block help-block-error "></p>
    </div>
</div>
<div class="form-group field-user-email required">
    <label class="control-label col-sm-3" for="user-email">Email</label>
    <div class="col-sm-9">
        <label style="margin-top:5px;"><?= isset($user->email)?$user->email:''; ?></label>
        <p class="help-block help-block-error "></p>
    </div>
</div>
 
<?= $form->field($user, 'password')->passwordInput() ?>

<?php if(!empty($profile)): ?>
<?= $form->field($profile, 'firstname')->textInput()->label(Yii::t('chanpan','Firstname')) ?>
<?= $form->field($profile, 'lastname')->textInput()->label(Yii::t('chanpan','Lastname')) ?>
<?= $form->field($profile, 'tel')->widget(\yii\widgets\MaskedInput::className(), [
    'mask' => '9999999999',
]) ?>
 
    <?php
    echo $form->field($profile, 'auth_str')->widget(Select2::classname(), [
        'data' => $auth_str,
        'options' => ['id'=>'auth_str','placeholder' => Yii::t('chanpan', 'Select role'), 'multiple' => true],
        'pluginOptions' => [
            'allowClear' => true,
        ],
    ])->label(Yii::t('rbac-admin', 'Role'));
    ?>  
<?php // \common\modules\user\classes\SiteCodeFunc::getSiteCode($form, $profile) ?>
<?php // \common\modules\user\classes\CNDepartment::getDepartmentForm($form, $profile, 'department');?>
<?php endif; ?>    

 