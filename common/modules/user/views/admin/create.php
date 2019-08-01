<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use dektrium\user\models\User;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Nav;
use yii\helpers\Html;
use yii\web\View;
use appxq\sdii\helpers\SDHtml;
use appxq\sdii\helpers\SDNoty;
/**
 * @var View $this
 * @var User $user
 */

$this->title = Yii::t('user', 'Create a user account');
$this->params['breadcrumbs'][] = ['label' => Yii::t('user', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
 

<div class="row">
    <div class="col-md-12">
        <div class="">
            <div class="">
             
                <?php $form = ActiveForm::begin([
                    'enableAjaxValidation' => true,
                    'action' => yii\helpers\Url::to(['/user/admin/create']),
                    'validationUrl' => yii\helpers\Url::to(['/user/admin/validation-form']),
                    'layout' => 'horizontal',
                    'id'=>$profile->formName(),

                ]); ?>
                <div class=""> 
                    <div class="modal-header"><i class="fa fa-user"></i> <?= Yii::t('user','Users')?><button type="button" class="close" data-dismiss="modal">&times;</button></div>
                    <div class="modal-body">
                        <?= $this->render('_user', ['form' => $form, 'user' => $user,'profile'=>$profile,'auth_str'=>$auth_str]) ?>
                        <div class="form-group">
                            <div class="col-lg-offset-3 col-lg-6">
                                <?= Html::submitButton(Yii::t('user', 'Save'), ['class' => 'btn btn-block btn-primary']) ?>
                            </div>
                        </div>
                    </div>
                </div>
                

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>

</div>

<?php  $this->registerJs("
$('form#{$profile->formName()}').on('beforeSubmit', function(e){
    var \$form = $(this);
    let url = '".yii\helpers\Url::to(['/user/admin/create'])."';
    $.post(
		url,
		\$form.serialize()
    ).done(function(result){
                 
		if(result.status == 'success'){
			". SDNoty::show('result.message', 'result.status') .";
			initUser();
                        $('#modal-user').modal('hide');
		} else{
			". SDNoty::show('result.message', 'result.status') .";
                        initUser();
                        $('#modal-user').modal('hide');
		} 
                
               
    }).fail(function(){
		". SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') ."
		console.log('server error');
    });
    return false;
});

");?>