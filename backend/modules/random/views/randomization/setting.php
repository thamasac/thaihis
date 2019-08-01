<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//$options = isset($options)?\appxq\sdii\utils\SDUtility::string2Array($options):[];

//$target = backend\modules\ezforms2\classes\EzfQuery::getTargetOne($ezf_id);
$reloadDiv = isset($options['reloadDiv']) ? $options['reloadDiv'] : 'random-' . \appxq\sdii\utils\SDUtility::getMillisecTime();
?>

<!--config start-->
<div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
    <form id="frmRandomization">
        <div class="panel <?=isset($color) && $color != '' ? $color : 'panel-primary'?>">
            <div class="panel-heading"><?= Yii::t('chanpan', 'Random List') ?></div>
            <div class="panel-body">
                <div class="form-group row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= Html::label(Yii::t('chanpans', 'Seed'), 'options[title]', ['class' => 'control-label']) ?>

                            <?= Html::input('number', 'options[seed]', isset($options['seed']) ? $options['seed'] : '', ['class' => 'form-control']) ?>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= Html::label(Yii::t('chanpans', 'Treatment groups') . " " . Html::tag('code', 'Group A, Group B'), 'options[title]', ['class' => 'control-label']) ?>
                            <?= Html::textInput('options[treatment]', isset($options['treatment']) ? $options['treatment'] : '', ['class' => 'form-control']) ?>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= Html::label(Yii::t('chanpans', 'Block sizes') . " " . Html::tag('code', '4, 6, 8'), 'options[title]', ['class' => 'control-label']) ?>
                            <?= Html::textInput('options[block_size]', isset($options['block_size']) ? $options['block_size'] : '', ['class' => 'form-control']) ?>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= Html::label(Yii::t('chanpans', 'List length'), 'options[title]', ['class' => 'control-label']) ?>
                            <?= Html::input('number', 'options[list_length]', isset($options['list_length']) ? $options['list_length'] : '', ['class' => 'form-control']) ?>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group pull-right">
                            <?= Html::button(Yii::t('chanpans', 'Generator'), ['class' => 'btn btn-primary btnGenerator']) ?>
                        </div>
                    </div>
                    <?=Html::hiddenInput('reloadDiv',$reloadDiv)?>
                    <?=Html::hiddenInput('color',isset($color) && $color != '' ? $color : 'panel-primary')?>
                </div>
            </div>
        </div>
    </form>
</div>
<div class="col-xs-12 col-sm-6 col-md-8 col-lg-8">
    <div id="divDataRandom">
        <?= $this->renderAjax('index', ['options' => $options,'color'=>$color,'reloadDiv'=>$reloadDiv]) ?>
    </div>
</div>

<!--config end-->

<?php

$this->registerJS("
    $('.btnGenerator').click(function(){
        let frm = $('#frmRandomization').serialize();
//        let url = '" . Url::to(['/random/randomization/save-session']) . "';
//        $.post(url, frm, function(data){
//            console.log(data);
//             getData('/random/randomization/index','#contacts',frm); 
//             $('#index').addClass('active');
//             $('#setting').removeClass('active');
//        });
        $.get('/random/randomization/index',frm, function(data) {
             $('#divDataRandom').html(data);
        });
        return false;
    });
    
    function getData(loadurl,targ,frm){
                    $(targ).html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
                    $.get(loadurl,frm, function(data) {
                        $(targ).html(data);
                    });
                }
");
?>
