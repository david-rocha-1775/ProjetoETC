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
        $sql = "SELECT id_denuncia, titulo, descricao, localizacao, foto_path, status 
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

            $denuncias[] = $denuncia;
        }

        return $denuncias;
    }

    /**
     * Insere uma nova denúncia no banco.
     *
     * @param DenunciaDTO $denuncia
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

        $stmt->execute();
    }
}
?>