var g_style = [{"featureType": "landscape","stylers": [{"saturation": -100},{"lightness": 65},{"visibility": "on"}]},{"featureType": "poi","stylers": [{"saturation": -100},{"lightness": 51},{"visibility": "simplified"}]},{"featureType": "road.highway","stylers": [{"saturation": -100},{"visibility": "simplified"}]},{"featureType": "road.arterial","stylers": [{"saturation": -100},{"lightness": 30},{"visibility": "on"}]},{"featureType": "road.local","stylers": [{"saturation": -100},{"lightness": 40},{"visibility": "on"}]},{"featureType": "transit","stylers": [{"saturation": -100},{"visibility": "simplified"}]},{"featureType": "administrative.province","stylers": [{"visibility": "off"}]},{"featureType": "water","elementType": "labels","stylers": [{"visibility": "on"},{"lightness": -25},{"saturation": -100}]},{"featureType": "water","elementType": "geometry","stylers": [{"hue": "#ffff00"},{"lightness": -25},{"saturation": -97}]}]

var device = 'desktop';

/**
 * google map
 */
function initGoogleMap(elementId, style, positionX, positionY, title_) {
    "use strict";
    var myOptions = {
        zoom: 12,
        center: new google.maps.LatLng(positionX, positionY),
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        styles: style,
        disableDefaultUI: true
    };
    var map = new google.maps.Map(document.getElementById(elementId), myOptions);
    var marker_pos = new google.maps.LatLng(positionX, positionY);
    var marker = new google.maps.Marker({
        position: marker_pos,
        map: map,
        title: title_,
        icon: 'path/to/icon/img/googlemapsIcon.png'
    });
    return;
}

jQuery( document ).ready(function($) {
	detectDevice();
	$( window ).resize(function(){ detectDevice(); });

	/* basic detect device */
	function detectDevice(){
        'use strict';
        if(window.innerWidth >= 980){
			device = 'desktop';
		}
		else if(window.innerWidth >= 768 && window.innerWidth < 980){
			device = 'tablet';
		}
		else if(window.innerWidth < 768){
			device = 'mobile';
		}
    }
});