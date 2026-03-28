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
        $sql = "SELECT id_denuncia, titulo, descricao, localizacao, foto_path, status, fk_usuario, fk_categoria
                FROM denuncias 
                ORDER BY id_denuncia DESC 
                LIMIT :limite";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
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
            $denuncia->setFotoPath($dados['foto_path']);
            $denuncia->setStatus($status);
            $denuncia->setIdUsuario($dados['fk_usuario']);
            $denuncia->setIdCategoria($dados['fk_categoria']);

            $denuncias[] = $denuncia;
        }

        return $denuncias;
    }

    /**
     * Busca uma denúncia pelo ID.
     *
     * @param int $idDenuncia
     * @return DenunciaDTO|null
     */
    public function buscarPorId($idDenuncia)
    {
        $sql = "SELECT id_denuncia, titulo, descricao, localizacao, foto_path, status, fk_usuario, fk_categoria, data_criacao
                FROM denuncias
                WHERE id_denuncia = :id_denuncia";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindParam(':id_denuncia', $idDenuncia, PDO::PARAM_INT);
        $stmt->execute();

        $dados = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($dados === false) {
            return null;
        }

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
        $denuncia->setFotoPath($dados['foto_path']);
        $denuncia->setStatus($status);
        $denuncia->setIdUsuario($dados['fk_usuario']);
        $denuncia->setIdCategoria($dados['fk_categoria']);
        $denuncia->setDataCriacao($dados['data_criacao']);

        return $denuncia;
    }

    /**
     * Insere uma nova denúncia no banco.
     *
     * @param DenunciaDTO $denuncia
     * @return bool
     */
    public function cadastrar(DenunciaDTO $denuncia)
    {
        $sql = "INSERT INTO denuncias (titulo, descricao, localizacao, foto_path, status, fk_usuario, fk_categoria) 
                VALUES (:titulo, :descricao, :localizacao, :foto_path, :status, :fk_usuario, :fk_categoria)";

        $stmt = $this->conexao->prepare($sql);

        $titulo = $denuncia->getTitulo();
        $descricao = $denuncia->getDescricao();
        $localizacao = $denuncia->getLocalizacao();
        $fotoPath = $denuncia->getFotoPath();
        $status = $denuncia->getStatus();
        $idUsuario = $denuncia->getIdUsuario();
        $idCategoria = $denuncia->getIdCategoria();

        $stmt->bindParam(':titulo', $titulo);
        $stmt->bindParam(':descricao', $descricao);
        $stmt->bindParam(':localizacao', $localizacao);
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
                    foto_path = :foto_path,
                    status = :status,
                    fk_usuario = :fk_usuario,
                    fk_categoria = :fk_categoria
                WHERE id_denuncia = :id_denuncia";

        $stmt = $this->conexao->prepare($sql);

        $idDenuncia = $denuncia->getId();
        $titulo = $denuncia->getTitulo();
        $descricao = $denuncia->getDescricao();
        $localizacao = $denuncia->getLocalizacao();
        $fotoPath = $denuncia->getFotoPath();
        $status = $denuncia->getStatus();
        $idUsuario = $denuncia->getIdUsuario();
        $idCategoria = $denuncia->getIdCategoria();

        $stmt->bindParam(':id_denuncia', $idDenuncia, PDO::PARAM_INT);
        $stmt->bindParam(':titulo', $titulo);
        $stmt->bindParam(':descricao', $descricao);
        $stmt->bindParam(':localizacao', $localizacao);
        $stmt->bindParam(':foto_path', $fotoPath);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':fk_usuario', $idUsuario);
        $stmt->bindParam(':fk_categoria', $idCategoria);

        return $stmt->execute();
    }

    /**
     * Exclui uma denúncia pelo ID.
     *
     * @param int $idDenuncia
     * @return bool
     */
    public function excluirPorId($idDenuncia)
    {
        $sql = "DELETE FROM denuncias WHERE id_denuncia = :id_denuncia";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindParam(':id_denuncia', $idDenuncia, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
?>