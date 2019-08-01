<?php

use yii\helpers\Html;
use yii\helpers\Url;


?>
<div class="row">
    <div class="col-md-12">
        <div >
            <div class="upload-msg">
                <?= Html::img($image, ['id' => 'preview_icon', 'class' => 'img-rounded']) ?>
            </div>
        </div>
        <div class="form-group">
            <div class="upload-edit">
                <div id="upload-edit"></div>
                <div id="upload-action" class="text-center">
                    <a id="save-upload" class="btn btn-success"><?= Yii::t('ezform', 'Save Icon') ?></a>
                </div>
                <input type="hidden" id="change_icon"/> 
                <div id="div-upload-file">

                    <?= Html::fileInput('upload_input', null, ['id' => 'upload-input', 'class' => '']) ?>                      
                </div>
            </div>
        </div>
    </div>
</div>
<?php \appxq\sdii\widgets\CSSRegister::begin(); ?>
<style>

    #preview_icon{width:100px;height:100px;}
    /*file uploads*/

    .custom-file-upload {
        border: 1px solid #ccc;
        display: inline-block;
        padding: 6px 12px;
        cursor: pointer;
        font-size: 16pt;
    }
    #upload-input {
        /*        font-size: 16pt;
                cursor: pointer;
                position: absolute;
                left: -75px;
                top: -4px;
                opacity: 0;
                margin-top: 5px;*/
    }

    .croppie-container{
        height:auto;
    }
    #upload-action{margin-bottom:10px;}
    .upload-msg{
        margin: 10px auto;
        overflow: hidden;
        text-align: center;
        width: 115px;
        height: 115px;
        background: #f1efef57;
        padding: 8px;
        border-radius: 5px;
        box-shadow: 1px 1px 1px 1px #d8d8d8;
    }


</style>
<?php \appxq\sdii\widgets\CSSRegister::end(); ?>
<?php \richardfan\widget\JSRegister::begin(); ?>
<script>

    /* crop images */
    var uploadCrops;

    function readFile(input) {
        console.log(input);
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                uploadCrops.croppie('bind', {
                    url: e.target.result
                });
                $('.upload-edit').addClass('ready');
            }

            reader.readAsDataURL(input.files[0]);
        } else {
            swal("Sorry - you're browser doesn't support the FileReader API");
        }
    }

    uploadCrops = $('#upload-edit').croppie({
        enableExif: true,
        viewport: {
            width: 100,
            height: 100,
            type: 'square' //square, circle
        },
        boundary: {
            width: 150,
            height: 150
        }
    });

    $('#upload-input').on('change', function () {
        readFile(this);
    });


    $('#save-upload').on('click', function () {
        uploadCrops.croppie('result', {
            type: 'canvas',
            size: 'viewport'
        }).then(function (resp) {
            //alert( resp );
            $('#change_icon').val(resp);
            $('#ez1523071255006806900-projecticon').val(resp);
            $('#preview_icon').attr('src', resp);
            $('.upload-edit').removeClass('ready');
            $('#change_icon').trigger('change');
        });
        return false;
    });
</script>
<?php \richardfan\widget\JSRegister::end(); ?>