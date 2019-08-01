<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use backend\modules\subjects\classes\SubjectManagementQuery;
use yii\helpers\ArrayHelper;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div class="panel panel-primary">
    <div class="panel-heading">
        <h4>All My Tasks</h4>
    </div>
    <div class="panel-body">

        <div class="clearfix"></div>
        <div class="table-responsive">
        <table class="table tab-bordered">
            <thead>
                <tr>
                <th style="width: 25%;">Task Name</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Progress</th>
                <th>Task Status</th>
                <th>Credit Point</th>
                <th>Reward Point</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    $m_number = 1;
                    $s_number = 1;
                    $t_number = 1;
                    $mainQuery = false;
                    $mainData = false;
                    foreach ($dataUserPMS as $key => $val):
                        if($mainQuery !==$key){
                            $mainData = SubjectManagementQuery::GetTableDataNotEzform('zdata_project',['id'=>$key],'one');
                            $mainQuery=$key;
                        }
                    //appxq\sdii\utils\VarDumper::dump($key,0);
                    ?>
                    
                    <?php if($mainData):?>
                        <tr>
                            <td colspan="7"><label style="font: 22px;"><i class="fa fa-folder-open" aria-hidden="true"></i> <?=$mainData['project_name']?></label></td>
                        </tr>
                        
                        <?php 
                        $s_number = 1;
                        foreach ($val as $key2 => $val2):
                            $subData = false;
                            $mainSub = false;
                            if($key == $key2){
                                $mainSub = SubjectManagementQuery::GetTableData('pms_task_target',['id'=>$key2],'one');
                            }else{
                                $subData = SubjectManagementQuery::GetTableData('pms_task_target',['id'=>$key2],'one');
                            }
                            ?>
                           <?php if($mainSub):?>
                                <?php 
                                $t_number=1;
                                foreach ($val2 as $key3 => $val3):
                                    $userData = \backend\modules\ezforms2\classes\EzfQuery::getUserProfile($val3['task_owner']);
                                    $startDate = SubjectManagementQuery::convertDate(date('Y-m-d', strtotime($val3['start_date'])));
                                    $endDate = SubjectManagementQuery::convertDate(date('Y-m-d', strtotime($val3['end_date'])));
                                    ?>
                               <tr>
                                <td><span style="font: 14px;margin-left: 10px;color: blue;"><i class="fa fa-file-text-o" aria-hidden="true"></i> <?=$m_number.".".$s_number." ".$val3['task_name']?></span></td>
                                <td><span style="font: 14px;margin-left: 10px;color: blue;"><?=$startDate?></span></td>
                                <td><span style="font: 14px;margin-left: 10px;color: blue;"><?=$endDate?></span></td>
                                <td><span style="font: 14px;margin-left: 10px;color: blue;"><?=$val3['progress']==''?0:$val3['progress']?> %</span></td>
                                <td><span style="font: 14px;margin-left: 10px;color: blue;"><?=$val3['task_status']?></span></td>
                                <td><span style="font: 14px;margin-left: 10px;color: blue;"><?=$val3['credit_point']?></span></td>
                                <td><span style="font: 14px;margin-left: 10px;color: blue;"><?=$val3['reward_point']?></span></td>
                                </tr>
                                <?php 
                                $t_number++;
                                $s_number++;
                                endforeach;?>
                           <?php  endif;?>
                            <?php if($subData):?>
                               <tr> <td colspan="7"><label style="font: 18px;margin-left: 10px;color: green;"><i class="fa fa-folder-open-o" aria-hidden="true"></i> <?=$m_number.".".$s_number." ".$subData['task_name']?></label></td></tr>
                               
                                <?php 
                                $t_number=1;
                                foreach ($val2 as $key3 => $val3):
                                    $userData = \backend\modules\ezforms2\classes\EzfQuery::getUserProfile($val3['task_owner']);
                                    $startDate = SubjectManagementQuery::convertDate(date('Y-m-d', strtotime($val3['start_date'])));
                                    $endDate = SubjectManagementQuery::convertDate(date('Y-m-d', strtotime($val3['end_date'])));
                                    ?>
                               <tr>
                                <td><span style="font: 14px;margin-left: 30px;color: blue;"><i class="fa fa-file-text-o" aria-hidden="true"></i> <?=$m_number.".".$s_number.".".$t_number." ".$val3['task_name']?></span></td>
                                <td><span style="font: 14px;margin-left: 30px;color: blue;"><?=$startDate?></span></td>
                                <td><span style="font: 14px;margin-left: 30px;color: blue;"><?=$endDate?></span></td>
                                <td><span style="font: 14px;margin-left: 30px;color: blue;"><?=$val3['progress']==''?0:$val3['progress']?> %</span></td>
                                <td><span style="font: 14px;margin-left: 30px;color: blue;"><?=$val3['task_status']?></span></td>
                                <td><span style="font: 14px;margin-left: 30px;color: blue;"><?=$val3['credit_point']?></span></td>
                                <td><span style="font: 14px;margin-left: 30px;color: blue;"><?=$val3['reward_point']?></span></td>
                                </tr>
                                <?php 
                                $t_number++;
                                endforeach;?>
                                
                            <?php endif;?>
                        <?php 
                        $s_number++;
                        endforeach;?>
                    <?php endif;?>
                    
                <?php endforeach;?>
            </tbody>
        </table>
        </div>
    </div>
</div>
<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>

<script>
    
</script>
<?php \richardfan\widget\JSRegister::end(); ?>