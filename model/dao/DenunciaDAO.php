<?php
// DAO (Data Access Object) da tabela 'denuncias'

require_once "model/dao/Conexao.php";
require_once "model/dto/DenunciaDTO.php";

class DenunciaDAO
{
    private $conexao;

    public function __construct()
    {
        $this->conexao = Conexao::getInstance();
    }

    /**
     * Retorna a lista das últimas denúncias registradas.
     *
     * @param int $limite Quantidade máxima de registros.
     * @return DenunciaDTO[] Array de objetos DenunciaDTO.
     */
    public function listarUltimas($limite = 10)
    {
        return $this->listarPaginadas(null, 1, $limite, 'recentes');
    }

    /**
     * Retorna denúncias com filtro opcional por categoria e paginação.
     *
     * @param int|null $idCategoria
     * @param int $pagina
     * @param int $limite
     * @param string $ordenacao
     * @return DenunciaDTO[]
     */
    public function listarPaginadas($idCategoria = null, $pagina = 1, $limite = 10, $ordenacao = 'recentes')
    {
        $pagina = (int) $pagina;
        $limite = (int) $limite;
        $ordenacao = strtolower(trim((string) $ordenacao));

        if ($pagina < 1) {
            $pagina = 1;
        }

        if ($limite < 1) {
            $limite = 10;
        }

        $offset = ($pagina - 1) * $limite;

        $sql = "SELECT id_denuncia, titulo, descricao, localizacao, latitude, longitude, foto_path, status, ativo, fk_usuario, fk_categoria, data_criacao
            FROM denuncias
            WHERE ativo = 1";

        if ($idCategoria !== null) {
            $sql .= " AND fk_categoria = :fk_categoria";
        }

        $ordemSql = ($ordenacao === 'antigas') ? 'ASC' : 'DESC';

        $sql .= " ORDER BY data_criacao {$ordemSql}, id_denuncia {$ordemSql}
            LIMIT :limite OFFSET :offset";

        $stmt = $this->conexao->prepare($sql);

        if ($idCategoria !== null) {
            $stmt->bindValue(':fk_categoria', (int) $idCategoria, PDO::PARAM_INT);
        }

        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $denuncias = [];

        while ($dados = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $denuncias[] = $this->hidratarDenuncia($dados);
        }

        return $denuncias;
    }

    /**
     * Conta denúncias ativas com filtro opcional por categoria.
     *
     * @param int|null $idCategoria
     * @return int
     */
    public function contarPaginadas($idCategoria = null)
    {
        $sql = "SELECT COUNT(*) AS total
            FROM denuncias
            WHERE ativo = 1";

        if ($idCategoria !== null) {
            $sql .= " AND fk_categoria = :fk_categoria";
        }

        $stmt = $this->conexao->prepare($sql);

        if ($idCategoria !== null) {
            $stmt->bindValue(':fk_categoria', (int) $idCategoria, PDO::PARAM_INT);
        }

        $stmt->execute();
        $dados = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int) ($dados['total'] ?? 0);
    }

    /**
     * Busca uma denúncia pelo ID.
     *
     * @param int $idDenuncia
     * @return DenunciaDTO|null
     */
    public function buscarPorId($idDenuncia)
    {
        $sql = "SELECT id_denuncia, titulo, descricao, localizacao, latitude, longitude, foto_path, status, ativo, fk_usuario, fk_categoria, data_criacao
            FROM denuncias
            WHERE id_denuncia = :id_denuncia AND ativo = 1";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindParam(':id_denuncia', $idDenuncia, PDO::PARAM_INT);
        $stmt->execute();

        $dados = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($dados === false) {
            return null;
        }

        return $this->hidratarDenuncia($dados);
    }

    /**
     * Insere uma nova denúncia no banco.
     *
     * @param DenunciaDTO $denuncia
     * @return bool
     */
    public function cadastrar(DenunciaDTO $denuncia)
    {
        $sql = "INSERT INTO denuncias (titulo, descricao, localizacao, latitude, longitude, foto_path, status, ativo, fk_usuario, fk_categoria) 
            VALUES (:titulo, :descricao, :localizacao, :latitude, :longitude, :foto_path, :status, 1, :fk_usuario, :fk_categoria)";

        $stmt = $this->conexao->prepare($sql);

        $titulo = $denuncia->getTitulo();
        $descricao = $denuncia->getDescricao();
        $localizacao = $denuncia->getLocalizacao();
        $latitude = $denuncia->getLatitude();
        $longitude = $denuncia->getLongitude();
        $fotoPath = $denuncia->getFotoPath();
        $status = $denuncia->getStatus();
        $idUsuario = $denuncia->getIdUsuario();
        $idCategoria = $denuncia->getIdCategoria();

        $stmt->bindParam(':titulo', $titulo);
        $stmt->bindParam(':descricao', $descricao);
        $stmt->bindParam(':localizacao', $localizacao);
        $stmt->bindParam(':latitude', $latitude);
        $stmt->bindParam(':longitude', $longitude);
        $stmt->bindParam(':foto_path', $fotoPath);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':fk_usuario', $idUsuario);
        $stmt->bindParam(':fk_categoria', $idCategoria);

        return $stmt->execute();
    }

    /**
     * Atualiza os dados de uma denúncia.
     *
     * @param DenunciaDTO $denuncia
     * @return bool
     */
    public function atualizar(DenunciaDTO $denuncia)
    {
        $sql = "UPDATE denuncias
                SET titulo = :titulo,
                    descricao = :descricao,
                    localizacao = :localizacao,
                    latitude = :latitude,
                    longitude = :longitude,
                    foto_path = :foto_path,
                    status = :status,
                    fk_usuario = :fk_usuario,
                    fk_categoria = :fk_categoria
                WHERE id_denuncia = :id_denuncia AND ativo = 1";

        $stmt = $this->conexao->prepare($sql);

        $idDenuncia = $denuncia->getId();
        $titulo = $denuncia->getTitulo();
        $descricao = $denuncia->getDescricao();
        $localizacao = $denuncia->getLocalizacao();
        $latitude = $denuncia->getLatitude();
        $longitude = $denuncia->getLongitude();
        $fotoPath = $denuncia->getFotoPath();
        $status = $denuncia->getStatus();
        $idUsuario = $denuncia->getIdUsuario();
        $idCategoria = $denuncia->getIdCategoria();

        $stmt->bindParam(':id_denuncia', $idDenuncia, PDO::PARAM_INT);
        $stmt->bindParam(':titulo', $titulo);
        $stmt->bindParam(':descricao', $descricao);
        $stmt->bindParam(':localizacao', $localizacao);
        $stmt->bindParam(':latitude', $latitude);
        $stmt->bindParam(':longitude', $longitude);
        $stmt->bindParam(':foto_path', $fotoPath);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':fk_usuario', $idUsuario);
        $stmt->bindParam(':fk_categoria', $idCategoria);

        return $stmt->execute();
    }

    /**
     * Busca denúncias por proximidade usando bounding box e Haversine.
     *
     * @param float $latitude
     * @param float $longitude
     * @param float $raioKm
     * @param int $limite
     * @return DenunciaDTO[]
     */
    public function buscarPorProximidade($latitude, $longitude, $raioKm = 10.0, $limite = 50)
    {
        $latitude = (float) $latitude;
        $longitude = (float) $longitude;
        $raioKm = (float) $raioKm;
        $limite = (int) $limite;

        if ($limite <= 0) {
            $limite = 50;
        }

        $kmPorGrau = 111.045;
        $deltaLatitude = $raioKm / $kmPorGrau;
        $cosLatitude = cos(deg2rad($latitude));
        if (abs($cosLatitude) < 0.000001) {
            $cosLatitude = 0.000001;
        }
        $deltaLongitude = $raioKm / ($kmPorGrau * abs($cosLatitude));

        $latitudeMin = $latitude - $deltaLatitude;
        $latitudeMax = $latitude + $deltaLatitude;
        $longitudeMin = $longitude - $deltaLongitude;
        $longitudeMax = $longitude + $deltaLongitude;

        $sql = "SELECT id_denuncia, titulo, descricao, localizacao, latitude, longitude, foto_path, status, ativo, fk_usuario, fk_categoria, data_criacao,
                    (6371 * ACOS(
                        LEAST(1.0, GREATEST(-1.0,
                            COS(RADIANS(:latitude_ref)) * COS(RADIANS(latitude)) * COS(RADIANS(longitude) - RADIANS(:longitude_ref)) +
                            SIN(RADIANS(:latitude_ref)) * SIN(RADIANS(latitude))
                        ))
                    )) AS distancia_km
                FROM denuncias
                WHERE ativo = 1
                    AND latitude IS NOT NULL
                    AND longitude IS NOT NULL
                    AND latitude BETWEEN :latitude_min AND :latitude_max
                    AND longitude BETWEEN :longitude_min AND :longitude_max
                HAVING distancia_km <= :raio_km
                ORDER BY distancia_km ASC, data_criacao DESC
                LIMIT :limite";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(':latitude_ref', $latitude);
        $stmt->bindValue(':longitude_ref', $longitude);
        $stmt->bindValue(':latitude_min', $latitudeMin);
        $stmt->bindValue(':latitude_max', $latitudeMax);
        $stmt->bindValue(':longitude_min', $longitudeMin);
        $stmt->bindValue(':longitude_max', $longitudeMax);
        $stmt->bindValue(':raio_km', $raioKm);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();

        $denuncias = [];
        while ($dados = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $denuncia = new DenunciaDTO();
            $status = $dados['status'];
            $statusValido = ['Aberto', 'Em Andamento', 'Resolvido'];

            if (!in_array($status, $statusValido, true)) {
                $status = 'Aberto';
            }

            $denuncia->setId($dados['id_denuncia']);
            $denuncia->setTitulo($dados['titulo']);
            $denuncia->setDescricao($dados['descricao']);
            $denuncia->setLocalizacao($dados['localizacao']);
            $denuncia->setLatitude($dados['latitude']);
            $denuncia->setLongitude($dados['longitude']);
            $denuncia->setFotoPath($dados['foto_path']);
            $denuncia->setStatus($status);
            $denuncia->setAtivo($dados['ativo']);
            $denuncia->setIdUsuario($dados['fk_usuario']);
            $denuncia->setIdCategoria($dados['fk_categoria']);
            $denuncia->setDataCriacao($dados['data_criacao']);

            $denuncias[] = $denuncia;
        }

        return $denuncias;
    }

    /**
     * Exclui uma denúncia pelo ID.
     *
     * @param int $idDenuncia
     * @return bool
     */
    public function excluirPorId($idDenuncia)
    {
        $sql = "UPDATE denuncias SET ativo = 0 WHERE id_denuncia = :id_denuncia AND ativo = 1";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindParam(':id_denuncia', $idDenuncia, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Monta um DTO de denúncia a partir de um registro bruto.
     *
     * @param array $dados
     * @return DenunciaDTO
     */
    private function hidratarDenuncia(array $dados)
    {
        $denuncia = new DenunciaDTO();
        $status = $dados['status'];
        $statusValido = ['Aberto', 'Em Andamento', 'Resolvido'];

        if (!in_array($status, $statusValido, true)) {
            $status = 'Aberto';
        }

        $denuncia->setId($dados['id_denuncia']);
        $denuncia->setTitulo($dados['titulo']);
        $denuncia->setDescricao($dados['descricao']);
        $denuncia->setLocalizacao($dados['localizacao']);
        $denuncia->setLatitude($dados['latitude']);
        $denuncia->setLongitude($dados['longitude']);
        $denuncia->setFotoPath($dados['foto_path']);
        $denuncia->setStatus($status);
        $denuncia->setAtivo($dados['ativo']);
        $denuncia->setIdUsuario($dados['fk_usuario']);
        $denuncia->setIdCategoria($dados['fk_categoria']);
        $denuncia->setDataCriacao($dados['data_criacao']);

        return $denuncia;
    }
}
?>