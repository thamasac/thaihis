<?php 
    use yii\helpers\Html;
    use yii\helpers\Url;
    $this->title = 'Monitor User';
    
    $this->params['breadcrumbs'][] = ['label' => Yii::t('user', 'User'), 'url' => ['/ezmodules/ezmodule/view?id=1520798323068323400&tab=1524480265054326200&addon=0']];
    $this->params['breadcrumbs'][] = $this->title;

?> 
<?php 
    $modal_id = 'modal-project';
    echo yii\bootstrap\Modal::widget([
        'id'=>$modal_id,
        'size'=>'modal-xxl',
        'options'=>['tabindex' => false]
   ]);
?>
<div class="panel panel-default">
    <div class="panel-heading">
        
        <i class="fa fa-desktop"></i> <?= Html::encode($this->title)?>
    </div>
    <div class="panel-body">
        <div class="form-search" style="background: #fff;
             
    padding: 10px;
    box-shadow: 0px 0px 0px 1px #d2d2d2;
    margin-bottom: 5px;
    border-radius: 5px;margin-bottom:25px;">
            <div class="row">
                <div class="col-md-5">
                    <div class="alert alert-warning">
                        <div class="row">
                            <div class="col-md-4 col-xs-4">
                                <span><i class="fa fa-user"></i> <?= Yii::t('user', 'Total User')?>: <b><u><?= cpn\chanpan\classes\CNUser::getCountUser();?></u></b> <?= Yii::t('user','Items')?></span>
                            </div>
                             <div class="col-md-4 col-xs-4">
                                 <span><i class="fa fa-folder-open"></i> <?= Yii::t('user', 'Total Project')?>: <b><u><?= \cpn\chanpan\classes\utils\CNProject::getCountProject(); ?></u></b> <?= Yii::t('user','Items')?></span>
                            </div>
                            <div class="clearfix" ></div>
                             <div class="col-md-6 col-xs-12" style="margin-top:15px;">
                                 <span><i class="fa fa-trash"></i> <?= Yii::t('user', 'Total Project Delete')?>: <b><u><?= \cpn\chanpan\classes\utils\CNProject::getCountProjectDelete(); ?></u></b> <?= Yii::t('user','Items')?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-7">
                    <form method="get" id="form-search" action="<?= Url::to(['/manage_user/user/monitor-users'])?>">
                        <div class="input-group">
                            <input style="font-size: 16pt;
                                   height: 47px;" name="textsearch" id="textsearch" type="text" class="form-control" placeholder="<?= Yii::t('user','Search')?>">
                            <span class="input-group-btn">
                                <button type="submit" class="btn btn-primary btn-lg"><i class="fa fa-search" aria-hidden="true"></i> <?= Yii::t('user','Search')?></button>
                            </span>
                        </div>
                    </form>
                </div>
                
            </div>
            
             
        </div>
        <?= \yii\grid\GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                [
                    'format'=>'raw',
                    'attribute'=> 'name',
                    'value'=>function($model){
                        $name = isset($model['name'])?$model['name']:'';
                        $user_id = isset($model['user_id'])?$model['user_id']:'';
                        return "<a href='#' data-user-id='{$user_id}' class='btn-show-project'>{$name}</a>";
                    }
                ],
                'sitecode',
                [
                    'attribute'=>'count_project',
                    'contentOptions'=>['style'=>'width:80px;text-align:center;'],
                    'value'=>'count_project'
                ],
                [
                    'headerOptions'=>['style'=>'width:100px;'],
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{view}',
                    'buttons' => [
                        'view' => function($url, $model, $key) {     // render your custom button
                            $user_id = isset($model['user_id'])?$model['user_id']:'';
                            return yii\helpers\Html::a("<i class='fa fa-eye'></i> View", $url, ['class'=>'btn btn-info btn-block btn-show-project', 'data-user-id'=>$user_id]);
                        }
                    ]
                ]         

            ],
            'layout'=> "{summary}\n{items}\n<div class='row' style='background:#ebebeb;text-align:center;font-size: 16pt;padding: 15px 0 5px 0;'>{pager}</div>"
                
        ]) ?>
    </div>
</div>
<?php \richardfan\widget\JSRegister::begin();?>
<script>
     
    $( "#textsearch" ).autocomplete({
        source: function (request, response) {
            $.ajax({
                url: '<?= \yii\helpers\Url::to(['/manage_user/user/get-keyword-search-auto-complete']) ?>',
                dataType: 'json',
                data: {
                    term: request.term
                },
                success: function (data) {
                    response(data);
                }
            });
        },
        minLength: 2,
        select: function (event, ui) {
             $('#form-search').trigger('submit');
            //let result_search = $('#text-search-term').val();
            //console.log(result_search);
            //window.open(url, '_parent');
        }
    });
    
    
    
    $('.btn-show-project').on('click', function(){
       let user_id = $(this).attr('data-user-id');
       let url = '<?= Url::to(['/manage_user/user/get-user-project'])?>?user_id='+user_id;
       get_user_project(url);
       return false;
       
    });
    
    function get_user_project(url){
        $.get(url, function(result){
           $('#<?= $modal_id?>').modal('show');
            $('#<?= $modal_id?> .modal-content').html(result);
       });
       //alert(user_id);
       return false;
    }
</script>
<?php \richardfan\widget\JSRegister::end();?>