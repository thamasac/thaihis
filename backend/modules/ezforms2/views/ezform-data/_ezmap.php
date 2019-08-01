<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
\appxq\sdii\assets\LeafletAsset::register($this);
?>

<div class="row">

    <div class="col-md-12 ">
        <div id="map" style="width: 100%; height: 700px; margin-top: 10px;"></div>
    </div>
</div>

<?php
        $js='';
        $btn_add = '';
        
        if(is_array($forms) && !empty($forms)){
            $overlays='{';
            foreach ($forms as $key => $value) {
                //โปรแกรม สร้างแมพ
                $iconArry = explode('-', $value['icon']);
                $prefix = $iconArry[0];
                $icon = str_replace($prefix.'-', '', $value['icon']);
                $color = $value['color'];
                $show = $value['show'];
                $map_field = $value['field'];
                
                $ezform = backend\modules\ezforms2\classes\EzfQuery::getEzformOne($value['ezf_id']);
                $dataField = backend\modules\ezforms2\classes\EzfQuery::getFieldRef($ezform['ezf_id'], $map_field, $ezform['ezf_version']);
                
                $lng_field = '';
                $lat_field = '';
                if($dataField){
                    foreach ($dataField as $field_value) {
                        if($field_value['ezf_field_label']=='lat'){
                            $lat_field = $field_value['ezf_field_name'];
                        } elseif($field_value['ezf_field_label']=='lng'){
                            $lng_field = $field_value['ezf_field_name'];
                        }
                        
                    }
                } else {
                    continue;
                }
                
                if($lng_field=='' || $lat_field == ''){
                    continue;
                }
                
                $layerGroup = "map_{$key}";
                $js .= "var $layerGroup = new L.LayerGroup();";
                
                $sedate = null;
                if(isset($value['conddate']) && $value['conddate']==1){
                    $sedate = ['s'=>$sdate, 'e'=>$edate];
                } 
                $var_date = isset($value['date']) && !empty($value['date'])?$value['date']:'create_date';
                        
                $data_map = \backend\modules\ezmodules\classes\ModuleQuery::genMapData($ezform, $sedate, $var_date, $lng_field, $lat_field);
                
                if($data_map){
//                                    var data_set = {
//                                        'sys_lat':e.latlng.lat,
//                                        'sys_lng':e.latlng.lng,
//                                    };
//                                    data_set = btoa(JSON.stringify(data_set));
                    foreach ($data_map as $i => $map) {
                        if($map[$lat_field]!='' && $map[$lng_field]!=''){
                            $eventEdit = '';
                            if($addmap==0){
                                $eventEdit = ".on('click', function(e){
                                        var url = '".Url::to(['/ezforms2/ezform-data/ezform',
                                                'ezf_id'=>$value['ezf_id'],
                                                'dataid'=>$map['id'],
                                                'target'=>$target,
                                                'reloadDiv' => $reloadDiv,
                                                'modal' => $modal,
                                            ])."';

                                        modalEzformMap(url, '$modal');
                                    })";
                            }
                            $js .= "L.marker([{$map[$lat_field]}, {$map[$lng_field]}], {icon: L.AwesomeMarkers.icon({icon: '$icon', prefix: '$prefix', markerColor: '$color'}) })$eventEdit.addTo($layerGroup);";
                        }
                    }
                }
                if($show==1){
                    $js .= "$layerGroup.addTo(map);";
                }
                
                $overlays .= "'{$value['label']}':$layerGroup,";
                
                if(isset($value['adddata']) && $value['adddata']==1){
                    $btn_add .= backend\modules\ezforms2\classes\EzfHelper::btn($value['ezf_id'])
                            ->label('<i class="fa '.$value['icon'].'"></i> '.$value['label'])
                            ->reloadDiv($reloadDiv)
                            ->options(['data-lat-field'=>$lat_field, 'data-lng-field'=>$lng_field])
                            ->buildBtnAdd()
                            .' ';
                }
                
            }
            $overlays .= '}';
            
            $js .= "
               
                var grayscale   = L.tileLayer(mbUrl2, {id: 'mapbox.light', attribution: mbAttr2}),
                    streets  = L.tileLayer(mbUrl, {id: 'mapbox.streets',   attribution: mbAttr});   
                streets.addTo(map);
                var baseLayers = {
                        'Satellite': grayscale,
                        'Streets': streets
                };        
                var overlays = $overlays;
                    
                L.control.layers(baseLayers,overlays, 
                {
                    collapsed: false,
                    autoZIndex: false
                }).addTo(map);
                
               lc = L.control.locate({
                    strings: {
                        title: '".Yii::t('app', 'Current Coordinates')."'
                    }
                }).addTo(map);

            ";
        }
        
        $eventAdd = '';
        if($addmap==1){
            $btn_add = $btn_add==''?'Do not allow extra data.':$btn_add;
            $eventAdd = "var currentMarker=null;

            map.on('click', function (event) {
             if (currentMarker) {

                    currentMarker._icon.style.transition = 'transform 0.3s ease-out';
                    currentMarker._shadow.style.transition = 'transform 0.3s ease-out';

                    currentMarker.setLatLng(event.latlng);
                    currentMarker.openPopup();

                    setTimeout(function () {
                        currentMarker._icon.style.transition = null;
                        currentMarker._shadow.style.transition = null;
                    }, 300);

                    $('.ezform-main-open').attr('data-lat',event.latlng.lat);
                    $('.ezform-main-open').attr('data-lng',event.latlng.lng);

                    return;
                }

                currentMarker = L.marker(event.latlng, {
                    icon: L.AwesomeMarkers.icon({icon: 'star',  prefix: 'fa',markerColor: 'red'}),
                    draggable: true
                }).addTo(map).on('click', function (e) {
                    setTimeout(function () {
                        $('.ezform-main-open').attr('data-lat',e.latlng.lat);
                        $('.ezform-main-open').attr('data-lng',e.latlng.lng);
                    }, 300);
                    event.originalEvent.stopPropagation();
                }).bindPopup('$btn_add',{closeButton:false}).openPopup();
                
                $('.ezform-main-open').attr('data-lat',event.latlng.lat);
                $('.ezform-main-open').attr('data-lng',event.latlng.lng);
                

            });";
        }
        
        $this->registerJs("
	
        var map = L.map('map').setView([$lat_init, $lng_init], $zoom_init);

            var mbUrl2 = 'http://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}';
            var mbAttr2 = 'Map data &copy; <a href=\"http://openstreetmap.org\">OpenStreetMap</a> contributors, ' ;

            var mbUrl = 'https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw';
            var mbAttr = 'Map data &copy; <a href=\"http://openstreetmap.org\">OpenStreetMap</a> contributors, ' ;


            L.tileLayer(mbUrl, {
              maxZoom: 18,
              attribution: mbAttr,
              id: 'mapbox.streets'
            }).addTo(map);

            // Add geocoder search-MKZrG6M
            var geocoder = L.Control.geocoder({position:'topleft'}).addTo(map);

        $js
        
        $eventAdd
            
        function modalEzformMap(url, modal) {
            $('#'+modal+' .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
            $('#'+modal).modal('show')
            .find('.modal-content')
            .load(url);
        }
        
    ");
    ?>