<?php

use backend\modules\ezforms2\classes\EzActiveForm;
use yii\helpers\Html;
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
            ],
            'options' => [
                'enctype' => 'multipart/form-data',
                'data-ezf_id' => $ezf_id,
                'data-dataid' => $dataid,
                'data-modal' => $modal,
                'data-reloadDiv' => $reloadDiv,
                'data-initdata' => $initdata,
            ]
        ]);
?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3 class="modal-title" id="itemModalLabel"><?= $modelEzf->ezf_name ?> <small><?= $modelEzf->ezf_detail ?></small></h3>
</div>
<div class="modal-body">
    <div id="formPanel-<?= $modelEzf->ezf_id ?>" class="row">
        <?php
        foreach ($modelFields as $field) {
            if ($field['ezf_field_type'] > 0) {
                $disabled = 0;
                if (isset($field['ezf_field_ref']) && $field['ezf_field_ref'] > 0) {
                    $cloneRefField = EzfFunc::cloneRefField($field);
                    $field = $cloneRefField['field'];
                    $disabled = $cloneRefField['disabled'];
                }

                $dataInput;
                if (isset(Yii::$app->session['ezf_input'])) {
                    $dataInput = EzfFunc::getInputByArray($field['ezf_field_type'], Yii::$app->session['ezf_input']);
                }

                
                $options = \appxq\sdii\utils\SDUtility::string2ArrayJs($field['ezf_field_options']);
                if (isset($options['options']['data-type']) && in_array($options['options']['data-type'], ['viewer', 'target'])) {
                    $disabled = 1;
                }

                echo EzfFunc::generateInput($form, $model, $field, $dataInput, $disabled);
                if ($field['ezf_condition'] == 1) {
                    EzfFunc::generateCondition($model, $field, $modelEzf, $this, $dataInput);
                }

                if (isset($field['ezf_field_cal']) && $field['ezf_field_cal'] != '') {
                    $cut = preg_match_all("%{(.*?)}%is", $field['ezf_field_cal'], $matches);
                    if ($cut) {
                        $varArry = $matches[1];
                        $createEvent = EzfFunc::genJs($varArry, $model, $field);
                        $this->registerJs($createEvent);
                    }
                }
            }
        }
        ?>
    </div>
</div>
<div class="modal-footer">
    <?= Html::button('<i class="glyphicon glyphicon-remove"></i> '.Yii::t('app', 'Close'), ['class' => 'btn btn-default', 'data-dismiss' => 'modal']) ?>    
</div>
<?php EzActiveForm::end(); ?>

<?php
$disabledInput = "$('#formPanel-{$modelEzf->ezf_id} input, #formPanel-{$modelEzf->ezf_id} select, #formPanel-{$modelEzf->ezf_id} textarea').attr('disabled', true); $('#formPanel-{$modelEzf->ezf_id} input[type=\"hidden\"]').attr('disabled', false);";

$this->registerJs("
    
{$modelEzf->ezf_js}
   
$disabledInput

$('form#$formName').on('beforeSubmit', function(e) {
    return false;
});

");
?>