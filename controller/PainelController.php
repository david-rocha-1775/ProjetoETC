<?php
// Fachada de compatibilidade para o antigo controller monolítico do painel.

require_once "controller/PainelListagemController.php";
require_once "controller/PainelDenunciaController.php";
require_once "controller/PainelInteracaoController.php";

class PainelController
{
    private $listagemController;
    private $denunciaController;
    private $interacaoController;

    public function __construct()
    {
        $this->listagemController = new PainelListagemController();
        $this->denunciaController = new PainelDenunciaController();
        $this->interacaoController = new PainelInteracaoController();
    }

    public function exibirMapa()
    {
        $this->listagemController->exibirMapa();
    }

    public function index()
    {
        $this->listagemController->index();
    }

    public function detalheDenuncia()
    {
        $this->listagemController->detalheDenuncia();
    }

    public function cadastrarDenuncia()
    {
        $this->denunciaController->cadastrarDenuncia();
    }

    public function atualizarDenuncia()
    {
        $this->denunciaController->atualizarDenuncia();
    }

    public function excluirDenuncia()
    {
        $this->denunciaController->excluirDenuncia();
    }

    public function comentarDenuncia()
    {
        $this->interacaoController->comentarDenuncia();
    }

    public function curtirDenuncia()
    {
        $this->interacaoController->curtirDenuncia();
    }

    public function curtirComentario()
    {
        $this->interacaoController->curtirComentario();
    }
}
