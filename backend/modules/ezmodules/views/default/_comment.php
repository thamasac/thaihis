<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;

/* @var $this yii\web\View */
/* @var $model backend\modules\ezmodules\models\EzmoduleComment */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="ezmodule-comment-form">

    <?php
    $form = ActiveForm::begin([
                'id' => $model->formName(),
    ]);
    ?>


    <?= $form->field($model, 'message')->widget(vova07\imperavi\Widget::className(), [
        'settings' => ['plugins' => [
                'fontcolor',
                'fontsize',
            ]]
        ]) ?>

    <?= $form->field($model, 'ezm_id')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'user_id')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'vote')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'created_by')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'created_at')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'updated_by')->hiddenInput()->label(false) ?>

<?= $form->field($model, 'updated_at')->hiddenInput()->label(false) ?>

    <div class="row">
        <div class="col-md-1 text-right">
<?= Yii::t('ezmodule', 'Like') ?>
        </div>
        <div class="col-md-5 sdbox-col">
<?= Html::dropDownList('rating_star', $star, [0, 1, 2, 3, 4, 5], ['id' => 'rating_star']) ?>
        </div>
        <div class="col-md-6 sdbox-col">
            <div class="text-right">
<?= Html::submitButton('<i class="glyphicon glyphicon-comment"></i> ' . Yii::t('ezmodule', 'Post'), ['class' => 'btn btn-success btn-sm']) ?>
            </div>
        </div>
    </div>




<?php ActiveForm::end(); ?>

</div>

<?php $this->registerJs("
$('#rating_star').barrating({
    theme: 'bootstrap-stars',
    readonly:false,
     allowEmpty:true, 
     emptyValue:0,
});

$('#rating_star').change(function(){
    saveRating(parseInt($(this).val()));
});

$('form#{$model->formName()}').on('beforeSubmit', function(e) {
    var \$form = $(this);
    $.post(
        \$form.attr('action'), //serialize Yii2 form
        \$form.serialize()
    ).done(function(result) {
    if(result.status == 'success') {
        " . SDNoty::show('result.message', 'result.status') . "
        getComment();
        getCommentList();
    } else {
        " . SDNoty::show('result.message', 'result.status') . "
    } 
    }).fail(function() {
        " . SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') . "
        console.log('server error');
    });
    return false;
});

function saveRating(star) {
    $.post(
        '" . yii\helpers\Url::to(['/ezmodules/default/rating', 'ezm_id' => $model['ezm_id']]) . "',
       {star:star}
    ).done(function(result) {
    if(result.status == 'success') {
        " . SDNoty::show('result.message', 'result.status') . "
    } else {
        " . SDNoty::show('result.message', 'result.status') . "
    } 
    }).fail(function() {
        " . SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') . "
        console.log('server error');
    });
}
        
function getComment() {
            $.ajax({
                method: 'GET',
                url: '" . yii\helpers\Url::to(['/ezmodules/default/comment', 'ezm_id' => $model['ezm_id']]) . "',
                dataType: 'HTML',
                success: function(result, textStatus) {
                    $('#comment-box').html(result);
                }
            });
        }
        
function getCommentList() {
            $.ajax({
                method: 'GET',
                url: '" . yii\helpers\Url::to(['/ezmodules/default/comment-list', 'ezm_id' => $model['ezm_id']]) . "',
                dataType: 'HTML',
                success: function(result, textStatus) {
                    $('#comment-list').html(result);
                }
            });
        }
"); ?>