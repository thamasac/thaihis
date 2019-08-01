<?php 
    use yii\helpers\Url;
    use yii\helpers\Html;
    use backend\modules\ezforms2\classes\EzfAuthFuncManage;
    \cpn\chanpan\assets\CNLoadingAssets::register($this);
?>
<?php 
    echo yii\bootstrap\Modal::widget([
        'id'=>'modal-project',
        'size'=>'modal-xxl',
        'options'=>['tabindex' => false]
    ]);
   
?>
<?php if($status == 'view'): ?>
<div class="row">
    
    <div class="col-md-12">
        <?php if(EzfAuthFuncManage::auth()->accessManage($module_id, 1)){?>
        <div class="pull-right">
            <div class="col-md-12" style="margin-bottom:10px;">
                <?= Html::button("<i class='fa fa-edit'></i> ".Yii::t('chanpan','Edit'), ['id'=>'btnEdit','class'=>'btn btn-success btn-sm']);?>
                <?= Html::button("<i class='fa fa-unlock-alt'></i> ".Yii::t('chanpan','Permission'), ['id'=>'btnPermission','class'=>'btn btn-warning btn-sm']);?>
            </div>
        </div>
        <?php }?>
        <div class="clearfix"></div>
        <div id="view-modules"></div>
    </div>
    
</div>
<?php \richardfan\widget\JSRegister::begin();?>
<script>
     $('#btnEdit').click(function(){
       let url = '<?= Url::to(['/manage_modules/default/update']);?>';  
       $.get(url, {status:'update'}, function(data){
           if(data.status == 'success'){
               location.reload();
           }
       });
       return false;
     });
     $('#btnPermission').click(function(){
       let url = '<?= Url::to(['/manage_modules/default/permission']);?>';  
       $('#modal-project .modal-content').html('<div class="sdloader "><i class="sdloader-icon"></i></div>');
       $('#modal-project').modal('show');
       $.get(url, function(data){
           $('#modal-project .modal-content').html(data); 
       });
       return false;
     });
      
</script>
<?php \richardfan\widget\JSRegister::end();?>


<?php \richardfan\widget\JSRegister::begin();?>
<script>
    function loadViewsModule(){
       let url = '<?= Url::to(['/manage_modules/default/view-module']);?>';
        $.get(url, function(data){
           $('#view-modules').html(data);
        }); 
        
    }
    loadViewsModule();
</script>
<?php \richardfan\widget\JSRegister::end();?>

<?php endif; ?>





<?php if($status == 'update'): ?>
<div class="row">
        <div class="col-md-3 col-sm-3" style="border-right: 1px solid #eee;padding: 0;">
            <div id="module-all"></div>
        </div>
        <div class="col-md-9 col-sm-9">
            <div>
                <h4><?= Yii::t('chanpan', 'All Modules (Enable)'); ?></h4>
                
                <?= $this->render('_btn') ?>
                <hr/>
                <div id="manage-modules-enable"></div><hr />
                
            </div>

        </div>
    </div>
<?php \richardfan\widget\JSRegister::begin();?>
<script>
    function getModuleAll(){
        onLoadings();
        let url = '<?= Url::to(['/manage_modules/default/get-module']);?>';
        $.get(url, function(data){
           $('#module-all').html(data);
           hideLoadings();
        });
    }
    function getManageModules(){
        onLoadings();
        let url = '<?= Url::to(['/manage_modules/default/get-manage-module']);?>';
        $.get(url,{term:1},function(data){
           $('#manage-modules-enable').html(data);
           hideLoadings();
        });
    }
    function getManageModulesDisabled(){
        
    }
    getModuleAll();
    getManageModules();
    getManageModulesDisabled();
    function onLoadings(){
        $('#module-all').waitMe({
            effect : 'facebook',
            text : 'Please wait...',
            bg : 'rgba(255,255,255,0.7)',
            color : '#000',
            maxSize : '',
            waitTime : -1,
            textPos : 'vertical',
            fontSize : '',
            source : '',
            onClose : function() {}
        });
    }
    function hideLoadings(){
         $('#module-all').waitMe("hide");
    }
</script>
<?php \richardfan\widget\JSRegister::end();?>
<?php endif; ?>

