<?php

/* 
 * This file is part of the Dektrium project
 * 
 * (c) Dektrium project <http://github.com/dektrium>
 * 
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
/**
 * @var yii\web\View $this
 * @var dektrium\user\models\User $user
 */

?>

<?php //$this->beginContent('@dektrium/user/views/admin/update.php', ['user' => $user]) ?>

    <?php $form = ActiveForm::begin([
        'id' => 'frm-account',
        'layout' => 'horizontal',
        //'enableAjaxValidation'   => true,
        //'enableClientValidation' => false,
        //'validationUrl' => ['/user/admin/update-validate', 'id'=>$user->id],
        'fieldConfig' => [
            'horizontalCssClasses' => [
                'wrapper' => 'col-sm-9',
            ]
        ],
    ]); ?>

    <?= $this->render('_user', ['form' => $form, 'user' => $user]) ?>

    <div class="form-group">
        <div class="col-lg-offset-3 col-lg-9">
            <?= Html::submitButton(Yii::t('user', 'Update'), ['class' => 'btn btn-block btn-success']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

<?php //$this->endContent() ?>
<?php $this->registerJs("
$('form#frm-account').on('beforeSubmit', function(e){
    var \$form = $(this);
    //console.log(\$form.serialize()); 
    $.post(
		 \$form.attr('action'),  
		\$form.serialize()
    ).done(function(result){ 
                
		if(result.status == 'success'){
			" . SDNoty::show('result.message', 'result.status') . "
			//$.pjax.reload({container:'#user-grid-pjax'});
                        $('#modal-user').modal('toggle');
                        $('#modal-user').modal('hide');
		} else{
			" . SDNoty::show('result.message', 'result.status') . "
		} 
    }).fail(function(){
		" . SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') . "
		console.log('server error');
    });
    return false;
});

"); ?> 