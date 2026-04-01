<?php
// DAO (Data Access Object) da tabela 'curtida_comentarios'

require_once "model/dao/Conexao.php";
require_once "model/dto/CurtidaComentarioDTO.php";

class CurtidaComentarioDAO
{
    private $conexao;

    public function __construct()
    {
        $this->conexao = Conexao::getInstance();
    }

    /**
     * Registra uma curtida em comentário.
     *
     * @param CurtidaComentarioDTO $curtida
     * @return bool
     */
    public function curtir(CurtidaComentarioDTO $curtida)
    {
        $sql = "INSERT INTO curtida_comentarios (fk_usuario, fk_comentario, ativo)
            VALUES (:fk_usuario, :fk_comentario, 1)
            ON DUPLICATE KEY UPDATE ativo = 1, data_curtida = CURRENT_TIMESTAMP";

        $stmt = $this->conexao->prepare($sql);

        $idUsuario = $curtida->getIdUsuario();
        $idComentario = $curtida->getIdComentario();

        $stmt->bindParam(':fk_usuario', $idUsuario, PDO::PARAM_INT);
        $stmt->bindParam(':fk_comentario', $idComentario, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Remove a curtida de um comentário.
     *
     * @param int $idUsuario
     * @param int $idComentario
     * @return bool
     */
    public function removerCurtida($idUsuario, $idComentario)
    {
        $sql = "UPDATE curtida_comentarios
            SET ativo = 0
            WHERE fk_usuario = :fk_usuario
              AND fk_comentario = :fk_comentario
              AND ativo = 1";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindParam(':fk_usuario', $idUsuario, PDO::PARAM_INT);
        $stmt->bindParam(':fk_comentario', $idComentario, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Verifica se o usuário já curtiu o comentário.
     *
     * @param int $idUsuario
     * @param int $idComentario
     * @return bool
     */
    public function usuarioCurtiu($idUsuario, $idComentario)
    {
        $sql = "SELECT 1
                FROM curtida_comentarios
                WHERE fk_usuario = :fk_usuario
                  AND fk_comentario = :fk_comentario
                                    AND ativo = 1
                LIMIT 1";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindParam(':fk_usuario', $idUsuario, PDO::PARAM_INT);
        $stmt->bindParam(':fk_comentario', $idComentario, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchColumn() !== false;
    }

    /**
     * Conta as curtidas de um comentário.
     *
     * @param int $idComentario
     * @return int
     */
    public function contarCurtidas($idComentario)
    {
        $sql = "SELECT COUNT(*) AS total
                FROM curtida_comentarios
                WHERE fk_comentario = :fk_comentario AND ativo = 1";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindParam(':fk_comentario', $idComentario, PDO::PARAM_INT);
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }
}
?>