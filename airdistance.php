<?php
// Veritabanı bağlantısı
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sehirler";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Veritabanı bağlantısı başarısız: " . $conn->connect_error);
}
//Veritabanı sorunsuz bağlanıyor...
// Şehir tablosunu oluşturma
$sql = "CREATE TABLE sehir (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    İsim VARCHAR(50) NOT NULL,
    Enlem Int(50) NOT NULL,
    Boylam Int(50) NOT NULL,
    Plaka INT(11) NOT NULL
)";

if ($conn->query($sql) === TRUE) {
    echo "Veritabanı tablosu başarıyla oluşturuldu.";
} 

// Veritabanındaki kayıt sayısını kontrol et
$sehirQuery = "SELECT COUNT(*) as count FROM sehir";
$result = $conn->query($sehirQuery);
$row = $result->fetch_assoc();
$recordCount = $row['count'];

// Eğer veritabanı boşsa verileri ekle
if ($recordCount === "0") {
    
// Şehir verilerini eklemek için bir dizi oluşturun
$sehirler = 
    [  ["Adana", 37.0000, 35.3213, 1],
  ["Adıyaman", 37.7648, 38.2766, 2],
  ["Afyonkarahisar", 38.7507, 30.5567, 3],
  ["Ağrı", 39.7217, 43.0567, 4],
  ["Amasya", 40.6499, 35.8353, 5],
  ["Ankara", 39.9208, 32.8541, 6],
  ["Antalya", 36.8841, 30.7056, 7],
  ["Artvin", 41.1828, 41.8193, 8],
  ["Aydın", 37.8560, 27.8416, 9],
  ["Balıkesir", 39.6484, 27.8826, 10],
  ["Bilecik", 40.0567, 30.0665, 11],
  ["Bingöl", 39.0626, 40.7696, 12],
  ["Bitlis", 38.3938, 42.1232, 13],
  ["Bolu", 40.5760, 31.5788, 14],
  ["Burdur", 37.4613, 30.0665, 15],
  ["Bursa", 40.2669, 29.0634, 16],
  ["Çanakkale", 40.1553, 26.4142, 17],
  ["Çankırı", 40.6013, 33.6134, 18],
  ["Çorum", 40.5506, 34.9556, 19],
  ["Denizli", 37.7765, 29.0864, 20],
  ["Diyarbakır", 37.9144, 40.2306, 21],
  ["Edirne", 41.6818, 26.5623, 22],
  ["Elazığ", 38.6810, 39.2264, 23],
  ["Erzincan", 39.7500, 39.5000, 24],
  ["Erzurum", 39.9000, 41.2700, 25],
  ["Eskişehir", 39.7667, 30.5250, 26],
  ["Gaziantep", 37.0662, 37.3833, 27],
  ["Giresun", 40.9128, 38.3895, 28],
  ["Gümüşhane", 40.4605, 39.4828, 29],
  ["Hakkâri", 37.5833, 43.7333, 30],
  ["Hatay", 36.4018, 36.3498, 31],
  ["Isparta", 37.7648, 30.5566, 32],
  ["Mersin", 36.8000, 34.6333, 33],
  ["İstanbul", 41.0082, 28.9784, 34],
  ["İzmir", 38.4192, 27.1287, 35],
  ["Kars", 40.6065, 43.0975, 36],
  ["Kastamonu", 41.3887, 33.7827, 37],
  ["Kayseri", 38.7312, 35.4787, 38],
  ["Kırklareli", 41.7333, 27.2167, 39],
  ["Kırşehir", 39.1425, 34.1709, 40],
  ["Kocaeli", 40.8533, 29.8815, 41],
  ["Konya", 37.8775, 32.4849, 42],
  ["Kütahya", 39.4167, 29.9833, 43],
  ["Malatya", 38.3552, 38.3095, 44],
  ["Manisa", 38.6191, 27.4289, 45],
  ["Kahramanmaraş", 37.5858, 36.9371, 46],
  ["Mardin", 37.3124, 40.7351, 47],
  ["Muğla", 37.2153, 28.3636, 48],
  ["Muş", 38.9462, 41.7539, 49],
  ["Nevşehir", 38.6939, 34.6857, 50],
  ["Niğde", 37.9692, 34.6766, 51],
  ["Ordu", 40.9839, 37.8764, 52],
  ["Rize", 41.0201, 40.5234, 53],
  ["Sakarya", 40.6940, 30.4358, 54],
  ["Samsun", 41.2928, 36.3313, 55],
  ["Siirt", 37.9333, 41.9500, 56],
  ["Sinop", 42.0231, 35.1531, 57],
  ["Sivas", 39.7477, 37.0179, 58],
  ["Tekirdağ", 40.9839, 27.5156, 59],
  ["Tokat", 40.3139, 36.5544, 60],
  ["Trabzon", 41.0000, 39.7333, 61],
  ["Tunceli", 39.3074, 39.4388, 62],
  ["Şanlıurfa", 37.1591, 38.7969, 63],
  ["Uşak", 38.6823, 29.4082, 64],
  ["Van", 38.4891, 43.4089, 65],
  ["Yozgat", 39.8171, 34.8147, 66],
  ["Zonguldak", 41.4564, 31.7987, 67],
  ["Aksaray", 38.3686, 34.0370, 68],
  ["Bayburt", 40.2550, 40.2249, 69],
  ["Karaman", 37.1759, 33.2287, 70],
  ["Kırıkkale", 39.8454, 33.5063, 71],
  ["Batman", 37.8812, 41.1351, 72],
  ["Şırnak", 37.4183, 42.4918, 73],
  ["Bartın", 41.5811, 32.4610, 74],
  ["Ardahan", 41.1105, 42.7022, 75],
  ["Iğdır", 39.8880, 44.0048, 76],
  ["Yalova", 40.6500, 29.2606, 77],
  ["Karabük", 41.2061, 32.6204, 78],
  ["Kilis", 36.7184, 37.1212, 79],
  ["Osmaniye", 37.0742, 36.2477, 80],
  ["Düzce", 40.8438, 31.1565, 81],
];


// Bayrak kontrolü için bir değişken tanımlayın
$veriEklendi = false;

// Verilerin daha önce eklenip eklenmediğini kontrol edin
$sql = "SELECT COUNT(*) as count FROM sehir";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
if ($row['count'] > 0) {
    $veriEklendi = true;
}

// Verileri tabloya ekle
if (!$veriEklendi) {
    foreach ($sehirler as $sehir) {
        $isim = $sehir[0];
        $enlem = $sehir[1];
        $boylam = $sehir[2];
        $plaka = $sehir[3];

        $sql = "INSERT INTO sehir (İsim, Enlem, Boylam, Plaka) VALUES ('$isim', '$enlem', '$boylam', '$plaka')";

        if ($conn->query($sql) === false) {
            echo "Hata: " . $sql . "<br>" . $conn->error;
        }
    }
    
}
  
  echo "Veriler başarıyla eklendi.";
}
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
