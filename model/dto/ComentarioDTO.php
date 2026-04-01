<?php
// DTO (Data Transfer Object) do Comentário
// Representa a tabela 'comentarios' como um objeto PHP
// Serve para transportar dados entre as camadas do MVC

class ComentarioDTO
{
    private $id;
    private $texto;
    private $dataComentario;
    private $ativo;
    private $idUsuario;
    private $idDenuncia;
    private $nomeUsuario;

    // --- GETTERS ---

    public function getId()
    {
        return $this->id;
    }

    public function getTexto()
    {
        return $this->texto;
    }

    public function getDataComentario()
    {
        return $this->dataComentario;
    }

    public function getAtivo()
    {
        return $this->ativo;
    }

    public function getIdUsuario()
    {
        return $this->idUsuario;
    }

    public function getIdDenuncia()
    {
        return $this->idDenuncia;
    }

    public function getNomeUsuario()
    {
        return $this->nomeUsuario;
    }

    // --- SETTERS ---

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setTexto($texto)
    {
        $this->texto = $texto;
    }

    public function setDataComentario($dataComentario)
    {
        $this->dataComentario = $dataComentario;
    }

    public function setAtivo($ativo)
    {
        $this->ativo = $ativo;
    }

    public function setIdUsuario($idUsuario)
    {
        $this->idUsuario = $idUsuario;
    }

    public function setIdDenuncia($idDenuncia)
    {
        $this->idDenuncia = $idDenuncia;
    }

    public function setNomeUsuario($nomeUsuario)
    {
        $this->nomeUsuario = $nomeUsuario;
    }
}
?>