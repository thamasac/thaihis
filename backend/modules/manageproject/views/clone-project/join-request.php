<?php
/**
 * Created by PhpStorm.
 * User: developer
 * Date: 3/7/2018 AD
 * Time: 13:11
 */

use appxq\sdii\helpers\SDHtml;
use appxq\sdii\helpers\SDNoty;

/* @var $this \yii\web\View */

?>
<div class="modal-body">
    <?php yii\bootstrap\ActiveForm::begin([
        'id' => 'frm-collaboration-approve',
        'enableAjaxValidation' => false,
        "action" => \yii\helpers\Url::to(['/manageproject/clone-project/send-join-project'])
    ]); ?>
    <div class="form-group">
        <div class="text-center" style="margin:0 auto;max-width: 720px">
            <h1 class='jumbotron-heading'> Request to join project</h1>
            <p class='lead text-muted'> This project enable to join but need approve from administrator of this project
                please fill compose for more information about you.</p>
            <b style="font-weight: 500 !important;" class='lead' ><u>The application result will be notified to you by E-mail.</u></b>
            <p class='text-muted'> After join the project please check your site and role in project.</p>
        </div>


        <div style="margin-left: 25%;margin-right: 25%">
            <?php
            echo \yii\bootstrap\Html::label("Compose");
            echo \yii\bootstrap\Html::textarea("compose", "", ["class" => "form-control"]);
            echo \yii\bootstrap\Html::hiddenInput("project_id", $project_id);
            ?>
            <div class="form-group">
                <br>
                <div style="margin-right: 25%;margin-left: 25%;">
                    <?php
                    echo \yii\bootstrap\Html::submitButton("Submit", ["class" => "btn btn-primary btn-block"]);
                    ?>
                </div>
            </div>
        </div>

        <?php yii\bootstrap\ActiveForm::end(); ?>
    </div>

    <?php
    $alertResponse = SDNoty::show('result.message', 'result.status');
    $this->registerJs(<<<JS
$('form#frm-collaboration-approve').on('beforeSubmit', function(e){
    let form = $(this);
    $.post(
		 form.attr('action'),
		 form.serialize()
    ).done(function(result){
		if(result.status == 'success'){
			$alertResponse
            $('#modal-ezform-main').modal('toggle');
            $('#modal-ezform-main').modal('hide');
		} else{
			$alertResponse
		}
    }).fail(function(){
		$alertResponse
		console.log('server error');
    });
    return false;
});
JS
    );
    ?>
