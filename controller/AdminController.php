<?php
// Controller Administrativo (Ações restritas a administradores)

require_once "model/dao/UsuarioDAO.php";
require_once "model/dao/CategoriaDAO.php";
require_once "model/dto/CategoriaDTO.php";

class AdminController
{
    private $usuarioDAO;
    private $categoriaDAO;

    public function __construct()
    {
        $this->usuarioDAO = new UsuarioDAO();
        $this->categoriaDAO = new CategoriaDAO();

        $this->exigirAcessoAdmin();
    }

    /**
     * Lista usuários cadastrados (uso administrativo).
     */
    public function listarUsuarios()
    {
        try {
            $usuarios = $this->usuarioDAO->listarUsuarios();
            $categorias = $this->categoriaDAO->listarTodas();
            $tituloPagina = 'Usuários Cadastrados';
            include 'view/admin/admin.php';
            return;

        } catch (Exception $e) {
            $this->redirecionarComErro('Erro ao listar usuários: ' . $e->getMessage(), 'painel');
        }
    }

    /**
     * Cadastra uma nova categoria (uso administrativo).
     */
    public function cadastrarCategoria()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirecionarComErro('Método inválido para cadastro de categoria.', 'painel');
        }

        try {
            $nomeCategoria = trim($_POST['nome_categoria'] ?? '');
            if ($nomeCategoria === '') {
                throw new Exception('Informe o nome da categoria.');
            }

            $categoria = new CategoriaDTO();
            $categoria->setNomeCategoria($nomeCategoria);

            $salvou = $this->categoriaDAO->cadastrar($categoria);
            if (!$salvou) {
                throw new Exception('Não foi possível cadastrar a categoria.');
            }

            $this->redirecionarComSucesso('Categoria cadastrada com sucesso!', 'listar_usuarios');

        } catch (Exception $e) {
            $this->redirecionarComErro('Erro ao cadastrar categoria: ' . $e->getMessage(), 'listar_usuarios');
        }
    }

    /**
     * Atualiza uma categoria existente (uso administrativo).
     */
    public function atualizarCategoria()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirecionarComErro('Método inválido para atualização de categoria.', 'painel');
        }

        try {
            $idCategoria = (int) ($_POST['id_categoria'] ?? 0);
            $nomeCategoria = trim($_POST['nome_categoria'] ?? '');

            if ($idCategoria <= 0 || $nomeCategoria === '') {
                throw new Exception('Dados da categoria inválidos.');
            }

            $categoria = new CategoriaDTO();
            $categoria->setId($idCategoria);
            $categoria->setNomeCategoria($nomeCategoria);

            $atualizou = $this->categoriaDAO->atualizar($categoria);
            if (!$atualizou) {
                throw new Exception('Não foi possível atualizar a categoria.');
            }

            $this->redirecionarComSucesso('Categoria atualizada com sucesso!', 'listar_usuarios');

        } catch (Exception $e) {
            $this->redirecionarComErro('Erro ao atualizar categoria: ' . $e->getMessage(), 'listar_usuarios');
        }
    }

    /**
     * Exclui uma categoria pelo ID (uso administrativo).
     */
    public function excluirCategoria()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirecionarComErro('Método inválido para exclusão de categoria.', 'painel');
        }

        try {
            $idCategoria = (int) ($_POST['id_categoria'] ?? 0);
            if ($idCategoria <= 0) {
                throw new Exception('ID da categoria inválido.');
            }

            $excluiu = $this->categoriaDAO->excluirPorId($idCategoria);
            if (!$excluiu) {
                throw new Exception('Não foi possível excluir a categoria.');
            }

            $this->redirecionarComSucesso('Categoria excluída com sucesso!', 'listar_usuarios');

        } catch (Exception $e) {
            $this->redirecionarComErro('Erro ao excluir categoria: ' . $e->getMessage(), 'listar_usuarios');
        }
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
     * Helper para redirecionamento com mensagens de erro.
     */
    private function redirecionarComErro($mensagem, $rota)
    {
        $_SESSION['mensagem'] = $mensagem;
        $_SESSION['tipo_mensagem'] = 'erro';
        header('Location: index.php?rota=' . $rota);
        exit();
    }

    /**
     * Helper para redirecionamento com mensagens de sucesso.
     */
    private function redirecionarComSucesso($mensagem, $rota)
    {
        $_SESSION['mensagem'] = $mensagem;
        $_SESSION['tipo_mensagem'] = 'sucesso';
        header('Location: index.php?rota=' . $rota);
        exit();
    }
}
?>