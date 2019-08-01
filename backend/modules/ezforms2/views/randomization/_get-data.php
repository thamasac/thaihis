<?php

use appxq\sdii\helpers\SDNoty;
use yii\helpers\Html;

$code_random = preg_split("/\r\n|\n|\r/", $dataCode['code_random']);
$html = '';
echo Html::beginTag('div', ['class' => 'table-responsive', 'style' => 'overflow-y:auto;max-height:350px;']);
echo Html::beginTag('table', ['class' => 'table table-hover', 'id' => 'tableRandom']);
echo Html::beginTag('thead');
echo Html::beginTag('tr');
echo Html::tag('th', 'Code', []);
echo Html::tag('th', 'Data ID', []);
echo Html::tag('th', '', []);
echo Html::endTag('tr');
echo Html::endTag('thead');
foreach ($dataRandom as $key => $value) {
    $code = explode(',', $code_random[$value['key']]);
    if (isset($code[$dataCode['code_index'] - 1])) {
        echo Html::beginTag('tbody');
        echo Html::beginTag('tr');
        echo Html::tag('td', $code[$dataCode['code_index'] - 1], []);
        echo Html::tag('td', $value['data_id'], []);
        echo Html::tag('td', Html::button('<span class="glyphicon glyphicon-eye-open"></span>', [
                    'title' => Yii::t('yii', 'View'),
                    'class' => 'btn btn-default btn-xs btnViewEzf',
                    'data-url' => '/ezforms2/ezform-data/ezform-view?ezf_id=' . $value['ezf_id'] . '&dataid=' . $value['data_id']
                        ]
                )
//        . ' ' . Html::button('<span class="glyphicon glyphicon-trash"></span>', [
//                    'data-url' => yii\helpers\Url::to(['/ezforms2/randomization/delete-randomcode',
//                        'id' => $value['id'],
//                        'data_id' => $value['data_id'],
//                        'ezf_id' => $value['ezf_id']
//                    ]),
//                    'data-action' => 'delete',
//                    'title' => Yii::t('yii', 'Delete'),
////                    'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
////                    'data-method' => 'post',
////                    'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
//                    'class' => 'btn btn-danger btn-xs btnDeleteData',
//                ])
        );

        echo Html::endTag('tr');
        echo Html::endTag('tbody');
    }
}
echo Html::endTag('table');
echo Html::endTag('div') . Html::tag('div', '', ['class' => 'clearfix']);

\backend\modules\ezforms2\assets\EzfGenAsset::register($this);
\backend\modules\ezforms2\assets\EzfColorInputAsset::register($this);
\backend\modules\ezforms2\assets\DadAsset::register($this);
\backend\modules\ezforms2\assets\EzfToolAsset::register($this);
\backend\modules\ezforms2\assets\EzfTopAsset::register($this);
\backend\modules\ezforms2\assets\ListdataAsset::register($this);

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

    $('.btnViewEzf').click(function () {
        $('#<?= $idModal ?> .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
        $('#<?= $idModal ?>').modal('show')
                .find('.modal-content')
                .load($(this).attr('data-url'));
    });

    $('.btnDeleteData').on('click', function () {
        var url = $(this).attr('data-url');
        yii.confirm('<?= Yii::t('app', 'Are you sure you want to delete this item?') ?>', function () {
            $.post(
                    url, {'_csrf': '" . Yii::$app->request->getCsrfToken() . "'}
            ).done(function (result) {
                if (result) {
<?= SDNoty::show("'" . Yii::t('ezform', 'Complete') . "'", '"success"') ?>
                    $.get('<?= $dataUrl ?>', {url: '<?= $dataUrl ?>', reloadDiv: '<?= $reloadDiv ?>'}, function (data) {
                        $('#<?= $reloadDiv ?>').html(data);
                    });
                } else {
<?= SDNoty::show("'" . Yii::t('ezform', 'Failed') . "'", '"error"') ?>
                }
            }).fail(function () {
<?= SDNoty::show('"Server Error"', '"error"') ?>
                console.log('server error');
            });
        });
        return false;
    }
    );
</script>
<?php
\richardfan\widget\JSRegister::end();



