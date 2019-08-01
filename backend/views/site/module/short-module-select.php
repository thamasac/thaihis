
<?php 
    use appxq\sdii\helpers\SDNoty;
    use appxq\sdii\helpers\SDHtml;
?>
<?= $this->render('_item-horizontal',['model'=>$output, 'edit'=>$editMode]);?> 
<?php
\richardfan\widget\JSRegister::begin();
?>
<script>
    $("#ezf_dad").sortable({
        update:function( event, ui ){
            let dataObj = [];
            $(this).find('.dad').each(function(index){
                dataObj.push($(this).attr('data-id'));
                //dataObj[index] = {id:$(this).attr('data-id'), forder:$(this).attr('data-forder')} 
            });
//            console.log(dataObj);
            saveOrder(dataObj);
        }
    });
    function saveOrder(dataObj){
        let dataStr = dataObj.join();
        let url ='/site/sort-short-module-select';
        $.post(url,{data:dataStr}, function(result){
            if(result.status == 'success') {
                <?= SDNoty::show('result.message', 'result.status')?>
            } else {
                <?= SDNoty::show('result.message', 'result.status')?>
            } 
        });
        return false;
        
    }
    //  
</script>
<?php \richardfan\widget\JSRegister::end(); ?>
 
