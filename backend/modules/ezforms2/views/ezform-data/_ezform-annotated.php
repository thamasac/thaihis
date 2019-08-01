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
                'reloadDiv' => $reloadDiv,
                'v' => $modelVersion['ver_code'],
            ],
            'options' => [
                'enctype' => 'multipart/form-data',
                'data-ezf_id' => $ezf_id,
                'data-modal' => $modal,
                'data-reloadDiv' => $reloadDiv,
                'data-v' => $modelVersion['ver_code'],
            ]
        ]);
?>
<div id="print-<?= $modelEzf->ezf_id ?>" style="background-color: #fff; border-radius: 6px;">
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3 class="modal-title" id="itemModalLabel"><?= backend\modules\ezforms2\classes\EzfUiFunc::getEzformIcon($modelEzf)?> <?= $modelEzf->ezf_name ?> <small><?= $modelEzf->ezf_detail ?></small>
     <?php if($modelEzf->enable_version==1){
                $model_version = backend\modules\ezforms2\classes\EzfQuery::getEzformVersionApprovList($ezf_id);
                ?>
        <div class="btn-group">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >
                 <?=($modelVersion['ver_active']==1?'<span class="glyphicon glyphicon-star" aria-hidden="true"></span>':'')?> <?=$modelVersion['ver_code']?>  <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" >
                  <?php
                  if(isset($model_version) && !empty($model_version)){
                      foreach ($model_version as $key => $value) {
                          ?>
                  <li class="<?=($value['ver_code']==$modelVersion['ver_code'])?'active':''?>"><a class="ezform-main-open" data-modal="<?=$modal?>" data-url="<?= \yii\helpers\Url::to(['/ezforms2/ezform-data/ezform-annotated', 
                          'ezf_id'=>$ezf_id,
                          'v'=>$value['ver_code'],  
                          'modal'=>$modal,
                          'reloadDiv'=>$reloadDiv,
                          ])?>">
                                       <?=($value['ver_active']==1?'<span class="glyphicon glyphicon-star" aria-hidden="true"></span>':'')?> <?=$value['ver_code']?> 
                          </a></li>
                          <?php
                      }
                  }
                  ?>
                </ul>
              </div>
        <?php } else {
                echo '<span class="label label-default">'.$modelVersion['ver_code'].'</span>';
            }
            ?>
    </h3>
</div>
<div class="modal-body">
    <div id="formPanel-<?= $modelEzf->ezf_id ?>" class="row">
        <?php
        echo \backend\modules\ezforms2\classes\EzfUiFunc::renderEzform($modelFields, Yii::$app->session['ezf_input'], $form, $model, $modelEzf, $this);
        ?>
    </div>
</div>
</div>  
<div class="modal-footer">
    <?php
    appxq\sdii\assets\Html2CanvasAsset::register($this);
    echo Html::button('<i class="glyphicon glyphicon-print"></i>', [
        'id'=>'h2c',
        'class'=>'btn btn-default ', 
        'target'=>'_blank', 
    ]);     
    ?>
    <?= Html::button('<i class="glyphicon glyphicon-remove"></i> '.Yii::t('app', 'Close'), ['class' => 'btn btn-default', 'data-dismiss' => 'modal']) ?>    
</div>
<?php EzActiveForm::end(); ?>

<?php
$options = \appxq\sdii\utils\SDUtility::string2Array($modelEzf->ezf_options);
$popup_size = isset($options['popup_size'])?$options['popup_size'].'%':'';
?>

<?php \richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]); ?>
<script>
    
    $('#h2c').click(function(){
        html2canvas($('#print-<?= $modelEzf->ezf_id ?>'), {
            onrendered: function(canvas) {
                var img = canvas.toDataURL('image/png');
                $.ajax({
                    method: 'POST',
                    url:'<?= Url::to(['/ezforms2/drawing/canvas-image'])?>',
                    data: {type: 'data', image: img, name: 'h2c', '_csrf':'<?=Yii::$app->request->getCsrfToken()?>'},
                    dataType: 'JSON',
                    success: function(result, textStatus) {
                        if(result.status == 'success') {
                            let win = window.open(result.path+result.data, '_blank');
                            win.focus();
                            //window.location.href = result.path+result.data;
                        } else {
                            <?= SDNoty::show('result.message', 'result.status')?>
                        }
                    }
                });
            }
        });
    });
       
</script>
<?php \richardfan\widget\JSRegister::end(); ?>

<?php
$disabledInput = "$('#formPanel-{$modelEzf->ezf_id} input, #formPanel-{$modelEzf->ezf_id} select, #formPanel-{$modelEzf->ezf_id} textarea').attr('disabled', true); $('#formPanel-{$modelEzf->ezf_id} input[type=\"hidden\"]').attr('disabled', false);";

$popup_css = $popup_size==''?'':"width: $popup_size;";//addClass removeClass
$this->registerCss("
    @media (min-width: 768px){
        #$modal .popup-size {
            $popup_css
        }
    }
");

$this->registerJs("

$('#$modal .modal-dialog').addClass('popup-size');
    
{$modelEzf->ezf_js}

$('form#$formName').on('beforeSubmit', function(e) {
    return false;
});

");
?>