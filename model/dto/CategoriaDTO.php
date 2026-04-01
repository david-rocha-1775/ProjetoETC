<?php
// DTO (Data Transfer Object) da Categoria
// Representa a tabela 'categorias' como um objeto PHP
// Serve para transportar dados entre as camadas do MVC

class CategoriaDTO
{
    private $id;
    private $nomeCategoria;
    private $ativo;

    // --- GETTERS ---

    public function getId()
    {
        return $this->id;
    }

    public function getNomeCategoria()
    {
        return $this->nomeCategoria;
    }

    public function getAtivo()
    {
        return $this->ativo;
    }

    // --- SETTERS ---

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setNomeCategoria($nomeCategoria)
    {
        $this->nomeCategoria = $nomeCategoria;
    }

    public function setAtivo($ativo)
    {
        $this->ativo = $ativo;
    }
}
?>