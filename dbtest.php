<?php
try {
    $dsn = 'mysql:host=localhost;dbname=hssagrii_hssagri;charset=utf8';
    $username = 'hssagrii';
    $password = '7W-5IaTJ-rab';

    $pdo = new PDO($dsn, $username, $password);
    echo 'Database connection successful.';
} catch (PDOException $e) {
    echo 'Database connection failed: ' . $e->getMessage();
}
