<?php

namespace appxq\sdii\assets;

use yii\web\AssetBundle;

class LeafletAsset extends AssetBundle {

	public $sourcePath = '@appxq/sdii/assets/leaflet';
	public $css = [
            //'examples.css',
            'leaflet.css',
            //'leaflet-src.css',
            'leaflet.awesome-markers.css',
            //'leaflet-geocoder-mapzen.css',
            'L.Control.Locate.min.css',
            //'Control.Geocoder.css?2',
            'Control.Geocoder.css',
	];
	public $js = [
            'leaflet.js',
            //'leaflet-src.js',
            'leaflet.awesome-markers.min.js',
            //'leaflet-geocoder-mapzen.min.js',
            'L.Control.Locate.js',
            //'Control.Geocoder.js',
            //'leaflet-hash.min.js',
            'Control.Geocoder.js',
	];
	public $depends = [
	];

}
