<?php

use yii\helpers\Html;

?>
    <div class="panel <?= isset($color) && $color != '' ? $color : 'panel-primary' ?>">
        <div class="panel-heading">
            <?= Yii::t('chanpan', 'Randomization code generation') ?>
            <div style="float:right">
                <div class="form-inline">


                    <div class="form-group mb-2 divInput"
                         style="<?= isset($options['seed']) && $options['seed'] != '' ? '' : 'display:none' ?>">
                        <button class="btn btn-warning" id="btnDownload"><i
                                    class="glyphicon glyphicon-export"></i> <?= Yii::t('chanpan', 'Export CSV') ?>
                        </button>
                    </div>
                    <div class="form-group md-2 divInput"  style="<?= isset($options['seed']) && $options['seed'] != '' ? '' : 'display:none' ?>">
                        <button class="btn btn-success" id="btnCopyExcel"><i
                                    class="fa fa-copy"></i> <?= Yii::t('chanpan', ' Copy for excel') ?></button>
                    </div>
                </div>

                <!--                <button class="btn btn-success" id="btnCopy"><i class="fa fa-copy"></i> <?= Yii::t('chanpan', 'Copy') ?></button> -->

            </div>
            <div class="clearfix"></div></div>
        <div class="panel-body">
            <!--        <h4><b>--><? //= Yii::t('chanpan', 'Seed') ?><!--:</b> -->
            <? //= isset($options['seed'])?$options['seed']:'' ?><!--</h4>-->
            <!--        <h4><b>--><? //= Yii::t('chanpan', 'Block sizes') ?><!--:</b> -->
            <? //= isset($options['block_size']) ? $options['block_size'] : ''?><!--</h4>-->
            <!--        <h4><b>--><? //= Yii::t('chanpan', 'Actual list length') ?><!--:</b> -->
            <? //= isset($options['list_length']) ? $options['list_length'] :'' ?><!--</h4>-->
            <!--        <h4><b>-->
            <? //= Yii::t('chanpan', 'block identifier, block size, sequence within block, treatment') ?><!--</b></h4>-->


            <div style="margin-top:10px;">
                <div class="col-md-12" class="divInput"
                     style="<?= isset($options['seed']) && $options['seed'] != '' ? '' : 'display:none' ?>">
                    <textarea id="myInput" class="form-control"
                              rows="50"><?php isset($options['seed']) && $options['seed'] != '' && isset($options['block_size']) && $options['block_size'] != '' && isset($options['list_length']) && $options['list_length'] != '' ? \backend\modules\random\classes\CNRandom::getRandomBlock($options, 1) : ''; ?></textarea>
                    <!--                <textarea id="myInput2" style="display: none;" class="form-control" rows="50">-->
                    <?php //isset($options['seed'] ) && $options['seed'] != '' && isset($options['block_size'] ) && $options['block_size'] != '' && isset($options['list_length']) && $options['list_length'] != '' ? \backend\modules\random\classes\CNRandom::getRandomBlock($options, 2) : ''; ?><!--</textarea>-->
                </div>
                <div class="col-md-12">
                    <div id="data-downloadCsv" style="overflow-y: auto; height: 1px;opacity: 0;position: absolute;z-index: -1"></div>
                </div>
            </div>
        </div>
    </div>


<?php
$this->registerJs("
    
    $(document).ready(function(){
        loadDataTable();
    });
    
    function loadDataTable(){
         let value = $('#myInput').val();
         let url = '" . yii\helpers\Url::to(['/random/randomization/downloadcsv']) . "';
         $.post(url,{value:value,type:'1',table_id:'csv'}, function(data){
            $('#data-downloadCsv').html(data);
         });
    }
    $('#myInput').change(function(){
        var strData = $(this).val();
        setTimeout(()=>{
             var html = '<table class=\"table table-bordered\" id=\"csv\">';
                   if(strData != '' && typeof strData != 'undefined'){
                        var arrData = strData.split('\\n');
                        if(Array.isArray(arrData)){
                            html += '<tbody>';
                            for(var vData of arrData){
                                if(vData != ''){
                                    var arrDataSub = vData.split(',');
                                    if(Array.isArray(arrDataSub)){
                                        html += '<tr>';
                                        for(var vDataSub of arrDataSub){
                                            if(vDataSub != ''){
                                                html += '<td>'+vDataSub+'</td>';
                                            }
                                        }
                                        html += '</tr>';
                                    }
                                }
                                
                            }
                            html += '</tbody>';
                        }
                   }
                   html += '</table>';
                   $('#data-downloadCsv').html(html);
        },1500);
        
    });
    function selectElementContents(el) {
        var body = document.body, range, sel;
        if (document.createRange && window.getSelection) {
            range = document.createRange();
            sel = window.getSelection();
            sel.removeAllRanges();
            try {
                range.selectNodeContents(el);
                sel.addRange(range);
//                document.execCommand(\"Copy\");
            } catch (e) {
                range.selectNode(el);
                sel.addRange(range);
//                document.execCommand(\"Copy\");
            }
            document.execCommand(\"Copy\", false, null);
           
        } else if (body.createTextRange) {
            range = body.createTextRange();
            range.moveToElementText(el);
            range.select();
//            range.execCommand(\"Copy\");
            range.execCommand(\"Copy\", false, null);
        }
        " . appxq\sdii\helpers\SDNoty::show('"Success"', '"success"') . "
    }
    
    //Randomization code generator gen เมื่อไหร่ ทำโดยใคร แถบแรกเป็นโค้ด แทบที่ 2 เป็นค่าที่เขา set ไว้
    $('#btnCopy').click(function(){
        myFunction();
    });
    
    $('#btnCopyExcel').click(function(){
        selectElementContents(document.getElementById('csv'));
    });
    
    function insertCode(html) {
        $('#myInput').val('');
        $('#myInput').val(html);
        " . appxq\sdii\helpers\SDNoty::show('"Success"', '"success"') . "
        return false;

    }
    
    $('#random-import').bind('change',function(){
        var selected_file_name = $(this).val();
        var formData = new FormData();
        formData.append('file', $(this)[0].files[0]);
        formData.append('head', '1');
//        console.log( $(this)[0].files[0])
        if (selected_file_name.length > 0) {
            $.ajax({
                url: '" . yii\helpers\Url::to(['/ezforms2/randomization/import-scv']) . "',
                type: 'POST',
                data: formData,
                dataType:'JSON',
                processData: false, // tell jQuery not to process the data
                contentType: false, // tell jQuery not to set contentType
                success: function (data) {
                   $('.divInput').show();
                   var strData = data.data_text;
                   var strHead = data.data_head;
                   insertCode(strData);
                   var html = '<table class=\"table table-bordered\" id=\"csv\">';
                   if((strData != '' && typeof strData != 'undefined') && (strHead != '' && typeof strHead != 'undefined')){
                        var arrHead = strHead.split(',');
                        if(Array.isArray(arrHead)){
                            html += '<thead><tr>';
                            for(var vHead of arrHead){
                                html += '<th>'+vHead+'</th>';
                            }
                            html += '</tr></thead>';
                        }
                        var arrData = strData.split('\\n');
                        if(Array.isArray(arrData)){
                            html += '<tbody>';
                            for(var vData of arrData){
                                var arrDataSub = vData.split(',');
                                if(Array.isArray(arrDataSub)){
                                    html += '<tr>';
                                    for(var vDataSub of arrDataSub){
                                        html += '<td>'+vDataSub+'</td>';
                                    }
                                    html += '</tr>';
                                }
                                
                            }
                            html += '</tbody>';
                        }
                   }
                   html += '</table>';
                   $('#data-downloadCsv').html(html);
                }
            });
//            
        } else {
//            alert(selected_file_name);
        }
    });
    
    $('#btnDownload').click(function(){
        setTimeout(()=>{
             $('#csv').tableToCSV();
        },1500);
          
//         let value = $('#myInput2').val();
//         let url = '" . yii\helpers\Url::to(['/random/randomization/downloadcsv']) . "';
//         $.post(url,{value:value}, function(data){
//            $('#downloadCsv').html(data);
//         });
    });
    function myFunction() {
        var copyText = document.getElementById('myInput');
        copyText.select();
        document.execCommand('Copy');
        " . appxq\sdii\helpers\SDNoty::show('"Success"', '"success"') . "
//        
        
   }
") ?>