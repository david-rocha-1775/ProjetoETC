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
    private $fkPerfil;
    private $nomePerfil;
    private $ativo;

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
        if ($this->tipo !== null && $this->tipo !== '') {
            return $this->tipo;
        }

        return $this->nomePerfil;
    }

    public function getFkPerfil()
    {
        return $this->fkPerfil;
    }

    public function getNomePerfil()
    {
        return $this->nomePerfil;
    }

    public function getAtivo()
    {
        return $this->ativo;
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

    public function setFkPerfil($fkPerfil)
    {
        $this->fkPerfil = $fkPerfil;
    }

    public function setNomePerfil($nomePerfil)
    {
        $this->nomePerfil = $nomePerfil;
        $this->tipo = $nomePerfil;
    }

    public function setAtivo($ativo)
    {
        $this->ativo = $ativo;
    }

    public function setDataCadastro($dataCadastro)
    {
        $this->dataCadastro = $dataCadastro;
    }
}
?>