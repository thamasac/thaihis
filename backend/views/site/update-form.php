<?php 
    $this->title = Yii::t('chanpan', 'Update Project');
    
?>
<div class="row">
    <div class="col-md-6 col-md-offset-3">
        <div class="panel panel-primary">
            <div class="panel-heading"><?= yii\helpers\Html::encode($this->title)?></div>
            <div class="panel-body">
                <div id="showLoader"></div>                
            </div>
            <div class="panel-footer">
                <div class="text-right">
                    <?= yii\helpers\Html::button(Yii::t('chanpan', 'Update Project'). '<i style="display:none" id="loadings" class="fa fa-refresh fa-spin fa-fw"></i>', ['class'=>'btn btn-primary', 'id'=>'btnUpdate'])?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
    $this->registerJs("
        $('#btnUpdate').click(function(){
            $('#loadings').show();
            $('#btnUpdate').attr('disabled', true);
            let url = '".\yii\helpers\Url::to(['/site/update-form'])."';
            $('#showLoader').html('<div class=\"sdloader \"><i class=\"fa fa-refresh fa-spin fa-3x fa-fw\"></i></div>');
            $.post(url,{status:1}, function(data){console.log(data);
                setTimeout(function(){
                    $('#showLoader').html('<div class=\'alert alert-success\'><i class=\'fa fa-check-circle\'></i> Update Success</div>');                    
                    ".appxq\sdii\helpers\SDNoty::show('data.message','data.status').";
                    $('#loadings').hide(); 
                    $('#btnUpdate').attr('disabled', false);
                },2000);
            });
            return false;
        });
    ");
?>