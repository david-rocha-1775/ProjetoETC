<?php
// DTO (Data Transfer Object) do Usuário
// Representa a tabela 'usuarios' como um objeto PHP
// Serve para transportar dados entre as camadas do MVC

class UsuarioDTO
{
    private $id;
    private $nome;
    private $email;
    private $senha;
    private $tipo;

    private $dataCadastro;

    // --- GETTERS ---

    public function getId()
    {
        return $this->id;
    }

    public function getNome()
    {
        return $this->nome;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getSenha()
    {
        return $this->senha;
    }

    public function getTipo()
    {
        return $this->tipo;
    }

    public function getDataCadastro()
    {
        return $this->dataCadastro;
    }

    // --- SETTERS ---

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setNome($nome)
    {
        $this->nome = $nome;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setSenha($senha)
    {
        $this->senha = $senha;
    }

    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }

    public function setDataCadastro($dataCadastro)
    {
        $this->dataCadastro = $dataCadastro;
    }
}
?>