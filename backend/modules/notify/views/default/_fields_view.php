<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="itemModalLabel"><?= Yii::t('ezform', 'Variable')?></h4>
</div>
<div class="modal-body">
<?php if($model): ?>
<?php if(!empty($model)): ?>
<div id="fieldsSelect" class="list-group">
<?php foreach ($model as $key => $value): ?>
	<?php
	$labelTag = '';
	if(strlen($value['ezf_field_label'])==0){
		$labelTag = $value['ezf_field_name'];
	} else {
		$labelTag = $value['ezf_field_label'];
	}
	
	?>
    <a id="list-<?= $value['ezf_field_id'] ?>" data-id="<?= $value['ezf_field_id'] ?>" data-var="<?= $value['ezf_field_name'] ?>" class="list-group-item"><?= $labelTag ?></a>
<?php endforeach; ?>
</div>
<?php endif; ?>
<?php endif; ?>
</div>

<?php
$this->registerJs("
    var fieldId = '".$_GET['field']."';
    var showId = ".$_GET['show'].";
    var labelclass = 'danger';
    var boxId = 'hidnContent';
    var property = 'cond_jump';
    var propertyLabel = 'label_jump';
    var propertyVar = 'var_jump';
    
    if(showId==1){
	labelclass = 'success';
	boxId = 'showContent';
	property = 'cond_require';
	propertyLabel = 'label_require';
	propertyVar = 'var_require';
    }
    
    var condItem = condArr[fieldId][property];
    
    if(typeof condItem != 'undefined'){
	if(condItem!=''){
	    if(condItem!==null){
		var condItemArr = condItem;
		var index = 0;
		for(index; index<condItemArr.length; ++index){
		    $('#list-'+condItemArr[index]).addClass('active');
		}
	    }
	}
    }
    
    $('#fieldsSelect .list-group-item').click(function(){
	    var old = this;
		if($(this).hasClass('active')){
			$(this).removeClass('active');
			$('#'+boxId+'-'+fieldId).find('#field-'+$(this).attr('data-id')+'-'+fieldId).remove();
			
			if($('#condition_auto').prop('checked')){
			    jQuery.each( $('#'+boxId+'-'+fieldId).parent().parent().parent().parent().find('.tab-pane'), function( i, val ) {

				var strID = $(val).attr('id');
				var arrID = strID.split('-');
				var newID = arrID[1];
				if(newID == 'ezformfields'){
				    newID = 'ezformfields-'+arrID[2];
				}

				if(newID!=fieldId){
				    if(showId==1) {
					$(val).find('#hidnContent-'+newID).find('#field-'+$(old).attr('data-id')+'-'+newID).remove(); 

					condArr[newID]['cond_jump'] = updateCondition('#hidnContent-'+newID);
					condArr[newID]['label_jump'] = updateConditionLabel('#hidnContent-'+newID);
					condArr[newID]['var_jump'] = updateConditionVar('#hidnContent-'+newID);
				    } else {
					$(val).find('#showContent-'+newID).find('#field-'+$(old).attr('data-id')+'-'+newID).remove();

					condArr[newID]['cond_require'] = updateCondition('#showContent-'+newID);
					condArr[newID]['label_require'] = updateConditionLabel('#showContent-'+newID);
					condArr[newID]['var_require'] = updateConditionVar('#showContent-'+newID);
				    }
				}
			    });
			} 
			
			condArr[fieldId][property] = updateCondition('#'+boxId+'-'+fieldId);
			condArr[fieldId][propertyLabel] = updateConditionLabel('#'+boxId+'-'+fieldId);
			condArr[fieldId][propertyVar] = updateConditionVar('#'+boxId+'-'+fieldId);

			createConditionArray(condArr);
		} else {
			$(this).addClass('active');
			var labelTag = $(this).html();
			if(labelTag.length>50){
				labelTag = labelTag.substr(0, 50)+'...';
			} else if(labelTag.length == 0) {
				labelTag = $(this).attr('data-var');
			}
			$('#'+boxId+'-'+fieldId).append('<span id=\"field-'+$(this).attr('data-id')+'-'+fieldId+'\" data-id=\"'+$(this).attr('data-id')+'\" data-var=\"'+$(this).attr('data-var')+'\" class=\"field-item label label-'+labelclass+'\">'+labelTag+' <a class=\"close btn-del\" >&times;</a></span> ');
			
			if($('#condition_auto').prop('checked')){
			    jQuery.each( $('#'+boxId+'-'+fieldId).parent().parent().parent().parent().find('.tab-pane'), function( i, val ) {

				var strID = $(val).attr('id');
				var arrID = strID.split('-');
				var newID = arrID[1];
				if(newID == 'ezformfields'){
				    newID = 'ezformfields-'+arrID[2];
				}

				if(newID!=fieldId){
				    if(showId==1) {
					$(val).find('#hidnContent-'+newID).append('<span id=\"field-'+$(old).attr('data-id')+'-'+newID+'\" data-id=\"'+$(old).attr('data-id')+'\" data-var=\"'+$(old).attr('data-var')+'\" class=\"field-item label label-danger\">'+labelTag+' <a class=\"close btn-del\" >&times;</a></span> ');

					condArr[newID]['cond_jump'] = updateCondition('#hidnContent-'+newID);
					condArr[newID]['label_jump'] = updateConditionLabel('#hidnContent-'+newID);
					condArr[newID]['var_jump'] = updateConditionVar('#hidnContent-'+newID);

				    } else {
					$(val).find('#showContent-'+newID).append('<span id=\"field-'+$(old).attr('data-id')+'-'+newID+'\" data-id=\"'+$(old).attr('data-id')+'\" data-var=\"'+$(old).attr('data-var')+'\" class=\"field-item label label-success\">'+labelTag+' <a class=\"close btn-del\" >&times;</a></span> ');

					condArr[newID]['cond_require'] = updateCondition('#showContent-'+newID);
					condArr[newID]['label_require'] = updateConditionLabel('#showContent-'+newID);
					condArr[newID]['var_require'] = updateConditionVar('#showContent-'+newID);
				    }

				}
			    });
			}
			
			condArr[fieldId][property] = updateCondition('#'+boxId+'-'+fieldId);
			condArr[fieldId][propertyLabel] = updateConditionLabel('#'+boxId+'-'+fieldId);
			condArr[fieldId][propertyVar] = updateConditionVar('#'+boxId+'-'+fieldId);
			
			createConditionArray(condArr);
		}
    });

");
?>