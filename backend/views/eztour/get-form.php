<?php 
    use yii\helpers\Html;
    use yii\helpers\Url;
//    $options['datas'] = $options;
?>
<?php foreach($model as $m):?>
<div class="col-md-12" id="panel-<?= $m['id']?>">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <span class="pull-right"> 
                    <?= Html::button('<i class="fa fa-trash"></i> '.Yii::t('tour','DEL'), ['class'=>'btn btn-sm btn-danger btnDel' ,'data-id'=>$m['id']])?>
                </span>
                <h4 class="modal-title" ><?= Yii::t('eztour', $m['title']) ?></h4>
            </div>
            <div class="panel-body">
                <div class="col-md-3">
                    <label><?= Yii::t('tour', 'Element ID') ?></label>
                    <?= Html::textInput('element',$m['element'], ['class' => 'form-control', 'onBlur'=>'updateChange(this)' , 'id'=>$m['id']]) ?>
                </div>
                <div class="col-md-3">
                    <label><?= Yii::t('tour', 'Title') ?></label>
                    <?= Html::textInput('title', $m['title'], ['class' => 'form-control', 'onBlur'=>'updateChange(this)' , 'id'=>$m['id']]) ?>
                </div>
                <div class="col-md-3">
                    <label><?= Yii::t('tour', 'Placement') ?></label>
                    <?php
                    $items = [
                        'auto' => 'Auto', 'top' => 'Top', 'left' => 'Left', 'bottom' => 'Bottom', 'right' => 'Right'
                    ];
                    ?>
                    <?= Html::dropDownList('placement', $m['placement'], $items, ['class' => 'form-control', 'onChange'=>'updateChange(this)' , 'id'=>$m['id']]) ?>
                </div>
                
                <div class="col-md-3">
                    <label><?= Yii::t('tour', 'Placement') ?></label>
                    <?php
                    $items = [
                        true => 'TRUE', false => 'FALSE'
                    ];
                    ?>
                    <?= Html::dropDownList('smartPlacement', $m['smartPlacement'], $items, ['class' => 'form-control', 'onChange'=>'updateChange(this)' , 'id'=>$m['id']]) ?>
                </div>
                 
                <div class="clearfix"></div><hr/>
                <div class="col-md-12">
                    <label><?= Yii::t('tour', 'Content') ?></label>
                    <?php
                    echo \appxq\sdii\widgets\FroalaEditorWidget::widget([
                        'name' => 'content',
                        'value' => $m['content'],
                        'toolbar_size' => 'lg',
                        'options' => ['class' => 'eztemplate', 'id' => appxq\sdii\utils\SDUtility::getMillisecTime(), 'data-id'=>$m['id']],
                    ])
                    ?>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>
<?php richardfan\widget\JSRegister::begin(); ?>
<script>
    
   $('.btnDel').on('click' , function(){
       
        let id = $(this).attr('data-id');
        let url = '/eztour/delete-tour';
        yii.confirm('<?= Yii::t('app', 'Are you sure you want to delete these items?')?>', function() {
            $.post(url, {id:id}  ,function(result){
                if(result.status == 'success') {
                    <?= \appxq\sdii\helpers\SDNoty::show('result.message', 'result.status')?>
                    $('#panel-'+id).remove();
                } else {
                    <?= \appxq\sdii\helpers\SDNoty::show('result.message', 'result.status')?>
                } 
            });
        });
        
        return false;
   });
   $('.eztemplate').on('froalaEditor.contentChanged', function (e, editor) {
        let id = $(this).attr('data-id');
        let name = $(this).attr('name');
        let value = $(this).val();
        let data = {id:id, name:name, value:value}; 
        saveChange(data);
        return false;
        
   }); 
   
    function updateChange(e){
        let id = e.id;
        let value = e.value;
        let name = e.name;
        let data = {id:id, name:name, value:value}; 
        saveChange(data);
        return false;
    }
    
    function saveChange(data){
        let url = '/eztour/update-tour';
        $.post(url, {data:data}  ,function(result){
            if(result.status == 'success') {
                <?= \appxq\sdii\helpers\SDNoty::show('result.message', 'result.status')?>
            } else {
                <?= \appxq\sdii\helpers\SDNoty::show('result.message', 'result.status')?>
            } 
        });
        return false;
    }
</script>
<?php richardfan\widget\JSRegister::end();?>