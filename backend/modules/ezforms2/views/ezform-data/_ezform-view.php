<?php

use backend\modules\ezforms2\classes\EzActiveForm;
use yii\helpers\Html;
use appxq\sdii\utils\SDUtility;

use backend\modules\ezforms2\classes\EzfFunc;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use yii\helpers\Url;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>

<?php
$formName = 'ezform-' . $ezf_id;
$form = EzActiveForm::begin([
            'id' => $formName,
            'action' => ['/ezforms2/ezform-data/ezform',
                'ezf_id' => $ezf_id,
                'modal' => $modal,
                'dataid' => $dataid,
                'initdata' => $initdata,
                'reloadDiv' => $reloadDiv,
                'v' => $modelVersion['ver_code'],
            ],
            'options' => [
                'enctype' => 'multipart/form-data',
                'data-ezf_id' => $ezf_id,
                'data-dataid' => $dataid,
                'data-modal' => $modal,
                'data-reloadDiv' => $reloadDiv,
                'data-initdata' => $initdata,
                'data-v' => $modelVersion['ver_code'],
            ]
        ]);
?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3 class="modal-title" id="itemModalLabel"><?= backend\modules\ezforms2\classes\EzfUiFunc::getEzformIcon($modelEzf)?> <?= $modelEzf->ezf_name ?> <small><?= $modelEzf->ezf_detail ?></small></h3>
</div>
<div class="modal-body">
    <ul class="nav nav-tabs" role="tablist" style="margin-bottom: 10px;">
        <li role="presentation" class="active"><a href="#form-<?=$modelEzf->ezf_id?>" aria-controls="form-<?=$modelEzf->ezf_id?>" role="tab" data-toggle="tab"><?=Yii::t('app', 'Form')?></a></li>
        <li role="presentation"><a href="#discuss-<?=$modelEzf->ezf_id?>" aria-controls="discuss-<?=$modelEzf->ezf_id?>" role="tab" data-toggle="tab"><?=Yii::t('app', 'Discuss')?></a></li>
        <li role="presentation"><a href="#querytool-<?=$modelEzf->ezf_id?>" aria-controls="querytool-<?=$modelEzf->ezf_id?>" role="tab" data-toggle="tab"><?=Yii::t('ezform', 'Query Tool')?></a></li>
    </ul>

  <!-- Tab panes -->
  <div class="tab-content">
      <div role="tabpanel" class="tab-pane active" id="form-<?=$modelEzf->ezf_id?>">
        <div id="formPanel-<?= $modelEzf->ezf_id ?>" class="row">
            <?php
            echo \backend\modules\ezforms2\classes\EzfUiFunc::renderEzform($modelFields, Yii::$app->session['ezf_input'], $form, $model, $modelEzf, $this);
            ?>
        </div>
    </div>
      <div role="tabpanel" class="tab-pane" id="discuss-<?=$modelEzf->ezf_id?>">
        <?php
        if($modelEzf->consult_tools==2){
            echo backend\modules\ezforms2\classes\CommunityBuilder::Community()->type('ezform')->object_id($modelEzf->ezf_id)->dataid($dataid)->buildCommunity();
        } else {
            echo '<p class="lead" style="font-size: 16px;margin-bottom: 10px;">'.Yii::t('ezform', 'Close the consultant tool.').'</p>';
        }
        ?>
    </div>
    <div role="tabpanel" class="tab-pane" id="querytool-<?=$modelEzf->ezf_id?>">
        <?php
        if(in_array($modelEzf->query_tools, [2,3])){
            echo backend\modules\ezforms2\classes\CommunityBuilder::Community()->type('query_tool')->object_id($modelEzf->ezf_id)->dataid($dataid)->buildQueryTool();
        } else {
            echo '<p class="lead" style="font-size: 16px;margin-bottom: 10px;">'.Yii::t('ezform', 'Close the query tool.').'</p>';
        }
        ?>
    </div>
  </div>
    
</div>
<div class="modal-footer">
    <?= Html::button('<i class="glyphicon glyphicon-remove"></i> '.Yii::t('app', 'Close'), ['class' => 'btn btn-default', 'data-dismiss' => 'modal']) ?>    
</div>
<?php EzActiveForm::end(); ?>

<?php
$disabledInput = "$('#formPanel-{$modelEzf->ezf_id} input, #formPanel-{$modelEzf->ezf_id} select, #formPanel-{$modelEzf->ezf_id} textarea').attr('disabled', true); $('#formPanel-{$modelEzf->ezf_id} input[type=\"hidden\"]').attr('disabled', false);";
$options = SDUtility::string2Array($modelEzf->ezf_options);
$popup_size = (isset($options['popup_size']) && !empty($options['popup_size']))?$options['popup_size'].'%':'';

$popup_css = $popup_size==''?'':"width: $popup_size;";//addClass removeClass
$this->registerCss("
    @media (min-width: 768px){
        #$modal .popup-size {
            $popup_css
        }
    }
");

$this->registerJs("
    
{$modelEzf->ezf_js}
   
$disabledInput

$('#$modal .modal-dialog').addClass('popup-size');
    
$('form#$formName').on('beforeSubmit', function(e) {
    return false;
});

");
?>