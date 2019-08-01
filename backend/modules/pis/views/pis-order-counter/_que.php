<?php
echo $this->render('_search', ['model' => $searchModel, 'reloadDiv' => $reloadDiv]);
?>
<div id="que-list-view">            
  <?php
  echo \yii\widgets\ListView::widget([
      'id' => 'que-list',
      'dataProvider' => $dataProvider,
      'itemOptions' => ['tag' => false],
      'layout' => '<div class="list-group">{items}</div><div class="list-pager">{pager}</div>',
      'itemView' => function ($model, $index)use ($order_status) {
          return $this->render('_item_que', [
                      'model' => $model,
                      'index' => $index,
                      'order_status' => $order_status,
          ]);
      },
  ]);
  ?>
</div>
<?php
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
