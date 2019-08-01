<?php

use yii\helpers\Html;
?>
<div class="line-default-index" id="line">
    <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
        <div class="panel panel-default"> 
            <div class="panel-heading"> 
                <div class="pull-left"><h3 class="panel-title"><?= $title ?></h3></div> 
                <?php if (Yii::$app->user->can('administrator')) { ?>
<!--                    <div class="pull-right">-->
                        <?=
                        Html::a('', '#', [
                            'class' => 'fa fa-edit fa-2x underline pull-right',
                            'title' => Yii::t('line', 'Edit'),
                            'id' => 'btnEditLine'
                        ])
                        ?>
<!--                    </div>-->
                <?php } ?>
                <?= $lineid != '' ? Html::button('Disconnect Line',['class'=> 'btn btn-danger btn-sm pull-right','id'=>'btn-disconnect','data-id'=>$user_id]): ''?>
                <div class="clearfix"></div>
            </div> 
            <div class="panel-body">

                <?php
                if ($line_qrcode == '') {
                    echo \yii\helpers\Html::tag('div', \Yii::t('line', 'Empty Line Data'), ['class' => 'alert alert-danger']);
                } else {
                    if ($lineid == "") {
                        ?>
                        <div class="col-md-4 col-sm-12 col-xs-12">
                            <center><img src="<?= $line_qrcode ?>" class="img-responsive" width="80%" height="80%"></center>

                        </div>
                        <div class="col-md-8 col-sm-12 col-xs-12">

                            <div class="page-header">
                                <h4>
<!--                                    <i class="fa fa-cog" aria-hidden="true"></i> -->
                                    <?= Yii::t('line', 'How to setup line notification') ?>
                                </h4>
                            </div>                    
                            <ul class="list-group">
                                <li class="list-group-item text-center"><?= Yii::t('line', 'Open the Line application and add a friend from QRCODE, then enter OTP (one time password) by typing the number below.') ?> <br/><code><?= Yii::t('line', 'This OTP code is only valid for 5 minutes.') ?></code></li>
                                <li class="list-group-item"><center>
                                    <div id='countdown' style='display:none' class='alert alert-success'><strong>05 : 00</strong></div>
                                    <h3 style="display: inline-block;">
                                        <span id='pincode' class="label label-danger"><?= $pincode; ?></span></h3></center></li>
                            </ul>

                        <?php } else { ?>
                            <div class="page-header text-center">
                                <h4>
<!--                                    <i class="fa fa-cog" aria-hidden="true"></i>-->
                                    <?= Yii::t('line', 'Connect line success') ?>

                                </h4>
                            </div>                    
                            <div class="col-md-6 col-sm-12 col-xs-12">
                                <img style="max-width: 200px; max-width: 200px" src="<?php echo Yii::$app->user->identity->profile->avatar_path != '' && Yii::$app->user->identity->profile->avatar_base_url != '' ? Yii::$app->user->identity->profile->avatar_base_url . "/" . Yii::$app->user->identity->profile->avatar_path : Yii::getAlias('@storageUrl'). '/images/nouser.png' ?>" alt="" class="center-block img-thumbnail img-responsive">
                                <hr>
                                <div class="text-center">
                                    Name : <?=Yii::$app->user->identity->profile->firstname." ".Yii::$app->user->identity->profile->lastname?>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12 col-xs-12">

                                <img style="max-width: 200px; max-width: 200px" src="<?php echo $lineimg; ?>" alt="" class="center-block img-thumbnail img-responsive">
                                <hr>
                                <div class="text-center">
                                    Line : <?=$line_name?>
                                </div>
                            </div>                        
                        <?php
                        }
                    }
                    ?>
                </div>                
            </div> 
        </div>        
    </div>
</div>

</body>
</html>
<?php
$modal_line = 'modal-line-' . \appxq\sdii\utils\SDUtility::getMillisecTime();
echo yii\bootstrap\Modal::widget([
    'id' => $modal_line,
    'size' => 'modal-lg'
]);

\richardfan\widget\JSRegister::begin()
?>
<script>
console.log(<?=Yii::$app->user->id == '1522145992064240300' ? json_encode($line_message) : ''?>);

function getUiAjax(url, divid) {
//    $('#'+divid).html('<div class=\"sdloader\"><i class=\"sdloader-icon\"></i></div>');
    $.ajax({
        method: 'GET',
        url: url,
        dataType: 'HTML',
        success: function (result, textStatus) {
            $('#' + divid).html(result);
        }
    }).fail(function (err) {
        err = JSON.parse(JSON.stringify(err))['responseText'];
        $('#' + divid).html(`<div class='alert alert-danger'>` + err + `</div>`);
    });
}

    $('#btnEditLine').on('click', function () {
        $('#<?= $modal_line ?> .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
        $('#<?= $modal_line ?>').modal('show')
                .find('.modal-content')
                .load('/line/default/edit?reloadDiv=<?= $reloadDiv ?>&modal=<?= $modal_line ?>');
    });

    $('#<?= $modal_line ?>').on('hidden.bs.modal', function () {
        getUiAjax($('#<?= $reloadDiv ?>').attr('data-url'), '<?= $reloadDiv ?>');
    });

    $('#<?= $modal_line ?>').on('hidden.bs.modal', function (e) {
        if ($('body .modal').hasClass('in')) {
            $('body').addClass('modal-open');
        }
        getUiAjax($('#<?= $modal_line ?>').attr('data-url'), '<?= $modal_line ?>');
    });

    $('#btn-disconnect').click(function(){
        $.post('/line/default/disconnect',{user_id:$('#btn-disconnect').attr('data-id')},function(data){
            if(data == 'success'){
                location.reload();
            }else{
                <?= \appxq\sdii\helpers\SDNoty::show("'" . Yii::t('ezform', 'Error') . "'", '"error"') ?>;
            }
        });
    });


    var lineid = '<?= $lineid ?>';
    var line_qrcode = '<?= $line_qrcode ?>';
    if (lineid == '' && line_qrcode != '') {

        var min = 4;
        var sec = 60;
        $('#countdown').show();
        var check_otp = setInterval(function () {
            checkOTP();
        }, 3 * 1000);
        var time = setInterval(function () {
            sec--;
            if (min == 0 && sec == 0) {
                clearInterval(time);
                clearInterval(check_otp);
                setTimeout(() => {
                    location.reload();
                }, 3 * 1000);
//                $.get('<?php // echo yii\helpers\Url::to(['/line/default/pincode'])       ?>', {pincode: $('#pincode').html()}, function (data) {
////                    $('#countdown').hide();
////                    $('#pincode').html('New PIN Code').addClass('btn newpin');
////                    $('.newpin').on('click',function(){
////                        location.reload();
////                    });
//                });
            }
            if (sec == 0 && min != 0) {
                sec = 60;
                min--;
            }
            if (sec < 10) {
                sec = '0' + sec;
            }
            $('#countdown').html('<strong>0' + min + ' : ' + sec + '</strong>');
        }, 1000);



        function checkOTP() {
            $.get('<?= yii\helpers\Url::to(['/line/default/check-otp']) ?>', {pincode: $('#pincode').html()}, function (data) {
                if (data == 'success') {
                    clearInterval(time);
                    clearInterval(check_otp);
                    $('#pincode').html('Success').removeClass('label-danger').addClass('label-success');
                    setTimeout(() => {
                        location.reload();
                    }, 1000);

                }

            });
        }
    }
</script>

<?php \richardfan\widget\JSRegister::end() ?>

