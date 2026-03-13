<?php
// DTO (Data Transfer Object) da Curtida em Comentário
// Representa a tabela 'curtida_comentarios' como um objeto PHP
// Serve para transportar dados entre as camadas do MVC

class CurtidaComentarioDTO
{
    private $idUsuario;
    private $idComentario;
    private $dataCurtida;

    // --- GETTERS ---

    public function getIdUsuario()
    {
        return $this->idUsuario;
    }

    public function getIdComentario()
    {
        return $this->idComentario;
    }

    public function getDataCurtida()
    {
        return $this->dataCurtida;
    }

    // --- SETTERS ---

    public function setIdUsuario($idUsuario)
    {
        $this->idUsuario = $idUsuario;
    }

    public function setIdComentario($idComentario)
    {
        $this->idComentario = $idComentario;
    }

    public function setDataCurtida($dataCurtida)
    {
        $this->dataCurtida = $dataCurtida;
    }
}
?>