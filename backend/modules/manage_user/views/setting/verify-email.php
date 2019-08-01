<?php
    use yii\helpers\Html;
    $this->title = Yii::t('chanpan', 'Verify email template');
?>
<?= $this->render('_menu')?>

<div class="col-md-12"><br>
    <h4><i class="fa fa-envelope-o" aria-hidden="true"></i> <?= Html::encode($this->title);?></h4><hr />
  <?=
        \vova07\imperavi\Widget::widget([
            'id' => 'verify-email',
            'name' => 'verify-email',
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
     <?= Html::button(Yii::t('chanpan','Save'), ['class'=>'btn btn-primary' ,'id'=>'btnSave'])?>
    </div>    
</div>
<?php 
    $this->registerJs("
        $('#btnSave').click(function(){
            let detail = $('#verify-email').val();
            let url = '".yii\helpers\Url::to(['/manage_user/setting/verify-email'])."';
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