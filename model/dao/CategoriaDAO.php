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
     * @param int|null $ativo
     * @return CategoriaDTO[] Array de objetos CategoriaDTO.
     */
    public function listarTodas($ativo = 1)
    {
        $sql = "SELECT id_categoria, nome_categoria, ativo FROM categorias WHERE 1 = 1";

        if ($ativo !== null) {
            $sql .= " AND ativo = :ativo";
        }

        $sql .= " ORDER BY nome_categoria ASC";

        $stmt = $this->conexao->prepare($sql);

        if ($ativo !== null) {
            $stmt->bindValue(':ativo', (int) $ativo, PDO::PARAM_INT);
        }

        $stmt->execute();

        $categorias = [];

        while ($dados = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $categoria = new CategoriaDTO();
            $categoria->setId($dados['id_categoria']);
            $categoria->setNomeCategoria($dados['nome_categoria']);
            $categoria->setAtivo($dados['ativo']);

            $categorias[] = $categoria;
        }

        return $categorias;
    }

    /**
     * Cadastra uma nova categoria no banco.
     *
     * @param CategoriaDTO $categoria
     * @return bool
     */
    public function cadastrar(CategoriaDTO $categoria)
    {
        $sql = "INSERT INTO categorias (nome_categoria) VALUES (:nome_categoria)";

        $stmt = $this->conexao->prepare($sql);

        $nomeCategoria = $categoria->getNomeCategoria();
        $stmt->bindParam(':nome_categoria', $nomeCategoria);

        return $stmt->execute();
    }

    /**
     * Atualiza os dados de uma categoria.
     *
     * @param CategoriaDTO $categoria
     * @return bool
     */
    public function atualizar(CategoriaDTO $categoria)
    {
        $sql = "UPDATE categorias
                SET nome_categoria = :nome_categoria
                WHERE id_categoria = :id_categoria AND ativo = 1";

        $stmt = $this->conexao->prepare($sql);

        $idCategoria = $categoria->getId();
        $nomeCategoria = $categoria->getNomeCategoria();

        $stmt->bindParam(':id_categoria', $idCategoria, PDO::PARAM_INT);
        $stmt->bindParam(':nome_categoria', $nomeCategoria);

        return $stmt->execute();
    }

    /**
     * Exclui uma categoria pelo ID.
     *
     * @param int $idCategoria
     * @return bool
     */
    public function excluirPorId($idCategoria)
    {
        $sql = "UPDATE categorias SET ativo = 0 WHERE id_categoria = :id_categoria AND ativo = 1";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindParam(':id_categoria', $idCategoria, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
?>