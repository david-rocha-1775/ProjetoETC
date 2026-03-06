<?php
// DAO (Data Access Object) do Usuário
// Contém TODOS os comandos SQL relacionados à tabela 'usuarios'
// Nenhum outro lugar do sistema deve ter SQL de usuário!

require_once "model/dao/Conexao.php";
require_once "model/dto/UsuarioDTO.php";

class UsuarioDAO
{
    private $conexao;

    public function __construct()
    {
        $this->conexao = Conexao::getInstance();
    }

    // Cadastrar um novo usuário no banco
    public function cadastrar(UsuarioDTO $usuario)
    {
        $sql = "INSERT INTO usuarios (nome, email, senha) VALUES (:nome, :email, :senha)";

        $stmt = $this->conexao->prepare($sql);

        $nome = $usuario->getNome();
        $email = $usuario->getEmail();
        $senha = $usuario->getSenha();

        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':senha', $senha);

        $stmt->execute();
    }

    // Buscar um usuário pelo e-mail (usado no login)
    public function buscarPorEmail($email)
    {
        $sql = "SELECT id_usuario, nome, email, senha FROM usuarios WHERE email = :email";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $dados = $stmt->fetch(PDO::FETCH_ASSOC);

            $usuario = new UsuarioDTO();
            $usuario->setId($dados['id_usuario']);
            $usuario->setNome($dados['nome']);
            $usuario->setEmail($dados['email']);
            $usuario->setSenha($dados['senha']);

            return $usuario;
        }

        return null; // Usuário não encontrado
    }
}
?>