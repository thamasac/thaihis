<?php
\backend\modules\ezforms2\assets\ListdataAsset::register($this);
\backend\modules\cpoe\assets\CpoeAsset::register($this);
$id = \appxq\sdii\utils\SDUtility::getMillisecTime();
//appxq\sdii\utils\VarDumper::dump($current_url);
?>
<section id="items-side-<?= $id ?>" class="items-sidebar navbar-collapse collapse" role="complementary" style="margin-left: 5px;width: 100%;position: unset;">
    <div class="sidebar-nav-title text-center"><?php
        echo \yii\helpers\Html::radioList('que_type', $que_type, ['1' => 'Que', '3' => 'All'], ['id' => 'que_type']);
        ?><hr/><i class="fa fa-user-circle-o"></i> <?= Yii::t('patient', 'Patients Queue') . " (" . $dataProviderQue->getCount() . ")" ?></div>
    <div id="items-side-scroll-<?= $id ?>" class="row">
        <div class="col-lg-12" id="que-list-view">   
            <?php
            echo \yii\widgets\ListView::widget([
                'id' => 'que-list',
                'dataProvider' => $dataProviderQue,
                'itemOptions' => ['tag' => false],
                'layout' => '<div class="list-group">{items}</div><div class="list-pager">{pager}</div>',
                'itemView' => function ($model) use($pt_id, $que_type,$image_field,$bdate_field, $modelFields,$template_custom,$current_url) {
                    return $this->render('_item_que', [
                        'model' => $model, 
                        'pt_id' => $pt_id, 
                        'que_type' => $que_type,
                        'ezf_id'=>$ezf_id,
                        'image_field'=>$image_field,
                        'bdate_field'=>$bdate_field,
                        'template_custom'=>$template_custom,
                        'modelFields'=> appxq\sdii\utils\SDUtility::array2String($modelFields),
                        'current_url' => $current_url
                    ]);
                },
            ]);
            ?>

        </div>
    </div>
</section>

<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);

//$url = yii\helpers\Url::to(['/cpoe/default/queue-view', 'ptid' => $pt_id, 'reloadDiv' => $reloadDiv]);
?>

<script>
    /* $('#items-side').on('click', '.list-group .item', function () {
     $('#items-side .list-group a').removeClass('active');
     $(this).addClass('active');
     $('#que-list-view').attr('data-keyselect', $(this).attr('data-key'));
     var url = $(this).attr('href');
     if (url) {
     $.get(url, {reloadDiv: 'cpoe-content'}).done(function (result) {
     $('#items-views .cpoe-content').html(result);
     }).fail(function () {
     console.log('server error');
     });
     }
     
     return false;
     });*/

    /*$('body').removeClass('page-sidebar-fixed page-sidebar-closed');
     $('.page-content').removeClass('page-container');
     $('#main-nav-app').removeClass('page-container');
     $('#slide-collapse').remove();*/

    $('#que_type').on('change', function () {
        let que_type = $('#que_type [type="radio"]:checked').val();
        let url = $('#<?=$reloadDiv?>').attr('data-url');
        $.get(url, {que_type: que_type}).done(function (result) {
            $('#<?= $reloadDiv ?>').html(result);
        }).fail(function () {
            console.log('server error');
        });
    });

    $('.footer').css('margin-left', '218px');
    itemsSidebar($('#items-side-scroll-<?= $id ?>'));
    $('#main-nav-app .navbar-header').append('<a class="a-collapse glyphicon glyphicon-th-list navbar-toggle" data-toggle="collapse" data-target="#items-side">&nbsp;</a>');

    function  getHeight() {
        var sidebarHeight = $(window).height() - 51; //- $('.header').height()
        if ($('body').hasClass("page-footer-fixed")) {
            sidebarHeight = sidebarHeight - $('.footer').height();
        }
        return sidebarHeight;
    }

    function  itemsSidebar(id) {
        var itemside = id;

        if ($(window).width() >= 350) {
            var sidebarHeight = getHeight();
            itemside.slimScroll({
                size: '7px',
                color: '#a1b2bd',
                opacity: .8,
                position: 'right',
                height: sidebarHeight / 2,
                //width: 250,
                allowPageScroll: false,
                disableFadeOut: false
            });
        } else {
            if (itemside.parent('.slimScrollDiv').length() === 1) {
                itemside.slimScroll({
                    destroy: true
                });
                itemside.removeAttr('style');
                $('#items-side-<?= $id ?>').removeAttr('style');
            }
        }

    }

    $(document).on('hidden.bs.modal', '.modal', function (e) {
        var hasmodal = $('body .modal').hasClass('in');
        if (hasmodal) {
            $('body').addClass('modal-open');
        }
    });
</script>
<?php \richardfan\widget\JSRegister::end(); ?>


