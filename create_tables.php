<?php
$host = '127.0.0.1';
$db   = 'gap_software';
$user = 'postgres';
$pass = '';

$dsn = "pgsql:host=$host;dbname=$db";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS mst_process_printing_mesin (
        mst_process_printing_id INT NOT NULL,
        mst_mesin_proses_id INT NOT NULL
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS mst_process_pfp_mesin (
        mst_process_pfp_id INT NOT NULL,
        mst_mesin_proses_id INT NOT NULL
    )");

    echo "Tables created successfully.\n";
} catch (\PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}


