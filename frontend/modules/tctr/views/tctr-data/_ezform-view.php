<?php

use yii\helpers\Html;
use appxq\sdii\utils\SDdate;
use backend\modules\tctr\classes\TctrFunction;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$modal ="modal-ezform-main";
?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3 class="modal-title" id="itemModalLabel"><?=$data['trial_id'] ?> </h3>
</div>
<div class="modal-body">

    <div class="row table-responsive">
        <div class="col-md-12">
            <table class="table table-bordered">
                <tr class="info">
                    <th colspan="2" class="">
                        <h4><b><?=Yii::t('app', 'Tracking Information')?></b>
                        </h4>
                    </th>
                </tr>
                <tr>
                    <th><?=Yii::t('app', 'Date of First Enrollment')?>:</th>
                    <td width="70%"><?=date_format(date_create($data['create_date']),"F d, Y");?></td>
                </tr>
                <tr>
                    <th><?=Yii::t('app', 'Target Sample Size')?>:</th>
                    <td width="70%"><?=$data['target_size'] ?></td>
                </tr>
                <tr>
                    <th><?=Yii::t('app', 'Primary Outcome(s)')?>:</th>
                    <td width="70%"> 
                        <?php 
                        if(isset($primary)){
                            foreach ($primary as $key => $value) {
                            ?>
                            <b>- <?=Yii::t('app', 'Outcome')?>:</b><?=$value['outcome_name'] ?> <br>
                            <b>&nbsp;&nbsp;<?=Yii::t('app', 'Safety Issue')?>?:</b><?= TctrFunction::getOneChoice('1520779798000066500','safety_issue',$value['safety_issue']) ?>
                        <?php
                            }
                        } ?>
                               
                    </td>
                </tr>
                <tr>
                    <th><?=Yii::t('app', 'Key Secondary Outcomes')?>:</th>
                    <td width="70%">
                        <?php  
                        if(isset($secondary)){
                            foreach ($secondary as $key => $value) {
                            ?>
                            <b>- <?=Yii::t('app', 'Outcome')?> :</b><?=$value['outcome_name'] ?> <br>
                            <b>&nbsp;&nbsp;<?=Yii::t('app', 'Safety Issue')?>?:</b><?= TctrFunction::getOneChoice('1520780022010173600','safe_issue',$value['safe_issue'])  ?><br>
                        <?php
                            }
                        } 
                        ?>
                    </td>
                </tr>
            </table>
        </div>
        <div class="clearfix"></div>
        <div class="col-md-12">
            <table class="table table-bordered">
                <tr>
                    <th colspan="2" class="info">
                        <h4>
                            <span><b><?=Yii::t('app', 'Descriptive Information')?></b></span>
                        </h4>
                    </th>
                </tr>
                <tr>
                    <th><?=Yii::t('app', 'Public Title')?>:</th>
                    <td width="70%"><?=$data['public_title'] ?></td>
                </tr>
                <tr>
                    <th><?=Yii::t('app', 'Scientific Title')?>:</th>
                    <td width="70%"><?=$data['scientific_title'] ?></td>
                </tr>
                <tr>
                    <th><?=Yii::t('app', 'Brief Summary')?>:</th>
                    <td width="70%"><?=$data['i_freetext'] ?></td>
                </tr>
                <tr>
                    <th><?=Yii::t('app', 'Detailed Description')?>:</th>
                    <td width="70%"><?=$data['i_freetext_detailed'] ?></td>
                </tr>
                <tr>
                    <th><?=Yii::t('app', 'Study Design')?>:</th>
                    <td width="70%">
                        <b><?=Yii::t('app', 'Allocation')?>:</b><?=TctrFunction::getOneChoice($ezf_id,'study_design',$data['study_design'])  ?><br>
                        <b><?=Yii::t('app', 'Control')?>:</b><?= TctrFunction::getOneChoice($ezf_id,'study_control',$data['study_control'])?><br>
                        <b><?=Yii::t('app', 'Study Endpoint Classification')?>:</b><?=TctrFunction::getOneChoice($ezf_id,'study_classification',$data['study_classification'])  ?><br>
                        <b><?=Yii::t('app', 'Intervention Model')?>:</b><?=TctrFunction::getOneChoice($ezf_id,'intervention_model',$data['intervention_model']) ?><br>
                        <b><?=Yii::t('app', 'Number of Arms')?>:</b><?=$data['num_arms'] ?><br>
                        <b><?=Yii::t('app', 'Masking')?>:</b><?=TctrFunction::getOneChoice($ezf_id,'masking',$data['masking']) ?><br>
                        <b><?=Yii::t('app', 'Primary Purpose')?>:</b><?=TctrFunction::getOneChoice($ezf_id,'primarty_purpose',$data['primarty_purpose']) ?><br>
                        <b><?=Yii::t('app', 'Study Phase')?>:</b><?=TctrFunction::getOneChoice($ezf_id,'phase',$data['phase']) ?><br>
                    </td>
                </tr>
                <tr>
                    <th><?=Yii::t('app', 'Health Condition(s) or Problem(s) Studied')?>:</th>
                    <td width="70%"><?=$data['hc_freetext'] ?></td>
                </tr>
                <tr>
                    <th><?=Yii::t('app', 'Study Arms')?>:</th>
                    <td width="70%">
                        
                        <?php  
                        if(isset($intervention)){
                            $arm = 0;
                            foreach ($intervention as $key => $value) {
                                $arm++;
                            ?>
                        <div class="col-md-12">
                            <b> -  <?=$arm?> :</b>
                        </div>
                        <div class="col-md-3 text-right"><b><?=Yii::t('app', 'Intervention Name')?>:</b></div>
                        <div class="col-md-9"><?=$value['intername']?></div>
                        <div class="col-md-3 text-right"><b><?=Yii::t('app', 'Type')?>:</b></div>
                        <div class="col-md-9"><?=TctrFunction::getOneChoice('1521539374020575500','intertype',$value['intertype'])?></div>
                        <div class="col-md-3 text-right"><b><?=Yii::t('app', 'Classification')?>:</b></div>
                        <div class="col-md-9"><?=TctrFunction::getOneChoice('1521539374020575500','interclass',$value['interclass'])?></div>
                        <div class="col-md-3 text-right"><b><?=Yii::t('app', 'Descriptions')?>:</b></div>
                        <div class="col-md-9"><?= $value['interdes']?></div>
                        <?php
                            }
                        } 
                        ?>
                    </td>
                </tr>
            </table>
            </div>
            <div class="clearfix"></div>
        <div class="col-md-12">
            <table class="table table-bordered">
                <tr>
                    <th colspan="2" class="info"><h4><b><?=Yii::t('app', 'Recruitment Information')?></b></h4></th>
                </tr>
                <tr>
                    <th><?=Yii::t('app', 'Recruitment Status')?>:</th>
                    <td width="70%"><?=TctrFunction::getOneChoice($ezf_id,'recruitment_status',$data['recruitment_status'])?></td>
                </tr>
                <tr>
                    <th><?=Yii::t('app', 'Estimated Enrollment')?>:</th>
                    <td width="70%"><?=$data['target_size'] ?></td>
                </tr>
                <tr>
                    <th><?=Yii::t('app', 'Study Start Date (First enrollment)')?>:</th>
                    <td width="70%"><?=date_format(date_create($data['date_enrolment']),"F d, Y");?></td>
                </tr>
                <tr>
                    <th><?=Yii::t('app', 'Primary Completion Date')?>:</th>
                    <td width="70%"><?=date_format(date_create($data['primary_com_date']),"F d, Y");?></td>
                </tr>
                <tr>
                    <th><?=Yii::t('app', 'Inclusion Criteria')?>:</th>
                    <td width="70%">
                        <div class="row">
                            <div class="col-md-3 text-right"><b><?=Yii::t('app', 'Gender')?>:</b></div>
                            <div class="col-md-9"><?= $data['gender'] ?></div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 text-right"><b><?=Yii::t('app', 'Age Limit')?>:</b></div>
                            <div class="col-md-9"><?=Yii::t('app', 'Minimum')?>:<?= $data['agemin'] ?>: <?=Yii::t('app', 'Maximum')?> : <?= $data['agemax'] ?></div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 text-right"><b><?=Yii::t('app', 'Inclusion Criteria')?>:</b></div>
                            <div class="col-md-9"><?= $data['inclusion_criteria'] ?></div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 text-right"><b><?=Yii::t('app', 'Exclusion Criteria')?>:</b></div>
                            <div class="col-md-9"><?= $data['exclusion_criteria'] ?></div>
                        </div>
                        <div class="row">
                            <div class="col-md-12"><b><?=Yii::t('app', 'Accept Healthy Volunteers')?>: </b></div>
                            <div class="col-md-12"><?= $data['accept_healthy'] ?></div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th><?=Yii::t('app', 'Countries of Recruitment')?>:</th>
                    <td width="70%"><?=$sectionB?></td>
                </tr>
                <tr>
                    <th><?=Yii::t('app', 'Contact for Public Queries')?>:</th>
                    <td width="70%">
                        <?php  
                        if(isset($sectionC)){
                            foreach ($sectionC as $key => $value) {
                            ?>
                        <div class="row">
                            <div class="col-md-4 text-right"><b><?=Yii::t('app', 'Contact for Public Querys')?></b></div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 text-right"><b><?=Yii::t('app', 'Name')?>:</b></div>
                            <div class="col-md-8"><?=$value['firstname']?> <?=$value['middlename']?> <?=$value['lastname']?></div>
                        </div>
                         <div class="row">
                            <div class="col-md-4 text-right"><b><?=Yii::t('app', 'Degree')?>:</b></div>
                            <div class="col-md-8"><?=$value['degree']?></div>
                         </div>
                         <div class="row">
                            <div class="col-md-4 text-right"><b><?=Yii::t('app', 'Phone')?>:</b></div>
                            <div class="col-md-8"><?=$value['telephone']?> <b>Ext:</b> <?=$value['extension']?></div>
                         </div>
                        <div class="row">
                        <div class="col-md-4 text-right"><b><?=Yii::t('app', 'Email')?>:</b></div>
                        <div class="col-md-8"><?=$value['email']?></div>
                        </div>
                        <div class="row">
                        <div class="col-md-4 text-right"><b><?=Yii::t('app', 'Postal Address')?>:</b></div>
                        <div class="col-md-8"><?=$value['address']?></div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 text-right"><b><?=Yii::t('app', 'State/Province')?>:</b></div>
                                    <div class="col-md-8"><?=$value['city']?></div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 text-right"><b><?=Yii::t('app', 'Country')?>:</b></div>
                            <div class="col-md-8"><?=$value['country1']?></div>
                        </div>
                        <?php
                            }
                        } 
                        ?>

                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
<div class="modal-footer">
    <?= Html::button('<i class="glyphicon glyphicon-remove"></i> ' . Yii::t('app', 'Close'), ['class' => 'btn btn-default', 'data-dismiss' => 'modal']) ?>    
</div>
<?php
\richardfan\widget\JSRegister::begin([
    'position' => \yii\web\View::POS_READY
]);
?>
<script>
$('.btn-edit').on('click',function(){
        var url = $(this).attr('href');
        $('#modal-ezform-main .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
        $('#modal-ezform-main').modal('show')
        .find('.modal-content')
        .load(url);
        return false;
    });
</script>
<?php \richardfan\widget\JSRegister::end(); ?>
