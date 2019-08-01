<?php

namespace appxq\sdii\widgets;

use Yii;
use yii\helpers\Html;
use yii\bootstrap\Widget as BaseWidget;

/**
 * MapInput class file UTF-8
 * @author SDII <iencoded@gmail.com>
 * @link http://www.appxq.com/
 * @copyright Copyright &copy; 2015 AppXQ
 * @license http://www.appxq.com/license/
 * @package appxq\sdii\widgets
 * @version 2.0.0 Date: Sep 5, 2015 3:18:34 PM
 * @example 
 */
class MapInput extends BaseWidget {

    public $key = '';
    public $sensor = 'false';
    public $lat='';
    public $lng='';
    public $latValue='';
    public $lngValue='';
    
    public function init() {
	parent::init();
	
	$this->clientOptions['id'] = $this->options['id'].'-map';
	$this->clientOptions['search'] = $this->options['id'].'-search';
	$this->clientOptions['lat'] = $this->options['id'].'-lat';
	$this->clientOptions['lng'] = $this->options['id'].'-lng';
	$this->clientOptions['btn-search'] = $this->options['id'].'-btn-search';
	$this->clientOptions['btn-auto'] = $this->options['id'].'-btn-auto';
	$this->clientOptions['lat-attr'] = $this->lat;
	$this->clientOptions['lng-attr'] = $this->lng;
	$this->clientOptions['lat-value'] = isset($this->latValue)?$this->latValue:'';
	$this->clientOptions['lng-value'] = isset($this->lngValue)?$this->lngValue:'';
	//$this->options['style'] = 'width: 100%; height: 350px;';
	
	echo Html::beginTag('div', $this->options) . "\n";
    }

    /**
     * Renders the widget.
     */
    public function run() {
        
        
	echo Html::beginTag('div', ['class'=>'row']) . "\n";
	echo Html::beginTag('div', ['class'=>'col-sm-9']) . "\n";
	
	echo Html::beginTag('div', ['id'=>$this->clientOptions['id'], 'style'=>'width: 100%; height: 350px;']) . "\n";
	
	echo "\n" . Html::endTag('div');
	
	echo "\n" . Html::endTag('div');
	echo Html::beginTag('div', ['class'=>'col-sm-3 sdbox-col']) . "\n";
	
	
//	echo Html::beginTag('div', ['class'=>'form-group']) . "\n";
//	echo Html::label(\Yii::t('app', 'Search'));
//	echo Html::textInput($this->clientOptions['search'], '', ['class'=>'form-control', 'id'=>$this->clientOptions['search']]);
//	echo "\n" . Html::endTag('div');
//	
//	echo Html::beginTag('div', ['class'=>'form-group']) . "\n";
//	echo Html::button(\Yii::t('app', 'Search'), ['class'=>'btn btn-block btn-success', 'id'=>$this->clientOptions['btn-search']]);
//	echo "\n" . Html::endTag('div');
	
	echo Html::beginTag('div', ['class'=>'form-group']) . "\n";
	echo Html::label(\Yii::t('app', 'Coordinates'));
	echo Html::textInput($this->clientOptions['lat'], '', ['class'=>'form-control', 'placeholder'=>'Latitude', 'id'=>$this->clientOptions['lat']]);
        echo isset($this->options['annotated_lat'])?$this->options['annotated_lat']:'';
	echo "\n" . Html::endTag('div');
	
	echo Html::beginTag('div', ['class'=>'form-group']) . "\n";
	echo Html::textInput($this->clientOptions['lng'], '', ['class'=>'form-control', 'placeholder'=>'Longitude', 'id'=>$this->clientOptions['lng']]);
        echo isset($this->options['annotated_lng'])?$this->options['annotated_lng']:'';
	echo "\n" . Html::endTag('div');
	
//	echo Html::beginTag('div', ['class'=>'form-group']) . "\n";
//	echo Html::button(\Yii::t('app', 'Current Coordinates'), ['class'=>'btn btn-block btn-success', 'id'=>$this->clientOptions['btn-auto']]);
	//echo "\n" . Html::endTag('div');
	
	echo "\n" . Html::endTag('div');
	echo "\n" . Html::endTag('div');
	echo "\n" . Html::endTag('div');
	
	
	
	$view = $this->getView();
	
	$option = \yii\helpers\Json::encode($this->clientOptions);
	
	//\appxq\sdii\assets\map\MapAsset::register($view);
	\appxq\sdii\assets\LeafletAsset::register($view);
        
	$view->registerJs("
            function initializeMap(options) {
                let zoom = 13;
                let currentMarker = null;
                
                // ตรวจว่ารองรับ geolocation หรือไม่
                let latlng = [16.4419355, 102.8359921];
                
                let latlngModel = false;
                let location = '';
                
                if(options['lat-value']!='' && options['lng-value']!=''){
                    latlngModel = [options['lat-value'], options['lng-value']];
                }
                
                if ( navigator.geolocation && !latlngModel) {
                
                    navigator.geolocation.getCurrentPosition(function(position) {
                        location = position.coords;
                        latlng = [location.latitude, location.longitude];
			$('#'+options['lat']).val(location.latitude);
			$('#'+options['lng']).val(location.longitude);
			
			$('#'+options['lat']).trigger('change');
			$('#'+options['lng']).trigger('change');
                        
                    }, function() {
                        $('#'+options['lat']).val(latlng[0]);
                        $('#'+options['lng']).val(latlng[1]);

                        $('#'+options['lat']).trigger('change');
                        $('#'+options['lng']).trigger('change');
                    });//, {timeout:10000}
                } else {
                    if(latlngModel){
                        latlng = latlngModel;
                    }

                    $('#'+options['lat']).val(latlng[0]);
                    $('#'+options['lng']).val(latlng[1]);

                    $('#'+options['lat']).trigger('change');
                    $('#'+options['lng']).trigger('change');
                }
                
                let map = L.map(options['id']).setView(latlng, zoom);
                
                setMarker({lat:latlng[0], lng:latlng[1] });
                
                let tl_grayscale = 'http://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}';
                let attr_grayscale = 'Map data &copy; <a href=\"https://www.openstreetmap.org/\">OpenStreetMap</a> contributors, <a href=\"https://creativecommons.org/licenses/by-sa/2.0/\">CC-BY-SA</a>, Imagery © <a href=\"https://www.mapbox.com/\">Mapbox</a> ';

                let tl_streets = 'https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw';
                let attr_streets = 'Map data &copy; <a href=\"https://www.openstreetmap.org/\">OpenStreetMap</a> contributors, <a href=\"https://creativecommons.org/licenses/by-sa/2.0/\">CC-BY-SA</a>, Imagery © <a href=\"https://www.mapbox.com/\">Mapbox</a> ';

                L.tileLayer(tl_streets, {
                  maxZoom: 18,
                  attribution: attr_streets,
                  id: 'mapbox.streets'
                }).addTo(map);

                let grayscale = L.tileLayer(tl_grayscale, {id: 'mapbox.light', attribution: attr_grayscale});
                let streets   = L.tileLayer(tl_streets, {id: 'mapbox.streets',   attribution: attr_streets});  

                streets.addTo(map);

                let baseLayers = {
                        'Satellite': grayscale,
                        'Map': streets
                };     

                let overlays = {};

                L.control.layers(baseLayers, overlays,
                {
                    collapsed: false,
                    autoZIndex: false
                }).addTo(map);

               location = L.control.locate({
                    strings: {
                        title: 'Your Location'
                    }
                }).addTo(map);

                //let geocoder = L.Control.Geocoder.google('AIzaSyCq1YL-LUao2xYx3joLEoKfEkLXsEVkeuk');
                let geocoder = L.Control.Geocoder.nominatim();
                let control = L.Control.geocoder({
                        geocoder: geocoder,
                        position:'topleft',
                }).addTo(map).on('finishgeocode', function(e) {
                    if (currentMarker) { // check
                        map.removeLayer(currentMarker); // remove
                        currentMarker = null;
                    }
                    $('#'+options['lat']).val(e.sourceTarget._map._lastCenter.lat);
                    $('#'+options['lng']).val(e.sourceTarget._map._lastCenter.lng);   

                    $('#'+options['lat']).trigger('change');
                    $('#'+options['lng']).trigger('change');
                });

                
                
                function setMarker(latlng){
                    currentMarker = L.marker(latlng, {
                        icon: L.AwesomeMarkers.icon({icon: 'star',  prefix: 'fa',markerColor: 'red'}),
                        draggable: true
                    }).addTo(map).on('drag', function (e) {
                        setTimeout(function () {
                            $('#'+options['lat']).val(e.latlng.lat);
                            $('#'+options['lng']).val(e.latlng.lng);   

                            $('#'+options['lat']).trigger('change');
                            $('#'+options['lng']).trigger('change');
                        }, 300);
                    }).on('click', function (e) {
                        setTimeout(function () {
                            $('#'+options['lat']).val(e.latlng.lat);
                            $('#'+options['lng']).val(e.latlng.lng);   

                            $('#'+options['lat']).trigger('change');
                            $('#'+options['lng']).trigger('change');
                        }, 300);
                    });
                }
                
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

                        $('#'+options['lat']).val(event.latlng.lat);
                        $('#'+options['lng']).val(event.latlng.lng);   

                        $('#'+options['lat']).trigger('change');
                        $('#'+options['lng']).trigger('change');
                        return;
                    }

                    setMarker(event.latlng);
                    
                    $('#'+options['lat']).val(event.latlng.lat);
		    $('#'+options['lng']).val(event.latlng.lng);   
		    
		    $('#'+options['lat']).trigger('change');
		    $('#'+options['lng']).trigger('change');
                });
            }
            
	", \yii\web\View::POS_END);
	
	$view->registerJs("
            setTimeout(function () {
                initializeMap($option);
            }, 300);
	    
	
	    $('#{$this->clientOptions['lat']}').change(function(){
		var lat = '{$this->clientOptions['lat-attr']}';
		if(lat!=''){
		    $('#'+lat).val($(this).val());
		}    
	    }); 
	    
	    $('#{$this->clientOptions['lng']}').change(function(){
		var lng = '{$this->clientOptions['lng-attr']}';
		if(lng!=''){
		    $('#'+lng).val($(this).val());
		}
	    }); 
	    
	", \yii\web\View::POS_READY);
    }

}
