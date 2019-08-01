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
class MapView extends BaseWidget {

    public $key = 'AIzaSyDenMKbzFR4nJSmiWg4j7wrglPjupwsP0I';
    public $sensor = 'false';
    public $url = '';
    public function init() {
	parent::init();
	
	$this->clientOptions['id'] = $this->options['id'].'-map';
	$this->clientOptions['search'] = $this->options['id'].'-search';
	$this->clientOptions['btn-search'] = $this->options['id'].'-btn-search';
	$this->clientOptions['url'] = $this->url;
	//$this->options['style'] = 'width: 100%; height: 350px;';
	
	echo Html::beginTag('div', $this->options) . "\n";
    }

    /**
     * Renders the widget.
     */
    public function run() {
	echo Html::beginTag('div', ['class'=>'row']) . "\n";
	echo Html::beginTag('div', ['class'=>'col-sm-9']) . "\n";
	
	echo Html::beginTag('div', ['id'=>$this->clientOptions['id'], 'style'=>'width: 100%; height: 550px;']) . "\n";
	
	echo "\n" . Html::endTag('div');
	
	echo "\n" . Html::endTag('div');
	echo Html::beginTag('div', ['class'=>'col-sm-3 sdbox-col']) . "\n";
	
	echo Html::beginTag('div', ['class'=>'form-group']) . "\n";
	echo Html::label('ค้นหา');
	echo Html::textInput($this->clientOptions['search'], '', ['class'=>'form-control', 'id'=>$this->clientOptions['search']]);
	echo "\n" . Html::endTag('div');
	
	echo Html::beginTag('div', ['class'=>'form-group']) . "\n";
	echo Html::button('ค้นหา', ['class'=>'btn btn-block btn-success', 'id'=>$this->clientOptions['btn-search']]);
	echo "\n" . Html::endTag('div');
	
	echo "\n" . Html::endTag('div');
	echo "\n" . Html::endTag('div');
	echo "\n" . Html::endTag('div');
	
	
	
	$view = $this->getView();
	
//	$op['sensor'] = $this->sensor;
//	if($this->key!=''){
//	    $op['key'] = $this->key;
//	}
	$op['language'] = 'th';
	
	$q = array_filter($op);
	
	$view->registerJsFile('https://maps.google.com/maps/api/js?'.http_build_query($q), [
	    'position'=>\yii\web\View::POS_HEAD,
	    'depends'=>'yii\web\YiiAsset',
	]);
	
	$option = \yii\helpers\Json::encode($this->clientOptions);
	
	//\appxq\sdii\assets\map\MapAsset::register($view);
	
	$view->registerJs("
	    var initialLocation;
	    var geocoder;
	    var map;
	    var latlng = new google.maps.LatLng(18.356901, 103.651744);
	    var searchPlace;
	    // The web service URL from Drive 'Deploy as web app' dialog.
	    var DATA_SERVICE_URL = 'https://script.google.com/macros/s/AKfycbziLo2rdoGwXRCLamzwABGn9L11tI-ImIe_bkI_Ge8rON3slf8/exec';

	    function initialize(options) {
		geocoder = new google.maps.Geocoder();
		
		var mapOptions = {
                    center: new google.maps.LatLng(0, 0),
		    zoom: 2,
		    maxZoom: 20,
                    mapTypeControl: true,
                    navigationControlOptions: {style: google.maps.NavigationControlStyle.SMALL},
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                };
		map = new google.maps.Map(document.getElementById(options['id']), mapOptions);
		
		// ตรวจว่ารองรับ geolocation หรือไม่
//                if ( navigator.geolocation ) {
//                    navigator.geolocation.getCurrentPosition(function(location) {
//                        var location = location.coords;
//			
//                        initialLocation = new google.maps.LatLng(location.latitude, location.longitude);// ระบุตำแหน่งแสดงแผนที่ให้ google map
//                        map.setCenter(initialLocation);// ให้ตำแหน่งแสดงแผนที่อยู่ตรงกลางพอดี
//                        
//                    }, function() {
//                        handleNoGeolocation();// ตรวจตำแหน่ง lat/lng ไม่ได้ ให้ใช้ค่าเริ่มต้น
//                    });
//                } else {
//                    handleNoGeolocation();// ตรวจตำแหน่ง lat/lng ไม่ได้ ให้ใช้ค่าเริ่มต้น
//                }
		
		// no geolocation ฟังก์ชั่นนี้จะถูกเรียกใช้งานเมื่อตรวจค่า lat/lng ไม่ได้
                function handleNoGeolocation() {
                    map.setCenter(latlng);
                    
                }
		
		// set marker
                function setMarker(initialName, image, info) {
                    var marker = new google.maps.Marker({
                        position: initialName,
                        map: map,
			animation: google.maps.Animation.DROP,
			icon: image,
			info:info,
                        title: 'คุณอยู่ที่นี่'
                    });
                    
                    google.maps.event.addListener(marker, 'click', function(event) {
			marker.info.open(map, marker);
                    });
                }
		
		$.ajax({
		    url: DATA_SERVICE_URL,//encodeURI(options['url']),
		    //data: data,
		    type: 'GET',
		    dataType:'json',
		    success: function( data ) {
			console.log(data);
			var locations = data;//.data;
			    
			for(var i=0;i<locations.length;i++)
			{
			     var newPoint = new google.maps.LatLng(locations[i][3], locations[i][2]);
			     var info = new google.maps.InfoWindow({
				content: '<h4 id=\"overview\" class=\"page-header\" style=\"margin-top: 5px; \">' + locations[i].title + '</h4>' + locations[i].content
			      });
			     setMarker(newPoint, locations[i].icon, info);
			}
			if(data.status=='success')
			{
			    var locations = data.data;
			    
			    for(var i=0;i<locations.length;i++)
			    {
				 var newPoint = new google.maps.LatLng(locations[i].lat, locations[i].lng);
				 var info = new google.maps.InfoWindow({
				    content: '<h4 id=\"overview\" class=\"page-header\" style=\"margin-top: 5px; \">' + locations[i].title + '</h4>' + locations[i].content
				  });
				 setMarker(newPoint, locations[i].icon, info);
			    }
			}
		    }
		});
		
		// ส่วนของฟังก์ชันค้นหาชื่อสถานที่ในแผนที่
		searchPlace = function() {
		    var AddressSearch=$('#'+options['search']).val();
		    if(geocoder){ // ตรวจสอบว่าถ้ามี Geocoder Object   
			geocoder.geocode({  
			     address: AddressSearch // ให้ส่งชื่อสถานที่ไปค้นหา  
			},function(results, status){ // ส่งกลับการค้นหาเป็นผลลัพธ์ และสถานะ  
			    if(status == google.maps.GeocoderStatus.OK) { // ตรวจสอบสถานะ ถ้าหากเจอ  
				var my_Point=results[0].geometry.location; // เอาผลลัพธ์ของพิกัด มาเก็บไว้ที่ตัวแปร  
				map.setCenter(my_Point); // กำหนดจุดกลางของแผนที่ไปที่ พิกัดผลลัพธ์  
				return false;
			    }else{  
				// ค้นหาไม่พบแสดงข้อความแจ้ง  
				alert('Geocode was not successful for the following reason: ' + status);  
				$('#'+options['search']).val('');
			     }  
			});  
		    }        
		}
		
	    }
	", \yii\web\View::POS_END);
	
	$view->registerJs("
	    initialize($option);
	
	    $('#{$this->clientOptions['btn-search']}').click(function(){ // เมื่อคลิกที่ปุ่ม id=SearchPlace ให้ทำงานฟังก์ฃันค้นหาสถานที่  
		searchPlace();  // ฟังก์ฃันค้นหาสถานที่  
	    }); 
	    
	    $('#{$this->clientOptions['search']}').keyup(function(event){ // เมื่อพิมพ์คำค้นหาในกล่องค้นหา  
		if(event.keyCode==13){  //  ตรวจสอบปุ่มถ้ากด ถ้าเป็นปุ่ม Enter ให้เรียกฟังก์ชันค้นหาสถานที่  
		    searchPlace();      // ฟังก์ฃันค้นหาสถานที่  
		    
		}         
	    });  
	", \yii\web\View::POS_READY);
    }

}
