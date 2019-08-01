<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$url = ['/ezforms2/data-lists/index'];
$queryParams = Yii::$app->request->getQueryParams();

$alert = 'label-default';
if($model['rstat']==0){
    $alert = 'label-info';
} elseif($model['rstat']==1){
    $alert = 'label-success';
} elseif($model['rstat']==2){
    $alert = 'label-primary';
} elseif($model['rstat']==3){
    $alert = 'label-danger';
} 

$rstat = backend\modules\core\classes\CoreFunc::itemAlias('rstat', $model['rstat']);
$html_rstat = "<span class=\"label $alert\">$rstat</span>";


$html_detail = '';
$detail = appxq\sdii\utils\SDUtility::string2Array($model['ezf_detail']);

if (is_array($detail)) {
    try {
        $query = new \yii\db\Query();
        $query->select(['*']);
        $query->from($model['ezf_table']);
        $query->where('id=:id', [':id' => $model['data_id']]);

        $zdata = $query->createCommand()->queryOne();
    } catch (\yii\db\Exception $e) {
        \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
    }
    
    if($zdata){
        $html = '';
        $comma = '';
        $options = appxq\sdii\utils\SDUtility::string2Array($modelEzf->ezf_options);
        $column = isset($options['display_col'])?$options['display_col']:0;
        $template = isset($options['display_tmp'])?$options['display_tmp']:'<span class="content_box"><b class="content_label">{label}: </b><span class="content_value">{value}</span> </span>';
        $row_count = 0;
        $field_count = count($detail);
        foreach ($detail as $index_field => $field) {
            $col = $column>0?12/$column:0;
            $sdbox = '';
            $row_count++;

            if($col>0 && $row_count>1){
                $sdbox = 'sdbox-col';
            }

            $template_content = $template;
            if($col>0) {
                $template_content = "<div class=\"col-md-$col $sdbox\">{$template}</div>";
            }
            
            foreach ($modelFields as $key => $value) {
                $var = $value['ezf_field_name'];
                $version = $value['ezf_version'];
                if($field == $var && ($zdata['ezf_version'] == $version || $version=='all')){
                    $dataInput;
                    if (Yii::$app->session['ezf_input']) {
                        $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($value['ezf_field_type'], Yii::$app->session['ezf_input']);
                    }
                    if(isset($zdata[$var]) && $zdata[$var]!=''){
                        if($col>0 && $row_count==1){
                            $html .= '<div class="row" >';
                        }

                        $html .= strtr($template_content, [
                            '{label}' => $value['ezf_field_label'],
                            '{value}' => backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $value, $zdata),
                        ]);
                                //$comma . "<b>{$value['ezf_field_label']}</b>" . backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $value, $zdata);
                    } else {
                        $row_count--;
                    }
             
                    //$html .= $comma . backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $value, $zdata);
                    break;
                }
                $comma = ' ';
            }
            if($col>0 && ($row_count==$column || $field_count==$index_field+1)){
                $html .= '</div>';
                $row_count = 0;
            }
        }
        
        $html_detail = $html;
    } 
}
        
$html_view_btn = '';
if (!$disabled) {
if(backend\modules\ezforms2\classes\EzfUiFunc::showViewDataEzf($modelEzf, Yii::$app->user->id, $model['user_create'])){
    $html_view_btn = Html::a('<span class="glyphicon glyphicon-eye-open"></span>', 
            Url::to(['/ezforms2/ezform-data/ezform-view', 
                'ezf_id'=>$ezf_id, 
                 'dataid'=>$model['data_id'],
                 'modal'=>$modal,
                'reloadDiv'=>$reloadDiv,
            ]), 
            [
             'data-action' => 'update',
             'data-pjax' => isset($this->pjax_id)?$this->pjax_id:'0',
             'class'=> 'btn btn-default btn-auth-view',
     ]);
}
}
$html_update_btn = '';
if (!$disabled) {
if(backend\modules\ezforms2\classes\EzfUiFunc::showEditDataEzf($modelEzf, Yii::$app->user->id, $model['user_create'])){
    $html_update_btn = Html::a('<span class="glyphicon glyphicon-pencil"></span>', 
            Url::to(['/ezforms2/ezform-data/ezform', 
                'ezf_id'=>$ezf_id, 
                 'dataid'=>$model['data_id'],
                 'modal'=>$modal,
                'reloadDiv'=>$reloadDiv,
            ]), 
            [
             'data-action' => 'update',
             'data-pjax' => isset($this->pjax_id)?$this->pjax_id:'0',
             'class'=> 'btn btn-primary btn-auth-update',
     ]);
}
}
$html_delete_btn = '';
if (!$disabled) {
if(backend\modules\ezforms2\classes\EzfUiFunc::showDeleteDataEzf($modelEzf, Yii::$app->user->id, $model['user_create'])){
    $html_delete_btn =  Html::a('<span class="glyphicon glyphicon-trash"></span>', 
            Url::to(['/ezforms2/ezform-data/delete', 
                 'ezf_id'=>$ezf_id, 
                 'dataid'=>$model['data_id'],
                 'reloadDiv'=>$reloadDiv,
            ]),  
            [
             'data-action' => 'delete',
             'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
             'data-method' => 'post',
             'data-pjax' => isset($this->pjax_id)?$this->pjax_id:'0',
             'class'=> 'btn btn-danger btn-auth-del',
     ]);
}
}
?>
<div class="media-body"> 
<!--    <button type="button" class="close" ><i class="fa fa-cog"></i></button>-->
    
    <h4 class="media-heading item-heading">  <?= backend\modules\ezforms2\classes\EzfUiFunc::getEzformIcon($model)?> <?=$model->ezf_name?>
        <small>
          <strong ><i class="glyphicon glyphicon-user"></i> </strong> <?= $model['userby']; ?> 
          <strong><i class="glyphicon glyphicon-calendar"></i> </strong> <?= !empty($model['update_date'])?\appxq\sdii\utils\SDdate::mysql2phpThDateSmall($model['update_date']):'';?> &nbsp;  
      </small>
    </h4> 
    <p class="list-group-item-text" style="color: #888">
        
        <?=$html_detail?> 
    </p>
    
    <div class="pull-right">
            <span class="label label-warning" data-toggle="tooltip" title="<?=$model['sitename']?>"><?=$model['xsourcex']?></span>
            <?=$html_rstat?>
    </div>
    
    <p class="list-group-item-text action" style="margin-top: 5px;">
        <?=$html_view_btn?> <?=$html_update_btn?> <?=$html_delete_btn?>
    </p>
</div>