<?php
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\web\JsExpression;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$newColor = $color == 'info'?'default':'info';
$data_subforms = [];
$ref_ezf_id = isset($value['form'])?$value['form']:0;
$dataFields = [];
if($ref_ezf_id>0){
    $modelSubForms = backend\modules\ezmodules\classes\ModuleQuery::getEzformList($ref_ezf_id);
    if(isset($modelSubForms)){
        $data_subforms = \yii\helpers\ArrayHelper::map($modelSubForms, 'ezf_id', 'ezf_name');
    }
    
    $modelFields = backend\modules\ezmodules\classes\ModuleQuery::getFieldsOptionList($ref_ezf_id);
    if(isset($modelFields)){
        $dataFields = \yii\helpers\ArrayHelper::map($modelFields, 'id', 'name');
    }
}

?>
<div class="panel panel-<?=$color?>" style="margin-left: <?=$margin?>px;">
    <div class="panel-heading">
        <button type="button" class="close btn-del pull-right" aria-hidden="true">&times;</button>  
        <h3 class="panel-title"><?= Yii::t('ezmodule', 'Form') ?> lvl <?=$lvl?></h3>
    </div>
    <div class="panel-body">
        <?=Yii::$app->controller->renderPartial('/ezmodule-forms/_subform_header')?>
        <div id="form_option_<?=$id?>" class="row form-group" data-id="<?=$id?>">
            <div class="col-md-4 ">
                <?= Select2::widget([
                    'options' => ['id'=>'form_input_'.$id, 'prompt' => Yii::t('ezmodule', 'Form')],
                    'data' => $dataForm,
                    'name'=>"options{$prefix}[forms][$id][form]",
                    'value' => isset($value['form'])?$value['form']:NULL,
                    'pluginOptions' => [
                        'allowClear' => false,
                    ],
                    'pluginEvents' => [
                        "change"=>"function(e) { $('.divitems_$id').html(''); }",
                        "select2:select" => "function(e) { $('#form_option_$id .label-input').val(e.params.data.text); $('.action_$id a').attr('data-ezf_id', e.params.data.id); }",
                        "select2:unselect" => "function(e) { $('#form_option_$id .label-input').val(''); $('.action_$id a').attr('data-ezf_id', 0);}"
                    ]
                ])?>
            </div>
            <div class="col-md-4 sdbox-col"><?= Html::textInput("options{$prefix}[forms][$id][label]", isset($value['label'])?$value['label']:NULL, ['class'=>'form-control label-input'])?></div>
            <div class="col-md-2 sdbox-col"><?= Html::dropDownList("options{$prefix}[forms][$id][pdf]", isset($model->options['pdf']) ? $model->options['pdf'] : 1, [1 => 'Yes', 0 => 'No'], ['class' => 'form-control']) ?></div>
            <div class="col-md-2 sdbox-col"><?= Html::textInput("options{$prefix}[forms][$id][width_pdf]", isset($model->options['width_pdf']) ? $model->options['width_pdf'] : 25, ['class' => 'form-control', 'type' => 'number']) ?></div>
        </div>
        
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title"><?= Yii::t('ezmodule', 'Field') ?> lvl <?=$lvl?></h3>
            </div>
            <div class="panel-body">
                <?=Yii::$app->controller->renderPartial('/ezmodule-forms/_field_header')?>
                <div id="field_option_<?=$id?>" class="divitems_<?=$id?>">
                    <?php
                        if($ref_ezf_id>0){
                            if(isset($value['fields']) && !empty($value['fields'])){
                                foreach ($value['fields'] as $key => $field) {
                                    echo $this->render('_field', [
                                        'id'=>$key,
                                        'ezf_id'=>$parent_ezf_id,
                                        'dataFields'=>$dataFields,
                                        'value'=>$field,
                                        'prefix'=>$prefix."[forms][$id]",
                                    ]);
                                }
                            }
                        }
                        ?>
                </div>
                <div class="modal-footer <?='action_'.$id?>">
                    <a style="cursor: pointer;" data-div="field_option_<?=$id?>" data-ezf_id="<?=$ref_ezf_id?>" data-prefix="<?=$prefix."[forms][$id]"?>" class="btn btn-primary btn-sub-field"><i class="fa fa-plus"></i> <?= Yii::t('ezmodule', 'Add Field')?> lvl <?=$lvl?></a>
                </div>
            </div>
        </div>
        
        <div class="panel panel-warning">
            <div class="panel-heading">
                <h3 class="panel-title"><?= Yii::t('ezmodule', 'Condition') ?> lvl <?=$lvl?></h3>
            </div>
            <div class="panel-body">
                <?=Yii::$app->controller->renderPartial('/ezmodule-forms/_condition_header')?>
                <div id="condition_option_<?=$id?>" >
                    <?php
                        if($ref_ezf_id>0){
                            if(isset($value['conditions']) && !empty($value['conditions'])){
                                foreach ($value['conditions'] as $key => $conditions) {
                                    echo $this->render('_condition', [
                                        'id'=>$key,
                                        'ezf_id'=>$parent_ezf_id,
                                        'dataForm'=>$dataFormCond,
                                        'value'=>$conditions,
                                        'prefix'=>$prefix."[forms][$id]",
                                    ]);
                                }
                            }
                        }
                        ?>
                </div>
                <div class="modal-footer ">
                    <a style="cursor: pointer;" data-div="condition_option_<?=$id?>" data-ezf_id="<?=$parent_ezf_id?>" data-prefix="<?=$prefix."[forms][$id]"?>" class="btn btn-warning btn-sub-condition"><i class="fa fa-plus"></i> <?= Yii::t('ezmodule', 'Add Condition')?> lvl <?=$lvl?></a>
                </div>
            </div>
        </div>
        
        <div class="panel panel-success">
            <div class="panel-heading">
                <h3 class="panel-title"><?= Yii::t('ezmodule', 'Show Items') ?> lvl <?=$lvl?></h3>
            </div>
            <div class="panel-body">
                <?=Yii::$app->controller->renderPartial('/ezmodule-forms/_show_header')?>
                <div id="show_option_<?=$id?>" class="divitems_<?=$id?>">
                    <?php
                        if($ref_ezf_id>0){
                            if(isset($value['show']) && !empty($value['show'])){
                                foreach ($value['show'] as $key => $show) {
                                    echo $this->render('_show', [
                                        'id'=>$key,
                                        'ezf_id'=>$parent_ezf_id,
                                        'dataFields'=>$dataFields,
                                        'value'=>$show,
                                        'prefix'=>$prefix."[forms][$id]",
                                    ]);
                                }
                            }
                        }
                        ?>
                </div>
                <div class="modal-footer <?='action_'.$id?>">
                    <a style="cursor: pointer;" data-div="show_option_<?=$id?>" data-ezf_id="<?=$ref_ezf_id?>" data-prefix="<?=$prefix."[forms][$id]"?>" class="btn btn-success btn-sub-show"><i class="fa fa-plus"></i> <?= Yii::t('ezmodule', 'Add Show Items')?> lvl <?=$lvl?></a>
                </div>
            </div>
        </div>
        
        <div class="modal-header" style="margin-bottom: 10px;padding-top: 0px;">
            <h4 class="modal-title"><?= Yii::t('ezmodule', 'Hierarchical Form') ?> lvl <?=$lvl+1?></h4>
        </div>
        
        <div id="form_<?=$id?>" class="divitems_<?=$id?>">
            <?php
                if($ref_ezf_id>0){
                    
                    if(isset($value['forms']) && !empty($value['forms'])){
                        
                        
                        foreach ($value['forms'] as $key => $form) {
                            echo $this->render('_subform', [
                                'id'=>$key,
                                'ezf_id'=>$ezf_id,
                                'parent_ezf_id'=>$parent_ezf_id,
                                'lvl'=>$lvl+1,
                                'color'=>$newColor,
                                'prefix'=>$prefix."[forms][$id]",
                                'margin'=>50,
                                'dataForm'=>$data_subforms,
                                'value'=>$form,
                            ]);
                        }
                    }
                }
            ?>
        </div>
        <div class="text-right <?='action_'.$id?>">
            <a style="cursor: pointer;" data-margin="50" data-parent_ezf_id="<?=$parent_ezf_id?>" data-ezf_id="<?=$ref_ezf_id?>" data-div="form_<?=$id?>" data-color="<?=$newColor?>" data-prefix="<?=$prefix."[forms][$id]"?>" data-lvl="<?=$lvl+1?>" class="btn btn-<?=$newColor?> add-subform"><i class="fa fa-plus"></i> <?= Yii::t('ezmodule', 'Add Form')?> lvl <?=$lvl+1?></a>
        </div>
    </div>
</div>