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
        $sql = "SELECT id_usuario, nome, email, senha, tipo FROM usuarios WHERE email = :email";

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
            $usuario->setTipo($dados['tipo']);

            return $usuario;
        }

        return null;
    }

    /**
     * Busca um usuário pelo ID.
     *
     * @param int $idUsuario
     * @return UsuarioDTO|null
     */
    public function buscarPorId($idUsuario)
    {
        $sql = "SELECT id_usuario, nome, email, senha, tipo, data_cadastro FROM usuarios WHERE id_usuario = :id_usuario";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindParam(':id_usuario', $idUsuario, PDO::PARAM_INT);
        $stmt->execute();

        $dados = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($dados === false) {
            return null;
        }

        $usuario = new UsuarioDTO();
        $usuario->setId($dados['id_usuario']);
        $usuario->setNome($dados['nome']);
        $usuario->setEmail($dados['email']);
        $usuario->setSenha($dados['senha']);
        $usuario->setTipo($dados['tipo']);
        $usuario->setDataCadastro($dados['data_cadastro']);

        return $usuario;
    }

    /**
     * Verifica se um e-mail já está cadastrado.
     *
     * @param string $email
     * @param int|null $ignorarIdUsuario
     * @return bool
     */
    public function emailJaCadastrado($email, $ignorarIdUsuario = null)
    {
        if ($ignorarIdUsuario !== null) {
            $sql = "SELECT 1 FROM usuarios WHERE email = :email AND id_usuario <> :id_usuario LIMIT 1";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':id_usuario', $ignorarIdUsuario, PDO::PARAM_INT);
        } else {
            $sql = "SELECT 1 FROM usuarios WHERE email = :email LIMIT 1";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindParam(':email', $email);
        }

        $stmt->execute();
        return $stmt->fetchColumn() !== false;
    }

    /**
     * Atualiza os dados de um usuário.
     *
     * @param UsuarioDTO $usuario
     * @return bool
     */
    public function atualizar(UsuarioDTO $usuario)
    {
        $sql = "UPDATE usuarios
                SET nome = :nome,
                    email = :email,
                    senha = :senha
                WHERE id_usuario = :id_usuario";

        $stmt = $this->conexao->prepare($sql);

        $idUsuario = $usuario->getId();
        $nome = $usuario->getNome();
        $email = $usuario->getEmail();
        $senha = $usuario->getSenha();

        $stmt->bindParam(':id_usuario', $idUsuario, PDO::PARAM_INT);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':senha', $senha);

        return $stmt->execute();
    }

    /**
     * Exclui um usuário pelo ID.
     *
     * @param int $idUsuario
     * @return bool
     */
    public function excluirPorId($idUsuario)
    {
        $sql = "DELETE FROM usuarios WHERE id_usuario = :id_usuario";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindParam(':id_usuario', $idUsuario, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Lista usuários cadastrados em ordem de criação (mais recentes primeiro).
     *
     * @return UsuarioDTO[]
     */
    public function listarUsuarios()
    {
        $sql = "SELECT id_usuario, nome, email, tipo, data_cadastro
                FROM usuarios
                ORDER BY id_usuario DESC";

        $stmt = $this->conexao->prepare($sql);
        $stmt->execute();

        $usuarios = [];
        while ($dados = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $usuario = new UsuarioDTO();
            $usuario->setId($dados['id_usuario']);
            $usuario->setNome($dados['nome']);
            $usuario->setEmail($dados['email']);
            $usuario->setTipo($dados['tipo']);
            $usuario->setDataCadastro($dados['data_cadastro']);

            $usuarios[] = $usuario;
        }

        return $usuarios;
    }
}
?>