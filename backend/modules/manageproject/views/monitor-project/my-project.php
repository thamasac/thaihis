<div class="table-responsive">
<?php
    use appxq\sdii\helpers\SDNoty;
    use appxq\sdii\helpers\SDHtml;
    \backend\modules\ezforms2\assets\JLoading::register($this);
    echo \yii\grid\GridView::widget([
       'dataProvider'=>$dataProvider,
       'tableOptions' => ['id'=>'tables','class' => 'table table-hover table-bordered table-responsive'],
       'columns'=>[
           [
               'class' => 'yii\grid\SerialColumn',
               'header' => '#',                
           ],
           
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
                  return \yii\helpers\Html::img($model['projecticon'], ['style'=>'width:50px;height:50px;border-radius:3px;']);  
              }
            ], 
                    
            [
                'format'=>'raw',
                'label'=>'Acronym / URL / Domain',
                'attribute'=>'projectacronym',
                'value'=> 
                function($model){
//                   $html = "
//                        <div>".($model['projectacronym']) ? $model['projectacronym'] : ''."</div>
//                        <div>".($model['projurl']) ? $model['projurl'] : ''."</div>
//                        <div>".($model['projdomain']) ? $model['projdomain'] : ''."</div>    
//                    ";
                   $html = "
                       <a target='_blank' href='https://".$model['projurl'].".".$model['projdomain']."' style='text-decoration: none;'>
                       <label>{$model['projectacronym']}</label> \
                       <label>{$model['projurl']}</label> \
                       <label>{$model['projdomain']}</label>
                       </a>    
                    ";                   
                   return $html;
                }
            ],
            [
                'label'=> Yii::t('project','Database Name'),
                'value'=>function($model){
                    $data_id = isset($model['id']) ? $model['id']: ''; 
                    $myproject = cpn\chanpan\classes\utils\CNProject::getMyProjectById($data_id);
                    $dbname = isset($myproject['data_dynamic']['dbname'])?$myproject['data_dynamic']['dbname']:'';
                    return $dbname;
                }
            ],        
//            [
//                'label'=>'URL',
//                'attribute'=>'projurl',
//                'value'=>'projurl'
//            ],
//            [
//                'label'=>'Domain',
//                'attribute'=>'projdomain',
//                'value'=>'projdomain'
//            ],
//            [
//                'label'=>'Template',
//                'attribute'=>'useTemplate',
//                'value'=>function($model){
//                    return isset($model['useTemplate']) ? $model['useTemplate'] : 'ncrc';
//                }
//            ],             
            [
                'label'=>'PI Name',
                'attribute'=>'pi_name',
                'value'=>'pi_name'
            ],
            [
                'label'=>'Created By',
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
                    return ($model['rstat'] == 3 || $model['rstat'] == 0) ? "<div class='label label-danger' style='display: block;'>Delete</div>" : "<div class='label label-success' style='display: block;'>Active</div>";
                    
                }
            ],
           [
                'headerOptions'=>['style'=>'text-align:center;width:450px;'],
                'class' => 'yii\grid\ActionColumn',
                'template' => '{edit} {update} {restore} {clone} {backup} {delete} {destroy}', 
                'buttons' => [
                    'edit' => function($url, $model, $key) {
                        if($model['rstat'] == 1){
                        return \yii\bootstrap\Html::a("<i class='fa fa-pencil'></i> " . Yii::t('chanpan', 'Edit'),
                                \yii\helpers\Url::to(["/ezforms2/ezform-data/ezform?ezf_id=1523071255006806900&dataid={$model['id']}&modal=modal-1523071255006806900&reloadDiv=modal-divview-1523071255006806900&db2=0"]),
                                ['title'=>'Update Project','data-action' => 'edit', 'class' => 'btn btn-xs btn-info btnProject', 'data-id' => $model['id']]);
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
                                ['title'=>'Clone Project','data-action' => 'clone', 'class' => 'btn btn-xs btn-success btnProject', 'data-id' => $model['id']]);
                        }
                    },            
                    'backup' => function($url, $model, $key) { 
                        if($model['rstat'] == 1){
                        return \yii\bootstrap\Html::a("<i class='fa fa-hdd-o'></i> " . Yii::t('chanpan', 'Backup'),
                                \yii\helpers\Url::to(["/manageproject/monitor-project/backup"]),
                                ['title'=>'Backup Project','data-action' => 'backup', 'class' => 'btn btn-xs btn-default btnProject', 'data-id' => $model['id']]);
                        }
                    },          
                    'delete'=>function($url, $model, $key){
                             if($model['rstat'] == 1){
                                 return \yii\bootstrap\Html::a("<i class='fa fa-trash'></i> ".Yii::t('chanpan','Delete'),
                                        \yii\helpers\Url::to(['/manageproject/setting-project/delete', 'id'=>$model['id']]), 
                                        ['title'=>'Delete Project','data-action'=>'delete','class'=>'btn btn-xs btn-danger btnProject', 'data-id'=>$model['id']]);
                             }
                    },
                    'destroy'=>function($url, $model, $key){
                             if($model['rstat'] == 3){
                                 return \yii\bootstrap\Html::a("<i class='fa fa-trash'></i> ".Yii::t('chanpan','Permanently delete'),
                                        \yii\helpers\Url::to(['/manageproject/setting-project/destroy', 'id'=>$model['id']]), 
                                        ['title'=>'Delete Project','data-action'=>'destroy','class'=>'btn btn-xs btn-danger btnProject', 'data-id'=>$model['id']]);
                             }
                    },        
                            
                ]
            ],          
       ]
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
           console.log('backup');
           return false;
       }
       else if(action == 'delete'){
           Delete(id, url);
       }else if(action == 'destroy'){
           PDelete(id, url);
       }
       return false;
    });
    $('#my-project #tables tbody tr').dblclick(function(){
        let id = $(this).attr('data-key');
        let url = "/ezforms2/ezform-data/ezform?ezf_id=1523071255006806900&dataid="+id+"&modal=modal-1523071255006806900&reloadDiv=modal-divview-1523071255006806900&;db2=0";
        Edit(id, url);
        return false;         
    });
    function Clone(id, url){
        yii.confirm('Confirm Clone', function(){
                let ele = 'body';
                $('.btnProject').attr('disabled', true);
                onLoadings(ele);
                $.post(url, {id:id}, function(data){
//                   console.log(data);return false;
                   if(data.status == 'success'){
                     let url2 = "/manageproject/setting-project/repair?id="+data['data']['id'];  
                     getUpdate(data['data']['id'], url2); 
                   }                   
                    
                   
                });
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
                 //$(project_acronym_str).replaceWith('<div style=\'padding: 7px; background: #e7e7ec;\'><b>'+project_acronym_str.val()+'</b></div>');
                 //$(project_url).replaceWith('<div style=\'padding: 7px; background: #e7e7ec;\'><b>'+project_url.val()+'</b></div>');
                 var status_form = $('#ez1523071255006806900-status_form');
                 status_form.val('update');
                
            });
          
        return false;
    }
    function Update(id, url){
        yii.confirm('Confirm Update', function(){
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
            //console.log(data);
            <?= SDNoty::show('data.message', 'data.status') ?>
            hideLoadings(ele);
            $('.btnProject').attr('disabled', false);
            let url = '/manageproject/monitor-project/my-project?search='+$('#txtSearch').val();
            loadUrl(url);
            $('.btnProject').attr('disabled', false);
        }).fail(function(){
            hideLoadings(ele);
        });
    }
    function Delete(id, url){
        yii.confirm('Confirm Delete', function(){
                $.post(url,{id:id}, function(data){
//                    console.log(data);
                    if(data.status=='success'){
                       <?= \appxq\sdii\helpers\SDNoty::show('data.message', 'data.status')?>                  
                    }else{
                        <?= \appxq\sdii\helpers\SDNoty::show('data.message', 'data.status')?>
                    }
                    let url = '/manageproject/monitor-project/my-project';
                    loadUrl(url);
                });
        });
        return false;
    }
    function PDelete(id, url){
        yii.confirm('Permanently Delete', function(){
                $.post(url,{id:id}, function(data){
                    console.log(data);
                    if(data.status=='success'){
                       <?= \appxq\sdii\helpers\SDNoty::show('data.message', 'data.status')?>                  
                    }else{
                        <?= \appxq\sdii\helpers\SDNoty::show('data.message', 'data.status')?>
                    }
                    let url = '/manageproject/monitor-project/my-project';
                    loadUrl(url);
                });
        });
        return false;
    }
    function Restore(id, url){
        yii.confirm('Confirm Restore', function(){
                
                $.post(url,{id:id}, function(data){
//                    console.log(data);
                    if(data.status=='success'){
                       <?= \appxq\sdii\helpers\SDNoty::show('data.message', 'data.status')?>                  
                    }else{
                        <?= \appxq\sdii\helpers\SDNoty::show('data.message', 'data.status')?>
                    }
                    let url = '/manageproject/monitor-project/my-project';
                    loadUrl(url);
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
        console.log(url);
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