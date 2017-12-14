<?php
	require "get_request.php";
    
    $json = GET("https://api.foursquare.com/v2/venues/search?v=20161016&near=Bergamo&query=pizza&intent=checkin&limit=50&client_id=4FPUXDWGSST25LCPB5CCFJWCCFYYCOAGOQCHGD43MTAP1DBV&client_secret=1DPIKHBYXLUFIJHBJ2S3APZ5NF1QY42XO3MTZARD5R5XT34M");
   	 
    $resp_array = json_decode($json, true);
?>
<!DOCTYPE html>
<html>
	<head>
    	<title>Pizzerie Bergamo</title>
        <link rel="stylesheet" type="text/css" href="http://maestronim.altervista.org/Pizzerie/style12.css">
        <meta charset="utf-8">
  		<meta name="viewport" content="width=device-width, initial-scale=1">
  		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    </head>
    <body>
    	<div class="container">
        	<div class="mapcontainer">
           		<div id="map"></div>
            </div>
        	<?php
                $venues = $resp_array["response"]["venues"];
                for($i=0;$i<count($venues);$i++)
                {
                  	echo "<div id=\"" . $venues[$i]["id"] . "\" class=\"venuecontainer\" onclick=\"divClick(this.id)\">";
                    	echo "<div class=\"index\">" . ($i+1) . "</div>";
                    	echo "<div class=\"venuemeta\">";
                    		echo "<div class=\"venuenameaddress\">";
                  				echo "<div class=\"venuename\">" . $venues[$i]["name"] . "</div>";
                  				echo "<div class=\"venueaddress\">" . $venues[$i]["location"]["address"] . "</div>";
                    		echo "</div>";
                            echo "<div class=\"venuecheckins\">Visite registrate: " . $venues[$i]["stats"]["checkinsCount"] . "</div>";
                            echo "<hr class=\"myhr\">";
                  		echo "</div>";
                    echo "</div>";
                    $coords[] = array(
                    				"lat" => $venues[$i]["location"]["lat"],
                                    "lng" => $venues[$i]["location"]["lng"]
                    );
                    $IDs[] = $venues[$i]["id"];
          			$names[] = $venues[$i]["name"];
                    $venues_info [] = array(
                    					"id" => $venues[$i]["id"],
                                    	"name" => $venues[$i]["name"],
                                    	"coords" => array(
                                        					"lat" => $venues[$i]["location"]["lat"],
                                                            "lng" => $venues[$i]["location"]["lng"]
                                         )
                   	);
                                    				
                }
            ?>
		</div>
        <script>
        	<?php
            	echo "var venues_info = " . json_encode($venues_info) . ";";
            ?>
            
            var markers = [];
            var MAP;
            var precInfoWindow = null;
            var precMarker = null;
            
			function myMap() {
				var mapProp = {
                	center: new google.maps.LatLng(45.6947359, 9.6687071),
                	zoom: 10
            	};
            	MAP = new google.maps.Map(document.getElementById("map"), mapProp);
                for (i = 0; i < venues_info.length; i++) {
                	placeMarker(new google.maps.LatLng(parseFloat(venues_info[i]["coords"]["lat"]), parseFloat(venues_info[i]["coords"]["lng"])));
                }
            }
            
            function placeMarker(location) {
  				var marker = new google.maps.Marker({
    				position: location,
                    animation: google.maps.Animation.DROP,
    				map: MAP
  				});
                google.maps.event.addListener(marker,'click',function() {
    				if(precInfoWindow != null) {
                		precInfoWindow.close();
                	}
                
                	if(precMarker != null) {
                		precMarker.setAnimation(null);
                	}
                    
                    for (i = 0; i < venues_info.length; i++) {
                    	if(Number((parseFloat(venues_info[i]["coords"]["lat"])).toFixed(6)) == Number(parseFloat(marker.getPosition().lat()).toFixed(6)) &&
                        Number((parseFloat(venues_info[i]["coords"]["lng"])).toFixed(6)) == Number(parseFloat(marker.getPosition().lng()).toFixed(6))) {
                        	var infowindow = new google.maps.InfoWindow({
                        		content: venues_info[i]["name"] + "<br>Lat: " +
                            	Number(parseFloat(marker.getPosition().lat()).toFixed(6)) + "<br>Lng: " +
                            	Number(parseFloat(marker.getPosition().lng()).toFixed(6))
                      		});
                            marker.setAnimation(google.maps.Animation.BOUNCE);
                            infowindow.open(MAP, marker);
                            var top = document.getElementById(venues_info[i]["id"]).offsetTop;
    						window.scrollTo(0, top);
                            precInfoWindow = infowindow;
                            precMarker = marker;
                            break;
                       	}
                    }
    			});
                markers.push(marker);
            }
            
            function divClick(id) {
            	if(precInfoWindow != null) {
                	precInfoWindow.close();
                }
                
                if(precMarker != null) {
                	precMarker.setAnimation(null);
                }
                
            	for (i = 0; i < venues_info.length; i++) {
                	if(id.localeCompare(venues_info[i]["id"]) == 0) {
                      	var infowindow = new google.maps.InfoWindow({
                        	content: venues_info[i]["name"] + "<br>Lat: " +
                            Number((parseFloat(venues_info[i]["coords"]["lat"])).toFixed(6)) + "<br>Lng: " +
                            Number((parseFloat(venues_info[i]["coords"]["lng"])).toFixed(6))
                      	});
                        markers[i].setAnimation(google.maps.Animation.BOUNCE);
                      	infowindow.open(MAP, markers[i]);
                        precInfoWindow = infowindow;
                        precMarker = markers[i];
                        break;
                    }
                }
            }
        </script>

		<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBGSsq_8J9V1KCMFdGjuszWGe1hEvWkd6Q&callback=myMap"></script>
    </body>
</html>