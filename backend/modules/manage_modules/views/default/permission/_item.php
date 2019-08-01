<?php 
    use backend\modules\ezforms2\classes\BtnBuilder;
    use backend\modules\ezforms2\classes\EzfHelper;
    use backend\modules\ezforms2\classes\EzfAuthFuncManage;
    use yii\helpers\Url;
    use yii\helpers\Html;
    $reloadDiv = "permission"; 
    $modal = "modal-ezform-main"; 
    $ezfId='1519707087068015000';
?>
<?php 
 if(empty($data)){
     echo Html::beginTag("TABLE", ['class'=>'table table-bordered']);
     echo Html::beginTag("TBODY");     
     echo Html::beginTag("TR"); 
        $contentEveryOne = Html::tag("LABEL", "Every one");
        echo Html::tag("TD", $contentEveryOne, ['style'=>'width:300px']);
        echo Html::tag("TD", "Access denied", ['style'=>'width:300px']);
        $iconPermission = Html::a("<i class='fa fa-info-circle'></i>", Url::to(['/ezforms2/ezform-data/ezform?ezf_id=1519707087068015000&dataid=&modal='.$modal.'&reloadDiv=permission&db2=0']),
                ['class'=>'btnEditPermission', 'data-id'=>$model['module_id'],'style'=>'color:#F44336;']);
        echo Html::tag("TD", $iconPermission, ['style'=>'width:50px;text-align: center;font-size: 14pt;']);
     echo Html::endTag("TR"); 
     
     echo Html::endTag("TBODY"); 
     echo Html::endTag("TABLE"); 
 }
    
?>
<table class="table table-bordered"> 
    <?php if(!empty($data)):?>
<!--        <thead>
            <tr>
                <th>Role</th>
                <th>Permission</th>
            </tr>
        </thead>-->
    <?php endif; ?>
    <tbody>
        <?php foreach($data as $d):?>
        <?php 
            $every_one = '';
            $roleName = isset($d['role_name']) ? $d['role_name'] : '';
            $dataRole =(new yii\db\Query())
                      ->select('*')->from('zdata_role')
                      ->where('role_name=:role_name',[':role_name'=>$roleName])
                      ->andWhere('rstat not in(0,3)')->all();
            if($d['member'] == '1'){                
                $every_one .= "<td style='width:300px'><label>Every one</label></td>";
                $every_one .= "<td style='width:300px'>";
                            if ($d['permission_type'] == '1') {   
                                $every_one .= "Manage ";
                        } else if ($d['permission_type'] == '2') {
                                $every_one .= "Read/Write ";
                        } else if ($d['permission_type'] == '3') {
                                $every_one .= "Read";
                        }
                $every_one .= "</td>";        
                
                $every_one .= "<td style='width:50px;text-align: center;font-size: 14pt;'>
                                <a data-id=".$model['module_id']." href=".Url::to(['/ezforms2/ezform-data/ezform?ezf_id=1519707087068015000&dataid='.$d['id'].'&modal='.$modal.'&reloadDiv=permission&db2=0'])." class='btnEditPermission text-info'><i class='fa fa-info-circle'></i></a>
                            </td>";
                echo $every_one;  
            }
        ?>
        
        <?php foreach($dataRole as $dr):?>
            <tr>
                
                <td style='width:300px'><?= $dr['role_detail']?> (<?= $dr['role_name']?>)</td>
                <td style='width:300px'><?php
                       
                        if ($d['permission_type'] == '1') {   
                                echo "Manage ";
                        } else if ($d['permission_type'] == '2') {
                                echo "Read/Write ";
                        } else if ($d['permission_type'] == '3') {
                                echo "Read";
                        }
                   ?>
                </td>
                <td style='width:50px;text-align: center;font-size: 14pt;'>
                    <?php if($d['member'] == 2):?>
                    <a data-id="<?= $model['module_id']?>" href='<?= Url::to(['/ezforms2/ezform-data/ezform?ezf_id=1519707087068015000&dataid='.$d['id'].'&modal='.$modal.'&reloadDiv=permission&db2=0'])?>' style="color:#f0ad4e;" class="btnEditPermission"><i class='fa fa-info-circle'></i></a>
                    <?php else:?>
                        <a data-id="<?= $model['module_id']?>" href='<?= Url::to(['/ezforms2/ezform-data/ezform?ezf_id=1519707087068015000&dataid='.$d['id'].'&modal='.$modal.'&reloadDiv=permission&db2=0'])?>' class="btnEditPermission text-primary"><i class='fa fa-info-circle'></i></a>
                         
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php endforeach; ?>
    </tbody>
</table>
<?php \richardfan\widget\JSRegister::begin();?>
<script>
    $('.btnEditPermission').on('click', function(){
       let url = $(this).attr('href');
       $('#<?= $modal?> .modal-content').html('<div class="sdloader "><i class="sdloader-icon"></i></div>');
       $('#<?= $modal?>').modal('show');
       let module_id = $(this).attr('data-id');
       
       $.get(url, function(data){
            $('#<?= $modal?> .modal-content').html(data);
            setTimeout(function(){
                $('#ez1519707087068015000-module_id').val(module_id);
            },500);
       });
       return false;
    });     
</script>

<?php richardfan\widget\JSRegister::end()?>