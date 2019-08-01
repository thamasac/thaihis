<?php
echo \yii\bootstrap\Tabs::widget([
    'id' => 'eztabs-'.$ezf_id,
    'items' => $items,
]);

?>
<div class="clearfix"></div>
<br/>

<div class="clearfix"></div>

<div class="modal modal-content-grid" role="dialog">
    <div class="modal-dialog" style="width:80%;" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Document All Version</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<?php

//\appxq\sdii\utils\VarDumper::dump($field_taget);
richardfan\widget\JSRegister::begin(['position'=> yii\web\View::POS_READY]);
?>
<script>

    
    function getGantchart(){
        
    }

</script>

<?phprichardfan\widget\JSRegister::end();



