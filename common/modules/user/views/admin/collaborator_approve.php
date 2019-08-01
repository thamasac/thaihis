<?php

use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use yii\bootstrap\Html;

?>

<?php //$this->beginContent('@dektrium/user/views/admin/update.php', ['user' => $user]) ?>
    <div class="modal-header">
        Project Collaboration Request
        <button type="button" class="close" data-dismiss="modal">&times;</button>
    </div>

    <div class="modal-body">
        <!--        <div class="panel-heading">-->
        <!--        </div>-->
        <div class="panel-body">
            <div class="col-md-12 ">
                <?php
                //                $isOnlySiteAdmin = !Yii::$app->user->can('administrator') && Yii::$app->user->can('adminsite');
                //                $optionIsOnlySiteAdmin = ["inputOptions" => ["readonly" => true]];
                $form = ActiveForm::begin([
                    'id' => 'frm-collaboration-approve',
                    'layout' => 'horizontal',
                    'enableAjaxValidation' => false,
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

                $data = Yii::$app->db->createCommand("SELECT firstname,lastname,var_text FROM zdata_project_join_request WHERE id = :id", [":id" => $id])
                    ->queryOne();


                echo Html::hiddenInput("id", $id);
                echo Html::label("Name");
                echo Html::textarea('name', $data["firstname"] . " " . $data["lastname"], ["class" => "form-control", "disabled" => true]);
                echo "<br>";
                echo Html::label("Compose");
                echo Html::textarea('compose', $data["var_text"], ["class" => "form-control", "disabled" => true]);
                echo "<hr>";

                echo Html::radioList("method", null, ["Accept", "Denied"], ["id" => 'accept_radio_list']);


                ?>
                <br>

                <div class="row" id="deploy_list" style="display: none;">
                    <h2> Deploy to </h2>
                    <div class="col-md-6">
                        <?php
                        $model = new \backend\modules\manage_user\models\CNUserForm();
                        echo \common\modules\user\classes\SiteCodeFunc::getSiteCode($form, $model);
                        ?>
                    </div>
                </div>

                <div class="row" id="denied_box" style="display: none;">
                    <h2> Cause </h2>
                    <?php
                    echo Html::label("Cause");
                    echo Html::textarea('cause', "Not determined cause", ["class" => "form-control"]);
                    echo "<br>";
                    ?>
                </div>

                <div class="form-group">
                    <div class="col-md-offset-6 col-md-6">
                        <div class="col-md-12">
                            <?= \yii\helpers\Html::submitButton(Yii::t('user', 'Submit'), ['class' => 'btn btn-block btn-success']) ?>
                        </div>
                        <br>
                    </div>
                </div>

                <?php ActiveForm::end(); ?>
            </div>

        </div>
    </div>


<?php
$this->registerJs(<<<JS
    $("#accept_radio_list").change( function() {
      console.log();    
      if($("input[name=method][value=0]").prop("checked")){
          $("#deploy_list").show();
                    $("#denied_box").hide();

      }else{
                    $("#deploy_list").hide();
                              $("#denied_box").show();
      }
    })  ;
JS
);

$this->registerJs("
$('form#frm-collaboration-approve').on('beforeSubmit', function(e){
    var \$form = $(this);
    $.post(
		 \$form.attr('action'),
		 \$form.serialize()
    ).done(function(result){
    result = JSON.parse(result);
    console.log(result);
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