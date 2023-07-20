<?php
// Veritabanı bağlantısı
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Sehirler";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Veritabanı bağlantısı başarısız: " . $conn->connect_error);
}
//Veritabanı sorunsuz bağlanıyor...

// İki şehir arasındaki uzaklığı hesaplayan fonksiyon
function calculateDistance($city1, $city2) {
    // İki şehirin enlem ve boylam bilgilerini veritabanından alın
    global $conn;
    $sql = "SELECT * FROM sehir WHERE İsim = '$city1' OR İsim = '$city2'"; 
    
    $result = $conn->query($sql);
    $city1Coords = null;
    $city2Coords = null;

    if ($result->num_rows == 2) {
        while ($row = $result->fetch_assoc()) {
            if ($row['İsim'] == $city1) {
                $city1Coords = array('lat' => $row['Enlem'], 'lng' => $row['Boylam']);
            } elseif ($row['İsim'] == $city2) {
                $city2Coords = array('lat' => $row['Enlem'], 'lng' => $row['Boylam']);
            }
        }
    }

    // İki şehirin enlem ve boylam bilgileri bulunamazsa hata döndür
    if (!$city1Coords || !$city2Coords) {
        return 'Hatalı şehir bilgisi.';
    }

    // Uzaklık hesaplama işlemlerini gerçekleştir (Kuş uçuşu hesaplaması)
    $lat1 = deg2rad($city1Coords['lat']);
    $lon1 = deg2rad($city1Coords['lng']);
    $lat2 = deg2rad($city2Coords['lat']);
    $lon2 = deg2rad($city2Coords['lng']);

    $earthRadius = 6371; // Dünya yarıçapı (km)

    $deltaLat = $lat2 - $lat1;
    $deltaLon = $lon2 - $lon1;

    $angle = 2 * asin(sqrt(pow(sin($deltaLat / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($deltaLon / 2), 2)));

    $distance = $angle * $earthRadius;

    return $distance;
}

// İstekleri işle... İlk burada çalışmaya başlar. Sunucuya gelen metinden istenilen değişkenler alınıyor.
if (isset($_GET['city1']) && isset($_GET['city2'])) {
    
    $city1 = $_GET['city1'];
    $city2 = $_GET['city2'];

    $distance =calculateDistance($city1, $city2);
     
    echo $distance;
}

$conn->close();
?>