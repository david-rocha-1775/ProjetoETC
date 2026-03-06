<?php
// DTO (Data Transfer Object) do Comentário
// Representa a tabela 'comentarios' como um objeto PHP
// Serve para transportar dados entre as camadas do MVC

class ComentarioDTO
{
    private $id;
    private $texto;
    private $dataComentario;
    private $idUsuario;
    private $idDenuncia;

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

    public function getIdUsuario()
    {
        return $this->idUsuario;
    }

    public function getIdDenuncia()
    {
        return $this->idDenuncia;
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

    public function setIdUsuario($idUsuario)
    {
        $this->idUsuario = $idUsuario;
    }

    public function setIdDenuncia($idDenuncia)
    {
        $this->idDenuncia = $idDenuncia;
    }
}
?>