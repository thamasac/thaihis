<?php

use yii\helpers\Html;
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
$template_item;
if (isset($options['display'])) {
    if ($options['display'] == 'content_v') {
        $template_item = $template_v;
    } elseif ($options['display'] == 'content_h') {
        $template_item = $template_h;
    } else {
        $template_item = '<span id="{id}">{value}</span>';
    }
} else {
    $template_item = $template_h;
}


//start content
$column = isset($options['column']) ? $options['column'] : 2;

$path_items = [
    "{update_date}" => (isset($model['update_date']) && !empty($model['update_date']) ? '<i class="glyphicon glyphicon-calendar"></i> ' . appxq\sdii\utils\SDdate::mysql2phpDateTime($model['update_date']) : ''),
];

$template_content = '<p class="text-right">
                        <small>{update_date}</small>
                    </p>
                    <div class="row" >';

foreach ($modelFields as $key => $value) {
    $fieldName = $value['ezf_field_name'];
    $col = 12 / $column;
    $template_content .= "<div class=\"col-md-$col\">{{$value['ezf_field_name']}}</div>";

    $var = $value['ezf_field_name'];
    $label = $value['ezf_field_label'];

    $dataInput;
    if (isset(Yii::$app->session['ezf_input'])) {
        $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($value['ezf_field_type'], Yii::$app->session['ezf_input']);
    }

    $path_data = [
        '{id}' => Html::getInputId($model, $fieldName),
        '{label}' => $label,
        '{value}' => backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $value, $model),
    ];

    $path_items["{{$fieldName}}"] = strtr($template_item, $path_data);
}

$template_content .= '</div>';

if (isset($options['image_field']) && !empty($options['image_field'])) {
    $image_field = Html::img(Yii::getAlias('@storageUrl/images/nouser.png'), ['class' => 'media-object img-rounded', 'style' => 'width: 100px;']);
    if (isset($model[$options['image_field']]) && !empty($model[$options['image_field']])) {
        $image_field = Html::img(Yii::getAlias('@storageUrl/ezform/fileinput/') . $model[$options['image_field']], ['class' => 'media-object img-rounded', 'style' => 'width: 100px;']);
    }
//    \appxq\sdii\utils\VarDumper::dump($options,0);
//    \appxq\sdii\utils\VarDumper::dump($model);
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

if (!$disabled) {

    $path_display = [
        '{content}' => $content,
        '{theme}' => $theme,
        '{title}' => $title,
    ];
    $template_action = '';
    foreach ($action as $value) {
        //create, update, delete, view, search
        if ($value == 'create') {
            $template_action .= ' {create}';
            $path_display['{create}'] = EzfHelper::btn($ezf_id)
                    ->reloadDiv($reloadDiv)
                    ->target($target)
                    ->targetField($targetField)
                    ->modal($modal)
                    ->label('<i class="glyphicon glyphicon-plus"></i>')
                    ->options(['class' => 'btn btn-success btn-sm'])
                    ->buildBtnAdd();
        } elseif ($value == 'update') {
            $template_action .= ' {update}';
            $path_display['{update}'] = '';
            if (isset($model->id)) {
                $path_display['{update}'] = EzfHelper::btn($ezf_id)
                        ->reloadDiv($reloadDiv)
                        ->target($target)
                        ->targetField($targetField)
                        ->modal($modal)
                        ->label('<i class="glyphicon glyphicon-pencil"></i>')
                        ->options(['class' => 'btn btn-primary btn-sm'])
                        ->buildBtnEdit($model->id);
            }
        } elseif ($value == 'delete') {
            $template_action .= ' {delete}';
            $path_display['{delete}'] = '';
            if (isset($model->id)) {
                $path_display['{delete}'] = EzfHelper::btn($ezf_id)
                        ->reloadDiv($reloadDiv)
                        ->target($target)
                        ->targetField($targetField)
                        ->modal($modal)
                        ->label('<i class="glyphicon glyphicon-trash"></i>')
                        ->options(['class' => 'btn btn-danger btn-sm'])
                        ->buildBtnDelete($model->id);
            }
        } elseif ($value == 'view') {
            $template_action .= ' {view}';
            $path_display['{view}'] = EzfHelper::btn($ezf_id)
                    ->reloadDiv($reloadDiv)
                    ->target($target)
                    ->targetField($targetField)
                    ->modal($modal)
                    ->addbtn(0)
                    ->readonly(1)
                    ->popup(1)
                    ->data_column($fields)
                    ->label('<i class="glyphicon glyphicon-th-list"></i>')
                    ->options(['class' => 'btn btn-default btn-sm'])
                    ->buildBtnGrid();
        }
    }

    if ($readonly) {
        $template_action = '';
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

");
?>