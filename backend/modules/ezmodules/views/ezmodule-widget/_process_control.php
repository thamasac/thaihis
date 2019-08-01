<?php

use backend\modules\ezmodules\classes\ModuleQuery;
use backend\modules\ezforms2\classes\EzfHelper;
use backend\modules\ezmodules\classes\ModuleFunc;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$userProfile = Yii::$app->user->identity->profile;
$dept = (isset($userProfile->department) && !empty($userProfile->department)) ? $userProfile->department : 0;

$inList = ModuleQuery::getSettingWorkList($dept, $module, 'in_comming');
$processList = ModuleQuery::getSettingWorkList($dept, $module, 'process');
$completeList = ModuleQuery::getSettingWorkList($dept, $module, 'completed');
?>

<ul class="list-group">
  <li class="list-group-item disabled">
    <i class="fa fa-sign-in fa-lg"></i> <strong>In comming request</strong>

    <span class="badge" style="background-color: #CB0011;margin-left: 5px;"><?= count($inList) ?></span>
    <span class="pull-right"><?= \yii\helpers\Html::a('<i class="glyphicon glyphicon-home"></i> EzWorkbench', yii\helpers\Url::to(['/ezforms2/queue-log/index']), ['class' => 'btn btn-default btn-xs']) ?></span> 
  </li>

  <?php if (isset($inList) && !empty($inList)): ?>
      <?php foreach ($inList as $key => $value): ?>
          <?php
          $sql = "SELECT *
            FROM zdata_working_unit_setting
            WHERE id = :id
            ";
          $dataSetting = Yii::$app->db->createCommand($sql, [':id' => $value['setting_id']])->queryOne();
          if (!$dataSetting) {
              continue;
          }
          ?>
          <li class="list-group-item">
              <?= ModuleFunc::getProcessLabel($value, $dataSetting) ?> <code>[<?= $dataSetting['tab_name'] ?>]</code>
            <div style="margin-top: 5px; padding-top: 5px; border-top: 1px solid #eee;">
              <?php
              echo EzfHelper::btn($value['ezf_id'])->label('<i class="glyphicon glyphicon-eye-open"></i> View')->options(['class' => 'btn btn-info btn-sm '])->buildBtnEdit($value['dataid']);
              ?>
              <button class="btn btn-warning btn-sm btn-receive" data-id="<?= $value['id'] ?>"><i class="fa fa-sign-out"></i> Receive</button>
            </div>
          </li>
      <?php endforeach; ?>
  <?php endif; ?>

  <li class="list-group-item disabled">
    <i class="fa fa-pencil-square-o fa-lg"></i> <strong>Process within the unit</strong>
    <span class="badge" ><?= count($processList) ?></span>
  </li>

  <?php if (isset($processList) && !empty($processList)): ?>
      <?php foreach ($processList as $key => $value): ?>
          <?php
          $sql = "SELECT *
            FROM zdata_working_unit_setting
            WHERE id = :id
            ";
          $dataSetting = Yii::$app->db->createCommand($sql, [':id' => $value['setting_id']])->queryOne();
          if (!$dataSetting) {
              continue;
          }
          ?>
          <li class="list-group-item">
              <?= ModuleFunc::getProcessLabel($value, $dataSetting) ?> <code>[<?= $dataSetting['tab_name'] ?>]</code>
            <div style="margin-top: 5px; padding-top: 5px; border-top: 1px solid #eee;">
              
              <?php
              echo EzfHelper::btn($value['ezf_id'])->label('<i class="glyphicon glyphicon-eye-open"></i> View')->options(['class' => 'btn btn-info btn-sm '])->buildBtnEdit($value['dataid']);
              $process_forms = isset($dataSetting['process_forms']) ? appxq\sdii\utils\SDUtility::string2Array($dataSetting['process_forms']) : [];
              $formList = [];
              $li = '';
              foreach ($process_forms as $form_id) {
                  $modelEzf = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($form_id);
                  $formList[$form_id] = $modelEzf->ezf_name;
                  $li .= "<li><a class=\"ezform-main-open\" data-modal=\"modal-ezform-main\" data-url=\"/ezforms2/ezform-data/ezform?ezf_id=$form_id&amp;modal=modal-ezform-main&amp;reloadDiv=&amp;initdata=&amp;target={$value['dataid']}\">{$modelEzf->ezf_name}</a></li>";
              }
              //appxq\sdii\utils\VarDumper::dump($formList);
              ?>
              <div class="btn-group">
                <button type="button" class="btn btn-default dropdown-toggle btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                   Workbench <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                  <?=$li?>
                </ul>
              </div>

              <button class="btn btn-success btn-sm btn-complete" data-id="<?= $value['id'] ?>"><i class="fa fa-sign-out"></i> Complete</button>
            </div>
          </li>
      <?php endforeach; ?>
  <?php endif; ?>

  <li class="list-group-item disabled" >
     <i class="fa fa-check-circle fa-lg"></i> <strong>Completed</strong>
     <span class="badge" style="margin-left: 5px;"><?= count($completeList) ?></span>
     <span class="pull-right"><?= \yii\helpers\Html::a('<i class="glyphicon glyphicon-eye-open"></i> Views', yii\helpers\Url::to(['/ezforms2/queue-log/index', 'tab'=>'completed']), ['class' => 'btn btn-default btn-xs']) ?></span> 
  </li>        

</ul>

<?php
$divname = 'process-' . $module;
$this->registerJs("

    $('.btn-receive').click(function(){
        var id = $(this).attr('data-id');
        yii.confirm('" . Yii::t('ezform', 'Are you sure you want to receive this item?') . "', function() {
            $.ajax({
                method: 'POST',
                url: '" . yii\helpers\Url::to(['/ezmodules/ezmodule-widget/process-receive']) . "',
                data:{id:id},
                dataType: 'JSON',
                success: function(result, textStatus) {
                    //location.href=url;
                    if(result.status == 'success') {
                        getWorkingUnitContent();
                    } else {
                        " . \appxq\sdii\helpers\SDNoty::show('result.message', 'result.status') . "
                    }
                    
                }
            });
	    
	});
    });
    
    $('.btn-complete').click(function(){
        var id = $(this).attr('data-id');
        yii.confirm('" . Yii::t('ezform', 'Are you sure you want to complete this item?') . "', function() {
            $.ajax({
                method: 'POST',
                url: '" . yii\helpers\Url::to(['/ezmodules/ezmodule-widget/process-complete']) . "',
                data:{id:id},
                dataType: 'JSON',
                success: function(result, textStatus) {
                    //location.href=url;
                    if(result.status == 'success') {
                        getWorkingUnitContent();
                    } else {
                        " . \appxq\sdii\helpers\SDNoty::show('result.message', 'result.status') . "
                    }
                    
                }
            });
	    
	});
    });
    
    function getWorkingUnitContent() {
        $.ajax({
            method: 'GET',
            url:  $('#$divname').attr('data-url'),
            dataType: 'HTML',
            success: function(result, textStatus) {
                $('#$divname').html(result);
            }
        });
    }

");
?>