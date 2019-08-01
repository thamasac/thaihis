<!--<div class="card card-cpoe" style="margin-bottom: 0px">
    <div class="card-header">EMR/EHR</div>
</div>-->
<div id="visithos-side-scroll" class="row">
    <div class="col-lg-12">                                                       
        <div id="visithos-items">
            <?=
            \yii\widgets\ListView::widget([
                'id' => 'visithos-list',
                'dataProvider' => $dataProvider,
                'itemOptions' => ['tag' => false],
                'layout' => '<form id="form-visit-list"><div class="list-group"><div class="list-group-item list-cpoe-header"><strong>EMR/EHR</strong></div>'
                . '{items}</div></form><div class="list-pager">{pager}</div>',
                'itemView' => function ($model, $key, $index) use($dataid, $reloadChildDiv) {
                    return $this->render('_viewvisithos', [
                                'model' => $model,
                                'index' => $index,
                                'dataid' => $dataid,
                                'reloadChildDiv' => $reloadChildDiv
                    ]);
                },
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
$url = \yii\helpers\Url::to(['/patient/medical-history/visit', 'dataid' => $dataid]);
?>
<script>
    $('#visithos-list .list-group').on('change', 'input[type="checkbox"]', function () {
        $.post('<?= $url ?>', $('#form-visit-list').serialize()).done(function (result) {
            $('#<?= $reloadChildDiv ?>').html(result);
        }).fail(function () {
            console.log('server error');
        });
    });

    $('.footer').addClass('items-views');
    itemsSidebar();

    function  getHeight() {
        var sidebarHeight = $(window).height() - 51; //- $('.header').height()
        if ($('body').hasClass("page-footer-fixed")) {
            sidebarHeight = sidebarHeight - $('.footer').height();
        }
        return sidebarHeight;
    }

    function  itemsSidebar() {
        var itemside = $('#visithos-side-scroll');

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
            if (itemside.parent('.slimScrollDiv').length() === 1) {
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