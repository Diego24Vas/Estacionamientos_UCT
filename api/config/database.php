<?php
class Database {
    private static $host = 'localhost';
    private static $db_name = 'A2024_dvasquez';
    private static $username = 'dvasquez';
    private static $password = 'aVOeGU27CWrnwXMyx';
    private static $conn;

    public static function getConnection() {
        if (!self::$conn) {
            try {
                self::$conn = new PDO(
                    "mysql:host=" . self::$host . ";dbname=" . self::$db_name . ";charset=utf8",
                    self::$username,
                    self::$password
                );
                self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                echo "Error en conexión: " . $e->getMessage();
                exit;
            }
        }
        return self::$conn;
    }
}
