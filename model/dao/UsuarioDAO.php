<?php
// DAO (Data Access Object) da tabela 'usuarios'

require_once "model/dao/Conexao.php";
require_once "model/dto/UsuarioDTO.php";

class UsuarioDAO
{
    private $conexao;

    public function __construct()
    {
        $this->conexao = Conexao::getInstance();
    }

    /**
     * Cadastra um novo usuário no banco de dados.
     *
     * @param UsuarioDTO $usuario Objeto contendo os dados do usuário.
     */
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

    /**
     * Busca um usuário pelo endereço de e-mail (usado no login).
     *
     * @param string $email
     * @return UsuarioDTO|null Retorna o objeto DTO ou null se não encontrado.
     */
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

        return null;
    }
}
?>