<?php

use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use yii\bootstrap\Html;

?>

<?php //$this->beginContent('@dektrium/user/views/admin/update.php', ['user' => $user]) ?>
    <div class="modal-header">
        Site Request
        <button type="button" class="close" data-dismiss="modal">&times;</button>
    </div>

    <div class="modal-body">
<!--        <div class="panel-heading">-->
<!--        </div>-->
        <div class="panel-body">
            <div class="col-md-12 ">
                <?php
                $isOnlySiteAdmin = !Yii::$app->user->can('administrator') && Yii::$app->user->can('adminsite');
                $optionIsOnlySiteAdmin = ["inputOptions" => ["readonly" => true]];
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


                <?php
                $compose = Yii::$app->db->createCommand("SELECT var_text FROM zdata_site_request WHERE id = :request_id", [":request_id" => $request_id])
                    ->queryScalar();
                echo Html::label("Compose");
                echo Html::textarea('compose', $compose, ["class" => "form-control", "disabled" => true]);
                echo "<hr>";
                echo Select2::widget([
                    'name' => 'permission',
                    'data' => [1 => "Allowed", 3 => "Denied"],
                    'pluginEvents' => [
                        "change" => 'function(jq) {
                                                if(jq.target.value > 0) {
                                                    $(\':input[type="submit"]\').prop(\'disabled\', false);
                                                }else{
                                                      $(\':input[type="submit"]\').prop(\'disabled\', true);
                                                }

                                            }'
                    ],
                    'options' => ['placeholder' => 'Permission', 'id' => "approve-select2"],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'multiple' => false
                    ]
                ]);
                ?>
                <br>
                <div class="form-group">
                    <div class="col-lg-offset-9 col-lg-3">
                        <?= \yii\helpers\Html::submitButton(Yii::t('user', 'Save'), ['class' => 'btn btn-block btn-success', 'disabled' => true]) ?>
                        <br>
                    </div>
                </div>

                <?php ActiveForm::end(); ?>
            </div>

        </div>
    </div>


<?php
$this->registerJs("
$('form#frm-profile').on('beforeSubmit', function(e){
    var \$form = $(this);
    $.post(
		 \$form.attr('action'),  
		 \$form.serialize()
    ).done(function(result){ 
		if(result.status == 'success'){
			" . SDNoty::show('result.message', 'result.status') . "
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
");
?>