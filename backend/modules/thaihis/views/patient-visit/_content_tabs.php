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
        if ($firstIndex == null) {
            if (isset($valTab['dept_display']) && $valTab['dept_display'] == '1') {
                if (in_array($dept, $valTab['dept_list'])) {
                    $firstIndex = $keyField;
                }
            } else {
                $firstIndex = $keyField;
            }
        } else {
            continue;
        }
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

//end content
//start box content
$title = isset($options['title']) ? $options['title'] : '';

$template_box = isset($options['template_box']) ? $options['template_box'] : '';
$addon = isset($options['addon']) ? $options['addon'] : 0;
$theme = isset($options['theme']) ? $options['theme'] : 'primary';

if (count($tabs) > 1) {
    $style = isset($options['style_content']) ? 'style="' . $options['style_content'] . '"' : '';

    $title = '<ul class="nav nav-tabs navtabs-' . $key_gen . '" role="tablist" >';

    foreach ($tabs as $key => $val) {
        $url = '';
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
                    $title .= '<li class="nav-item"><a ' . $style . ' href="' . $url . '" target="_blank" class="tab-primary" id="btn-tab' . $key_gen . $key . '"  key_index="' . $key . '">' . (isset($val['icon']) ? '<i class="fa ' . $val['icon'] . '"></i>' : '') . ' ' . $val['tab_title'] . '</a></li>';
                else
                    $title .= '<li class="nav-item"><a ' . $style . ' href="#tab' . $key_gen . $key . '" class=" tab-primary btn-tabs' . $key_gen . '"  id="btn-tab' . $key_gen . $key . '" key_index="' . $key . '" data-url="' . $url . '" >' . (isset($val['icon']) ? '<i class="fa ' . $val['icon'] . '"></i>' : '') . ' ' . $val['tab_title'] . '</a></li>';
            }
        } else {

            $icon = "";
            if ($val['main_ezf_id'] == '1504661230056849800' && isset($model_tabs[$key]['id']) && $model_tabs[$key]['id'] != '') {
                $icon = '<label style="font-size:16px;" class="label label-success"><i class="fa fa-check-circle" aria-hidden="true"></i></label>';
            }

            if ((isset($valTab['dept_display']) && $valTab['dept_display'] == '1') && (isset($model_tabs[$key]['id']) && $model_tabs[$key]['id'] != '')) {
                $icon = '<label style="font-size:16px;" class="label label-success" ><i class="fa fa-check-circle" aria-hidden="true"></i></label>';
            }

            // init tab
            if ($key == $firstIndex) {
                if ($val['tab_type'] == '1') {
                    $url = \yii\helpers\Url::to(["/thaihis/patient-visit/ezform-content", 'widget_id' => $widget_id, 'keyTab' => $key, 'visitid' => $visitid, 'target' => $target, 'visit_type' => $visit_type
                                , 'reloadDiv' => $reloadDiv, 'modal' => $modal, 'options' => \appxq\sdii\utils\SDUtility::array2String($options)]);
                } else {
                    $url = \yii\helpers\Url::to(["/thaihis/patient-visit/widget-content", 'widget_id' => $widget_id, 'keyTab' => $key, 'visitid' => $visitid, 'target' => $target, 'visit_type' => $visit_type
                                , 'reloadDiv' => $reloadDiv, 'modal' => $modal, 'options' => \appxq\sdii\utils\SDUtility::array2String($options)]);
                }
                $js_ajax_content .= "
                    $(function(){
                        var div_content = $('.tab-content-display$key_gen');
                        div_content.html('<div class=\"sdloader\"><i class=\"sdloader-icon\"></i></div>');
                        $.get('$url',{},function(result){
                            div_content.html(result);
                        });
                    });
                ";
                $js_ajax_content .= "
                    $('#btn-tab" . $key_gen . $key . "').click(function(){
                        var div_content = $('.tab-content-display$key_gen');
                        div_content.html('<div class=\"sdloader\"><i class=\"sdloader-icon\"></i></div>');
                        $.get('$url',{},function(result){
                            div_content.html(result);
                        });
                    });
                ";
                $title .= '<li class="nav-item"><a ' . $style . ' href="#tab' . $key_gen . $key . '" class=" tab-primary tab-active btn-tabs' . $key_gen . '"  id="btn-tab' . $key_gen . $key . '" key_index="' . $key . '" data-url="' . $url . '" >' . (isset($val['icon']) ? '<i class="fa ' . $val['icon'] . '"></i>' : '') . ' ' . $val['tab_title'] . ' ' . $icon . '</a>  </li>';
            } else {
                if ($val['tab_type'] == '1') {
                    $url = \yii\helpers\Url::to(["/thaihis/patient-visit/ezform-content", 'widget_id' => $widget_id, 'keyTab' => $key, 'visitid' => $visitid, 'target' => $target, 'visit_type' => $visit_type
                                , 'reloadDiv' => $reloadDiv, 'modal' => $modal, 'options' => \appxq\sdii\utils\SDUtility::array2String($options)]);
                } else {
                    $url = \yii\helpers\Url::to(["/thaihis/patient-visit/widget-content", 'widget_id' => $widget_id, 'keyTab' => $key, 'visitid' => $visitid, 'target' => $target, 'visit_type' => $visit_type
                                , 'reloadDiv' => $reloadDiv, 'modal' => $modal, 'options' => \appxq\sdii\utils\SDUtility::array2String($options)]);
                }
                $js_ajax_content .= "
                    $('#btn-tab" . $key_gen . $key . "').click(function(){
                        var div_content = $('.tab-content-display$key_gen');
                        div_content.html('<div class=\"sdloader\"><i class=\"sdloader-icon\"></i></div>');
                        $.get('$url',{},function(result){
                            div_content.html(result);
                        });
                    });
                ";
                $title .= '<li class="nav-item"><a ' . $style . ' href="#tab' . $key_gen . $key . '" class="tab-primary btn-tabs' . $key_gen . '" id="btn-tab' . $key_gen . $key . '"  key_index="' . $key . '" data-url="' . $url . '" >' . (isset($val['icon']) ? '<i class="fa ' . $val['icon'] . '"></i>' : '') . ' ' . $val['tab_title'] . ' ' . $icon . '</a>  </li>';
            }
        }
    }
} else {

    if ($tabs[$firstIndex]['tab_type'] == '1') {
        $url = \yii\helpers\Url::to(["/thaihis/patient-visit/ezform-content", 'widget_id' => $widget_id, 'keyTab' => $firstIndex, 'visitid' => $visitid, 'target' => $target, 'visit_type' => $visit_type
                    , 'reloadDiv' => $reloadDiv, 'modal' => $modal, 'options' => \appxq\sdii\utils\SDUtility::array2String($options)]);
        $js_ajax_content .= "
                    $(function(){
                        var div_content = $('.tab-content-display$key_gen');
                        div_content.html('<div class=\"sdloader\"><i class=\"sdloader-icon\"></i></div>');
                        $.get('$url',{},function(result){
                            div_content.html(result);
                        });
                    });
                ";
    } else {
        $url = \yii\helpers\Url::to(["/thaihis/patient-visit/widget-content", 'widget_id' => $widget_id, 'keyTab' => $firstIndex, 'visitid' => $visitid, 'target' => $target, 'visit_type' => $visit_type
                    , 'reloadDiv' => $reloadDiv, 'modal' => $modal, 'options' => \appxq\sdii\utils\SDUtility::array2String($options)]);
        $js_ajax_content .= "
                    $(function(){
                        var div_content = $('.tab-content-display$key_gen');
                        div_content.html('<div class=\"sdloader\"><i class=\"sdloader-icon\"></i></div>');
                        $.get('$url',{},function(result){
                            div_content.html(result);
                        });
                    });
                ";
    }
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
        '{theme}' => $theme,
        '{title}' => $title,
    ];
    $template_action = '';
    $template_action_view = '';
    $dept_only = true;
    if ($manage_state) {
        if (isset($tabs) && is_array($tabs)) {
            foreach ($tabs as $keyField => $valTab) {
                $edit_own = false;
                if (isset($valTab['edit_data_own']) && $valTab['edit_data_own'] == '1') {
                    $edit_own = true;
                }
                if (isset($valTab['dept_display']) && $valTab['dept_display'] == '1') {
                    $dept_only = false;
                    if (in_array($dept, $valTab['dept_list'])) {
                        $dept_only = true;
                    }
                }

                $action = isset($valTab['action']) && !empty($valTab['action']) ? $valTab['action'] : ['create', 'update', 'view', 'delete'];
                if ($keyField == $firstIndex) {
                    $template_action .= '<div id="action_tab_' . $key_gen . $keyField . '">';
                    $template_action_view .= '<div id="action_tab_' . $key_gen . $keyField . '">';
                } else {
                    $template_action .= '<div id="action_tab_' . $key_gen . $keyField . '" style="display:none;">';
                    $template_action_view .= '<div id="action_tab_' . $key_gen . $keyField . '" style="display:none;">';
                }

                $initdata = [];
                if ($valTab['ezf_id'] == '1503378440057007100') {
                    $initdata = ['pt_cid' => $model_tabs[$keyField]['pt_cid']];
                }

                if ($valTab['tab_type'] == '4') {
                    foreach ($action as $value) {
                        if ($value == 'create' && $dept_only) {
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
                        if ($value == 'create' && $dept_only) {
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

                        if ($value == 'create' && $dept_only) {
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
                        } elseif ($value == 'update' && $dept_only) {
                            if (($edit_own && isset($model_tabs[$keyField]['user_create']) && $model_tabs[$keyField]['user_create'] != $user_id))
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
                        } elseif ($value == 'delete' && $dept_only) {
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

                            //if (isset($model_tabs[$keyField]['id'])) {
                            $template_action .= ' {view' . $keyField . '}';
                            $path_display['{view' . $keyField . '}'] = EzfHelper::btn($valTab['main_ezf_id'])
                                    ->reloadDiv($reloadDiv)
                                    ->targetField('target')
                                    ->target($visitid)
                                    ->modal('modal-ezform-main')
                                    //->data_column($fields)
                                    ->label('<i class="glyphicon glyphicon-th-list"></i>')
                                    ->options(['class' => 'btn btn-default btn-sm'])
                                    ->buildBtnGrid();

                            $template_action_view .= ' {view' . $keyField . '}';
                            $path_display['{view' . $keyField . '}'] = EzfHelper::btn($valTab['main_ezf_id'])
                                    ->reloadDiv($reloadDiv)
                                    ->targetField('target')
                                    ->target($visitid)
                                    ->modal('modal-ezform-main')
                                    //->data_column($fields)
                                    ->label('<i class="glyphicon glyphicon-th-list"></i>')
                                    ->options(['class' => 'btn btn-default btn-sm'])
                                    ->buildBtnGrid();
                            //}
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
                $template_action_view .= '<div id="action_tab_' . $key_gen . $keyField . '" style="display:none;">';
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
                                ->targetField('target')
                                ->target($visitid)
                                ->modal('modal-ezform-main')
                                ->addbtn(0)
                                ->readonly(0)
                                ->popup(1)
                                //->data_column($fields)
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

    if (empty($template_box)) {
        $txtHeader = '';
        if (isset($options['header_text']) && $options['header_text']) {
            $txtHeader = '<div class="panel-heading" style="padding: 4px 15px;">' . $options['header_text'] . '</div>';
        }
        $template_box = '<div class="panel panel-{theme}">  
            ' . $txtHeader . '
            <div class="panel-heading" style="padding-bottom:0">
                <div class="row">
                    <div class="col-md-9">
                        {title}
                    </div>
                    <div class="col-md-3 text-right">
                        ' . $template_action . '
                    </div>
                  </div>
              </div>
              <div class="panel-body" >
                <div class="tab-content-display' . $key_gen . '">
                    
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
            $('#action_tab_'+key_gen+''+$(e).children().attr('key_index')).css('display','none');
            $(e).children().removeClass('tab-active');
        });
        
        $('#action_tab_'+key_gen+''+$(this).attr('key_index')).css('display','block');
        $(this).addClass('tab-active');
    });
");
?>