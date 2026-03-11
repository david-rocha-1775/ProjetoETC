<?php
// DAO (Data Access Object) da tabela 'categorias'

require_once "model/dao/Conexao.php";
require_once "model/dto/CategoriaDTO.php";

class CategoriaDAO
{
    private $conexao;

    public function __construct()
    {
        $this->conexao = Conexao::getInstance();
    }

    /**
     * Retorna a lista de todas as categorias registradas.
     *
     * @return CategoriaDTO[] Array de objetos CategoriaDTO.
     */
    public function listarTodas()
    {
        $sql = "SELECT id_categoria, nome_categoria FROM categorias ORDER BY nome_categoria ASC";

        $stmt = $this->conexao->prepare($sql);
        $stmt->execute();

        $categorias = [];

        while ($dados = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $categoria = new CategoriaDTO();
            $categoria->setId($dados['id_categoria']);
            $categoria->setNomeCategoria($dados['nome_categoria']);

            $categorias[] = $categoria;
        }

        return $categorias;
    }
}
?>