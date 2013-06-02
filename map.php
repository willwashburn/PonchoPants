<?php

include('connection.php');

$stations = array();
$acc = "select * from stations;";
$acc_res = mysql_query($acc,$conn) or die(mysql_error());
while ($a = mysql_fetch_array($acc_res)) {
	$id = $a['id'];
	
	$stations["$id"] = array();
	$stations["$id"]['id'] = $id;
	$stations["$id"]['name'] = str_replace("'","\'", $a['stationName']);
	$stations["$id"]['lat'] = $a['latitude'];
	$stations["$id"]['long'] = $a['longitude'];
	$stations["$id"]['stAddress1'] = str_replace("'","\'", $a['stAddress1']);
	
}

$stationVars = array();
$stationMarkers = array();
$stationSets = array();
foreach($stations as $s){
	
	$this_station_var = "
			var station".$s['id']." = new google.maps.LatLng(".$s['lat'].",".$s['long'].");
			";
			
	$this_station_marker = "
			var marker".$s['id']." = new google.maps.Marker({
			    position: station".$s['id'].",
			    title:'".$s['name']."'
			});";	
			
	$this_station_set = "
			marker".$s['id'].".setMap(map);
			";
			
	array_push($stationVars, $this_station_var);
	array_push($stationMarkers, $this_station_marker);
	array_push($stationSets, $this_station_set);		
}

?>

<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
    <link rel="stylesheet" href="style.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="bootstrap/css/bootstrap.css" type="text/css" media="screen" />
    <style type="text/css">
      html { height: 100% }
      body { height: 100%; margin: 0; padding: 0 }
      #map-canvas { height: 100% }
    </style>
    <script type="text/javascript"
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA2hnhXoVCy4mdtPdwT2rpbqOZBzmkkZMM&sensor=true">
    </script>
    <script type="text/javascript">
      function initialize() {
      	
      	<?php
      		foreach($stationVars as $v){
      			echo $v;
      		}
      	?>	
      	
        var mapOptions = {
          center: new google.maps.LatLng(40.749598,-73.960133),
          zoom: 13,
	        panControl: true,
	        zoomControl: true,
	        mapTypeControl: true,
	        scaleControl: true,
	        streetViewControl: true,
	        overviewMapControl: true,
          mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        var map = new google.maps.Map(document.getElementById("map-canvas"),
            mapOptions);
        
        <?php
        	foreach($stationMarkers as $v){
        		echo $v;
        	}
        ?>	
        
        // To add the marker to the map, call setMap();
        <?php
	       	foreach($stationSets as $v){
	       		echo $v;
	       	}
        ?>	   
            
      }
      google.maps.event.addDomListener(window, 'load', initialize);
    </script>
  </head>
  <body>
  	<div class='header'>
  		<div class="navbar nav-fixed-top">
  		  <div class="navbar-inner">
  		    <a class="brand" href="#">PonchoPants</a>
  		    <ul class="nav">
  		      <li class="active"><a href="#">Home</a></li>
  		      <li><a href="#">Link</a></li>
  		      <li><a href="#">Link</a></li>
  		    </ul>
  		  </div>
  		</div>
  		<div class="navbar navbar-inverse nav-fixed-top nav-fixed-under">
  		  <div class="navbar-inner">
  		    <ul class="nav">
  		      <li class="active"><a href="#">Home</a></li>
  		      <li><a href="#">Link</a></li>
  		      <li><a href="#">Link</a></li>
  		    </ul>
  		  </div>
  		</div>	
  	</div>
  	<div class='map-wrapper'>
    	<div id="map-canvas"/>
    </div>	
  </body>
</html>