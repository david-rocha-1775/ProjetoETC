<?php
// DAO (Data Access Object) da tabela 'comentarios'

require_once "model/dao/Conexao.php";
require_once "model/dto/ComentarioDTO.php";

class ComentarioDAO
{
    private $conexao;

    public function __construct()
    {
        $this->conexao = Conexao::getInstance();
    }

    /**
     * Cadastra um novo comentário no banco.
     *
     * @param ComentarioDTO $comentario
     * @return int|bool ID inserido ou false em caso de falha
     */
    public function cadastrar(ComentarioDTO $comentario)
    {
        $sql = "INSERT INTO comentarios (texto, ativo, fk_usuario, fk_denuncia)
            VALUES (:texto, 1, :fk_usuario, :fk_denuncia)";

        $stmt = $this->conexao->prepare($sql);

        $texto = $comentario->getTexto();
        $idUsuario = $comentario->getIdUsuario();
        $idDenuncia = $comentario->getIdDenuncia();

        $stmt->bindParam(':texto', $texto);
        $stmt->bindParam(':fk_usuario', $idUsuario, PDO::PARAM_INT);
        $stmt->bindParam(':fk_denuncia', $idDenuncia, PDO::PARAM_INT);

        if (!$stmt->execute()) {
            return false;
        }

        return (int) $this->conexao->lastInsertId();
    }

    /**
     * Lista os comentários de uma denúncia.
     *
     * @param int $idDenuncia
     * @return ComentarioDTO[]
     */
    public function listarPorDenuncia($idDenuncia)
    {
        $sql = "SELECT c.id_comentario, c.texto, c.data_comentario, c.ativo, c.fk_usuario, c.fk_denuncia, u.nome AS nome_usuario
                FROM comentarios c
                INNER JOIN usuarios u ON u.id_usuario = c.fk_usuario
            WHERE c.fk_denuncia = :id_denuncia AND c.ativo = 1
                ORDER BY c.data_comentario ASC";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindParam(':id_denuncia', $idDenuncia, PDO::PARAM_INT);
        $stmt->execute();

        $comentarios = [];
        while ($dados = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $comentario = new ComentarioDTO();
            $comentario->setId($dados['id_comentario']);
            $comentario->setTexto($dados['texto']);
            $comentario->setDataComentario($dados['data_comentario']);
            $comentario->setAtivo($dados['ativo']);
            $comentario->setIdUsuario($dados['fk_usuario']);
            $comentario->setIdDenuncia($dados['fk_denuncia']);
            $comentario->setNomeUsuario($dados['nome_usuario']);

            $comentarios[] = $comentario;
        }

        return $comentarios;
    }

    /**
     * Lista comentários ativos agrupados por denúncia.
     *
     * @param int[] $idsDenuncia
     * @return array<int, ComentarioDTO[]>
     */
    public function listarPorDenuncias(array $idsDenuncia)
    {
        $idsDenuncia = array_values(array_filter(array_map('intval', $idsDenuncia), static function ($id) {
            return $id > 0;
        }));

        if ($idsDenuncia === []) {
            return [];
        }

        $placeholders = implode(', ', array_fill(0, count($idsDenuncia), '?'));
        $sql = "SELECT c.id_comentario, c.texto, c.data_comentario, c.ativo, c.fk_usuario, c.fk_denuncia, u.nome AS nome_usuario
                FROM comentarios c
                INNER JOIN usuarios u ON u.id_usuario = c.fk_usuario
                WHERE c.fk_denuncia IN ($placeholders) AND c.ativo = 1
                ORDER BY c.fk_denuncia ASC, c.data_comentario ASC";

        $stmt = $this->conexao->prepare($sql);
        foreach ($idsDenuncia as $index => $idDenuncia) {
            $stmt->bindValue($index + 1, $idDenuncia, PDO::PARAM_INT);
        }
        $stmt->execute();

        $resultado = [];
        while ($dados = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $comentario = new ComentarioDTO();
            $comentario->setId($dados['id_comentario']);
            $comentario->setTexto($dados['texto']);
            $comentario->setDataComentario($dados['data_comentario']);
            $comentario->setAtivo($dados['ativo']);
            $comentario->setIdUsuario($dados['fk_usuario']);
            $comentario->setIdDenuncia($dados['fk_denuncia']);
            $comentario->setNomeUsuario($dados['nome_usuario']);

            $idDenuncia = (int) $dados['fk_denuncia'];
            if (!isset($resultado[$idDenuncia])) {
                $resultado[$idDenuncia] = [];
            }

            $resultado[$idDenuncia][] = $comentario;
        }

        return $resultado;
    }

    /**
     * Busca um comentário pelo ID.
     *
     * @param int $idComentario
     * @return ComentarioDTO|null
     */
    public function buscarPorId($idComentario)
    {
        $sql = "SELECT c.id_comentario, c.texto, c.data_comentario, c.ativo, c.fk_usuario, c.fk_denuncia, u.nome AS nome_usuario
                FROM comentarios c
                INNER JOIN usuarios u ON u.id_usuario = c.fk_usuario
            WHERE c.id_comentario = :id_comentario AND c.ativo = 1";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindParam(':id_comentario', $idComentario, PDO::PARAM_INT);
        $stmt->execute();

        $dados = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($dados === false) {
            return null;
        }

        $comentario = new ComentarioDTO();
        $comentario->setId($dados['id_comentario']);
        $comentario->setTexto($dados['texto']);
        $comentario->setDataComentario($dados['data_comentario']);
        $comentario->setAtivo($dados['ativo']);
        $comentario->setIdUsuario($dados['fk_usuario']);
        $comentario->setIdDenuncia($dados['fk_denuncia']);
        $comentario->setNomeUsuario($dados['nome_usuario']);

        return $comentario;
    }

    /**
     * Atualiza o texto de um comentário.
     *
     * @param ComentarioDTO $comentario
     * @return bool
     */
    public function atualizar(ComentarioDTO $comentario)
    {
        $sql = "UPDATE comentarios
                SET texto = :texto
            WHERE id_comentario = :id_comentario AND ativo = 1";

        $stmt = $this->conexao->prepare($sql);

        $idComentario = $comentario->getId();
        $texto = $comentario->getTexto();

        $stmt->bindParam(':id_comentario', $idComentario, PDO::PARAM_INT);
        $stmt->bindParam(':texto', $texto);

        return $stmt->execute();
    }

    /**
     * Exclui um comentário pelo ID.
     *
     * @param int $idComentario
     * @return bool
     */
    public function excluirPorId($idComentario)
    {
        $sql = "UPDATE comentarios SET ativo = 0 WHERE id_comentario = :id_comentario AND ativo = 1";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindParam(':id_comentario', $idComentario, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
?>