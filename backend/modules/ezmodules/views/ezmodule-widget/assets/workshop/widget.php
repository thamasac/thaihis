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

?>
<div id="workshop-grid">

</div>
<?php
$url = yii\helpers\Url::to(['/workshop/default/workshop-view']);
$this->registerJs(<<<JS
initWorkshopGrid=function(){        
        let url = '$url';
        $.get(url, function(data){
            $('#workshop-grid').html(data);
        }).fail(function(err) {
             err = JSON.parse(JSON.stringify(err))['responseText'];
             $('#workshop-grid').html(`<div class='alert alert-danger'>\${err}</div>`);
        });
    }
    initWorkshopGrid();
JS
);
?>


 