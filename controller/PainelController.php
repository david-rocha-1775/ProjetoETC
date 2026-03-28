<?php
// Controller de Painel e Ações Autenticadas

require_once "model/dao/DenunciaDAO.php";
require_once "model/dao/CategoriaDAO.php";

class PainelController
{
    private $denunciaDAO;
    private $categoriaDAO;

    public function __construct()
    {
        // Middleware de verificação de sessão (protege todas as ações deste controller)
        if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
            header("Location: index.php?rota=login");
            exit();
        }

        $this->denunciaDAO = new DenunciaDAO();
        $this->categoriaDAO = new CategoriaDAO();
    }

    /**
     * Carrega a página principal do painel (Dashboard).
     */
    public function index()
    {
        try {
            $denuncias = $this->denunciaDAO->listarUltimas(10);
            $tituloPagina = "Painel do Usuário";
            $usuarioNome = $_SESSION['usuario_nome'];

            include "view/painel/index.php";

        } catch (Exception $e) {
            $_SESSION['mensagem'] = "Erro ao carregar os dados do painel: " . $e->getMessage();
            $_SESSION['tipo_mensagem'] = "erro";
            header("Location: index.php?rota=inicio");
            exit();
        }
    }

    /**
     * Processa o cadastro de uma nova denúncia.
     */
    public function cadastrarDenuncia()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $titulo = trim($_POST['titulo'] ?? '');
                $descricao = trim($_POST['descricao'] ?? '');
                $localizacao = trim($_POST['localizacao'] ?? '');
                $idCategoria = trim($_POST['id_categoria'] ?? '');

                if (empty($titulo) || empty($descricao) || empty($localizacao) || empty($idCategoria)) {
                    throw new Exception("Os campos título, descrição, localização e categoria são obrigatórios.");
                }

                $fotoPath = null;
                // Processa o upload da foto se ela for enviada
                if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                    $fotoPath = $this->processarUploadImagem($_FILES['foto']);
                }

                $denuncia = new DenunciaDTO();
                $denuncia->setTitulo($titulo);
                $denuncia->setDescricao($descricao);
                $denuncia->setLocalizacao($localizacao);
                $denuncia->setFotoPath($fotoPath);
                $denuncia->setIdCategoria($idCategoria);
                $denuncia->setStatus('Aberto'); // Status inicial deve respeitar o ENUM do banco
                // A sessão deve possuir o id do usuário no momento do login ("usuario_id")
                $denuncia->setIdUsuario($_SESSION['usuario_id'] ?? null);

                $salvou = $this->denunciaDAO->cadastrar($denuncia);
                if (!$salvou) {
                    throw new Exception("Não foi possível cadastrar a denúncia.");
                }

                $_SESSION['mensagem'] = "Denúncia cadastrada com sucesso!";
                $_SESSION['tipo_mensagem'] = "sucesso";

            } catch (Exception $e) {
                $_SESSION['mensagem'] = "Erro: " . $e->getMessage();
                $_SESSION['tipo_mensagem'] = "erro";
            }

            // Redirecionar via PRG para evitar resubmissões e mostrar retorno da operação
            header("Location: index.php?rota=painel");
            exit();
        } else {
            // Em caso de requisição GET, exibe a view de formulário da denúncia
            $tituloPagina = "Nova Denúncia";
            $usuarioNome = $_SESSION['usuario_nome'] ?? 'Usuário';

            // Carrega as categorias do banco para preencher o <select>
            $categorias = $this->categoriaDAO->listarTodas();

            include "view/painel/nova_denuncia.php";
        }
    }

    /**
     * Processa edição de denúncia existente.
     */
    public function atualizarDenuncia()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?rota=painel");
            exit();
        }

        try {
            $idDenuncia = (int) ($_POST['id_denuncia'] ?? 0);
            $titulo = trim($_POST['titulo'] ?? '');
            $descricao = trim($_POST['descricao'] ?? '');
            $localizacao = trim($_POST['localizacao'] ?? '');
            $idCategoria = (int) ($_POST['id_categoria'] ?? 0);
            $status = trim($_POST['status'] ?? 'Aberto');

            if ($idDenuncia <= 0 || $idCategoria <= 0 || empty($titulo) || empty($descricao) || empty($localizacao)) {
                throw new Exception("Dados obrigatórios da denúncia não informados corretamente.");
            }

            $denunciaAtual = $this->denunciaDAO->buscarPorId($idDenuncia);
            if ($denunciaAtual === null) {
                throw new Exception("Denúncia não encontrada.");
            }

            $this->validarPermissaoDenuncia($denunciaAtual->getIdUsuario());

            $statusValido = ['Aberto', 'Em Andamento', 'Resolvido'];
            if (!in_array($status, $statusValido, true)) {
                $status = 'Aberto';
            }

            $fotoPath = $denunciaAtual->getFotoPath();
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $fotoPath = $this->processarUploadImagem($_FILES['foto']);
            }

            $denuncia = new DenunciaDTO();
            $denuncia->setId($idDenuncia);
            $denuncia->setTitulo($titulo);
            $denuncia->setDescricao($descricao);
            $denuncia->setLocalizacao($localizacao);
            $denuncia->setFotoPath($fotoPath);
            $denuncia->setStatus($status);
            $denuncia->setIdUsuario($denunciaAtual->getIdUsuario());
            $denuncia->setIdCategoria($idCategoria);

            $atualizou = $this->denunciaDAO->atualizar($denuncia);
            if (!$atualizou) {
                throw new Exception("Não foi possível atualizar a denúncia.");
            }

            $_SESSION['mensagem'] = "Denúncia atualizada com sucesso!";
            $_SESSION['tipo_mensagem'] = "sucesso";

        } catch (Exception $e) {
            $_SESSION['mensagem'] = "Erro: " . $e->getMessage();
            $_SESSION['tipo_mensagem'] = "erro";
        }

        header("Location: index.php?rota=painel");
        exit();
    }

    /**
     * Processa exclusão de denúncia.
     */
    public function excluirDenuncia()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?rota=painel");
            exit();
        }

        try {
            $idDenuncia = (int) ($_POST['id_denuncia'] ?? 0);
            if ($idDenuncia <= 0) {
                throw new Exception("ID da denúncia inválido.");
            }

            $denunciaAtual = $this->denunciaDAO->buscarPorId($idDenuncia);
            if ($denunciaAtual === null) {
                throw new Exception("Denúncia não encontrada.");
            }

            $this->validarPermissaoDenuncia($denunciaAtual->getIdUsuario());

            $excluiu = $this->denunciaDAO->excluirPorId($idDenuncia);
            if (!$excluiu) {
                throw new Exception("Não foi possível excluir a denúncia.");
            }

            $_SESSION['mensagem'] = "Denúncia excluída com sucesso!";
            $_SESSION['tipo_mensagem'] = "sucesso";

        } catch (Exception $e) {
            $_SESSION['mensagem'] = "Erro: " . $e->getMessage();
            $_SESSION['tipo_mensagem'] = "erro";
        }

        header("Location: index.php?rota=painel");
        exit();
    }

    /**
     * Garante que somente o dono da denúncia ou admin altere/exclua o registro.
     */
    private function validarPermissaoDenuncia($idUsuarioDono)
    {
        $idUsuarioSessao = (int) ($_SESSION['usuario_id'] ?? 0);
        $usuarioAdmin = isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] === 'admin';

        if (!$usuarioAdmin && $idUsuarioSessao !== (int) $idUsuarioDono) {
            throw new Exception("Você não tem permissão para alterar esta denúncia.");
        }
    }

    /**
     * Valida e processa upload de imagem de denúncia.
     *
     * @param array $arquivo
     * @return string Caminho salvo da imagem.
     */
    private function processarUploadImagem($arquivo)
    {
        $tamanhoMaximo = 5 * 1024 * 1024;
        if ($arquivo['size'] > $tamanhoMaximo) {
            throw new Exception("A foto deve ter no máximo 5 MB.");
        }

        $extensoesPermitidas = ['jpg', 'jpeg', 'png'];
        $extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
        if (!in_array($extensao, $extensoesPermitidas, true)) {
            throw new Exception("Formato de arquivo não permitido. Use JPG e PNG.");
        }

        $mimeType = (new finfo(FILEINFO_MIME_TYPE))->file($arquivo['tmp_name']);
        $mimesPermitidos = ['image/jpeg', 'image/png'];
        if (!in_array($mimeType, $mimesPermitidos, true)) {
            throw new Exception("O arquivo enviado não é uma imagem válida.");
        }

        $diretorioDestino = 'uploads/';
        if (!is_dir($diretorioDestino)) {
            mkdir($diretorioDestino, 0755, true);
        }

        $nomeArquivo = bin2hex(random_bytes(16)) . '.' . $extensao;
        $caminhoCompleto = $diretorioDestino . $nomeArquivo;

        if (!move_uploaded_file($arquivo['tmp_name'], $caminhoCompleto)) {
            throw new Exception("Houve uma falha ao realizar o upload da foto.");
        }

        return $caminhoCompleto;
    }
}
?>