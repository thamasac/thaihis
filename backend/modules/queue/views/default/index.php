<?php
\backend\modules\ezforms2\assets\ListdataAsset::register($this);
$id = \appxq\sdii\utils\SDUtility::getMillisecTime();
$position_type = isset($position['position_type']) ? $position['position_type'] : '1';
$height_static = isset($position['height_static']) ? $position['height_static'] : '100';
$fixed_position = isset($position['fixed_position']) ? $position['fixed_position'] : 1;
$page = Yii::$app->request->get('page', '');
//$check_search = 1;

if (!isset(Yii::$app->session['ezf_input'])) {
    Yii::$app->session['ezf_input'] = backend\modules\ezforms2\classes\EzfQuery::getInputv2All();
}
$width = isset($position['width']) ? $position['width'] : 350;
?>
<div id="main-list-<?= $id ?>" style="<?= $check_search == '' ? 'visibility: hidden' : '' ?>">
    <section id="items-side-<?= $id ?>" class="items-sidebar navbar-collapse collapse" role="complementary"
             style="<?= $position_type == '1' ? 'margin-left: 5px;width: 100%;position: unset;' : '' ?>">
        <div id="items-side-scroll-<?= $id ?>" class="row">
            <div class="col-lg-12" id="que-list-view">
                <div class="sidebar-nav-title text-center">
                    <?php

                    echo !$radio_check && $dept_field ? "<div style='margin-top: 2%'>" . \yii\helpers\Html::radioList('que_type-' . $id, $que_type, ['1' => 'Que', '3' => 'All'], ['id' => 'que_type-' . $id, 'class' => 'que_type']) . '</div>' : '';
                    echo $whereSearchOne != '' ? '<div style=\'margin-top: 2%\'>' . yii\helpers\Html::textInput('searchBoxOne', $searchBoxOne, ['class' => 'form-control searchBoxOne', 'placeholder' => $txtSearchOne, 'id' => 'searchBoxOne-' . $id]) . '</div>' : '';
                    $vParamSeach = '';
                    $searchBoxOne != '' ? $vParamSeach .= '&searchBoxOne=' . $searchBoxOne : null;
                    if (isset($fields_search_multi) && is_array($fields_search_multi)) {
                        foreach ($fields_search_multi as $k => $field) {
                            if (isset($modelFieldsSearchMulti) && is_array($modelFieldsSearchMulti)) {
                                foreach ($modelFieldsSearchMulti as $key => $value) {
                                    if ($field == $value['ezf_field_id']) {
                                        $fieldName = $value['ezf_field_name'];
                                        $var = $value['ezf_field_name'];
                                        $label = $value['ezf_field_label'];
                                        $vSearch = isset($search_field[$fieldName]) ? $search_field[$fieldName] : '';
                                        $vSearch != '' ? $vParamSeach .= '&search_field[' . $var . ']=' . $vSearch : null;
                                        $dataInput = [];
                                        if (isset(Yii::$app->session['ezf_input'])) {
                                            $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($value['ezf_field_type'], backend\modules\ezforms2\models\EzformInput::find()->all());
                                        }
                                        echo '<div style=\'margin-top: 2%\'>' . backend\modules\queue\classes\QueueFunc::htmlFilter($value, $dataInput, $var, $vSearch) . '</div>';
                                    }
                                }
                            }
                        }
                    }
                    ?>

                    <div style='margin-top: 2%'> <?= isset($icon) && $icon != '' ? ' <i class="fa ' . $icon . '"></i> ' : '' ?><?= isset($title) && $title != '' ? $title : Yii::t('patient', 'Patients Queue') ?><?= isset($count_queue) ? " (" . $count_queue . ")" : '(0)' ?>  <?= isset($btn_report) && $btn_report ? \yii\helpers\Html::button('<i class="fa fa-print"></i> Report', ['class' => 'btn btn-warning btn-xs', 'title' => 'Print', 'id' => 'btn-report-que-' . $id]) : '' ?></div>
                </div>
                <?php
                echo \yii\widgets\ListView::widget([
                    'id' => 'que-list-' . $id,
                    'dataProvider' => $dataProviderQue,
                    'itemOptions' => ['tag' => false],
                    'layout' => '<div class="list-group">{items}</div><div class="list-pager">{pager}</div>',
                    'itemView' => function ($model) use (
                        $ezf_main_id, $modelFields, $reloadDiv, $status_field
                        , $dept_field, $bdate_field, $pic_field, $template_content, $que_type, $target, $current_url
                        , $action, $data_columns, $param, $custom_label, $vParamSeach, $params_value
                    ) {

                        return $this->render('_item_que', [
                            'ezf_main_id' => $ezf_main_id,
                            'model' => $model,
                            'status_field' => $status_field,
                            'dept_field' => $dept_field,
                            'bdate_field' => $bdate_field,
                            'pic_field' => $pic_field,
                            'template_custom' => $template_content,
                            'que_type' => $que_type,
                            'target' => $target,
                            'current_url' => $current_url,
                            'action' => $action,
                            'data_columns' => $data_columns,
                            'modelFields' => $modelFields,
                            'param' => $param,
                            'custom_label' => $custom_label,
                            'reloadDiv' => $reloadDiv,
                            'vParamSeach' => $vParamSeach,
                            'params_value' => $params_value
                        ]);
                    },
                ]);
                ?>

            </div>
        </div>
    </section>

</div>
<?php
$modalID = 'modal-sub-' . $reloadDiv;
$submodal = '<div id="' . $modalID . '" class="fade modal" role="dialog"><div class="modal-dialog modal-xxl"><div class="modal-content"></div></div></div>';
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
//    'position' => \yii\web\View::POS_HEAD
]);
?>

<script>
    try {
        function onLoadBlock(ele) {
            $(ele).waitMe({
                effect: 'facebook',
                text: 'Please wait...',
                bg: 'rgba(255,255,255,0.8)',
                color: '#000',
                maxSize: '',
                waitTime: -1,
                textPos: 'vertical',
                fontSize: '',
                source: '',
                onClose: function () {
                }
            });
        }

        function hideLoadBlock(ele) {
            $(ele).waitMe("hide");
        }
    } catch (e) {
        console.log(e);
    }

    try {
        if ('<?= $position_type ?>' == '2') {
            var screenHeight = $('#slide-collapse').height() - 20;
            var screenWidth = $('.sdbox').width();
            // alert(screenWidth);
            var scrollHeight = $('#items-side-scroll-<?= $id ?>').offset().top;
            var heightFix = <?= $fixed_position == 1 || $fixed_position == 2 ? 1 : 2 ?>;
            var items_side = $('#items-side-<?= $id ?>');
            var items_side_scroll = $('#items-side-scroll-<?= $id ?>');
            var slim_scroll = $('#<?= $reloadDiv ?> .slimScrollDiv');
            var nav_title = $('#<?= $reloadDiv ?> .sidebar-nav-title');
            var que_list = $('#<?= $reloadDiv ?> #que-list-<?= $id ?>');
            var page_column = $('.page-column');
//        if (heightFix == 2) {
            if ('<?= $fixed_position ?>' == '1' || '<?= $fixed_position ?>' == '3') {
                var i = '<?= $fixed_position ?>' == '3' ? 0 : 35;
                if (!page_column.hasClass('items-views')) {
                    page_column.addClass('items-views');
                }
                page_column.css('margin-left', <?= $width ?>);
                items_side_scroll.css({
                    'height': screenHeight / heightFix - i,
                    'max-height': screenHeight / heightFix - i
                });
            } else if ('<?= $fixed_position ?>' == '2') {
                page_column.css('margin-right', <?= $width ?>);
                items_side.css('margin-left', screenWidth - 220);
                items_side_scroll.css({
                    'height': screenHeight / heightFix - 35,
                    'max-height': screenHeight / heightFix - 35
                });
            } else if ('<?= $fixed_position ?>' == '4') {
                if (!page_column.hasClass('items-views')) {
                    page_column.addClass('items-views');
                }
                items_side.css('margin-top', screenHeight / heightFix + 10);
                items_side_scroll.css({
                    'height': screenHeight / heightFix - 35,
                    'max-height': screenHeight / heightFix - 35
                });
            } else if ('<?= $fixed_position ?>' == '5') {
                page_column.css('margin-right', <?= $width ?>);
                items_side.css('margin-left', screenWidth - 220);
                items_side_scroll.css({'height': screenHeight / heightFix, 'max-height': screenHeight / heightFix});
            } else if ('<?= $fixed_position ?>' == '6') {
                page_column.css('margin-right', <?= $width ?>);
                items_side_scroll.css({
                    'height': screenHeight / heightFix - 35,
                    'max-height': screenHeight / heightFix - 35
                });
            }
            nav_title.css({'position': 'fixed', 'z-index': '1200', 'width': '<?= $width ?>' + 'px'});
            que_list.css({'margin-top': nav_title.height() + 7});
            items_side.css({'max-height': screenHeight / heightFix, 'width': '<?= $width ?>' + 'px'});
            slim_scroll.css({'height': (screenHeight / heightFix), 'max-height': (screenHeight / heightFix)});
//        } else {
//            if (!page_column.hasClass('items-views')) {
//                page_column.addClass('items-views');
//            }
//            nav_title.css({'position': 'fixed', 'z-index': '1200', 'width': '350px'});
//            que_list.css({'margin-top': nav_title.height() + 5});
//            items_side_scroll.css({'height': screen.height - 200, 'min-height': screen.height - 200});
//            slim_scroll.css({'height': screen.height - 200, 'min-height': screen.height - 200});
//            items_side.css('max-height', screen.height - 200);
//        }
            setTimeout(() => {
                $('#main-list-<?= $id ?>').css('visibility', 'visible')
            }, 1500);
        } else {
            $('#items-side-<?= $id ?>').show().css('max-height', '<?= $height_static ?>%');
            $('#items-side-scroll-<?= $id ?>').css('max-height', '<?= $height_static ?>%');
            $('#<?= $reloadDiv ?> .slimScrollDiv').css('max-height', '<?= $height_static ?>%');
            $('#items-side-scroll-<?= $id ?>').css('height', '<?= $height_static ?>%');
            $('#<?= $reloadDiv ?> .slimScrollDiv').css('height', '<?= $height_static ?>%');
            setTimeout(() => {
                $('#main-list-<?= $id ?>').css('visibility', 'visible')
            }, 1500);
        }


        var hasMyModal = $('body').has('#<?= $modalID ?>').length;
        if (!hasMyModal) {
            $('#ezf-modal-box').append('<?= $submodal ?>');
        }
        $('#<?= $modalID ?>').on('hidden.bs.modal', function (e) {
            $('#<?= $modalID ?> .modal-content').html('');
        });

        $('#<?= $reloadDiv ?> #items-side-scroll-<?= $id ?> .list-group-item').click(function () {
            var url = $(this).attr('href');
            if ('<?= $action ?>' == '1') {
                window.location = url;
            } else if ('<?= $action ?>' == '2') {
                $('#<?= $modalID ?> .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
                $('#<?= $modalID ?>').modal('show')
                    .find('.modal-content')
                    .load(url);
            } else if ('<?= $action ?>' == '3') {
                $.get(url, function (data) {
                    $('#<?= $element_id ?>').html(data);
                });
            }
            return false;
        });
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

        $('#<?= $reloadDiv ?> .que_type').change(function () {
            let que_type = $('.que_type [type="radio"]:checked').val();
            if (typeof(que_type) == "undefined") {
                que_type = '<?=$que_type?>';
            }
            let url = $('#<?= $reloadDiv ?>').attr('data-url');
            onLoadBlock('body');
            $.get(url, {que_type: que_type, check_search: 1}).done(function (result) {
                $('#<?= $reloadDiv ?>').html(result);
                hideLoadBlock('body');
            }).fail(function () {
                console.log('server error');
                hideLoadBlock('body');
            });
            return false;
        });

        $('#<?= $reloadDiv ?> .footer').css('margin-left', '218px');
        itemsSidebar($('#<?= $reloadDiv ?> #items-side-scroll-<?= $id ?>'));
        $('#<?= $reloadDiv ?> #main-nav-app .navbar-header').append('<a class="a-collapse glyphicon glyphicon-th-list navbar-toggle" data-toggle="collapse" data-target="#items-side">&nbsp;</a>');

        function getHeight() {
            var sidebarHeight = $(window).height() - 51; //- $('.header').height()
            if ($('body').hasClass("page-footer-fixed")) {
                sidebarHeight = sidebarHeight - $('.footer').height();
            }
            return sidebarHeight;
        }

        function itemsSidebar(id) {
            var itemside = id;

            if ($(window).width() >= 350) {
                var sidebarHeight = getHeight();
                itemside.slimScroll({
                    size: '7px',
                    color: '#a1b2bd',
                    opacity: .8,
                    position: 'right',
                    height: sidebarHeight / <?= $fixed_position == 1 || $fixed_position == 2 ? 1 : 2 ?>,
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
                    $('#<?= $reloadDiv ?> #items-side-<?= $id ?>').removeAttr('style');
                }
            }

        }

        //    $('#searchBox').unbind().keyup(function () {
        //        var value = $(this).val();
        //        var url = $('#<?= $reloadDiv ?>').attr('data-url');
        ////        if (value.length > 3) {
        //        getUiAjax(url + '&searchBox=' + value, '<?= $reloadDiv ?>');
        ////        }
        //    });


        $('#<?= $reloadDiv ?> .searchBoxOne').change(function (event) {
            console.log('search');
            var value = $(this).val();
            var que_type = $('#<?= $reloadDiv ?> .que_type [type="radio"]:checked').val();
            if (typeof(que_type) == "undefined") {
                que_type = '<?=$que_type?>';
            }
            var url = $('#<?= $reloadDiv ?>').attr('data-url');
            $('#<?= $reloadDiv ?> .search-input').each(function (k, v) {
                url += '&' + $(this).attr('name') + '=' + $(this).val();
            });
            onLoadBlock('body');
            getUiAjax(url + '&searchBoxOne=' + value + '&check_search=1&que_type=' + que_type, '<?= $reloadDiv ?>', {});
            return false;
        });
        $('#que-list-<?= $id ?> .pagination a').on('click', function () {
            var value = $('#<?= $reloadDiv ?> .searchBoxOne').val();
            var que_type = $('#<?= $reloadDiv ?> .que_type [type="radio"]:checked').val();
            if (typeof(que_type) == "undefined") {
                que_type = '<?=$que_type?>';
            }
            var url = $(this).attr('href');
            $('#<?= $reloadDiv ?> .search-input').each(function (k, v) {
                url += '&' + $(this).attr('name') + '=' + $(this).val();
            });
            onLoadBlock('body');
            getUiAjax(url + '&searchBoxOne=' + value + '&check_search=1&que_type=' + que_type, '<?= $reloadDiv ?>', {});
            return false;
        });

        $('#<?= $reloadDiv ?> .search-input').change(function (event) {

            $('#<?= $clearDiv ?>').html(""); //ClearDiv ByOak
            var value = $('#<?= $reloadDiv ?> .searchBoxOne').val();
            var que_type = $('#<?= $reloadDiv ?> .que_type [type="radio"]:checked').val();
            if (typeof(que_type) == "undefined") {
                que_type = '<?=$que_type?>';
            }
            var url = $('#<?= $reloadDiv ?>').attr('data-url');
            $('#<?= $reloadDiv ?> .search-input').each(function (k, v) {
                url += '&' + $(this).attr('name') + '=' + $(this).val();
            });

            onLoadBlock('body');
            getUiAjax(url + '&searchBoxOne=' + value + '&check_search=1&que_type=' + que_type, '<?= $reloadDiv ?>', {});
            return false;
        });

        $('#btn-report-que-<?=$id?>').click(function () {
            var value = $('#<?= $reloadDiv ?> .searchBoxOne').val();
            var que_type = $('#<?= $reloadDiv ?> .que_type [type="radio"]:checked').val();
            if (typeof(que_type) == "undefined") {
                que_type = '<?=$que_type?>';
            }
            var url = $('#<?= $reloadDiv ?>').attr('data-url');
            $('#<?= $reloadDiv ?> .search-input').each(function (k, v) {
                url += '&' + $(this).attr('name') + '=' + $(this).val();
            });
            window.open(url + '&searchBoxOne=' + value + '&check_search=1&que_type=' + que_type+ '&report=1');
        });

        $(document).on('hidden.bs.modal', '.modal', function (e) {
            var hasmodal = $('body .modal').hasClass('in');
            if (hasmodal) {
                $('body').addClass('modal-open');
            }
        });

        function getUiAjax(url, divid, data = {}) {
            $.ajax({
                method: 'GET',
                url: url,
                data: data,
                dataType: 'HTML',
                success: function (result, textStatus) {
                    hideLoadBlock('body');
                    $('#' + divid).html(result);
                }
            }).fail(function (err) {
                hideLoadBlock('body');
                // err = JSON.parse(JSON.stringify(err))['responseText'];
                $('#' + divid).html(`<div class='alert alert-danger'>Server Error</div>`);
            });
        }

    } catch (e) {
        console.log(e);
        hideLoadBlock('body');
    }


</script>
<?php \richardfan\widget\JSRegister::end(); ?>



