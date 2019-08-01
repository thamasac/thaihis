<div id="<?= $reloadDiv ?>-sub-view" data-url="<?= $data_url ?>">
    <div id="print-<?= $reloadDiv ?>" style="background-color: #fff">
        <div class="modal-header" style="background-color: #fff">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3 class="modal-title" id="itemModalLabel">
                <?php
                $datName = (new yii\db\Query())
                        ->select('*')
                        ->from($ezform_name['ezf_table'])
//                        ->leftJoin($ezform_name['ezf_table'], $ezform_type['ezf_table'] . '.target = ' . $ezform_name['ezf_table'] . '.target')
                        ->where($ezform_name['ezf_table'] . ".id = :id", [':id' => $data_id])
                        ->one();
                echo "<b>Document Name : " . $datName['F2v2'] . "</b>";
                ?>
            </h3>    
        </div>
        <div class="modal-body">

            <?php

            use yii\helpers\Html;
            use appxq\sdii\helpers\SDNoty;
            use appxq\sdii\helpers\SDHtml;
            use yii\helpers\Url;
            use backend\modules\ezforms2\classes\EzfFunc;
            use appxq\sdii\utils\SDUtility;

//appxq\sdii\utils\VarDumper::dump($dataProvider->getModels());
            $columns = [
                    [
                    'class' => 'yii\grid\SerialColumn',
                    'headerOptions' => ['style' => 'text-align: center;'],
                    'contentOptions' => ['style' => 'min-width:60px;width:60px;text-align: center;'],
                ],
            ];
            if (!$disabled) {
                $columns[] = [
                    'class' => 'appxq\sdii\widgets\ActionColumn',
                    'contentOptions' => ['style' => 'min-width:110px;width:110px;text-align: center;'],
                    'template' => '{view} {update} {delete} ',
                    'buttons' => [
                        'view' => function ($url, $data, $key) use($ezform_detail, $reloadDiv, $subModal, $type_id, $data_url) {
//                appxq\sdii\utils\VarDumper::dump($data);
                            if (backend\modules\ezforms2\classes\EzfUiFunc::showViewDataEzf($ezform_detail, Yii::$app->user->identity->profile->sitecode, $data['xsourcex']) ||
                                backend\modules\ezforms2\classes\EzfUiFunc::showViewDataEzf($ezform_detail, '1530521953014158200', $data['user_create'])) {
                                return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', Url::to(['/ezforms2/ezform-data/ezform-view',
                                                    'ezf_id' => $ezform_detail['ezf_id'],
                                                    'dataid' => $data['id'],
                                                    'modal' => $subModal,
                                                    'reloadDiv' => $reloadDiv,
                                                ]), [
                                            'data-action' => 'update',
                                            'title' => Yii::t('yii', 'View'),
                                            'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                                            'class' => 'btn btn-default btn-xs ',
                                ]);
                            }
                        },
                        'update' => function ($url, $data, $key) use($ezform_detail, $reloadDiv, $subModal) {
                            if (backend\modules\ezforms2\classes\EzfUiFunc::showEditDataEzf($ezform_detail, Yii::$app->user->id, $data['user_create']) ||
                                backend\modules\ezforms2\classes\EzfUiFunc::showViewDataEzf($ezform_detail, '1530521953014158200', $data['user_create'])) {
                                return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Url::to(['/ezforms2/ezform-data/ezform',
                                                    'ezf_id' => $ezform_detail['ezf_id'],
                                                    'dataid' => $data['id'],
                                                    'modal' => $subModal,
                                                    'reloadDiv' => $reloadDiv,
                                                ]), [
                                            'data-action' => 'update',
                                            'title' => Yii::t('yii', 'Update'),
                                            'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                                            'class' => 'btn btn-primary btn-xs btnDetail',
                                ]);
                            }
                        },
                        'delete' => function ($url, $data, $key) use($ezform_detail, $reloadDiv) {
                            if (backend\modules\ezforms2\classes\EzfUiFunc::showDeleteDataEzf($ezform_detail, Yii::$app->user->id, $data['user_create']) ||
                                backend\modules\ezforms2\classes\EzfUiFunc::showViewDataEzf($ezform_detail, '1530521953014158200', $data['user_create'])) {
                                return Html::a('<span class="glyphicon glyphicon-trash"></span>', Url::to(['/ezforms2/ezform-data/delete',
                                                    'ezf_id' => $ezform_detail['ezf_id'],
                                                    'dataid' => $data['id'],
                                                    'reloadDiv' => $reloadDiv,
                                                ]), [
                                            'data-action' => 'delete',
                                            'title' => Yii::t('yii', 'Delete'),
                                            'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                            'data-method' => 'post',
                                            'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
                                            'class' => 'btn btn-danger btn-xs',
                                ]);
                            }
                        },
                    ],
                ];
            }
            if ($default_column) {
                $columns[] = [
                    'attribute' => 'create_date',
                    'value' => function ($data) {
                        return !empty($data['create_date']) ? \appxq\sdii\utils\SDdate::mysql2phpDateTime($data['create_date']) : '';
                    },
                    'headerOptions' => ['style' => 'text-align: center;'],
                    'contentOptions' => ['style' => 'min-width:140px;width:140px;text-align: center;'],
                    'filter' => \kartik\daterange\DateRangePicker::widget([
                        'model' => $searchModel,
                        'attribute' => 'create_date',
                        'convertFormat' => true,
                        //'useWithAddon'=>true,
                        'options' => ['id' => 'dr_' . $reloadDiv . '_' . $modal, 'class' => 'form-control'],
                        'pluginOptions' => [
                            'locale' => [
                                'format' => 'd-m-Y',
                                'separator' => ' to ',
                            //'language'=>'TH',
                            ],
                        //'opens'=>'left'
                        ]
                    ]),
                ];
            }

            $modelFields = \backend\modules\ezforms2\models\EzformFields::find()
                    ->where('ezf_id = :ezf_id_name OR ezf_id = :ezf_id_detail OR ezf_id = :ezf_id_type', [':ezf_id_name' => $ezf_name_id,
                        ':ezf_id_detail' => $ezf_detail_id, ':ezf_id_type' => $ezf_type_id
                    ])
                    ->orderBy(['ezf_field_order' => SORT_ASC])
                    ->all();
            $fieldsGroup = \backend\modules\ezforms2\classes\EzfFunc::getFieldsGroup($modelFields, $ezform_detail['ezf_version']);
            if (!isset(Yii::$app->session['ezf_input'])) {
                Yii::$app->session['ezf_input'] = backend\modules\ezforms2\classes\EzfQuery::getInputv2All();
            }
//appxq\sdii\utils\VarDumper::dump($data_column);
            foreach ($data_column as $field) {

                $fieldName = $field;
                if (is_array($field) && isset($field['attribute'])) {
                    $fieldName = $field['attribute'];
                }

                $changeField = TRUE;
                foreach ($fieldsGroup as $key => $value) {
                    $var = $value['ezf_field_name'];
                    $label = $value['ezf_field_label'];

                    if ($fieldName == $var && $fieldName != 'target' && $var != 'target' && $var != 'F2v5' && $var != 'F2v9' && $var != 'owner' && $var != 'F2v6' && $var != 'approve_status' && $var != 'review' && $var != 'approve' && $var != 'acknowledge') {
                        $dataInput;
                        $ezf_input;
                        if (isset(Yii::$app->session['ezf_input'])) {
                            $ezf_input = Yii::$app->session['ezf_input'];
                            $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($value['ezf_field_type'], Yii::$app->session['ezf_input']);
                        }
//            $htmlFilter = \backend\modules\ezmodules\classes\ModuleFunc::htmlFilter($value, $dataInput, $searchModel, $var);
//                        if ($var == 'F2v9') {
//                            $colTmp = [
//                                'attribute' => $var,
//                                'label' => $label,
//                                'format' => 'raw',
//                                'value' => function ($data) use($dataInput, $value) {
////                        $dataText = backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $value, $data);
//                                    if ($data['F2v9'] != '') {
//                                        return Html::a('Download', Yii::getAlias('@storageUrl') . "/ezform/fileinput/" . $data['F2v9'], [
//                                                    'target' => '_blank',
//                                                    'class' => 'btnViewFile',
////                                            'data-status' => '2',
//                                                    'data-id' => $data['id'],
//                                                    'data-pjax' => isset($this->pjax_id) ? $this->pjax_id : '0',
//                                        ]);
//                                    } else {
//                                        return '';
//                                    }
//                                },
//                                'headerOptions' => ['class' => "text-center"],
//                                'contentOptions' => ['style' => "min-width:100px;", 'class' => 'text-center'],
//                                'filter' => $var,
//                            ];
//                        } else 
                        if ($var == 'status') {

                            $colTmp = [
                                'attribute' => $var,
                                'label' => 'Status',
                                'format' => 'raw',
                                'value' => function ($data) use($dataInput, $var, $value) {
//                        $dataText = backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $value, $data);
//                        $dataText = empty($dataText) ? '' : $dataText;
                                    $data_check = [];
                                    if ($data['check_user'] != '') {
                                        $data_check = SDUtility::string2Array($data['check_user']);
                                    }
                                    $data_user = [];
                                    if ($data['final_name'] != '') {
                                        $data_user = SDUtility::string2Array($data['final_name']);
                                    }
                                    $data_role = [];
                                    if ($data['final_role'] != '') {
                                        $data_role = SDUtility::string2Array($data['final_role']);
                                    }
                                    $data_approve = [];
                                    if ($data['approve_status'] != '') {
                                        $data_approve = SDUtility::string2Array($data['approve_status']);
                                    }

                                    $data_role = backend\modules\tmf\classes\TmfFn::getRole($data_role);

                                    if (!empty($data_role)) {
                                        if (!empty($data_user)) {
                                            $data_user = array_merge($data_user, $data_role);
                                        } else {
                                            $data_user = $data_role;
                                        }
                                    }
                                    $result = array();
                                    foreach ($data_user as $value) {
                                        if (!isset($result[$value]))
                                            $result[$value] = $value;
                                    }
                                    if (empty($result)) {
                                        $count_user = count($data_user);
                                        $result = $data_user;
                                    } else {
                                        $count_user = count($result);
                                    }
                                    $num = 0;
                                    $checkApprove = false;
                                    foreach ($result as $vUser) {
//                            foreach ($data_check as $vCheck) {
                                        if (in_array($vUser, $data_check)) {
                                            if (isset($data_approve[$vUser]) && $data_approve[$vUser] != '' && $checkApprove == false) {
                                                $checkApprove = true;
                                            }
                                            $num++;
                                        }
//                            }
                                    }
                                   
                                    $text = 'Waiting';
                                    $class = 'btn btn-warning btn-xs btnViewAss';
//                                    $btn = " " . Html::button('<span class="glyphicon glyphicon-eye-open"></span>', [
//                                                'class' => 'btn btn-primary btn-xs btnViewAss',
//                                                'data-name' => $data['assign_name'],
//                                                'data-role' => $data['assign_role'],
//                                                'data-check' => $data['check_user']
//                                                    ]
//                                    );
                                    $check = false;
                                    if (($data[$var] == 5 || $data[$var] == 6) && $data['acknowledge'] == true) {
                                        if ($count_user == $num) {
                                            $text = 'Acknowledged';
                                            $class = 'btn btn-success btn-xs btnViewAss';
                                        }
                                        $check = true;
                                    } else if (($data[$var] == 1 || $data[$var] == 2) && $data['review'] == true) {
                                        if ($count_user == $num) {
                                            $text = 'Reviewed';
                                            $class = 'btn btn-success btn-xs btnViewAss';
                                        }
                                        $check = true;
                                    } else if (($data[$var] == 4 || $data[$var] == 3) && $data['approve'] == true) {
                                        $dataApprove = [];
                                        if ($data['approve_status'] != '') {
                                            $dataApprove = SDUtility::string2Array($data['approve_status']);
                                        }
                                        if ($count_user == $num) {
                                            if (!empty($dataApprove)) {

                                                foreach ($dataApprove as $value) {
                                                    if ($value == '0') {
//                                        return Html::tag('div', "Not Approve", ['class' => 'alert-danger col-md-12']);
                                                        if ($checkApprove == true) {
                                                            $text = 'Not Approve';
                                                            $class = 'btn btn-danger btn-xs btnViewAss';
                                                            break;
                                                        }
                                                    } else {
                                                        if ($checkApprove == true) {
                                                            $text = 'Approved';
                                                            $class = 'btn btn-success btn-xs btnViewAss';
                                                        }
                                                    }
                                                }
                                            } else {
                                                if ($checkApprove == true) {
                                                    $text = 'Approved';
                                                    $class = 'btn btn-success btn-xs btnViewAss';
                                                }
                                            }
                                        }
                                        $check = true;
                                    }
                                    return $check ? Html::tag('div', $num . "/" . $count_user . "<br/>" . $text, [
                                                'class' => $class,
                                                'data-id' => EzfFunc::arrayEncode2String($result),
                                                'data-approve' => $data['approve_status'],
                                                'data-action' => $data['final_action'],
                                                'data-check' => "{$data['check_user']}"
                                            ]) : '';

//                                    return $check ? Html::tag('div', $num . "/" . $count_user . "<br/>" . $text, [
//                                                'class' => $class,
//                                                'data-name' => "{$data['final_name']}",
//                                                'data-role' => "{$data['final_role']}",
//                                                'data-check' => "{$data['check_user']}"
//                                            ]) : '';
                                },
                                'headerOptions' => ['class' => "text-center"],
                                'contentOptions' => ['style' => "min-width:100px;", 'class' => 'text-center'],
                                'filter' => $var,
                            ];
//            } else if ($var == 'owner') {
//
//                $colTmp = [
//                    'attribute' => $var,
//                    'label' => $label,
//                    'format' => 'raw',
//                    'value' => function ($data) use($dataInput, $var, $value) {
//                        $name = backend\modules\ezforms2\classes\MyWorkbenchFunc::GetUserName($data[$var]);
//                        return $name == '' ? '' : "<div style='margin-top:5px;' class='label label-primary'>" . $name['firstname'] . ' ' . $name['lastname'] . "</div>";
//                    },
//                    'headerOptions' => ['class' => "text-center"],
//                    'contentOptions' => ['style' => "min-width:100px;",'class' => "text-center"],
//                    'filter' => $var,
//                ];
                        } else if ($var == 'final_action') {

                            $colTmp = [
                                'attribute' => $var,
                                'label' => 'Action',
                                'format' => 'raw',
                                'value' => function ($data) use($dataInput, $value, $modelFields, $fieldName, $ezf_input) {
                                    foreach ($modelFields as $key => $field) {
                                        $var = $field['ezf_field_name'];
                                        $version = $field['ezf_version'];
                                        if ($fieldName == $var && ($data['ezf_version'] == $version || $version == 'all')) {
                                            if ($ezf_input) {
                                                $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($field['ezf_field_type'], $ezf_input);
                                            }
                                            $dataText = backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $field, $data);
                                            return $dataText == '' ? '' : "<div class='label label-info'>" . $dataText . "</div>";
                                        }
                                    }
                                    return '';
                                },
                                'headerOptions' => ['class' => "text-center"],
                                'contentOptions' => ['style' => "min-width:100px;", 'class' => "text-center"],
                                'filter' => $var,
                            ];
                        } else {
                            $colTmp = [
                                'attribute' => $var,
                                'label' => $label,
                                'format' => 'raw',
                                'value' => function ($data) use($dataInput, $value, $modelFields, $fieldName, $ezf_input) {
                                    foreach ($modelFields as $key => $field) {
                                        $var = $field['ezf_field_name'];
                                        $version = $field['ezf_version'];
                                        if ($fieldName == $var && ($data['ezf_version'] == $version || $version == 'all')) {

                                            if ($ezf_input) {
                                                $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($field['ezf_field_type'], $ezf_input);
                                            }
                                            $dataText = backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $field, $data);
                                            if ($fieldName == 'final_name' || $fieldName == 'final_role') {
                                                $data = explode(',', $dataText);
                                                $html = '';
                                                foreach ($data as $value) {
                                                    $html .= "<div style='margin-top:5px;' class='label label-primary'>" . $value . "</div><br/>";
                                                }
                                                return $html;
                                            } else {
                                                return $dataText == '' ? '' : $dataText;
                                            }
                                        }
                                    }
                                    return '';
                                },
                                'headerOptions' => ['class' => "text-center"],
                                'contentOptions' => ['style' => "min-width:100px;"],
                                'filter' => $var,
                            ];
                        }

                        if (is_array($field) && isset($field['attribute'])) {
                            $colTmp = \yii\helpers\ArrayHelper::merge($colTmp, $field);
                        }

                        $changeField = FALSE;
                        $columns[] = $colTmp;
                        break;
                    }
                }

                if ($changeField && $fieldName != 'F2v5' && $fieldName != 'date_version' && $fieldName != 'F2v6' && $fieldName != 'F2v9' && $fieldName != 'owner' && $fieldName != 'approve_status' && $fieldName != 'review' && $fieldName != 'approve' && $fieldName != 'acknowledge') {
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
            if ($default_column) {
                $columns[] = [
                    'attribute' => 'xsourcex',
                    'format' => 'raw',
                    'value' => function ($data) {
                        return "<span class=\"label label-success\" data-toggle=\"tooltip\" title=\"{$data['sitename']}\">{$data['xsourcex']}</span>";
                    },
                    'headerOptions' => ['style' => 'text-align: center;'],
                    'contentOptions' => ['style' => 'width:100px;text-align: center;'],
                ];
                $columns[] = [
                    'attribute' => 'userby',
                    'contentOptions' => ['style' => 'width:200px;'],
                    'filter' => '',
                ];
                $columns[] = [
                    'attribute' => 'rstat',
                    'format' => 'raw',
                    'value' => function ($data) {
                        $alert = 'label-default';
                        if ($data['rstat'] == 0) {
                            $alert = 'label-info';
                        } elseif ($data['rstat'] == 1) {
                            $alert = 'label-warning';
                        } elseif ($data['rstat'] == 2) {
                            $alert = 'label-success';
                        } elseif ($data['rstat'] == 3) {
                            $alert = 'label-danger';
                        }

                        $rstat = backend\modules\core\classes\CoreFunc::itemAlias('rstat', $data['rstat']);
                        return "<h4 style=\"margin: 0;\"><span class=\"label $alert\">$rstat</span></h4>";
                    },
                    'headerOptions' => ['style' => 'text-align: center;'],
                    'contentOptions' => ['style' => 'width:120px;text-align: center;'],
                    'filter' => Html::activeDropDownList($searchModel, 'rstat', backend\modules\core\classes\CoreFunc::itemAlias('rstat'), ['class' => 'form-control', 'prompt' => 'All']),
                ];
            }
            ?>

            <?=
            \appxq\sdii\widgets\GridView::widget([
                'id' => "$reloadDiv-sub-grid",
                'panelBtn' => !$disabled ? \backend\modules\ezforms2\classes\BtnBuilder::btn()
                                ->ezf_id($ezf_detail_id)
                                ->initdata(['owner' => Yii::$app->user->id])
                                ->target($data_id)
                                ->reloadDiv($reloadDiv)
                                ->modal($subModal)
                                ->label('<span class="glyphicon glyphicon-plus"></span> ' . Yii::t('tmf', 'Add Doc Detail'))
                                ->version('v1')
                                ->buildBtnAdd() : '',
                'dataProvider' => $dataProvider,
//                'filterModel' => $searchModel,
                'columns' => $columns,
            ]);

//            $subModal2 = '';
            $submodal = '<div id="' . $subModal . '" class="fade modal" role="dialog"><div class="modal-dialog modal-xxl"><div class="modal-content"></div></div></div>';
            ?>

            <?php
            $this->registerJs("
    
    var hasMyModal = $( 'body' ).has( '#$subModal' ).length;
        
//    if($('body .modal').hasClass('in')){
        if(!hasMyModal){
            $('#ezf-modal-box').append('$submodal');
        }
//    } 
    

    $('#$subModal').on('hidden.bs.modal', function(e){
            $('#$subModal .modal-content').html('');
            if($('body .modal').hasClass('in')){
                $('body').addClass('modal-open');
               $('#$subModal').remove();
                getUiAjax($('#$reloadDiv-sub-view').attr('data-url')+'&type_id=$type_id&data_id=$data_id', '$reloadDiv-sub-view');
            } 
    });
    
    

//    $('#$reloadDiv').attr('data-url',$('#$reloadDiv').attr('data-url')+'&type_id='+'$type_id');

    $('#$reloadDiv-sub-grid tbody tr td a').on('click', function() {
    
        var url = $(this).attr('href');
        var action = $(this).attr('data-action');

        if(action === 'update' || action === 'create'){
            $('#$subModal .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
            $('#$subModal').modal('show')
            .find('.modal-content')
            .load(url);
        } else if(action === 'view') {
            $('#$subModal .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
            $('#$subModal').modal('show')
            .find('.modal-content')
            .load(url);
        } else if(action === 'delete') {
            yii.confirm('" . Yii::t('app', 'Are you sure you want to delete this item?') . "', function(){
                    $.post(
                            url, {'_csrf':'" . Yii::$app->request->getCsrfToken() . "'}
                    ).done(function(result){
                            if(result.status == 'success'){
                                    " . SDNoty::show('result.message', 'result.status') . "
                                var urlreload =  $('#$reloadDiv-sub-view').attr('data-url')+'&type_id=$type_id&data_id=$data_id';        
                                getUiAjax(urlreload, '$reloadDiv-sub-view'); 
                                var urlMain = $('#$reloadDiv').attr('data-url')+'&type_id=$type_id'; 
                                getUiAjax(urlMain, '$reloadDiv');  
                            } else {
                                    " . SDNoty::show('result.message', 'result.status') . "
                            }
                    }).fail(function(){
                            " . SDNoty::show("'" . "Server Error'", '"error"') . "
                            console.log('server error');
                    });
            });
        }
        return false;
    });

$('#$reloadDiv-sub-grid').on('beforeFilter', function(e) {
    var \$form = $(this).find('form');
    $.ajax({
	method: 'GET',
	url: \$form.attr('action')+'&type_id=$type_id&data_id=$data_id',
        data: \$form.serialize(),
	dataType: 'HTML',
	success: function(result, textStatus) {
	    $('#$reloadDiv-sub-view').html(result);
	}
    });
    return false;
});

$('#$reloadDiv-sub-grid .pagination a').on('click', function() {
    getUiAjax($(this).attr('href')+'&type_id=$type_id&data_id=$data_id', '$reloadDiv-sub-view');
    return false;
});

$('#$reloadDiv-sub-grid thead tr th a').on('click', function() {
    getUiAjax($(this).attr('href')+'&type_id=$type_id&data_id=$data_id', '$reloadDiv-sub-view');
    return false;
});

$('#$reloadDiv-sub-grid tbody tr').on('mouseover', function() {
    $(this).css('cursor','pointer');
});

$('#$reloadDiv-sub-grid tbody tr').on('dblclick', function() {
    var hasTagA = $(this).has('.btnDetail').length;
    if(hasTagA){
        var url = $(this).children('td').children('.btnDetail').attr('href');
        $('#$subModal .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
        $('#$subModal').modal('show')
        .find('.modal-content')
        .load(url);
    }
});

$('#$reloadDiv .tabHeader').on('click',function(){
    var url = $(this).attr('data-url')+'&type_id='+$(this).attr('data-id');
    getUiAjax(url, '$reloadDiv');
//    return false;
});



$('#$reloadDiv-sub-grid .btnViewAss').on('click',function(){
//    var name = $(this).attr('data-name');
//    var role = $(this).attr('data-role');
//    var check = $(this).attr('data-check');
//    var url = '/tmf/tmf/view-assign?data-name='+name+'&data-role='+role+'&data-check='+check;
    var id = $(this).attr('data-id');
    var approve = $(this).attr('data-approve');
    var check = $(this).attr('data-check');
    var action = $(this).attr('data-action');
    var url = '/tmf/tmf/view-assign?data-id='+id+'&data-check='+check+'&data-approve='+approve+'&data-action='+action;
    viewAssign(url);
});





");
            ?>

            <div class="modal-footer" >


                <?= Html::button('<i class="glyphicon glyphicon-remove"></i> ' . Yii::t('app', 'Close'), ['class' => 'btn btn-default', 'data-dismiss' => 'modal']) ?>    
            </div>
        </div>