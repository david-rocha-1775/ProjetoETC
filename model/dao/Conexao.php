<?php

class Conexao
{
    private static $conexao;

    public function __construct() {}

    public static function getInstance(): mixed
    {
        if (!isset(self::$conexao)){
            try {
                $opcoes = [
                    PDO::ATTR_PERSISTENT => true,
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
                ];
                self::$conexao = new PDO(
                    dsn: "mysql:host=localhost;dbname=plataforma_denuncias",
                    username: "root",
                    password: "",
                    options: $opcoes,
                );
            } catch (PDOException $e) {
                throw new Exception(message: "Erro de conexão: " . $e->getMessage());
            }
        }

        return self::$conexao;
    }
}
?>