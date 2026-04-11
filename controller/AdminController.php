<?php
// Controller Administrativo (Ações restritas a administradores)

require_once "model/dao/UsuarioDAO.php";
require_once "model/dao/CategoriaDAO.php";
require_once "model/dao/DenunciaDAO.php";
require_once "model/dao/ComentarioDAO.php";
require_once "model/dto/CategoriaDTO.php";
require_once "config/traits/ValidadorRequisicao.php";

class AdminController
{
    use ValidadorRequisicao;
    private const LIMITES_PAGINA = [10, 20, 50];

    private $usuarioDAO;
    private $categoriaDAO;
    private $denunciaDAO;
    private $comentarioDAO;

    public function __construct()
    {
        $this->usuarioDAO = new UsuarioDAO();
        $this->categoriaDAO = new CategoriaDAO();
        $this->denunciaDAO = new DenunciaDAO();
        $this->comentarioDAO = new ComentarioDAO();

        $this->exigirAcessoAdmin();
    }

    /**
     * Exibe o dashboard com métricas administrativas básicas.
     */
    public function dashboard()
    {
        try {
            $denunciasAtivas = $this->denunciaDAO->contarPaginadas();
            $denunciasPorStatus = $this->denunciaDAO->contarPorStatusAdmin();
            $usuariosAtivos = $this->usuarioDAO->contarAtivos();
            $comentariosAtivos = $this->comentarioDAO->contarAtivos();

            $tituloPagina = 'Dashboard Administrativo';
            include 'view/admin/dashboard.php';
            return;

        } catch (Throwable $e) {
            $this->redirecionarComErro('Não foi possível carregar o dashboard administrativo.', 'painel');
        }
    }

    /**
     * Lista usuários cadastrados (uso administrativo).
     */
    public function listarUsuarios()
    {
        try {
            $usuarios = $this->usuarioDAO->listarUsuarios();
            $idUsuarioSessao = (int) ($_SESSION['usuario_id'] ?? 0);
            $tituloPagina = 'Usuários Cadastrados';
            include 'view/admin/usuarios.php';
            return;

        } catch (Throwable $e) {
            $this->redirecionarComErro('Não foi possível carregar a listagem de usuários.', 'painel');
        }
    }

    /**
     * Lista categorias cadastradas (uso administrativo).
     */
    public function listarCategorias()
    {
        try {
            $categorias = $this->categoriaDAO->listarTodas();
            $tituloPagina = 'Gerenciamento de Categorias';
            include 'view/admin/categorias.php';
            return;

        } catch (Throwable $e) {
            $this->redirecionarComErro('Não foi possível carregar a listagem de categorias.', 'admin_dashboard');
        }
    }

    /**
     * Lista denúncias com filtros para moderação administrativa.
     */
    public function listarDenuncias()
    {
        try {
            $statusFiltro = $this->normalizarStatusFiltro($_GET['status'] ?? '');
            $idCategoriaFiltro = $this->normalizarCategoriaFiltro($_GET['categoria'] ?? null);
            $buscaFiltro = $this->normalizarBuscaFiltro($_GET['busca'] ?? '');
            $paginaAtual = $this->normalizarPagina($_GET['pagina'] ?? 1);
            $limitePagina = $this->normalizarLimite($_GET['limite'] ?? 10);
            $ordemFiltro = $this->normalizarOrdenacao($_GET['ordem'] ?? 'recentes');

            $totalDenuncias = $this->denunciaDAO->contarPaginadasAdmin(
                $statusFiltro !== '' ? $statusFiltro : null,
                $idCategoriaFiltro,
                $buscaFiltro
            );

            $totalPaginas = $totalDenuncias > 0 ? (int) ceil($totalDenuncias / $limitePagina) : 0;
            if ($totalPaginas > 0 && $paginaAtual > $totalPaginas) {
                $paginaAtual = $totalPaginas;
            }

            $denuncias = $this->denunciaDAO->listarPaginadasAdmin(
                $statusFiltro !== '' ? $statusFiltro : null,
                $idCategoriaFiltro,
                $buscaFiltro,
                $paginaAtual,
                $limitePagina,
                $ordemFiltro
            );

            $categorias = $this->categoriaDAO->listarTodas();
            $interacoesDenuncias = $this->carregarInteracoesDenuncias($denuncias);
            $mapaUsuarios = $this->mapearUsuariosAtivos();
            $mapaCategorias = $this->mapearCategorias($categorias);

            $filtrosAtuais = [
                'status' => $statusFiltro,
                'categoria' => $idCategoriaFiltro,
                'busca' => $buscaFiltro,
                'limite' => $limitePagina,
                'ordem' => $ordemFiltro,
            ];
            $queryFiltrosSemPagina = $this->montarQueryDenuncias($filtrosAtuais, ['pagina']);
            $queryFiltrosComPaginaAtual = $this->montarQueryDenuncias($filtrosAtuais, []) . '&pagina=' . $paginaAtual;

            $tituloPagina = 'Gerenciamento de Denúncias';
            include 'view/admin/denuncias.php';
            return;

        } catch (Throwable $e) {
            $this->redirecionarComErro(
                $this->mensagemErroExibivel($e, 'Não foi possível carregar as denúncias para moderação.'),
                'admin_dashboard'
            );
        }
    }

    /**
     * Atualiza status de uma denúncia (uso administrativo).
     */
    public function atualizarStatusDenuncia()
    {
        $this->exigirMetodoPost('admin_denuncias');

        try {
            $idDenuncia = $this->normalizarId($_POST['id_denuncia'] ?? null, 'Denúncia inválida.');
            $status = $this->validarStatusDenuncia($_POST['status'] ?? '');

            $denuncia = $this->denunciaDAO->buscarPorId($idDenuncia);
            if ($denuncia === null) {
                throw new InvalidArgumentException('Denúncia não encontrada.');
            }

            $atualizou = $this->denunciaDAO->atualizarStatusPorId($idDenuncia, $status);
            if (!$atualizou) {
                throw new RuntimeException('Falha ao atualizar status da denúncia.');
            }

            $this->redirecionarComSucesso(
                'Status da denúncia atualizado com sucesso.',
                'admin_denuncias',
                $this->normalizarQueryRetornoDenuncias($_POST['retorno_filtros'] ?? '')
            );

        } catch (Throwable $e) {
            $this->redirecionarComErro(
                $this->mensagemErroExibivel($e, 'Não foi possível atualizar o status da denúncia.'),
                'admin_denuncias',
                $this->normalizarQueryRetornoDenuncias($_POST['retorno_filtros'] ?? '')
            );
        }
    }

    /**
     * Exclui logicamente comentário (uso administrativo).
     */
    public function excluirComentario()
    {
        $this->exigirMetodoPost('admin_denuncias');

        try {
            $idComentario = $this->normalizarId($_POST['id_comentario'] ?? null, 'Comentário inválido.');
            if ($this->comentarioDAO->buscarPorId($idComentario) === null) {
                throw new InvalidArgumentException('Comentário não encontrado.');
            }

            $excluiu = $this->comentarioDAO->excluirPorId($idComentario);
            if (!$excluiu) {
                throw new RuntimeException('Falha ao excluir comentário.');
            }

            $this->redirecionarComSucesso(
                'Comentário removido com sucesso.',
                'admin_denuncias',
                $this->normalizarQueryRetornoDenuncias($_POST['retorno_filtros'] ?? '')
            );

        } catch (Throwable $e) {
            $this->redirecionarComErro(
                $this->mensagemErroExibivel($e, 'Não foi possível remover o comentário.'),
                'admin_denuncias',
                $this->normalizarQueryRetornoDenuncias($_POST['retorno_filtros'] ?? '')
            );
        }
    }

    /**
     * Desativa um usuário e seus dados relacionados (uso administrativo).
     */
    public function excluirUsuario()
    {
        $this->exigirMetodoPost('listar_usuarios');

        try {
            $idUsuario = $this->normalizarId($_POST['id_usuario'] ?? null, 'Usuário inválido.');
            $idAdminSessao = (int) ($_SESSION['usuario_id'] ?? 0);

            if ($idUsuario === $idAdminSessao) {
                throw new InvalidArgumentException('Não é permitido desativar a própria conta nesta tela.');
            }

            $usuario = $this->usuarioDAO->buscarPorId($idUsuario);
            if ($usuario === null) {
                throw new InvalidArgumentException('Usuário não encontrado.');
            }

            $excluiu = $this->usuarioDAO->excluirPorId($idUsuario);
            if (!$excluiu) {
                throw new RuntimeException('Falha ao desativar usuário.');
            }

            $this->redirecionarComSucesso('Usuário desativado com sucesso.', 'listar_usuarios');

        } catch (Throwable $e) {
            $this->redirecionarComErro(
                $this->mensagemErroExibivel($e, 'Não foi possível desativar o usuário.'),
                'listar_usuarios'
            );
        }
    }

    /**
     * Cadastra uma nova categoria (uso administrativo).
     */
    public function cadastrarCategoria()
    {
        $this->exigirMetodoPost('painel');

        try {
            $nomeCategoria = trim($_POST['nome_categoria'] ?? '');
            if ($nomeCategoria === '') {
                throw new InvalidArgumentException('Informe o nome da categoria.');
            }

            if (mb_strlen($nomeCategoria) < 3 || mb_strlen($nomeCategoria) > 50) {
                throw new InvalidArgumentException('A categoria deve ter entre 3 e 50 caracteres.');
            }

            $categoria = new CategoriaDTO();
            $categoria->setNomeCategoria($nomeCategoria);

            $salvou = $this->categoriaDAO->cadastrar($categoria);
            if (!$salvou) {
                throw new RuntimeException('Falha ao cadastrar categoria.');
            }

            $this->redirecionarComSucesso('Categoria cadastrada com sucesso!', 'listar_categorias_admin');

        } catch (Throwable $e) {
            $this->redirecionarComErro(
                $this->mensagemErroExibivel($e, 'Não foi possível cadastrar a categoria.'),
                'listar_categorias_admin'
            );
        }
    }

    /**
     * Atualiza uma categoria existente (uso administrativo).
     */
    public function atualizarCategoria()
    {
        $this->exigirMetodoPost('painel');

        try {
            $idCategoria = (int) ($_POST['id_categoria'] ?? 0);
            $nomeCategoria = trim($_POST['nome_categoria'] ?? '');

            if ($idCategoria <= 0 || $nomeCategoria === '') {
                throw new InvalidArgumentException('Dados da categoria inválidos.');
            }

            if (mb_strlen($nomeCategoria) < 3 || mb_strlen($nomeCategoria) > 50) {
                throw new InvalidArgumentException('A categoria deve ter entre 3 e 50 caracteres.');
            }

            $categoria = new CategoriaDTO();
            $categoria->setId($idCategoria);
            $categoria->setNomeCategoria($nomeCategoria);

            $atualizou = $this->categoriaDAO->atualizar($categoria);
            if (!$atualizou) {
                throw new RuntimeException('Falha ao atualizar categoria.');
            }

            $this->redirecionarComSucesso('Categoria atualizada com sucesso!', 'listar_categorias_admin');

        } catch (Throwable $e) {
            $this->redirecionarComErro(
                $this->mensagemErroExibivel($e, 'Não foi possível atualizar a categoria.'),
                'listar_categorias_admin'
            );
        }
    }

    /**
     * Exclui uma categoria pelo ID (uso administrativo).
     */
    public function excluirCategoria()
    {
        $this->exigirMetodoPost('painel');

        try {
            $idCategoria = (int) ($_POST['id_categoria'] ?? 0);
            if ($idCategoria <= 0) {
                throw new InvalidArgumentException('ID da categoria inválido.');
            }

            $excluiu = $this->categoriaDAO->excluirPorId($idCategoria);
            if (!$excluiu) {
                throw new RuntimeException('Falha ao excluir categoria.');
            }

            $this->redirecionarComSucesso('Categoria excluída com sucesso!', 'listar_categorias_admin');

        } catch (Throwable $e) {
            $this->redirecionarComErro(
                $this->mensagemErroExibivel($e, 'Não foi possível excluir a categoria.'),
                'listar_categorias_admin'
            );
        }
    }

    /**
     * Carrega comentários agrupados por denúncia para moderacão.
     *
     * @param DenunciaDTO[] $denuncias
     * @return array<int, ComentarioDTO[]>
     */
    private function carregarInteracoesDenuncias($denuncias)
    {
        $idsDenuncia = [];
        foreach ($denuncias as $denuncia) {
            $idsDenuncia[] = (int) $denuncia->getId();
        }

        if ($idsDenuncia === []) {
            return [];
        }

        return $this->comentarioDAO->listarPorDenuncias($idsDenuncia);
    }

    /**
     * Mapeia usuários ativos por ID para exibição em telas administrativas.
     *
     * @return array<int, string>
     */
    private function mapearUsuariosAtivos()
    {
        $usuarios = $this->usuarioDAO->listarUsuarios();
        $resultado = [];

        foreach ($usuarios as $usuario) {
            $resultado[(int) $usuario->getId()] = (string) $usuario->getNome();
        }

        return $resultado;
    }

    /**
     * Mapeia categorias por ID para exibição em telas administrativas.
     *
     * @param CategoriaDTO[] $categorias
     * @return array<int, string>
     */
    private function mapearCategorias($categorias)
    {
        $resultado = [];

        foreach ($categorias as $categoria) {
            $resultado[(int) $categoria->getId()] = (string) $categoria->getNomeCategoria();
        }

        return $resultado;
    }

    /**
     * Retorna mensagem adequada para usuário final.
     */
    private function mensagemErroExibivel(Throwable $erro, $mensagemPadrao)
    {
        if ($erro instanceof InvalidArgumentException) {
            return $erro->getMessage();
        }

        return $mensagemPadrao;
    }

    /**
     * Valida e normaliza status da denúncia.
     */
    private function validarStatusDenuncia($status)
    {
        $status = trim((string) $status);

        if (!in_array($status, DenunciaDAO::STATUS_VALIDOS, true)) {
            throw new InvalidArgumentException('Status de denúncia inválido.');
        }

        return $status;
    }

    /**
     * Normaliza filtro de status da listagem admin.
     */
    private function normalizarStatusFiltro($status)
    {
        $status = trim((string) $status);
        if ($status === '') {
            return '';
        }

        if (!in_array($status, DenunciaDAO::STATUS_VALIDOS, true)) {
            throw new InvalidArgumentException('Filtro de status inválido.');
        }

        return $status;
    }

    /**
     * Normaliza filtro de categoria.
     */
    private function normalizarCategoriaFiltro($idCategoria)
    {
        if ($idCategoria === null || $idCategoria === '') {
            return null;
        }

        if (filter_var($idCategoria, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) === false) {
            throw new InvalidArgumentException('Filtro de categoria inválido.');
        }

        return (int) $idCategoria;
    }

    /**
     * Normaliza filtro de busca textual.
     */
    private function normalizarBuscaFiltro($busca)
    {
        $busca = trim((string) $busca);
        if ($busca === '') {
            return '';
        }

        if (mb_strlen($busca) > 120) {
            throw new InvalidArgumentException('A busca deve ter no máximo 120 caracteres.');
        }

        return $busca;
    }

    /**
     * Normaliza número de página.
     */
    private function normalizarPagina($pagina)
    {
        if (filter_var($pagina, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) === false) {
            return 1;
        }

        return (int) $pagina;
    }

    /**
     * Normaliza limite permitido por página.
     */
    private function normalizarLimite($limite)
    {
        $limite = (int) $limite;
        if (!in_array($limite, self::LIMITES_PAGINA, true)) {
            return 10;
        }

        return $limite;
    }

    /**
     * Normaliza ordenação da listagem administrativa.
     */
    private function normalizarOrdenacao($ordenacao)
    {
        $ordenacao = strtolower(trim((string) $ordenacao));
        if (!in_array($ordenacao, ['recentes', 'antigas'], true)) {
            return 'recentes';
        }

        return $ordenacao;
    }

    /**
     * Normaliza ID recebido por formulário.
     */
    private function normalizarId($valor, $mensagem)
    {
        if (filter_var($valor, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) === false) {
            throw new InvalidArgumentException($mensagem);
        }

        return (int) $valor;
    }

    /**
     * Monta query string de filtros para retorno da listagem de denúncias.
     *
     * @param array $filtros
     * @param array $ignorar
     * @return string
     */
    private function montarQueryDenuncias(array $filtros, array $ignorar)
    {
        $query = [];

        foreach ($filtros as $chave => $valor) {
            if (in_array($chave, $ignorar, true)) {
                continue;
            }

            if ($valor === null || $valor === '') {
                continue;
            }

            $query[$chave] = $valor;
        }

        return http_build_query($query);
    }

    /**
     * Sanitiza query de retorno enviada por formulários da tela de denúncias.
     *
     * @param string $query
     * @return array
     */
    private function normalizarQueryRetornoDenuncias($query)
    {
        $query = trim((string) $query);
        if ($query === '') {
            return [];
        }

        $dados = [];
        parse_str($query, $dados);

        $retorno = [];

        try {
            $status = $this->normalizarStatusFiltro($dados['status'] ?? '');
            if ($status !== '') {
                $retorno['status'] = $status;
            }
        } catch (Throwable $e) {
        }

        try {
            $categoria = $this->normalizarCategoriaFiltro($dados['categoria'] ?? null);
            if ($categoria !== null) {
                $retorno['categoria'] = $categoria;
            }
        } catch (Throwable $e) {
        }

        try {
            $busca = $this->normalizarBuscaFiltro($dados['busca'] ?? '');
            if ($busca !== '') {
                $retorno['busca'] = $busca;
            }
        } catch (Throwable $e) {
        }

        $retorno['limite'] = $this->normalizarLimite($dados['limite'] ?? 10);
        $retorno['ordem'] = $this->normalizarOrdenacao($dados['ordem'] ?? 'recentes');
        $retorno['pagina'] = $this->normalizarPagina($dados['pagina'] ?? 1);

        return $retorno;
    }

    /**
     * Garante que a sessão esteja autenticada e com perfil administrativo.
     */
    private function exigirAcessoAdmin()
    {
        if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true || !isset($_SESSION['usuario_id'])) {
            $this->redirecionarComErro('Faça login para continuar.', 'login');
        }
        if (!isset($_SESSION['usuario_tipo']) || $_SESSION['usuario_tipo'] !== 'admin') {
            $this->redirecionarComErro('Acesso restrito a administradores.', 'painel');
        }
    }

    /**
     * Helper consolidado para redirecionamento com mensagens.
     */
    private function redirecionarComMensagem($mensagem, $rota, $tipo = 'erro', array $query = [])
    {
        $_SESSION['mensagem'] = $mensagem;
        $_SESSION['tipo_mensagem'] = $tipo;
        $url = 'index.php?rota=' . $rota;
        if ($query !== []) {
            $url .= '&' . http_build_query($query);
        }
        header('Location: ' . $url);
        exit();
    }

    /**
     * Helper para redirecionamento com mensagens de erro.
     */
    private function redirecionarComErro($mensagem, $rota, array $query = [])
    {
        $this->redirecionarComMensagem($mensagem, $rota, 'erro', $query);
    }

    /**
     * Helper para redirecionamento com mensagens de sucesso.
     */
    private function redirecionarComSucesso($mensagem, $rota, array $query = [])
    {
        $this->redirecionarComMensagem($mensagem, $rota, 'sucesso', $query);
    }
}
?>