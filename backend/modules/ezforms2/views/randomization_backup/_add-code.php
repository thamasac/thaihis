<?php

use yii\helpers\Html;
use appxq\sdii\helpers\SDNoty;

//use yii\helpers\ArrayHelper;
?>
<div class="modal-header">

</div>
<div class="modal-body">
    <div class="form-group row">
        <div class="col-md-12"> 
            <ul class="nav nav-tabs" id="tabRandom">
                <li class="active"><a data-toggle="tab" href="#code">Code</a></li>
                <li><a data-toggle="tab" href="#generator">Code Generator</a></li>
            </ul>
            <div class="tab-content">

                <div id="code" class="tab-pane fade in active" style="margin-top: 10px;">
                    <form id="formCode">
                        <div class="col-md-12">
                            <?= Html::hiddenInput('id', isset($data['id']) ? $data['id'] : appxq\sdii\utils\SDUtility::getMillisecTime()) ?>
                            <div class="col-md-12 divName" style="margin-top: 10px;">
                                <?= Html::label(Yii::t('chanpan', 'Name'), 'title', ['class' => 'control-label']) ?>
                                <?= Html::textInput("name", isset($data['name']) ? $data['name'] : '', ['class' => 'form-control name_code', 'id' => 'name_code']) ?>
                            </div>
                            <div class="col-md-12" style="margin-top: 10px;">
                                <?= Html::label(Yii::t('chanpan', 'Code Random'), 'title', ['class' => 'control-label']) ?>
                                <?= Html::textarea("code_random", isset($data['code_random']) ? $data['code_random'] : '', ['class' => 'form-control codeBox', 'rows' => 20, 'id' => 'codeBox']) ?>
                            </div>
                            <div class="col-md-6" style="margin-top: 10px;"><?= Html::label(Yii::t('chanpan', 'Code Index'), 'options[title]', ['class' => 'control-label']) ?>

                                <?= Html::input('number', 'code_index', isset($data['code_index']) ? $data['code_index'] : '', ['class' => 'form-control', 'id' => 'code_index']) ?>
                            </div>
                            <div class="col-md-6" style="margin-top: 10px;">
                                <?= Html::label(Yii::t('chanpan', 'Max Index'), 'title', ['class' => 'control-label']) ?>

                                <?= Html::input('number', 'max_index', isset($data['max_index']) ? $data['max_index'] : '', ['class' => 'form-control', 'id' => 'max_index']) ?>
                            </div>
                        </div>
                    </form>
                </div>
                <div id="generator" class="tab-pane fade" style="margin-top: 10px;">
                    <form id="formGenCode">
                        <div class="form-group row">
                            <div class="col-md-12">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= Html::label(Yii::t('chanpan', 'Seed'), 'title', ['class' => 'control-label']) ?>

                                        <?= Html::input('number', 'seed', isset($data['seed']) ? $data['seed'] : '', ['class' => 'form-control', 'id' => 'seed']) ?>
                                    </div>
                                </div>    
                                <div class="clearfix"></div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= Html::label(Yii::t('chanpan', 'Treatment groups'), 'title', ['class' => 'control-label']) ?>
                                        <?= Html::textInput('treatment', isset($data['treatment']) ? $data['treatment'] : Yii::t('chanpan', 'Group A, Group B'), ['class' => 'form-control', 'id' => 'treatment']) ?>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= Html::label(Yii::t('chanpan', 'Block sizes'), 'title', ['class' => 'control-label']) ?>
                                        <?= Html::textInput('block_size', isset($data['block_size']) ? $data['block_size'] :Yii::t('chanpan', '4, 6, 8'), ['class' => 'form-control', 'id' => 'block_size']) ?>
                                    </div>
                                </div>    
                                <div class="clearfix"></div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= Html::label(Yii::t('chanpan', 'List length'), 'title', ['class' => 'control-label']) ?>
                                        <?= Html::input('number', 'list_length', isset($data['list_length']) ? $data['list_length'] : '', ['class' => 'form-control', 'id' => 'list_length']) ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group pull-right">
                                        <?= Html::button(Yii::t('chanpan', 'Generator'), ['class' => 'btn btn-primary', 'id' => 'btnGen']) ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12" style="display: none"  id="output">
                                <div class="col-md-12">
                                    <h4><b><?= Yii::t('chanpan', 'Seed') ?>:</b><span id="v-seed"></span></h4>
                                    <h4><b><?= Yii::t('chanpan', 'Block sizes') ?>:</b><span id="v-block_size"> </span></h4>
                                    <h4><b><?= Yii::t('chanpan', 'Actual list length') ?>:</b><span id="v-list_length"></span></h4>
                                    <h4><b><?= Yii::t('chanpan', 'block identifier, block size, sequence within block, treatment') ?></b></h4>

                                    <div style=" position: relative; bottom: 10px; float:right">
                                        <!--<button class="btn btn-primary" id="btnDownload"><i class="fa fa-file-excel-o"></i> <?= Yii::t('chanpan', 'Download CSV') ?></button>-->
                                        <a class="btn btn-default btnCopy" id="btnCopy"><i class="fa fa-copy"></i> <?= Yii::t('chanpan', 'Copy') ?></a>
                                        <a class="btn btn-success btnInsert" id="btnInsert"><i class="glyphicon glyphicon-plus"></i> <?= Yii::t('chanpan', 'Insert to code box') ?></a>
                                    </div>  
                                    <div style="margin-top:10px;">  
                                        <?= Html::textarea("", '', ['class' => 'form-control myInput', 'rows' => 20, 'id' => 'myInput']) ?>
                                        <!--<textarea id="myInput" name="options[options][code_gen]" class="form-control" rows="20"><?php // isset($options['options']['code_gen']) ? $options['options']['code_gen'] : '' // $data = \backend\modules\random\classes\CNRandom::getRandomBlock($options, 1);                                               ?></textarea>-->
                                        <!--<textarea id="myInput2" style="display: none;" class="form-control" rows="20"><?php // $data = \backend\modules\random\classes\CNRandom::getRandomBlock($options, 2);                                               ?></textarea>-->
                                    </div>

                                    <div id="downloadCsv"></div>
                                </div>
                            </div>

                        </div> 
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <?= Html::button(isset($data)?'Update':'Add', ['class' => 'btn btn-success', 'id' => 'btnAddData','data-action'=>isset($data)?'update':'add']) ?>
</div>


<?php
$id = \appxq\sdii\utils\SDUtility::getMillisecTime();
$submodal = '<div id="modal-' . $id . '" class="fade modal" role="dialog"><div class="modal-dialog modal-md"><div class="modal-content"></div></div></div>';
$submodalFix = '<div id="modal-fix-' . $id . '" class="fade modal" role="dialog"><div class="modal-dialog modal-xxl"><div class="modal-content"></div></div></div>';



\richardfan\widget\JSRegister::begin();
?>
<script>

    var hasMyModal = $('body .sdbox').has('#modal-<?= $id ?>').length;

    if ($('body .modal').hasClass('in')) {
        if (!hasMyModal) {
            $('.sdbox').append('$submodal');
            $('.sdbox').append('$submodalFix');
        }
    }

    $('#modal-fix-<?= $id ?>').on('hidden.bs.modal', function (e) {
        $('#modal-fix-{$id} .modal-content').html('');

        if ($('body .modal').hasClass('in')) {
            $('body').addClass('modal-open');
        }
    });

    $('#modal-<?= $id ?>').on('hidden.bs.modal', function (e) {
        $('#modal-<?= $id ?> .modal-content').html('');

        if ($('body .modal').hasClass('in')) {
            $('body').addClass('modal-open');
        }
    });

    $('#btnAddData').on('click', function () {
        var code = $('#formGenCode').serializeArray();
        var genCode = $('#formCode').serializeArray();
        var arr = code.concat(genCode);
        if ($('#name_code').val() != '') {
            $('.divName').removeClass('has-error');
            if($(this).attr('data-action')=='add'){
                 var url = '/ezforms2/randomization/add';
            }else{
                var url = '/ezforms2/randomization/update';
            }
            $.post(url, arr, function (data) {
                if (data) {
                    $('#modal-gridtype').modal('hide');
<?= SDNoty::show('"Success"', '"success"') ?>
                } else {
<?= SDNoty::show('"Error"', '"error"') ?>
                }
            }).fail(function () {
<?= SDNoty::show('"Error"', '"error"') ?>
            });
        } else {
            $('.divName').addClass('has-error');
            <?= \appxq\sdii\helpers\SDNoty::show('"Name not empty"', '"error"') ?>
        }

    });
    
    $('#name_code').change(function(){
        if($(this).val() != ''){
            $('.divName').removeClass('has-error');
        }
    });

    $('.btnCopy').on('click', function () {
        myFunction()
    });
    $('#btnDownload').click(function () {
        let value = $('#myInput2').val();
        let url = '<?= yii\helpers\Url::to(['/random/randomization/downloadcsv']) ?>';
        $.get(url, {value: value}, function (data) {
            $('#downloadCsv').html(data);
        });
    });
    function myFunction() {
        var copyText = document.getElementById('myInput');
        copyText.select();
        $('.myInput').select();
        document.execCommand('Copy');
<?= SDNoty::show('"Success"', '"success"') ?>
        return false;

    }

    function insertCode(html) {
        $('.codeBox').val('');
        $('.codeBox').val(html);
        var str = $('.codeBox').val();
        str = str.split('\n');
        if (Array.isArray(str)) {
            str = str[0].split(',');
            if (Array.isArray(str)) {
                $('#code_index').val(str.length);
                $('#max_index').val(str.length);
            }
        }
<?= SDNoty::show('"Success"', '"success"') ?>
        return false;

    }
    $('#codeBox').on('change', function () {
        var str = $(this).val();
        str = str.split('\n');
        if (Array.isArray(str)) {
            str = str[0].split(',');
            if (Array.isArray(str)) {
                $('#code_index').val(str.length);
                $('#max_index').val(str.length);
            }
        }
    });

    $('.btnCopy').on('click', function () {
        myFunction();
        $('#modal-<?= $id ?>').modal('hide');
    });
    $('.btnInsert').on('click', function () {
        insertCode($('.myInput').html());
        $('#modal-<?= $id ?>').modal('hide');
    });

    $('#btnGen').on('click', function () {
        $('#v-seed').html($('#seed').val());
        $('#v-block_size').html($('#block_size').val());
        $('#v-list_length').html($('#list_length').val());
        $('#modal-<?= $id ?> .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
        $('#modal-<?= $id ?>').modal('show');
        $.get('<?= yii\helpers\Url::to(['/random/randomization/get-random-code']) ?>', {
            seed: $('#seed').val(),
            block_size: $('#block_size').val(),
            list_length: $('#list_length').val(),
            treatment: $('#treatment').val(),
            status: 1
        }, function (data) {
            $('#myInput').html(data);
            $('#output').show();
//            $('#modal-<?= $id ?> .modal-content').html(`<div class=\"modal-header\">
//        <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-hidden=\"true\">×</button>
//        <h4 class=\"modal-title\" id=\"itemModalLabel\">Code Generator</h4>
//    </div><div class='modal-body'>` + $('#output').html() + `</div><div class=\"modal-footer\">
//        <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\"><i class=\"glyphicon glyphicon-remove\"></i> ปิด</button>    </div>`);
//
        });
    }
    );
</script>
<?php \richardfan\widget\JSRegister::end(); ?>