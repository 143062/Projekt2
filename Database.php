<?php

namespace App;

use PDO;

class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        $config = include(__DIR__ . '/config.php');
        $dsn = 'pgsql:host=' . $config['database']['host'] . ';port=' . $config['database']['port'] . ';dbname=' . $config['database']['dbname'];
        $this->pdo = new PDO($dsn, $config['database']['username'], $config['database']['password']);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->pdo;
    }
}
?>
