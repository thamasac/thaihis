 <?php 
    use appxq\sdii\helpers\SDNoty;
    use appxq\sdii\helpers\SDHtml;
    use yii\bootstrap\ActiveForm;
    
    $form = ActiveForm::begin([
        'options' => ['enctype' => 'multipart/form-data'],
        'id'=>'ez-setup-form',
    ]);
 ?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title"><?= Yii::t('topic','Update EzSetup')?></h4>
</div>
<div class="modal-body">    
    <?= $form->field($model, 'steps')->textarea(['rows'=>'4']) ?>
    <?= $form->field($model, 'link') ?>
</div>
<div class="modal-footer">
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <?= yii\helpers\Html::submitButton(Yii::t('user','Submit'), ['class'=>'btn btn-lg btn-primary btn-block'])?>
        </div>
    </div>
</div>

<?php ActiveForm::end();?>

<?php richardfan\widget\JSRegister::begin(); ?>
<script>
    $('form#ez-setup-form').on('beforeSubmit', function(e) {
        var $form = $(this);
        $.post(
            $form.attr('action'), //serialize Yii2 form
            $form.serialize()
        ).done(function(result) {
            if(result.status == 'success') {
                <?= SDNoty::show('result.message', 'result.status')?>
                $(document).find('#modal-ez-setup').modal('hide');
                setTimeout(function(){
                    
                    loadEzSetup();
                },1500);
            } else {
                <?= SDNoty::show('result.message', 'result.status')?>
            } 
        }).fail(function() {
            <?= SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"')?>
            console.log('server error');
        });
        return false;
    });
    function loadEzSetup(){
        let url = '/topic/ez-setup/get-ez-setup';
        $.get(url,{id:'<?= $widget_id?>'}, function(data){
            $('#<?= $widget_id?>').html(data);
             $('#radio-45').hide();
        });
    }
</script>
<?php richardfan\widget\JSRegister::end(); ?> 