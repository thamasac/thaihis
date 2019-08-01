<?php

echo \yii\widgets\ListView::widget([
    'id' => 'que-list',
    'dataProvider' => $dataProviderQue,
    'itemOptions' => ['tag' => false],
    'layout' => '<div class="list-group">{items}</div><div class="list-pager">{pager}</div>',
    'itemView' => function ($model) use($pt_id, $report_status, $que_type, $page) {
        return $this->render('_item_que', [
                    'model' => $model, 'pt_id' => $pt_id, 'report_status' => $report_status, 'que_type' => $que_type,
                    'page' => $page
        ]);
    },
]);

$this->registerJS("
        $('#que-list').on('click', '.pagination li a', function() { //Next 
            var url = $(this).attr('href');
            getUiAjax(url, '$reloadDiv');
            return false;
        });
        
        function getUiAjax(url, divid) {
            $.ajax({
                method: 'POST',
                url: url,
                dataType: 'HTML',
                success: function(result, textStatus) {
                    $('#'+divid).html(result);
                }
            });
        }
        ");
?>
