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
$template_content = "";
$path_items = null;

$pic_field = explode('.', $valTab['field_pic']);
if (isset($pic_field[1]) && !empty($pic_field[1])) {
    $image_field = Html::img(Yii::getAlias('@storageUrl/images/nouser.png'), ['class' => 'media-object img-rounded', 'style' => 'width: 100px;']);
    if (isset($model_tabs[$pic_field[1]]) && !empty($model_tabs[$pic_field[1]])) {
        $image_field = Html::img(Yii::getAlias('@storageUrl/ezform/fileinput/') . $model_tabs[$pic_field[1]], ['class' => 'media-object img-rounded', 'style' => 'width: 100px;']);
    }

    if(isset($options['image_field']))
        $path_items["{{$options['image_field']}}"] = $image_field;
}

if (is_array($modelFields_tabs)) {

    foreach ($modelFields_tabs as $key => $value) {

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
            $conDate = backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $value, $model_tabs);
            $path_data = [
                '{id}' => Html::getInputId($model_tabs, $fieldName),
                '{label}' => $label,
                '{value}' => isset($conDate) && $conDate != '' ? appxq\sdii\utils\SDdate::mysql2phpThDateSmall($conDate) : '',
            ];

            $path_items["{{$fieldName}}"] = strtr($template_item, $path_data);
        } else if ($fieldName == 'right_status') { // สถานะของสิทธื์
            $right_value = backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $value, $model_tabs);
            $label_val = '';
            if (!$right_value) {
                $label_val = "<label style='font-size:14px;' class='label label-danger'>ไม่พบข้อมูลสิทธ์ กรุณาติดต่อห้องตรวจสิทธิ์!</label>";
            } else {

                if ($model_tabs[$fieldName] == '1' || $model_tabs[$fieldName] == '2') {
                    $label_val = "<label style='font-size:14px;' class='label label-success'>" . $right_value . "</label>";
                } else {
                    $label_val = "<label style='font-size:14px;' class='label label-danger'>" . $right_value . "</label>";
                }
            }


            $path_data = [
                '{id}' => Html::getInputId($model_tabs, $fieldName),
                '{label}' => $label,
                '{value}' => $label_val,
            ];

            $path_items["{{$fieldName}}"] = strtr($template_item, $path_data);
        } else if ($fieldName == 'pt_drug_status') { // สถานะการแพ้ยา
            $label_val = "text-primary";
            if (isset($model_tabs[$fieldName]) && $model_tabs[$fieldName] == '2') {
                $label_val = "text-danger";
            }
            $path_items["{style_disease}"] = $label_val;

            $path_data = [
                '{id}' => Html::getInputId($model_tabs, $fieldName),
                '{label}' => $label,
                '{value}' => backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $value, $model_tabs),
            ];

            $path_items["{{$fieldName}}"] = strtr($template_item, $path_data);
        } else {
            $path_data = [
                '{id}' => Html::getInputId($model_tabs, $fieldName),
                '{label}' => $label,
                '{value}' => backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $value, $model_tabs),
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
        '{value}' => \backend\modules\thaihis\classes\ThaiHisQuery::calAge($model_tabs[$bdate_name[1]]),
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
                    $btn_option['data-hn'] = $model_tabs['pt_hn'];
                    $btn_option['id'] = "btn_varpopup" . $keyVar;
                    $url = $valVar['url_target_link'] . "?target=" . $target . "&pt_hn=" . $model_tabs['pt_hn'];
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

$tab_content = "";
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

if ($subcontent && is_array($subcontent)) {
    $template_subcontent .= '<table class="table" style="margin-bottom: 0px"><tbody>';
    foreach ($subcontent as $keySub => $valSub) {
        $not_require = isset($valSub['not_require_data']) ? $valSub['not_require_data'] : 0;
        if ((isset($valTab['require_data_subc']) && ($valTab['require_data_subc'] == '1'))) {
            if (($not_require == 0 && empty($model_tabs['dataid'])))
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
        $template_subcontent .= '</td></tr>';
    }
    $template_subcontent .= '</tbody></table>';
}

$template_content .= $template_subcontent;
$template_content .= '</div>';

if ($path_items != null)
    $content .= strtr($template_content, $path_items);

//end content
//start box content
$title = isset($options['title']) ? $options['title'] : '';

$template_box = isset($options['template_box']) ? $options['template_box'] : '';
$addon = isset($options['addon']) ? $options['addon'] : 0;
$theme = isset($options['theme']) ? $options['theme'] : 'primary';

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

$path_display = [
    '{content}' => $content,
    '{warning_btn}' => $warning_btn,
];


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

$update_date_display = '';
if (isset($model_tabs['update_date']) && $model_tabs['update_date']) {
    $path_display['{update_date}'] = '<i class="glyphicon glyphicon-calendar"></i> '
            . appxq\sdii\utils\SDdate::mysql2phpDateTime($model_tabs['update_date']);
    $path_display['{user_update}'] = '<i class="fa fa-user"></i> '
            . \backend\modules\ezforms2\classes\EzfQuery::getUserProfile($model_tabs['user_update'])['fullname'];

    $update_date_display = '<div class="row">
                        <div class="col-md-12 text-right" style="font-size: 10px;">
                            {update_date} {user_update}
                        </div>
                    </div>';
}

if (empty($template_box)) {
    $template_box = '<div class="content">
        ' . $update_date_display . '
                        {content}
                        ' . $warning_content . '          
                    </div>';
}


$display_box = strtr($template_box, $path_display);
echo $display_box;

//end box content
?>

<?php

$this->registerJs("
    $js_ajax_content

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