<?php // backend\modules\ezforms2\classes\CommunityBuilder::Community()
        //->type('webboard')
        //->object_id('1526460315096965800')->buildCommunity();?>

<?php 
    $this->title = Yii::t('chanpan','Webboard');
?>
 
    <?=
    \appxq\sdii\widgets\GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions'=>['class'=>'table table-bordered table-hover'],
        'rowOptions'=>[],
        'panelBtn'=>'<button data-url="'. \yii\helpers\Url::to(['/ezforms2/ezform-data/ezform?ezf_id=1526460315096965800&modal=modal-ezform-main&reloadDiv=show-webboard&initdata=&target=&targetField=']).'" id="btnPost" class="btn btn-success">'. Yii::t('Chanpan','Posts').'</button>',
        'columns' => [ 
            [
                'format'=>'raw',
                'attribute'=>'title',
                'label'=> Yii::t('chanpan','Post all'),
                'contentOptions'=>['style'=>'width:70%;cursor:pointer;'],
                'value'=>function($model){
                    return "
                        <a data-id='{$model['id']}' href='#'>
                        <div class='media'>
                            <div class='media-left'>
                                <i style='font-size:16pt;color: #ffb83f;' class='fa fa-folder'></i>
                            </div>
                            <div class='media-body'>
                              <h4 class='media-heading'>{$model['title']}</h4>                                  
                            </div>
                        </div>
                        </a>
                    ";
                }
            ],
            [
                'format'=>'raw',
                'attribute'=>'name',
                'label'=> Yii::t('chanpan','By'),
                'value'=>function($model){
                    return "<div style='font-size:8pt;'>
                            <div>{$model['name']}</div>
                            <div ><i class='glyphicon glyphicon-calendar'></i> ".$model['create_date']."</div>    
                    </div>
                    ";
                }
            ],
            [
                'format'=>'raw',
                'attribute'=>'name',
                'label'=> Yii::t('chanpan','Reply/view'),
                'contentOptions'=>['style'=>'text-align:center;'],
                'headerOptions'=>['style'=>'text-align:center;'],
                'value'=>function($model){
                    $count = \backend\modules\ezforms2\models\EzformCommunity::find()->where('object_id=:object_id AND type="webboard"', [':object_id'=>$model['id']])->count();
                    $view = (new \yii\db\Query())->select('view_post')->from('zdata_webboard')->where('id=:id',[':id'=>$model['id']])->one();
                    $view = !empty($view['view_post']) ? $view['view_post'] : 0;
                    
                    $btnUpdate = \yii\bootstrap\Html::button("<i class='fa fa-edit'></i> ".Yii::t('chanpan','Update'), ['style'=>'padding:2px;','data-action'=>'update','class'=>'btn btn-xs btn-info', 'data-url'=>\yii\helpers\Url::to(['/ezforms2/ezform-data/ezform?ezf_id=1526460315096965800&dataid='.$model['id'].'&modal=modal-ezform-main&reloadDiv=show-webboard&initdata=&target=&targetField='])]);
                    $btnDelete = \yii\bootstrap\Html::button("<i class='fa fa-trash'></i> ".Yii::t('chanpan','Delete'), ['data-action'=>'delete','class'=>'btn btn-xs btn-danger','data-url'=>\yii\helpers\Url::to(['/webboard/default/delete', 'id'=>$model['id']])]);
                    if(Yii::$app->user->can('administrator') || $model['id'] == Yii::$app->user->id){
                       return "<div style='font-size:8pt;'>
                            <div><b>{$count}/{$view}</b>&nbsp;&nbsp; {$btnUpdate} {$btnDelete}</div>
                        </div>
                        ";      
                    } 
                    return "<div style='font-size:8pt;'>
                            <div><b>{$count}/{$view}<b></div>
                    </div>
                    ";
                }
            ],
//            [
//                'contentOptions'=>['style'=>'text-align:center;width:150px;'],
//                'class' => 'yii\grid\ActionColumn',
//                'template' => '{update} {delete}',  // the default buttons + your custom button
//                'buttons' => [
//                    'update'=>function($url, $model, $key){
//                        if(Yii::$app->user->can('administrator') || $model['id'] == Yii::$app->user->id){
//                            return \yii\bootstrap\Html::button("<i class='fa fa-edit'></i> ".Yii::t('chanpan','Update'), ['data-action'=>'update','class'=>'btn btn-xs btn-info', 'data-url'=>\yii\helpers\Url::to(['/ezforms2/ezform-data/ezform?ezf_id=1526460315096965800&dataid='.$model['id'].'&modal=modal-ezform-main&reloadDiv=show-webboard&initdata=&target=&targetField='])]);
//                        }                        
//                    },
//                    'delete'=>function($url, $model, $key){
//                        if(Yii::$app->user->can('administrator') || $model['id'] == Yii::$app->user->id){
//                            return \yii\bootstrap\Html::button("<i class='fa fa-trash'></i> ".Yii::t('chanpan','Delete'), ['data-action'=>'delete','class'=>'btn btn-xs btn-danger','data-url'=>\yii\helpers\Url::to(['/webboard/default/delete', 'id'=>$model['id']])]);
//                        }
//                    }        
//                ]
//            ]        
        ],
    ])
    ?> 
 
<?php echo \appxq\sdii\widgets\ModalForm::widget([
                'id' => 'modal-webboard',
                'size' => 'modal-lg',
                'tabindexEnable' => false,
            ]);
?>

<?php

$this->registerJs("
    $('.btn').on('click',function(e){
        let actions = $(this).attr('data-action');
        let url = $(this).attr('data-url');
         
        if(actions == 'update'){
           $('#modal-webboard .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>'); 
           $.get(url,function(data){
                $('#modal-webboard .modal-content').html(data);
                $('#modal-webboard').modal('show');
           });
        }
        if(actions == 'delete'){
          yii.confirm('".Yii::t('chanpan','Do you want to delete?')."', function(){
             $.get(url,function(data){
                ".\appxq\sdii\helpers\SDNoty::show('data.message', 'data.status').";
                 let urls = '/webboard/default/get-webboard';
                 getWebboard(urls);    
             });  
	  });
        }
        return false;
    });
    
    $('#tables thead tr th a , .pagination li a').click(function(){
        let url = $(this).attr('href');
        sortUser(url);
        return false;
         
    });
    
   
");
?>


<?php 
    $this->registerJs("
        $('#btnPost').on('click',function(){
     
           let url = $(this).attr('data-url');
           $('#modal-webboard .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>'); 
           $.get(url,function(data){
                $('#modal-webboard .modal-content').html(data);
                $('#modal-webboard').modal('show');
           });
            return false;
        });
        $('#show-webboard .pagination li a').on('click', function(){
            let url = $(this).attr('href');            
            getWebboard(url);    
            return false;
        });
        $('#show-webboard .table thead tr th a').on('click', function(){
            let url = $(this).attr('href');            
            getWebboard(url);    
            return false;
        });
        
        $('#show-webboard .table tbody tr td a').on('click', function(){
            let id = $(this).attr('data-id');            
            let url = '".\yii\helpers\Url::to(['/webboard/default/view'])."';
            location.href=url+'?id='+id; 
            return false;
        });
        
      
    ");
?>