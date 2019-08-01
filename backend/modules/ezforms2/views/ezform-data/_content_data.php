<?php

use yii\helpers\Html;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use yii\helpers\Url;
use backend\modules\ezforms2\classes\EzfHelper;

$template_h = '<dl id="box-{id}" class="dl-horizontal content_box">
                <dt class="content_label">{label}</dt>
                <dd class="content_value">{value}</dd>
              </dl>';
//<div id="box-{id}" class="row">
//                    <div class="col-md-4 sdcol-label">
//                      <strong>{label}</strong>
//                    </div>
//                    <div class="col-md-8 sdbox-col">
//                      {value}
//                    </div>
//               </div>

$template_v = '<dl id="box-{id}" class="content_box">
                    <dt class="content_label">{label}</dt>
                    <dd class="content_value">{value}</dd>
               </dl>';

$template_table = '<table id="box-{id}" class="table content_box" style="margin-bottom: 0px;"><tbody><tr> <th class="content_label text-right" style="width: 160px;">{label}</th> <td class="content_value">{value}</td> </tr><tbody></table>';

$template_item;
$customDisplay = false;
if(isset($options['display'])){
    if($options['display']=='content_v'){
        $template_item = $template_v;
    } elseif ($options['display']=='content_h') {
        $template_item = $template_h;
    } elseif ($options['display']=='content_table') {
        $template_item = $template_table;
    }  else {
        $template_item = '<span id="{id}" class="content_value">{value}</span>';
        $customDisplay = TRUE;
    }
} else {
    $template_item = $template_h;
}


//start content
$column = isset($options['column'])?$options['column']:2;
//
//$path_items = [
//    "{update_date}" => (isset($model['update_date']) && !empty($model['update_date']) ? '<i class="glyphicon glyphicon-calendar"></i> '.appxq\sdii\utils\SDdate::mysql2phpDateTime($model['update_date']):''),
//    ];

$template_content = '<p class="text-right">
                        <small>{updated_at} &nbsp; {updated_by}</small>
                    </p>
                    ';
$row_count = 0;
foreach ($fields as $field) {
    $fieldName = $field;
    $col = 12/$column;
    $sdbox = '';
    $row_count++;
    
    if($row_count==1){
        $template_content .= '<div class="row" >';
    }
    if($row_count>1){
        $sdbox = 'sdbox-col';
    }
    $template_content .= "<div class=\"col-md-$col $sdbox\">{{$fieldName}}</div>";
    
    if($row_count==$column){
        $template_content .= '</div>';
        $row_count = 0;
    }
    
    foreach ($modelFields as $key => $value) {
        $var = $value['ezf_field_name'];
        $label = $value['ezf_field_label'];

        if ($fieldName == $var) {
            $dataInput;
            if (isset(Yii::$app->session['ezf_input'])) {
                $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($value['ezf_field_type'], Yii::$app->session['ezf_input']);
            }

            $path_data = [
                '{id}' => Html::getInputId($model, $fieldName).'_content',
                '{label}' => $label,
                '{value}' => backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $value, $model),
            ];
            
            $path_items["{{$fieldName}}"] = strtr($template_item, $path_data);

            break;
        }
    }
}
//$template_content .= '</div>';

if(isset($options['template_content']) && !empty($options['template_content'])){
    $template_content = $options['template_content'];
}

if(isset($options['image_field']) && !empty($options['image_field'])){
    $image_field = Html::img(Yii::getAlias('@storageUrl/images/nouser.png'), ['class'=>'media-object img-rounded', 'style'=>'width: 100px;']);
    if(isset($model[$options['image_field']]) && !empty($model[$options['image_field']])){
        $image_field = Html::img(Yii::getAlias('@storageUrl/ezform/fileinput/').$model[$options['image_field']], ['class'=>'media-object img-rounded content_img', 'style'=>'width: 100px;']);
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


//end content

//start box content
$title = isset($options['title'])?$options['title']:''; 
$readonly = isset($options['readonly'])?$options['readonly']:0;
$disabled = isset($options['disabled'])?$options['disabled']:0;
$disabled_box = isset($options['disabled_box'])?$options['disabled_box']:0;
$action = isset($options['action']) && !empty($options['action'])?$options['action']:['create', 'update', 'view'];
$template_box = isset($options['template_box'])?$options['template_box']:'';
$addon = isset($options['addon'])?$options['addon']:0;
$theme = isset($options['theme'])?$options['theme']:'default';

if(!$disabled){
    $path_display = $path_items;
    
    $template_action = '';
    foreach ($action as $value) {
        //create, update, delete, view, search
        if($value=='create'){
            $template_action .= ' {create}';
            $path_display['{create}'] = EzfHelper::btn($ezf_id)
                    ->reloadDiv($reloadDiv)
                    ->target($target)
                    ->targetField($targetField)
                    ->modal($modal)
                    ->label('<i class="glyphicon glyphicon-plus"></i>')
                    ->options(['class'=>'btn btn-success btn-sm btn-auth-create'])
                    ->buildBtnAdd();
        } elseif ($value=='update') {
            $template_action .= ' {update}';
            $path_display['{update}'] = '';
            if(isset($model->id)){
                $path_display['{update}'] = EzfHelper::btn($ezf_id)
                    ->reloadDiv($reloadDiv)
                    ->target($target)
                    ->targetField($targetField)
                    ->modal($modal)
                    ->label('<i class="glyphicon glyphicon-pencil"></i>')
                    ->options(['class'=>'btn btn-primary btn-sm btn-auth-update'])    
                    ->buildBtnEdit($model->id);
            }

        } elseif ($value=='delete') {
            $template_action .= ' {delete}';
            $path_display['{delete}'] = '';
            if(isset($model->id)){
                $path_display['{delete}'] = EzfHelper::btn($ezf_id)
                    ->reloadDiv($reloadDiv)
                    ->target($target)
                    ->targetField($targetField)
                    ->modal($modal)
                    ->label('<i class="glyphicon glyphicon-trash"></i>')
                    ->options(['class'=>'btn btn-danger btn-sm btn-auth-del'])    
                    ->buildBtnDelete($model->id);
            }
        } elseif ($value=='view') {
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
                    ->options(['class'=>'btn btn-default btn-sm btn-auth-view'])
                    ->buildBtnGrid();
        } elseif ($value=='data_table') {
            $template_action .= ' {data_table}';
            
        }
    }
    
        $path_display['{ezf_id}'] = $ezf_id;
        $path_display['{reloadDiv}'] = $reloadDiv;
        $path_display['{target}'] = $target;
        $path_display['{targetField}'] = $targetField;
        $path_display['{modal}'] = $modal;
        $path_display['{dataid}'] = isset($model->id)?$model->id:'';
        //Variable : {ezf_id}, {reloadDiv}, {target}, {targetField}, {modal}, {dataid}, {updated_at}, {created_at}, {save}, {create}, {update}, {delete}, {view}, {data_table}
        $path_display['{updated_by}'] = isset($model->user_update)?'<i class="glyphicon glyphicon-user"></i> '.\backend\modules\ezforms2\classes\EzfQuery::getUserProfileById($model->user_update, 'name'):'';
        $path_display['{created_by}'] = isset($model->user_create)?'<i class="glyphicon glyphicon-user"></i> '.\backend\modules\ezforms2\classes\EzfQuery::getUserProfileById($model->user_create, 'name'):'';
        $path_display['{updated_at}'] = isset($model->update_date)?'<i class="glyphicon glyphicon-calendar"></i> '.\appxq\sdii\utils\SDdate::mysql2phpDateTime($model->update_date):'';
        $path_display['{created_at}'] = isset($model->create_date)?'<i class="glyphicon glyphicon-calendar"></i> '.\appxq\sdii\utils\SDdate::mysql2phpDateTime($model->create_date):'';
        if(isset($model->id)){
            $path_display['{save}'] = EzfHelper::btn($ezf_id)
                    ->reloadDiv($reloadDiv)
                    ->target($target)
                    ->targetField($targetField)
                    ->modal($modal)
                    ->label('<i class="glyphicon glyphicon-pencil"></i>')
                    ->options(['class'=>'btn btn-primary btn-sm btn-auth-update'])    
                    ->buildBtnEdit($model->id);
        } else {
            $path_display['{save}'] = EzfHelper::btn($ezf_id)
                    ->reloadDiv($reloadDiv)
                    ->target($target)
                    ->targetField($targetField)
                    ->modal($modal)
                    ->label('<i class="glyphicon glyphicon-plus"></i>')
                    ->options(['class'=>'btn btn-success btn-sm btn-auth-create'])
                    ->buildBtnAdd();
        }
        $path_display['{data_table}'] = EzfHelper::btn($ezf_id)
                    ->reloadDiv($reloadDiv)
                    ->target($target)
                    ->targetField($targetField)
                    ->modal($modal)
                    ->popup(1)
                    ->data_column($fields)
                    ->label('<i class="glyphicon glyphicon-th-list"></i>')
                    ->options(['class'=>'btn btn-default btn-sm btn-auth-view'])
                    ->buildBtnGrid();
    
    
    if($readonly){
        $template_action = '';
    }
    
    if(empty($template_box)){
        $template_box = '<div class="panel panel-{theme}">
            <div class="panel-heading" style="padding: 4px 15px;">
                <div class="row">
                    <div class="col-md-6">
                        <h4>{title}</h4>
                    </div>
                    <div class="col-md-6 text-right" style="margin-top: 5px;">
                        '.$template_action.'
                    </div>
                  </div>
              </div>
              <div class="panel-body">
                    {content}
              </div>
          </div>';
    }

    $content = strtr($template_content, $path_display);
    $path_system = [
        '{content}' => $content,
        '{theme}' => $theme,
        '{title}' => $title,
    ];
    
    $path_display = yii\helpers\ArrayHelper::merge($path_system, $path_display);
    $display_box = strtr($template_box, $path_display);
    
    if($disabled_box){
        
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