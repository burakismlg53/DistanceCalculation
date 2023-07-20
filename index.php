<!DOCTYPE html>
<html>
<head>
<meta name="author">
    <title>Şehir Uzaklık Hesaplama</title>
    <style>
        #map {
            height: 100vh;
            width: 100%;
            position: absolute;
            z-index: 0;
        }
        body{
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }
        .input-container {
            position: fixed;
            z-index: 1;
            top: 20px;
            left: 20px;
            background-color: #FFFFFF;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 16px 0 rgba(0, 0, 0, 0.2);
            max-width: 350px;
            width: auto;
            height: auto;
        }
        .bd {
            width: 100%;
            padding: 10px;
            border: none;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
            margin-bottom: 10px;
            border-radius: 5px;
            font-size: 16px;
        }
        .city {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        .button{
            text-decoration: none;
            font-size: 16px;
            color: #fff;
            background-color: #007BFF;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }
        select {
            width: 100%;
            padding:10px;
            border: none;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
            border-radius: 5px;
            font-size: 16px;
        }
        h1 {
            color: #333;
            font-size:22px;
            margin-bottom: 20px;
            text-align: center;
        }
        #result {
            width: 100%;
            padding: 10px;
            margin-top: 20px;
            text-align: center;
            color: #333;
            font-weight: bold;
            border: 1px solid #ddd;
            border-radius: 5px;
            background: #f3f3f3;
        }
    </style>
</head>
<body>
<div class="input-container">
    <h1>Şehirler Arası Uzaklık Hesaplama</h1>
    <label class ="city" for="city1">Şehir 1:</label>
    <input class ="bd" type="text" id="city1" name="city1" required>

    <label class ="city" for="city2">Şehir 2:</label>
    <input class ="bd"type="text" id="city2" name="city2" required>

    <label class ="city" for="method">Hesaplama Yöntemi:</label>
    <select id="method">
        <option value="direct">Kuş Uçuşu Mesafe</option>
        <option value="shortest">En Kısa Yol Dijkstra İle </option>
    </select>

    <button class="button" id="calculateBtn">Hesapla</button>

    <div id="result"></div>
</div>
    <div id="map"></div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script> <!--DOM ve AJAX işlemleri -->
    <script>
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
            script.src = 'https://maps.googleapis.com/maps/api/js?key='YOUR_API_KEY'&callback=initMap'; // Harita kütüphanesi
            script.src = 'https://maps.googleapis.com/maps/api/js?key='YOUR_API_KEY'&callback=initMap&libraries=geometry'; // Harita üzerinde çizgi çekme kütüphanesi
            document.body.appendChild(script); // Buradan sonra callback ile initmap() fonksiyonu çağrılır.
        }
        // İlk burası çalışır.
        $(document).ready(function () { // Sayfanın tam yüklenmesini bekler ve loadMapScript fonksiyonunu çağırır.
            loadMapScript();
        });



    </script>
</body>
</html>
