<?php 
    use yii\helpers\Url;
?>
<div class="col-md-12">
    <table id="table-patient" class="table table-bordered">
        <thead class="table-default">
            <tr>
                <th width="5%" style="text-align: center;"><label style="font-size: 18px;" >ภาพ US</label></th>
                <th width="23%" style="text-align: center;"><label style="font-size: 18px;" >ชื่อ-นามสกุล</label></th>
                <th width="6%" style="text-align: center;"><label style="font-size: 18px;">ICF</label></th>
                <th width="6%" style="text-align: center;"><label style="font-size: 18px;">ภาพ<br/>บัตร</label></th>
                <th width="6%" style="text-align: center;"><label style="font-size: 18px;">CCA01</label></th>
                <th width="6%" style="text-align: center;"><label style="font-size: 18px;">CCA02</label></th>
                <th width="6%" style="text-align: center;"><label style="font-size: 18px;">US<br/>Photo</label></th>
                <th width="6%" style="text-align: center;"><label style="font-size: 18px;">ลงชื่อ แพทย์</label></th>
                <th width="6%" style="text-align: center;"><label style="font-size: 18px;">ถูกต้อง</label></th>
                <th width="15%" style="text-align: center;"><label style="font-size: 18px;">ผลตรวจหลัก</label></th>
                <th width="6%" style="text-align: center;"><label style="font-size: 18px;">Refer</label></th>
                <th style="text-align: center;"><label style="font-size: 18px;">Status</label></th>
            </tr>
        </thead>

        <tbody>
            <?php
           
            $x = 1;
            foreach ($patientList as $key=> $value) {
                
                $usimage = $value['usimage']==''?'0':$value['usimage']; 
                $patient_data = \backend\modules\usfinding\classes\QueryMonitor::GetPatientById($value['ptid'],$value['hsitecode']);
                ?>
                <tr>
                    <th>
                        <img id="usimage" src="https://tools.cascap.in.th/api/us/imglist.php?ptid=<?=$value['ptid']?>&id=<?=$value['dataid_02']?>" height="50" style="vertical-align: text-top;">
                    </th>
                    <th>
                        <label class="label label-danger" style="font-size:16px;"><?= $x ?></label><label style="font-size:16px;" class="label label-primary"><?= $patient_data['ptcodefull'] ?></label><br/>
                        <label style="font-size:18px;"><?= $patient_data['title'] . '' . $patient_data['name'] . ' ' . $patient_data['surname']; ?></label>
                    </th>
                    <th style="font-size: 25px;text-align: center;">
                        <label ><?php if($value['icf_upload1'] == '') 
                                    echo"<center><i class='glyphicon glyphicon-remove-circle' aria-hidden='true' style='color:red;'></i></center>"; 
                                else if($value['icf_upload1'] > 0) 
                                    echo "<center><i class='glyphicon glyphicon-ok-circle' aria-hidden='true' style='color:green;'></i></center>"; 
                                else
                                    echo "<center><i data-toggle='tooltip' title='ใบยินยอมยังไม่ผ่านการตรวจสอบ!' class='glyphicon glyphicon-exclamation-sign' aria-hidden='true' style='color:orange;'></i></center>";
                                ?>
                        </label>
                    </th>
                    <th style="font-size: 25px;text-align: center;">
                        <label ><?php echo $value['icf_upload2'] == '' ? "<center><i class='glyphicon glyphicon-remove-circle' aria-hidden='true' style='color:red;'></i></center>" : "<center><i class='glyphicon glyphicon-ok-circle' aria-hidden='true' style='color:green;'></i></center>"; ?></label>
                    </th>
                    <th  style="font-size: 25px;text-align: center;">
                        <label>
                            <a target="_blank" href="<?=Url::to(['/usfinding/monitoring/open-form','ezf_id'=>'1437377239070461302', 'dataid'=>$value['dataid_01']])?>">
                                <?php echo $value['dataid_01'] == '' ? "<center><i class='glyphicon glyphicon-remove-circle' aria-hidden='true' style='color:red;'></i></center>" : "<center><i class='glyphicon glyphicon-ok-circle' aria-hidden='true' style='color:green;'></i></center>"; ?>
                            </a>
                        </label>
                    </th>
                    <th style="font-size: 25px;text-align: center;">
                        <label >
                            <a target="_blank" href="<?=Url::to(['/usfinding/monitoring/open-form','ezf_id'=>'1437619524091524800', 'dataid'=>$value['dataid_02'],])?>">
                            <?php echo $value['dataid_02'] == '' ? "<center><i class='glyphicon glyphicon-remove-circle' aria-hidden='true' style='color:red;'></i></center>" : "<center><i class='glyphicon glyphicon-ok-circle' aria-hidden='true' style='color:green;'></i></center>"; ?>
                        </a>
                        </label>
                        
                    </th>
                    <th  style="font-size: 22px;text-align: center;">
                        <div id="usimg<?=$key?>" onblur="loadUsimg()">
                            <?= $usimage?>
                        </div>
                    </th>
                    <th  style="font-size: 25px;text-align: center;">
                        <label><?php echo $value['f2doctorcode'] == '' ? "<center><i class='glyphicon glyphicon-remove-circle' aria-hidden='true' style='color:red;'></i></center>" : "<center><i class='glyphicon glyphicon-ok-circle' aria-hidden='true' style='color:green;'></i></center>"; ?></label>
                    </th>
                    <th  style="font-size: 25px;text-align: center;">
                       
                        <label><?php  echo $value['error'] != '' ? "<center><a href='javascript:void(0)' onclick=\"onThrowError('".$value['error']."')\"><i class='glyphicon glyphicon-remove-circle' aria-hidden='true' style='color:red;'></i></a></center>" : "<center><i class='glyphicon glyphicon-ok-circle' aria-hidden='true' style='color:green;'></i></center>"; ?></label>
                    </th>
                    <th style="font-size: 20px;text-align: center;">
                        <label ><?php echo $value['result_inspect']?></label>
                    </th>
                    <th  style="font-size: 25px;text-align: center;">
                        <label><?php echo $value['refer'] == false ? "<center><i class='glyphicon glyphicon-remove-circle' aria-hidden='true' style='color:red;'></i></center>" : "<center><i class='glyphicon glyphicon-ok-circle' aria-hidden='true' style='color:green;'></i></center>"; ?></label>
                    </th>
                    <th  style="font-size: 25px;text-align: center;">
                        <label>
                            <?php
                            
                            if ($value['status'] == -1) {
                                echo "<center><label class='label label-danger'>มีข้อผิดพลาด</label></center>";
                            } else if ($value['inspected'] == '1') {
                                echo "<center><label class='label label-success'>ตรวจเสร็จแล้ว</label></center>";
                            } else if($value['inspecting'] == '1'){
                                echo "<center><label class='label label-primary'>กำลังตรวจ</label></center>";
                            } else if($value['startinspect'] == '1'){
                                 echo "<center><label class='label label-warning'>เริ่มตรวจ</label></center>";
                            }
?>
                            </label>
                        </th>
                    </tr>
                    <?php
                    $x++;
                }
                ?>
        </tbody>
    </table>
</div>
<?php 
$this->registerJs("
    function onOpenForm(ezf_id, data_id){
        $.post({

        });
    }
    function loadUsimg(){
        console.log('Loading');
    }
    
");
?>