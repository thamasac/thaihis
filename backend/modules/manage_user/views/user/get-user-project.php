<?php 
    use yii\helpers\Html;
    use yii\helpers\Url;
    $this->title = 'User project';
    $modal_id = 'modal-project';
?>  
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title"><?= Html::encode($this->title)?></h4>
</div>
<div class="modal-body">
    <?= \yii\grid\GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'attribute'=>'project_name',
            'label'=> Yii::t('user', 'Project Name'),
            'value'=>function($model){
                if($model['create_at']){
                    return $model['project_name'];
                }
            }
        ],    
        'url',
        'dbname',
        [
            'attribute'=>'create_at',
            'value'=>function($model){
                if($model['create_at']){
                    return \appxq\sdii\utils\SDdate::mysql2phpDate($model['create_at']);
                }
            }
        ],
            
        
    ],
]) ?>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
</div>



<?php \richardfan\widget\JSRegister::begin();?>
<script>
   $('.modal-body .pagination li a').on('click', function(){
      let url = $(this).attr('href');
      reload_url(url);
      return false;
   });
   $('.table>thead>tr>th>a').on('click', function(){
      let url = $(this).attr('href');
      reload_url(url);
      return false;
   });
   function reload_url(url){
       $.get(url, function(result){
           $('#<?= $modal_id?> .modal-content').html(result);
       });
       //alert(user_id);
       return false;
    }
</script>
<?php \richardfan\widget\JSRegister::end();?>