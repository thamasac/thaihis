<?php    
    $imgPath = Yii::getAlias('@storageUrl');
    $noImage = $imgPath.'/ezform/img/no_icon.png';
    \cpn\chanpan\assets\CNDragAssets::register($this);
    $i=2;
?>
<div class="row" id="ezf-box">
    <ul id="sortable">
        <?php echo $this->render('show-grid',['datas'=>$list]);?>
    </ul>
</div>    
<?php    \richardfan\widget\JSRegister::begin();?>
<script>
    
    $('#ezf-box').dad({
            draggable:'.draggable',
            callback:function(e){
                var positionArray = [];
                $('#ezf-box').find('.dads-children').each(function(){
                    positionArray.push($(this).attr('data-id'));
                    //var list_order = $(this).sortable('toArray').toString();
                });
                 
                $.ajax({
                    url: '<?= \yii\helpers\Url::to(['/manage_modules/default/order'])?>',
                    type: 'POST',
                    data: {list_order:positionArray.toString()},
                    success: function(data) {
                        console.log(data);
                        //finished
                    }
                });
            }
    });
    
    $('.btn-edit').on('click', function(){
       let action = $(this).attr('data-action');
       let url = $(this).attr('data-url');
           $('#modal-project').modal('show');
           $('#modal-project .modal-content').html('<div class="sdloader "><i class="sdloader-icon"></i></div>');
           $.get(url, function(data){
                $('#modal-project .modal-content').html(data);                
           });
       return false;
    });
    $('.btn-delete').on('click', function(){
        let id = $(this).attr('data-id');
        let url = $(this).attr('data-url');
        yii.confirm("<?= Yii::t('app', 'Are you sure you want to delete this item?')?>", function(){
            $.post(url,{id:id}, function(res){
                <?= appxq\sdii\helpers\SDNoty::show('res.message', 'res.status')?>
                getManageModules();
            });
        });        
       return false;
    });
    
    
    $('#sortable-1111').sortable({
        //axis: ('x','y'),
        opacity: 0.7,
        handle: 'span',
        revert: true,
        scroll: false,
        placeholder: "sortable-placeholder",
        update: function(event, ui) {
            var list_order = $(this).sortable('toArray').toString();
            
            $.ajax({
                url: '<?= \yii\helpers\Url::to(['/manage_modules/default/order'])?>',
                type: 'POST',
                data: {list_order:list_order},
                success: function(data) {
                    console.log(data);
                    //finished
                }
            });
        }
    }); // fin sortable
</script>
<?php    \richardfan\widget\JSRegister::end();?>

<?php    \appxq\sdii\widgets\CSSRegister::begin();?>
<style>
    #sortable { 
        list-style: none; 
        text-align: left; 
    }
    #sortable li { 
        margin: 0 0 10px 0;        
        height: 100%; 
        color: #333333;
    }
    #sortable li span {
        background-repeat: no-repeat;
        background-position: center;
        width: 100%;
        height: 100%; 
        display: inline-block;
        float: left;
        cursor: move;
    }
    #sortable li span:hover {
         
    }
    
 
</style>
<?php    \appxq\sdii\widgets\CSSRegister::end();?>