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

    /**
     * Conta curtidas agrupadas por comentário.
     *
     * @param int[] $idsComentario
     * @return array<int, int>
     */
    public function contarCurtidasPorComentarios(array $idsComentario)
    {
        $idsComentario = array_values(array_filter(array_map('intval', $idsComentario), static function ($id) {
            return $id > 0;
        }));

        if ($idsComentario === []) {
            return [];
        }

        $placeholders = implode(', ', array_fill(0, count($idsComentario), '?'));
        $sql = "SELECT fk_comentario, COUNT(*) AS total
                FROM curtida_comentarios
                WHERE ativo = 1 AND fk_comentario IN ($placeholders)
                GROUP BY fk_comentario";

        $stmt = $this->conexao->prepare($sql);
        foreach ($idsComentario as $index => $idComentario) {
            $stmt->bindValue($index + 1, $idComentario, PDO::PARAM_INT);
        }
        $stmt->execute();

        $resultado = [];
        while ($dados = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $resultado[(int) $dados['fk_comentario']] = (int) $dados['total'];
        }

        return $resultado;
    }

    /**
     * Retorna um mapa [id_comentario => true] para comentários curtidos pelo usuário.
     *
     * @param int $idUsuario
     * @param int[] $idsComentario
     * @return array<int, bool>
     */
    public function usuarioCurtiuPorComentarios($idUsuario, array $idsComentario)
    {
        $idUsuario = (int) $idUsuario;
        $idsComentario = array_values(array_filter(array_map('intval', $idsComentario), static function ($id) {
            return $id > 0;
        }));

        if ($idUsuario <= 0 || $idsComentario === []) {
            return [];
        }

        $placeholders = implode(', ', array_fill(0, count($idsComentario), '?'));
        $sql = "SELECT fk_comentario
                FROM curtida_comentarios
                WHERE ativo = 1 AND fk_usuario = ? AND fk_comentario IN ($placeholders)";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(1, $idUsuario, PDO::PARAM_INT);
        foreach ($idsComentario as $index => $idComentario) {
            $stmt->bindValue($index + 2, $idComentario, PDO::PARAM_INT);
        }
        $stmt->execute();

        $resultado = [];
        while ($dados = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $resultado[(int) $dados['fk_comentario']] = true;
        }

        return $resultado;
    }
}
?>