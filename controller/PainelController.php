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
                    // Validação de tamanho (máx. 5 MB)
                    $tamanhoMaximo = 5 * 1024 * 1024;
                    if ($_FILES['foto']['size'] > $tamanhoMaximo) {
                        throw new Exception("A foto deve ter no máximo 5 MB.");
                    }

                    // Validação de extensão (whitelist)
                    $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                    $extensao = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
                    if (!in_array($extensao, $extensoesPermitidas)) {
                        throw new Exception("Formato de arquivo não permitido. Use JPG, PNG, GIF ou WEBP.");
                    }

                    // Validação de MIME type real (lê bytes do arquivo, não confia no nome)
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mimeType = finfo_file($finfo, $_FILES['foto']['tmp_name']);
                    finfo_close($finfo);
                    $mimesPermitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                    if (!in_array($mimeType, $mimesPermitidos)) {
                        throw new Exception("O arquivo enviado não é uma imagem válida.");
                    }

                    $diretorioDestino = 'uploads/';
                    if (!is_dir($diretorioDestino)) {
                        mkdir($diretorioDestino, 0755, true);
                    }
                    $nomeArquivo = bin2hex(random_bytes(16)) . '.' . $extensao;
                    $caminhoCompleto = $diretorioDestino . $nomeArquivo;

                    if (move_uploaded_file($_FILES['foto']['tmp_name'], $caminhoCompleto)) {
                        $fotoPath = $caminhoCompleto;
                    } else {
                        throw new Exception("Houve uma falha ao realizar o upload da foto.");
                    }
                }

                $denuncia = new DenunciaDTO();
                $denuncia->setTitulo($titulo);
                $denuncia->setDescricao($descricao);
                $denuncia->setLocalizacao($localizacao);
                $denuncia->setFotoPath($fotoPath);
                $denuncia->setIdCategoria($idCategoria);
                $denuncia->setStatus('Pendente'); // Status inicial de toda denúncia cadastrada
                // A sessão deve possuir o id do usuário no momento do login ("usuario_id")
                $denuncia->setIdUsuario($_SESSION['usuario_id'] ?? null);

                $this->denunciaDAO->cadastrar($denuncia);

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
}
?>