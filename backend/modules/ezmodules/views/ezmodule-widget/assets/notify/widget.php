<?php

$widget = backend\modules\notify\classes\NotifyWidget::ui()->ezf_id(isset($options['ezf_id']) ? $options['ezf_id'] : '')
        ->target($target)
        ->data_column(isset($options['fields']) ? $options['fields'] : '')
        ->reloadDiv('notify-'.\appxq\sdii\utils\SDUtility::getMillisecTime())
        ->pageSize(isset($options['page_size']) ? $options['page_size'] : '')
        ->actionRequire(isset($options['action']) ? $options['action'] : 0)
        ->hideTab(isset($options['hide_tab']) ? $options['hide_tab'] : 0)
        ->module($module)
        ->data_id(Yii::$app->request->get('data_id',''))
        ->notify_id(Yii::$app->request->get('notify_id',''))
        ->tab(Yii::$app->request->get('tab_notify','to_me'));
//if (backend\modules\ezforms2\classes\EzfAuthFuncManage::accessBtnGrid(isset($module) ? $module : '')) {
//    $widget->disabled(true);
//}
echo $widget->buildGrid();
?>


<?php

$this->registerJs("
    
        $('#modal-ezform-main').on('hidden.bs.modal', function () {
            var urlreloadMy =  $('#notify-my').attr('data-url');
            $('#notify-my').html('<center>Loading...</center>');        
            getUiAjax(urlreloadMy, 'notify-my');
            var urlreloadMem =  $('#notify-mem').attr('data-url');
            $('#notify-mem').html('<center>Loading...</center>');        
            getUiAjax(urlreloadMem, 'notify-mem');
        //    window.location.reload();
        });
//        $('.tab-notify').click(function(){
//            $('.radioSreach').prop('checked', false);
//        });
        
        $('.radioSreachMem').click(function(){
            $('.radioSreachMem').not(this).prop('checked', false);
            
            var param = '';
            if($(this).prop('checked')){
                if($(this).val() == 1){
                    param = '&status_view=0';
                }else if($(this).val() == 2){
                    param = '&status_view=1';
                }else if($(this).val() == 3){
                    param = '&complete_date=1';
                }
                
            }
            getUiAjax($('#notify-mem').attr('data-url')+param,'notify-mem');
        });
        
        $('.radioSreachMy').click(function(){
            $('.radioSreachMy').not(this).prop('checked', false);
            var param = '';
            if($(this).prop('checked')){
                 if($(this).val() == 1){
                    param = '&status_view=0';
                }else if($(this).val() == 2){
                    param = '&status_view=1';
                }else if($(this).val() == 3){
                    param = '&complete_date=1';
                }
            }
            getUiAjax($('#notify-my').attr('data-url')+param,'notify-my');
            
            
        });
        
        $('#my').click(function(){
            getUiAjax($('#notify-my').attr('data-url'),'notify-my');
        });
        $('#member').click(function(){
            getUiAjax($('#notify-mem').attr('data-url'),'notify-mem');
        });
        
        function getUiAjax(url, divid) {
            $.ajax({
                method: 'GET',
                url: url,
                dataType: 'HTML',
                success: function(result, textStatus) {
                    $('#'+divid).html(result);
                }
            });
        }
        
    ");
