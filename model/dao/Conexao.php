<?php
// Classe de Conexão com o Banco de Dados (Padrão Singleton)

class Conexao
{
    private static $conexao;

    // Construtor privado para evitar instanciação direta
    private function __construct()
    {
    }

    /**
     * Retorna a instância única da conexão PDO.
     */
    public static function getInstance(): mixed
    {
        if (!isset(self::$conexao)) {
            try {
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
                throw new Exception("Erro de conexão: " . $e->getMessage());
            }
        }

        return self::$conexao;
    }
}
?>