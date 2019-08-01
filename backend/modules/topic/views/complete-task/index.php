<?php 
    \cpn\chanpan\card\assets\CardAssets::register($this);
?>
<?php 
    $modalId = 'modal-mark';
    echo yii\bootstrap\Modal::widget([
        'id'=>$modalId,
        'size'=>'modal-xxl',
        'options'=>['tabindex' => false]
   ]);
?>
<div id="task-all">
    <div class="pull-right">
        <button class="btn btn-success btn-task-create" data-widget-id="<?= $widget_id?>"><i class="fa fa-plus"></i></button>
    </div>
    <div class="pull-left">
        <h3>Task</h3>
    </div>
    <div class="clearfix"></div><br/>
    <?= $this->render('search', ['widget_id'=>$widget_id])?>
    <div class="clearfix"></div><br/>
    
    <?=
        \yii\widgets\ListView::widget([
            'dataProvider' => $dataProvider,
            'options' => [
                'tag' => 'ul',
                'class' => 'list-none',
                'id' => 'ezf_dad',
            ],
            'itemOptions' => function($model) {
                return [
                    'tag' => 'li',
                    'id' => '',
                    'data-id' => '',
                    ];
            },
            // 'layout' => "{summary}\n{items}\n{pager}",
            'layout' => "{summary}\n{items}\n<div class='pagers text-center task-pager'>{pager}</div>",
            'itemView' => function ($model, $key, $index, $widget) {
                return $this->render('_items', ['model' => $model, 'widget'=>$widget]);
            },
        ]);
    ?>
</div>




<?php richardfan\widget\JSRegister::begin();?>
<script>
    $('.btn-task-propover').on('click' , function(){
       let url = $(this).attr('data-url');
       $(this).popover({content: $(this).attr('data-content'), html:true, container:'body'}).popover('toggle');
       return false;
    });
    $('body').on('click', function (e) {
        if ($(e.target).data('toggle') !== 'popover'
            && $(e.target).parents('.popover.in').length === 0) { 
            $('[data-toggle="popover"]').popover('hide');
        }
    });
    
    
    $("#task-all #ezf_dad").sortable({
        update:function( event, ui ){
            let dataObj = [];
            $(this).find('.dad').each(function(index){
                dataObj.push($(this).attr('data-id'));
                //dataObj[index] = {id:$(this).attr('data-id'), forder:$(this).attr('data-forder')} 
            });
            //console.log(dataObj);
           saveOrder(dataObj);
        }
    });
    function saveOrder(dataObj){
        let dataStr = dataObj.join();
        let url ='/topic/complete-task/save-forder';
        $.post(url,{data:dataStr}, function(result){
            if(result.status == 'success') {
                <?= appxq\sdii\helpers\SDNoty::show('result.message', 'result.status')?>
            } else {
                <?= appxq\sdii\helpers\SDNoty::show('result.message', 'result.status')?>
            } 
        });
        return false;
        
    }
    $('.btn-task-create').on('click' , function(){
         let url ='/topic/complete-task/create';
         let id = $(this).attr('data-widget-id');
         $.get(url, {id:id}, function(result){
            if(result.status == 'success') {
                <?= appxq\sdii\helpers\SDNoty::show('result.message', 'result.status')?>
                  reloadTask(id);
            } else {
                <?= appxq\sdii\helpers\SDNoty::show('result.message', 'result.status')?>
            } 
         });
        return false;
    });
    //btn-task-done
    $('.btn-task-done').on('click' , function(){
        let id = $(this).attr('data-id');
        let status = $(this).attr('data-status');
        
        yii.confirm('<?= Yii::t('topic', 'Are you sure ?')?>', function() {
             let url ='/topic/complete-task/done';
             
             $.post(url, {id:id, status:status}, function(result){
                 if(result.status == 'success') {
                    <?= appxq\sdii\helpers\SDNoty::show('result.message', 'result.status')?>
                    setTimeout(function(){
                        reloadTask('<?= $widget_id?>');
                    },500);
                } else {
                    <?= appxq\sdii\helpers\SDNoty::show('result.message', 'result.status')?>
                } 
             });
        });
        return false;
    });
    $('.btn-task-delete').on('click' , function(){
        let id = $(this).attr('data-id');
        yii.confirm('<?= Yii::t('topic', 'Are you sure you want to delete these items?')?>', function() {
             let url ='/topic/complete-task/delete';
             $.post(url, {id:id}, function(result){
                 if(result.status == 'success') {
                    <?= appxq\sdii\helpers\SDNoty::show('result.message', 'result.status')?>
                    setTimeout(function(){
                        $('#card-task-'+id).fadeOut(300, function(){ $(this).remove();});
                    },500);
                } else {
                    <?= appxq\sdii\helpers\SDNoty::show('result.message', 'result.status')?>
                } 
             });
        });
        return false;
    });
    $('.task-pager .pagination li a').on('click', function(){
        let url = $(this).attr('href');
        //alert(url);
        $.get(url, function(data){
            $('#<?= $widget_id?>').html(data);
        });
        //
        return false;
    });
    function reloadTask(widget_id){
        let url = '/topic/complete-task';
        $.get(url,{id:widget_id}, function(data){
            $('#'+widget_id).html(data);
        });
        
    }
</script>
<?php richardfan\widget\JSRegister::end();?>

<?php appxq\sdii\widgets\CSSRegister::begin();?>
<style>
    .card {
        border-radius: 6px;
        box-shadow: 0 0px 5px rgba(204, 197, 185, 0.5);
        background-color: #FFFFFF;
        color: #252422;
        margin-bottom: 20px;
        position: relative;
        z-index: 1;
    }
    .card .content {
        padding: 15px 15px 10px 15px;
    }
    .card .header {
        padding: 0px 35px 0px !important;
    }
    .list-none {
        list-style: none;
        padding-left: 15px;
    }
    .card ul.team-members li:not(:last-child) {
        border-bottom: 1px solid #F1EAE0;
    }
    .card ul.team-members li {
        padding: 10px 0px;
    }
    .content-image-check img{
        width:80px;
        margin: 0 auto;
    }
    .check-complete{
        font-size:40pt;
        color:#0e9d72;
    }
    .check-warning{
        font-size:40pt;
        color:orange;
    }
    .popover {
        position: absolute;
        top: 0;
        left: 0;
        z-index: 1010;
        display: none;
        max-width: 600px;
        padding: 1px;
        text-align: left;
        white-space: normal;
        background-color: #ffffff;
        border: 1px solid #ccc;
        border: 1px solid rgba(0, 0, 0, 0.2);
        -webkit-border-radius: 6px;
           -moz-border-radius: 6px;
                border-radius: 6px;
        -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
           -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
                box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        -webkit-background-clip: padding-box;
           -moz-background-clip: padding;
                background-clip: padding-box;
      }
    .color-gray{color:#b7b7b7 !important;}
</style>
<?php appxq\sdii\widgets\CSSRegister::end();?>