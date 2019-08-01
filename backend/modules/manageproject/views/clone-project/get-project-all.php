<?php if(!empty($data)): ?>
<?php echo $this->render('_item',['data'=>$data, 'status'=>'all','dataProvider'=>$dataProvider, 'element_id'=>'showProjectAll'])?> 
<?php endif; ?>