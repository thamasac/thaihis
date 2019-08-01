<div style="margin-top:20px;"></div>
<?php if(!empty($data)): ?>
<?= $this->render('_item',['data'=>$data, 'status'=>'trash','dataProvider'=>$dataProvider,])?> 
<?php endif; ?>

