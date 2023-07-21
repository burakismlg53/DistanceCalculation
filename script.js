var line; // Çizgiyi saklamak için değişken tanımla
var infoWindow; // InfoWindow saklamak için değişken tanımla

// Harita Yükleme
function initMap() {
            var map = new google.maps.Map(document.getElementById('map'), { 
                center: { lat: 39.925533, lng: 32.866287 },
                zoom: 6
            }); // Burda new google.maps.Map nesnesinden map sınıfını oluşturur ve içine parametreleri atar. Başında var olması değişkenin
                //global değil de yerel scope ta çalışmasını sağlar. Eğer hiçbir şey olmasaydı globalde çalışırdı.

        directionsRenderer = new google.maps.DirectionsRenderer({
        map: null, // Başlangıçta çizgiyi görüntüleme
        suppressMarkers: true,
        polylineOptions: {
            strokeColor: "#FF0000",
            strokeOpacity: 1.0,
            strokeWeight: 2
        }
        });

        // Uzaklık çizgisini çizme
        function drawDirectDistanceLine(map, latLng1, latLng2, distance) {
            if (line) {
                line.setMap(null); // Önceki çizgiyi kaldır
            }
            if (infoWindow) {
                infoWindow.close(); // Önceki info window'u kapat
            }
                    line = new google.maps.Polyline({
                    path: [latLng1, latLng2],
                    geodesic: true,
                    strokeColor: '#FF0000',
                    strokeOpacity: 1.0,
                    strokeWeight: 2
                });

                line.setMap(map);

                var midLatLng = google.maps.geometry.spherical.interpolate(latLng1, latLng2, 0.5);

                infoWindow = new google.maps.InfoWindow({
                    content: 'Uzaklık: ' + distance + ' km'
                });

                infoWindow.setPosition(midLatLng);
                infoWindow.open(map);
            }

            function clearDirectDistanceLine() {
                if (line) {
                    line.setMap(null); // Önceki çizgiyi kaldır
                }
                if (infoWindow) {
                    infoWindow.close(); // Önceki info window'u kapat
                }
            }

$('#calculateBtn').click(function () {
    var method = $('#method').val();
    // Kuş uçuşu mesafe hesaplaması yap
    if (method === 'direct') {
        
        var city1 = $('#city1').val();
                var city2 = $('#city2').val();

                var geocoder = new google.maps.Geocoder();
                var latLng1, latLng2;

                // İlk şehirin geocode isteği
                geocoder.geocode({ address: city1 }, function (results1, status1) {
                    if (status1 === 'OK') {
                        latLng1 = results1[0].geometry.location;

                        // İkinci şehirin geocode isteği
                        geocoder.geocode({ address: city2 }, function (results2, status2) {
                            if (status2 === 'OK') {
                                latLng2 = results2[0].geometry.location;

                                // Sunucuya AJAX isteği gönder
                                var xhr = new XMLHttpRequest();
                                var url = "airdistance.php";
                                var params = 'city1=' + encodeURIComponent(city1) + '&city2=' + encodeURIComponent(city2);

                                xhr.onreadystatechange = function () {
                                    if (xhr.readyState == 4) {
                                        if (xhr.status == 200) {
                                            // İstek tamamlandığında ve yanıt 200 (Başarılı) olduğunda yapılacak işlemler
                                            var response = xhr.responseText;
                                            document.getElementById('result').innerHTML = 'Uzaklık: ' + parseInt(response) + ' km';

                                            var distance = parseFloat(response);
                                            var map = new google.maps.Map(document.getElementById("map"), {
                                            center: { lat: 39.925533, lng: 32.866287 },
                                            zoom: 6
                        });
                                            drawDirectDistanceLine(map, latLng1, latLng2, distance);
                                        } else {
                                            // İstek başarısız oldu
                                            alert('Uzaklık hesaplanırken bir hata oluştu.');
                                        }
                                    }
                                };

                                xhr.open('GET', url + '?' + params, true);
                                xhr.send();
                            } else {
                                alert('Geocode hatası: ' + status2);
                            }
                        });
                    } else {
                        alert('Geocode hatası: ' + status1);
                    }
                });

         // En kısa yol hesaplaması yap
    } 
    else if (method === 'shortest') {
        clearDirectDistanceLine();

        var city1 = document.getElementById("city1").value;
            var city2 = document.getElementById("city2").value;
            var url = "dijkstra.php?city1=" + city1 + "&city2=" + city2;
            var xhr = new XMLHttpRequest();
            xhr.open("GET", url, true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    try {
                        var response = JSON.parse(xhr.responseText);
                        var distance = response.mesafe;
                        var path = response.yol;

                        document.getElementById("result").innerHTML = "En kısa yol: " + path.join(" > ") + "<br><br>Mesafe: " + distance + " km";

                        // Harita alanına ekleme
                        var map = new google.maps.Map(document.getElementById("map"), {
                            center: { lat: 39.925533, lng: 32.866287 },
                            zoom: 6
                        });

                        // Şehirler arasındaki çizgi çizme
var geocoder = new google.maps.Geocoder();
var directionsService = new google.maps.DirectionsService();
var directionsRenderer = new google.maps.DirectionsRenderer({
    map: map,
    suppressMarkers: true,
    polylineOptions: {
        strokeColor: "#FF0000",
        strokeOpacity: 1.0,
        strokeWeight: 2
    }
});

var waypoints = [];
for (var i = 1; i < path.length - 1; i++) {
    waypoints.push({ location: path[i], stopover: true });
}

var request = {
    origin: path[0],
    destination: path[path.length - 1],
    waypoints: waypoints,
    optimizeWaypoints: true,
    travelMode: google.maps.TravelMode.DRIVING
};

directionsService.route(request, function(result, status) {
    if (status === google.maps.DirectionsStatus.OK) {
        directionsRenderer.setDirections(result);
    }
});

                    } catch (error) {
                        console.log(error);
                        console.log("Gelen yanıt:", xhr.responseText);
                        document.getElementById("result").innerHTML = "Bir hata meydana geldi.";
                    }
                }
            };
            xhr.send();
        
    }
});
            // Şehirleri Al
            
        }



        // Api Yükleme
        function loadMapScript() {
            var script = document.createElement('script');
            script.src = 'https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&callback=initMap'; // Harita kütüphanesi
            script.src = 'https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&callback=initMap&libraries=geometry'; // Harita üzerinde çizgi çekme kütüphanesi
            document.body.appendChild(script); // Buradan sonra callback ile initmap() fonksiyonu çağrılır.
        }
        // İlk burası çalışır.
        $(document).ready(function () { // Sayfanın tam yüklenmesini bekler ve loadMapScript fonksiyonunu çağırır.
            loadMapScript();
});



