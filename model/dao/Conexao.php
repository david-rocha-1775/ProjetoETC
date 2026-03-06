<?php

class Conexao
{
    private static $conexao;

    public function __construct()
    {
    }

    public static function getInstance(): mixed
    {
        if (!isset(self::$conexao)) {
            try {
                // Carrega as configurações do arquivo separado
                $config = require "config/database.php";

                $opcoes = [
                    PDO::ATTR_PERSISTENT => true,
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . $config['charset']
                ];
                self::$conexao = new PDO(
                    dsn: "mysql:host=" . $config['host'] . ";dbname=" . $config['dbname'],
                    username: $config['usuario'],
                    password: $config['senha'],
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