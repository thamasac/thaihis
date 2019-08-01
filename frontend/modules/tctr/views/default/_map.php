<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
\cpn\chanpan\assets\CNLoadingAssets::register($this);

$type = isset($type) ? $type : 0;
$reloadDiv = 'grid-widget-custom';

?>
<div class="row">
    <div class="col-md-12">
        <div id="map" style="background: #f5f5f5; height:700px; width:100% ">
        </div>
    </div>
</div>
<div class="leaflet-top leaflet-right">
    <div class="leaflet-control-layers leaflet-control-layers-expanded leaflet-control" aria-haspopup="true">
        <a class="leaflet-control-layers-toggle" href="#" title="Layers"></a>
        <form class="leaflet-control-layers-list">
            <div class="leaflet-control-layers-overlays">
                <b style="font-size: 15px">
                    <div><input type="checkbox" class="leaflet-control-layers-selector" id="thaisite" checked>
                        <span> <b><font> Clinical studies registered in Thailand</font></b></span>
                    </div>
                </b>
                <label style="margin-left: 19px">
                    <a target="_blank" href="http://www.clinicaltrials.in.th/">
                        <span style="font-size: 12px"><?=Yii::t('app', 'https://www.clinicaltrials.in.th')?></span>
                    </a>
                </label>
                <b style="font-size: 15px">
                    <div><input type="checkbox" class="leaflet-control-layers-selector" id="othersite" >
                        <span> <b><font>Clinical studies registered in other sites</font></b></span>
                    </div>
                </b>
                <label style="margin-left: 19px">
                    <a target="_blank" href="https://clinicaltrials.gov">
                        <span style="font-size: 12px"><?=Yii::t('app', 'https://clinicaltrials.gov')?></span>
                    </a>
                </label>
                <label>
                    <div><input type="checkbox" class="leaflet-control-layers-selector" id="Interventional" checked>
                        <span> <b><font>Interventional</font></b></span>
                    </div>
                </label>
                <label>
                    <div>
                        <input type="checkbox" class="leaflet-control-layers-selector" id="Observational" checked>
                        <span> <b><font>Observational</font></b></span>
                    </div>
                </label>
            </div>
<!--            <div class="form-group" style="padding-left: 10px; padding-right: 10px">-->
<!--                <label>--><?//=Yii::t('app', 'Overall Recruitment Status')?><!--</label>-->
<!--                --><?php
//                echo kartik\helpers\Html::dropDownList('type',$type, ArrayHelper::map($dropdown, 'value', 'ezf_choicelabel'), [
//                    'class' => 'form-control',
//                    'id' => 'type',
//                ]);
//                ?>
<!--            </div>-->
            <div class="form-group" style="padding-left: 10px; padding-right: 10px">
                <?= Html::button('<i class="glyphicon glyphicon-home"></i> ' . Yii::t('app', 'Home'), ['class' => 'btn btn-default' ,'id' =>'reset-map']) ?>
            </div>
        </form>
    </div>
</div>
<?php
\richardfan\widget\JSRegister::begin([
    'position' => \yii\web\View::POS_READY
]);
?>
<script>
    var options = [];
    var  markers = [];
    var Icon = new L.Icon({
            iconUrl: '/leaflet.markercluster/icon/marker-icon-blue.png',
            shadowUrl: '/leaflet.markercluster/icon/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
    });
    var mbUrl ='https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
    var mbAttr ='&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors';
    var tiles = L.tileLayer(mbUrl, {
            maxZoom: 18,
            attribution: mbAttr,
            id: 'mapbox.streets'
        }),
        latlng = new L.LatLng(31.167182331571496,75.15876916272225);
    var map = new L.Map('map', {center: latlng, zoom: 3, layers: [tiles]});
    var data = L.markerClusterGroup();
    map.createPane('panel').style.zIndex = 610;
    $.ajax({
        url: "/leaflet.markercluster/raw-trial-thai.json",
        method: "GET",
        success : function(locations){
            for (i = 0; i < locations.length; i++) {
                L.marker([locations[i][1],locations[i][2]], {icon: Icon}).bindPopup(locations[i][0]).addTo(data);
            }
            data.addTo(map);
        }
    });
    $.ajax({
        url: "/leaflet.markercluster/raw-trial.json",
        method: "GET",
        success : function(data){
            markers = data;
            console.log('done!');
        }
    });

    data.on('popupopen', function(e) {
        $('.marker').on('click',function(){
            var dataid = $(this).attr('dataid');
            var url = '/tctr/tctr-data/ezform-view?ezf_id=1520776142078903600&modal=modal-view&reloadDiv=modal-view&type=map&dataid='+dataid;
            $('#modal-view .modal-content').html('<div class="sdloader"><i class="sdloader-icon"></i></div>');
            $('#modal-view').modal('show')
            .find('.modal-content')
            .load(url);
            return false;
        }); 
    });

    $( "#map" ).append( $( ".leaflet-right" ) );
    $('#type').on('change', function () {
        //var options =<?//= json_encode($options); ?>//;
        options['type'] = $('#type').val();
        var url = '/tctr/tctr-data/show-map';
        onLoadings();
        $.ajax({
            url: url,
            type: 'POST',
            data: options,
            success: function (result) {
                $('#show-map').waitMe("hide");
                $('#show-map').html(result);
            }
        })
    });
    $('#Interventional ,#Observational,#thaisite ,#othersite').on('click',function(){
        data.clearLayers();
        if($('#Interventional').is(':checked')) {
            var Interventional = 'Interventional';
        }else{
            var Interventional = '';
        }
        if($('#Observational').is(':checked')) {
            var Observational = 'Observational';
        }else{
            var Observational = '';
        }
        if($('#thaisite').is(':checked')) { 
            var thaisite = 'tctr';
        }else{
            var thaisite = '';
        }
        if($('#othersite').is(':checked')) {
            var othersite = 'nct';
        }else{
            var othersite= '';
        }
        // options['type'] = $('#type').val();
        onLoadings();
        $.ajax({
            url: "/leaflet.markercluster/raw-trial.json",
            method: "GET",
            success : function(locations){
                for (i = 0; i < locations.length; i++) {
                    if(othersite===locations[i][3] ){
                        if(Interventional===locations[i][5]){
                            L.marker([locations[i][1],locations[i][2]], {icon: Icon}).bindPopup(locations[i][0]).addTo(data);
                        }else if(Observational===locations[i][5]){
                            L.marker([locations[i][1],locations[i][2]], {icon: Icon}).bindPopup(locations[i][0]).addTo(data);
                        }
                    }else if(thaisite===locations[i][3]){
                        if(Interventional===locations[i][5]){
                            L.marker([locations[i][1],locations[i][2]], {icon: Icon}).bindPopup(locations[i][0]).addTo(data);
                        }else if(Observational===locations[i][5]){
                            L.marker([locations[i][1],locations[i][2]], {icon: Icon}).bindPopup(locations[i][0]).addTo(data);
                        }
                    }
                }
                hideLoadings('#show-map');
            }
        });
    });

    $('#reset-map').on('click',function(){
       map.setZoom(2);
       map.setCenter(latlng);
    });

    function onLoadings(){
        $('#show-map').waitMe({
            effect : 'facebook',
            text : 'Please wait...',
            bg : 'rgba(255,255,255,0.7)',
            color : '#000',
            maxSize : '',
            waitTime : -1,
            textPos : 'center',
            fontSize : '',
            source : '',
            onClose : function() {}
        });
    }
    function hideLoadings(ele){
        $(ele).waitMe("hide");
    }
</script>
<?php \richardfan\widget\JSRegister::end(); ?>