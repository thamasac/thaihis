<div id="visit-side-scroll" class="row">
    <div class="col-lg-12">                                                       
        <div id="visit-items">
            <?=
            \yii\widgets\ListView::widget([
                'id' => 'visit-list',
                'dataProvider' => $dataProvider,
                'itemOptions' => ['tag' => false],
                'layout' => '<div class="list-group"><div class="list-group-item list-cpoe-header"><strong>' . Yii::t('patient', 'Date') . '</strong></div>'
                . '{items}</div><div class="list-pager">{pager}</div>',
                'itemView' => function ($model, $key, $index)use($dataid, $reloadChildDiv) {
                    return $this->render('_viewvisit', [
                                'model' => $model,
                                'index' => $index,
                                'dataid' => $dataid,
                                'reloadChildDiv' => $reloadChildDiv
                    ]);
                },
                'showOnEmpty' => false,
                'emptyText' => "<div class=\"list-group\"><div class=\"list-group-item list-cpoe-header\"><strong>" . Yii::t('patient', 'Date')
                . "</strong></div><div class=\"list-group-item\"><span><i class=\"fa \"></i> ไม่พบผลลัพธ์</span></div>
                    <script>$('#{$reloadChildDiv}').html('');</script>",
            ])
            ?>
        </div>
    </div>
</div>
<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>
<script>
    $('#visit-list .list-group').on('click', 'a', function () {
        $('#visit-list .list-group a').removeClass('active');
        $(this).addClass('active');
        var url = $(this).attr('href');
        if (url) {
            $.get(url).done(function (result) {
                $('#<?= $reloadChildDiv ?>').html(result);
            }).fail(function () {
                console.log('server error');
            });
        }
        return false;
    });

    itemsSidebar();

    function  getHeight() {
        var sidebarHeight = $(window).height() - 51; //- $('.header').height()
        if ($('body').hasClass("page-footer-fixed")) {
            sidebarHeight = sidebarHeight - $('.footer').height();
        }
        return sidebarHeight;
    }

    function  itemsSidebar() {
        var itemside = $('#visit-side-scroll');

        if ($('.page-sidebar-fixed').length === 0) {
            return;
        }

        if ($(window).width() >= 992) {
            var sidebarHeight = getHeight();

            itemside.slimScroll({
                size: '7px',
                color: '#a1b2bd',
                opacity: .8,
                position: 'right',
                height: sidebarHeight,
                allowPageScroll: false,
                disableFadeOut: false
            });
        } else {
            if (itemside.parent('.slimScrollDiv').length === 1) {
                itemside.slimScroll({
                    destroy: true
                });
                itemside.removeAttr('style');
                $('.items-sidebar').removeAttr('style');
            }
        }

    }

</script>
<?php \richardfan\widget\JSRegister::end(); ?>