<?php
// Controller de Painel e Ações Autenticadas

require_once "model/dao/DenunciaDAO.php";
require_once "model/dao/CategoriaDAO.php";
require_once "model/dao/ComentarioDAO.php";
require_once "model/dao/CurtidaDenunciaDAO.php";
require_once "model/dao/CurtidaComentarioDAO.php";
require_once "model/dto/ComentarioDTO.php";
require_once "model/dto/CurtidaDenunciaDTO.php";
require_once "model/dto/CurtidaComentarioDTO.php";

class PainelController
{
    private $denunciaDAO;
    private $categoriaDAO;
    private $comentarioDAO;
    private $curtidaDenunciaDAO;
    private $curtidaComentarioDAO;

    public function __construct()
    {
        // Middleware de verificação de sessão (protege todas as ações deste controller)
        if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
            header("Location: index.php?rota=login");
            exit();
        }

        $this->denunciaDAO = new DenunciaDAO();
        $this->categoriaDAO = new CategoriaDAO();
        $this->comentarioDAO = new ComentarioDAO();
        $this->curtidaDenunciaDAO = new CurtidaDenunciaDAO();
        $this->curtidaComentarioDAO = new CurtidaComentarioDAO();
    }

    /**
     * Exibe o mapa apenas para usuários autenticados.
     */
    public function exibirMapa()
    {
        $tituloPagina = 'Mapa de Denuncias';
        include 'view/painel/mapa.php';
    }

    /**
     * Carrega a página principal do painel (Dashboard).
     */
    public function index()
    {
        try {
            $idCategoriaFiltro = $this->normalizarFiltroCategoria($_GET['categoria'] ?? null);
            $paginaAtual = $this->normalizarPagina($_GET['pagina'] ?? 1);
            $limitePagina = $this->normalizarLimite($_GET['limite'] ?? 10);
            $ordenacaoFiltro = $this->normalizarOrdenacao($_GET['ordem'] ?? 'recentes');

            $totalDenuncias = $this->denunciaDAO->contarPaginadas($idCategoriaFiltro);
            $totalPaginas = $totalDenuncias > 0 ? (int) ceil($totalDenuncias / $limitePagina) : 0;

            if ($totalPaginas > 0 && $paginaAtual > $totalPaginas) {
                $paginaAtual = $totalPaginas;
            }

            $denuncias = $this->denunciaDAO->listarPaginadas($idCategoriaFiltro, $paginaAtual, $limitePagina, $ordenacaoFiltro);
            $categorias = $this->categoriaDAO->listarTodas();
            $interacoes = $this->carregarInteracoesDenuncias($denuncias);
            $tituloPagina = "Painel do Usuário";
            $usuarioNome = $_SESSION['usuario_nome'];
            $filtroCategoriaSelecionada = $idCategoriaFiltro;
            $filtroLimiteSelecionado = $limitePagina;
            $filtroOrdenacaoSelecionada = $ordenacaoFiltro;
            $painelQueryRetorno = $this->montarUrlRetornoPainel();
            $painelQueryFiltros = $this->montarUrlRetornoPainel(['rota' => null]);

            include "view/painel/index.php";

        } catch (Exception $e) {
            $_SESSION['mensagem'] = "Erro ao carregar os dados do painel: " . $e->getMessage();
            $_SESSION['tipo_mensagem'] = "erro";
            header("Location: index.php?" . $this->montarUrlRetornoPainel(['rota' => 'inicio']));
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
                $latitude = trim($_POST['latitude'] ?? '');
                $longitude = trim($_POST['longitude'] ?? '');

                if (empty($titulo) || empty($descricao) || empty($localizacao) || empty($idCategoria)) {
                    throw new Exception("Os campos título, descrição, localização e categoria são obrigatórios.");
                }

                if (mb_strlen($titulo) > 150) {
                    throw new Exception('O título deve ter no máximo 150 caracteres.');
                }

                if (mb_strlen($descricao) > 5000) {
                    throw new Exception('A descrição deve ter no máximo 5000 caracteres.');
                }

                if (mb_strlen($localizacao) > 255) {
                    throw new Exception('A localização deve ter no máximo 255 caracteres.');
                }

                if (filter_var($idCategoria, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) === false) {
                    throw new Exception('Categoria inválida.');
                }

                // Valida coordenadas se foram fornecidas
                if (!empty($latitude) && !empty($longitude)) {
                    $coordsValidas = $this->validarCoordenadas($latitude, $longitude);
                    if (!$coordsValidas['valida']) {
                        throw new Exception($coordsValidas['mensagem']);
                    }
                    $latitude = (float) $latitude;
                    $longitude = (float) $longitude;
                } else {
                    $latitude = null;
                    $longitude = null;
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

                // Define coordenadas se foram validadas
                if ($latitude !== null && $longitude !== null) {
                    $denuncia->setLatitude($latitude);
                    $denuncia->setLongitude($longitude);
                }

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
            header("Location: index.php?" . $this->montarUrlRetornoPainel());
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
            header("Location: index.php?" . $this->montarUrlRetornoPainel());
            exit();
        }

        try {
            $idDenuncia = (int) ($_POST['id_denuncia'] ?? 0);
            $titulo = trim($_POST['titulo'] ?? '');
            $descricao = trim($_POST['descricao'] ?? '');
            $localizacao = trim($_POST['localizacao'] ?? '');
            $idCategoria = (int) ($_POST['id_categoria'] ?? 0);
            $status = trim($_POST['status'] ?? 'Aberto');
            $latitude = trim($_POST['latitude'] ?? '');
            $longitude = trim($_POST['longitude'] ?? '');

            if ($idDenuncia <= 0 || $idCategoria <= 0 || empty($titulo) || empty($descricao) || empty($localizacao)) {
                throw new Exception("Dados obrigatórios da denúncia não informados corretamente.");
            }

            if (mb_strlen($titulo) > 150) {
                throw new Exception('O título deve ter no máximo 150 caracteres.');
            }

            if (mb_strlen($descricao) > 5000) {
                throw new Exception('A descrição deve ter no máximo 5000 caracteres.');
            }

            if (mb_strlen($localizacao) > 255) {
                throw new Exception('A localização deve ter no máximo 255 caracteres.');
            }

            $denunciaAtual = $this->denunciaDAO->buscarPorId($idDenuncia);
            if ($denunciaAtual === null) {
                throw new Exception("Denúncia não encontrada.");
            }

            $this->validarPermissaoDenuncia($denunciaAtual->getIdUsuario());

            // Valida coordenadas se foram fornecidas
            if (!empty($latitude) && !empty($longitude)) {
                $coordsValidas = $this->validarCoordenadas($latitude, $longitude);
                if (!$coordsValidas['valida']) {
                    throw new Exception($coordsValidas['mensagem']);
                }
                $latitude = (float) $latitude;
                $longitude = (float) $longitude;
            } else {
                // Se não forneceu coordenadas, mantém as antigas
                $latitude = $denunciaAtual->getLatitude();
                $longitude = $denunciaAtual->getLongitude();
            }

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
            $denuncia->setLatitude($latitude);
            $denuncia->setLongitude($longitude);

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

        header("Location: index.php?" . $this->montarUrlRetornoPainel());
        exit();
    }

    /**
     * Processa exclusão de denúncia.
     */
    public function excluirDenuncia()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?" . $this->montarUrlRetornoPainel());
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

        header("Location: index.php?" . $this->montarUrlRetornoPainel());
        exit();
    }

    /**
     * Processa o cadastro de um comentário em uma denúncia.
     */
    public function comentarDenuncia()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            if ($this->requisicaoAceitaJson()) {
                $this->responderJson([
                    'success' => false,
                    'message' => 'Método não permitido.'
                ], 405);
            }

            header("Location: index.php?" . $this->montarUrlRetornoPainel());
            exit();
        }

        try {
            $idDenuncia = (int) ($_POST['id_denuncia'] ?? 0);
            $texto = trim($_POST['texto'] ?? '');

            if ($idDenuncia <= 0 || $texto === '') {
                throw new Exception('Informe a denúncia e o texto do comentário.');
            }

            if (mb_strlen($texto) > 2000) {
                throw new Exception('O comentário deve ter no máximo 2000 caracteres.');
            }

            if ($this->denunciaDAO->buscarPorId($idDenuncia) === null) {
                throw new Exception('Denúncia não encontrada.');
            }

            $comentario = new ComentarioDTO();
            $comentario->setTexto($texto);
            $comentario->setIdDenuncia($idDenuncia);
            $comentario->setIdUsuario((int) ($_SESSION['usuario_id'] ?? 0));

            $salvou = $this->comentarioDAO->cadastrar($comentario);
            if (!$salvou) {
                throw new Exception('Não foi possível cadastrar o comentário.');
            }

            $comentarioCriado = $this->comentarioDAO->buscarPorId((int) $salvou);
            if ($comentarioCriado === null) {
                throw new Exception('Não foi possível recuperar o comentário cadastrado.');
            }

            if ($this->requisicaoAceitaJson()) {
                $this->responderJson([
                    'success' => true,
                    'message' => 'Comentário publicado com sucesso!',
                    'id_denuncia' => $idDenuncia,
                    'comentario' => $this->comentarioParaResposta($comentarioCriado),
                ], 201);
            }

            $_SESSION['mensagem'] = 'Comentário publicado com sucesso!';
            $_SESSION['tipo_mensagem'] = 'sucesso';

        } catch (Exception $e) {
            if ($this->requisicaoAceitaJson()) {
                $this->responderJson([
                    'success' => false,
                    'message' => 'Erro: ' . $e->getMessage(),
                ], 400);
            }

            $_SESSION['mensagem'] = 'Erro: ' . $e->getMessage();
            $_SESSION['tipo_mensagem'] = 'erro';
        }

        header("Location: index.php?" . $this->montarUrlRetornoPainel());
        exit();
    }

    /**
     * Alterna a curtida da denúncia.
     */
    public function curtirDenuncia()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            if ($this->requisicaoAceitaJson()) {
                $this->responderJson([
                    'success' => false,
                    'message' => 'Método não permitido.'
                ], 405);
            }

            header("Location: index.php?" . $this->montarUrlRetornoPainel());
            exit();
        }

        try {
            $idDenuncia = (int) ($_POST['id_denuncia'] ?? 0);
            $idUsuario = (int) ($_SESSION['usuario_id'] ?? 0);

            if ($idDenuncia <= 0 || $idUsuario <= 0) {
                throw new Exception('Não foi possível processar a curtida.');
            }

            if ($this->denunciaDAO->buscarPorId($idDenuncia) === null) {
                throw new Exception('Denúncia não encontrada.');
            }

            if ($this->curtidaDenunciaDAO->usuarioCurtiu($idUsuario, $idDenuncia)) {
                $this->curtidaDenunciaDAO->removerCurtida($idUsuario, $idDenuncia);
                $mensagem = 'Curtida removida.';
            } else {
                $curtida = new CurtidaDenunciaDTO();
                $curtida->setIdUsuario($idUsuario);
                $curtida->setIdDenuncia($idDenuncia);
                $this->curtidaDenunciaDAO->curtir($curtida);
                $mensagem = 'Denúncia curtida com sucesso!';
            }

            $totalCurtidas = $this->curtidaDenunciaDAO->contarCurtidas($idDenuncia);
            $usuarioCurtiu = $this->curtidaDenunciaDAO->usuarioCurtiu($idUsuario, $idDenuncia);

            if ($this->requisicaoAceitaJson()) {
                $this->responderJson([
                    'success' => true,
                    'message' => $mensagem,
                    'id_denuncia' => $idDenuncia,
                    'total_curtidas' => $totalCurtidas,
                    'usuario_curtiu' => $usuarioCurtiu,
                ]);
            }

            $_SESSION['mensagem'] = $mensagem;
            $_SESSION['tipo_mensagem'] = 'sucesso';

        } catch (Exception $e) {
            if ($this->requisicaoAceitaJson()) {
                $this->responderJson([
                    'success' => false,
                    'message' => 'Erro: ' . $e->getMessage(),
                ], 400);
            }

            $_SESSION['mensagem'] = 'Erro: ' . $e->getMessage();
            $_SESSION['tipo_mensagem'] = 'erro';
        }

        header("Location: index.php?" . $this->montarUrlRetornoPainel());
        exit();
    }

    /**
     * Alterna a curtida de um comentário.
     */
    public function curtirComentario()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            if ($this->requisicaoAceitaJson()) {
                $this->responderJson([
                    'success' => false,
                    'message' => 'Método não permitido.'
                ], 405);
            }

            header("Location: index.php?" . $this->montarUrlRetornoPainel());
            exit();
        }

        try {
            $idComentario = (int) ($_POST['id_comentario'] ?? 0);
            $idUsuario = (int) ($_SESSION['usuario_id'] ?? 0);

            if ($idComentario <= 0 || $idUsuario <= 0) {
                throw new Exception('Não foi possível processar a curtida.');
            }

            if ($this->comentarioDAO->buscarPorId($idComentario) === null) {
                throw new Exception('Comentário não encontrado.');
            }

            if ($this->curtidaComentarioDAO->usuarioCurtiu($idUsuario, $idComentario)) {
                $this->curtidaComentarioDAO->removerCurtida($idUsuario, $idComentario);
                $mensagem = 'Curtida removida.';
            } else {
                $curtida = new CurtidaComentarioDTO();
                $curtida->setIdUsuario($idUsuario);
                $curtida->setIdComentario($idComentario);
                $this->curtidaComentarioDAO->curtir($curtida);
                $mensagem = 'Comentário curtido com sucesso!';
            }

            $totalCurtidas = $this->curtidaComentarioDAO->contarCurtidas($idComentario);
            $usuarioCurtiu = $this->curtidaComentarioDAO->usuarioCurtiu($idUsuario, $idComentario);

            if ($this->requisicaoAceitaJson()) {
                $this->responderJson([
                    'success' => true,
                    'message' => $mensagem,
                    'id_comentario' => $idComentario,
                    'total_curtidas' => $totalCurtidas,
                    'usuario_curtiu' => $usuarioCurtiu,
                ]);
            }

            $_SESSION['mensagem'] = $mensagem;
            $_SESSION['tipo_mensagem'] = 'sucesso';

        } catch (Exception $e) {
            if ($this->requisicaoAceitaJson()) {
                $this->responderJson([
                    'success' => false,
                    'message' => 'Erro: ' . $e->getMessage(),
                ], 400);
            }

            $_SESSION['mensagem'] = 'Erro: ' . $e->getMessage();
            $_SESSION['tipo_mensagem'] = 'erro';
        }

        header("Location: index.php?" . $this->montarUrlRetornoPainel());
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
     * Normaliza a categoria do filtro.
     *
     * @param mixed $valor
     * @return int|null
     */
    private function normalizarFiltroCategoria($valor)
    {
        if ($valor === null || $valor === '') {
            return null;
        }

        $categoria = filter_var($valor, FILTER_VALIDATE_INT, [
            'options' => [
                'min_range' => 1,
            ],
        ]);

        return $categoria === false ? null : (int) $categoria;
    }

    /**
     * Normaliza o número da página.
     *
     * @param mixed $valor
     * @return int
     */
    private function normalizarPagina($valor)
    {
        $pagina = filter_var($valor, FILTER_VALIDATE_INT, [
            'options' => [
                'min_range' => 1,
            ],
        ]);

        return $pagina === false ? 1 : (int) $pagina;
    }

    /**
     * Normaliza a quantidade de itens por página.
     *
     * @param mixed $valor
     * @return int
     */
    private function normalizarLimite($valor)
    {
        $limite = (int) $valor;
        $limitesPermitidos = [10, 25, 50];

        if (!in_array($limite, $limitesPermitidos, true)) {
            return 10;
        }

        return $limite;
    }

    /**
     * Normaliza a ordenação do painel.
     *
     * @param mixed $valor
     * @return string
     */
    private function normalizarOrdenacao($valor)
    {
        $valor = is_string($valor) ? strtolower(trim($valor)) : 'recentes';

        $ordenacoesPermitidas = ['recentes', 'antigas'];
        if (!in_array($valor, $ordenacoesPermitidas, true)) {
            return 'recentes';
        }

        return $valor;
    }

    /**
     * Monta a query string de retorno do painel preservando filtros e paginação.
     *
     * @param array $substituicoes
     * @return string
     */
    private function montarUrlRetornoPainel(array $substituicoes = [])
    {
        $parametros = [
            'rota' => 'painel',
        ];

        $categoria = $this->normalizarFiltroCategoria($_GET['categoria'] ?? null);
        $pagina = $this->normalizarPagina($_GET['pagina'] ?? 1);
        $limite = $this->normalizarLimite($_GET['limite'] ?? 10);

        if ($categoria !== null) {
            $parametros['categoria'] = $categoria;
        }

        if ($pagina > 1) {
            $parametros['pagina'] = $pagina;
        }

        if ($limite !== 10) {
            $parametros['limite'] = $limite;
        }

        $ordem = $this->normalizarOrdenacao($_GET['ordem'] ?? 'recentes');
        if ($ordem !== 'recentes') {
            $parametros['ordem'] = $ordem;
        }

        foreach ($substituicoes as $chave => $valor) {
            if ($valor === null) {
                unset($parametros[$chave]);
                continue;
            }

            $parametros[$chave] = $valor;
        }

        return http_build_query($parametros);
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

        $tipoImagem = exif_imagetype($arquivo['tmp_name']);
        if ($tipoImagem !== IMAGETYPE_JPEG && $tipoImagem !== IMAGETYPE_PNG) {
            throw new Exception('Formato de imagem inválido.');
        }

        $dimensoes = getimagesize($arquivo['tmp_name']);
        if ($dimensoes === false) {
            throw new Exception('Não foi possível validar a imagem enviada.');
        }

        if (($dimensoes[0] ?? 0) > 4000 || ($dimensoes[1] ?? 0) > 3000) {
            throw new Exception('A imagem deve ter no máximo 4000x3000 pixels.');
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

    /**
     * Indica se a requisição espera resposta JSON.
     */
    private function requisicaoAceitaJson()
    {
        $accept = strtolower($_SERVER['HTTP_ACCEPT'] ?? '');
        $xRequestedWith = strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '');

        return strpos($accept, 'application/json') !== false || $xRequestedWith === 'xmlhttprequest';
    }

    /**
     * Envia uma resposta JSON e encerra a execução.
     *
     * @param array $dados
     * @param int $statusCode
     */
    private function responderJson(array $dados, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($dados, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit();
    }

    /**
     * Converte um comentário em array para retorno via JSON.
     *
     * @param ComentarioDTO $comentario
     * @return array
     */
    private function comentarioParaResposta(ComentarioDTO $comentario)
    {
        return [
            'id' => (int) $comentario->getId(),
            'texto' => $comentario->getTexto(),
            'data_comentario' => $comentario->getDataComentario(),
            'nome_usuario' => $comentario->getNomeUsuario() ?: 'Usuário',
            'id_usuario' => (int) $comentario->getIdUsuario(),
            'id_denuncia' => (int) $comentario->getIdDenuncia(),
            'total_curtidas' => 0,
            'usuario_curtiu' => false,
        ];
    }

    /**
     * Carrega comentários e curtidas de cada denúncia do painel.
     *
     * @param array $denuncias
     * @return array
     */
    private function carregarInteracoesDenuncias($denuncias)
    {
        $interacoes = [];
        $idUsuarioSessao = (int) ($_SESSION['usuario_id'] ?? 0);

        $idsDenuncia = [];
        foreach ($denuncias as $denuncia) {
            $idsDenuncia[] = (int) $denuncia->getId();
        }

        if ($idsDenuncia === []) {
            return $interacoes;
        }

        $comentariosPorDenuncia = $this->comentarioDAO->listarPorDenuncias($idsDenuncia);
        $totalCurtidasPorDenuncia = $this->curtidaDenunciaDAO->contarCurtidasPorDenuncias($idsDenuncia);
        $usuarioCurtiuDenuncia = $idUsuarioSessao > 0
            ? $this->curtidaDenunciaDAO->usuarioCurtiuPorDenuncias($idUsuarioSessao, $idsDenuncia)
            : [];

        $idsComentario = [];
        foreach ($comentariosPorDenuncia as $comentariosDaDenuncia) {
            foreach ($comentariosDaDenuncia as $comentario) {
                $idsComentario[] = (int) $comentario->getId();
            }
        }

        $totalCurtidasPorComentario = $this->curtidaComentarioDAO->contarCurtidasPorComentarios($idsComentario);
        $usuarioCurtiuComentario = $idUsuarioSessao > 0
            ? $this->curtidaComentarioDAO->usuarioCurtiuPorComentarios($idUsuarioSessao, $idsComentario)
            : [];

        foreach ($denuncias as $denuncia) {
            $idDenuncia = (int) $denuncia->getId();
            $comentarios = $comentariosPorDenuncia[$idDenuncia] ?? [];

            $comentariosComCurtidas = [];
            foreach ($comentarios as $comentario) {
                $idComentario = (int) $comentario->getId();
                $comentariosComCurtidas[] = [
                    'comentario' => $comentario,
                    'totalCurtidas' => (int) ($totalCurtidasPorComentario[$idComentario] ?? 0),
                    'usuarioCurtiu' => (bool) ($usuarioCurtiuComentario[$idComentario] ?? false),
                ];
            }

            $interacoes[$idDenuncia] = [
                'comentarios' => $comentariosComCurtidas,
                'totalCurtidas' => (int) ($totalCurtidasPorDenuncia[$idDenuncia] ?? 0),
                'usuarioCurtiu' => (bool) ($usuarioCurtiuDenuncia[$idDenuncia] ?? false),
            ];
        }

        return $interacoes;
    }

    /**
     * Valida se as coordenadas (latitude, longitude) estão nos intervalos permitidos.
     * 
     * @param mixed $latitude
     * @param mixed $longitude
     * @return array ['valida' => bool, 'mensagem' => string]
     */
    private function validarCoordenadas($latitude, $longitude)
    {
        // Valida se são números válidos antes da conversão
        if (!is_numeric($latitude) || !is_numeric($longitude)) {
            return [
                'valida' => false,
                'mensagem' => 'Latitude e longitude devem ser números válidos.'
            ];
        }

        // Converte para float
        $lat = (float) $latitude;
        $lon = (float) $longitude;

        // Valida intervalos
        if ($lat < -90 || $lat > 90) {
            return [
                'valida' => false,
                'mensagem' => 'Latitude deve estar entre -90 e 90.'
            ];
        }

        if ($lon < -180 || $lon > 180) {
            return [
                'valida' => false,
                'mensagem' => 'Longitude deve estar entre -180 e 180.'
            ];
        }

        return ['valida' => true, 'mensagem' => ''];
    }
}
?>