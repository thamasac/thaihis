<ul class="nav nav-tabs" role="tablist" style="padding-top: 10px;">
	<?php
	$dataMenu = CoreQuery::getPostMenu($type);
	$num = 0;
	$htmlMenu = '';
	$active = '';
	$activeAll = true;
	$sticky = (!in_array($type, Yii::app()->controller->module->hasParentPost))?count(CJSON::decode(CoreQuery::getOptions('sticky_posts')->option_value)):0;
	foreach ($dataMenu as $key => $value) {
		if(isset($status) && $status==$value['post_status']){
			$active =  'active';
			$activeAll = false;
		}
		$htmlMenu .= '<li class="'.$active.'"><a href="'.Yii::app()->createUrl('//core/posts/index', array('type'=>$type, 'status'=>$value['post_status'] )).'">'.CoreFunc::t(ucfirst($value['post_status']))." ({$value['num']})</a></li>";
		$active = '';
		if($value['post_status']!=='trash'){
			$num+=$value['num'];
		}
	}
	if(isset($status) && $status=='sticky'){
		if($sticky>0){
			$active =  'active';
			$activeAll = false;
		}
	}
	echo '<li class="'.($activeAll?'active':'').'"><a href="'.Yii::app()->createUrl('//core/posts/index', array('type'=>$type)).'">'.CoreFunc::t('All').' ('.$num.')</a></li>';
	if($sticky>0){
		echo '<li class="'.$active.'"><a href="'.Yii::app()->createUrl('//core/posts/index', array('type'=>$type, 'status'=>'sticky')).'">'.CoreFunc::t('Sticky').' ('. $sticky .')</a></li>';
		$active = '';
	}
	echo $htmlMenu;
	?>
</ul>