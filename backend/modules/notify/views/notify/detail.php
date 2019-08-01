<?php

use yii\helpers\Html;
use yii\helpers\Url;
use appxq\sdii\helpers\SDNoty;
?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    <h4 class="modal-title"><?= Yii::t('notify', 'Notification') ?></h4>

</div>
<div class="modal-body">
    <?=
    \yii\widgets\DetailView::widget([
        'id' => appxq\sdii\utils\SDUtility::getMillisecTime(),
        'model' => $data,
        'attributes' => [
                [
                'format' => 'raw',
                'label' => Yii::t('ezform', 'Title'),
                'value' => $data['notify']
            ],
                [
                'format' => 'raw',
                'label' => Yii::t('ezform', 'Detail'),
                'value' => $data['detail']
            ],
                [
                'format' => 'html',
                'label' => Yii::t('notify', 'Sender'),
                'value' => isset($dataUser) ? Html::tag('div',$dataUser['firstname'] . " " . $dataUser['lastname'],['class'=>'label label-primary']) : ''
            ],
        ],
    ]);
    ?>

</div>
<div class="modal-footer">

    <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">
        <?= Yii::t('app', 'Close') ?>
    </button>
    <?php
    $view = $data['readonly'] ? 'ezform-view' : 'ezform';

    if ($data['type_link'] == '1') {
        echo Html::button(Yii::t('notify', 'Go to Link'), [
            'data-url' => $data['url'],
            'title' => Yii::t('yii', 'View'),
            'data-action' => 'redirect',
            'data-view' => $data['status_view'],
            'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
            'class' => 'btn btn-primary btnViewNotify',
        ]);
    } else if ($data['type_link'] == '2') {
        echo Html::button(Yii::t('ezform', 'Open Form'), [
            'data-url' => Url::to(['/ezforms2/ezform-data/' . $view,
                'ezf_id' => $data['ezf_id'],
                'dataid' => $data['data_id'],
                'id' => $data['id'],
                'target' => $data['data_target'],
                'modal' => $modal,
            ]),
            'data-id' => $data['id'],
            'data-complete' => $data['complete_date'] != '' ? '1' : '0',
            'data-button' => $data['action'],
            'data-ezf_id' => '1520530564093708000',
            'data-tb' => $data['ezf_id'],
            'data-form' => $data['data_id'],
            'data-view' => $data['status_view'],
            'data-action' => 'view',
            'title' => Yii::t('yii', 'View'),
            'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
            'class' => 'btn btn-primary btnViewNotify',
        ]);
    }
    ?>

</div>

<?php
richardfan\widget\JSRegister::begin();
?>
<script>
    $('.btnViewNotify').on('click', function () {

        var url = $(this).attr('data-url');
        var action = $(this).attr('data-action');
        if (action === 'view') {
            var btn = '';
            var data_id = $(this).attr('data-id');
            var data_complete = $(this).attr('data-complete');
            var data_btn = $(this).attr('data-button');
            var data_tb = $(this).attr('data-tb');
            var data_form = $(this).attr('data-form');
            var ezf_id = $(this).attr('data-ezf_id');
            var url_data = '/notify/notify/grid-complete?modal=$modal&reloadDiv=grid-complete&ezf_id=' + ezf_id + '&data_form=' + data_form + '&data-ezf_id=' + data_tb;
            $('#<?=$modal?>').modal('hide');
            $('#<?= $sub_modal ?> .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
            $('#<?= $sub_modal ?>').modal('show');
            $.ajax({
                method: 'POST',
                url: url,
                dataType: 'HTML',
                success: function (result, textStatus) {
                    $('#<?= $sub_modal ?>').find('.modal-content').html(result);
                    if (data_btn != '') {
                        if (data_btn == 'Acknowledge') {
                            btn = '<button data-value=\'1\' class=\'btn btn-success btnStatus pull-right\'>Acknowledge</button>'
                        } else if (data_btn == 'Review') {
                            btn = '<button data-value=\'1\' class=\'btn btn-success btnStatus pull-right\'>Review</button>'
                        } else if (data_btn == 'Approve') {
                            btn = '<button data-value=\'2\' class=\'btn btn-success approve pull-right\'>Approve</button> <button data-value=\'3\' style=\'margin-right:5px;\' class=\'btn btn-danger pull-right approve\'> Not Approve</button>'
                        }
                        if (data_complete == '0') {
                            $('#<?= $sub_modal ?>').find('#form-' + data_tb).append(btn + '<div class=\'clearfix\'></div><hr/><div data-url=' + url_data + ' id=\'grid-complete\'><div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div></div>');
                        } else {
                            $('#<?= $sub_modal ?>').find('#form-' + data_tb).append('<div data-url=' + url_data + ' id=\'grid-complete\'><div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div></div>');
                        }

                        $('.btnStatus').on('click', function () {
                            UpdateResult(data_form, data_tb, $(this).attr('data-value'), $(this), data_id, ezf_id);
                        });
                        $('.approve').on('click', function () {
                            UpdateResult(data_form, data_tb, $(this).attr('data-value'), $(this), data_id, ezf_id);
                        });
                        getUiAjax(url_data, 'grid-complete');
                    }
                }
            });
            return false;
        } else if (action === 'redirect') {
            window.open(url);
            return false;
        }
    });
    function UpdateResult(id = '', ezf_id = '', value = '', btn, id_notify, ezf_notify) {

        $.ajax({
            method: 'POST',
            url: '/tmf/tmf/update-result',
            dataType: 'HTML',
            data: {
                ezf_id: ezf_id,
                id: id,
                value: value
            },
            success: function (result, textStatus) {
                var url = $('#<?= $reloadDiv ?>').attr('data-url');
                if (result) {
<?= SDNoty::show('"' . Yii::t('ezform', 'Complete') . '"', '"success"') ?>
                    getUiAjax(url, '<?= $reloadDiv ?>');
                    btn.hide();
                    $('.approve').hide()
                    UpdateNotify(id_notify, ezf_notify);
                    getUiAjax($('#grid-complete').attr('data-url'), 'grid-complete');
                    $('#<?= $modal ?>').find('.modal-content').load('/notify/notify/detail?id=<?= $data['id'] ?>&modal=<?= $modal ?>&sub_modal=<?= $sub_modal ?>');
                } else {
<?= SDNoty::show(Yii::t('ezform', 'Failed'), '"error"') ?>
//                    btn.hide();
                }
            }
        });
    }

    function UpdateNotify(id = '', ezf_id = '') {

        $.ajax({
            method: 'POST',
            url: '/notify/notify/update-result',
            dataType: 'HTML',
            data: {
                ezf_id: ezf_id,
                id: id,
            },
            success: function (result, textStatus) {
            }
        });
    }
    function getUiAjax(url, divid, tab = '') {
//    $('#'+divid).html('<div class=\"sdloader\"><i class=\"sdloader-icon\"></i></div>');
        $.ajax({
            method: 'GET',
            url: url,
            data: {tab: tab},
            dataType: 'HTML',
            success: function (result, textStatus) {
                $('#' + divid).html(result);
            }
        }).fail(function (err) {
            err = JSON.parse(JSON.stringify(err))['responseText'];
            $('#' + divid).html(`<div class='alert alert-danger'>` + err + `</div>`);
        });
    }
</script>

<?php
richardfan\widget\JSRegister::end();
