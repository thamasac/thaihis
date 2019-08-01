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
//
use appxq\sdii\utils\SDUtility;
//
$divId = SDUtility::getMillisecTime();
$optionsJson = json_encode($options);
//// TODO redirect to and map field
//// CONFIRM BOX
//
//
//echo \backend\modules\ezbuilder\classes\widgets\SocketBuilder::create()
//    ->setSocketUrl('http://61.19.254.15')
//    ->setElementId('socketA')
//    ->appendEvent("connected",
//<<<JS
//    (data) => {
//    console.log('I got welcome', data )
//    }
//JS
//    )->appendEvent("onNotify",
//        <<<JS
//    (data) => {
//    }
//JS
//    )->build();
//
$ezfId = $options['ezf_id'];
$modalUrl = "/ezforms2/ezform-data/ezform?ezf_id=1503378440057007100&modal=modal-ezform-main&reloadDiv=&target=&dataid=&targetField=&version=&db2=0&initdata=";
$modalUrl = "/ezforms2/ezform-data/ezform?ezf_id=$ezfId&modal=modal-ezform-main&reloadDiv=&target=&targetField=&version=&db2=0&dataid=";
$insertUrl = "/api/ezform/save-ezform";
//
//echo backend\modules\ezforms2\classes\BtnBuilder::btn()->ezf_id($options['ezf_id'])
//    ->tag('a')->options(['class' => 'btn btn-sm '])
//    ->initdata(['pt_firstname' => 'กวินท์'])
//    ->label('<span class="fa fa-wpforms"></span>')
////    ->target(0)
////    ->dataid(0)
//    ->buildBtnAdd();
//
//$this->registerJS(<<<JS
//$("#socketA").trigger('actionTo',["emailRoom" , "ACTION_FETCH", "myID"]);
//JS
//);
//
$this->registerJS(<<<JS
var socket = io('http://localhost:5000');
var options = $optionsJson;
console.log($optionsJson);
            console.log(options['redirectUrl'])

var action = options['action'];
socket.on('STATE_CHANGE', function (data) {
    console.log('STATE_CHNAGE', data);
    if(data[1] == "State_Active_With_Card"){
                  socket.emit('ACTION', 'READ_DATA');
    }
});
socket.on('ROOM_BROADCAST', function (data) {
    console.log('ROOM_BROADCAST', data);
});
socket.on('RESPONSE', function (data) {
    console.log('RESPONSE', data);
    let res = JSON.parse(data);
    if(res['action'] === 'PERSON_INFO'){
        let personInfo = res['data'];
        console.log(action);
        if(action == 'REDIRECT'){
            let url = options['redirectUrl'];
            for(let key in personInfo){
                url = url.replace("{"+key+"}", personInfo[key]);
            }
            console.log(url);
        }else{
            console.log(options['map']);
            let initData = {};
            for (let mapName in options['map']){
                for (let fieldName of options['map'][mapName]){
                    initData[fieldName] = personInfo[mapName];
                }
            }
            
            $.post( "$insertUrl", { data: JSON.stringify(initData), ezf_id: "$ezfId" })
              .done(function( res ) {
                console.log( "Data Loaded: " + res['data']['id'] );
                modalEzformMain(encodeURI("$modalUrl")+res['data']['id'], "modal-ezform-main");

              })
              .fail(function( data ) {
                console.error( "Data Loaded: " + data );
              } );
        }
    }
});
socket.on('CURRENT_STATE', function (data) {
    console.log('CURRENT_STATE', data);
});


JS
);
?>

