
window.addEventListener('tac.root_available', function() {
	tarteaucitron.user.mapscallback = 'gmapLoaded';
});

window.gmapLoaded = function(){	
	var maps = document.getElementsByName("gmap");		
	
	for(var i =0; i < maps.length; i++){		
		var myCenter = new google.maps.LatLng(maps[i].dataset.lat, maps[i].dataset.lon);
		var mapProp = {center:myCenter, zoom:13, scrollwheel:false, draggable:true, mapTypeId:google.maps.MapTypeId.ROADMAP};
		var map = new google.maps.Map(maps[i],mapProp);
		var marker = new google.maps.Marker({position:myCenter});
		marker.setMap(map);
	}
}
