<?php
require_once __DIR__ . '/../config/db.php';

class Mesa {
    public static function todas() {
        global $pdo;
        return $pdo->query("SELECT * FROM mesas ORDER BY numero ASC")->fetchAll();
    }
}