<?php
    use yii\grid\GridView;
    $modal = "modal-ezform-main"; 
?>
<div class="table-responsive">
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'tableOptions'=>['class'=>'table table-bordered table-hover table-responsive'],
    'columns' => [
        [
            'class' => 'yii\grid\SerialColumn',
            // you may configure additional properties here
        ],
       [
           'contentOptions'=>['style'=>'width:600px'],
           'format'=>'raw',
           'label'=>Yii::t('chanpan','Module'),
           'attribute'=>'module_name',
           'value'=>function($model){
               return $this->render("_view",['rs'=>$model]);
           }
       ],
       [
           'format'=>'raw',
           'label'=> Yii::t('chanpan','Role / Permission / Remark'),
           'attribute'=>'module_id',
           'value'=>function($model){
                $data =(new yii\db\Query())
                        ->select('*')->from('zdata_permission_module')
                        ->where('module_id=:module_id',[':module_id'=>$model['module_id']])
                        ->andWhere('rstat not in(0,3)')->all();
                if(!empty($data)){
                    return $this->render("_item",['data'=>$data, 'model'=>$model]);
                }else{
                    return $this->render("_item",['data'=>$data, 'model'=>$model]);
//                    foreach($data as $d){                       
                       //return 'Access denied';                       
                }
           }
       ],
                
    ],
]) ?>
</div>

<?php \richardfan\widget\JSRegister::begin();?>
<script>
     $('#show-permission .table thead tr th a').on('click', function(){
        let url = $(this).attr('href');
        getModules(url);
        return false;
     });
     $('#show-permission .pagination li a').on('click', function(){
        let url = $(this).attr('href');
        getModules(url);
        return false;
     });
</script>

<?php richardfan\widget\JSRegister::end()?>