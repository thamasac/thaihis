<?php
// start widget builder

/* Params widget สามารถใช้งาน ตัวแปรต่อไปนี้ได้
  'options' => $options,
  'widget_config' => $widget_config,
  'model' => $model,
  'modelOrigin'=>$modelOrigin,
  'menu' => $menu,
  'module' => $module,
  'addon' => $addon,
  'filter' => $filter,
  'reloadDiv' => $reloadDiv,
  'dataFilter' => $dataFilter,
  'modelFilter' => $modelFilter,
  'target' => $target,
 */
//appxq\sdii\utils\VarDumper::dump($options); 
cpn\chanpan\assets\copy\CNCopy::register($this);
$elementStr = implode(',', $options['input_type']);
$widget_id = isset($widget_config['widget_id']) ? $widget_config['widget_id'] : '';
//\appxq\sdii\utils\VarDumper::dump($elementStr);
?>
<div id="div-reload" style="padding:10px;"></div>
<button class="btn btn-lg btn-success btnShowElement" ><i class='fa fa-eye'></i> Show Element</button> 
<?php \appxq\sdii\widgets\CSSRegister::begin()?>
<style>
    .widgetmt-100{margin-top:100px;}
</style>
<?php \appxq\sdii\widgets\CSSRegister::end();?>
<?php
\richardfan\widget\JSRegister::begin([
    'position' => \yii\web\View::POS_READY
]);
?>
<script>
   $('.btnShowElement').on('click' , function(){
       $('.sdbox').addClass('widgetmt-100');
       $("<?= $elementStr?>").each(function( index ) {
           //$(this).html('');
           let cl = $(this).attr('class');
           let id = $(this).attr('id');
           let value = $(this).text(); 
           let clstr = '';
           $(this).unbind( "click" );
           
           
           if(id != undefined){
               $(this).html(`
                    <div class='alert alert-info'>
                        <div class='row'>
                            <div class='col-md-12 col-sm-12 col-xs-12'>
                                <input type='text' value='#${id}' class='form-control'>   
                            </div>
                        </div>
                        <br>
                        ${value}
                   </div>`);
           }else{
               if(cl != undefined){
                   let _class = cl.split(" ");
                   let html = `<div class='alert alert-info'> <div class='row'>`;
                   for(let i of _class){
                       if(i != ''){
                           let id = makeid();
                           html += `
                                    <div class='col-md-12 col-sm-12 col-xs-12'>
                                        <input id='txt-${id}' type='text' value='.${i}' class='form-control'>   
                                    </div>
                            `;
                       }
                   }  
                   html += `</div><div>${value}</div></div>`;
                    $(this).html(html);
               }
           }
           $('#div-reload').html(`<button onClick='location.reload()' class="btn btn-lg btn-warning btnReload"><i class="fa fa-refresh" aria-hidden="true"></i> Reload</button>`);
            
       }); 
        
       return false;
   }); 
    function makeid() {
        var text = "";
        var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

        for (var i = 0; i < 6; i++)
          text += possible.charAt(Math.floor(Math.random() * possible.length));

        return text;
      }
</script>
<?php \richardfan\widget\JSRegister::end(); ?>