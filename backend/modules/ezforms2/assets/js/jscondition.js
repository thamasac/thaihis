$(document).ready(function () {

    $('body').on('change', '#ezformfields-ezf_field_name', function () {
	if($('#items-editor').hasClass('condition-editor')){
	    renderTap('#items-editor.condition-editor .item');
	}
    });

});
//Choose from list
var condArr = new Object;
var newLoad = true;

function renderTap(findTxt, auto=1) {
    
    $('#conditionBox').html('<label><input type="checkbox" id="condition_auto" value="1" '+(auto?'checked':'')+'> Auto adding variable to Hide when specified Show or vice versa</label><ul id="conditionTabs" class="nav nav-tabs" role="tablist"></ul><div id="conditionTabContent" class="tab-content"></div>');
    
    var items = $('#box-data').find(findTxt);
    var ac = 'active';
    var iac = 'in active';
    condArr = new Object;
    
    $.each(items, function (index, value) {
	
	var item = $(value).find('.conditions-value');
	var item_lable = $(value).find('.conditions-label');
	
	var uiTemp = '<div class="row">'+
			'<div class="col-sm-6">'+
			    '<h4 class="page-header">'+ 
				'<div class="row" >'+
				    '<div class="col-sm-12">Hide <button id="btn-hidn-' + $(item).attr('id') + '" type="button" class="btn btn-primary btn-xs"><i class="glyphicon glyphicon-pencil"></i></button></div>'+
				'</div>'+
			    '</h4>'+
			    '<div id="hidnContent-' + $(item).attr('id') + '" ></div>'+
			'</div>'+
			'<div class="col-sm-6">'+
			    '<h4 class="page-header">'+
				'<div class="row" >'+
				    '<div class="col-sm-12">Show <button id="btn-show-' + $(item).attr('id') + '" type="button" class="btn btn-primary btn-xs"><i class="glyphicon glyphicon-pencil"></i></button></div>'+
				'</div>'+
			    '</h4>'+
			    '<div id="showContent-' + $(item).attr('id') + '" ></div>'+
			'</div>'+
		    '</div>';
	
	//START RENDER HTML TAP
	$('#conditionTabs').append('<li class="'+ac+'"><a href="#cond-' + $(item).attr('id') + '" id="cond-tab-' + $(item).attr('id') + '" data-toggle="tab">' + $(item_lable).val() + '</a></li>');
	$('#conditionTabContent').append('<div class="tab-pane fade '+iac+'" id="cond-' + $(item).attr('id') + '" >'+uiTemp+'</div>');
	
	var ezf_field_name = $(item).val();
	var ezf_field_value = '1';
	
	if($(value).attr('data-type')=='radio'){
	    ezf_field_name = $('#ezformfields-ezf_field_name').val();
	    ezf_field_value = $(item).val();
	}
	
	//START DATA CONDITION
	condArr[$(item).attr('id')] = {ezf_id:eid, ezf_version:eversion, ezf_field_name:ezf_field_name, ezf_field_value:ezf_field_value, cond_jump:'', cond_require:'', label_jump:'', label_require:'', var_jump:'', var_require:''};
	
	//START RENDER CONDITION ITEMS
	if(newLoad){//edit field DB loading.
	    //console.log(conditionUrl);
	    $.ajax({
		type   : 'POST',
		url    : conditionUrl,
		data   : {ezf_id:eid, ezf_version:eversion, ezf_field_name:ezf_field_name, ezf_field_value:ezf_field_value},
		dataType: 'JSON',
		success: function (response) {
		    if(response.status == 'success'){
			
			condArr[$(item).attr('id')] = response.data;
			
			createConditionArray(condArr);
			
			
			//console.log(condArr);
			//add value cond_jump and cond_require
			//add items in hidnContent and showContent
			createConditionItems($(item).attr('id'));
		    } else {
			//var noty_id = noty({text:response.message, type:response.status});
		    }
		    //console.log(condArr);
		    //
		},
		error  : function (e) {
		    console.log(e);
		}
	    });
	   
	    
	} else {
	    createConditionItems($(item).attr('id'));
	}
	
	
	//START EVENT
	$('#'+$(item).attr('id')).change(function () {
	    if($(value).attr('data-type')=='radio'){
		condArr[$(item).attr('id')].ezf_field_name = $('#ezformfields-ezf_field_name').val();
		condArr[$(item).attr('id')].ezf_field_value = $(item).val();
	    } else {
		condArr[$(item).attr('id')].ezf_field_name = $(item).val();
		condArr[$(item).attr('id')].ezf_field_value = 1;
	    }
	    
	    createConditionArray(condArr);
	});
	
	$('#'+$(item_lable).attr('id')).change(function () {
	    $('#cond-tab-'+$(item).attr('id')).html($(item_lable).val());
	});
	
	$('#btn-hidn-'+$(item).attr('id')).click(function () {
	    modalCondition(fieldsUrl+'&field='+$(item).attr('id')+'&var='+ezf_field_name+'&show=0');
	});
	
	$('#modal-condition').on('hidden.bs.modal', function(e){
	    $('body').addClass('modal-open');
	});
	
	$('#btn-show-'+$(item).attr('id')).click(function () {
	    modalCondition(fieldsUrl+'&field='+$(item).attr('id')+'&var='+ezf_field_name+'&show=1');
	});
	
	$('#hidnContent-'+ $(item).attr('id')).on('click', '.btn-del', function () {
	    var actionDel = $(this).parent();
	    actionDel.remove();
	    
	    if($('#condition_auto').prop('checked')){
		jQuery.each($('#hidnContent-'+ $(item).attr('id')).parent().parent().parent().parent().find('.tab-pane'), function( i, val ) {
		    var strID = $(val).attr('id');
		    var arrID = strID.split('-');
		    var newID = arrID[1];
		    if(newID!=$(item).attr('id')){
			$(val).find('#showContent-'+newID).find('#field-'+actionDel.attr('data-id')+'-'+newID).remove();

			condArr[newID].cond_require = updateCondition('#showContent-'+newID);
			condArr[newID].label_require = updateConditionLabel('#showContent-'+newID);
			condArr[newID].var_require = updateConditionVar('#showContent-'+newID);
		    }
		});
	    }
	    
	    condArr[$(item).attr('id')].cond_jump = updateCondition('#hidnContent-'+ $(item).attr('id'));
	    condArr[$(item).attr('id')].label_jump = updateConditionLabel('#hidnContent-'+ $(item).attr('id'));
	    condArr[$(item).attr('id')].var_jump = updateConditionVar('#hidnContent-'+ $(item).attr('id'));
	    createConditionArray(condArr);
	    
	});
	
	$('#showContent-'+ $(item).attr('id')).on('click', '.btn-del', function () {
	    var actionDel = $(this).parent();
	    actionDel.remove();
	    
	    if($('#condition_auto').prop('checked')){
		jQuery.each($('#showContent-'+ $(item).attr('id')).parent().parent().parent().parent().find('.tab-pane'), function( i, val ) {
		    var strID = $(val).attr('id');
		    var arrID = strID.split('-');
		    var newID = arrID[1];
		    if(newID!=$(item).attr('id')){
			$(val).find('#hidnContent-'+newID).find('#field-'+actionDel.attr('data-id')+'-'+newID).remove();

			condArr[newID].cond_jump = updateCondition('#hidnContent-'+newID);
			condArr[newID].label_jump = updateConditionLabel('#hidnContent-'+newID);
			condArr[newID].var_jump = updateConditionVar('#hidnContent-'+newID);
		    }
		});
	    }
	    
	    condArr[$(item).attr('id')].cond_require = updateCondition('#showContent-'+ $(item).attr('id'));
	    condArr[$(item).attr('id')].label_require = updateConditionLabel('#showContent-'+ $(item).attr('id'));
	    condArr[$(item).attr('id')].var_require = updateConditionVar('#showContent-'+ $(item).attr('id'));
	    createConditionArray(condArr);
	    
	});

	ac = '';
	iac = '';
    });
    //console.log(condArr);
    createConditionArray(condArr);
    newLoad = false;
}

function createConditionItems(id) {
    var objCond = JSON.parse($('#conditionFields').val());
	    
    var condJump = '';
    var condRequire = '';
    var labelJump = '';
    var labelRequire = '';
    var varJump = '';
    var varRequire = '';
    
    if (typeof objCond[id] != 'undefined') {
	condJump = objCond[id]['cond_jump'];
	condRequire = objCond[id]['cond_require'];
	labelJump = objCond[id]['label_jump'];
	labelRequire = objCond[id]['label_require'];
	varJump = objCond[id]['var_jump'];
	varRequire = objCond[id]['var_require'];
    }

    condArr[id].cond_jump = condJump;
    condArr[id].cond_require = condRequire;
    condArr[id].label_jump = labelJump;
    condArr[id].label_require = labelRequire;
    condArr[id].var_jump = varJump;
    condArr[id].var_require = varRequire;
    
    
    
    if(condJump != ''){
	if(condJump !== null){
	    var condItemArr = condJump;
	    var labelItemArr = labelJump;
	    var varItemArr = varJump;
	    var index = 0;
	    for(index; index<condItemArr.length; ++index){
		var labelTag = '';
		if(labelItemArr[index].length>50){
		    labelTag = labelItemArr[index].substr(0, 50)+'...';
		} else if(labelItemArr[index].length == 0) {
		    labelTag = varItemArr[index];
		} else {
		    labelTag = labelItemArr[index];
		}
		$('#hidnContent-'+ id).append('<span id="field-'+condItemArr[index]+'-'+id+'" data-id="'+condItemArr[index]+'" data-var="'+varItemArr[index]+'" class="field-item label label-danger">'+labelTag+' <a class="close btn-del" >&times;</a></span> ');

	    }
	}
	
    }
    //console.log(condRequire);
    if(condRequire != ''){
	if(condRequire !== null){
	    var condItemArr = condRequire;
	    var labelItemArr = labelRequire;
	    var varItemArr = varRequire;
	    var index = 0;
	    for(index; index<condItemArr.length; ++index){
		var labelTag = '';
		if(labelItemArr[index].length>50){
		    labelTag = labelItemArr[index].substr(0, 50)+'...';
		} else if(labelItemArr[index].length == 0) {
		    labelTag = varItemArr[index];
		} else {
		    labelTag = labelItemArr[index];
		}
		$('#showContent-'+ id).append('<span id="field-'+condItemArr[index]+'-'+id+'" data-id="'+condItemArr[index]+'" data-var="'+varItemArr[index]+'" class="field-item label label-success">'+labelTag+' <a class="close btn-del" >&times;</a></span> ');

	    }
	}
    }
}

function createConditionArray(data) {
    //$.cookie('gen_condition', JSON.stringify(data), {path:'/', expire:60*60*24});
    $('#conditionFields').val(JSON.stringify(data));
    //console.log(JSON.stringify(data));
    //localStorage.setItem("todoData", JSON.stringify(data));
}

function updateCondition(editSelect) {
    var items = $(editSelect).find('span.field-item');
    var fieds = [];
    $.each(items, function (index, value) {
	fieds.push($(value).attr('data-id'));
    });
    
    return fieds;
}

function updateConditionLabel(editSelect) {
    var items = $(editSelect).find('span.field-item');
    var fieds = [];
    $.each(items, function (index, value) {
	//$(value).find('.close').remove();
	fieds.push($(value).text());
    });
    
    return fieds;
}

function updateConditionVar(editSelect) {
    var items = $(editSelect).find('span.field-item');
    var fieds = [];
    $.each(items, function (index, value) {
	fieds.push($(value).attr('data-var'));
    });
    
    return fieds;
}

function modalCondition(url) {
    $('#modal-core-options .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
    $('#modal-condition').modal({'show':true, 'backdrop':false})
    .find('.modal-content')
    .load(url);
}