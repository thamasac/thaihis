<?php
$bed_type = 'info';
$btn_bed = Yii::t('patient', 'Move bed');//'Discharge'
if(isset($model['bed_type']) && !empty($model['bed_type'])){
    if($model['bed_type']==1){
        $bed_type = 'danger';
    }elseif ($model['bed_type']==2) {
        $bed_type = 'primary';
    }
}
$bed_label = $bed_type;
if (isset($model['pt_hn'])) {
    $url='#';
    if($tab!=''){
        $url = \yii\helpers\Url::to(['/ezmodules/ezmodule/view', 'id'=>$module, 'tab'=>$tab, 'target' => $model['pt_id'], 'visitid' => $model['visit_id'],
                'admit_id' => $model['admit_id'], 'pt_hn' => $model['pt_hn'], 'bed_id' => $model['bed_id']]);
        }
        $urlMove = \yii\helpers\Url::to(['/patient/admit/admit-pt', 'pt_id' => $model['pt_id'], 'visit_id' => $model['visit_id'],
                'admit_id' => $model['admit_id'], 'reloadDiv' => $reloadDiv, 'pt_hn' => $model['pt_hn']]);
    
        
        
    if(isset($model['admit_status']) && $model['admit_status']==3){
        $bed_type = 'warning';
        $btn_bed = Yii::t('patient', 'Discharge');
    }
    
      $pt_pic=Yii::getAlias('@storageUrl/images/nouser.png');
    if (isset($model['pt_pic']) && !empty($model['pt_pic'])){
        $pt_pic = Yii::getAlias('@storageUrl/ezform/fileinput/').$model['pt_pic'];
    }
    
    $ezfAdmit_id = \backend\modules\patient\Module::$formID['admit'];
    $btnEdit = \backend\modules\ezforms2\classes\EzfHelper::btn($ezfAdmit_id)
            ->reloadDiv($reloadDiv)
            ->label('บันทึกฟอร์มรับ')
            ->options(['class'=>'btn btn-block btn-sm btn-'.$bed_type])
            ->buildBtnEdit($model['admit_id'] );
        
    ?> 
    <a class = "alert-patient alert-patient-<?=$bed_type?> btn btn-block "
       style="padding: 0.75rem 1.25rem;margin-bottom: 1rem;" href="<?=$url?>">
      <div class="alert-patient-<?=$bed_label?>"><i class = "fa fa-bed"></i> <strong ><?= $model['bed_code']; ?></strong>   </div>
      <hr style="margin-top: 5px;margin-bottom: 5px;">
      <div class="media" style="margin-top: 0px; margin-bottom: 10px;"> 
        <div class="media-left"> 
            <img class="media-object img-rounded" src="<?=$pt_pic?>" style="width: 36px; height: 36spx;">
        </div> 
        <div class="media-body text-left"> 
          <div>
            HN : <strong><?= $model['pt_hn'] ?></strong> AN : <strong><?= $model['admit_an'] ?></strong>
          </div>
          <div>
            <i class="fa fa-user"></i> : <strong><?= $model['fullname'] ?></strong>
          </div>
        </div> 
      </div>
      
        <div class="row">
          <div class="col-md-6">
            <button class="btn btn-block btn-sm btn-default ezform-main-open" data-url="<?= $urlMove ?>" data-modal="modal-ezform-main"><?= $btn_bed?></button>
          </div>
          <div class="col-md-6 sdbox-col">
            <?=$btnEdit?>
          </div>
      </div>
      
      
    </a>
    <?php
} else {
    $admit_status = backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String(["1", "2"]);
    $url = \yii\helpers\Url::to(['/patient/admit/ward-ptadmit', 'admit_status' => $admit_status, 'bed_status' => '1', 'dept' => $dept, 'reloadDiv' => $reloadDiv, 'bed_id' => $model['bed_id']]);
    ?>
    <div class = "alert-patient alert-patient-dark text-center btn btn-block ezform-main-open" 
         data-url="<?= $url ?>" data-modal="modal-ezform-main"
         style="padding: 0.75rem 1.25rem;margin-bottom: 1rem; min-height: 131px;">

      <div class="alert-patient-<?=$bed_label?>"><i class = "fa fa-bed"></i> <strong ><?= $model['bed_code']; ?></strong>   </div>
      <hr style="margin-top: 5px;margin-bottom: 5px;">
      <div>&nbsp;</div>
      <div>
        <strong><i class="fa fa-check-square-o"></i> <?= Yii::t('patient', 'Empty bed')?></strong>
      </div>
    </div>
    <?php
}
?>