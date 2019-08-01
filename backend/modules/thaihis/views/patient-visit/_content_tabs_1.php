<?php

use yii\helpers\Html;
use backend\modules\ezforms2\classes\EzfHelper;
use kartik\tabs\TabsX;

$custom_css = $this->registerCss("
    
    .nav-tabs {
        border-bottom: 0 solid #ddd;
    }
    .tab-primary{
        color:#ffffff;
        border-color: #ddd;
    }
    
    .tab-active {
        background-color: #ffffff;
        color:#337ab7;
	border-color: #ddd;
	border-bottom-color: transparent;
    }
    .nav > .nav-item > a:focus {
        background-color: #ffffff;
    }
");

$template_h = '<div id="box-{id}" class="row">
                    <div class="col-md-4 sdcol-label">
                      <strong>{label}</strong>
                    </div>
                    <div class="col-md-8 sdbox-col">
                      {value}
                    </div>
               </div>';

$template_v = '<dl id="{id}">
                    <dt>{label}</dt>
                    <dd>{value}</dd>
               </dl>';

$template_item;
$user_id = Yii::$app->user->id;
$dept = Yii::$app->user->identity->profile->department;

if (isset($options['display'])) {
    if ($options['display'] == 'content_v') {
        $template_item = $template_v;
    } elseif ($options['display'] == 'content_h') {
        $template_item = $template_h;
    } else {
        $template_item = '<span id="box-{id}">{value}</span>';
    }
} else {
    $template_item = $template_h;
}

//start content
$column = isset($options['column']) ? $options['column'] : 2;
$readonly = isset($options['readonly']) ? $options['readonly'] : 0;
$disabled = isset($options['disabled']) ? $options['disabled'] : 0;
$disabled_box = isset($options['disabled_box']) ? $options['disabled_box'] : 0;

$js_ajax_content = '';
$content = '';
$firstIndex = null;
if (isset($tabs) && is_array($tabs)) {
    foreach ($tabs as $keyField => $valTab) {
        if ($firstIndex == null)
            $firstIndex = $keyField;
        else
            continue;
    }
}
$warning_btn = '';
if (isset($options['warn_enabled']) && $options['warn_enabled'] == 1) {
    $ezfWarn = backend\modules\ezforms2\classes\EzfQuery::getEzformOne($options['warn_ezf_id']);
    $dataWarn = \backend\modules\patient\classes\PatientFunc::loadTbDataByTarget($ezfWarn['ezf_table'], $target);

    $btn_warn = EzfHelper::btn($options['warn_ezf_id'])->label('<i class="fa fa-warning"></i>')->target($target)->options(['class' => 'btn btn-success btn-sm'])->buildBtnAdd();
    $btnClass = 'success';
    if ($dataWarn) {
        switch ($dataWarn['wn_level']) {
            case '1':
                $btnClass = 'warning';
                break;
            case '2':
                $btnClass = 'error';
                break;
            case '3':
                $btnClass = 'error';
                break;
            default:
                $btnClass = 'success';
                break;
        }

        $btn_warn = EzfHelper::btn($options['warn_ezf_id'])->label('<i class="fa fa-warning"></i>')->options(['class' => 'btn btn-' . $btnClass . ' btn-sm'])->buildBtnEdit($dataWarn['id']);
    }
    if ($readonly == 0)
        $warning_btn .= "<span class='pull-right'>" . '<label>' . Yii::t('thaihis', 'เตือน') . '</label> ' . $btn_warn . "</span>";
}

$subcontent = '';
$template_subcontent = '';
if (isset($tabs) && is_array($tabs)) {
    foreach ($tabs as $keyField => $valTab) {
        $check_con = true;
        if (isset($valTab['condition_display']) && $valTab['condition_display'] == '1') {
            $check_con = false;
            eval('$param_con = $' . $valTab['param_name'] . ';');
            if ($valTab['condition'] == 1 && $param_con == $valTab['value']) {
                $check_con = true;
            } else if ($valTab['condition'] == 2 && $param_con > $valTab['value']) {
                $check_con = true;
            } else if ($valTab['condition'] == 3 && $param_con < $valTab['value']) {
                $check_con = true;
            } else if ($valTab['condition'] == 4 && $param_con != $valTab['value']) {
                $check_con = true;
            }
        }
        if (!$check_con) {
            continue;
        }

        $template_content = "";

        $path_items = [
            "{update_date}" => (isset($model_tabs[$keyField]['update_date']) && !empty($model_tabs[$keyField]['update_date']) ? '<i class="glyphicon glyphicon-calendar"></i> ' . appxq\sdii\utils\SDdate::mysql2phpDateTime($model_tabs[$keyField]['update_date']) : ''),
        ];

        // Tab type ajax requeset ======================================
        if ($valTab['tab_type'] == '4') { // Tab type ajax requeset
            if (isset($valTab['url_request']) && $valTab['url_request'] != null) {
                if (isset($valTab['url_target_blank']) && $valTab['url_target_blank'] == '1')
                    continue;
                $url = yii\helpers\Url::to([$valTab['url_request'],
                            'visit_id' => $visitid,
                            'reloadDiv' => $reloadDiv,
                ]);
                $template_content .= "<div id='show-content-tab$keyField-$key_gen'>";

                $template_content .= "</div>";
                $js_ajax_content .= "
                var div = $('#show-content-tab$keyField-$key_gen');
                div.html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
                $.get('$url',function(result){
                    div.empty();
                    div.html(result);
                });
            ";
            }
        } else if ($valTab['tab_type'] == '2') { //Tab type widget
            $sub_widget_id = $valTab['widget_id'];
            $widget_ops = backend\modules\subjects\classes\SubjectManagementQuery::getWidgetById($sub_widget_id);
            $template_content .= "<div id='show-content-tab$keyField-$widget_id'>";
            $template_content .= $this->render($widget_ops['widget_render'], ['widget_config' => $widget_ops, 'modal' => $modal]);
            $template_content .= "</div>";
            
            $js_ajax_content .="";
        } else {
            $pic_field = explode('.', $valTab['field_pic']);
            if (isset($pic_field[1]) && !empty($pic_field[1])) {
                $image_field = Html::img(Yii::getAlias('@storageUrl/images/nouser.png'), ['class' => 'media-object img-rounded', 'style' => 'width: 100px;']);
                if (isset($model_tabs[$keyField][$pic_field[1]]) && !empty($model_tabs[$keyField][$pic_field[1]])) {
                    $image_field = Html::img(Yii::getAlias('@storageUrl/ezform/fileinput/') . $model_tabs[$keyField][$pic_field[1]], ['class' => 'media-object img-rounded', 'style' => 'width: 100px;']);
                }

                $path_items["{{$options['image_field']}}"] = $image_field;
            }

            if (is_array($modelFields_tabs[$keyField])) {

                foreach ($modelFields_tabs[$keyField] as $key => $value) {

                    $fieldName = $value['ezf_field_name'];
                    $col = 12 / $column;
                    $template_content .= "<div class=\"col-md-$col\">{{$value['ezf_field_name']}}</div>";

                    $var = $value['ezf_field_name'];
                    $label = $value['ezf_field_label'];

                    $dataInput;
                    if (isset(Yii::$app->session['ezf_input'])) {
                        $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($value['ezf_field_type'], Yii::$app->session['ezf_input']);
                    }

                    if (isset($valTab['template_content']) && strlen($valTab['template_content']) > 0) {
                        $template_item = '<span id="box-{id}">{value}</span>';
                    } else {
                        $template_item = $template_h;
                    }
                    $bdate_name = [];
                    if (isset($valTab['field_bdate']) && $valTab['field_bdate'] != '') {
                        $bdate_name = explode('.', $valTab['field_bdate']);
                    }

                    if (isset($bdate_name[1]) && $bdate_name[1] != '' && $fieldName == $bdate_name[1]) {
                        $conDate = backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $value, $model_tabs[$keyField]);
                        $path_data = [
                            '{id}' => Html::getInputId($model_tabs[$keyField], $fieldName),
                            '{label}' => $label,
                            '{value}' => isset($conDate) && $conDate != '' ? appxq\sdii\utils\SDdate::mysql2phpThDateSmall($conDate) : '',
                        ];

                        $path_items["{{$fieldName}}"] = strtr($template_item, $path_data);
                    } else if ($fieldName == 'right_status') { // สถานะของสิทธื์
                        $right_value = backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $value, $model_tabs[$keyField]);
                        $label_val = '';
                        if (!$right_value) {
                            $label_val = "<label style='font-size:14px;' class='label label-danger'>ไม่พบข้อมูลสิทธ์ กรุณาติดต่อห้องตรวจสิทธิ์!</label>";
                        } else {

                            if ($model_tabs[$keyField][$fieldName] == '1' || $model_tabs[$keyField][$fieldName] == '2') {
                                $label_val = "<label style='font-size:14px;' class='label label-success'>" . $right_value . "</label>";
                            } else {
                                $label_val = "<label style='font-size:14px;' class='label label-danger'>" . $right_value . "</label>";
                            }
                        }


                        $path_data = [
                            '{id}' => Html::getInputId($model_tabs[$keyField], $fieldName),
                            '{label}' => $label,
                            '{value}' => $label_val,
                        ];

                        $path_items["{{$fieldName}}"] = strtr($template_item, $path_data);
                    } else if ($fieldName == 'pt_drug_status') { // สถานะการแพ้ยา
                        $label_val = "text-primary";
                        if (isset($model_tabs[$keyField][$fieldName]) && $model_tabs[$keyField][$fieldName] == '2') {
                            $label_val = "text-danger";
                        }
                        $path_items["{style_disease}"] = $label_val;

                        $path_data = [
                            '{id}' => Html::getInputId($model_tabs[$keyField], $fieldName),
                            '{label}' => $label,
                            '{value}' => backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $value, $model_tabs[$keyField]),
                        ];

                        $path_items["{{$fieldName}}"] = strtr($template_item, $path_data);
                    } else {
                        $path_data = [
                            '{id}' => Html::getInputId($model_tabs[$keyField], $fieldName),
                            '{label}' => $label,
                            '{value}' => backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $value, $model_tabs[$keyField]),
                        ];

                        $path_items["{{$fieldName}}"] = strtr($template_item, $path_data);
                    }
                }
            }

            if (isset($valTab['field_bdate']) && $valTab['field_bdate'] != '') {
                $bdate_name = explode('.', $valTab['field_bdate']);
                $path_data = [
                    '{id}' => 'ezf-age',
                    '{label}' => '',
                    '{value}' => \backend\modules\thaihis\classes\ThaiHisQuery::calAge($model_tabs[$keyField][$bdate_name[1]]),
                ];
                $path_items["{age}"] = strtr($template_item, $path_data);
            }

            if (isset($valTab['variables']) && is_array($valTab['variables'])) {
                $target_require = "";
                foreach ($valTab['variables'] as $keyVar => $valVar) {
                    if ($valVar['require'] == 'visit') {
                        $target_require = $visitid;
                    } else if ($valVar['require'] == 'patient') {
                        $target_require = $target;
                    }

                    $label = '<i class="fa ' . (isset($valVar['icon']) ? $valVar['icon'] : '') . '"></i>' . ' ' . (isset($valVar['label']) ? $valVar['label'] : '');

                    $btn_option = ['class' => 'btn ' . (isset($valVar['themes']) ? $valVar['themes'] . ' btn-sm' : 'btn-default btn-sm')];
                    $btn_action = '';
                    if ($readonly == 0) {
                        if (isset($valVar['ezf_id']) && $valVar['ezf_id'] != '') {
                            $ezformVar = backend\modules\ezforms2\classes\EzfQuery::getEzformOne($valVar['ezf_id']);
                            $data_right = backend\modules\thaihis\classes\ThaiHisQuery::getTableData($ezformVar, ['target' => $target_require], 'one');

                            //if(isset($data_right['id'])){
                            if (isset($valVar['readonly']) && $valVar['readonly'] == '1') {
                                $btn_action = EzfHelper::btn($valVar['ezf_id'])->label($label)->target($target_require)->reloadDiv($reloadDiv)->options($btn_option)->buildBtnView($data_right['id']);
                            } else {
                                $btn_action = EzfHelper::btn($valVar['ezf_id'])->label($label)->target($target_require)->reloadDiv($reloadDiv)->options($btn_option)->buildBtnEdit($data_right['id']);
                            }
                            //}
                        } else {
                            if (isset($valVar['popup']) && $valVar['popup'] == '1') {
                                $btn_option['data-target'] = $target;
                                $btn_option['data-hn'] = $model_tabs[$keyField]['pt_hn'];
                                $btn_option['id'] = "btn_varpopup" . $keyVar;
                                $url = $valVar['url_target_link'] . "?target=" . $target . "&pt_hn=" . $model_tabs[$keyField]['pt_hn'];
                                $btn_action = Html::a($label, 'javascript:void(0)', $btn_option);

                                $js_ajax_content .= "
                                    $('#{$btn_option['id']}').on('click',function(){
                                        $('#{$modal}').modal();
                                        $('#{$modal}').find('.modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
                                        $('#{$modal}').find('.modal-content').load('{$url}');
                                    });
                                ";
                            } else {
                                $btn_option['target'] = '_blank';
                                $btn_action = Html::a($label, $valVar['url_target_link'] . "?pt_id=" . $target, $btn_option);
                            }
                        }
                    }
                    $path_data = [
                        '{id}' => $valVar['var_name'] . appxq\sdii\utils\SDUtility::getMillisecTime(),
                        '{label}' => '',
                        '{value}' => $btn_action,
                    ];

                    $path_items["{" . $valVar['var_name'] . "}"] = strtr($template_item, $path_data);
                }
            }

            if (isset($valTab['template_content']) && !empty($valTab['template_content'])) {
                $template_content = $valTab['template_content'];
            }
        }

        if ($firstIndex == $keyField) {
            $tab_content = '<div class="tab-pane fade in active" id="content-tab' . $key_gen . $keyField . '" role="tabpanel" aria-labelledby="nav-tab' . $keyField . '-tab">';
        } else {
            $tab_content = '<div class="tab-pane" id="content-tab' . $key_gen . $keyField . '" role="tabpanel" aria-labelledby="nav-tab' . $keyField . '-tab">';
        }

        if (isset($valTab['field_pic']) && $valTab['field_pic'] != '') {
            $template_content = $tab_content . "<div class=\"media\"> 
                                <div class=\"media-left\"> 
                                      $image_field
                                </div> 
                                <div class=\"media-body\"> 
                                      $template_content
                                </div> 
                        </div>";
        } else {
            $template_content = $tab_content . "<div > 
                                      $template_content
                                </div>";
        }
        $subcontent = isset($valTab['subcontent']) ? $valTab['subcontent'] : null;
        $subaction = '';
        $template_subcontent = '';
//            $template_subcontent = '<div class="clearfix"></div><br/>';
        if ($subcontent && is_array($subcontent)) {
            $template_subcontent .= '<table class="table" style="margin-bottom: 0px"><tbody>';
            foreach ($subcontent as $keySub => $valSub) {
                $not_require = isset($valSub['not_require_data']) ? $valSub['not_require_data'] : 0;
                if ((isset($valTab['require_data_subc']) && ($valTab['require_data_subc'] == '1'))) {
                    if (($not_require == 0 && empty($model_tabs[$keyField]['dataid'])))
                        continue;
                }

                $template_subcontent .= '<tr><td>';
                $template_sub = "<span class=\"text-info\" id='{id}'>{value}</span>";
                $modelField_subc = \backend\modules\ezforms2\classes\EzfQuery::getFieldByName($valSub['ezf_id'], $valSub['field']);
                $dataInput;
                if (isset(Yii::$app->session['ezf_input'])) {
                    $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($modelField_subc['ezf_field_type'], Yii::$app->session['ezf_input']);
                }

                $diData = null;
                $target_action = '';
                if (isset($valSub['target_name'])) {
                    if ($valSub['target_name'] == 'id' || !isset($valSub['target_name'])) {
                        $target_action = $diData['id'];
                    } else {
                        eval('$target_action=' . '$' . $valSub['target_name'] . ';');
                    }
                } else {
                    $target_action = $diData['id'];
                }

                if ($valSub['title'] == 'Staging') {
                    $template_sub = "<span id='{id}'>{value}</span>";
                    $diEzform = backend\modules\ezforms2\classes\EzfQuery::getEzformOne($valTab['main_ezf_id']);
                    $diData = backend\modules\subjects\classes\SubjectManagementQuery::GetTableData($diEzform, ['target' => $visitid], 'one', null, ['column' => 'create_date', 'type' => 'desc']);
                    $subEzform = backend\modules\ezforms2\classes\EzfQuery::getEzformOne($modelField_subc['ezf_id']);
                    $dataSub = backend\modules\subjects\classes\SubjectManagementQuery::GetTableData($subEzform, ['target' => $target_action], 'one');

                    $staging = '';
                    $tnm = [];
                    $subaction = '';
                    if ($dataSub) {
                        $path_data = [
                            '{id}' => appxq\sdii\utils\SDUtility::getMillisecTime(),
                            '{value}' => backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $modelField_subc, $dataSub),
                        ];

                        $staging = strtr($template_sub, $path_data);
                        $tnm = explode('-', $dataSub['staging_tnm']);
                    }


                    $template_subcontent .= '<label style="font-size:20px;">' . $valSub['title'] . ' </label> <label class="label label-warning" style="font-size:20px;"> ' . $staging . ' </label> ';
                    $template_subcontent .= ' <label style="font-size:20px;"> T </label> <label class="label label-primary" style="font-size:20px;"> ' . (isset($tnm[0]) ? $tnm[0] : ' ') . '</label>';
                    $template_subcontent .= ' <label style="font-size:20px;"> N </label> <label class="label label-primary" style="font-size:20px;">' . (isset($tnm[1]) ? $tnm[1] : ' ') . '</label>';
                    $template_subcontent .= ' <label style="font-size:20px;"> M </label> <label class="label label-primary" style="font-size:20px;">' . (isset($tnm[2]) ? $tnm[2] : ' ') . ' </label> ';

                    $template_subcontent .= '<div class="pull-right">';
                    if ($readonly == 0) {
                        if (isset($valSub['ezf_id']) && $valSub['ezf_id'] != '') {

                            $subaction = EzfHelper::btn($valSub['ezf_id'])->label("<i class='fa fa-plus'></i>")->reloadDiv($reloadDiv)->options(['class' => 'btn btn-success btn-sm'])->target($target_action)->buildBtnAdd();
                            if ($dataSub) {
                                $subaction = EzfHelper::btn($valSub['ezf_id'])->label("<i class='fa fa-pencil'></i>")->reloadDiv($reloadDiv)->options(['class' => 'btn btn-primary btn-sm'])->buildBtnEdit($dataSub['id']);
                            }
                        }
                    }
                } else {
                    $dataSub = backend\modules\thaihis\classes\ThaiHisQuery::getQueryGroupConcat($valSub['ezf_id'], $target_action, $valSub['field']);
                    $valueList = [];
                    foreach ($dataSub as $keyDat => $valDat) {
                        $path_data = [
                            '{id}' => appxq\sdii\utils\SDUtility::getMillisecTime(),
                            '{value}' => backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $modelField_subc, $valDat),
                        ];
                        $valueList[] = strtr($template_sub, $path_data);
                    }


                    $template_subcontent .= '<label>' . $valSub['title'] . ': </label> ';

                    $template_subcontent .= join($valueList, ',');

                    $template_subcontent .= '<div class="pull-right">';

                    if ($readonly == 0) {
                        if (isset($valSub['ezf_id']) && $valSub['ezf_id'] != '') {

                            $subaction = EzfHelper::btn($valSub['ezf_id'])->label("<i class='fa fa-plus'></i>")->reloadDiv($reloadDiv)->options(['class' => 'btn btn-success btn-sm'])->target($target_action)->buildBtnAdd();
                            if ($dataSub) {
                                $subaction .= ' ' . EzfHelper::btn($valSub['ezf_id'])->label("<i class='fa fa-bars'></i>")->reloadDiv($reloadDiv)->options(['class' => 'btn btn-info btn-sm'])->target($target_action)->buildBtnGrid();
                            }
                        }
                    }
                }


                $template_subcontent .= $subaction;
                $template_subcontent .= '</div>';
//                    $template_subcontent .= '<div class="clearfix"></div><hr/>';
                $template_subcontent .= '</td></tr>';
            }
            $template_subcontent .= '</tbody></table>';
        }

        $template_content .= $template_subcontent;
        $template_content .= '</div>';


        $content .= strtr($template_content, $path_items);
    }
}

//end content
//start box content
$title = isset($options['title']) ? $options['title'] : '';

$template_box = isset($options['template_box']) ? $options['template_box'] : '';
$addon = isset($options['addon']) ? $options['addon'] : 0;
$theme = isset($options['theme']) ? $options['theme'] : 'primary';

if (count($tabs) > 1) {
    $title = '<ul class="nav nav-tabs navtabs-' . $key_gen . '" role="tablist" >';

    foreach ($tabs as $key => $val) {

        $check_con = true;
        if (isset($val['condition_display']) && $val['condition_display'] == '1') {

            $check_con = false;
            eval('$param_con = $' . $val['param_name'] . ';');
            if ($val['condition'] == 1 && $param_con == $val['value']) {
                $check_con = true;
            } else if ($val['condition'] == 2 && $param_con > $val['value']) {
                $check_con = true;
            } else if ($val['condition'] == 3 && $param_con < $val['value']) {
                $check_con = true;
            } else if ($val['condition'] == 4 && $param_con != $val['value']) {
                $check_con = true;
            }
        }
        if (!$check_con) {
            continue;
        }

        if ($val['tab_type'] == '4') { // Tab type ajax requeset
            if (isset($val['url_request']) && $val['url_request'] != null) {
                $url = $val['url_request'];

                if (isset($val['url_target_blank']) && $val['url_target_blank'] == '1')
                    $title .= '<li class="nav-item"><a href="' . $url . '" target="_blank" class="tab-primary" id="btn-tab' . $key_gen . $key . '"  key_index="' . $key . '">' . (isset($val['icon']) ? '<i class="fa ' . $val['icon'] . '"></i>' : '') . ' ' . $val['tab_title'] . '</a></li>';
                else
                    $title .= '<li class="nav-item"><a href="#tab' . $key_gen . $key . '" class=" tab-primary btn-tabs' . $key_gen . '"  id="btn-tab' . $key_gen . $key . '" key_index="' . $key . '" >' . (isset($val['icon']) ? '<i class="fa ' . $val['icon'] . '"></i>' : '') . ' ' . $val['tab_title'] . '</a></li>';
            }
        } else {
            $icon = "";
            if ($val['main_ezf_id'] == '1504661230056849800' && isset($model_tabs[$key]['id']) && $model_tabs[$key]['id'] != '') {
                $icon = '<label style="font-size:16px;" class="label label-success"><i class="fa fa-check-circle" aria-hidden="true"></i></label>';
            }

            if ($key == $firstIndex)
                $title .= '<li class="nav-item"><a href="#tab' . $key_gen . $key . '" class=" tab-primary tab-active btn-tabs' . $key_gen . '"  id="btn-tab' . $key_gen . $key . '" key_index="' . $key . '" >' . (isset($val['icon']) ? '<i class="fa ' . $val['icon'] . '"></i>' : '') . ' ' . $val['tab_title'] . ' ' . $icon . '</a>  </li>';
            else
                $title .= '<li class="nav-item"><a href="#tab' . $key_gen . $key . '" class="tab-primary btn-tabs' . $key_gen . '" id="btn-tab' . $key_gen . $key . '"  key_index="' . $key . '">' . (isset($val['icon']) ? '<i class="fa ' . $val['icon'] . '"></i>' : '') . ' ' . $val['tab_title'] . ' ' . $icon . '</a>  </li>';
        }
    }
}else {
    if (isset($tabs[$firstIndex]['icon']))
        $title = '<h4><i class="fa ' . $tabs[$firstIndex]['icon'] . '"></i>' . ' ' . $tabs[$firstIndex]['tab_title'] . '</h4>';
    else
        $title = '<h4>' . (isset($tabs[$firstIndex]['tab_title']) ? $tabs[$firstIndex]['tab_title'] : "Unknow name") . '</h4>';
}
$title .= '</ul>';
$js_ajax_popup = '';

$manage_state = false;
if (isset($options['doctor_can']) && $options['doctor_can'] == '1') {
    if (Yii::$app->user->can('doctor')) {
        $manage_state = true;
    } else {
        $manage_state = false;
    }
} else {
    $manage_state = true;
}

if (!$disabled) {

    $path_display = [
        '{content}' => $content,
        '{theme}' => $theme,
        '{title}' => $title,
        '{warning_btn}' => $warning_btn,
    ];
    $template_action = '';
    $template_action_view = '';
    if ($manage_state) {
        if (isset($tabs) && is_array($tabs)) {
            foreach ($tabs as $keyField => $valTab) {
                $edit_own = false;
                if (isset($valTab['edit_data_own']) && $valTab['edit_data_own'] == '1') {
                    $edit_own = true;
                }
                $action = isset($valTab['action']) && !empty($valTab['action']) ? $valTab['action'] : ['create', 'update', 'view', 'delete'];
                if ($keyField == $firstIndex) {
                    $template_action .= '<div id="action_tab_' . $key_gen . $keyField . '">';
                    $template_action_view .= '<div id="action_tab_' . $key_gen . $keyField . '">';
                } else {
                    $template_action .= '<div id="action_tab_' . $key_gen . $keyField . '" style="display:none;">';
                    $template_action_view .= '<div id="action_tab_' . $key_gen . $keyField . '">';
                }

                $initdata = [];
                if ($valTab['ezf_id'] == '1503378440057007100') {
                    $initdata = ['pt_cid' => $model_tabs[$keyField]['pt_cid']];
                }

                if ($valTab['tab_type'] == '4') {
                    foreach ($action as $value) {
                        if ($value == 'create') {
                            $template_action .= ' {create' . $keyField . '}';
                            $path_display['{create' . $keyField . '}'] = Html::button('<i class="fa fa-plus"></i>', ['class' => 'btn btn-success', 'id' => 'btn_popup' . $keyField . '-' . $widget_id]);
                            if (isset($valTab['url_request_popup']) && $valTab['url_request_popup'] != null) {
                                $url = yii\helpers\Url::to([$valTab['url_request_popup'],
                                            'visitid' => $visitid,
                                            'visit_type' => $visit_type,
                                            'target' => $target,
                                ]);

                                $js_ajax_popup .= "
                                $('#btn_popup$keyField-$key_gen').click(function(){
                                    $('#$modal').modal();
                                    $('#$modal').find('.modal-content').load('$url');
                                });
                            ";
                            }
                        }
                    }
                } else if ($valTab['tab_type'] == '2') {
                    foreach ($action as $value) {
                        if ($value == 'create') {
                            $template_action .= ' {create' . $keyField . '}';
                            $path_display['{create' . $keyField . '}'] = Html::button('<i class="fa fa-plus"></i>', ['class' => 'btn btn-success', 'id' => 'btn_popup' . $keyField . '-' . $key_gen]);
                            if (isset($valTab['custom_action']) && $valTab['custom_action'] != null) {
                                $widgetData = \backend\modules\subjects\classes\SubjectManagementQuery::getWidgetById($valTab['widget_id']);
                                $widget_option = \appxq\sdii\utils\SDUtility::string2Array($widgetData['options']);

                                $url = yii\helpers\Url::to([$valTab['custom_action'],
                                            'visitid' => $visitid,
                                            'visit_type' => $visit_type,
                                            'target' => $target,
                                            'modal' => $modal,
                                            'options' => backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String($widget_option),
                                ]);

                                $js_ajax_popup .= "
                                $('#btn_popup$keyField-$key_gen').click(function(){
                                    $('#$modal').modal();
                                    $('#$modal').find('.modal-content').html('');
                                    $('#$modal').find('.modal-content').load('$url');
                                });
                            ";
                            }
                        }
                    }
                } else if ($valTab['tab_type'] == '1') {

                    foreach ($action as $value) {
                        //create, update, delete, view, search

                        if ($value == 'create') {
                            if (isset($model_tabs[$keyField]['id']) && $valTab['main_ezf_id'] == '1504661230056849800') {
                                continue;
                            }

                            $template_action .= ' {create' . $keyField . '}';
                            if ($valTab['main_ezf_id'] == '1504661230056849800') {
                                $initdata = [
                                    'admit_doctor_user' => $user_id,
                                    'admit_from_dept' => Yii::$app->user->identity->profile->department,
                                    'admit_status' => '1'
                                ];
                            }


                            $path_display['{create' . $keyField . '}'] = EzfHelper::btn($valTab['main_ezf_id'])
                                    ->reloadDiv($reloadDiv)
                                    ->modal('modal-ezform-main')
                                    ->initdata($initdata)
                                    ->target($dataid)
                                    ->label('<i class="glyphicon glyphicon-plus"></i>')
                                    ->options(['class' => 'btn btn-success btn-sm'])
                                    ->buildBtnAdd();
                        } elseif ($value == 'update') {
                            if (($edit_own && $model_tabs[$keyField]['user_create'] != $user_id))
                                continue;
                            if (isset($model_tabs[$keyField]['id'])) {
                                $template_action .= ' {update' . $keyField . '}';
                                $path_display['{update' . $keyField . '}'] = '';

                                if (isset($model_tabs[$keyField]['id'])) {
                                    $path_display['{update' . $keyField . '}'] = EzfHelper::btn($valTab['main_ezf_id'])
                                            ->reloadDiv($reloadDiv)
                                            //->target($model_tabs[$keyField]['target'])
                                            ->targetField('target')
                                            ->modal('modal-ezform-main')
                                            ->label('<i class="glyphicon glyphicon-pencil"></i>')
                                            ->options(['class' => 'btn btn-primary btn-sm'])
                                            ->buildBtnEdit($model_tabs[$keyField]['dataid']);
                                }
                            }
                        } elseif ($value == 'delete') {
                            if (isset($model_tabs[$keyField]['id'])) {
                                $template_action .= ' {delete' . $keyField . '}';
                                $path_display['{delete' . $keyField . '}'] = '';
                                if (isset($model_tabs[$keyField]['id'])) {
                                    $path_display['{delete' . $keyField . '}'] = EzfHelper::btn($valTab['main_ezf_id'])
                                            ->reloadDiv($reloadDiv)
                                            ->target($model_tabs[$keyField]['target'])
                                            ->targetField('target')
                                            ->modal('modal-ezform-main')
                                            ->label('<i class="glyphicon glyphicon-trash"></i>')
                                            ->options(['class' => 'btn btn-danger btn-sm'])
                                            ->buildBtnDelete($model_tabs[$keyField]['dataid']);
                                }
                            }
                        } elseif ($value == 'view') {

                            if (isset($model_tabs[$keyField]['id'])) {
                                $template_action .= '{view' . $keyField . '}';
                                $path_display['{view' . $keyField . '}'] = EzfHelper::btn($valTab['main_ezf_id'])
                                        ->reloadDiv($reloadDiv)
                                        ->dataid($model_tabs[$keyField]['dataid'])
                                        ->targetField('target')
                                        ->target($model_tabs[$keyField]['target'])
                                        ->modal('modal-ezform-main')
                                        ->addbtn(0)
                                        ->readonly(0)
                                        ->popup(1)
                                        ->data_column($fields)
                                        ->label('<i class="glyphicon glyphicon-th-list"></i>')
                                        ->options(['class' => 'btn btn-default btn-sm'])
                                        ->buildBtnGrid();

                                $template_action_view .= '{view' . $keyField . '}';
                                $path_display['{view' . $keyField . '}'] = EzfHelper::btn($valTab['main_ezf_id'])
                                        ->reloadDiv($reloadDiv)
                                        ->dataid($model_tabs[$keyField]['dataid'])
                                        ->targetField('target')
                                        ->target($model_tabs[$keyField]['target'])
                                        ->modal('modal-ezform-main')
                                        ->addbtn(0)
                                        ->readonly(0)
                                        ->popup(1)
                                        ->data_column($fields)
                                        ->label('<i class="glyphicon glyphicon-th-list"></i>')
                                        ->options(['class' => 'btn btn-default btn-sm'])
                                        ->buildBtnGrid();
                            }
                        }
                    }
                }
                $template_action .= '</div>';
                $template_action_view .= '</div>';
            }
        }
    }
    $template_action_view = '';
    if (isset($tabs) && is_array($tabs)) {
        foreach ($tabs as $keyField => $valTab) {
            $action = isset($valTab['action']) && !empty($valTab['action']) ? $valTab['action'] : ['create', 'update', 'view', 'delete'];
            if ($keyField == $firstIndex) {
                $template_action_view .= '<div id="action_tab_' . $key_gen . $keyField . '">';
            } else {
                $template_action_view .= '<div id="action_tab_' . $key_gen . $keyField . '">';
            }

            $initdata = [];
            if ($valTab['ezf_id'] == '1503378440057007100') {
                $initdata = ['pt_cid' => $model_tabs[$keyField]['pt_cid']];
            }

            if ($valTab['tab_type'] == '1') {

                foreach ($action as $value) {
                    if ($value == 'view') {
                        $template_action_view .= '{view' . $keyField . '}';
                        $path_display['{view' . $keyField . '}'] = EzfHelper::btn($valTab['main_ezf_id'])
                                ->reloadDiv($reloadDiv)
                                ->dataid($model_tabs[$keyField]['dataid'])
                                ->targetField('target')
                                ->target($model_tabs[$keyField]['target'])
                                ->modal('modal-ezform-main')
                                ->addbtn(0)
                                ->readonly(0)
                                ->popup(1)
                                ->data_column($fields)
                                ->label('<i class="glyphicon glyphicon-th-list"></i>')
                                ->options(['class' => 'btn btn-default btn-sm'])
                                ->buildBtnGrid();
                    }
                }
            }
            $template_action_view .= '</div>';
        }
    }


    if ($readonly) {
        $template_action = $template_action_view;
    }

    if (isset($options['doctor_can']) && $options['doctor_can'] == '1') {
        if (!Yii::$app->user->can('doctor')) {
            $template_action = $template_action_view;
        }
    }

    $warning_content = "";

    if (isset($options['warn_enabled']) && $options['warn_enabled'] == '1') {
        $warning_content = '<div class="row"><div class="col-md-12">';
        $data_level = '';
//        $modelFieldWarn = \backend\modules\ezforms2\classes\EzfQuery::getFieldByName($options['warn_ezf_id'], $options['warn_level']);
//        $data_level = backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $modelFieldWarn, $dataWarn);        
//        \appxq\sdii\utils\VarDumper::dump($data_level);

        if ($dataWarn[$options['warn_text']]) {
            $warning_content .= '<div class="alert alert-' . $btnClass . ' alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>' . $data_level . ' !</strong>
                            ' . $dataWarn[$options['warn_text']] . '
                    </div>';
        }

        $warning_content .= ' {warning_btn}</div></div>';
    }

    if (empty($template_box)) {
        $template_box = '<div class="panel panel-{theme}">
            <div class="panel-heading" style="padding-bottom:0">
                <div class="row">
                    <div class="col-md-8">
                        {title}
                    </div>
                    <div class="col-md-4 text-right">
                        
                        ' . $template_action . '
                        
                    </div>
                  </div>
              </div>
              <div class="panel-body" >
                <div class="tab-content">
                    {content}
                     ' . $warning_content . '
                </div>
              </div>             
          </div>';
    }


    $display_box = strtr($template_box, $path_display);

    if ($disabled_box) {
        echo $content;
    } else {
        echo $display_box;
    }
}
//end box content
?>

<?php

$this->registerJs("
    $js_ajax_content
    $js_ajax_popup
    $('.btn-tabs$key_gen').on('click',function(){
        var key_gen = '$key_gen';
        var navtabs = 'navtabs-$key_gen';
        $(this).parents('.'+navtabs).find('.nav-item').each(function(i,e){
            $('#content-tab'+key_gen+''+$(e).children().attr('key_index')).removeClass('fade in active');
            $('#action_tab_'+key_gen+''+$(e).children().attr('key_index')).css('display','none');
            $(e).children().removeClass('tab-active');
        });
        
        $('#content-tab'+key_gen+''+$(this).attr('key_index')).addClass('fade in active');
        $('#action_tab_'+key_gen+''+$(this).attr('key_index')).css('display','block');
        $(this).addClass('tab-active');
    });
");
?>