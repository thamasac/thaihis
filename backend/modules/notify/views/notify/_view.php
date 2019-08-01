<?php

use yii\helpers\Html;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use yii\helpers\Url;

$columns = [];
if($tab == 'to_me' ){
    $columns = [
        [
            'class' => 'yii\grid\CheckboxColumn',
            'checkboxOptions' => [
                'class' => 'selectionSystemNotifyIds'
            ],
            'headerOptions' => ['style' => 'text-align: center;'],
            'contentOptions' => ['style' => 'width:40px;text-align: center;'],
        ],
        [
            'class' => 'yii\grid\SerialColumn',
            'headerOptions' => ['style' => 'text-align: center;'],
            'contentOptions' => ['style' => 'min-width:60px;width:60px;text-align: center;'],
        ],
    ];
}


if ($tab == 'to_me' || $tab == 'all') {
    $modelFields = \backend\modules\ezforms2\models\EzformFields::find()
        ->where('ezf_id = :ezf_id', [':ezf_id' => $ezform->ezf_id])
        ->orderBy(['ezf_field_order' => SORT_ASC])
        ->all();

    $fieldsGroup = \backend\modules\ezforms2\classes\EzfFunc::getFieldsGroup($modelFields, $ezform->ezf_version);
    if (!isset(Yii::$app->session['ezf_input'])) {
        Yii::$app->session['ezf_input'] = backend\modules\ezforms2\classes\EzfQuery::getInputv2All();
    }


//yii\helpers\VarDumper::dump($dataProvider->getModels());
    foreach ($data_column as $field) {

        $fieldName = $field;
        if (is_array($field) && isset($field['attribute'])) {
            $fieldName = $field['attribute'];
        }

        $changeField = TRUE;
        foreach ($fieldsGroup as $key => $value) {
            $var = $value['ezf_field_name'];
            $label = $value['ezf_field_label'];

            if ($fieldName == $var && $var != 'readonly' && $var != 'readonly' && $var != 'ezf_id' && $var != 'data_id' && $var != 'module_id' && $var != 'status_view' && $var != 'assign_to') {
                $dataInput = null;
                $ezf_input = null;
                if (isset(Yii::$app->session['ezf_input'])) {
                    $ezf_input = Yii::$app->session['ezf_input'];
                    $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($value['ezf_field_type'], Yii::$app->session['ezf_input']);
                }

                $htmlFilter = \backend\modules\ezmodules\classes\ModuleFunc::htmlFilter($value, $dataInput, $searchModel, $var);
                if ($var == 'final_action') {
                    $label = 'Action';
                }
                $colTmp = [
                    'attribute' => $var,
                    'label' => $label,
                    'format' => 'raw',
                    'value' => function ($data) use ($dataInput, $value, $modelFields, $fieldName, $ezf_input) {
                        foreach ($modelFields as $key => $field) {
                            $var = $field['ezf_field_name'];
                            $version = $field['ezf_version'];
                            if ($fieldName == $var) {
                                if ($ezf_input) {
                                    $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($field['ezf_field_type'], $ezf_input);
                                }

                                $dataText = backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $field, $data);

                                if ($fieldName == 'sender') {
                                    return "<div style='margin-top:5px;' class='label label-primary'>" . $dataText . "</div><br/>";
                                } else if ($fieldName == 'mandatory') {
                                    $class = $data[$var] ? 'label-success' : 'label-danger';
                                    return "<div style='margin-top:5px;' class='label " . $class . "'>" . $dataText . "</div><br/>";
                                } else if ($fieldName == 'action') {
                                    if ($data[$fieldName] == 1) {
                                        return "<div style='margin-top:5px;' class='label label-primary'>Require</div><br/>";
                                    }
                                    return "<div style='margin-top:5px;' class='label label-primary'>" . $dataText . "</div><br/>";
//                            } else if ($fieldName == 'file_upload') {
//                                return $dataText != '' ? Html::a('Download', Yii::getAlias('@storageUrl') . "/ezform/fileinput/" . $data[$fieldName], [
//                                            'target' => '_blank',
//                                            'class' => 'btnViewFile ',
////                                            'data-status' => '2',
//                                            'data-id' => $data['id'],
////                                        'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
//                                        ]) : '';
//                            } else if($fieldName == 'detail'){
//                                return"<div class='text-sm-left'>$dataText</div>";
                                } else {
                                    return $dataText == '' ? '' : $dataText;
                                }
                            }
                        }
                        return '';
                    },
                    'headerOptions' => ['class' => "text-center"],
                    'contentOptions' => ['style' => "min-width:100px;"],
                    'filter' => $htmlFilter,
                ];


                if (is_array($field) && isset($field['attribute'])) {
                    $colTmp = \yii\helpers\ArrayHelper::merge($colTmp, $field);
                }

                $changeField = FALSE;
                $columns[] = $colTmp;
                break;
            }
        }

        if ($changeField && $fieldName && $fieldName != 'readonly' && $fieldName != 'readonly' && $fieldName != 'ezf_id' && $fieldName != 'data_id' && $fieldName != 'module_id' && $fieldName != 'status_view' && $fieldName != 'assign_to') {
            if (is_array($field) && isset($field['attribute'])) {
                $columns[] = $field;
            } else {
                if ($field != 'target')
                    $columns[] = [
                        'attribute' => $field,
                        'label' => $field,
                    ];
            }
        }
    }

    $columns[] = [
        'label' => 'Status',
        'format' => 'raw',
        'value' => function ($data) use ($dataInput, $value, $modelFields, $fieldName, $ezf_input) {
            $textReturn = '';
            if ($data['action'] != '' && ($data['action'] == 'Approve' || $data['action'] == 'Review' || $data['action'] == 'Acknowledge')) {
                if ($data['complete_date'] == '' && $data['status_view'] == '0') {
                    $textReturn = "<div style='margin-top:5px;' class='label label-danger'>New</div>";
                } else if ($data['complete_date'] == '' && $data['status_view'] == '1') {
                    $textReturn = "<div style='margin-top:5px;' class='label label-warning'>Waiting</div>";
                } else if (($data['complete_date'] != '' && $data['status_view'] == '1') || ($data['complete_date'] != '' && $data['status_view'] != '0')) {
                    $textReturn = "<div style='margin-top:5px;' class='label label-success'>Complete</div>";
                }
            } else {
                if ($data['status_view'] == '0') {
                    $textReturn = "<div style='margin-top:5px;' class='label label-danger'>New</div>";
                } else if ($data['status_view'] == '1') {
                    $textReturn = "<div style='margin-top:5px;' class='label label-success'>Viewed</div>";
                }
            }
            return $textReturn;
        },
        'headerOptions' => ['class' => "text-center"],
        'contentOptions' => ['style' => "min-width:100px;", 'class' => "text-center"],
//        'filter' => $var,
    ];
    if ($tab == 'to_me')
        $columns[] = [
            'class' => 'appxq\sdii\widgets\ActionColumn',
            'contentOptions' => ['style' => 'min-width:110px;width:110px;text-align: center;'],
            'template' => '{view} {delete}', //'{view} {update} {delete} ',
            'buttons' => [
                'view' => function ($url, $data, $key) use ($ezform, $reloadDiv, $modal) {
//                if (backend\modules\ezforms2\classes\EzfUiFunc::showViewDataEzf($ezform, Yii::$app->user->id, $data['user_create'])) {
                    $view = $data['readonly'] ? 'ezform-view' : 'ezform';
//                    if ($data['type_link'] == '1') {
//                        return Html::a('<span class="glyphicon glyphicon-share-alt"></span>', $data['url'], [
//                                    'title' => Yii::t('yii', 'View'),
//                                    'data-action' => 'redirect',
//                                    'data-view' => $data['status_view'],
//                                    'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
//                                    'class' => 'btn btn-default btn-xs btnDetail',
//                        ]);
//                    } else if ($data['type_link'] == '2') {
                    return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', Url::to(['/ezforms2/ezform-data/' . $view,
                        'ezf_id' => $data['ezf_id'],
                        'dataid' => $data['data_id'],
                        'id' => $data['id'],
                        'modal' => $modal,
                        'reloadDiv' => $reloadDiv,
                    ]), [
                        'data-id' => $data['id'],
                        'data-complete' => $data['complete_date'] != '' ? '1' : '0',
                        'data-button' => $data['action'],
                        'data-ezf_id' => $ezform->ezf_id,
                        'data-tb' => $data['ezf_id'],
                        'data-form' => $data['data_id'],
                        'data-view' => $data['status_view'],
                        'data-action' => 'view',
                        'title' => Yii::t('yii', 'View'),
                        'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                        'class' => 'btn btn-default btn-xs btnDetail',
                    ]);
//                    }
//                }
                },
                'update' => function ($url, $data, $key) use ($ezform, $reloadDiv, $modal) {
//                    if (backend\modules\ezforms2\classes\EzfUiFunc::showEditDataEzf($ezform, Yii::$app->user->id, $data['user_create'])) {
                    return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Url::to(['/ezforms2/ezform-data/ezform',
                        'ezf_id' => $ezform->ezf_id,
                        'dataid' => $data['id'],
                        'modal' => $modal,
                        'reloadDiv' => $reloadDiv,
                    ]), [
                        'data-action' => 'update',
                        'title' => Yii::t('yii', 'Update'),
                        'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                        'class' => 'btn btn-primary btn-xs btnDetail',
                    ]);
//                    }
                },
                'delete' => function ($url, $data, $key) use ($ezform, $reloadDiv) {
//                    if (backend\modules\ezforms2\classes\EzfUiFunc::showDeleteDataEzf($ezform, Yii::$app->user->id, $data['user_create'])) {
                    return Html::a('<span class="glyphicon glyphicon-trash"></span>', Url::to(['/notify/notify/deletes',
                        'ezf_id' => $ezform->ezf_id,
                        'dataid' => $data['id'],
                        'reloadDiv' => $reloadDiv,
                    ]), [
                        'data-action' => 'delete',
                        'data-ezf_id' => $ezform->ezf_id,
                        'data-id' => $data['id'],
                        'title' => Yii::t('yii', 'Delete'),
                        'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                        'data-method' => 'post',
                        'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                        'class' => 'btn btn-danger btn-xs btnDetail',
                    ]);
//                    }
                },
            ],
        ];
} else if ($tab == 'tool') {
    $columns = [
        [
            'class' => 'yii\grid\SerialColumn',
            'headerOptions' => ['style' => 'text-align: center;'],
            'contentOptions' => ['style' => 'min-width:60px;width:60px;text-align: center;'],
        ],
        [
            'attribute' => 'ezf_name',
            'label' => Yii::t('ezforms', 'Ezform Name'),
//        ], [
//            'attribute' => 'ezf_version',
//            'label' => Yii::t('ezforms', 'Ezform Version')
        ], [
//            'attribute' => 'ezf_version',
            'label' => Yii::t('ezforms', 'Total'),
            'value' => function ($data) {
                return (new yii\db\Query())->select('ezf_field_id')->from('ezform_fields')->where(['ezf_id' => $data['ezf_id'], 'ezf_field_type' => '912'])->count();
            }
        ], [
            'class' => 'appxq\sdii\widgets\ActionColumn',
            'contentOptions' => ['style' => 'min-width:110px;width:110px;text-align: center;'],
            'template' => '{view}', //'{view} {update} {delete} ',
            'buttons' => [
                'view' => function ($url, $data, $key) use ($reloadDiv) {
//                if (backend\modules\ezforms2\classes\EzfUiFunc::showViewDataEzf($ezform, Yii::$app->user->id, $data['user_create'])) {

                    return Html::a('<span class="glyphicon glyphicon-list"></span>', '/notify/default/detail?ezf_id=' . $data['ezf_id'] . '&v=' . $data['ezf_version'] . '&modal=' . $reloadDiv . '-modal' . '&reloadDiv=' . $reloadDiv, [
                        'title' => Yii::t('yii', 'View'),
                        'data-action' => 'tool',
                        'class' => 'btn btn-default btn-xs btnDetailNotify',
                    ]);
                },
//                'update' => function ($url, $data, $key) use($ezform, $reloadDiv, $modal) {
//                    if (backend\modules\ezforms2\classes\EzfUiFunc::showEditDataEzf($ezform, Yii::$app->user->id, $data['user_create'])) {
//                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Url::to(['/ezforms2/ezform-data/ezform',
//                                            'ezf_id' => $ezform->ezf_id,
//                                            'dataid' => $data['id'],
//                                            'modal' => $modal,
//                                            'reloadDiv' => $reloadDiv,
//                                        ]), [
//                                    'data-action' => 'update',
//                                    'title' => Yii::t('yii', 'Update'),
//                                    'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
//                                    'class' => 'btn btn-primary btn-xs',
//                        ]);
//                    }
//                },
//                'delete' => function ($url, $data, $key) use($ezform, $reloadDiv) {
//                    if (backend\modules\ezforms2\classes\EzfUiFunc::showDeleteDataEzf($ezform, Yii::$app->user->id, $data['user_create'])) {
//                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', Url::to(['/ezforms2/ezform-data/delete',
//                                            'ezf_id' => $ezform->ezf_id,
//                                            'dataid' => $data['id'],
//                                            'reloadDiv' => $reloadDiv,
//                                        ]), [
//                                    'data-action' => 'delete',
//                                    'title' => Yii::t('yii', 'Delete'),
//                                    'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
//                                    'data-method' => 'post',
//                                    'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
//                                    'class' => 'btn btn-danger btn-xs',
//                        ]);
//                    }
//                },
            ],
        ]
    ];
}
?>


    <ul class="nav nav-tabs">
        <?php if (!$hide_tab) { ?>
            <li class="tab-notify <?= $tab == 'to_me' ? 'active' : '' ?>" id="to_me"><a data-toggle="tab"
                                                                                        href="#to_me"><?= Yii::t('notify', 'All My Notification') ?></a>
            </li>
            <li id="all" class="tab-notify <?= $tab == 'all' ? 'active' : '' ?>"><a data-toggle="tab"
                                                                                    href="#all"><?= Yii::t('notify', 'All Notification of all member') ?></a>
            </li>
            <li id="tool" class="tab-notify <?= $tab == 'tool' ? 'active' : '' ?>"><a data-toggle="tab"
                                                                                      href="#tool"><?= Yii::t('notify', 'Notification tools') ?></a>
            </li>
            <!--            <li id="send" class="tab-notify --><?php //echo $tab == 'send' ? 'active' : '' ?><!--"><a data-toggle="tab"-->
            <!--                                                                                      href="#send">--><?php //echo Yii::t('notify', 'Send Notification') ?><!--</a>-->
            <!--            </li>-->
        <?php } ?>
    </ul>

    <div id="div-tab-content" style="margin-top: 10px;">
        <?php
        $radioBox = Html::beginTag('div', ['class' => 'form-inline', 'style' => 'margin-top:20px;margin-bottom:30px'])
            . backend\modules\ezforms2\classes\EzformWidget::checkbox('radioBoxSearch', $status_view == '1' ? TRUE : FALSE, [
                'class' => 'radioBoxSearch',
                'style' => "width: 20px; height: 20px;",
                'value' => 1,
                'label' => 'New'
            ]) .
            ' &nbsp; ' .
            backend\modules\ezforms2\classes\EzformWidget::checkbox('radioBoxSearch', $status_view == '2' ? TRUE : FALSE, [
                'class' => 'radioBoxSearch',
                'style' => "width: 20px; height: 20px;",
                'value' => 2,
                'label' => 'Waiting'
            ]) .
            ' &nbsp; ' .
            backend\modules\ezforms2\classes\EzformWidget::checkbox('radioBoxSearch', $status_view == '3' ? TRUE : FALSE, [
                'class' => 'radioBoxSearch',
                'style' => "width: 20px; height: 20px;",
                'value' => 3,
                'label' => 'Completed'
            ]) . ' &nbsp; ' .
//            backend\modules\ezforms2\classes\EzformWidget::radioList('radioBoxSearch', $status_view, ['data' => ['1' => 'New', '2' => 'Waiting', '3' => 'Completed']], ['inline' => true, 'class' => 'radioBoxSearch']) .
            Html::endTag('div');
        ?>
        <?php
        if ($tab == 'to_me' || $tab == 'all') {
            echo $radioBox . appxq\sdii\widgets\GridView::widget([
                    'id' => "$reloadDiv-notify",
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'panelBtn' =>
                        $tab == 'to_me' ? Html::button('<i class="glyphicon glyphicon-trash"></i>', ['data-url' => Url::to(['/notify/notify/deletes']), 'class' => 'btn btn-danger btn-sm', 'id' => 'modal-delbtn-notify', 'disabled' => true, 'title' => Yii::t('notify', 'Delete All')]) : '',
//    'filterModel' => $searchModel,
                    'columns' => $columns,
                ]);
//        } else if ($tab == 'tool') {
        } else {
            echo appxq\sdii\widgets\GridView::widget([
                'id' => "$reloadDiv-notify-tool",
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => $columns,
                'panelBtn' => Html::button("<span class='glyphicon glyphicon-send'></span> " . Yii::t('notify', 'Send notify'), [
                        'class' => 'btn btn-primary',
                        'id' => 'btnSendNotify',
                        'title' => Yii::t('notify', 'Send notify'),
                        'data-url' => '/notify/notify/send-notify?&modal=' . $reloadDiv . '-modal' . '&reloadDiv=' . $reloadDiv
                    ]).' '. Html::button("<span class='glyphicon glyphicon-plus'></span> " . Yii::t('notify', 'Add Notify to Ezform'), [
                        'class' => 'btn btn-success',
                        'id' => 'btnAddNotify',
                        'title' => Yii::t('notify', 'Add Notify to Ezform'),
                        'data-url' => '/notify/default/add?&modal=' . $reloadDiv . '-modal' . '&reloadDiv=' . $reloadDiv
                    ]),
            ]);

//        }else{
//            echo $this->render('_send-notify');
        }
        ?>
    </div>

<?=
appxq\sdii\widgets\ModalForm::widget([
    'id' => $reloadDiv . "-modal",
    'size' => 'modal-lg',
    'tabindexEnable' => false,
]);
?>

<?=
appxq\sdii\widgets\ModalForm::widget([
    'id' => $reloadDiv . "-modal-detail",
    'size' => 'modal-lg',
    'tabindexEnable' => false,
]);
?>


<?=
appxq\sdii\widgets\ModalForm::widget([
    'id' => $reloadDiv . "-add-notify",
    'size' => 'modal-lg',
    'tabindexEnable' => false,
]);
?>


<?php
//$sub_modal = '<div id="modal-'.$ezform->ezf_id.'" class="fade modal" role="dialog"><div class="modal-dialog modal-xxl"><div class="modal-content"></div></div></div>';

$this->registerJs("

hideLoadBlock('body');

$('.btnView').removeClass('ezform-main-open');

$('#$reloadDiv-modal').on('hidden.bs.modal', function(e){
            if($('body .modal').hasClass('in')){
                $('body').addClass('modal-open');
            } 
            onLoadBlock('body');
            getUiAjax($('#$reloadDiv').attr('data-url'),'$reloadDiv','tool');
});

$('#$reloadDiv-modal-detail').on('hidden.bs.modal', function(e){
            if($('body .modal').hasClass('in')){
                $('body').addClass('modal-open');
            } 
            onLoadBlock('body');
            getUiAjax($('#$reloadDiv').attr('data-url'),'$reloadDiv','$tab');
});

$('#modal-ezform-main').on('hidden.bs.modal', function(e){
            if($('body .modal').hasClass('in')){
                $('body').addClass('modal-open');
            } 
            onLoadBlock('body');
            getUiAjax($('#$reloadDiv').attr('data-url'),'$reloadDiv','$tab');
});
    
$('#$reloadDiv-add-notify').on('hidden.bs.modal', function(e){
            if($('body .modal').hasClass('in')){
                $('body').addClass('modal-open');
            } 
});

$('#$reloadDiv-notify').on('click', '#modal-delbtn-notify', function() {
    selectionSystemErrorGrid($(this).attr('data-url'));
});

$('#$reloadDiv-notify').on('click', '.selectionSystemNotifyIds', function() {
    var key = $('input:checked[class=\"'+$(this).attr('class')+'\"]');
    disabledSystemErrorBtn(key.length);
});

$('#$reloadDiv-notify').on('click', '.select-on-check-all', function() {
    window.setTimeout(function() {
	var key = $('#$reloadDiv-notify').yiiGridView('getSelectedRows');
	disabledSystemErrorBtn(key.length);
    },100);
});

function disabledSystemErrorBtn(num) {
    if(num>0) {
	$('#modal-delbtn-notify').attr('disabled', false);
    } else {
	$('#modal-delbtn-notify').attr('disabled', true);
    }
}

function selectionSystemErrorGrid(url) {
    yii.confirm('" . Yii::t('app', 'Are you sure you want to delete these items?') . "', function() {
	$.ajax({
	    method: 'POST',
	    url: url,
	    data: $('.selectionSystemNotifyIds:checked[name=\"selection[]\"]').serialize(),
	    dataType: 'JSON',
	    success: function(result, textStatus) {
		if(result.status == 'success') {
                getUiAjax($('#$reloadDiv').attr('href'), '$reloadDiv','$tab');
		    " . SDNoty::show('result.message', 'result.status') . "
		    
		} else {
		    " . SDNoty::show('result.message', 'result.status') . "
		}
	    }
	});
    });
}
    
$('#$reloadDiv-notify-tool tbody tr').on('dblclick', function() {
        var url = $(this).children('td').children('.btnDetailNotify').attr('href');
        $('#$reloadDiv-modal .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
        $('#$reloadDiv-modal').modal('show')
        .find('.modal-content')
        .load(url);
        return false;
});

$('#$reloadDiv-notify-tool tbody tr td a').on('click', function() {
    
    var url = $(this).attr('href');
    var action = $(this).attr('data-action');
    
    if(action === 'tool'){
        $('#$reloadDiv-modal .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
        $('#$reloadDiv-modal').modal('show')
        .find('.modal-content')
        .load(url);
    }
    return false;
});

$('#$reloadDiv-notify-tool thead tr th a').on('click', function() {
    
    var url = $(this).attr('href');
    getUiAjax(url, '$reloadDiv','$tab');
    return false;
});

$('#$reloadDiv-notify tbody tr td .btnDetail').on('click', function() {
    
    var url = $(this).attr('href');
    var action = $(this).attr('data-action');
    var ezf_id = $(this).attr('data-ezf_id');
    var data_id = $(this).attr('data-id'); 
    var btn = '';
    var data_complete = $(this).attr('data-complete');
    var data_btn = $(this).attr('data-button');
    var data_tb = $(this).attr('data-tb');
    var data_form = $(this).attr('data-form');
    
    if($(this).attr('data-view') == '0'){
        $.ajax({
                method: 'POST',
                url: '/notify/notify/viewed?id=' + $(this).attr('data-id'),
                dataType: 'HTML',
                success: function (result, textStatus) {

                }
        });
    }
//    if(action === 'redirect'){
////        window.location = url;
//        window.open(url, '_blank');
//        return false;
//    }else if(action === 'update' || action === 'create'){
//        $('#$modal .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
//        $('#$modal').modal('show')
//        .find('.modal-content')
//        .load(url);
//        return false;
//    } else if(action === 'view') {
    if(action != 'delete'){
       
        var url_data = '/notify/notify/grid-complete?modal=$modal&reloadDiv=grid-complete&ezf_id='+ezf_id+'&data_form='+data_form+'&data-ezf_id='+data_tb;
        
        $('#$reloadDiv-modal-detail .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
        $('#$reloadDiv-modal-detail').modal('show').find('.modal-content').load('/notify/notify/detail?id=' + data_id + '&modal=$reloadDiv-modal-detail&sub_modal=$modal');
//        $.ajax({
//            method: 'POST',
//            url: url,
//            dataType: 'HTML',
//            success: function (result, textStatus) {
//                $('#$modal').find('.modal-content').html(result);
//                if(data_btn != ''){
//                    if(data_btn == 'Acknowledge'){
//                        btn = '<button data-value=\'1\' class=\'btn btn-success btnStatus pull-right\'>Acknowledge</button>'
//                    }else if(data_btn == 'Review'){
//                        btn = '<button data-value=\'1\' class=\'btn btn-success btnStatus pull-right\'>Review</button>'
//                    }else if(data_btn == 'Approve'){
//                        btn = '<button data-value=\'2\' class=\'btn btn-success approve pull-right\'>Approve</button> <button data-value=\'3\' style=\'margin-right:5px;\' class=\'btn btn-danger pull-right approve\'> Not Approve</button>'
//                    }
//                    if(data_complete == '0'){
//                        $('#$modal').find('#form-'+data_tb).append(btn+'<div class=\'clearfix\'></div><hr/><div data-url='+url_data+' id=\'grid-complete\'><div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div></div>');
//                    }else{
//                        $('#$modal').find('#form-'+data_tb).append('<div data-url='+url_data+' id=\'grid-complete\'><div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div></div>');
//                    }
//
//                    getUiAjax(url_data,'grid-complete');
//
//                    $('.btnStatus').on('click',function(){
//                        UpdateResult(data_form,data_tb,$(this).attr('data-value'),$(this),data_id,ezf_id);
//                    });
//                    $('.approve').on('click',function(){
//                        UpdateResult(data_form,data_tb,$(this).attr('data-value'),$(this),data_id,ezf_id);
//                    }); 
//                }
//            }
//        });
//        $('#$modal .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
//        $('#$modal').modal('show')
//        .find('.modal-content')
//        .load(url);
        return false;
    } else if(action === 'delete') {
        yii.confirm('" . Yii::t('app', 'Are you sure you want to delete this item?') . "', function(){
                $.post(
                        url, {'_csrf':'" . Yii::$app->request->getCsrfToken() . "',id:data_id}
                ).done(function(result){
                        if(result.status == 'success'){
                                " . SDNoty::show('result.message', 'result.status') . "
                            var urlreload =  $('#$reloadDiv').attr('data-url');        
                            getUiAjax(urlreload, '$reloadDiv','$tab');        
                        } else {
                                " . SDNoty::show('result.message', 'result.status') . "
                        }
                }).fail(function(){
                        " . SDNoty::show('"Server Error"', '"error"') . "
                        console.log('server error');
                });
        });
        return false;
    }
//    return false;
});

$('#$reloadDiv-notify').on('beforeFilter', function(e) {
    var \$form = $(this).find('form');
    $.ajax({
	method: 'GET',
	url: \$form.attr('action'),
        data: \$form.serialize(),
	dataType: 'HTML',
	success: function(result, textStatus) {
	    $('#$reloadDiv').html(result);
	}
    });
    return false;
});

$('#$reloadDiv-notify-tool').on('beforeFilter', function(e) {
    var \$form = $(this).find('form');
    $.ajax({
	method: 'GET',
	url: \$form.attr('action'),
        data: \$form.serialize(),
	dataType: 'HTML',
	success: function(result, textStatus) {
	    $('#$reloadDiv').html(result);
	}
    });
    return false;
});

$('#$reloadDiv-notify .pagination a').on('click', function() {
    getUiAjax($(this).attr('href'), '$reloadDiv','$tab');
    return false;
});

$('#$reloadDiv-notify thead tr th a').on('click', function() {
    getUiAjax($(this).attr('href'), '$reloadDiv','$tab');
    return false;
});

$('#$reloadDiv-notify tbody tr,#$reloadDiv-notify-tool tbody tr').on('mouseover', function() {
    $(this).css('cursor','pointer');
});


$('#$reloadDiv-notify tbody tr').on('dblclick', function() {
    var hasTagA = $(this).has('.btnDetail').length;
    if(hasTagA){
        var url = $(this).children('td').children('.btnDetail').attr('href');
        var action = $(this).children('td').children('.btnDetail').attr('data-action');
//        if(action == 'view'){
        var btn = '';
            var data_id = $(this).children('td').children('.btnDetail').attr('data-id');
            var data_complete = $(this).children('td').children('.btnDetail').attr('data-complete');
            var data_btn = $(this).children('td').children('.btnDetail').attr('data-button');
            var data_tb = $(this).children('td').children('.btnDetail').attr('data-tb');
            var data_form = $(this).children('td').children('.btnDetail').attr('data-form');
            var ezf_id = $(this).children('td').children('.btnDetail').attr('data-ezf_id');
            var data_view = $(this).children('td').children('.btnDetail').attr('data-view');
            var url_data = '/notify/notify/grid-complete?modal=$modal&reloadDiv=grid-complete&ezf_id='+ezf_id+'&data_form='+data_form+'&data-ezf_id='+data_tb;
                
            if(data_view == '0'){
                $.ajax({
                        method: 'POST',
                        url: '/notify/notify/viewed?id=' + data_id,
                        dataType: 'HTML',
                        success: function (result, textStatus) {

                        }
                });
            }
            $('#$reloadDiv-modal-detail .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
            $('#$reloadDiv-modal-detail').modal('show').find('.modal-content').load('/notify/notify/detail?id=' + data_id + '&modal=$reloadDiv-modal-detail&sub_modal=$modal');
//            $.ajax({
//                method: 'POST',
//                url: url,
//                dataType: 'HTML',
//                success: function (result, textStatus) {
//                    $('#$modal').find('.modal-content').html(result);
//                    if(data_btn != ''){
//                        if(data_btn == 'Acknowledge'){
//                            btn = '<button data-value=\'1\' class=\'btn btn-success btnStatus pull-right\'>Acknowledge</button>'
//                        }else if(data_btn == 'Review'){
//                            btn = '<button data-value=\'1\' class=\'btn btn-success btnStatus pull-right\'>Review</button>'
//                        }else if(data_btn == 'Approve'){
//                            btn = '<button data-value=\'2\' class=\'btn btn-success approve pull-right\'>Approve</button> <button data-value=\'3\' style=\'margin-right:5px;\' class=\'btn btn-danger pull-right approve\'> Not Approve</button>'
//                        }
//                        if(data_complete == '0'){
//                            $('#$modal').find('#form-'+data_tb).append(btn+'<div class=\'clearfix\'></div><hr/><div data-url='+url_data+' id=\'grid-complete\'><div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div></div>');
//                        }else{
//                            $('#$modal').find('#form-'+data_tb).append('<hr/><div data-url='+url_data+' id=\'grid-complete\'><div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div></div>');
//                        }
//
//                        getUiAjax(url_data,'grid-complete');
//
//                        $('.btnStatus').on('click',function(){
//                            UpdateResult(data_form,data_tb,$(this).attr('data-value'),$(this),data_id,ezf_id);
//                        });
//                        $('.approve').on('click',function(){
//                            UpdateResult(data_form,data_tb,$(this).attr('data-value'),$(this),data_id,ezf_id);
//                        }); 
//                    }
//                }
//            });
            return false;
//        }
//        if(action == 'redirect'){
//            window.location = url;
//        }
    }
});

$('#$reloadDiv-notify .btnViewFile').on('click',function(){
    var url = $(this).prop('href');
    var target = $(this).prop('target');
    window.open(url, target);
});



function UpdateResult(id='',ezf_id='',value='',btn,id_notify,ezf_notify){

     $.ajax({
        method: 'POST',
        url: '/tmf/tmf/update-result',
        dataType: 'HTML',
        data:{
            ezf_id:ezf_id,
            id:id,
            value:value
        },
        success: function(result, textStatus) {
            var url = $('#$reloadDiv').attr('data-url');
            if(result){
            " . SDNoty::show("'" . Yii::t('ezform', 'Complete') . "'", '"success"') . "
                getUiAjax(url, '$reloadDiv');
                    btn.hide();
                    $('.approve').hide()
                    UpdateNotify(id_notify,ezf_notify);
                    getUiAjax($('#grid-complete').attr('data-url'),'grid-complete');
            }else{
            " . SDNoty::show("'" . Yii::t('ezform', 'Failed') . "'", '"error"') . "
                getUiAjax(url, '$reloadDiv');
//                    btn.hide();
            }
        }
    });
}

function UpdateNotify(id='',ezf_id=''){

     $.ajax({
        method: 'POST',
        url: '/notify/notify/update-result',
        dataType: 'HTML',
        data:{
            ezf_id:ezf_id,
            id:id,
        },
        success: function(result, textStatus) {
        }
    });
}


 
$('#all').click(function(){
    $('.tab-notify').not($(this)).removeClass('active');
    $(this).addClass('active');
    $('#div-tab-content').html('<div class=\"sdloader\"><i class=\"sdloader-icon\"></i></div>');
    getUiAjax($('#$reloadDiv').attr('data-url')+'&data_id=','$reloadDiv','all');
    var url = window.location;
    url = url.toString();
    if(url.indexOf('&tab_notify') > 0){
        url.indexOf('&tab_notify');
        url = url.substr(0, url.indexOf('&tab_notify'));
    }
    if(url.indexOf('&notify_id') > 0){
        url.indexOf('&notify_id');
        url = url.substr(0, url.indexOf('&notify_id'));
    }
    window.history.replaceState({}, \"Payment And Cost\", url+'&tab_notify=all');
    return false;
//    window.location.href = url+'&tab_notify=all';
});

$('#to_me').click(function(){
    $('.tab-notify').not($(this)).removeClass('active');
    $(this).addClass('active');
    $('#div-tab-content').html('<div class=\"sdloader\"><i class=\"sdloader-icon\"></i></div>');
    getUiAjax($('#$reloadDiv').attr('data-url')+'&data_id=','$reloadDiv','to_me');
    var url = window.location;
    url = url.toString();
    if(url.indexOf('&tab_notify') > 0){
        url.indexOf('&tab_notify');
        url = url.substr(0, url.indexOf('&tab_notify'));
    }
    if(url.indexOf('&notify_id') > 0){
        url.indexOf('&notify_id');
        url = url.substr(0, url.indexOf('&notify_id'));
    }
    window.history.replaceState({}, \"Payment And Cost\", url+'&tab_notify=to_me');
    return false;
//    window.location.href = url+'&tab_notify=to_me'
});

$('#tool').click(function(){
    $('.tab-notify').not($(this)).removeClass('active');
    $(this).addClass('active');
    $('#div-tab-content').html('<div class=\"sdloader\"><i class=\"sdloader-icon\"></i></div>');
    getUiAjax($('#$reloadDiv').attr('data-url')+'&data_id=','$reloadDiv','tool');
    var url = window.location;
    url = url.toString();
    if(url.indexOf('&tab_notify') > 0){
        url.indexOf('&tab_notify');
        url = url.substr(0, url.indexOf('&tab_notify'));
    }
    if(url.indexOf('&notify_id') > 0){
        url.indexOf('&notify_id');
        url = url.substr(0, url.indexOf('&notify_id'));
    }
    window.history.replaceState({}, \"Payment And Cost\", url+'&tab_notify=tool');
    return false;
//    window.location.href = url+'&tab_notify=tool';
});

$('#send').click(function(){
    $('.tab-notify').not($(this)).removeClass('active');
    $(this).addClass('active');
    $('#div-tab-content').html('<div class=\"sdloader\"><i class=\"sdloader - icon\"></i></div>');
    getUiAjax($('#$reloadDiv').attr('data-url')+'&data_id=','$reloadDiv','send');
    var url = window.location;
    url = url.toString();
    if(url.indexOf('&tab_notify') > 0){
        url.indexOf('&tab_notify');
        url = url.substr(0, url.indexOf('&tab_notify'));
    }
    if(url.indexOf('&notify_id') > 0){
        url.indexOf('&notify_id');
        url = url.substr(0, url.indexOf('&notify_id'));
    }
    window.history.replaceState({}, \"Payment And Cost\", url+'&tab_notify=send');
    return false;
//    window.location.href = url+'&tab_notify=tool';
});

$('.radioBoxSearch').change(function(){
    $('.radioBoxSearch').not(this).prop('checked', false);
    var param = '';
    if($('input[name=\"radioBoxSearch\"]:checked').val() == 1){
        param = '&status_view=0';
    }else if($('input[name=\"radioBoxSearch\"]:checked').val() == 2){
        param = '&status_view=1';
    }else if($('input[name=\"radioBoxSearch\"]:checked').val() == 3){
        param = '&complete_date=1';
    }
    console.log($('#$reloadDiv').attr('data-url'));
    getUiAjax($('#$reloadDiv').attr('data-url')+param+'&data_id=', '$reloadDiv','$tab');
});

$('#btnAddNotify').click(function(){
    var url = $(this).attr('data-url');
    $('#$reloadDiv-modal .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
    $('#$reloadDiv-modal').modal('show')
    .find('.modal-content')
    .load(url);
    return false;
});


$('#btnSendNotify').click(function(){
    var url = $(this).attr('data-url');
    $('#$reloadDiv-modal .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader - icon\"></i></div>');
    $('#$reloadDiv-modal').modal('show')
    .find('.modal-content')
    .load(url);
    return false;
});

//$('#$modal').on('hidden.bs.modal', function () {
//    var urlreload =  $('#$reloadDiv').attr('data-url');
//    $('#$reloadDiv').html('<center>Loading...</center>');        
//    getUiAjax(urlreload, '$reloadDiv');
//    window.location.reload();
//});

var checkAjax = null;
function getUiAjax(url, divid,tab='') {
    if(checkAjax && checkAjax.readyState != 4){
        checkAjax.abort();
    }
    checkAjax = $.ajax({
        method: 'GET',
        url: url,
        data:{tab:tab},
        dataType: 'HTML',
        success: function(result, textStatus) {
            $('#'+divid).html(result);
            hideLoadBlock('body');
        }
    }).fail(function(err) {
        if(err.statusText != 'abort'){
            $('#'+divid).html(`<div class='alert alert-danger'>Server error</div>`);
        }
        hideLoadBlock('body');
    }); 
}

 function onLoadBlock(ele){
        $(ele).waitMe({
            effect : 'facebook',
            text : 'Please wait...',
            bg : 'rgba(255,255,255,0.8)',
            color : '#000',
            maxSize : '',
            waitTime : -1,
            textPos : 'vertical',
            fontSize : '',
            source : '',
            onClose : function() {}
        });
    }
    function hideLoadBlock(ele){
         $(ele).waitMe(\"hide\");
    }      

");
?>