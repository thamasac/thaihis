<?php

use yii\helpers\Html;
use appxq\sdii\utils\SDdate;
use backend\modules\tctr\classes\TctrFunction;
use yii\helpers\Url;
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
                        <h4><b>Tracking Information</b>
                        <?php
                        if ($data['user_create']==$userid and $typeview == "map") {
                            echo Html::a('<span class="glyphicon glyphicon-pencil"></span> แก้ไข', Url::to(['/ezforms2/ezform-data/ezform',
                                        'ezf_id' => $ezf_id,
                                        'dataid' => $data['id'],
                                        'modal' => $modal,
                                    ]), [
                                'title' => Yii::t('yii', 'Update'),
                                'class' => "btn btn-primary btn-edit",
                            ]);
                        }
                        ?> 
                        </h4>
                        
                    </th>
                </tr>
                <tr>
                    <th>Date of First Enrollment:</th>
                    <td width="70%"><?=$modelEzf->created_at?></td>
                </tr>
                <tr>
                    <th>Target Sample Size:</th>
                    <td width="70%"><?=$data['target_size'] ?></td>
                </tr>
                <tr>
                    <th>Last Updated Date:</th>
                    <td width="70%"><?=SDdate::mysql2phpDateTime($modelEzf->updated_at)?></td>
                </tr>
                <tr>
                    <th>Primary Outcome(s):</th>
                    <td width="70%"> 
                        <?php 
                        if(isset($primary)){
                            foreach ($primary as $key => $value) {
                            ?>
                            <b>- Outcome :</b><?=$value['outcome_name'] ?> <br>
                            <b>&nbsp;&nbsp;Safety Issue?:</b><?= TctrFunction::getOneChoice('1520779798000066500','safety_issue',$value['safety_issue']) ?>
                        <?php
                            }
                        } ?>
                               
                    </td>
                </tr>
                <tr>
                    <th>Key Secondary Outcomes:</th>
                    <td width="70%">
                        <?php  
                        if(isset($secondary)){
                            foreach ($secondary as $key => $value) {
                            ?>
                            <b>- Outcome :</b><?=$value['outcome_name'] ?> <br>
                            <b>&nbsp;&nbsp;Safety Issue?:</b><?= TctrFunction::getOneChoice('1520780022010173600','safe_issue',$value['safe_issue'])  ?><br>
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
                    <th colspan="2" class="info"><h4><b>Descriptive Information</b></h4></th>
                </tr>
                <tr>
                    <th>Public Title:</th>
                    <td width="70%"><?=$data['public_title'] ?></td>
                </tr>
                <tr>
                    <th>Scientific Title:</th>
                    <td width="70%"><?=$data['scientific_title'] ?></td>
                </tr>
                <tr>
                    <th>Brief Summary:</th>
                    <td width="70%"><?=$data['i_freetext'] ?></td>
                </tr>
                <tr>
                    <th>Detailed Description:</th>
                    <td width="70%"><?=$data['i_freetext_detailed'] ?></td>
                </tr>
                <tr>
                    <th>Study Design:</th>
                    <td width="70%">
                        <b>Allocation:</b><?=TctrFunction::getOneChoice($ezf_id,'study_design',$data['study_design'])  ?><br>
                        <b>Control:</b><?= TctrFunction::getOneChoice($ezf_id,'study_control',$data['study_control'])?><br>
                        <b>Study Endpoint Classification:</b><?=TctrFunction::getOneChoice($ezf_id,'study_classification',$data['study_classification'])  ?><br>
                        <b>Intervention Model:</b><?=TctrFunction::getOneChoice($ezf_id,'intervention_model',$data['intervention_model']) ?><br>
                        <b>Number of Arms:</b><?=$data['num_arms'] ?><br>
                        <b>Masking:</b><?=TctrFunction::getOneChoice($ezf_id,'masking',$data['masking']) ?>(<b>Masked Roles:</b>)<br>
                        <b>Primary Purpose:</b><?=TctrFunction::getOneChoice($ezf_id,'primarty_purpose',$data['primarty_purpose']) ?><br>
                        <b>Study Phase:</b><?=TctrFunction::getOneChoice($ezf_id,'phase',$data['phase']) ?><br>
                    </td>
                </tr>
                <tr>
                    <th>Health Condition(s) or Problem(s) Studied:</th>
                    <td width="70%"><?=$data['hc_freetext'] ?></td>
                </tr>
                <tr>
                    <th>Study Arms:</th>
                    <td width="70%">
                        
                        <?php  
                        if(isset($intervention)){
                            $arm = 0;
                            foreach ($intervention as $key => $value) {
                                $arm++;
                            ?>
                        <div class="col-md-12">
                            <b> - Arm <?=$arm?> :</b>
                        </div>
                        <div class="col-md-3 text-right"><b>Intervention Name:</b></div>
                        <div class="col-md-9"><?=$value['intername']?></div>
                        <div class="col-md-3 text-right"><b>Type:</b></div>
                        <div class="col-md-9"><?=TctrFunction::getOneChoice('1521539374020575500','intertype',$value['intertype'])?></div>
                        <div class="col-md-3 text-right"><b>Classification:</b></div>
                        <div class="col-md-9"><?=TctrFunction::getOneChoice('1521539374020575500','interclass',$value['interclass'])?></div>
                        <div class="col-md-3 text-right"><b>Descriptions:</b></div>
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
                    <th colspan="2" class="info"><h4><b>Recruitment Information</b></h4></th>
                </tr>
                <tr>
                    <th>Recruitment Status:</th>
                    <td width="70%"><?= $data['recruitment_status']?></td>
                </tr>
                <tr>
                    <th>Estimated Enrollment:</th>
                    <td width="70%"><?=$data['target_size'] ?></td>
                </tr>
                <tr>
                    <th>Study Start Date (First enrollment):</th>
                    <td width="70%"><?=$data['date_enrolment'] ?></td>
                </tr>
                <tr>
                    <th>Primary Completion Date:</th>
                    <td width="70%"><?=$data['primary_com_date'] ?></td>
                </tr>
                <tr>
                    <th>Inclusion Criteria:</th>
                    <td width="70%">
                        <div class="row">
                            <div class="col-md-3 text-right"><b>Gender:</b></div>
                            <div class="col-md-9"><?= $data['gender'] ?></div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 text-right"><b>Age Limit:</b></div>
                            <div class="col-md-9">Minimum:<?= $data['agemin'] ?>: Maximum : <?= $data['agemax'] ?></div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 text-right"><b>Inclusion Criteria:</b></div>
                            <div class="col-md-9"><?= $data['inclusion_criteria'] ?></div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 text-right"><b>Exclusion Criteria:</b></div>
                            <div class="col-md-9"><?= $data['exclusion_criteria'] ?></div>
                        </div>
                        <div class="row">
                            <div class="col-md-12"><b>Accept Healthy Volunteers: </b></div>
                            <div class="col-md-12"><?= $data['accept_healthy'] ?></div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>Countries of Recruitment:</th>
                    <td width="70%"><?=$sectionB?></td>
                </tr>
                <tr>
                    <th>Contact for Public Queries:</th>
                    <td width="70%">
                        <?php  
                        if(isset($sectionC)){
                            foreach ($sectionC as $key => $value) {
                            ?>
                        <div class="row">
                            <div class="col-md-4 text-right"><b>Contact for Public Query's</b></div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 text-right"><b>Name:</b></div>
                            <div class="col-md-8"><?=$value['firstname']?> <?=$value['middlename']?> <?=$value['lastname']?></div>
                        </div>
                         <div class="row">
                            <div class="col-md-4 text-right"><b>Degree:</b></div>
                            <div class="col-md-8"><?=$value['degree']?></div>
                         </div>
                         <div class="row">
                            <div class="col-md-4 text-right"><b>Phone:</b></div>
                            <div class="col-md-8"><?=$value['telephone']?> <b>Ext:</b> <?=$value['extension']?></div>
                         </div>
                        <div class="row">
                        <div class="col-md-4 text-right"><b>Email:</b></div>
                        <div class="col-md-8"><?=$value['email']?></div>
                        </div>
                        <div class="row">
                        <div class="col-md-4 text-right"><b>Postal Address:</b></div>
                        <div class="col-md-8"><?=$value['address']?></div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 text-right"><b>State/Province:</b></div>
                                    <div class="col-md-8"><?=$value['city']?></div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 text-right"><b>Country:</b></div>
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
