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
$ezfId = $options['ezf_id'];
$modalUrl = "/ezforms2/ezform-data/ezform?ezf_id=$ezfId&modal=modal-ezform-main&reloadDiv=&target=&targetField=&version=&db2=0&dataid=";
$insertUrl = "/api/ezform/save-ezform";
?>
<style>
    .cid-view-bg {
        position: relative;
        height: 360px;
        width: 540px
    }

    .cid-image-bg {
        position: absolute;
        height: 100%;
    }

    #cid-th-name {
        font-size: 24px;
        position: absolute;
        top: 29%;
        left: 32%;
    }

    #cid-id {
        font-size: 24px;
        position: absolute;
        top: 16%;
        left: 45%
    }

    #cid-en-name {
        font-size: 18px;
        position: absolute;
        top: 39%;
        left: 28%;
    }

    #cid-birthdate {
        font-size: 18px;
        position: absolute;
        top: 49%;
        left: 28%;
    }

    #cid-address {
        width: 55%;
        font-size: 18px;
        position: absolute;
        top: 59%;
        left: 18%;
    }

    #cid-image {
        position: absolute;
        width: 22%;
        top: 42%;
        right: 5%
    }

    .extension-support{
        position: absolute;bottom:0;right: 0
    }

    @media screen and (max-width: 845px) {
        .extension-support{
            display: none;
        }
    }
    @media screen and (max-width: 720px) {
        .cid-image-bg {
            position: absolute;
            width: 320px;
            height: 205px;
        }

        .cid-view-bg {
            position: relative;
            height: 180px;
            width: 360px
        }

        #cid-id {
            font-size: 16px;
            position: absolute;
            top: 18%;
            left: 40%
        }

        #cid-th-name {
            font-size: 16px;
            position: absolute;
            top: 32%;
            left: 28%;
        }


        #cid-en-name {
            font-size: 12px;
            position: absolute;
            top: 44%;
            left: 24%;
        }

        #cid-birthdate {
            font-size: 14px;
            position: absolute;
            top: 54%;
            left: 24%;
        }

        #cid-address {
            width: 48%;
            font-size: 12px;
            position: absolute;
            top: 66%;
            left: 12%;
        }

        #cid-image {
            position: absolute;
            width: 19%;
            top: 47%;
            right: 17%
        }

    }

</style>
<div class="extension-support">
    <div><img height="96px" src="/img/smartcard_support.png"/></div>
    <div class="text-center">
        <a href="#" "position: absolute">Require Extensions</a>
    </div>
</div>
    <div class="cid-view-bg">
        <img class="cid-image-bg" src="/img/cid_bg.jpg"/>
        <div id='cid-id'> </div>
        <span id='cid-th-name'>Please Insert TH Citizen Card</span>
        <span id='cid-en-name'>Name - </span>
        <span id='cid-birthdate'>Birthdate DD/MM/YYYY</span>
        <span id='cid-address'>Address -</span>
        <img id="cid-image"/>

    </div>
<?php
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
        console.log(personInfo);
        $("#cid-id").html(personInfo['citizenId']);
        $("#cid-th-name").html(personInfo['thPrefix']+" "+personInfo['thFirstname']+" "+personInfo['thLastname']);
        $("#cid-en-name").html(personInfo['enPrefix']+" "+personInfo['enFirstname']+" "+personInfo['enLastname']);
        $("#cid-birthdate").html(personInfo['birthdate']);
                        let tempAddr = personInfo['address'].replace(/#/g, ' ')

        $("#cid-address").html(tempAddr);
        $("#cid-image").attr("src","data:image/jpeg;base64,"+personInfo["photo"]);
    }
});
socket.on('CURRENT_STATE', function (data) {
    console.log('CURRENT_STATE', data);
});


JS
);
?>

