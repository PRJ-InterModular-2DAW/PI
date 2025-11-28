<?php
// 1. CARGAR LIBRERÍAS (COMPOSER)
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

// 2. CONFIGURACIÓN DE RUTAS
$uploadsDir = '/var/www/uploads/';
$dataDir = '/var/www/data/';
$jsonFilePath = $dataDir . 'db.json';

// 3. VALIDAR Y GUARDAR EL ARCHIVO SUBIDO
if (empty($_FILES['excelFile']) || $_FILES['excelFile']['error'] !== UPLOAD_ERR_OK) {
    die("Error: No se ha recibido ningún archivo o hubo un error en la subida.");
}

$fileExtension = strtolower(pathinfo($_FILES['excelFile']['name'], PATHINFO_EXTENSION));
$uploadFilePath = $uploadsDir . uniqid('import_', true) . '.' . $fileExtension;

if (!move_uploaded_file($_FILES['excelFile']['tmp_name'], $uploadFilePath)) {
    die("Error: No se pudo mover el archivo a /uploads/. Verifica los permisos.");
}

// 4. CARGAR LA BASE DE DATOS COMPLETA (Para no borrar usuarios)
// ------------------------------------
$dbData = [
    'productes' => [],
    'usuaris' => []
];

// Si existe el archivo, cargamos su contenido actual
if (file_exists($jsonFilePath)) {
    $jsonContent = file_get_contents($jsonFilePath);
    $decoded = json_decode($jsonContent, true);
    if (is_array($decoded)) {
        $dbData = $decoded;
    }
}

// Preparamos un mapa de productos actuales para buscar rápido por ID
$currentProductsMap = [];
if (!empty($dbData['productes']) && is_array($dbData['productes'])) {
    foreach ($dbData['productes'] as $prod) {
        if (isset($prod['id'])) {
            $currentProductsMap[$prod['id']] = $prod;
        }
    }
}

// 5. LEER EL EXCEL Y PROCESAR (UPSERT)
// ------------------------------------
$importedCount = 0;
$updatedCount = 0;
$skippedCount = 0;

try {
    $spreadsheet = IOFactory::load($uploadFilePath);
    $rows = $spreadsheet->getActiveSheet()->toArray();

    // Eliminar la primera fila (cabecera)
    array_shift($rows);

    foreach ($rows as $row) {
        // A(0): id, B(1): nombre, C(2): descripcion, D(3): precio, E(4): stock
        $id_excel = trim($row[0] ?? '');
        $nom = trim($row[1] ?? '');
        $descripcio = trim($row[2] ?? '');
        $preu_brut = trim($row[3] ?? '');
        $estoc = trim($row[4] ?? '');

        // Limpieza de precio
        $preu_net = str_replace(['€', ' '], '', $preu_brut);
        $preu = str_replace(',', '.', $preu_net);

        // Validación básica
        if (empty($id_excel) || empty($nom) || !is_numeric($preu) || !is_numeric($estoc)) {
            $skippedCount++;
            continue;
        }

        $id = (int) $id_excel;
        $isUpdate = isset($currentProductsMap[$id]);

        // Datos del producto
        $newProductData = [
            'id' => $id,
            'sku' => 'SKU-' . $id,
            'nom' => $nom,
            'descripcio' => $descripcio,
            'preu' => (float) $preu,
            'estoc' => (int) $estoc
        ];

        // Guardamos/Sobrescribimos en el mapa temporal
        $currentProductsMap[$id] = $newProductData;

        if ($isUpdate) {
            $updatedCount++;
        } else {
            $importedCount++;
        }
    }

} catch (Exception $e) {
    die("Error al leer el archivo Excel: " . $e->getMessage());
}

// 6. ACTUALIZAR EL ARRAY PRINCIPAL Y GUARDAR
// -------------------------

// Convertimos el mapa de productos de nuevo a una lista indexada
$dbData['productes'] = array_values($currentProductsMap);

// ¡IMPORTANTE! Aquí $dbData contiene tanto 'productes' (actualizados) como 'usuaris' (intactos)
$jsonContent = json_encode($dbData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

if (file_put_contents($jsonFilePath, $jsonContent) === false) {
    die("Error: No se pudo escribir en /data/db.json. Verifica permisos.");
}

// 7. MOSTRAR RESULTADO
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resultado de Importación</title>
    <style>
        body { font-family: sans-serif; margin: 40px; text-align: center; }
        .card { border: 1px solid #ddd; padding: 20px; max-width: 500px; margin: 0 auto; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        h1 { color: #2c3e50; }
        .stats { text-align: left; margin: 20px 0; }
        .btn { display: inline-block; padding: 10px 20px; background-color: #3498db; color: white; text-decoration: none; border-radius: 5px; margin-top: 20px; }
        .btn:hover { background-color: #2980b9; }
    </style>
</head>
<body>
    <div class="card">
        <h1>✅ Importación Exitosa</h1>
        <p>La base de datos de usuarios se ha mantenido intacta.</p>
        <div class="stats">
            <p>🆕 Nuevos productos: <strong><?php echo $importedCount; ?></strong></p>
            <p>🔄 Productos actualizados: <strong><?php echo $updatedCount; ?></strong></p>
            <p>⚠️ Filas ignoradas: <strong><?php echo $skippedCount; ?></strong></p>
            <hr>
            <p>📦 Total productos: <strong><?php echo count($dbData['productes']); ?></strong></p>
            <p>👥 Total usuarios: <strong><?php echo count($dbData['usuaris'] ?? []); ?></strong></p>
        </div>
        
        <p>Puedes verificar la API:</p>
        <a href="http://localhost:3001/productes" target="_blank">http://localhost:3001/productes</a>
        <br><br>
        <a href="/importar.html" class="btn">Volver al formulario</a>
    </div>
</body>
</html>