<?php

use yii\helpers\Html;
use appxq\sdii\helpers\SDNoty;

$fileInputID = 'fileinput_' . appxq\sdii\utils\SDUtility::getMillisecTime();
$display_code = isset($data['display_code']) ? appxq\sdii\utils\SDUtility::string2Array($data['display_code']) : [];
$code_index = isset($data['code_index']) ? $data['code_index'] : '';
//use yii\helpers\ArrayHelper;
?>
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    </div>
    <div class="modal-body">
        <div class="form-group row">
            <div class="col-md-12">
                <ul class="nav nav-tabs" id="tabRandom">
                    <li class="active" id="tab-code"><a data-toggle="tab" href="#code">Code</a></li>
                    <li id="tab-generator"><a data-toggle="tab" href="#generator">Code Generator</a></li>
                    <?php if (isset($data['id'])) { ?>
                        <li id="tab-data"><a data-toggle="tab" href="#data">Data</a></li>
                    <?php } ?>
                    <!--                </ul>-->
                    <div class="tab-content">

                        <div id="code" class="tab-pane fade in active form-group row" style="margin-top: 40px;">
                            <form id="formCode">
                                <div class="col-md-12">
                                    <?= Html::hiddenInput('id', isset($data['id']) ? $data['id'] : appxq\sdii\utils\SDUtility::getMillisecTime()) ?>
                                    <?= Html::hiddenInput('user_create', isset($data['user_create']) ? $data['user_create'] : Yii::$app->user->id) ?>
                                    <?= Html::hiddenInput('ezf_id', isset($data['ezf_id']) && $data['ezf_id'] != '' ? $data['ezf_id'] : $ezf_id) ?>
                                    <div class="col-md-12 form-group divName">
                                        <?= Html::label(Yii::t('chanpan', 'Rcode name'), 'title', ['class' => 'control-label']) ?>
                                        <?= Html::textInput("name", isset($data['name']) ? $data['name'] : '', ['class' => 'form-control name_code', 'id' => 'name_code']) ?>
                                    </div>
                                    <div class="col-md-12">
                                        <?= Html::label(Yii::t('chanpan', 'Code Random'), 'title', ['class' => 'control-label']) ?>
                                        <div class="row">
                                            <!--                                        <div class="form-group col-md-4">-->
                                            <!--                                            --><?php //echo Html::button('<i class="glyphicon glyphicon-paste"></i> Import form clipborad', ['class' => 'btn btn-primary', 'id' => 'btn-import']); ?>
                                            <!--                                        </div>-->
                                            <div class="form-group col-md-4">
                                                <?php
                                                echo kartik\widgets\FileInput::widget([
                                                    'name' => $fileInputID,
                                                    'id' => $fileInputID,
                                                    'pluginOptions' => [
                                                        'showPreview' => false,
                                                        'showCaption' => false,
                                                        'showRemove' => false,
                                                        'showUpload' => false,
                                                        'browseLabel' => 'Import CSV'
                                                    ],
                                                    'options' => ['accept' => '.csv']
                                                ]);
                                                ?>
                                            </div>

                                            <div class="col-md-4">
                                                <!--                                           --><?php //echo Html::button('<i class="glyphicon glyphicon-paste"></i> Generat code', ['class' => 'btn btn-primary', 'id' => 'btn-import']); ?>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="clearfix"></div>
                                    <hr/>
                                    <div class="form-group col-md-6 divCode divCodeIndex"
                                         style="display: none"><?= Html::label(Yii::t('chanpans', 'Code Index'), 'options[title]', ['class' => 'control-label']) ?>
                                        <div id="code_index">

                                        </div>
                                        <!--                                    --><?php //echo Html::input('number', 'code_index', isset($data['code_index']) ? $data['code_index'] : '', ['class' => 'form-control', 'id' => 'code_index']) ?>
                                    </div>
                                    <div class="form-group col-md-6 divCode" style="display: none">
                                        <?php echo Html::label(Yii::t('chanpans', 'Display'), 'title', ['class' => 'control-label']) ?>
                                        <div id="display">

                                        </div>
                                        <?php echo Html::input('number', 'max_index', isset($data['max_index']) ? $data['max_index'] : '', ['class' => 'form-control', 'id' => 'max_index', 'style' => 'display:none']) ?>
                                    </div>
                                    <div class="form-group col-md-6 divCode" style="display: none">
                                        <?php echo Html::label(Yii::t('chanpans', 'Start Row'), 'title', ['class' => 'control-label']) ?>
                                        <?php echo Html::input('number', 'start_row', isset($data['start_row']) ? $data['start_row'] : '1', ['class' => 'form-control', 'id' => 'start_row']) ?>
                                    </div>

                                    <div class="clearfix"></div>
                                    <div class="form-group col-md-12 divCode" style="display: none">
                                        <?php echo Html::button('Change setting', ['class' => 'btn btn-primary','id' => 'btn-change-setting']) ?>
                                        <?php echo Html::button('Preview setting', ['class' => 'btn btn-primary', 'id' => 'btn-preview']) ?>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <?= Html::textarea("code_random", isset($data['code_random']) ? $data['code_random'] : '', ['class' => 'form-control codeBox', 'rows' => 20, 'id' => 'codeBox']) ?>
                                    </div>
                                    <!--                                <div class="col-md-6" style="margin-top: 10px;">--><?php //echo Html::label(Yii::t('chanpan', 'Code Index'), 'options[title]', ['class' => 'control-label']) ?>
                                    <!---->
                                    <!--                                    --><?php //echo Html::input('number', 'code_index', isset($data['code_index']) ? $data['code_index'] : '', ['class' => 'form-control', 'id' => 'code_index']) ?>
                                    <!--                                </div>-->
                                    <!--                                <div class="col-md-6" style="margin-top: 10px;">-->
                                    <!--                                    --><?php //echo Html::label(Yii::t('chanpan', 'Max Index'), 'title', ['class' => 'control-label']) ?>
                                    <!---->
                                    <!--                                    --><?php //echo Html::input('number', 'max_index', isset($data['max_index']) ? $data['max_index'] : '', ['class' => 'form-control', 'id' => 'max_index']) ?>
                                    <!--                                </div>-->
                                </div>
                            </form>
                        </div>
                        <div id="generator" class="tab-pane fade" style="margin-top: 40px;">
                            <form id="formGenCode">
                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <div class="col-md-12">
                                            <div class="form-group" id="divSeed">
                                                <?= Html::label(Yii::t('chanpans', 'Seed'), 'title', ['class' => 'control-label']) ?>

                                                <?= Html::input('number', 'seed', isset($data['seed']) ? $data['seed'] : '', ['class' => 'form-control', 'id' => 'seed']) ?>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="col-md-12">
                                            <div class="form-group" id="divTreatment">
                                                <?= Html::label(Yii::t('chanpans', 'Treatment groups') . " " . Html::tag('code', 'Group A, Group B'), 'title', ['class' => 'control-label']) ?>
                                                <?= Html::textInput('treatment', isset($data['treatment']) ? $data['treatment'] : '', ['class' => 'form-control', 'id' => 'treatment']) ?>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="col-md-12">
                                            <div class="form-group" id="divBlockSize">
                                                <?= Html::label(Yii::t('chanpans', 'Block sizes') . " " . Html::tag('code', '4, 6, 8'), 'title', ['class' => 'control-label']) ?>
                                                <?= Html::textInput('block_size', isset($data['block_size']) ? $data['block_size'] : '', ['class' => 'form-control', 'id' => 'block_size']) ?>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="col-md-12">
                                            <div class="form-group" id="divListLength">
                                                <?= Html::label(Yii::t('chanpans', 'List length'), 'title', ['class' => 'control-label']) ?>
                                                <?= Html::input('number', 'list_length', isset($data['list_length']) ? $data['list_length'] : '', ['class' => 'form-control', 'id' => 'list_length']) ?>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group pull-right">
                                                <?= Html::button(Yii::t('chanpans', 'Generator'), ['class' => 'btn btn-primary', 'id' => 'btnGen']) ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12" style="display: none" id="output">
                                        <div class="col-md-12">
                                            <!--                                        <h4><b>-->
                                            <? //= Yii::t('chanpan', 'Seed') ?><!--:</b><span id="v-seed"></span></h4>-->
                                            <!--                                        <h4><b>-->
                                            <? //= Yii::t('chanpan', 'Block sizes') ?><!--:</b><span-->
                                            <!--                                                    id="v-block_size"> </span></h4>-->
                                            <!--                                        <h4><b>-->
                                            <? //= Yii::t('chanpan', 'Actual list length') ?><!--:</b><span-->
                                            <!--                                                    id="v-list_length"></span></h4>-->
                                            <!--                                        <h4>-->
                                            <!--                                            <b>-->
                                            <? //= Yii::t('chanpan', 'block identifier, block size, sequence within block, treatment') ?><!--</b>-->
                                            <!--                                        </h4>-->

                                            <div style=" position: relative; bottom: 10px; float:right">
                                                <!--<button class="btn btn-primary" id="btnDownload"><i class="fa fa-file-excel-o"></i> <?= Yii::t('chanpan', 'Download CSV') ?></button>-->
                                                <a class="btn btn-default btnCopy" id="btnCopy"><i
                                                            class="fa fa-copy"></i> <?= Yii::t('chanpans', 'Copy to clipboard') ?>
                                                </a>
                                                <a class="btn btn-success btnInsert" id="btnInsert"><i
                                                            class="glyphicon glyphicon-plus"></i> <?= Yii::t('chanpan', 'Insert to code box') ?>
                                                </a>
                                            </div>
                                            <div style="margin-top:10px;">
                                                <?= Html::textarea("", '', ['class' => 'form-control myInput', 'rows' => 20, 'id' => 'myInput']) ?>
                                                <!--<textarea id="myInput" name="options[options][code_gen]" class="form-control" rows="20"><?php // isset($options['options']['code_gen']) ? $options['options']['code_gen'] : '' // $data = \backend\modules\random\classes\CNRandom::getRandomBlock($options, 1);                                                          ?></textarea>-->
                                                <!--<textarea id="myInput2" style="display: none;" class="form-control" rows="20"><?php // $data = \backend\modules\random\classes\CNRandom::getRandomBlock($options, 2);                                                         ?></textarea>-->
                                            </div>

                                            <div id="downloadCsv"></div>
                                        </div>
                                    </div>

                                </div>
                            </form>
                        </div>
                        <div id="data" class="tab-pane fade" style="margin-top: 40px;">

                        </div>
                    </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <?= Html::button(isset($data) ? 'Update' : 'Add', ['class' => 'btn btn-success', 'disabled' => isset($data) ? false : true, 'id' => 'btnAddData', 'data-action' => isset($data) ? 'update' : 'add']) ?>
        <?= Html::button('<i class="glyphicon glyphicon-remove"></i> ' . Yii::t('app', 'Cancel'), ['class' => 'btn btn-default', 'data-dismiss' => 'modal']) ?>
    </div>


<?php
$id = \appxq\sdii\utils\SDUtility::getMillisecTime();
$submodal = '<div id="modal-import-' . $id . '" class="fade modal" role="dialog"><div class="modal-dialog modal-md"><div class="modal-content"></div></div></div>';
$submodalFix = '<div id="modal-fix-' . $id . '" class="fade modal" role="dialog"><div class="modal-dialog modal-xxl"><div class="modal-content"></div></div></div>';


\richardfan\widget\JSRegister::begin();
?>
    <script>

        var hasMyModal = $('body .sdbox').has('#modal-import-<?= $id ?>').length;
        var hasModal = $('body').has('#modal-gridtype').length;

        if ($('body .modal').hasClass('in')) {
            if (!hasMyModal) {
                $('.sdbox').append('<?= $submodal ?>');
                $('.sdbox').append('<?= $submodalFix ?>');
            }
        }

        $('#modal-fix-<?= $id ?>').on('hidden.bs.modal', function (e) {
            $('#modal-fix-<?= $id ?> .modal-content').html('');

            if ($('body .modal').hasClass('in')) {
                $('body').addClass('modal-open');
            }
        });

        $('#modal-import-<?= $id ?>').on('hidden.bs.modal', function (e) {
            $('#modal-import-<?= $id ?> .modal-content').html('');

            if ($('body .modal').hasClass('in')) {
                $('body').addClass('modal-open');
            }
        });

        $('#btn-import').click(function () {
            var retval = document.execCommand('paste');
            console.log(navigator.clipboard);
        });

        $('#tab-data').click(function () {
            var dataid = '<?= isset($data['id']) ? $data['id'] : '' ?>';
            $.get('/ezforms2/randomization/view-data?random_id=' + dataid + '&sitecode=<?= $sitecode ?>&have-modal=1', function (data) {
                $('#data').html(data);
            });
        });

        $('#btnAddData').on('click', function () {

//        $('#<?= $fileInputID ?>').attr('disabled', true);
//        var code = $('#formGenCode').serializeArray();
            var genCode = $('#formCode').serializeArray();
//        var arr = code.concat(genCode);

            if (typeof $('.code_index').filter(':checked').val() == 'undefined') {
                $('.divCodeIndex').addClass('has-error');
                $('#code_index').addClass('has-error');
                <?= \appxq\sdii\helpers\SDNoty::show('"Code Index not empty"', '"error"') ?>
                return;
            }else{
                $('.divCodeIndex').removeClass('has-error');
                $('#code_index').removeClass('has-error');
            }
            if ($('#name_code').val() != '') {
                $('.divName').removeClass('has-error');
                if ($(this).attr('data-action') == 'add') {
                    var url = '/ezforms2/randomization/add';
                } else {
                    var url = '/ezforms2/randomization/update-data';
                }
                $.post(url, genCode, function (data) {
                    if (data) {
                        if (hasModal) {
                            $('#modal-gridtype').modal('hide');
                        } else {
                            $('#btnAddData').parents('.modal').modal('hide');
                        }
                        <?= SDNoty::show("'" . Yii::t('ezform', 'Complete') . "'", '"success"') ?>
                    } else {
                        <?= SDNoty::show("'" . Yii::t('ezform', 'Failed') . "'", '"error"') ?>
                    }
                }).fail(function () {
                    <?= SDNoty::show('"Server Error"', '"error"') ?>
                });
            } else {
                $('.divName').addClass('has-error');
                <?= \appxq\sdii\helpers\SDNoty::show('"Name not empty"', '"error"') ?>
            }

        });

        $('#name_code').change(function () {
            if ($(this).val() != '') {
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
            $('#btnAddData').attr('disabled', false);
            $('#tab-generator').removeClass('active');
            $('#tab-data ').removeClass('active');
            $('#tab-code').addClass('active');
            $('#code').addClass('in').addClass('active');
            $('#generator').removeClass('active').removeClass('in');
            $('#data').removeClass('active').removeClass('in');
            $('.codeBox').val('');
            $('.codeBox').val(html);
            var str = $('.codeBox').val();
            initValue(str,true);
            // str = str.split('\n');
            // if (Array.isArray(str)) {
            //     str = str[0].split(',');
            //     if (Array.isArray(str)) {
            //         $('#code_index').val(str.length);
            //         $('#max_index').val(str.length);
            //     }
            // }
            <?= SDNoty::show('"Success"', '"success"') ?>
            return false;

        }

        initValue($('.codeBox').val(),'');

        function initValue(strVal,change = false,setting=false) {
            var display_code = <?=\yii\helpers\Json::encode($display_code)?>;
            if (setting == true) {
                display_code = [];
                $('.display_code').each(function () {
                    if ($(this).is(':checked')) {
                        display_code.push($(this).val());
                    }
                });
            }
            var codeIndexHtml = '';
            var displayHtml = '';
            var check_code_index = '';
            var check_display_code = '';
            var change_setting = '';
            if (strVal != '') {
                $('.divCode').show();
            } else {
                $('.divCode').hide();
            }
            strVal = strVal.split('\n');
            if (Array.isArray(strVal)) {
                var str = null;
                $.each(strVal, function (kStr, vStr) {
                    str = vStr.split(',');
                    if (Array.isArray(str)) {
                        $.each(str, function (k, v) {
                            if (setting == true) {
                                // if ($('#start_row').val() <= kStr) {
                                    if ($.inArray((k + 1).toString(), display_code) >= 0) {
                                        change_setting += v + ',';
                                    }
                                // }
                            }
                            if (kStr == 0) {
                                check_code_index = '';
                                check_display_code = '';
                                if ('<?=$code_index?>' == k + 1 && change == false) {
                                    check_code_index = 'checked';
                                }
                                if ($.inArray((k + 1).toString(), display_code) >= 0 || change != false) {
                                    check_display_code = 'checked';
                                }
                                codeIndexHtml += '<label><input type="radio" name="code_index" class="code_index" ' + check_code_index + ' value="' + (k + 1) + '"/> Index ' + (k + 1) + ' : ' + v + ' </label><br/>';
                                displayHtml += '<label><input type="checkbox" name="display_code[]" class="display_code" ' + check_display_code + ' value="' + (k + 1) + '"/> ' + (k + 1) + ' : ' + v + ' </label><br/>';
                            }
                        });
                        // if($('#start_row').val() <= kStr) {
                        if (setting == true) {
                            change_setting = change_setting.substr(0, change_setting.length - 1);
                            change_setting += '\n';
                        }

                        // }
                        // $('#code_index').val(str.length);
                        if (kStr == 0) {
                            $('#code_index').html(codeIndexHtml);
                            $('#display').html(displayHtml);
                            $('#max_index').val(str.length);
                            if (setting == false){
                                return;
                            }
                        }


                    }
                });
                if (setting == true) {
                    // change_setting = change_setting.substr(0,change_setting.length-2);
                    $('#codeBox').val(change_setting);
                    $('#codeBox').trigger('change');

                }
            }

        }

        $('#btn-change-setting').click(function(){
            initValue($('#codeBox').val(),false,true);
        });

        $('#btn-preview').click(function () {
            var display = [];
            $('.display_code').each(function () {
                if ($(this).is(':checked')) {
                    display.push($(this).val());
                }

            });
            $('#modal-import-<?=$id?>').modal('show');
            $.post('/ezforms2/randomization/preview-code',{display_code:display,start_row:$('#start_row').val(),code_index:$('.code_index').filter(':checked').val(),code:$('#codeBox').val(),modalID:'modal-import-<?=$id?>'},function(data){
                $('#modal-import-<?=$id?> .modal-content').html(data);
            });
        });

        $('#codeBox').on('change', function () {
            var str = $(this).val();
            if (str != '') {
                $('#btnAddData').attr('disabled', false);
            } else {
                $('#btnAddData').attr('disabled', true);
            }
            initValue(str,true);
            // str = str.split('\n');
            // if (Array.isArray(str)) {
            //     str = str[0].split(',');
            //     if (Array.isArray(str)) {
            //         var codeIndexHtml = '';
            //         $.each(str,function(k,v){
            //             codeIndexHtml += '<label><input type="radio" name="test" value="'+(k+1)+'"/> Index '+(k+1)+' : '+v+' </label><br/>';
            //         });
            //         // $('#code_index').val(str.length);
            //         $('#code_index').html(codeIndexHtml);
            //         $('#max_index').val(str.length);
            //     }
            // }
        });


        $('#<?= $fileInputID ?>').click(function () {
            $(this).val('');
        });
        $('#<?= $fileInputID ?>').bind('change', function () {
            var selected_file_name = $(this).val();
            var formData = new FormData();
            formData.append('file', $(this)[0].files[0]);
//        console.log( $(this)[0].files[0])
            if (selected_file_name.length > 0) {
                $.ajax({
                    url: '<?= yii\helpers\Url::to(['/ezforms2/randomization/import-scv']) ?>',
                    type: 'POST',
                    data: formData,
                    dataType: 'JSON',
                    processData: false, // tell jQuery not to process the data
                    contentType: false, // tell jQuery not to set contentType
                    success: function (data) {
                        insertCode(data.data_text);

                    }
                });
//            
            } else {
//            alert(selected_file_name);
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
                $('#divSeed').removeClass('has-error');
                $('#divBlockSize').removeClass('has-error');
                $('#divListLength').removeClass('has-error');
                $('#divTreatment').removeClass('has-error');
//        $('#modal-<?= $id ?> .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
//        $('#modal-<?= $id ?>').modal('show');
                if ($('#seed').val() == '') {
                    $('#divSeed').addClass('has-error');
                    <?= \appxq\sdii\helpers\SDNoty::show('"Seed not empty"', '"error"') ?>
                    return false;
                }
                if ($('#treatment').val() == '') {
                    $('#divTreatment').addClass('has-error');
                    <?= \appxq\sdii\helpers\SDNoty::show('"Treatment group not empty"', '"error"') ?>
                    return false;
                }
                if ($('#block_size').val() == '') {
                    $('#divBlockSize').addClass('has-error');
                    <?= \appxq\sdii\helpers\SDNoty::show('"Block size not empty"', '"error"') ?>
                    return false;
                }
                if ($('#list_length').val() == '') {
                    $('#divListLength').addClass('has-error');
                    <?= \appxq\sdii\helpers\SDNoty::show('"List length not empty"', '"error"') ?>
                    return false;
                }

                $.get('<?= yii\helpers\Url::to(['/ezforms2/randomization/get-random-code']) ?>', {
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