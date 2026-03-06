<?php
// DAO (Data Access Object) da Denúncia
// Contém TODOS os comandos SQL relacionados à tabela 'denuncias'

require_once "model/dao/Conexao.php";
require_once "model/dto/DenunciaDTO.php";

class DenunciaDAO
{
    private $conexao;

    public function __construct()
    {
        $this->conexao = Conexao::getInstance();
    }

    // Listar as últimas denúncias (para o painel)
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
            $denuncia->setId($dados['id_denuncia']);
            $denuncia->setTitulo($dados['titulo']);
            $denuncia->setDescricao($dados['descricao']);
            $denuncia->setLocalizacao($dados['localizacao']);
            $denuncia->setFotoPath($dados['foto_path']);
            $denuncia->setStatus($dados['status']);

            $denuncias[] = $denuncia;
        }

        return $denuncias;
    }

    // Cadastrar uma nova denúncia
    public function cadastrar(DenunciaDTO $denuncia)
    {
        $sql = "INSERT INTO denuncias (titulo, descricao, localizacao, foto_path, status, id_usuario) 
                VALUES (:titulo, :descricao, :localizacao, :foto_path, :status, :id_usuario)";

        $stmt = $this->conexao->prepare($sql);

        $titulo = $denuncia->getTitulo();
        $descricao = $denuncia->getDescricao();
        $localizacao = $denuncia->getLocalizacao();
        $fotoPath = $denuncia->getFotoPath();
        $status = $denuncia->getStatus();
        $idUsuario = $denuncia->getIdUsuario();

        $stmt->bindParam(':titulo', $titulo);
        $stmt->bindParam(':descricao', $descricao);
        $stmt->bindParam(':localizacao', $localizacao);
        $stmt->bindParam(':foto_path', $fotoPath);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id_usuario', $idUsuario);

        $stmt->execute();
    }
}
?>