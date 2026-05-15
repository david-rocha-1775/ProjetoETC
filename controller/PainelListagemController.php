<?php
// Controller de listagem e visualização do painel.

require_once "controller/PainelBaseController.php";

class PainelListagemController extends PainelBaseController
{
    public function exibirMapa()
    {
        $tituloPagina = 'Mapa de Denuncias';
        include 'view/painel/mapa.php';
    }

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
            $totaisStatus = $this->denunciaDAO->contarTotaisPorStatus($idCategoriaFiltro);
            $categorias = $this->categoriaDAO->listarTodas();
            $resumoInteracoes = $this->carregarResumoInteracoesDenuncias($denuncias);
            $autoresPorDenuncia = $this->carregarAutoresPorDenuncia($denuncias);
            $categoriasPorId = $this->mapearCategoriasPorId($categorias);
            $tituloPagina = "Painel do Usuário";
            $usuarioNome = $_SESSION['usuario_nome'];
            $filtroCategoriaSelecionada = $idCategoriaFiltro;
            $filtroLimiteSelecionado = $limitePagina;
            $filtroOrdenacaoSelecionada = $ordenacaoFiltro;
            $painelQueryRetorno = $this->montarUrlRetornoPainel();
            $painelQueryFiltros = $this->montarUrlRetornoPainel(['rota' => null]);
            $totalStatusEmAnalise = (int) ($totaisStatus['em_analise'] ?? 0);
            $totalStatusResolvido = (int) ($totaisStatus['resolvido'] ?? 0);

            include "view/painel/index.php";

        } catch (Exception $e) {
            $_SESSION['mensagem'] = "Erro ao carregar os dados do painel: " . $e->getMessage();
            $_SESSION['tipo_mensagem'] = "erro";
            header("Location: index.php?" . $this->montarUrlRetornoPainel(['rota' => 'inicio']));
            exit();
        }
    }

    public function detalheDenuncia()
    {
        try {
            $idDenuncia = (int) ($_GET['id'] ?? 0);
            if ($idDenuncia <= 0) {
                throw new Exception('Denúncia inválida.');
            }

            $denuncia = $this->denunciaDAO->buscarPorId($idDenuncia);
            if ($denuncia === null) {
                throw new Exception('Denúncia não encontrada.');
            }

            $categorias = $this->categoriaDAO->listarTodas();
            $categoriasPorId = $this->mapearCategoriasPorId($categorias);

            $interacoes = $this->carregarInteracoesDenuncias([$denuncia]);
            $interacaoDenuncia = $interacoes[$idDenuncia] ?? [
                'comentarios' => [],
                'totalCurtidas' => 0,
                'usuarioCurtiu' => false,
            ];

            $idUsuarioDono = (int) $denuncia->getIdUsuario();
            $autorDenuncia = $this->usuarioDAO->buscarPorId($idUsuarioDono);
            $nomeAutorDenuncia = $autorDenuncia ? $autorDenuncia->getNome() : 'Usuário';

            $usuarioLogadoId = (int) ($_SESSION['usuario_id'] ?? 0);
            $usuarioAdmin = $this->usuarioPossuiAcessoAdministrativo();
            $podeGerenciar = $usuarioAdmin || $usuarioLogadoId === $idUsuarioDono;

            $tituloPagina = 'Detalhes da Denúncia';
            $painelQueryRetorno = $this->montarUrlRetornoPainel();
            $nomeCategoriaDenuncia = $categoriasPorId[(int) $denuncia->getIdCategoria()] ?? 'Categoria não informada';

            include "view/painel/detalhe.php";

        } catch (Exception $e) {
            $_SESSION['mensagem'] = 'Erro: ' . $e->getMessage();
            $_SESSION['tipo_mensagem'] = 'erro';
            header('Location: index.php?' . $this->montarUrlRetornoPainel());
            exit();
        }
    }
}
