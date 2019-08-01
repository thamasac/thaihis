<?php

use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use backend\modules\core\classes\CoreFunc;
use kartik\file\FileInput;
use yii\web\JsExpression;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;

//use kartik\widgets\Select2;
?>

<?php //$this->beginContent('@dektrium/user/views/admin/update.php', ['user' => $user]) ?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <ul class="nav nav-tabs">
        <li class="active"><a data-toggle="tab" href="#home"><i
                        class="fa fa-user"></i> <?= Yii::t('rbac-admin', 'Profile') ?></a></li>
        <li><a data-toggle="tab" href="#menu1"><?= Yii::t('rbac-admin', 'Account') ?></a></li>
        <li><a data-toggle="tab" href="#menu2"><?= Yii::t('rbac-admin', 'Status') ?></a></li>
    </ul>


    <div class="container-fluid">
        <div class="tab-content">
            <div id="home" class="tab-pane fade in active">
                <div class="">
                    <div class="panel-heading"></div>
                    <div class="panel-body">
                        <div class="col-md-12 ">
                            <div>
                                <div>

                                    <?php
                                    $form = ActiveForm::begin([
                                        'id' => 'frm-profile',
                                        'layout' => 'horizontal',
                                        //'enableAjaxValidation' => true,
                                        //'enableClientValidation' => false,
                                        //'validationUrl' => ['/user/admin/validate-ajax'],
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
                                    ])->label(Yii::t('chanpan', 'Telephone number'))
                                    ?>
                                    <?php
                                    $url = \yii\helpers\Url::to(['/user/admin/get-sitecode']); //กำหนด URL ที่จะไปโหลดข้อมูล
                                    ?>



                                    <?php
                                    if (!empty($modelFields)) {
                                        foreach ($modelFields as $key => $value) {
                                            if (empty($value["input_data"])) {
                                                if ($value['table_varname'] == 'sitecode') {
                                                    if (Yii::$app->user->can('administrator')) {
                                                        echo \common\modules\user\classes\SiteCodeFunc::getSiteCode($form, $model);
                                                    }
                                                    //
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
                                        'id' => 'my_picture',
                                    ])
                                    ?>
                                    <?php
                                    //                                      echo $form->field($model, 'auth_str')->checkBoxList($auth_str)->label(
                                    //                                       Yii::t('rbac-admin', 'Role') . '(' . Yii::t('rbac-admin', 'Can select more than one role.') . ')'
                                    //                                    );


                                    ?>
                                    <div class="row">
                                        <div class="col-md-10">
                                            <?php

                                                echo $form->field($model, 'auth_str')->widget(Select2::classname(), [
                                                    'data' => $auth_str,
                                                    'options' => ['placeholder' => Yii::t('appmenu', 'System Privilege'), 'multiple' => true],
                                                    'pluginOptions' => [
                                                        'allowClear' => true,
                                                    ],
                                                ])->label(Yii::t('chanpan', 'System Privilege'));

                                                ?>
                                        </div>
                                        <div class="col-md-2">
                                            <a tabindex="0"
                                            style='font-size:18px;'   
                                            role="button" 
                                            data-html="true" 
                                            data-toggle="popover" 
                                            data-trigger="focus"
                                            title="<b>Info System Privilege</b>" 
                                            data-content="<?= isset(\Yii::$app->params['system_privilege'])?\Yii::$app->params['system_privilege']:''?>"><i class="fa fa-info-circle"></i></a>
                                            <?php richardfan\widget\JSRegister::begin();?>
                                            <script>
                                                $(function(){
                                                    // Enables popover
                                                    $("[data-toggle=popover]").popover({ html : true,placement: "right" });
                                                });
                                            </script>
                                            <?php richardfan\widget\JSRegister::end();?>
                                        </div>
                                        <?php \appxq\sdii\widgets\CSSRegister::begin();?>
                                        <style>
                                           .popover{
                                                width:500px;
                                                height:300px;  
                                                max-width:500px;
                                            }
                                        </style>
                                        <?php \appxq\sdii\widgets\CSSRegister::end();?>
                                    </div>    
                                     
                                    <?php // echo $form->field($model, 'gravatar_email')->hint(\yii\helpers\Html::a(Yii::t('user', 'Change your avatar at Gravatar.com'), 'http://gravatar.com'))   ?>


                                    <div class="form-group">
                                        <div class="col-lg-offset-3 col-lg-9">
                                            <?= \yii\helpers\Html::submitButton(Yii::t('user', 'Save'), ['class' => 'btn btn-block btn-success btn-lg']) ?>
                                            <br>
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
               $('.field-profile-certificate').hide();
$('form#frm-profile').on('beforeSubmit', function(e){
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
                        initUser();
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
            </div>
            <div id="menu1" class="tab-pane fade">
                <div id="show-account" style="margin-top:15px;"></div>
                <?php $this->registerJs("
                $.get('" . \yii\helpers\Url::to(['/user/admin/update', 'id' => $model->user_id]) . "', function(data){
                    $('#show-account').html(data);
                });
            ") ?>
            </div>
            <div id="menu2" class="tab-pane fade">
                <div id="show-status" style="margin-top:15px;"></div>
                <?php $this->registerJs("
                $.get('" . \yii\helpers\Url::to(['/user/admin/info', 'id' => $model->user_id]) . "', function(data){
                    $('#show-status').html(data);
                });
            ") ?>
            </div>
        </div>
    </div>
</div>


<!-- Modal -->
<div id="modal-system-privilege" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Info System Privilege</h4>
            </div>
            <div class="modal-body">
               <?= isset(\Yii::$app->params['system_privilege'])?\Yii::$app->params['system_privilege']:''?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><?= Yii::t('chanpan','Close')?></button>
            </div>
        </div>

    </div>
</div>