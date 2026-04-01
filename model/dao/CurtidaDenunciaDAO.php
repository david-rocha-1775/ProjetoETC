<?php
// DAO (Data Access Object) da tabela 'curtida_denuncias'

require_once "model/dao/Conexao.php";
require_once "model/dto/CurtidaDenunciaDTO.php";

class CurtidaDenunciaDAO
{
    private $conexao;

    public function __construct()
    {
        $this->conexao = Conexao::getInstance();
    }

    /**
     * Registra uma curtida em denúncia.
     *
     * @param CurtidaDenunciaDTO $curtida
     * @return bool
     */
    public function curtir(CurtidaDenunciaDTO $curtida)
    {
        $sql = "INSERT INTO curtida_denuncias (fk_usuario, fk_denuncia, ativo)
            VALUES (:fk_usuario, :fk_denuncia, 1)
            ON DUPLICATE KEY UPDATE ativo = 1, data_curtida = CURRENT_TIMESTAMP";

        $stmt = $this->conexao->prepare($sql);

        $idUsuario = $curtida->getIdUsuario();
        $idDenuncia = $curtida->getIdDenuncia();

        $stmt->bindParam(':fk_usuario', $idUsuario, PDO::PARAM_INT);
        $stmt->bindParam(':fk_denuncia', $idDenuncia, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Remove a curtida de uma denúncia.
     *
     * @param int $idUsuario
     * @param int $idDenuncia
     * @return bool
     */
    public function removerCurtida($idUsuario, $idDenuncia)
    {
        $sql = "UPDATE curtida_denuncias
            SET ativo = 0
            WHERE fk_usuario = :fk_usuario
              AND fk_denuncia = :fk_denuncia
              AND ativo = 1";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindParam(':fk_usuario', $idUsuario, PDO::PARAM_INT);
        $stmt->bindParam(':fk_denuncia', $idDenuncia, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Verifica se o usuário já curtiu a denúncia.
     *
     * @param int $idUsuario
     * @param int $idDenuncia
     * @return bool
     */
    public function usuarioCurtiu($idUsuario, $idDenuncia)
    {
        $sql = "SELECT 1
                FROM curtida_denuncias
                WHERE fk_usuario = :fk_usuario
                  AND fk_denuncia = :fk_denuncia
                                    AND ativo = 1
                LIMIT 1";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindParam(':fk_usuario', $idUsuario, PDO::PARAM_INT);
        $stmt->bindParam(':fk_denuncia', $idDenuncia, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchColumn() !== false;
    }

    /**
     * Conta as curtidas de uma denúncia.
     *
     * @param int $idDenuncia
     * @return int
     */
    public function contarCurtidas($idDenuncia)
    {
        $sql = "SELECT COUNT(*) AS total
                FROM curtida_denuncias
                WHERE fk_denuncia = :fk_denuncia AND ativo = 1";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindParam(':fk_denuncia', $idDenuncia, PDO::PARAM_INT);
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }
}
?>