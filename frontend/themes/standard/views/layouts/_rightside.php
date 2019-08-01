<section id="right-side" class="right-sidebar" role="complementary" >
    <div id="right-side-scroll" class="row">
	<div class="col-lg-12">
	    <?php if (!empty($this->params['menu'])): ?>
		<div class=" sidebar-nav-title" >เมนูดำเนินการ</div>
		<div class=" sidebar-nav" >
		    <?= common\lib\sdii\widgets\SDMenu::widget([
			'options'=>['class'=>'nav nav-list'],
			'items' => $this->params['menu'],
		    ]) ?>
		</div>
	    <?php endif ?>

	    <?php if (!empty($this->params['widgets'])): ?>
		<?php echo $this->params['widgets']; ?>
	    <?php endif ?>	
	</div>
    </div>
</section>
