<?php

use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use backend\modules\core\classes\CoreFunc;
use kartik\file\FileInput;
use yii\web\JsExpression;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
?>
<div class="panel panel-primary"> 
    <div class="panel-heading"><i class="fa fa-user"></i> <?= Yii::t('rbac-admin', 'Member') ?></div>
    <div class="panel-body">
        <div class="col-md-12 ">
            <div>
                <div>

                    <?php
                    $form = ActiveForm::begin([
                                'id' => 'frm-profile',
                                'layout' => 'horizontal',
                                'enableAjaxValidation' => true,
                                'enableClientValidation' => false,
                                'validationUrl' => ['/user/admin/validate-ajax'],
                                'options' => ['class' => '', 'enctype' => 'multipart/form-data'],
                                'fieldConfig' => [
                                    'template' => "{label}\n<div class=\"col-lg-9\">{input}</div>\n<div class=\"col-sm-offset-3 col-lg-9\">{error}\n{hint}</div>",
                                    'labelOptions' => ['class' => 'col-lg-3 control-label'],
                                ],
                    ]);
                    ?>

                    <?php $form->field($model, 'name') ?>

                    <?= $form->field($model, 'public_email') ?>


                    <?php // $form->field($model, 'cid')  ?>
                    <?=
                    $form->field($model, 'tel')->widget(\yii\widgets\MaskedInput::className(), [
                        'mask' => '9999999999',
                    ])
                    ?>
                    <?php
                    $url = \yii\helpers\Url::to(['/user/admin/get-sitecode']); //กำหนด URL ที่จะไปโหลดข้อมูล
                    ?>



                    <?php
                    if (!empty($modelFields)) {
                        foreach ($modelFields as $key => $value) {
                            if (empty($value["input_data"])) {
                                if ($value['table_varname'] == 'sitecode') {
                                    echo \common\modules\user\classes\SiteCodeFunc::getSiteCode($form, $model);
                                    echo common\modules\user\classes\CNDepartment::getDepartmentForm($form, $model, 'department');
                                } else {
                                    echo CoreFunc::generateInput($value, $model, $form, 'table_varname');
                                }
                            }
                        }
                    }
                    ?>



                    <?php
                    echo $form->field($model, 'picture')->widget(\trntv\filekit\widget\Upload::classname(), [
                        'url' => ['/core/file-storage/avatar-upload'],
                        'id' => 'xxx',
                    ])
                    ?>
                    <?=
                    $form->field($model, 'auth_str')->checkBoxList($auth_str)->label(
                            Yii::t('rbac-admin', 'Role') . '(' . Yii::t('rbac-admin', 'Can select more than one role.') . ')'
                    )
                    ?>  
                    <?php // echo $form->field($model, 'gravatar_email')->hint(\yii\helpers\Html::a(Yii::t('user', 'Change your avatar at Gravatar.com'), 'http://gravatar.com'))   ?>




                    <div class="form-group">
                        <div class="col-lg-offset-3 col-lg-9">
                            <?= \yii\helpers\Html::submitButton(Yii::t('user', 'Save'), ['class' => 'btn btn-block btn-success']) ?><br>
                        </div>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>

    </div>
</div>

<?php //$this->endContent() ?>
<?php $this->registerJs("
$('form#frm-profile').on('beforeSubmit', function(e){
    var \$form = $(this);
    //console.log(\$form.serialize()); 
    $.post(
		 \$form.attr('action'),  
		\$form.serialize()
    ).done(function(result){ 
                
		if(result.status == 'success'){
			" . SDNoty::show('result.message', 'result.status') . "
			$.pjax.reload({container:'#user-grid-pjax'});
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