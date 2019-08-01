<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
$zoom = isset($options['zoom']) ? $options['zoom'] : 4;
$type = isset($type) ? $type : 0;
$reloadDiv = 'grid-widget-custom';
?>
<div class="row">
    <div class="col-md-12">
        <div id="map" style="background: #f5f5f5; height:500px; width:100% ">

        </div>
    </div>
</div>
<div class="leaflet-top leaflet-right">
    <div class="leaflet-control-layers leaflet-control-layers-expanded leaflet-control" aria-haspopup="true">
        <a class="leaflet-control-layers-toggle" href="#" title="Layers"></a>
        <form class="leaflet-control-layers-list">
            <div class="leaflet-control-layers-overlays">
                <label>
                    <div><input type="checkbox" class="leaflet-control-layers-selector" id="Interventional" <?php if($Interventional=="1"){ echo "checked";} ?>>
                        <span> <b><font>Interventional</font></b></span>
                    </div>
                </label>
                <label>
                    <div>
                        <input type="checkbox" class="leaflet-control-layers-selector" id="Observational" <?php if($Observational=="1"){ echo "checked";} ?>>
                        <span> <b><font>Observational</font></b></span>
                    </div>
                </label>
            </div>
            <div class="form-group" style="padding-left: 10px; padding-right: 10px">
                <label>Overall Recruitment Status</label>
                <?php
                echo kartik\helpers\Html::dropDownList('type',$type, ArrayHelper::map($dropdown, 'value', 'ezf_choicelabel'), [
                    'class' => 'form-control',
                    'id' => 'type',
                ]);
                ?>
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
    var locations= <?= $data ?>;
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
        latlng = new L.LatLng(14.021935696,100.53442851);
    var map = new L.Map('map', {center: latlng, zoom: 4, layers: [tiles]});
    var data = L.markerClusterGroup();
    map.createPane('panel').style.zIndex = 610;
    for (i = 0; i < locations.length; i++) {
        L.marker([locations[i][1],locations[i][2]], {icon: Icon}).bindPopup(locations[i][0]).addTo(data);
    }
    data.on('popupopen', function(e) {
        $('.marker').on('click',function(){
            var url = $(this).attr('href');
            $('#modal-view .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
            $('#modal-view').modal('show')
            .find('.modal-content')
            .load(url);
            return false;
        }); 
    });
    data.addTo(map);
    $( "#map" ).append( $( ".leaflet-right" ) );
    $('#type').on('change', function () {
        var options =<?= json_encode($options); ?>;
        options['type'] = $('#type').val();
        var url = '/tctr/tctr-data/show-map';
        $.ajax({
            url: url,
            type: 'POST',
            data: options,
            success: function (result) {
                $('#show-map').html(result);
            }
        })
    });
    $('#Interventional ,#Observational').on('click',function(){
        var options=<?= json_encode($options); ?>;
        if($('#Interventional').is(':checked')) { 
            options['Interventional']= '1';
        }else{
            options['Interventional']= '0';
        }
        if($('#Observational').is(':checked')) { 
            options['Observational']= '1';
        }else{
            options['Observational']= '0';
        }
        options['type'] = $('#type').val();
        var url = '/tctr/tctr-data/show-map';
        $.ajax({
            url:url,
            type:'POST',
            data:options,
            success: function(result) {
                $('#show-map').html(result);
            }
       })
    });
</script>
<?php \richardfan\widget\JSRegister::end(); ?>