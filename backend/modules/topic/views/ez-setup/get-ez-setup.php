<?php 
    $statusArr = ['1'=>'<b class="c-orange">Waiting</b>', '2'=>'<b>NA</b>', '3'=>'<b>Skipped</b>', '4'=>'<b>Ongoing</b>', '5'=>'<b class="c-green">Done</b>'];
    $priority = ['1'=>'<b class="c-red">Required</b>', '2'=>'<b class="c-blue">Optional</b>'];
?>
<h2 class="text-center">EzSetup</h2>
<div class="pull-right">
    <?php 
        $itemsDemo = ['1'=>'1.Waiting', '2'=>'2.NA', '3'=>'3.Skipped', '4'=>'4.Ongoing', '5'=>'5.Done'];
        echo yii\helpers\Html::radioList("demo", '', $itemsDemo, ['id'=>"radio"]);
    ?>
    <br/>
</div>
<div class="clearfix"></div>
<table class="table table-responsive table-bordered table-striped">
    <thead>
        <tr>
            <th class='text-center f-s-16'>Priority</th>
            <th class='text-center f-s-16'>Status</th>
            <th class='text-center f-s-16'>Steps</th>
            <th class='text-center f-s-16 w-280'>Actions Taken</th>
            
        </tr>
    </thead>
    <tbody>
        <?php foreach($model as $k=>$v): ?>
        <tr>
            <td>
                <?php 
                    if($v['priority'] != ''){
                        echo "<span class='f-s-16'>".$priority[$v['priority']]."</span>";
                    }
                ?>
            </td>
            <td>
                <?php 
                    
                    if($v['status'] != ''){
                        echo "<span class='f-s-16'>".$statusArr[$v['status']]."</span>";
                    }
                    
                ?>
            </td>
            <td>
                <?php 
                    if($v['parent_id'] == 0){
                        echo "<a target='_blank' href='{$v['link']}'><b class='f-s-16'>{$v['steps']}</b></a>";
                    }else{
                        if($v['action_taken'] == ''){
                            echo "<span class='f-s-16'><input value='5' id='radio-{$v['id']}' type='radio' class='radio_parent' data-parent-id='{$v['parent_id']}' data-id='{$v['id']}'> ".$v['steps']."</span>";
                        }else{
                            echo "<span class='f-s-16'><a target='_blank' href='{$v['link']}'>".$v['steps']."</a></span>";
                        }
                    }
                    if(Yii::$app->user->id == '1'){
                        echo ' ';
                        echo yii\bootstrap\Html::button("<i class='fa fa-pencil'></i>", [
                            'class'=>'btn btn-primary btn-xs btn-ezsetup-edit',
                            'data-id'=>$v['id'],
                            'widget_id'=>$id
                            ]);
                    }
                ?>
            </td>
            <td>
                <?php 
                    $items = ['1'=>'1', '2'=>'2','3'=>'3','4'=>'4','5'=>'5'];
                    if($v['action_taken'] != ''){
                        echo yii\helpers\Html::radioList("action_taken_{$v['id']}", $v['action_taken'], $items, ['class'=>'action_taken_radio','data-id'=>$v['id'],'id'=>"radio-{$v['id']}"]);
                    }
                    
                ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php 
    $modalId = 'modal-ez-setup';
    echo yii\bootstrap\Modal::widget([
        'id'=>$modalId,
        'size'=>'modal-xl',
        'options'=>['tabindex' => false]
   ]);
?>

<?php \richardfan\widget\JSRegister::begin();?>
<script>
    
    
    $('.btn-ezsetup-edit').on('click', function(){
        let id = $(this).attr('data-id');
        let widget_id = $(this).attr('widget_id');
        let url = '/topic/ez-setup/update-form';
        $('#<?= $modalId?>').modal('show');
        $('#<?= $modalId?> .modal-content').html('<i class="fa fa-spinner fa-spin fa-fw"></i>');
        $.get(url,{id:id, widget_id:widget_id},function(data){
            $('#<?= $modalId?> .modal-content').html(data); 
        });
       return false;
    });
    $('.radio_parent').on('change', function(){
       let parent_id = $(this).attr('data-parent-id');
       let id = $(this).attr('data-id');
       let val = $(this).val();
       let url = '/topic/ez-setup/update-by-parent';
       $.post(url, {id:id,parent_id:parent_id, value:val}, function(result){
           if(result.status == 'success'){
               <?= \appxq\sdii\helpers\SDNoty::show('result.message', 'result.status') ?>
               setTimeout(function(){
                   loadEzSetup();
               },1000);
           }else{
               <?= \appxq\sdii\helpers\SDNoty::show('result.message', 'result.status') ?>
           }
       });
       return false;
    });
    $(".action_taken_radio input[type='radio']").on('change',function(){
       let val = $(this).val();
       let name = $(this).attr('name');
       let _name = name.split("_");
       let id = _name[2];
       let url = '/topic/ez-setup/update';
       $.post(url, {id:id, value:val}, function(result){
           if(result.status == 'success'){
               <?= \appxq\sdii\helpers\SDNoty::show('result.message', 'result.status') ?>
               setTimeout(function(){
                   loadEzSetup();
               },1000);
           }else{
               <?= \appxq\sdii\helpers\SDNoty::show('result.message', 'result.status') ?>
           }
       });
       return false;
    });
    function loadEzSetup(){
       
        let url = '/topic/ez-setup/get-ez-setup';
        $.get(url,{id:'<?= $id?>'}, function(data){
            $('#<?= $id?>').html(data);
             $('#radio-45').hide();
        });
    }
    $('#radio-45').hide();
</script>
<?php \richardfan\widget\JSRegister::end();?>

<?php   \appxq\sdii\widgets\CSSRegister::begin();?>
<style>
    .c-red{
        color:red;
    }
    .c-blue{
        color:blue;
    }
    .c-orange{
        color:orange;
    }
    .c-green{
         color:green;
    }
    input[type="radio"] {
        margin: 4px 7px 0 !important;
    }
    .f-s-16{
        font-size:14pt;
    }
    .w-280{
        width:280px;
    }
</style>
<?php   \appxq\sdii\widgets\CSSRegister::end();?>