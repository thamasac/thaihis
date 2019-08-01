<?php

use yii\helpers\Html;
use yii\helpers\Url;
use backend\modules\ezforms2\classes\EzfHelper;
use backend\modules\subjects\classes\SubjectManagementQuery;
use backend\modules\ezforms2\classes\EzfAuthFuncManage;
use appxq\sdii\widgets\ModalForm;
use backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\subjects\classes\JKDate;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$proposal_ezf_id = $options['proposal_ezf_id'];
$modal = 'modal-ezform-proposal';
$proposal_action = \appxq\sdii\utils\SDUtility::string2Array(Yii::$app->params['proposal_action']);
?>
<div class="modal-header">
    <h3>Proposal</h3>
</div>
<div class="modal-body">

    <?php
    if (EzfAuthFuncManage::auth()->accessBtn($module_id)) {
        echo \backend\modules\ezforms2\classes\EzfHelper::btn($proposal_ezf_id)
                ->reloadDiv('display-proposal')
                ->modal('modal-ezform-proposal')
                ->label('<i class="fa fa-plus"></i> ' . Yii::t('subjects', 'New Proposal'))
                ->target($data_id)
                ->options(['class' => 'btn btn-success pull-left', 'ezf_'])
                ->buildBtnAdd();
    }
    ?>
    <br/>
    <?php
    $columns = [
        [
            'class' => 'yii\grid\SerialColumn',
            'headerOptions' => ['style' => 'text-align: center;'],
            'contentOptions' => ['style' => 'min-width:60px;width:60px;text-align: center;'],
        ],
    ];
    if (EzfAuthFuncManage::auth()->accessBtn($module_id)) {
        $columns[] = [
            'class' => 'appxq\sdii\widgets\ActionColumn',
            'contentOptions' => ['style' => 'width:110px;min-width:110px;text-align: center;'],
            'template' => '{view} {update} {delete} ',
            'buttons' => [
                'view' => function ($url, $data, $key) use($proposal_ezf_id, $reloadDiv, $modal) {
                    //if (backend\modules\ezforms2\classes\EzfUiFunc::showViewDataEzf($data, Yii::$app->user->id, $data['user_create'])) {
                    //if (EzfAuthFuncManage::auth()->accessBtn($module_id)) {
                    return \backend\modules\ezforms2\classes\EzfHelper::btn($proposal_ezf_id)
                                    ->reloadDiv('display-detail')
                                    ->modal($modal)
                                    ->label('<i class="fa fa-eye"></i>')
                                    ->options(['class' => 'btn btn-default btn-xs'])
                                    ->reloadDiv($reloadDiv)
                                    ->buildBtnView($data['id']);
                    //}
                    //}
                },
                'update' => function ($url, $data, $key) use($proposal_ezf_id, $reloadDiv, $modal) {
                    //if (backend\modules\ezforms2\classes\EzfUiFunc::showEditDataEzf($data, Yii::$app->user->id, $data['user_create'])) {
                    //if (EzfAuthFuncManage::auth()->accessBtn($module_id)) {
                    return \backend\modules\ezforms2\classes\EzfHelper::btn($proposal_ezf_id)
                                    ->reloadDiv('display-detail')
                                    ->modal($modal)
                                    ->label('<i class="fa fa-pencil"></i>')
                                    ->options(['class' => 'btn btn-primary btn-xs btn-update-proposal'])
                                    ->reloadDiv($reloadDiv)
                                    ->buildBtnEdit($data['id']);
                    //}
                    //}
                },
                'delete' => function ($url, $data, $key) use($proposal_ezf_id, $reloadDiv, $modal) {
                    //if (backend\modules\ezforms2\classes\EzfUiFunc::showDeleteDataEzf($data, Yii::$app->user->id, $data['user_create'])) {
                    return \backend\modules\ezforms2\classes\EzfHelper::btn($proposal_ezf_id)
                                    ->reloadDiv('display-detail')
                                    ->label('<i class="fa fa-trash"></i>')
                                    ->options(['class' => 'btn btn-danger btn-xs'])
                                    ->reloadDiv($reloadDiv)
                                    ->buildBtnDelete($data['id']);
                    //}
                },
            ],
        ];
    }

    foreach ($display_column as $key => $value) {

        $columns[] = [
            'attribute' => $value,
            'format' => 'raw',
            'value' => function ($data) use($proposal_ezf_id, $value) {
                $field = EzfQuery::getFieldByName($proposal_ezf_id, $value);
                if ($field['ezf_field_type'] == '71') {
                    return Html::a('Download Proposal', backend\modules\manageproject\classes\CNImages::getStorageUrl() . '/ezform/fileinput/' . $data[$value], $options);
                } else {
                    return $data[$value];
                }
            },
            'headerOptions' => ['style' => 'text-align: center;'],
            'contentOptions' => ['style' => 'width:100px;text-align: center;'],
        ];
    }

    $columns[] = [
        'header' => 'Status',
        'format' => 'raw',
        'value' => function ($data) use($value) {
            $result = '';
            if ($data['rstat'] == '1') {
                $result = " <label class='label label-warning'>Save Draft</label> ";
            } else {
                $result = " <label class='label label-primary'>Submited</label> ";
            }
            return $result;
        },
        'headerOptions' => ['style' => 'text-align: center;'],
        'contentOptions' => ['style' => 'width:100px;text-align: center;'],
    ];

    $columns[] = [
        'header' => 'Latest action',
        'format' => 'raw',
        'value' => function ($data) use($proposal_action) {
            return $proposal_action[$data['action_status']];
        },
        'headerOptions' => ['style' => 'text-align: center;'],
        'contentOptions' => ['style' => 'width:100px;text-align: center;'],
    ];
    ?>
    <br/><br/>
    <?=
    yii\grid\GridView::widget([
        'id' => "$reloadDiv-profile-grid",
        'dataProvider' => $dataProvider,
        'filterModel' => isset($searchModel) ? $searchModel : null,
        'columns' => $columns,
    ]);
    ?>
    <br/><br/>
    <div id="display-detail" data-url="<?=
    Url::to([
        '/subjects/open-activity/grid-detail',
    ])
    ?>" ></div>

</div>
<div class="modal-footer">

</div>

<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>
<script>

    $(function () {

    })

    $('.btn-update-proposal').on('click', function () {
        setTimeout(function () {
            $(".btn-submit").on("click", function () {

            });
        }, 500);
    });

    $(document).on('beforeSubmit', '#ezform-<?= $proposal_ezf_id ?>', function () {
        var dataid = $(this).attr('data-dataid');
        var url = "/proposal/proposal/update-action";
        var data = {
            dataid: dataid,
        };
    });

</script>
<?php \richardfan\widget\JSRegister::end(); ?>