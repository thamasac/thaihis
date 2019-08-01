<?php 
    use yii\helpers\Html;
?>

<div > 
    <strong class="clearfix">
        <img src="<?= $model['projecticon']; ?>" style="width:90px;border-radius:5px;" class="img-rounded" data-toggle="tooltip" data-placement="bottom" title="<?= "{$model['projectname']}"?>"/>
    </strong>
    <strong class="clearfix">
        <?= cpn\chanpan\classes\utils\CNUtils::lengthName($model['projectacronym']) ?>
    </strong>
</div>
<div class="children btnManage" data-id="<?= $model['id']?>" style="padding: 10px;">
 
   <?php
        echo Html::button(" <i class='fa fa-bars'></i> ", [
            'class' => 'btn btn-xs btn-default btnDrag',
                'data-id' => $model['id']
            ]);
        echo " ";
        if($model['mode'] != 'co'  &&  $model['mode'] != 'assign'){
            
            $collaboration = $model['collaboration'];
            $userIsCreate = \Yii::$app->user->id == $model["user_create"]; 
            if(($collaboration == 1 || $collaboration == 2) && !$userIsCreate){
                echo Html::button(Yii::t('project','Join'), 
                        ['class'=>'btn btn-xs btn-success btnJoin',
                            'data-collaboration'=>$collaboration, 
                            'data-id'=>$model['id'] , 
                            'data-checkurl'=> "https://{$model['projurl']}.{$model['projdomain']}" ,
                            'title'=>'Request join project']);
            }            
        }
        if($model['mode'] === 'assign' || $model['mode'] == 'co'){
            echo " ";
            echo Html::button(" <i class='fa fa-times'></i> ", [
                'class'=>'btn btn-xs btn-danger btnDiscon', 
                'data-id'=>$model['id'], 
                'data-type'=>'10',
                'title'=> Yii::t('project','Request for discontinuatio')
            ]);
        }
        if($model['mode'] != 'assign' && $model['mode'] != 'seek'){
            
            echo " ";
            echo Html::button(" <i class='mdi mdi mdi-settings'></i> ", ['class'=>'btn btn-xs btn-warning btnEdits', 'data-id'=>$model['id'], 'title'=> Yii::t('project','Setting')]);
        }
   ?> 
 
</div> 
<?php $modalf = 'discon-modal';?>
<?php   richardfan\widget\JSRegister::begin();?>
<script>
    $('.btnDiscon').on('click', function() {
        let id=$(this).attr('data-id');
        let project_type = $(this).attr('data-type');
        $('#<?= $modalf ?> .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
        $('#<?= $modalf ?>').modal('show');
        let url = '/manageproject/center-project/request-for-discontinuation';
           
        $.post(url,{id:id, project_type:project_type}, function(res){
           $('#<?= $modalf ?> .modal-content').html(res);                    
        });
        return false;
    });
    
    $('[data-toggle="tooltip"]').tooltip({
      track: true
    });
</script>
<?php   richardfan\widget\JSRegister::end();?>