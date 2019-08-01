<?php

use yii\helpers\Html;

if ($have_modal != '1') {
    ?>
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4><strong> <?= isset($dataCode['name']) ? 'Random Name : ' . $dataCode['name'] : '' ?> </strong></h4>
    </div>
<?php }
?>

<div class="modal-body">
    <?php
    echo appxq\sdii\widgets\GridView::widget([
        'id' => 'grid-random',
        'dataProvider' => $dataProvider,
        'columns' => [
            'code_random',
                [
                'label' => 'Ezfrom Name',
                'format' => 'raw',
                'value' => function($data) {
                    $ezf = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($data['ezf_id']);
                    return $ezf['ezf_name'];
                }
            ],
                [
                'label' => 'Data ID',
                'format' => 'raw',
                'value' => function($data) {
                    $html = '';
                    $html2 = '';
                    if ($data['data_id'] != '') {
                        $arrData = explode("|", $data['data_id']);

                        if ($arrData[0] != '') {
                            foreach ($arrData as $valData) {
                                $valDataSub = explode(",", $valData);
                                if (!empty($valDataSub)) {
                                    if ($valDataSub[0] == Yii::$app->user->identity->profile->sitecode) {
                                        $html2 = "<div class='label label-primary'>".$valDataSub[1].'</div><hr/>';
                                        $html .= $html2.'<hr/>';
                                    } else if (backend\modules\ezforms2\classes\RandomizationFunc::authAdmin()) {
                                        $html .= "<div class='label label-primary'>".$valDataSub[1].'</div><hr/>';
                                    }
                                }
                            }
                        }
                    }
                    $html = substr($html, 0, strlen($html) - 5);
                    return backend\modules\ezforms2\classes\RandomizationFunc::authAdmin() ? $html : $html2;
                }
            ],
                [
                'label' => 'Time',
                'format' => 'raw',
                'value' => function($data) {
                    $html = '';
                    $html2 = '';
                    if ($data['data_id'] != '') {
                        $arrData = explode("|", $data['data_id']);

                        if ($arrData[0] != '') {
                            foreach ($arrData as $valData) {
                                $valDataSub = explode(",", $valData);
                                if (!empty($valDataSub)) {
                                    if ($valDataSub[1] != '') {
                                        $ezfTb = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($data['ezf_id']);
                                        $tbAll = new \backend\modules\ezforms2\models\TbdataAll();
                                        $tbAll->setTableName($ezfTb['ezf_table']);
                                        $dataEzf = backend\modules\ezforms2\classes\EzfUiFunc::loadData($tbAll, $ezfTb['ezf_table'], $valDataSub[1]);
                                        if ($valDataSub[0] == Yii::$app->user->identity->profile->sitecode) {
                                            $html2 = "<div class='label label-warning'>".$dataEzf['update_date'].'</div><hr/>';
                                            $html .= $html2.'<hr/>';
                                        } else if (backend\modules\ezforms2\classes\RandomizationFunc::authAdmin()) {
                                            $html .= "<div class='label label-warning'>".$dataEzf['update_date'].'</div><hr/>';
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $html = substr($html, 0, strlen($html) - 5);
                    return backend\modules\ezforms2\classes\RandomizationFunc::authAdmin() ? $html : $html2;
                }
            ],
                [
                'label' => '',
                'format' => 'raw',
                'value' => function($data) {
                    $html = '';
                    $html2 = '';
                    if ($data['data_id'] != '') {
                        $arrData = explode("|", $data['data_id']);

                        if ($arrData[0] != '') {
                            foreach ($arrData as $valData) {
                                $valDataSub = explode(",", $valData);
                                if (!empty($valDataSub)) {
                                    if ($valDataSub[0] == Yii::$app->user->identity->profile->sitecode) {
                                        $html2 = $valDataSub[1] != '' ? Html::button('<span class="glyphicon glyphicon-eye-open"></span> ' . $valDataSub[0], [
                                                    'title' => Yii::t('yii', 'View'),
                                                    'class' => 'btn btn-default btn-xs btnViewEzf',
                                                    'data-url' => '/ezforms2/ezform-data/ezform-view?ezf_id=' . $data['ezf_id'] . '&dataid=' . $valDataSub[1]
                                                        ]
                                                ) . '<hr/>' : ' ';
                                        $html .= $html2.'<hr/>';
                                    } else if (backend\modules\ezforms2\classes\RandomizationFunc::authAdmin()) {
                                        $html .= $valDataSub[1] != '' ? Html::button('<span class="glyphicon glyphicon-eye-open"></span> ' . $valDataSub[0], [
                                                    'title' => Yii::t('yii', 'View'),
                                                    'class' => 'btn btn-default btn-xs btnViewEzf',
                                                    'data-url' => '/ezforms2/ezform-data/ezform-view?ezf_id=' . $data['ezf_id'] . '&dataid=' . $valDataSub[1]
                                                        ]
                                                ) . '<hr/>' : ' ';
                                    }
                                }
                            }
                        }
                    }
                    $html = substr($html, 0, strlen($html) - 5);
                    return backend\modules\ezforms2\classes\RandomizationFunc::authAdmin() ? $html : $html2;
                }
            ]
        ]
    ]);
    ?>
    <div id="displayData"></div>
</div>
<?php if ($have_modal != '1') { ?>
    <div class="modal-footer">
        <?= Html::button('<i class="glyphicon glyphicon-remove"></i> ' . Yii::t('app', 'Cancel'), ['class' => 'btn btn-default', 'data-dismiss' => 'modal']) ?>    
    </div>

    <?php
}
$idModal = 'modal-view-ezf';
$modal = '<div id="' . $idModal . '" class="fade modal" role="dialog"><div class="modal-dialog modal-xxl"><div class="modal-content"></div></div></div>';
richardfan\widget\JSRegister::begin();
?>
<script>
    var hasMyModal = $('body').has('#<?= $idModal ?>').length;
    if (!hasMyModal) {
        $('.sdbox').append('<?= $modal ?>');
    }

    $('#<?= $idModal ?>').on('hidden.bs.modal', function (e) {
        $('#<?= $idModal ?> modal-content').html('');
        if ($('body .modal').hasClass('in')) {
            $('body').addClass('modal-open');
        }
    });

    $('#grid-random .pagination a').on('click', function () {
        modalRandomCode($(this).attr('href'));
        return false;
    });

    $('.btnViewEzf').click(function () {
        $('#<?= $idModal ?> .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
        $('#<?= $idModal ?>').modal('show')
                .find('.modal-content')
                .load($(this).attr('data-url'));
    });

    $('.tabHeader').click(function () {
        var dataUrl = $(this).attr('data-url');
        $.get(dataUrl, {url: dataUrl, reloadDiv: 'displayData'}, function (data) {
            $('#displayData').html(data);
        });
    });

    function modalRandomCode(url) {
//        $('#modal-random-code .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
        $.get(url,function(data){
            $('#data').html(data);
        });
//         $('#modal-random-code').modal('show')
//                 .find('.modal-content')
//                 .load(url);
    }
</script>
<?php
richardfan\widget\JSRegister::end();

