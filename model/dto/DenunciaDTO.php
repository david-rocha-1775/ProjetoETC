<?php
// DTO (Data Transfer Object) da Denúncia
// Representa a tabela 'denuncias' como um objeto PHP
// Serve para transportar dados entre as camadas do MVC

class DenunciaDTO
{
    private $id;
    private $titulo;
    private $descricao;
    private $localizacao;
    private $fotoPath;
    private $status;
    private $idUsuario;
    private $idCategoria;
    private $dataCriacao;

    // --- GETTERS ---

    public function getId()
    {
        return $this->id;
    }

    public function getTitulo()
    {
        return $this->titulo;
    }

    public function getDescricao()
    {
        return $this->descricao;
    }

    public function getLocalizacao()
    {
        return $this->localizacao;
    }

    public function getFotoPath()
    {
        return $this->fotoPath;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getDataCriacao()
    {
        return $this->dataCriacao;
    }

    public function getIdUsuario()
    {
        return $this->idUsuario;
    }

    public function getIdCategoria()
    {
        return $this->idCategoria;
    }

    // --- SETTERS ---

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;
    }

    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;
    }

    public function setLocalizacao($localizacao)
    {
        $this->localizacao = $localizacao;
    }

    public function setFotoPath($fotoPath)
    {
        $this->fotoPath = $fotoPath;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function setDataCriacao($dataCriacao)
    {
        $this->dataCriacao = $dataCriacao;
    }

    public function setIdUsuario($idUsuario)
    {
        $this->idUsuario = $idUsuario;
    }

    public function setIdCategoria($idCategoria)
    {
        $this->idCategoria = $idCategoria;
    }
}
?>