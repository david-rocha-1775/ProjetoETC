<?php
// DTO (Data Transfer Object) da Curtida em Denúncia
// Representa a tabela 'curtida_denuncias' como um objeto PHP
// Serve para transportar dados entre as camadas do MVC

class CurtidaDenunciaDTO
{
    private $idUsuario;
    private $idDenuncia;
    private $dataCurtida;
    private $ativo;

    // --- GETTERS ---

    public function getIdUsuario()
    {
        return $this->idUsuario;
    }

    public function getIdDenuncia()
    {
        return $this->idDenuncia;
    }

    public function getDataCurtida()
    {
        return $this->dataCurtida;
    }

    public function getAtivo()
    {
        return $this->ativo;
    }

    // --- SETTERS ---

    public function setIdUsuario($idUsuario)
    {
        $this->idUsuario = $idUsuario;
    }

    public function setIdDenuncia($idDenuncia)
    {
        $this->idDenuncia = $idDenuncia;
    }

    public function setDataCurtida($dataCurtida)
    {
        $this->dataCurtida = $dataCurtida;
    }

    public function setAtivo($ativo)
    {
        $this->ativo = $ativo;
    }
}
?>