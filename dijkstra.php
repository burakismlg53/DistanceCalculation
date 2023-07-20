
<?php
error_reporting(0);
ini_set('display_errors', 0);
// Veritabanı bağlantısı yapılır
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dijkstra";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Veritabanı bağlantısı başarısız oldu: " . $conn->connect_error);
}

// İstemciden gelen şehir bilgileri alınır
$city1 = $_GET['city1'];
$city2 = $_GET['city2'];

// Dijkstra algoritmasıyla en kısa yol hesaplanır
$yol = dijkstra($city1, $city2, $conn);

// Sonuç istemciye döndürülür
$json_data = json_encode($yol, JSON_UNESCAPED_UNICODE);
header('Content-Type: application/json');
echo $json_data;

// Dijkstra algoritması
function dijkstra($city1, $city2, $conn) {
    // Şehirlerin komşuluk ilişkileri ve mesafeleri veritabanından alınır
    $query = "SELECT city1, city2, mesafe FROM sehir";
    $result = $conn->query($query);

    // Şehirler ve komşuluk ilişkileri bir graf olarak saklanır
    $graf = array();
    while ($row = $result->fetch_assoc()) {
        $sehir1 = $row['city1'];
        $sehir2 = $row['city2'];
        $mesafe = $row['mesafe'];

        if (!isset($graf[$sehir1])) {
            $graf[$sehir1] = array();
        }
        if (!isset($graf[$sehir2])) {
            $graf[$sehir2] = array();
        }

        $graf[$sehir1][$sehir2] = $mesafe;
        $graf[$sehir2][$sehir1] = $mesafe;
    }

    // Dijkstra algoritmasıyla en kısa yol hesaplanır
    $uzakliklar = array();
    $onceki = array();
    $ziyaretEdilenler = array();
    $siradakiSehir = $city1;

    foreach ($graf as $sehir => $mesafeler) {
        $uzakliklar[$sehir] = PHP_INT_MAX;
        $onceki[$sehir] = null;
    }

    $uzakliklar[$city1] = 0;

    while ($siradakiSehir !== null) {
        $minUzaklik = PHP_INT_MAX;
        $minUzaklikSehir = null;

        foreach ($graf[$siradakiSehir] as $komsu => $mesafe) {
            $geciciUzaklik = $uzakliklar[$siradakiSehir] + $mesafe;

            if ($geciciUzaklik < $uzakliklar[$komsu]) {
                $uzakliklar[$komsu] = $geciciUzaklik;
                $onceki[$komsu] = $siradakiSehir;
            }

            if (!in_array($komsu, $ziyaretEdilenler) && $uzakliklar[$komsu] < $minUzaklik) {
                $minUzaklik = $uzakliklar[$komsu];
                $minUzaklikSehir = $komsu;
            }
        }

        $ziyaretEdilenler[] = $siradakiSehir;

        // En yakın şehri bulma kontrolü
        $siradakiSehir = null;
        $minUzaklik = PHP_INT_MAX;
        foreach ($graf as $sehir => $mesafeler) {
            if (!in_array($sehir, $ziyaretEdilenler) && $uzakliklar[$sehir] < $minUzaklik) {
                $minUzaklik = $uzakliklar[$sehir];
                $siradakiSehir = $sehir;
            }
        }
    }
    //Yeni Kod
    // En kısa yolun şehir listesi ve mesafe bilgisi oluşturulur
    $yol = array();
    $gezilenSehir = $city2;
    $toplamMesafe = 0;
    while ($gezilenSehir !== $city1) {
        $yol[] = $gezilenSehir;
        $oncekiSehir = $onceki[$gezilenSehir];
        $mesafe = $graf[$oncekiSehir][$gezilenSehir];
        $toplamMesafe += $mesafe;
        $gezilenSehir = $oncekiSehir;
    }
    $yol[] = $city1;
    $yol = array_reverse($yol);

    return array(
        'yol' => $yol,
        'mesafe' => $toplamMesafe
    );
}

$conn->close();
?>
