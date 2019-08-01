<?php

use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="itemModalLabel"><?= Yii::t('patient', 'Patient waiting for bed') ?></h4>
</div>
<div class="modal-body">
    <div class="pt-padmit row">
        <div class="col-md-12">  
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th style="text-align: center;">#</th>
                        <th>HN</th>
                        <th><?= Yii::t('patient', 'Name') ?></th>
                        <th><?= Yii::t('patient', 'Right') ?></th>
                        <th><?= Yii::t('patient', 'Department') ?></th>
                        <th><?= Yii::t('patient', 'Doctor') ?></th>
                        <th> </th>
                </thead>
                <tbody>
                    <?php
                    foreach ($data as $index => $value) {
                        ?>
                        <tr>
                            <td><?= $index + 1; ?></td>
                            <td><?= $value['pt_hn']; ?></td> 
                            <td><?= $value['fullname']; ?></td>
                            <td><?= $value['right_name']; ?></td> 
                            <td><?= $value['admit_from_dept']; ?></td>
                            <td><?= $value['admit_doctor_name']; ?></td>
                            <td class="text-center">
                              <?php if($mode==1){
                                  $url='#';
                                if($tab!=''){
                                    $url = \yii\helpers\Url::to(['/ezmodules/ezmodule/view', 'id'=>$module, 'tab'=>$tab, 'target' => $value['ptid'], 'visitid' => $value['visit_id'],
                                            'admit_id' => $value['admit_id'], 'pt_hn' => $value['pt_hn'], 'bed_id' => $value['bed_id']]);
                                    }
                                  ?>
                              
                              <a class="btn btn-sm btn-primary" href="<?=$url?>"><i class="glyphicon glyphicon-arrow-right"></i> CPOE</a>
                              <?php } elseif ($mode==2) { 
                                  $ezfAdmit_id = \backend\modules\patient\Module::$formID['admit'];
                                  ?>
                                
                                     <?= \backend\modules\ezforms2\classes\EzfHelper::btn($ezfAdmit_id)->options(['class'=>'btn btn-sm btn-primary'])->buildBtnEdit($value['admit_id'] )?>
                                  <?php  } else { ?>
                                    <button class="btn btn-xs btn-primary btn-block btn-admit-pt" data-dataid="<?= $value['bed_tran_id'] ?>" data-target="<?= $value['admit_id'] ?>"><?= Yii::t('patient', 'Select') ?> <i class="fa fa-arrow-right"></i></button>
                                  <?php } ?>
                            </td>
                        </tr>

                        <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php
$url = \yii\helpers\Url::to(['/patient/admit/ward-ptadmit-select', 'bed_id' => $bed_id]);
$this->registerJS("
    $('.pt-padmit tbody tr td .btn-admit-pt').on('click',function(){
         $('.pt-padmit .btn-admit-pt').removeClass('.btn-admit-pt');
         var target = $(this).attr('data-target');
         var dataid = $(this).attr('data-dataid');
         $.get('$url', {target:target,dataid:dataid}).done(function (result) {
            " . SDNoty::show('result.message', 'result.status') . "
            $(document).find('#modal-ezform-main').modal('hide');
            
            location.reload();
        }).fail(function () {
            " . SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') . "
                    console.log('server error');
        });
        return false;
    });
    ");
?>