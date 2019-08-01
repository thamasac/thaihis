<div class="table-responsive">
<?php
    use appxq\sdii\helpers\SDNoty;
    use appxq\sdii\helpers\SDHtml;
    \backend\modules\ezforms2\assets\JLoading::register($this);
    echo \yii\grid\GridView::widget([
       'dataProvider'=>$dataProvider,
       'tableOptions' => ['id'=>'tables','class' => 'table table-hover table-bordered table-responsive'],
       'columns'=>[
//           [
//               'class' => 'yii\grid\SerialColumn',
//               'header' => '#',                
//           ],
           
            [
              'format'=>'raw',
              'label'=>'',
              'value'=>function($model){
                  $imgPath = \Yii::getAlias('@storageUrl');
                  $imgBackend = \Yii::getAlias('@backendUrl');
                  $imageSec = "/img/health-icon.png";
                  $imgUrl = '';
                  if(!empty($model['projecticon'])){
                      $imgUrl = "{$imgPath}/ezform/fileinput/{$model['projecticon']}";
                  }else{
                      $imgUrl = $imageSec;
                  }
                  return \yii\helpers\Html::img($model['projecticon'], ['style'=>'width:40px;height:40px;border-radius:3px;']);  
              }
            ],       
            [
                'format'=>'raw',
                'label'=>'Acronym / URL',
                'attribute'=>'projectacronym',
                'value'=> 
                function($model){
                   $html = "
                       <a title='".$model['projectname']."' target='_blank' href='https://".$model['projurl'].".".$model['projdomain']."' style='text-decoration: none;color:#333'>
                       <div class='' style='width:100%;display: block;'>{$model['projectacronym']}</div>
                       <label>{$model['projurl']}.{$model['projdomain']}</label>                           
                       </a>    
                    ";                   
                   return $html;
                }
            ],
//                     
            [
                'label'=>'PI Name',
                'attribute'=>'pi_name',
                'value'=>'pi_name'
            ],
            [
                'label'=>'Created By',
                'attribute'=>'create_by',
                //'attribute'=>'pi_name',
                'value'=>function($model){ 
                    $data= common\modules\user\classes\CNUserFunc::getUserById('ncrc', $model['user_create']);
                    if(!empty($data)){
                        return $data['firstname'] . ' '.$data['lastname'];
                    }
                    
                }
            ],
            [
                'label'=>'Date Created',
                'attribute'=>'create_date',
                //create_date , update_date
                'value'=>function($model){
                    $date = ($model['update_date'] != '') ? $model['update_date'] : $model['create_date'];
                    if($date != ''){
                        return appxq\sdii\utils\SDdate::mysql2phpDate($date);
                    }
                    return $date;
                }
            ],        
            [
                'format'=>'raw',
                'label'=> Yii::t('chanpan','Status'),
                'attribute'=>'rstat',
                'value'=>function($model){
                    return ($model['rstat'] == 3 || $model['rstat'] == 0) ? "<div class='label label-danger' style='display: block;'>Deleted</div>" : "<div class='label label-success' style='display: block;'>Active</div>";
                    
                }
            ],
           [
                'headerOptions'=>['style'=>'text-align:center;width:400px;'],
                'class' => 'yii\grid\ActionColumn',
                'template' => '{edit} {restore} {clone} {backup} {delete} {destroy}', 
                'buttons' => [
                    'edit' => function($url, $model, $key) {
                        if($model['rstat'] == 1){
                        return \yii\bootstrap\Html::a("<i class='fa fa-pencil'></i> " . Yii::t('chanpan', 'Edit'),
                                \yii\helpers\Url::to(["/ezforms2/ezform-data/ezform?ezf_id=1523071255006806900&dataid={$model['id']}&modal=modal-1523071255006806900&reloadDiv=modal-divview-1523071255006806900&db2=0"]),
                                ['title'=>'Update Project','data-action' => 'edit', 'class' => 'btn btn-xs btn-success btnProject', 'data-id' => $model['id']]);
                        }         
                    },
                    'update' => function($url, $model, $key) {
                        if($model['rstat'] == 1){
                        return \yii\bootstrap\Html::a("<i class='fa fa-wrench'></i> " . Yii::t('chanpan', 'Repair'),
                                \yii\helpers\Url::to(["/manageproject/setting-project/repair?id={$model['id']}"]),
                                ['title'=>'Update Project','data-action' => 'update', 'class' => 'btn btn-xs btn-warning btnProject', 'data-id' => $model['id']]);
                        }
                    },
                    'restore'=>function($url, $model, $key){
                            if($model['rstat'] == 3){
                                return \yii\bootstrap\Html::a("<i class='fa fa-refresh'></i> ".Yii::t('chanpan','Restore'),
                                    \yii\helpers\Url::to(['/manageproject/setting-project/restore', 'id'=>$model['id']]), 
                                    ['title'=>'Restore Project', 'data-action'=>'restore','class'=>'btn btn-xs btn-success btnProject','data-id'=>$model['id']]);
                            }    
                    },
                    'clone' => function($url, $model, $key) {
                        if($model['rstat'] == 1){
                        return \yii\bootstrap\Html::a("<i class='fa fa-clone'></i> " . Yii::t('chanpan', 'Clone'),
                                \yii\helpers\Url::to(["/manageproject/monitor-project/clone"]),
                                ['title'=>'Clone Project','data-action' => 'clone', 'class' => 'btn btn-xs btn-info btnProject', 'data-id' => $model['id']]);
                        }
                    },            
                    'backup' => function($url, $model, $key) { 
                        if($model['rstat'] == 1){
                        return \yii\bootstrap\Html::a("<i class='fa fa-hdd-o'></i> " . Yii::t('chanpan', 'Backup'),
                                \yii\helpers\Url::to(["/manageproject/backup-restore/backup"]),
                                ['title'=>'Backup Project','data-action' => 'backup', 'class' => 'btn btn-xs btn-default btnProject', 'data-id' => $model['id']]);
                        }
                    },          
                    'delete'=>function($url, $model, $key){
                             if($model['rstat'] == 1){
                                 return \yii\bootstrap\Html::a("<i class='fa fa-trash'></i> ".Yii::t('chanpan','Delete'),
                                        \yii\helpers\Url::to(['/manageproject/center-project/delete', 'id'=>$model['id']]), 
                                        ['title'=>'Delete Project','data-action'=>'delete','class'=>'btn btn-xs btn-danger btnProject', 'data-id'=>$model['id']]);
                             }
                    },
                    'destroy'=>function($url, $model, $key){
                             if($model['rstat'] == 3){
                                 return \yii\bootstrap\Html::a("<i class='fa fa-trash'></i> ".Yii::t('chanpan','Permanently delete'),
                                        \yii\helpers\Url::to(['/manageproject/setting-project/destroy', 'id'=>$model['id']]), 
                                        ['title'=>'Permanently delete','data-action'=>'pdelete','class'=>'btn btn-xs btn-danger btnProject', 'data-id'=>$model['id']]);
                             }
                    },        
                            
                ]
            ],         
       ],
       'rowOptions' => function ($model, $index, $widget, $grid) {
           return ['id' => $model['id']];             
       }
    ]);
?>
</div>
<?php      
    echo yii\bootstrap\Modal::widget([
        'id'=>'modal-create-project',
        'size'=>'modal-xxl',
        'options'=>['tabindex' => false]
    ]);
?>
<?php \richardfan\widget\JSRegister::begin();?>
<script>
    var setting_element = 'body';
    $('.btnProject').on('click', function(){
       let id = ''+$(this).attr('data-id');
       let action = $(this).attr('data-action');
       let url = $(this).attr('href');  
       if(action == 'edit'){
           Edit(id, url);
       }else if(action == 'update'){
           Update(id, url);
       }
       else if(action == 'restore'){
//           alert(url);
           Restore(id, url);
       }else if(action == 'clone'){
           Clone(id, url);
       }else if(action == 'backup'){
           Backup(id, url);
           return false;
       }
       else if(action == 'delete'){
           Delete(id, url);
       }
       else if(action == 'pdelete'){
           PDelete(id, url);
       }
       return false;
    });
    $('#my-project #tables tbody tr').dblclick(function(){
        let id = $(this).attr('id');
        let url = "/ezforms2/ezform-data/ezform?ezf_id=1523071255006806900&dataid="+id+"&modal=modal-1523071255006806900&reloadDiv=modal-divview-1523071255006806900&;db2=0";
        //alert(url);
        Edit(id, url);
        return false;         
    });
    function Clone(id, url){
        let uri = '<?= \yii\helpers\Url::to(['/manageproject/template/get-clone-form-create'])?>';
        $('#modal-create-project').modal('show');
        $('#modal-create-project .modal-content').html('<div class="sdloader "><i class="sdloader-icon"></i></div>');
        $.get(uri, {id:id}, function(data){ 
          $('#modal-create-project .modal-content').html(data); 
           return false;
        }); 
        return false;
    }
    function Edit(id, url){
        $('#modal-create-project').modal('show');
            $('#modal-create-project .modal-content').html('<div class="sdloader "><i class="sdloader-icon"></i></div>');
            $.get(url, function(data){                 
                $('#modal-create-project .modal-content').html(data); 
                 let proj_domain_str = $('#ez1523071255006806900-projdomain');
                 let project_acronym_str = $('#ez1523071255006806900-projectacronym');
                 let project_url = $('#ez1523071255006806900-projurl');
               
                 //$(proj_domain_str).replaceWith('<div style=\'padding: 7px; background: #e7e7ec;\'><b>'+proj_domain_str.val()+'</b></div>');
                 ///$(project_acronym_str).replaceWith('<div style=\'padding: 7px; background: #e7e7ec;\'><b>'+project_acronym_str.val()+'</b></div>');
                 //$(project_url).replaceWith('<div style=\'padding: 7px; background: #e7e7ec;\'><b>'+project_url.val()+'</b></div>');
                 var status_form = $('#ez1523071255006806900-status_form');
                 status_form.val('update');
                 setTimeout(function(){
                    $('#ezform-1523071255006806900').on('submit', function(e){
                       let id = $('#ezform-1523071255006806900').attr('data-dataid');
                       let uri = '/manageproject/center-project/update';
                       setTimeout(function(){
                           $.post(uri, {id:id}, function(data){
                               console.log(data);
                               //location.reload();
                           });
                           return false;
                       }, 2500);

                    });
                }, 1000);
                return false;
                
            });
          
        return false;
    }
    function Update(id, url){
        yii.confirm('Confirm Repair', function(){
            let ele = 'body';
            $('.btnProject').attr('disabled', true);
            onLoadings(ele);
            getUpdate(id, url);
        });
        return false;
    }
    function getUpdate(id, url){
        let ele = 'body';
        $.get(url, {id:id}, function(data){
            hideLoadings(ele);
            $('.btnProject').attr('disabled', false);
//            console.log(data);return;
            <?= SDNoty::show('data.message', 'data.status') ?>
            hideLoadings(ele);        
            
            let url = '/manageproject/setting-project/my-project/';             
            loadUrl(url);
            $('.btnProject').attr('disabled', false);
        }).fail(function(){
            hideLoadings(ele);
        });
    }
    function Delete(id, url){
        yii.confirm('Confirm Delete', function(){
            onLoadings(setting_element);//loading 
                $.post(url,{id:id}, function(data){
                    if(data.status=='success'){
                       <?= \appxq\sdii\helpers\SDNoty::show('data.message', 'data.status')?>                  
                    }else{
                        <?= \appxq\sdii\helpers\SDNoty::show('data.message', 'data.status')?>
                    }
                    let url = '<?= \yii\helpers\Url::to(['/manageproject/setting-project/my-project/'])?>';
                    setTimeout(function(){
                        loadUrl(url);
                    },1000);
                    setTimeout(function(){
                        hideLoadings(setting_element);
                    },1500);
                });
        });
        return false;
    }
    function PDelete(id, url){
        yii.confirm('<h4 style="color:#F44336"><i class="fa fa-warning"></i> Permanently Delete Warning Message : </h4><span style="color:#F44336">All files and data of this Project will be deleted permanently. If you wish to restore the Project in the future, please backup the Project then download the nCRC Backup File before this step.</span>', function(){            
            onLoadings(setting_element);//loading 
            $.post(url,{id:id}, function(data){
                if(data.status=='success'){
                    <?= \appxq\sdii\helpers\SDNoty::show('data.message', 'data.status')?>
                   $('.table tbody #'+id).remove();             
                }else{
                   <?= \appxq\sdii\helpers\SDNoty::show('data.message', 'data.status')?>
                }
                setTimeout(function(){
                        hideLoadings(setting_element);
                },1000);
                    
          });
        });
        return false;
    }
    function Restore(id, url){
        yii.confirm('Confirm Restore', function(){                
                $.post(url,{id:id}, function(data){ 
                    if(data.status=='success'){
                       <?= \appxq\sdii\helpers\SDNoty::show('data.message', 'data.status')?>                  
                    }else{
                        <?= \appxq\sdii\helpers\SDNoty::show('data.message', 'data.status')?>
                    }
                    let url = '/manageproject/setting-project/my-project/';
                    loadUrl(url);
                });
        });
        return false;
    }
    function Backup(id, url){
      yii.confirm('Backup file Project', function(){
        onLoadings('body');  
        $.post(url,{id:id}, function(data){
           hideLoadings('body');
           if(data.status == 'success'){
               <?= \appxq\sdii\helpers\SDNoty::show('data.message', 'data.status')?>
               let param = data['data']['url']+'/'+data['data']['path']+'/'+data['data']['file_name'];
               console.log(param);
               location.href = param;
               let uri = '/manageproject/backup-restore/download';
               $.get(uri,{params:data['data']}, function(data){
                   console.log(data);
               });
                
           } 
        });
      });
      return false;  
    }
    function onLoadings(ele){
        $(ele).waitMe({
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
    function hideLoadings(ele){
         $(ele).waitMe("hide");
    }
</script>
<?php \richardfan\widget\JSRegister::end();?>
<?php
$this->registerJsFile(
    '@web/wizard/js/chanpanJs.js',
    ['depends' => [\yii\web\JqueryAsset::className()]]
);
$this->registerCss("
   .table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
        background-color: #f6f3f0;
    } 
");
$this->registerJs("        
    $('#my-project #tables thead tr th a , .pagination li a').click(function(){
        let url = $(this).attr('href');
        //console.log(url);
        loadUrl(url);
        return false;         
    });
    
    function loadUrl(url){            
        $('#my-project').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
        $.get(url, function(data){
           $('#my-project').html(data);
        });
   }
");
?>