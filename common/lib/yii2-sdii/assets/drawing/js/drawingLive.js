var colorRed = "#ff0000";
var colorBlue = "#0033ff";
var colorYellow = "#ffcf33";
var colorBlack = "#333";
//var canvasDraw = 'canvasDrawDiv';
//var drawingName = 'myCanvasDrawing';
//var saveBtn = 'saveDrawing';

function drawCanvas(drawingName, canvasDraw, saveBtn, width, height, outlineName, outlinePath, outlineBg)
{
  var context;
  var imageObj = new Image();
  var imageBgObj = new Image();
  var paint = true;
  var textTool = false;
  var saveId = saveBtn;
  var lastEvent;
  var mouseDown = false;
  
  var canvasDiv = document.getElementById(canvasDraw + '_box');
  var canvas = document.createElement('canvas');
  canvas.setAttribute('width', width);
  canvas.setAttribute('height', height);
  canvas.setAttribute('class', 'drawingBox');
  canvas.setAttribute('id', drawingName);
  canvasDiv.appendChild(canvas);
  if (typeof G_vmlCanvasManager != 'undefined') {
    canvas = G_vmlCanvasManager.initElement(canvas);
  }
  
  context = canvas.getContext('2d');


  context.strokeStyle = colorRed;//สีเส����
  context.lineCap = 'round';//lineJoin เหลี��ยม  , lineCap ม��
  context.lineWidth = 4;//ข��า��เส����

  $(canvasDiv).on('click', '.saveText', function () {
    var x = $(this).attr('data-x');
    var y = $(this).attr('data-y');
    //get the value of the textarea then destroy it and the save button
    var text = $(canvasDiv).children('.textAreaPopUp').children().children().children('.textareaInput').val();
    $(canvasDiv).children('.textAreaPopUp').remove();
    //break the text into arrays based on a text width of 100px
    var phraseArray = getLines(context, text, 100);
    // this adds the text functions to the context
    CanvasTextFunctions.enable(context);
    var counter = 0;
    //set the font styles
    var font = 'Helvetica';
    var fontsize = 16;

    //draw each phrase to the screen, making the top position 20px more each time so it appears there are line breaks
    $.each(phraseArray, function () {
      //set the placement in the canvas
      var lineheight = fontsize * 1.5;
      var newline = ++counter;
      newline = newline * lineheight;
      var topPlacement = y - $(canvas).position().top + newline;
      var leftPlacement = x - $(canvas).position().left;
      text = this;
      //draw the text
      context.drawText(font, fontsize, leftPlacement, topPlacement, text);
      context.save();
      context.restore();
    });

    $('#' + saveId).trigger('click');
  });

  $(canvas).on('mousedown touchstart', function (e) {
    let rect = e.target.getBoundingClientRect();
    let event = (e.type.toLowerCase() === 'mousedown')? e.originalEvent : e.originalEvent.touches[0];
    
    let mouseX = event.pageX - rect.left;
    let mouseY = event.pageY - rect.top;

    if(e.type.toLowerCase() === 'mousemove'){
      mouseX = event.offsetX;
      mouseY = event.offsetY;
    }
      
    if (textTool) {
      context.globalCompositeOperation = 'source-over';
      
      if ($(canvasDiv).children('.textAreaPopUp').length == 0) {
        var appendString = '<div class=\"textAreaPopUp form-inline\" style=\"position:absolute;top:' + mouseY + 'px;left:' + mouseX + 'px;z-index:300;\">' +
                '<div class=\"form-group\">' +
                '<div class=\"input-group\">' +
                '<textarea type=\"text\" class=\"textareaInput form-control\" style=\"width:150px;height:35px;\" onkeyup="if(event.keyCode == 13) { $(this).parent().find(\'.saveText\').trigger(\'click\'); return false; }"></textarea>' +
                '<a type=\"button\" class=\"saveText btn btn-primary input-group-addon\" data-x=\"' + mouseX + '\" data-y=\"' + mouseY + '\">Save</a>' +
                '</div>' +
                '</div>' +
                '</div>';
        $(canvasDiv).append(appendString);
        setTimeout(function () {
          $(canvasDiv).children('.textAreaPopUp').children().children().children('.textareaInput').focus();
        }, 200);

      } else {
        $(canvasDiv).children('.textAreaPopUp').remove();
        var appendString = '<div class=\"textAreaPopUp form-inline\" style=\"position:absolute;top:' + mouseY + 'px;left:' + mouseX + 'px;z-index:300;\">' +
                '<div class=\"form-group\">' +
                '<div class=\"input-group\">' +
                '<textarea type=\"text\" class=\"textareaInput form-control\" style=\"width:150px;height:35px;\" onkeyup="if(event.keyCode == 13) { $(this).parent().find(\'.saveText\').trigger(\'click\'); return false; }"></textarea>' +
                '<a type=\"button\" class=\"saveText btn btn-primary input-group-addon\" data-x=\"' + mouseX + '\" data-y=\"' + mouseY + '\">Save</a>' +
                '</div>' +
                '</div>' +
                '</div>';
        $(canvasDiv).append(appendString);
        setTimeout(function () {
          $(canvasDiv).children('.textAreaPopUp').children().children().children('.textareaInput').focus();
        }, 200);
      }
    } else {
      mouseDown = true;
      lastEvent = event;
      
      context.beginPath();
      context.fillStyle = context.strokeStyle;
      context.arc(mouseX,mouseY,1,0,Math.PI*2,true);
      context.fill();
      context.stroke();
      context.closePath();
    }
  }).on('mousemove touchmove',function(e) {
    let rect = e.target.getBoundingClientRect();
    let event = (e.type.toLowerCase() === 'mousemove')? e.originalEvent : e.originalEvent.touches[0];
    
    let mouseX = event.pageX - rect.left;
    let mouseY = event.pageY - rect.top;
    
    let lastMouseX = event.pageX - rect.left;
    let lastMouseY = event.pageY - rect.top;
    if (lastEvent === undefined || lastEvent === null) {
         
     } else {
       lastMouseX = lastEvent.pageX - rect.left;
       lastMouseY = lastEvent.pageY - rect.top;
     }
    
    if(e.type.toLowerCase() === 'mousemove'){
      mouseX = event.offsetX;
      mouseY = event.offsetY;
      
      if (lastEvent === undefined || lastEvent === null) {
         lastMouseX = event.offsetX;
          lastMouseY = event.offsetY;
     } else {
       lastMouseX = lastEvent.pageX - rect.left;
       lastMouseY = lastEvent.pageY - rect.top;
     }
      
    }
    
    if (mouseDown) {
        //Draw lines
        context.beginPath();
        context.moveTo(lastMouseX, lastMouseY);
        context.lineTo(mouseX, mouseY);
        //context.strokeStyle = color;
        
        if (paint) {
          context.globalCompositeOperation = 'source-over';//paint
        } else {
          context.globalCompositeOperation = "destination-out";//ยา��ล�� eraser
        }
              
        context.stroke();
        context.closePath();
        lastEvent = event;
    }
}).on('mouseup touchend',function() {
    if (!textTool) {
      mouseDown = false;
      $('#' + saveId).trigger('click');
    }
}).on('mouseleave touchleave',function(e) {
    if(e.type.toLowerCase() === 'mouseleave'){
      $(canvas).trigger('mouseup');
    } else {
      $(canvas).trigger('touchend');
    }
    
});
//    
//  $(canvas)
//          .drag("start", function (ev, dd) {
//            //context.save();
//            //$('#'+saveId).removeClass('disabledDisplay');
//            if (!textTool) {
//              context.beginPath();//เริ��มเส������หม��
//              context.moveTo(
//                      ev.pageX - dd.originalX,
//                      ev.pageY - dd.originalY
//                      );
//            }
//          })
//          .drag(function (ev, dd) {
//            if (!textTool) {
//              context.lineTo(
//                      ev.pageX - dd.originalX,
//                      ev.pageY - dd.originalY
//                      );
//
//              if (paint) {
//                context.globalCompositeOperation = 'source-over';//paint
//              } else {
//                context.globalCompositeOperation = "destination-out";//ยา��ล�� eraser
//              }
//
//              context.stroke(); //วา��ภาพ
//            }
//
//          })
//          .drag("end", function (ev, dd) {
//            if (!textTool) {
//              $('#' + saveId).trigger('click');
//            }
//          });

  imageObj.onload = function () {
    context.drawImage(imageObj, 0, 0);
  };

  imageBgObj.onload = function () {
    context.drawImage(imageBgObj, 0, 0);
  };

  if (outlineName != '') {

    imageObj.src = outlinePath + '/' + outlineName; //ภาพเ��ิม
  }
  if (outlineBg != '') {
    imageBgObj.src = outlineBg; //ภาพพื����หลั��
  }

  $('#' + canvasDraw + ' .paintTool').click(function () {
    if ($(this).attr('data-type') == 'paint') {
      paint = true;
    } else {
      paint = false;
    }
  });

  $('#' + canvasDraw + ' .paintTool').click(function () {
    if ($(this).attr('data-type') == 'text') {
      textTool = true;
    } else {
      textTool = false;
    }
  });

  $('#' + canvasDraw + ' .colorTool').click(function () {
    if ($(this).attr('data-type') == 'ba') {
      context.strokeStyle = colorBlack;
    } else if ($(this).attr('data-type') == 'r') {
      context.strokeStyle = colorRed;
    } else if ($(this).attr('data-type') == 'b') {
      context.strokeStyle = colorBlue;
    } else if ($(this).attr('data-type') == 'y') {
      context.strokeStyle = colorYellow;
    }
  });

  $('#' + canvasDraw + ' .lineTool').click(function () {
    if ($(this).attr('data-type') == '2') {
      context.lineWidth = 2;
    } else if ($(this).attr('data-type') == '4') {
      context.lineWidth = 4;
    } else if ($(this).attr('data-type') == '6') {
      context.lineWidth = 6;
    } else if ($(this).attr('data-type') == '8') {
      context.lineWidth = 8;
    }
  });

  $('#' + canvasDraw + ' .clearDrawing').click(function () {
    context.clearRect(0, 0, width, height);//ล����ั����หม��
    context.drawImage(imageBgObj, 0, 0);
    //$('#'+saveId).addClass('disabledDisplay');
    $('#' + saveId).trigger('click');
  });

}

function getLines(context, phrase, maxPxLength) {
  //break the text area text into lines based on "box" width
  var wa = phrase.split(' '),
          phraseArray = [],
          lastPhrase = '',
          l = maxPxLength,
          measure = 0;
  context.font = '16px Helvetica';
  for (var i = 0; i < wa.length; i++) {
    var w = wa[i];
    measure = context.measureText(lastPhrase + w).width;
    if (measure < l) {
      lastPhrase += (' ' + w);
    } else {
      phraseArray.push(lastPhrase);
      lastPhrase = w;
    }
    if (i === wa.length - 1) {
      phraseArray.push(lastPhrase);
      break;
    }
  }
  return phraseArray;
}