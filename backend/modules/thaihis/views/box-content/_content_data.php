<?php

use yii\helpers\Html;
use yii\helpers\Url;
use backend\modules\ezforms2\classes\EzfHelper;

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

$user_id = Yii::$app->user->id;
$template_item;
$show_label = isset($options['show_label']) ? $options['show_label'] : 0;

$require_data = isset($options['require_data']) ? $options['require_data'] : 0;

if (isset($options['display'])) {
    if ($options['display'] == 'content_v') {
        $template_item = $template_v;
    } elseif ($options['display'] == 'content_h') {
        $template_item = $template_h;
    } else {
        if ($show_label) {
            $template_item = '<span id="{id}"><label>{label}</label> : <span class="text-info">{value}</span></span>';
        } else {
            $template_item = '<span id="{id}">{value}</span>';
        }
    }
} else {
    $template_item = $template_h;
}

//start content
$column = isset($options['column']) ? $options['column'] : 2;

$path_items = [
    "{update_date}" => (isset($model['update_date']) && !empty($model['update_date']) ? '<i class="glyphicon glyphicon-calendar"></i> ' . appxq\sdii\utils\SDdate::mysql2phpDateTime($model['update_date']) : ''),
];

$template_content = '<div class="row" >';
foreach ($fields as $field) {
    $fieldName = $field;
    $col = 12 / $column;

    if ($require_data) {
        if ($model[$fieldName] && $model[$fieldName] != '')
            $template_content .= "<div class=\"col-md-$col\">{{$fieldName}}</div>";
    }else {
        $template_content .= "<div class=\"col-md-$col\">{{$fieldName}}</div>";
    }

    foreach ($modelFields as $key => $value) {
        $var = $value['ezf_field_name'];
        $label = $value['ezf_field_label'];

        if ($fieldName == $var) {
            $dataInput;
            if (isset(Yii::$app->session['ezf_input'])) {
                $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($value['ezf_field_type'], Yii::$app->session['ezf_input']);
            }

            if ($show_label) {
                $dataValue = '';
                if ($model[$fieldName]) {
                    if ($value['ezf_field_type'] == 62) { //add ByOak checkbox ให้โชว์ label ด้วย
                        $dataValue = '<i class="fa fa-check"></i> ';
                    } else {
                        $dataValue = backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $value, $model);
                    }
                    $path_data = [
                        '{id}' => Html::getInputId($model, $fieldName) . '_content',
                        '{label}' => $label,
                        '{value}' => $dataValue,
                    ];

                    $path_items["{{$fieldName}}"] = strtr($template_item, $path_data);
                }
            } else {
                $dataValue = backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $value, $model);
                $path_data = [
                    '{id}' => Html::getInputId($model, $fieldName) . '_content',
                    '{label}' => $label,
                    '{value}' => $dataValue,
                ];

                $path_items["{{$fieldName}}"] = strtr($template_item, $path_data);
            }



            break;
        }
    }
}
$template_content .= '</div>';

if (isset($options['image_field']) && !empty($options['image_field'])) {
    $image_field = Html::img(Yii::getAlias('@storageUrl/images/nouser.png'), ['class' => 'media-object img-rounded', 'style' => 'width: 100px;']);
    if (isset($model[$options['image_field']]) && !empty($model[$options['image_field']])) {
        $image_field = Html::img(Yii::getAlias('@storageUrl/ezform/fileinput/') . $model[$options['image_field']], ['class' => 'media-object img-rounded', 'style' => 'width: 100px;']);
    }

    $path_items["{{$options['image_field']}}"] = $image_field;

    $template_content = "<div class=\"media\"> 
                                <div class=\"media-left\"> 
                                      $image_field
                                </div> 
                                <div class=\"media-body\"> 
                                      $template_content
                                </div> 
                        </div>";
}

if (isset($options['template_content']) && !empty($options['template_content'])) {
    $template_content = $options['template_content'];
}

$content = strtr($template_content, $path_items);
//appxq\sdii\utils\VarDumper::dump($path_items);
//end content
//start box content
$title = isset($options['title']) ? $options['title'] : '';
$readonly = isset($options['readonly']) ? $options['readonly'] : 0;
$disabled = isset($options['disabled']) ? $options['disabled'] : 0;
$disabled_box = isset($options['disabled_box']) ? $options['disabled_box'] : 0;
$action = isset($options['action']) && !empty($options['action']) ? $options['action'] : ['create', 'update', 'view'];
$template_box = isset($options['template_box']) ? $options['template_box'] : '';
$addon = isset($options['addon']) ? $options['addon'] : 0;
$theme = isset($options['theme']) ? $options['theme'] : 'default';

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
    $template_action = '';
    $template_action_view = '';
    $path_display = [
        '{content}' => $content,
        '{theme}' => $theme,
        '{title}' => $title,
    ];

    if ($manage_state) {
        foreach ($action as $value) {
            $edit_own = false;
            if (isset($options['edit_data_own']) && $options['edit_data_own'] == '1') {
                $edit_own = true;
            }
            //create, update, delete, view, search
            if ($value == 'create') {
                $template_action .= ' {create}';
                $path_display['{create}'] = EzfHelper::btn($ezf_id)
                        ->reloadDiv($reloadDiv)
                        ->target($visitid)
                        ->targetField($targetField)
                        ->modal('modal-ezform-main')
                        ->label('<i class="glyphicon glyphicon-plus"></i>')
                        ->options(['class' => 'btn btn-success btn-sm'])
                        ->buildBtnAdd();
            } elseif ($value == 'update') {
                if ((isset($options['doctor_can']) && $options['doctor_can'] == '1') && $model['user_create'] != $user_id)
                    continue;
                
                if ($edit_own) {
                    if (isset($model['user_create']) && $model['user_create'] != $user_id)
                        continue;
                }

                $template_action .= ' {update}';
                $path_display['{update}'] = '';
                if (isset($model->id)) {
                    $path_display['{update}'] = EzfHelper::btn($ezf_id)
                            ->reloadDiv($reloadDiv)
                            ->target($visitid)
                            ->targetField($targetField)
                            ->modal('modal-ezform-main')
                            ->label('<i class="glyphicon glyphicon-pencil"></i>')
                            ->options(['class' => 'btn btn-primary btn-sm'])
                            ->buildBtnEdit($model->id);
                } else {
                    $path_display['{create}'] = EzfHelper::btn($ezf_id)
                            ->reloadDiv($reloadDiv)
                            ->target($visitid)
                            ->targetField($targetField)
                            ->modal($modal)
                            ->label('<i class="glyphicon glyphicon-plus"></i>')
                            ->options(['class' => 'btn btn-success btn-sm'])
                            ->buildBtnAdd();
                }
            } elseif ($value == 'delete') {
                $template_action .= ' {delete}';
                $path_display['{delete}'] = '';
                if (isset($model->id)) {
                    $path_display['{delete}'] = EzfHelper::btn($ezf_id)
                            ->reloadDiv($reloadDiv)
                            ->target($visitid)
                            ->targetField($targetField)
                            ->modal('modal-ezform-main')
                            ->label('<i class="glyphicon glyphicon-trash"></i>')
                            ->options(['class' => 'btn btn-danger btn-sm'])
                            ->buildBtnDelete($model->id);
                }
            } elseif ($value == 'view') {
                $template_action .= ' {view}';
                $path_display['{view}'] = EzfHelper::btn($ezf_id)
                        ->reloadDiv($reloadDiv)
                        ->target($visitid)
                        ->targetField($targetField)
                        ->modal('modal-ezform-main')
                        ->addbtn(0)
                        ->readonly(0)
                        ->popup(1)
                        ->data_column($fields)
                        ->label('<i class="glyphicon glyphicon-th-list"></i>')
                        ->options(['class' => 'btn btn-default btn-sm'])
                        ->buildBtnGrid();

                $template_action_view .= ' {view}';
                $path_display['{view}'] = EzfHelper::btn($ezf_id)
                        ->reloadDiv($reloadDiv)
                        ->target($visitid)
                        ->targetField($targetField)
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
    $template_action_view = '';

    $action = isset($options['action']) && !empty($options['action']) ? $options['action'] : ['create', 'update', 'view'];

    foreach ($action as $value) {
        if ($value == 'view') {
            $template_action_view .= ' {view}';
            $path_display['{view}'] = EzfHelper::btn($ezf_id)
                    ->reloadDiv($reloadDiv)
                    ->target($visitid)
                    ->targetField($targetField)
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

    if ($readonly) {
        $template_action = $template_action_view;
    }
    if (isset($options['doctor_can']) && $options['doctor_can'] == '1') {
        if (!Yii::$app->user->can('doctor')) {
            $template_action = $template_action_view;
        }
    }
    // add graph display button
    $datafield = implode(',', $fields);
    $graphdisplay = isset($options['graphdisplay']) ? $options['graphdisplay'] : 0;
    if ($graphdisplay) {
        $displayitem = '<button id="graphdisplay-' . $reloadDiv . '" 
        class="pull-right graph-display btn-sm btn btn-info" style="margin-left:3px;"
        data-url="' . Url::to(["/graphconfig/graphconfig/display-graph"]) . '"
        data-title="Graph"
        data-visit="' . $visitid . '"
        data-ezfid="' . $ezf_id . '"
        data-fields="' . $datafield . '"
        ><i class="glyphicon glyphicon-stats"></i></button>';
        $template_action .= $displayitem;
    }
    $update_date_display = '';
    if (isset($model['update_date']) && $model['update_date']) {
        $path_display['{update_date}'] = '<i class="glyphicon glyphicon-calendar"></i> '
                . appxq\sdii\utils\SDdate::mysql2phpDateTime($model['update_date']);
        $path_display['{user_update}'] = '<i class="fa fa-user"></i> '
                . \backend\modules\ezforms2\classes\EzfQuery::getUserProfile($model['user_update'])['fullname'];
        
        $update_date_display = '<div class="row">
                        <div class="col-md-12 text-right" style="font-size: 10px;">
                            {update_date} {user_update}
                        </div>
                    </div>';
    }

    if (empty($template_box)) {
        $template_box = '<div class="panel panel-{theme}">
            <div class="panel-heading" style="padding: 4px 15px;">                
                <div class="row">
                    <div class="col-md-6">
                        <h4>{title}</h4>
                    </div>
                    <div class="col-md-6 text-right" style="margin-top: 5px;">
                        ' . $template_action . '
                    </div>
                  </div>
              </div>
              <div class="panel-body">    
                    ' . $update_date_display . '
                    {content}
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
$('#graphdisplay-" . $reloadDiv . "').click(function(){
    var url = $(this).attr('data-url'); 
    var title =$(this).attr('data-title'); 
    var visit = $(this).attr('data-visit'); 
    var ezfid = $(this).attr('data-ezfid'); 
    var fields = $(this).attr('data-fields'); 
         $('#modal-ezform-main .modal-content').html('<div class=\"progress progress-striped active\"><div class=\"progress-bar\" style=\"width:100%\"></div></div>');
        $('#modal-ezform-main').modal('show');
        setTimeout(function(){ 
            $.ajax({
                method: 'POST',
                url: url,
                data: {title:title, visit:visit, ezfid:ezfid, fields, fields},
                    dataType: 'HTML',
                    success: function(result) {
                    $('#modal-ezform-main .modal-content').html(result);
                    return false;
                }
            });
        }, 500);
});

");
?>