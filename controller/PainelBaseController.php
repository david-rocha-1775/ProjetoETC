<?php
// Base compartilhada das ações de painel autenticado.

require_once "model/dao/DenunciaDAO.php";
require_once "model/dao/CategoriaDAO.php";
require_once "model/dao/ComentarioDAO.php";
require_once "model/dao/CurtidaDenunciaDAO.php";
require_once "model/dao/CurtidaComentarioDAO.php";
require_once "model/dao/UsuarioDAO.php";
require_once "model/dto/ComentarioDTO.php";
require_once "model/dto/CurtidaDenunciaDTO.php";
require_once "model/dto/CurtidaComentarioDTO.php";
require_once "config/traits/ValidadorRequisicao.php";
require_once "config/ResponseHelper.php";

abstract class PainelBaseController
{
    use ValidadorRequisicao;

    protected $denunciaDAO;
    protected $categoriaDAO;
    protected $comentarioDAO;
    protected $curtidaDenunciaDAO;
    protected $curtidaComentarioDAO;
    protected $usuarioDAO;

    public function __construct()
    {
        if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
            header("Location: index.php?rota=login");
            exit();
        }

        $this->denunciaDAO = new DenunciaDAO();
        $this->categoriaDAO = new CategoriaDAO();
        $this->comentarioDAO = new ComentarioDAO();
        $this->curtidaDenunciaDAO = new CurtidaDenunciaDAO();
        $this->curtidaComentarioDAO = new CurtidaComentarioDAO();
        $this->usuarioDAO = new UsuarioDAO();
    }

    protected function montarUrlRetornoPosAcao($idDenunciaFallback = 0)
    {
        $retornoRota = isset($_POST['retorno_rota']) ? (string) $_POST['retorno_rota'] : '';
        $retornoId = (int) ($_POST['retorno_id'] ?? $idDenunciaFallback);

        if ($retornoRota === 'detalhe_denuncia' && $retornoId > 0) {
            return http_build_query([
                'rota' => 'detalhe_denuncia',
                'id' => $retornoId,
            ]);
        }

        return $this->montarUrlRetornoPainel();
    }

    protected function validarPermissaoDenuncia($idUsuarioDono)
    {
        $idUsuarioSessao = (int) ($_SESSION['usuario_id'] ?? 0);
        $usuarioAdmin = isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] === 'admin';

        if (!$usuarioAdmin && $idUsuarioSessao !== (int) $idUsuarioDono) {
            throw new Exception("Você não tem permissão para alterar esta denúncia.");
        }
    }

    protected function normalizarFiltroCategoria($valor)
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

    protected function normalizarPagina($valor)
    {
        $pagina = filter_var($valor, FILTER_VALIDATE_INT, [
            'options' => [
                'min_range' => 1,
            ],
        ]);

        return $pagina === false ? 1 : (int) $pagina;
    }

    protected function normalizarLimite($valor)
    {
        $limite = (int) $valor;
        $limitesPermitidos = [10, 25, 50];

        if (!in_array($limite, $limitesPermitidos, true)) {
            return 10;
        }

        return $limite;
    }

    protected function normalizarOrdenacao($valor)
    {
        $valor = is_string($valor) ? strtolower(trim($valor)) : 'recentes';

        $ordenacoesPermitidas = ['recentes', 'antigas'];
        if (!in_array($valor, $ordenacoesPermitidas, true)) {
            return 'recentes';
        }

        return $valor;
    }

    protected function montarUrlRetornoPainel(array $substituicoes = [])
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

    protected function processarUploadImagem($arquivo)
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

    protected function requisicaoAceitaJson()
    {
        $accept = strtolower($_SERVER['HTTP_ACCEPT'] ?? '');
        $xRequestedWith = strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '');

        return strpos($accept, 'application/json') !== false || $xRequestedWith === 'xmlhttprequest';
    }

    protected function responderJson(array $dados, $statusCode = 200)
    {
        ResponseHelper::responderJson($dados, $statusCode);
    }

    protected function comentarioParaResposta(ComentarioDTO $comentario)
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

    protected function carregarInteracoesDenuncias($denuncias)
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

    protected function carregarResumoInteracoesDenuncias($denuncias)
    {
        $resumo = [];
        $idUsuarioSessao = (int) ($_SESSION['usuario_id'] ?? 0);

        $idsDenuncia = [];
        foreach ($denuncias as $denuncia) {
            $idsDenuncia[] = (int) $denuncia->getId();
        }

        if ($idsDenuncia === []) {
            return $resumo;
        }

        $totalCurtidasPorDenuncia = $this->curtidaDenunciaDAO->contarCurtidasPorDenuncias($idsDenuncia);
        $totalComentariosPorDenuncia = $this->comentarioDAO->contarPorDenuncias($idsDenuncia);
        $usuarioCurtiuDenuncia = $idUsuarioSessao > 0
            ? $this->curtidaDenunciaDAO->usuarioCurtiuPorDenuncias($idUsuarioSessao, $idsDenuncia)
            : [];

        foreach ($idsDenuncia as $idDenuncia) {
            $resumo[$idDenuncia] = [
                'totalCurtidas' => (int) ($totalCurtidasPorDenuncia[$idDenuncia] ?? 0),
                'totalComentarios' => (int) ($totalComentariosPorDenuncia[$idDenuncia] ?? 0),
                'usuarioCurtiu' => (bool) ($usuarioCurtiuDenuncia[$idDenuncia] ?? false),
            ];
        }

        return $resumo;
    }

    protected function carregarAutoresPorDenuncia($denuncias)
    {
        $resultado = [];
        $idsUsuario = [];

        foreach ($denuncias as $denuncia) {
            $idsUsuario[] = (int) $denuncia->getIdUsuario();
        }

        $nomesPorUsuario = $this->usuarioDAO->buscarNomesPorIds($idsUsuario);

        foreach ($denuncias as $denuncia) {
            $idDenuncia = (int) $denuncia->getId();
            $idUsuario = (int) $denuncia->getIdUsuario();
            $resultado[$idDenuncia] = $nomesPorUsuario[$idUsuario] ?? 'Usuário';
        }

        return $resultado;
    }

    protected function mapearCategoriasPorId($categorias)
    {
        $resultado = [];

        foreach ($categorias as $categoria) {
            $resultado[(int) $categoria->getId()] = (string) $categoria->getNomeCategoria();
        }

        return $resultado;
    }

    protected function validarCoordenadas($latitude, $longitude)
    {
        if (!is_numeric($latitude) || !is_numeric($longitude)) {
            return [
                'valida' => false,
                'mensagem' => 'Latitude e longitude devem ser números válidos.'
            ];
        }

        $lat = (float) $latitude;
        $lon = (float) $longitude;

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
