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

// Usage with ActiveForm and model
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
$modal='modal-ezform-main';
$ezf_id = "1532334018067132600";

?>
<style>
    .numberCircle {
        border-radius: 50%;
        /* remove if you don't care about IE8 */
        width: 36px;
        position: relative;
        left: -30px;
        top: -30px;
        height: 36px;
        padding: 8px;
        background: #fff;
        border: 2px solid #666;
        color: #494949;
        text-align: center;
        font: 16px Arial, sans-serif;
    }
</style>
<!--style="max-width: 340px;min-width: 240px;width:30%;" class="pull-right"-->
<div class="row">
<div class="col-md-4 col-md-offset-8">
    <div class="row" style="margin-bottom: 8px">
        <h3>Preset </h3>
    </div>
    <div class="row">
        <div class="col-md-10">
            <?php
            try {
                echo Select2::widget([
                    'name' => "Filter",
                    'value' => "0",
                    'initValueText' => "Please Select template",
                    'options' => ['placeholder' => 'ค้นหา  ' . $label],
                    'pluginEvents' => [
                        "select2:select" => "function(e) { setPreset(e.params.data) }",
                    ],
                    'pluginOptions' => [
                        'minimumInputLength' => 0,
                        'ajax' => [
                            'url' => \yii\helpers\Url::to(['/workshop/default/workshop-data']),
                            'dataType' => 'json',
                            'data' => new \yii\web\JsExpression('function(params) { return {q:params.term}; }')
                        ],
                        'escapeMarkup' => new \yii\web\JsExpression('function (markup) { return markup; }'),
                        'templateResult' => new \yii\web\JsExpression('function(data) { return data.text; }'),
                        'templateSelection' => new \yii\web\JsExpression('function (data) { return data.text; }'),
                    ],
                ]);
            } catch (Exception $e) {
                echo "<p>" . $e->getMessage() . "</p>";
            }

            ?>
        </div>
        <div class="col-md-2">
            <?php
            $btnAdd = \backend\modules\ezforms2\classes\EzfHelper::btn('1533183847033979900')
                ->options([
                    'class' => 'btn btn-success btn-sm pull-right',
                ])
                ->buildBtnAdd();
            echo $btnAdd;
            ?>
        </div>
    </div>


</div>
</div>
<hr>
<div id="hotreload-div">
    <h3 style="color: #828282;margin-bottom: 300px;text-align: center"> Please select preset to start collaborative. </h3>
</div>
<?php

//<a class="btn btn-primary btn-xs btn-update" href="/ezforms2/ezform-data/ezform?ezf_id=1532334018067132600&amp;dataid=1533197843059427100&amp;modal=modal-ezform-main&amp;reloadDiv=workshop-grid-view&amp;db2=0" title="Update" data-action="update" data-pjax="0"><span class="glyphicon glyphicon-pencil"></span></a>
//$icon = 'pencil';
//$btnEdit = Html::a('<span class="glyphicon glyphicon-pencil"></span>', Url::to(['/ezforms2/ezform-data/ezform',
//    'ezf_id' => $ezf_id,
//    'dataid' => '{replaceId}',
//    'modal' => $modal,
//    'reloadDiv' => '',
//]), [
//    'data-action' => 'update',
//    'title' => Yii::t('yii', 'Update'),
//    'data-pjax' => '0',
//    'class' => "btn btn-primary btn-xs btn-update pull-right",
//
//]);
$btnEdit = \backend\modules\ezforms2\classes\EzfHelper::btn($ezf_id)
    ->options([
        'class' => 'btn btn-primary btn-xs btn-update pull-right',
        'style' => 'margin-right: 4px;'
    ])
    ->buildBtnEdit("replaceId");
$url = yii\helpers\Url::to(['/workshop/default/reload-data']);
$user_id = Yii::$app->user->id;
$this->registerJs( <<<JS
let userId = "$user_id";
let currentPreset = null;
let indexJquryArr = {};
let fields = [];
let currentTimeout = null; 
let lastUpdate = null;
let index = 1 ;

function setPreset(presetData){
    lastUpdate = null;
    currentPreset = presetData.id;
    index =1;
    // currentJquryArr = [];
    if(currentTimeout != null)
        clearTimeout(currentTimeout);
    $('#hotreload-div').empty();
    fetchData();
}

function fetchData(){
    let url = '$url'+"?id="+currentPreset+"&lastUpdate="+lastUpdate;
    $.get(url, function(data){
       // console.log(data.results);
       lastUpdate = data["lastUpdate"];
       for(let objField of data.fields){
           fields[objField["fieldName"]] = objField["fieldLabel"];
       }
       for (let obj of data.results){
           let result = createDiv(obj);
       } 
       currentTimeout = setTimeout(fetchData,2000);
   }).fail(function(err) {
       console.error(err);
       currentTimeout = setTimeout(fetchData,2000);
   });
}


function getFieldLabel(fieldName) {
    if(fields[fieldName] == null){
        return fieldName;
    }
    return fields[fieldName];
}
function createDiv(obj){
    let jHtml = $('#hotreload-'+obj["id"]);
    // console.log(jHtml);
    let isExistBefore = false;
    if(jHtml.length){
        jHtml.empty();
        isExistBefore = true;
        if(obj["rstat"] == 3){
            jHtml.remove();
            return false;
        }
    }else{
        jHtml = $("<div/>", {
         id: "hotreload-"+obj["id"],
         class: 'panel panel-default panel-body',
        });
        indexJquryArr[obj["id"]] = index;
        index ++;
    }
    //numberCircle
    jHtml.append($("<div/>", {
         class: 'numberCircle',
        }).append(indexJquryArr[obj["id"]])
    );
    let addedEditButton = false;
     if(obj["create_user_id"] == userId){
        addedEditButton =  true;
    }   
    if(!addedEditButton)
    for(let uid of obj["editable_users"]){
        if(uid == userId){
            addedEditButton =  true;
            break;
        }   
    }
    if(addedEditButton){
        let btnEditHtml = '$btnEdit';
        btnEditHtml = btnEditHtml.replace("replaceId",obj["id"]);
        jHtml.append(btnEditHtml);
    }
    for(let key in obj){
        switch (key){
            case "id":
                jHtml.append("<p style='color:#d3d3d3;' class='text-right'>"+(obj[key])+"</p>");
                break;
            case "editable_users":
            case "create_user_id":
            case "rstat":
                break;
            default:
                jHtml.append(
                    "<div class='row' style='margin-bottom:8px';>" 
                    +"<div class='col-md-2' style='font-size:18px;'><p><b><u>"+getFieldLabel(key)+"</u></b></p></div>"
                    + "<div class='col-md-8'>"+obj[key]+"</div>"
                    + "</div>");
                break
        } 
    }
    jHtml.append("<hr>");
    if(isExistBefore){
         jHtml.fadeIn(100).fadeOut(200).fadeIn(200);
    }else{
          $('#hotreload-div').append(jHtml);
    }
    return true;
}
    
initWorkshopGrid=function(){        
//        let url = '$url';
//        $.get(url, function(data){
//            $('#workshop-grid').html(data);
//        }).fail(function(err) {
//             err = JSON.parse(JSON.stringify(err))['responseText'];
//             $('#workshop-grid').html(`<div class='alert alert-danger'>\${err}</div>`);
//        });
    }
    initWorkshopGrid();

JS
);
?>


 