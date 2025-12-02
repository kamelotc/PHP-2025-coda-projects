<?php

require_once 'inc/page.inc.php';
require_once 'inc/database.inc.php';

try {
    $db = new DatabaseManager(
        dsn: 'mysql:host=mysql;dbname=lowify;charset=utf8mb4',
        username: 'lowify',
        password: 'lowifypassword'
    );
} catch (PDOException $ex) {
    echo "Erreur requête base de données  : " . $ex->getMessage();
    exit;
}

$Album = [];

try {

    $Album = $db->executeQuery("SELECT id, name, cover FROM album");
} catch (PDOException $ex) {
    echo "Erreur requête base de données : " . $ex->getMessage();
    exit;
}

