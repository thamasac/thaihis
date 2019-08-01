<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$options = isset($model->options) ? \appxq\sdii\utils\SDUtility::string2Array($model->options) : [];

$target = backend\modules\ezforms2\classes\EzfQuery::getTargetOne($ezf_id);
$id = appxq\sdii\utils\SDUtility::getMillisecTime();
?>


<!--config start-->

<?php
//    \cpn\chanpan\widgets\BootstrapTourWidget::widget([
//         'data'=>[
//             [
//                'element'=>'#ezmodulewidget-widget_name',
//                'title'=>'ปุ่มสำหรับสร้างโครงการ',
//                'content'=>'ปุ่มสำหรับสร้างโครงการ',
//                'placement'=> 'auto',
//                'smartPlacement'=> true, 
//            ],
////            [
////                'element'=>'#btnManageProject',
////                'title'=>'จัดการโครงการของคุณ',
////                'content'=>'รายละเอียด จัดการโครงการของคุณ',
////                'placement'=> 'auto',
////                'smartPlacement'=> true, 
////            ],
////            [
////                'element'=>'.btnEditShotModule',
////                'title'=>'Title of my step',
////                'content'=>'Content of my step Content of my step Content of my step',
////                'placement'=> 'auto',
////                'smartPlacement'=> true, 
////            ] 
//         ] 
//    ]);
       
    $options['widget_id'] = isset($model->widget_id) ? $model->widget_id : \appxq\sdii\utils\SDUtility::getMillisecTime();
    //print_r($options)
//\appxq\sdii\utils\VarDumper::dump($model->widget_id)
;?>

<!--config end-->
<div class="row">
    
    
    <div class="col-md-12">
        <div class="ez-optional">
            <h3><?= Yii::t('eztour', 'Optional')?></h3>
            <div class="clearfix"></div><hr/>
            <div class="row">
                <div class="col-md-12">
                    <div class="col-md-3">
                        <label><?= Yii::t('eztour', 'Auto Start') ?></label>
                        <?php
                            echo Html::radioList('options[auto_start]', isset($options['auto_start']) ? $options['auto_start'] : 2, ['1' => 'Yes', '2' => 'No']);
                        ?>
                    </div>
                    <div class='col-md-3'>
                        <label>Button name</label>
                        <?php
                        echo Html::textInput('options[btn_name]', isset($options['btn_name']) ? $options['btn_name'] : '', ['class' => 'form-control']);
                        ?>
                    </div>
                    <div class="col-md-5">
                        <label>Button type</label>
                        <?php
                        echo Html::radioList('options[btn_type]', isset($options['btn_type']) ? $options['btn_type'] : '', [
                            'primary' => 'Blue',
                            'success' => 'Green',
                            'info' => 'Sky blue',
                            'warning' => 'Orange',
                            'danger' => 'Red'
                        ]);
                        ?>
                    </div>
                    <div class="clearfix"></div><hr/>
                    <div class="col-md-4">
                        <label>Position</label>
                        <?php
                        echo Html::radioList('options[position_type]', isset($options['position_type']) ? $options['position_type'] : '', [
                            'pull-left' => 'Left',
                            'pull-right' => 'Right',
                            '' => 'Customized',
                        ]);
                        ?>
                    </div>
                    
                    <div class="col-md-4">
                        <label>Button size</label>
                        <?php
                        echo Html::radioList('options[position_size]', isset($options['position_size']) ? $options['position_size'] : 'btn-md', [
                            'btn-lg' => 'Large',
                            'btn-md' => 'Medium',
                            'btn-sm' => 'Small',
                            'btn-xs' => 'XSmall',
                        ]);
                        ?>
                    </div>
                    <div class="col-md-3">
                        <label>Block Level Buttons</label>
                        <?php
                        echo Html::radioList('options[btn_block]', isset($options['btn_block']) ? $options['btn_block'] : '', [
                            'btn-block' => 'TRUE',
                            '' => 'FALSE',
                        ]);
                        ?>
                    </div>
                    <div class="clearfix"></div><hr/>
                    <div class="col-md-3">
                        <label><?= Yii::t('chanpan', 'Icon') ?></label>
                        <?=
                        dominus77\iconpicker\IconPicker::widget([
                            'name' => "options[icon]",
                            'value' => isset($options['icon']) ? $options['icon'] : 'fa-check',
                            'options' => ['class' => 'sicon-input form-control', 'id' => "iconpicker_" . $id],
                            'clientOptions' => [
                                'hideOnSelect' => true,
                            ]
                        ])
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-12">
       <span class="pull-right"> 
           <?= Html::button('<i class="fa fa-plus"></i> '.Yii::t('tour','ADD'), ['class'=>'btn btn-sm btn-success btnAdd'])?>
       </span>
       <div class="clearfix"></div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="col-md-12">
                <h3><?= Yii::t('eztour', 'Config fields')?></h3>
                <hr/>
            </div>
            <?php $rand = \appxq\sdii\utils\SDUtility::getMillisecTime();?>
            <div id="clone-<?= $rand?>"></div>
        </div>
    </div>
    
    
    <div class="clearfix"></div>
    <div class="col-md-12">
       <span class="pull-right"> 
           <?= Html::button('<i class="fa fa-plus"></i> '.Yii::t('tour','ADD'), ['class'=>'btn btn-sm btn-success btnAdd'])?>
       </span>
       <div class="clearfix"></div>
    </div>
</div>

<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>
<script>
    // JS script
    $('.btnAdd').on('click' , function(){
       cloneForm();
       return false;
    });
    function cloneForm(){
        let url = '/eztour/clone';
        $.post(url ,{widget_id:'<?= $options['widget_id']?>'}, function(result){
            if(result.status == 'success') {
                <?= \appxq\sdii\helpers\SDNoty::show('result.message', 'result.status')?>
                getForm();
            } else {
                <?= \appxq\sdii\helpers\SDNoty::show('result.message', 'result.status')?>
            } 
           
        });
        return false;
    }
    function getForm(){
        let url = '/eztour/get-form';
        $.post(url ,{widget_id:'<?= $options['widget_id']?>'}, function(data){
            $('#clone-<?= $rand?>').html(data);
        });
        return false;
    }
    getForm();

</script>
<?php \richardfan\widget\JSRegister::end(); ?>

<?php \appxq\sdii\widgets\CSSRegister::begin();?>
<style>
    .btnAdd{
        margin-bottom:10px;
    }
    .ez-optional{
        border: 2px solid #2f79b9;
        padding: 5px;
        margin-bottom: 10px;
        border-radius: 5px;
        
    }
</style>
<?php \appxq\sdii\widgets\CSSRegister::end();?>