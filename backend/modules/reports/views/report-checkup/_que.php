<?php
$origin_url = isset($_SERVER['HTTP_ORIGIN'])?$_SERVER['HTTP_ORIGIN']:$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'];
$main_url = $origin_url."/ezmodules/ezmodule/view?id=".$module_id;


echo \yii\widgets\ListView::widget([
    'id' => 'que-list',
    'dataProvider' => $dataProviderQue,
    'itemOptions' => ['tag' => false],
    'layout' => '<div class="list-group">{items}</div><div class="list-pager">{pager}</div>',
    'itemView' => function ($model) use($target, $report_status, $que_type, $page, $main_url) {
        return $this->render('_item_que', [
                    'model' => $model, 'target' => $target, 'report_status' => $report_status, 'que_type' => $que_type,
                    'main_url'=>$main_url,
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
