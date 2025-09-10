<?php
// $ip = "192.168.15.186";
// $port = 8899;

// $fp = fsockopen($ip, $port, $errno, $errstr, 5);
// if (!$fp) {
//     echo "Error: $errstr ($errno)";
//     exit;
// }

// // Si la balanza requiere comando, envíalo:

// $data = fread($fp, 1024);
// fclose($fp);

// // Separar por comas
// $parts = explode(",", $data);

// // El peso suele estar en el último elemento
// $peso = trim(str_replace("kg", "", end($parts)));

// echo "Peso numérico: " . $peso; // → 0.0000

// // echo "<pre>";
// // print_r($data);
// // echo "</pre>";







// $ip = "192.168.15.190";
// $port = 8080;

// $fp = fsockopen("192.168.15.190", 8080, $errno, $errstr, 5);

// if ($fp) {
//     stream_set_timeout($fp, 2);
//     $data = fread($fp, 1024);
//     fclose($fp);

//     echo "Datos crudos:<br>";
//     for ($i = 0; $i < strlen($data); $i++) {
//         echo "Byte $i: " . ord($data[$i]) . "<br>";
//     }
// } else {
//     echo "Error $errno: $errstr";
// }


// $ip = "192.168.11.245";
// $ip = "192.168.15.190";
// $port = 8081;

// $fp = fsockopen($ip, $port, $errno, $errstr, 5);

// if ($fp) {
//     stream_set_timeout($fp, 2);
//     $data = fread($fp, 1024);
//     fclose($fp);

//     $clean = trim(preg_replace('/[^(\x20-\x7E)]*/', '', $data));
//     echo "Peso recibido: [$clean]";
// } else {
//     echo "Error $errno: $errstr";
// }

// $fp = fsockopen("192.168.15.53", 8088, $errno, $errstr, 5);
// if ($fp) {
//     stream_set_timeout($fp, 2);
//     fwrite($fp, "SI\r\n");
//     $data = fread($fp, 1024);
//     fclose($fp);

//     $bytes = str_split($data);
    
//     if (isset($bytes[3])) {
//         $peso = ord($bytes[3]) - 128;
//         $peso = $peso / 100; // ajustar según precisión

//         // Filtrar solo pesos significativos
//         if ($peso > 0.01) {
//             echo "Peso recibido: " . $peso . " kg\n";
//         } else {
//             echo "Sin peso en la balanza\n";
//         }
//     } else {
//         echo "No se pudo leer el peso";
//     }
// } else {
//     echo "Error $errno: $errstr";
// }




$ip = "192.168.15.53";  // IP de la balanza
$port = 8088;            // Puerto TCP
$lecturas = 3;           // Número de lecturas para comparar
$paquetes = [];

// Función para leer datos del puerto TCP
function leerPaquete($ip, $port) {
    $fp = fsockopen($ip, $port, $errno, $errstr, 2);
    if (!$fp) return false;

    fwrite($fp, "SI\r\n");
    $data = fread($fp, 1024);
    fclose($fp);

    $bytes = array_map('ord', str_split($data));
    return $bytes;
}

// Tomar varias lecturas
for ($i = 0; $i < $lecturas; $i++) {
    $paquete = leerPaquete($ip, $port);
    if ($paquete) $paquetes[] = $paquete;
    usleep(200000); // 200 ms entre lecturas
}

if (count($paquetes) < 2) {
    die("No se pudieron tomar suficientes lecturas.");
}

// Detectar el byte que cambia
$cambios = [];
$len = min(array_map('count', $paquetes));
for ($i = 0; $i < $len; $i++) {
    $valores = array_column($paquetes, $i);
    if (count(array_unique($valores)) > 1) {
        $cambios[] = ['indice' => $i, 'valores' => $valores];
    }
}

// Mostrar resultados
if (empty($cambios)) {
    echo "No se detectaron cambios entre las lecturas. Revisa que la balanza tenga peso distinto.\n";
} else {
    echo "Bytes que cambian con el peso:\n";
    foreach ($cambios as $c) {
        echo "Byte {$c['indice']}: Valores = [" . implode(", ", $c['valores']) . "]\n";
    }

    // Ejemplo: usar el primer byte que cambió para calcular peso
    $indicePeso = $cambios[0]['indice'];
    $peso = end($cambios[0]['valores']) - 128; // Ajusta la fórmula según tu balanza
    $pesoKg = $peso / 100; // Ajusta según precisión de la balanza
    echo "\nPeso aproximado usando Byte $indicePeso: $pesoKg kg\n";
}
?>




