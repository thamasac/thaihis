<?php
use yii\helpers\Url;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 
 * จำเป็นต้องมี options[render] และถ้ามีการส่งค่า options[params]
 */
$options = isset($model->options)?\appxq\sdii\utils\SDUtility::string2Array($model->options):[];
?>
<?=\yii\helpers\Html::hiddenInput('options[render]', '/ezmodule-tab/html/widget');?>

<div class="form-group row">
    <div class="col-md-12">
        <?= \yii\helpers\Html::label(Yii::t('ezmodule', 'Content'), 'options[params][content]', ['class' => 'control-label']) ?>
        <?php 
        $value = isset($options['params']['content'])?$options['params']['content']:'';
        echo appxq\sdii\widgets\FroalaEditorWidget::widget([
            'id'=>'config-'.\appxq\sdii\utils\SDUtility::getMillisecTime(),
            'name' => 'options[params][content]',
            'value' => $value,
        ]);
//        echo vova07\imperavi\Widget::widget([
//            'name' => 'options[params][content]',
//            'value' => $value,
//            'settings' => [
//                'minHeight' => 30,
//                'imageManagerJson' => Url::to(['/ezforms2/text-editor/images-get']),
//                'fileManagerJson' => Url::to(['/ezforms2/text-editor/files-get']),
//                'imageUpload' => Url::to(['/ezforms2/text-editor/image-upload']),
//                'fileUpload' => Url::to(['/ezforms2/text-editor/file-upload']),
//                'plugins' => [
//                    'fontcolor',
//                    'fontfamily',
//                    'fontsize',
//                    'textdirection',
//                    'textexpander',
//                    'counter',
//                    'table',
//                    'definedlinks',
//                    'video',
//                    'imagemanager',
//                    'filemanager',
//                    'limiter',
//                    'fullscreen',
//                ],
//                'paragraphize'=>false,
//                'replaceDivs'=>false,
//            ]
//        ]);
       
        ?>
    </div>
</div>