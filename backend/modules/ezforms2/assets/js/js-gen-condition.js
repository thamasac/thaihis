$(document).ready(function () {

});

function domHtml(fieldId, inputId, inputValue, fixValue, inputType, style, hideAll=0) {
    if (inputType == 'radio' || inputType == 'select') {
	if(hideAll==1){
          if (style == 'none') {
              $('div[item-id=\"' + fieldId + '\"]').hide('slow');
          } else {
              $('div[item-id=\"' + fieldId + '\"]').show('slow');
          }
        } else {
          if (style == 'none') {
              $('div[item-id=\"' + fieldId + '\"]').children().hide('slow');
          } else {
              $('div[item-id=\"' + fieldId + '\"]').children().show('slow');
          }
        }
	

    } else {
	if (inputValue == fixValue) {
	    if (style == 'none') {
		style = 'block';
	    } else {
		style = 'none';
	    }
	}
	
        if(hideAll==1){
          if (style == 'none') {
              $('div[item-id=\"' + fieldId + '\"]').hide('slow');
          } else {
              $('div[item-id=\"' + fieldId + '\"]').show('slow');
          }
        } else {
          if (style == 'none') {
              $('div[item-id=\"' + fieldId + '\"]').children().hide('slow');
          } else {
              $('div[item-id=\"' + fieldId + '\"]').children().show('slow');
          }
        }
	
	
	
    }
    
}

function eventRadio(inputName, dataCond, hideAll=0) {
    
    $('input[name=\"' + inputName + '\"]').on('change', function () {
	var condObj = JSON.parse(dataCond);
        
	$.each(condObj, function (index, cvalue) {

	    var jumpArr = cvalue.cond_jump;
	    var requireArr = cvalue.cond_require;
	    var inputValue = $('input[name=\"' + inputName + '\"]:checked').val();

            if(hideAll==1){
              if (typeof inputValue == 'undefined') {
                  if (jumpArr != '' && jumpArr != null) {
                      $.each(jumpArr, function (index, value) {
                          $('div[item-id=\"' + value + '\"]').hide('slow');
                          clearFormElements('div[item-id=\"' + value + '\"]');
                      });
                  }

              } else {
                  if (inputValue == cvalue.ezf_field_value) {
                      if (jumpArr != '' && jumpArr != null) {
                          $.each(jumpArr, function (index, value) {
                              $('div[item-id=\"' + value + '\"]').hide('slow');
                              clearFormElements('div[item-id=\"' + value + '\"]');
                          });
                      }
                      if (requireArr != '' && requireArr != null) {
                          $.each(requireArr, function (index, value) {
                              $('div[item-id=\"' + value + '\"]').show('slow');
                          });
                      }

                  }
              }
            } else {
              if (typeof inputValue == 'undefined') {
                  if (jumpArr != '' && jumpArr != null) {
                      $.each(jumpArr, function (index, value) {
                          $('div[item-id=\"' + value + '\"]').children().hide('slow');
                          clearFormElements('div[item-id=\"' + value + '\"]');
                      });
                  }

              } else {
                  if (inputValue == cvalue.ezf_field_value) {
                      if (jumpArr != '' && jumpArr != null) {
                          $.each(jumpArr, function (index, value) {
                              $('div[item-id=\"' + value + '\"]').children().hide('slow');
                              clearFormElements('div[item-id=\"' + value + '\"]');
                          });
                      }
                      if (requireArr != '' && requireArr != null) {
                          $.each(requireArr, function (index, value) {
                              $('div[item-id=\"' + value + '\"]').children().show('slow');
                          });
                      }

                  }
              }
            }
	    

	});

    });
}

function setRadio(inputName, dataCond, hideAll=0) {
	var condObj = JSON.parse(dataCond);
	$.each(condObj, function (index, cvalue) {

	    var jumpArr = cvalue.cond_jump;
	    var requireArr = cvalue.cond_require;
	    var inputValue = $('input[name=\"' + inputName + '\"]:checked').val();

            if(hideAll==1){
              if (typeof inputValue == 'undefined') {
                  if (jumpArr != '' && jumpArr != null) {
                      $.each(jumpArr, function (index, value) {
                          $('div[item-id=\"' + value + '\"]').hide('slow');
                      });
                  }

              } else {
                  if (inputValue == cvalue.ezf_field_value) {
                      if (jumpArr != '' && jumpArr != null) {
                          $.each(jumpArr, function (index, value) {
                              $('div[item-id=\"' + value + '\"]').hide('slow');
                          });
                      }
                      if (requireArr != '' && requireArr != null) {
                          $.each(requireArr, function (index, value) {
                              $('div[item-id=\"' + value + '\"]').show('slow');
                          });
                      }

                  }
              }
            } else {
              if (typeof inputValue == 'undefined') {
                  if (jumpArr != '' && jumpArr != null) {
                      $.each(jumpArr, function (index, value) {
                          $('div[item-id=\"' + value + '\"]').children().hide('slow');
                      });
                  }

              } else {
                  if (inputValue == cvalue.ezf_field_value) {
                      if (jumpArr != '' && jumpArr != null) {
                          $.each(jumpArr, function (index, value) {
                              $('div[item-id=\"' + value + '\"]').children().hide('slow');
                          });
                      }
                      if (requireArr != '' && requireArr != null) {
                          $.each(requireArr, function (index, value) {
                              $('div[item-id=\"' + value + '\"]').children().show('slow');
                          });
                      }

                  }
              }
            }
	    

	});
}

function eventSelect(inputId, dataCond, hideAll=0) {
    $('#' + inputId).on('change', function () {
	var condObj = JSON.parse(dataCond);
	$.each(condObj, function (index, cvalue) {
	    var jumpArr = cvalue.cond_jump;
	    var requireArr = cvalue.cond_require;

            if(hideAll==1){
              if ($('#' + inputId).val() == cvalue.ezf_field_value) {
                  if (jumpArr != '' && jumpArr != null) {
                      $.each(jumpArr, function (index, value) {
                          $('div[item-id=\"' + value + '\"]').hide('slow');
                          clearFormElements('div[item-id=\"' + value + '\"]');
                      });
                  }
                  if (requireArr != '' && requireArr != null) {
                      $.each(requireArr, function (index, value) {
                          $('div[item-id=\"' + value + '\"]').show('slow');
                      });
                  }
              } else if ($('#' + inputId).val() == '') {
                  if (jumpArr != '' && jumpArr != null) {
                      $.each(jumpArr, function (index, value) {
                          $('div[item-id=\"' + value + '\"]').hide('slow');
                          clearFormElements('div[item-id=\"' + value + '\"]');
                      });
                  }

              }
            } else {
              if ($('#' + inputId).val() == cvalue.ezf_field_value) {
                  if (jumpArr != '' && jumpArr != null) {
                      $.each(jumpArr, function (index, value) {
                          $('div[item-id=\"' + value + '\"]').children().hide('slow');
                          clearFormElements('div[item-id=\"' + value + '\"]');
                      });
                  }
                  if (requireArr != '' && requireArr != null) {
                      $.each(requireArr, function (index, value) {
                          $('div[item-id=\"' + value + '\"]').children().show('slow');
                      });
                  }
              } else if ($('#' + inputId).val() == '') {
                  if (jumpArr != '' && jumpArr != null) {
                      $.each(jumpArr, function (index, value) {
                          $('div[item-id=\"' + value + '\"]').children().hide('slow');
                          clearFormElements('div[item-id=\"' + value + '\"]');
                      });
                  }

              }
            }
	    
	});

    });
}

function setSelect(inputId, dataCond, hideAll=0) {
	var condObj = JSON.parse(dataCond);
	$.each(condObj, function (index, cvalue) {
	    var jumpArr = cvalue.cond_jump;
	    var requireArr = cvalue.cond_require;

            if(hideAll==1){
              if ($('#' + inputId).val() == cvalue.ezf_field_value) {
                  if (jumpArr != '' && jumpArr != null) {
                      $.each(jumpArr, function (index, value) {
                          $('div[item-id=\"' + value + '\"]').hide('slow');
                      });
                  }
                  if (requireArr != '' && requireArr != null) {
                      $.each(requireArr, function (index, value) {
                          $('div[item-id=\"' + value + '\"]').show('slow');
                      });
                  }
              } else if ($('#' + inputId).val() == '') {
                  if (jumpArr != '' && jumpArr != null) {
                      $.each(jumpArr, function (index, value) {
                          $('div[item-id=\"' + value + '\"]').hide('slow');
                      });
                  }

              }
            } else {
              if ($('#' + inputId).val() == cvalue.ezf_field_value) {
                  if (jumpArr != '' && jumpArr != null) {
                      $.each(jumpArr, function (index, value) {
                          $('div[item-id=\"' + value + '\"]').children().hide('slow');
                      });
                  }
                  if (requireArr != '' && requireArr != null) {
                      $.each(requireArr, function (index, value) {
                          $('div[item-id=\"' + value + '\"]').children().show('slow');
                      });
                  }
              } else if ($('#' + inputId).val() == '') {
                  if (jumpArr != '' && jumpArr != null) {
                      $.each(jumpArr, function (index, value) {
                          $('div[item-id=\"' + value + '\"]').children().hide('slow');
                      });
                  }

              }
            }
	    
	});
}

function eventCheckBox(inputId, dataCond, hideAll=0) {
    $('#' + inputId).on('change', function () {
	var condObj = JSON.parse(dataCond);
	$.each(condObj, function (index, cvalue) {
	    var jumpArr = cvalue.cond_jump;
	    var requireArr = cvalue.cond_require;
            
            if(hideAll==1){
              if ($('#' + inputId).prop("checked")) {
                  if (jumpArr != '' && jumpArr != null) {
                      $.each(jumpArr, function (index, value) {
                          $('div[item-id=\"' + value + '\"]').hide('slow');
                          clearFormElements('div[item-id=\"' + value + '\"]');
                      });
                  }
                  if (requireArr != '' && requireArr != null) {
                      $.each(requireArr, function (index, value) {
                          $('div[item-id=\"' + value + '\"]').show('slow');
                      });
                  }
              } else {
                  if (jumpArr != '' && jumpArr != null) {
                      $.each(jumpArr, function (index, value) {
                          $('div[item-id=\"' + value + '\"]').show('slow');
                      });
                  }
                  if (requireArr != '' && requireArr != null) {
                      $.each(requireArr, function (index, value) {
                          $('div[item-id=\"' + value + '\"]').hide('slow');
                          clearFormElements('div[item-id=\"' + value + '\"]');
                      });
                  }
              }
            } else {
              if ($('#' + inputId).prop("checked")) {
                  if (jumpArr != '' && jumpArr != null) {
                      $.each(jumpArr, function (index, value) {
                          $('div[item-id=\"' + value + '\"]').children().hide('slow');
                          clearFormElements('div[item-id=\"' + value + '\"]');
                      });
                  }
                  if (requireArr != '' && requireArr != null) {
                      $.each(requireArr, function (index, value) {
                          $('div[item-id=\"' + value + '\"]').children().show('slow');
                      });
                  }
              } else {
                  if (jumpArr != '' && jumpArr != null) {
                      $.each(jumpArr, function (index, value) {
                          $('div[item-id=\"' + value + '\"]').children().show('slow');
                      });
                  }
                  if (requireArr != '' && requireArr != null) {
                      $.each(requireArr, function (index, value) {
                          $('div[item-id=\"' + value + '\"]').children().hide('slow');
                          clearFormElements('div[item-id=\"' + value + '\"]');
                      });
                  }
              }
            }
	    
	});
    });

}

function setCheckBox(inputId, dataCond, hideAll=0) {
    var condObj = JSON.parse(dataCond);
    $.each(condObj, function (index, cvalue) {
	var jumpArr = cvalue.cond_jump;
	var requireArr = cvalue.cond_require;

        if(hideAll==1){
          if ($('#' + inputId).prop("checked")) {
              if (jumpArr != '' && jumpArr != null) {
                  $.each(jumpArr, function (index, value) {
                      $('div[item-id=\"' + value + '\"]').hide('slow');
                  });
              }
              if (requireArr != '' && requireArr != null) {
                  $.each(requireArr, function (index, value) {
                      $('div[item-id=\"' + value + '\"]').show('slow');
                  });
              }
          } else {
              if (jumpArr != '' && jumpArr != null) {
                  $.each(jumpArr, function (index, value) {
                      $('div[item-id=\"' + value + '\"]').show('slow');
                  });
              }
              if (requireArr != '' && requireArr != null) {
                  $.each(requireArr, function (index, value) {
                      $('div[item-id=\"' + value + '\"]').hide('slow');
                  });
              }
          }
        } else {
          if ($('#' + inputId).prop("checked")) {
              if (jumpArr != '' && jumpArr != null) {
                  $.each(jumpArr, function (index, value) {
                      $('div[item-id=\"' + value + '\"]').children().hide('slow');
                  });
              }
              if (requireArr != '' && requireArr != null) {
                  $.each(requireArr, function (index, value) {
                      $('div[item-id=\"' + value + '\"]').children().show('slow');
                  });
              }
          } else {
              if (jumpArr != '' && jumpArr != null) {
                  $.each(jumpArr, function (index, value) {
                      $('div[item-id=\"' + value + '\"]').children().show('slow');
                  });
              }
              if (requireArr != '' && requireArr != null) {
                  $.each(requireArr, function (index, value) {
                      $('div[item-id=\"' + value + '\"]').children().hide('slow');
                  });
              }
          }
        }
	
    });
}

function clearFormElements(ele) {
    $(ele).find(':input').each(function () {
	var type = this.type;
	var tag = this.tagName.toLowerCase(); // normalize case
	if (type == 'text' || type == 'password' || tag == 'textarea') {
	    this.value = '';
	} else if (type == 'checkbox' || type == 'radio') {
	    this.checked = false;
	} else if (tag == 'select') {
	    this.value = '';
	} else {
	    this.value = '';
	}
	$(this).trigger("change");
    });
}

function setFormElements(data, model) {
    for (var key in data) {

	var type_id = $('#' + model + '_' + key).attr('type');
	var type_name = $('input[name="' + model + '[' + key + ']"][value="' + data[key] + '"]').attr('type');
	var tag = $('#' + model + '_' + key).prop('tagName');
	var type_list = $('input[name="' + model + '[' + key + '][]"]').attr('type');

	if (type_id == 'text' || type_id == 'hidden' || type_id == 'password' || tag == 'TEXTAREA') {
	    $('#' + model + '_' + key).val(data[key]);
	}

	if (type_name == 'checkbox' || type_name == 'radio') {
	    $('input[name="' + model + '[' + key + ']"][value="' + data[key] + '"]').attr('checked', true);
	}

	if (type_list == 'checkbox') {
	    var arr = jQuery.parseJSON(data[key]);
	    for (var akey in arr) {
		$('input[name="' + model + '[' + key + '][]"][value="' + arr[akey] + '"]').attr('checked', true);
	    }
	}

	if (tag == 'SELECT') {
	    if ($('#' + model + '_' + key).attr('multiple') == 'multiple') {
		var arr = jQuery.parseJSON(data[key]);

		for (var akey in arr) {
		    $('#' + model + '_' + key + ' option[value="' + arr[akey] + '"]').attr('selected', true);
		}
	    }
	    else {
		$('#' + model + '_' + key).val(data[key]);
	    }

	}
    }
}