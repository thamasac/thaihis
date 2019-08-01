<?php
use appxq\sdii\helpers\SDNoty;

?>

<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js"></script>
<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=AIzaSyDsVkCe_UXKDMt1uyS65gqLKA0IsjziWz0"></script>
<div class="col-md-12">
    <div id="map" style="background: #f5f5f5; height:500px; "></div>
</div>
<div class="col-sm-3 sdbox-col">
    <div class="form-group">
        <label>พิกัด</label>
        <input type="text" id="w4-lat" class="form-control" name="w4-lat" value="">
    </div>
</div>
<?php
$this->registerJS("
    var result = {
    message:'5555',
    status:'success'
    };
    ". SDNoty::show('result.message', 'result.status') ."
    
");
?>
<script type="text/javascript">
//   SDNoty::show('result.message', 'message.status') ;
   
    var locations = [
      ['Bondi Beach', -33.890542, 151.274856],
      ['Coogee Beach', -33.923036, 151.259052],
      ['Cronulla Beach', -34.028249, 151.157507],
      ['Manly Beach', -33.80010128657071, 151.28747820854187],
      ['Maroubra Beach', -33.950198, 151.259302]
    ];
    var map = new google.maps.Map(document.getElementById('map'), {
      zoom: 7,
      center: new google.maps.LatLng(-33.92, 151.25),
      mapTypeId: google.maps.MapTypeId.ROADMAP
    });
    var infowindow = new google.maps.InfoWindow();
    var marker, i;
    for (i = 0; i < locations.length; i++) { 
      marker = new google.maps.Marker({
        position: new google.maps.LatLng(locations[i][1], locations[i][2]),
        map: map
      });
      google.maps.event.addListener(marker, 'click', (function(marker, i) {
        return function() {
          infowindow.setContent(locations[i][0]);
          infowindow.open(map, marker);
        }
      })(marker, i));
    }
  </script>
