<?php
// 1. CARREGAR LLIBRERIES (COMPOSER)
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

// 2. CONFIGURACIÓ DE RUTES
$uploadsDir = '../uploads/';
$dataDir = '../data/';
$jsonFilePath = $dataDir . 'products.json';

// 3. VALIDAR I DESAR EL FITXER PUJAT
// ------------------------------------

// Comprovar si s'ha rebut un fitxer vàlid
if (empty($_FILES['excelFile']) || $_FILES['excelFile']['error'] !== UPLOAD_ERR_OK) {
    die("Error: No s'ha rebut cap fitxer o hi ha hagut un error en la pujada.");
}

// Donar un nom únic al fitxer per seguretat
$fileExtension = strtolower(pathinfo($_FILES['excelFile']['name'], PATHINFO_EXTENSION));
$uploadFilePath = $uploadsDir . uniqid('import_', true) . '.' . $fileExtension;

if (!move_uploaded_file($_FILES['excelFile']['tmp_name'], $uploadFilePath)) {
    die("Error: No s'ha pogut moure el fitxer a /uploads/. Verifica els permisos.");
}

// 4. LLEGIR L'EXCEL I PROCESSAR DADES
// ------------------------------------
$products = [];
$skippedCount = 0;
// No fem servir $idCounter, ja que l'ID ve de l'Excel

try {
    $spreadsheet = IOFactory::load($uploadFilePath);
    $rows = $spreadsheet->getActiveSheet()->toArray();

    // Eliminar la primera fila (capçalera)
    array_shift($rows);

    foreach ($rows as $row) {

        // ---- ÍNDEXS CORREGITS SEGONS EL TEU EXCEL ----
        // Columna A (índex 0): id
        // Columna B (índex 1): nombre
        // Columna C (índex 2): descripcion
        // Columna D (índex 3): precio
        // Columna E (índex 4): stock

        $id_excel = trim($row[0] ?? '');
        $nom = trim($row[1] ?? '');
        $descripcio = trim($row[2] ?? '');
        $preu_brut = trim($row[3] ?? ''); // Agafem l'índex 3 (Col D)
        $estoc = trim($row[4] ?? '');     // Agafem l'índex 4 (Col E)

        // ---- NETEJA DEL PREU ----
        // Treure el símbol '€' i espais
        $preu_net = str_replace(['€', ' '], '', $preu_brut);
        // Canviar la coma decimal (si n'hi ha) per un punt
        $preu = str_replace(',', '.', $preu_net);

        // ---- VALIDACIÓ CORREGIDA ----
        if (empty($nom) || !is_numeric($preu) || !is_numeric($estoc)) {
            $skippedCount++;
            continue; // Ignora aquesta fila i segueix
        }

        // Si tot és correcte, afegeix el producte
        // (Fem servir els noms de camp del teu JSON Server: nom, descripcio, preu, estoc)
        $products[] = [
            'id' => (int) $id_excel, // Agafem l'ID de l'Excel
            'sku' => 'SKU-' . $id_excel, // Creem un SKU a partir de l'ID
            'nom' => $nom,
            'descripcio' => $descripcio,
            'preu' => (float) $preu,
            'estoc' => (int) $estoc
        ];
    }
} catch (Exception $e) {
    die("Error en llegir el fitxer Excel: " . $e->getMessage());
}
// 5. GENERAR L'ARXIU JSON
// -------------------------
// (Versió simple: sense còpia de seguretat)
$jsonData = ['productes' => $products];
$jsonContent = json_encode($jsonData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

if (file_put_contents($jsonFilePath, $jsonContent) === false) {
    die("Error: No s'ha pogut escriure a /data/products.json. Verifica els permisos.");
}

// 6. MOSTRAR RESULTAT (HTML SIMPLE)
// ----------------------------------
$importedCount = count($products);
?>

<!DOCTYPE html>
<html lang="ca">

<head>
    <meta charset="UTF-8">
    <title>Resultat d'Importació</title>
</head>

<body style="font-family: sans-serif; margin: 20px;">
    <h1>✅ Importació Completada</h1>
    <p>S'han importat <strong><?php echo $importedCount; ?></strong> productes.</p>
    <p>S'han ignorat <strong><?php echo $skippedCount; ?></strong> files amb errors o dades incompletes.</p>
    <hr>
    <p>
        Pots veure el resultat a l'API:
        <a href="http://localhost:3001/productes" target="_blank">
            http://localhost:3001/productes
        </a>
    </p>
    <p>
        <a href="../frontend/importar.html">Tornar al formulari</a>
    </p>
</body>

</html>