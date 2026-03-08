<?php
// Controller de Painel e Ações Autenticadas

require_once "model/dao/DenunciaDAO.php";

class PainelController
{
    private $denunciaDAO;

    public function __construct()
    {
        // Middleware de verificação de sessão (protege todas as ações deste controller)
        if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
            header("Location: index.php?rota=login");
            exit();
        }

        $this->denunciaDAO = new DenunciaDAO();
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
}
?>