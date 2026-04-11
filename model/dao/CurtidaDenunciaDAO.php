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

    /**
     * Conta curtidas agrupadas por denúncia.
     *
     * @param int[] $idsDenuncia
     * @return array<int, int>
     */
    public function contarCurtidasPorDenuncias(array $idsDenuncia)
    {
        $idsDenuncia = array_values(array_filter(array_map('intval', $idsDenuncia), static function ($id) {
            return $id > 0;
        }));

        if ($idsDenuncia === []) {
            return [];
        }

        $placeholders = implode(', ', array_fill(0, count($idsDenuncia), '?'));
        $sql = "SELECT fk_denuncia, COUNT(*) AS total
                FROM curtida_denuncias
                WHERE ativo = 1 AND fk_denuncia IN ($placeholders)
                GROUP BY fk_denuncia";

        $stmt = $this->conexao->prepare($sql);
        foreach ($idsDenuncia as $index => $idDenuncia) {
            $stmt->bindValue($index + 1, $idDenuncia, PDO::PARAM_INT);
        }
        $stmt->execute();

        $resultado = [];
        while ($dados = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $resultado[(int) $dados['fk_denuncia']] = (int) $dados['total'];
        }

        return $resultado;
    }

    /**
     * Retorna um mapa [id_denuncia => true] para denúncias curtidas pelo usuário.
     *
     * @param int $idUsuario
     * @param int[] $idsDenuncia
     * @return array<int, bool>
     */
    public function usuarioCurtiuPorDenuncias($idUsuario, array $idsDenuncia)
    {
        $idUsuario = (int) $idUsuario;
        $idsDenuncia = array_values(array_filter(array_map('intval', $idsDenuncia), static function ($id) {
            return $id > 0;
        }));

        if ($idUsuario <= 0 || $idsDenuncia === []) {
            return [];
        }

        $placeholders = implode(', ', array_fill(0, count($idsDenuncia), '?'));
        $sql = "SELECT fk_denuncia
                FROM curtida_denuncias
                WHERE ativo = 1 AND fk_usuario = ? AND fk_denuncia IN ($placeholders)";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(1, $idUsuario, PDO::PARAM_INT);
        foreach ($idsDenuncia as $index => $idDenuncia) {
            $stmt->bindValue($index + 2, $idDenuncia, PDO::PARAM_INT);
        }
        $stmt->execute();

        $resultado = [];
        while ($dados = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $resultado[(int) $dados['fk_denuncia']] = true;
        }

        return $resultado;
    }
}
?>