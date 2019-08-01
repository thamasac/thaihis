<?php 
    use appxq\sdii\widgets\ModalForm;
    cpn\chanpan\assets\CNLoadingAssets::register($this);  
    
    $domain = isset(Yii::$app->params['current_url']) ? Yii::$app->params['current_url'] : \cpn\chanpan\classes\CNServerConfig::getDomainName();
    $main_url = \backend\modules\core\classes\CoreFunc::getParams('main_url', 'url');

?>  
<?php if(cpn\chanpan\classes\CNServerConfig::isLocal() || $domain==$main_url || cpn\chanpan\classes\CNServerConfig::isTest()){?>
<?php backend\modules\ezforms2\classes\EzfStarterWidget::begin()?>
   
<div class=" box">   

    <div id="inv-person-grid-pjax">    
        <div class="modal-header" style="margin-bottom: 15px;">
            <div class="modal-title" id="itemModalLabel">
                <div class="row">
                    <div class="col-md-3" style="margin-bottom:10px;">
                        <?php
                            echo \yii\helpers\Html::button("<i class='glyphicon glyphicon-plus'></i> Create New Project",[
                                'class'=>'btn btn-success btn-block btnCreateProject',
                                'id'=>'btnCreateProject',
                                'style'=>'font-weight: bold;'
                             ]);
                         ?>
                    </div>
                    <div class="col-md-3">
                        <?= yii\helpers\Html::a("<i class='fa fa-cogs'></i> Manage the Created Projects",'/manageproject/setting-project', 
                            [
                                'id'=>'btnManageProject',
                                'class'=>'btn btn-default btn-block',
                                'style'=>'font-weight: bold;'   

                            ]);?>    
                    </div>
                </div>
                 
                  
            </div>
        </div>
    </div>
    
    <div class="modal-body">
<!--        <div class="row">-->
            <div id="showProjectAll">

            </div>
<!--        </div>-->
    </div>
</div>
 

<?php backend\modules\ezforms2\classes\EzfStarterWidget::end()?> 
<?php \richardfan\widget\JSRegister::begin();?>
    <script>
        $('.btnCreateProject').on('click', function(){

            let url = '<?= \yii\helpers\Url::to(['/manageproject/template/index'])?>';
            $.get(url, function(data){
                $('#modal-ezform-main').modal('show');
                $('#modal-ezform-main .modal-content').html(data);
            });
            return false;
        });
    </script>
<?php \richardfan\widget\JSRegister::end(); ?>
<?php 
    $this->registerJs("
       
        showProjectAll=function(){
            let url = '".yii\helpers\Url::to(['/manageproject/clone-project/get-project-all', 'status'=>1])."';
            $.get(url,function(data){
                $('#showProjectAll').html(data);
                $('button[type=\"submit\"][value=\"1\"]').attr('disabled', false);
            });
        }
        
        showProjectAll();
    ");
?>

<?php }?>