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
        $sql = "INSERT INTO usuarios (nome, email, senha, ativo) VALUES (:nome, :email, :senha, 1)";

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
        $sql = "SELECT id_usuario, nome, email, senha, tipo, ativo FROM usuarios WHERE email = :email AND ativo = 1";

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
            $usuario->setAtivo($dados['ativo']);

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
        $sql = "SELECT id_usuario, nome, email, senha, tipo, ativo, data_cadastro FROM usuarios WHERE id_usuario = :id_usuario AND ativo = 1";

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
        $usuario->setAtivo($dados['ativo']);
        $usuario->setDataCadastro($dados['data_cadastro']);

        return $usuario;
    }

    /**
     * Retorna um mapa id_usuario => nome para uma lista de usuários.
     *
     * @param int[] $idsUsuario
     * @return array<int, string>
     */
    public function buscarNomesPorIds(array $idsUsuario)
    {
        $idsUsuario = array_values(array_filter(array_map('intval', $idsUsuario), static function ($id) {
            return $id > 0;
        }));

        if ($idsUsuario === []) {
            return [];
        }

        $placeholders = implode(', ', array_fill(0, count($idsUsuario), '?'));
        $sql = "SELECT id_usuario, nome
                FROM usuarios
                WHERE ativo = 1 AND id_usuario IN ($placeholders)";

        $stmt = $this->conexao->prepare($sql);
        foreach ($idsUsuario as $index => $idUsuario) {
            $stmt->bindValue($index + 1, $idUsuario, PDO::PARAM_INT);
        }

        $stmt->execute();

        $resultado = [];
        while ($dados = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $resultado[(int) $dados['id_usuario']] = (string) $dados['nome'];
        }

        return $resultado;
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
                WHERE id_usuario = :id_usuario AND ativo = 1";

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
     * Desativa um usuário e seus dados relacionados com integridade transacional.
     *
     * @param int $idUsuario
     * @return bool
     */
    public function excluirPorId($idUsuario)
    {
        try {
            $this->conexao->beginTransaction();

            $queries = [
                "UPDATE curtida_denuncias SET ativo = 0 WHERE fk_usuario = :id_usuario",
                "UPDATE curtida_comentarios SET ativo = 0 WHERE fk_usuario = :id_usuario",
                "UPDATE comentarios SET ativo = 0 WHERE fk_usuario = :id_usuario",
                "UPDATE denuncias SET ativo = 0 WHERE fk_usuario = :id_usuario",
                "UPDATE comentarios c INNER JOIN denuncias d ON d.id_denuncia = c.fk_denuncia
                    SET c.ativo = 0
                    WHERE d.fk_usuario = :id_usuario",
                "UPDATE curtida_denuncias cd INNER JOIN denuncias d ON d.id_denuncia = cd.fk_denuncia
                    SET cd.ativo = 0
                    WHERE d.fk_usuario = :id_usuario",
                "UPDATE curtida_comentarios cc
                    INNER JOIN comentarios c ON c.id_comentario = cc.fk_comentario
                    INNER JOIN denuncias d ON d.id_denuncia = c.fk_denuncia
                    SET cc.ativo = 0
                    WHERE d.fk_usuario = :id_usuario",
            ];

            foreach ($queries as $sql) {
                $stmt = $this->conexao->prepare($sql);
                $stmt->bindParam(':id_usuario', $idUsuario, PDO::PARAM_INT);
                $stmt->execute();
            }

            $stmtUsuario = $this->conexao->prepare(
                "UPDATE usuarios SET ativo = 0 WHERE id_usuario = :id_usuario AND ativo = 1"
            );
            $stmtUsuario->bindParam(':id_usuario', $idUsuario, PDO::PARAM_INT);
            $stmtUsuario->execute();

            if ($stmtUsuario->rowCount() !== 1) {
                throw new Exception("Usuário não pôde ser desativado.");
            }

            $this->conexao->commit();
            return true;

        } catch (Throwable $e) {
            if ($this->conexao->inTransaction()) {
                $this->conexao->rollBack();
            }

            throw new Exception("Falha ao excluir conta com integridade de dados.", 0, $e);
        }
    }

    /**
     * Promove um usuário ativo para perfil administrativo.
     *
     * @param int $idUsuario
     * @return bool
     */
    public function promoverParaAdmin($idUsuario)
    {
        $sql = "UPDATE usuarios
                SET tipo = 'admin'
                WHERE id_usuario = :id_usuario
                  AND ativo = 1
                  AND tipo <> 'admin'";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindParam(':id_usuario', $idUsuario, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() === 1;
    }

    /**
     * Lista usuários cadastrados em ordem de criação (mais recentes primeiro).
     *
     * @param string $busca
     * @param string|null $papel
     * @param string $status
     * @return UsuarioDTO[]
     */
    public function listarUsuarios($busca = '', $papel = null, $status = 'ativo')
    {
        if ($status !== 'ativo') {
            return [];
        }

        $sql = "SELECT id_usuario, nome, email, tipo, ativo, data_cadastro
            FROM usuarios
            WHERE ativo = 1";

        $params = [];

        if ($papel !== null && $papel !== '') {
            $sql .= " AND tipo = :tipo";
            $params[':tipo'] = (string) $papel;
        }

        if ($busca !== '') {
            $sql .= " AND (nome LIKE :busca OR email LIKE :busca)";
            $params[':busca'] = '%' . $busca . '%';
        }

        $sql .= " ORDER BY id_usuario DESC";

        $stmt = $this->conexao->prepare($sql);
        foreach ($params as $chave => $valor) {
            $stmt->bindValue($chave, $valor);
        }
        $stmt->execute();

        $usuarios = [];
        while ($dados = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $usuario = new UsuarioDTO();
            $usuario->setId($dados['id_usuario']);
            $usuario->setNome($dados['nome']);
            $usuario->setEmail($dados['email']);
            $usuario->setTipo($dados['tipo']);
            $usuario->setAtivo($dados['ativo']);
            $usuario->setDataCadastro($dados['data_cadastro']);

            $usuarios[] = $usuario;
        }

        return $usuarios;
    }

    /**
     * Conta usuários ativos.
     *
     * @return int
     */
    public function contarAtivos()
    {
        $sql = "SELECT COUNT(*) AS total FROM usuarios WHERE ativo = 1";

        $stmt = $this->conexao->prepare($sql);
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }
}
?>