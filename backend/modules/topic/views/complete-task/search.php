<?php 
    use yii\bootstrap\ActiveForm;
    use yii\helpers\Html;
    use appxq\sdii\helpers\SDNoty;
    use appxq\sdii\helpers\SDHtml;
?>
<?php ActiveForm::begin([
            'options' => ['enctype' => 'multipart/form-data'],
                //'layout' => 'horizontal',
                'id' => "form-{$widget_id}",
                'action'=>'/topic/complete-task'        
                    
            ])
?>
<div class="col-md-12">
    <div class="panel panel-default">
        <div class="panel-body">
            <h4><?= Yii::t('topic', 'Search')?></h4>
            <input type="hidden" name="id" value="<?= $widget_id?>"/>
            <div class="col-md-6">
                <?php
                echo Html::textInput('txtsearch', '', ['class' => 'form-control', 'placeholder' => Yii::t('topic', 'Search by task')]);
                ?>
            </div>
            <div class="col-md-4">
                <?php
                $items = [''=>'Select status task','1' => 'done', '2' => 'not Done'];
                echo Html::dropDownList('dropsearch', '0', $items, ['class' => 'form-control']);
                ?>
            </div>
            <div class="col-md-2">
                <?php
                echo Html::submitButton('<i class="fa fa-search"></i>', ['class' => 'btn btn-primary btn-block']);
                ?>
            </div>
        </div>
    </div>
</div>
        
<?php ActiveForm::end() ?>


<?php richardfan\widget\JSRegister::begin();?>
<script>
   $('#form-<?= $widget_id?>').on('beforeSubmit', function(e) {
        var $form = $(this);
        $.post(
            $form.attr('action'), //serialize Yii2 form
            $form.serialize()
        ).done(function(data) {
             $('#<?= $widget_id?>').html(data);
        }).fail(function() {
            
        });
        return false;
    });
</script>
<?php richardfan\widget\JSRegister::end();?>