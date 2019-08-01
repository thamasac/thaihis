<?php

//\backend\modules\ezforms2\classes\EzfStarterWidget::begin();
use appxq\sdii\helpers\SDNoty;
use yii\helpers\Url;

//\appxq\sdii\utils\VarDumper::dump($data);
?>
<div class="list-group list-group-flush divMain" style="word-wrap: break-word; margin: -6px -9px -6px -9px;">
    <?php
    foreach ($data as $value) {
        ;
        ?>
        <button  
            class="btnViewNotify list-group-item <?= $value['status_view'] == 0 ? 'list-group-item-info' : '' ?>" 
            data-id ="<?= $value['id'] ?>"
            data-status_view ="<?= $value['status_view'] ?>" >
            <p><strong><span class="glyphicon glyphicon-exclamation-sign"></span> <?= $value['notify'] ?></strong></p>
            <!--<p><small><?php // echo $value['detail']     ?></small></p>-->
            <p><small><span class="glyphicon glyphicon-time"></span> <?php
                    if ($value['due_date_assign'] != '') {
                        echo $value['due_date_assign'].' '.$value['time_notify'];
                    } else if ($value['due_date_assign'] == '' && $value['delay_date'] != '') {
                        echo $value['delay_date'].' '.$value['time_notify'];
                    } else {
                        echo $value['update_date'];
                    }
                    ?></small></p>
        </button>
        <?php
    }
    ?>
</div>

<?php
$view = Yii::$app->getView();
$idModal = 'modal-notify';
$modal = '<div id="' . $idModal . '" class="fade modal" role="dialog"><div class="modal-dialog modal-lg"><div class="modal-content"></div></div></div>';

$idModalDetail = 'modal-notify-detail';
$modalDetail = '<div id="' . $idModalDetail . '" class="fade modal" role="dialog"><div class="modal-dialog modal-xxl"><div class="modal-content"></div></div></div>';

richardfan\widget\JSRegister::begin();
?>

<script>

    var hasMyModal = $('body').has('#<?= $idModal ?>').length;
    var hasMyModalDetail = $('body').has('#<?= $idModalDetail ?>').length;
    var hasDiv = $('body').has('#ezf-main-box').length;


    if (!hasDiv) {
        $('.page-column').append(`<div id="ezmodule-main-app" class="ezmodule-view">
            <div class="modal-body">
                <div id="ezf-main-box">
                    <div id="ezf-modal-box">
       
<?= $modal ?>
<?= $modalDetail ?>
<?php
\backend\modules\ezforms2\assets\EzfGenAsset::register($view);
\backend\modules\ezforms2\assets\EzfColorInputAsset::register($view);
\backend\modules\ezforms2\assets\DadAsset::register($view);
\backend\modules\ezforms2\assets\EzfToolAsset::register($view);
\backend\modules\ezforms2\assets\EzfTopAsset::register($view);
\backend\modules\ezforms2\assets\ListdataAsset::register($view);
?>
                    </div>
                </div>
            </div>
        </div>
        `);
    } else {
        if (!hasMyModal) {
            $('#ezf-modal-box').append(`
<?= $modal ?>
<?= $modalDetail ?>
<?php
\backend\modules\ezforms2\assets\EzfGenAsset::register($view);
\backend\modules\ezforms2\assets\EzfColorInputAsset::register($view);
\backend\modules\ezforms2\assets\DadAsset::register($view);
\backend\modules\ezforms2\assets\EzfToolAsset::register($view);
\backend\modules\ezforms2\assets\EzfTopAsset::register($view);
\backend\modules\ezforms2\assets\ListdataAsset::register($view);
?>
`);

        }
    }



    $('#<?= $idModal ?>').on('hidden.bs.modal', function (e) {
        $('#<?= $idModal ?> modal-content').html('');
        if ($('body .modal').hasClass('in')) {
            $('body').addClass('modal-open');
            $('#<?= $idModal ?>').remove();
        }
    });

    $('#<?= $idModalDetail ?>').on('hidden.bs.modal', function (e) {
        $('#<?= $idModalDetail ?> modal-content').html('');
        if ($('body .modal').hasClass('in')) {
            $('body').addClass('modal-open');
        }
        $('#<?= $idModalDetail ?>').remove();
    });

    $("#btnViewAll").on('click', function () {
        window.location = '<?= Url::to(['/ezmodules/ezmodule/view?id=1520785643053421500']); ?>';
    });

    $('.btnViewNotify').on('click', function () {

// console.log('click detail notify');
        var elementId = $(this);
        var data_id = $(this).attr('data-id');
        var status_view = $(this).attr('data-status_view');
//        alert(status_view);
        if (status_view == '0') {
            $.ajax({
                method: 'POST',
                url: '/notify/notify/viewed?id=' + data_id,
                dataType: 'HTML',
                success: function () {
                    elementId.removeClass('list-group-item-info');
//                    $('#<?= $reloadDiv ?>').popover('hide');
                    getCount();
                }
            });
        }

        $('#<?= $idModal ?> .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
        $('#<?= $idModal ?>').modal('show')
                .find('.modal-content').load('/notify/notify/detail?id=' + data_id + '&modal=<?= $idModal ?>&sub_modal=<?= $idModalDetail ?>');
        $('#<?=$reloadDiv?>').click();
    });


    function getCount() {
        $.ajax({
            method: 'POST',
            url: '<?= Url::to('/notify/notify/count-notify') ?>',
            dataType: 'HTML',
            success: function (result) {
                $('#<?= $notifyId ?>').html(result);
            }
        });
    }

    function getUiAjax(url, divid) {
        $.ajax({
            method: 'GET',
            url: url,
            dataType: 'HTML',
            success: function (result, textStatus) {
                $('#' + divid).html(result);
            }
        });
    }
    if (!$('.popover').has('.btnViewAll').length) {
        $('.popover').append('<div class=\'btnViewAll btn btn-default btn-block\'><?= Yii::t('notify', 'View All') ?></div>');
    }
    $('.btnViewAll').on('click', function () {
        window.location = '<?= Url::to(['/ezmodules/ezmodule/view?id=1520785643053421500']) ?>';
    });
//    var count = 200;
//    var scrollTop = 0;
//    $('.popover-content').scroll(function () { //detact scroll
//        console.log(count+' '+scrollTop);
//        scrollTop = $(this).scrollTop();
//        if (scrollTop > count) { //scrolled to bottom of the page
//             console.log($(this).scrollTop()+' '+count); //load content chunk 
//             count *= 2;
//             $('.divMain').append($('.divMain').html());
//            
////            alert('finish');
//        }
//    });
</script>

<?php
richardfan\widget\JSRegister::end();

$view->registerJs("
        if(!hasMyModal){
            $('#modal-ezform-main').on('hidden.bs.modal', function (e) {
                $('#ezf-modal-box').html('');
                $('#modal-ezform-main .modal-content').html('');
            });

            $('#modal-ezform-community').on('hidden.bs.modal', function(e){
                $('#modal-ezform-community .modal-content').html('');
                var hasmodal = $('body .modal').hasClass('in');
                if(hasmodal){
                    $('body').addClass('modal-open');
                } 
            });

            $('#modal-ezform-main').on('hidden.bs.modal', function(e){
                $('#modal-ezform-main .modal-content').html('');
                var hasmodal = $('body .modal').hasClass('in');
                if(hasmodal){
                    $('body').addClass('modal-open');
                } 
            });

            $('#ezf-main-box').on('click', '.ezform-main-open', function(){
                var url = $(this).attr('data-url');
                var modal = $(this).attr('data-modal');

                var lat = $(this).attr('data-lat');
                var lng = $(this).attr('data-lng');
                var lat_field = $(this).attr('data-lat-field');
                var lng_field = $(this).attr('data-lng-field');

                if(lat && lng){
                    var data_set = {};
                    data_set[lat_field] = lat;
                    data_set[lng_field] = lng;

                    data_set = btoa(JSON.stringify(data_set));
                    modalEzformMain(url+data_set, modal);
                } else {
                    modalEzformMain(url, modal);
                }
            });

            $('#ezf-main-box').on('click', '.btn-querytool', function(){
                var url = $(this).attr('data-url');
                var modal = 'modal-ezform-community';

                modalEzformMain(url, modal);
            });

            $('#ezf-main-box').on('click', '.ezform-delete', function(){
                var url = $(this).attr('data-url');
                var url_reload = $(this).attr('data-url-reload');

                yii.confirm('" . Yii::t('app', 'Are you sure you want to delete this item?') . "', function(){
                    $.post(
                            url, {'_csrf':'" . Yii::$app->request->getCsrfToken() . "'}
                    ).done(function(result){
                            if(result.status == 'success'){
                                    " . SDNoty::show('result.message', 'result.status') . "
                                    var urlreload =  $('#'+result.reloadDiv).attr('data-url');
                                    if(urlreload){
                                        getUiAjax(urlreload, result.reloadDiv);
                                    }
                            } else {
                                    " . SDNoty::show('result.message', 'result.status') . "
                            }
                    }).fail(function(){
                            " . SDNoty::show("'" . "Server Error'", '"error"') . "
                            console.log('server error');
                    });
                });
            });

            function getUiAjax(url, divid) {
                $.ajax({
                    method: 'POST',
                    url: url,
                    dataType: 'HTML',
                    success: function(result, textStatus) {
                        $('#'+divid).html(result);
                    }
                }).fail(function(err) {
                    err = JSON.parse(JSON.stringify(err))['responseText'];
                    $('$idModal').html(`<div class='alert alert-danger'>`+err+`</div>`);
               });
            }

            function modalEzformMain(url, modal) {
                $('#'+modal+' .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
                $('#'+modal).modal('show')
                .find('.modal-content')
                .load(url);
            }
        }

        ");
// \backend\modules\ezforms2\classes\EzfStarterWidget::end() 
?>

