
<?php 
    $this->title = Yii::t('chanpan','Switch Site');
    \backend\modules\ezforms2\classes\EzfStarterWidget::begin();
    use appxq\sdii\helpers\SDNoty;
    use appxq\sdii\helpers\SDHtml;
    
?>
<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="panel panel-info">
            <div class='panel-heading'>
                <div class="row">
                    <div class="col-md-8 col-sm-8 col-xs-8"><i class="fa fa-sitemap" aria-hidden="true"></i> <?= Yii::t('chanpan', 'Site Enable') ?></div>
                    <div class="col-md-4 col-sm-4 col-xs-4 text-right">
                        <?php
                        $ezf_id = '1552458740063735000';
                        $modal_switch_site = "modal-switch-site";
                        $reload_dev = "switch-site"; 

                        echo backend\modules\ezforms2\classes\BtnBuilder::btn()
                                ->ezf_id($ezf_id)
                                ->options(['class' => 'btn btn-success' , 'type'=>'button'])
                                ->modal($modal_switch_site)
                                ->reloadDiv($reload_dev)
                                ->label('<i class="fa fa-plus"></i>')
                                ->buildBtnAdd();
                        ?>
                        
                    </div>
                </div>
            </div>
            <div class="panel-body">

                <?php
                    $grid_switch_site= \backend\modules\ezforms2\classes\EzfHelper::ui($ezf_id)
                     ->data_column(['user_id','switch_site'])            
                     ->default_column(0)
                     ->reloadDiv($reload_dev)
                     ->modal($modal_switch_site)
                     //->search_column(['module_id' => $module_id])
                     ->addbtn(FALSE);
                    echo $grid_switch_site->buildGrid();

                ?>
                <?=
                        
                        \appxq\sdii\widgets\ModalForm::widget([
                            'id' => 'modal-switch-site',
                            'size' => 'modal-xxl',
                            'tabindexEnable' => false,
                        ]);
                        ?>
            </div>
        </div>
    </div>
</div>
<?php \backend\modules\ezforms2\classes\EzfStarterWidget::end(); ?> 

<!-- Modal -->
<div id="modal-switch-site-config" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content"> 
      <div class="modal-body">
          <?php \yii\bootstrap\ActiveForm::begin(['id'=>'switch-site-form']);?>
          <div>
                <button type="button" class="close" data-dismiss="modal">&times;</button> 
                <?php 
                   
                    $init_site = (new \yii\db\Query())
                    ->select(['site_name','site_detail'])
                    ->from('zdata_sitecode')
                    ->where('site_name=:site_name', [':site_name'=> common\modules\user\classes\CNSitecode::getSiteCodeCurrent()])
                    ->one();
            
                    echo \yii\helpers\Html::label(Yii::t('chanpan','Select Site'));
                    echo kartik\select2\Select2::widget([
                        'name' => 'switch_site',
                        //'data' => $data,
                        'initValueText'=> isset($init_site['site_detail'])?$init_site['site_detail']:'',
                        'value' => isset($init_site['site_name'])?$init_site['site_name']:'',
                        'options' => ['placeholder' => 'Select site ...'],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'minimumInputLength' => 0,
                            'ajax' => [
                                'url' => \yii\helpers\Url::to(['/ezforms2/select-site-single/get-site-all']),
                                'dataType' => 'json',
                                'data' => new \yii\web\JsExpression('function(params) { return {q:params.term}; }')
                            ],
                            'escapeMarkup' => new \yii\web\JsExpression('function (markup) { return markup; }'),
                            'templateResult' => new \yii\web\JsExpression('function(user) { return user.text; }'),
                            'templateSelection' => new \yii\web\JsExpression('function (user) { return user.text; }'),
                        ],
                    ]);
                ?>
          </div>
          <div class="text-right" style="margin-top:50px;">
            <button type="button" class="btn btn-danger" data-dismiss="modal"><?= Yii::t('chanpan','Close')?></button>  
            <button type="submit" class="btn btn-primary" ><?= Yii::t('chanpan','Submit')?></button>
          </div>
          <?php \yii\bootstrap\ActiveForm::end();?>
      </div>
      
    </div>

  </div>
</div>



<?php            \richardfan\widget\JSRegister::begin();?>
<script>
    init_switch_site = function(){
        let url = '/site/get-user';
        $.get(url, function(data){
            if(data){
                let data_obj = JSON.parse(data);
                $('#original_site').html(data_obj['site_detail']);
                $('#switch_site').html(data_obj['switch_site_detail']);
            }
        });
    }
    init_switch_site();
    
    $('form#switch-site-form').on('beforeSubmit', function(e) {
        var $form = $(this);
        $.post(
            $form.attr('action'), //serialize Yii2 form
            $form.serialize()
        ).done(function(result) {
            
            if(result.status == 'success') {
                <?= SDNoty::show('result.message', 'result.status')?>
                 $('#modal-switch-site-config').modal('toggle');
                 setTimeout(function(){
                     init_switch_site();
                 },500);
                
            } else {
                <?= SDNoty::show('result.message', 'result.status')?>
            } 
        }).fail(function() {
            <?= SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"')?>
            console.log('server error');
        });
        return false;
    });
</script>
<?php            \richardfan\widget\JSRegister::end();?>