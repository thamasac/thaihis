<?php
    use yii\helpers\Html;
    $this->title = Yii::t('chanpan', 'Recover password template');
?> 

<div class="col-md-12"><br>
    <h4><i class="fa fa-refresh" aria-hidden="true"></i> <?= Html::encode($this->title);?></h4><hr />
  <?=
        \vova07\imperavi\Widget::widget([
            'id' => 'recover',
            'name' => 'recover',
            'value' => $detail,
            'settings' => [
                'minHeight' => 200,
                'imageManagerJson' => '../../ezforms2/text-editor/images-get',
                'fileManagerJson' => '../../ezforms2/text-editor/files-get',
                'imageUpload' => '../../ezforms2/text-editor/image-upload',
                'fileUpload' => '../../ezforms2/text-editor/file-upload',
                'plugins' => [
                    'fontcolor',
                    'fontfamily',
                    'fontsize',
                    'textdirection',
                    'textexpander',
                    'counter',
                    'table',
                    'definedlinks',
                    'video',
                    'imagemanager',
                    'filemanager',
                    'limiter',
                    'fullscreen',
                ],
                'paragraphize' => false,
                'replaceDivs' => false,
            ],
        ])
        ?>
    <div class="pull-right">
     <?= Html::button(Yii::t('chanpan','Save'), ['class'=>'btn btn-primary' ,'id'=>'btnSave3'])?>
    </div>    
</div>
<?php 
    $this->registerJs("
        $('#btnSave3').click(function(){
            let detail = $('#recover').val();
            let url = '".yii\helpers\Url::to(['/site/recover-password-templates'])."';
            $.post(url,{detail:detail},function(data){
                console.log(data);
                if(data.status == 'success'){
                    ".appxq\sdii\helpers\SDNoty::show('data.message', 'data.status').";
                }
            });
            return false;
        });
    ");
?>