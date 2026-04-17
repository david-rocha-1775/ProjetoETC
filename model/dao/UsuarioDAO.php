<?php
// DAO (Data Access Object) da tabela 'usuarios'

require_once "model/dao/Conexao.php";
require_once "model/dto/UsuarioDTO.php";

class UsuarioDAO
{
    private $conexao;
    private const PERFIL_CIDADAO = 'cidadao';
    private const PERFIL_ADMIN = 'admin';

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
        $nome = $usuario->getNome();
        $email = $usuario->getEmail();
        $senha = $usuario->getSenha();
        $idPerfil = $this->buscarIdPerfilPorNome(self::PERFIL_CIDADAO);

        if ($idPerfil === null) {
            throw new Exception('Perfil padrão de cidadão não encontrado.');
        }

        try {
            $this->conexao->beginTransaction();

            $sqlUsuario = "INSERT INTO usuarios (nome, fk_perfil, ativo) VALUES (:nome, :fk_perfil, 1)";
            $stmtUsuario = $this->conexao->prepare($sqlUsuario);
            $stmtUsuario->bindParam(':nome', $nome);
            $stmtUsuario->bindParam(':fk_perfil', $idPerfil, PDO::PARAM_INT);
            $stmtUsuario->execute();

            $idUsuario = (int) $this->conexao->lastInsertId();

            $sqlLogin = "INSERT INTO logins (fk_usuario, email, senha) VALUES (:fk_usuario, :email, :senha)";
            $stmtLogin = $this->conexao->prepare($sqlLogin);
            $stmtLogin->bindParam(':fk_usuario', $idUsuario, PDO::PARAM_INT);
            $stmtLogin->bindParam(':email', $email);
            $stmtLogin->bindParam(':senha', $senha);
            $stmtLogin->execute();

            $this->conexao->commit();

        } catch (Throwable $e) {
            if ($this->conexao->inTransaction()) {
                $this->conexao->rollBack();
            }

            throw new Exception('Falha ao cadastrar usuário.', 0, $e);
        }
    }

    /**
     * Busca um usuário pelo endereço de e-mail (usado no login).
     *
     * @param string $email
     * @return UsuarioDTO|null Retorna o objeto DTO ou null se não encontrado.
     */
    public function buscarPorEmail($email)
    {
        $sql = "SELECT u.id_usuario,
                       u.nome,
                       u.ativo,
                       u.data_cadastro,
                       u.fk_perfil,
                       p.nome_perfil,
                       l.email,
                       l.senha
                FROM usuarios u
                INNER JOIN logins l ON l.fk_usuario = u.id_usuario
                INNER JOIN perfis p ON p.id_perfil = u.fk_perfil
                WHERE l.email = :email AND u.ativo = 1
                LIMIT 1";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $dados = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($dados !== false) {
            $usuario = new UsuarioDTO();
            $usuario->setId($dados['id_usuario']);
            $usuario->setNome($dados['nome']);
            $usuario->setEmail($dados['email']);
            $usuario->setSenha($dados['senha']);
            $usuario->setFkPerfil($dados['fk_perfil']);
            $usuario->setNomePerfil($dados['nome_perfil']);
            $usuario->setAtivo($dados['ativo']);
            $usuario->setDataCadastro($dados['data_cadastro']);

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
        $sql = "SELECT u.id_usuario,
                       u.nome,
                       u.ativo,
                       u.data_cadastro,
                       u.fk_perfil,
                       p.nome_perfil,
                       l.email,
                       l.senha
                FROM usuarios u
                INNER JOIN logins l ON l.fk_usuario = u.id_usuario
                INNER JOIN perfis p ON p.id_perfil = u.fk_perfil
                WHERE u.id_usuario = :id_usuario AND u.ativo = 1
                LIMIT 1";

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
        $usuario->setFkPerfil($dados['fk_perfil']);
        $usuario->setNomePerfil($dados['nome_perfil']);
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
            $sql = "SELECT 1
                    FROM logins
                    WHERE email = :email
                      AND fk_usuario <> :id_usuario
                    LIMIT 1";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':id_usuario', $ignorarIdUsuario, PDO::PARAM_INT);
        } else {
            $sql = "SELECT 1 FROM logins WHERE email = :email LIMIT 1";
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
        $idUsuario = $usuario->getId();
        $nome = $usuario->getNome();
        $email = $usuario->getEmail();
        $senha = $usuario->getSenha();

        if ($this->buscarPorId($idUsuario) === null) {
            throw new Exception('Usuário não encontrado para atualização.');
        }

        try {
            $this->conexao->beginTransaction();

            $sqlUsuario = "UPDATE usuarios
                           SET nome = :nome
                           WHERE id_usuario = :id_usuario AND ativo = 1";
            $stmtUsuario = $this->conexao->prepare($sqlUsuario);
            $stmtUsuario->bindParam(':id_usuario', $idUsuario, PDO::PARAM_INT);
            $stmtUsuario->bindParam(':nome', $nome);
            $stmtUsuario->execute();

            $sqlLogin = "UPDATE logins
                         SET email = :email,
                             senha = :senha
                         WHERE fk_usuario = :id_usuario";
            $stmtLogin = $this->conexao->prepare($sqlLogin);
            $stmtLogin->bindParam(':id_usuario', $idUsuario, PDO::PARAM_INT);
            $stmtLogin->bindParam(':email', $email);
            $stmtLogin->bindParam(':senha', $senha);
            $stmtLogin->execute();

            $this->conexao->commit();
            return true;

        } catch (Throwable $e) {
            if ($this->conexao->inTransaction()) {
                $this->conexao->rollBack();
            }

            throw new Exception('Falha ao atualizar dados do usuário.', 0, $e);
        }
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
        $sql = "UPDATE usuarios u
            INNER JOIN perfis p ON p.nome_perfil = :perfil_admin
            SET u.fk_perfil = p.id_perfil
            WHERE u.id_usuario = :id_usuario
              AND u.ativo = 1
              AND u.fk_perfil <> p.id_perfil";

        $stmt = $this->conexao->prepare($sql);
        $perfilAdmin = self::PERFIL_ADMIN;
        $stmt->bindParam(':perfil_admin', $perfilAdmin);
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

        $sql = "SELECT u.id_usuario,
                       u.nome,
                       l.email,
                       p.nome_perfil AS tipo,
                       u.fk_perfil,
                       u.ativo,
                       u.data_cadastro
                FROM usuarios u
                INNER JOIN logins l ON l.fk_usuario = u.id_usuario
                INNER JOIN perfis p ON p.id_perfil = u.fk_perfil
                WHERE u.ativo = 1";

        $params = [];

        if ($papel !== null && $papel !== '') {
            $sql .= " AND p.nome_perfil = :tipo";
            $params[':tipo'] = (string) $papel;
        }

        if ($busca !== '') {
            $sql .= " AND (u.nome LIKE :busca OR l.email LIKE :busca)";
            $params[':busca'] = '%' . $busca . '%';
        }

        $sql .= " ORDER BY u.id_usuario DESC";

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
            $usuario->setFkPerfil($dados['fk_perfil']);
            $usuario->setNomePerfil($dados['tipo']);
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

    /**
     * Busca o ID do perfil a partir do nome.
     *
     * @param string $nomePerfil
     * @return int|null
     */
    private function buscarIdPerfilPorNome($nomePerfil)
    {
        $sql = "SELECT id_perfil
                FROM perfis
                WHERE nome_perfil = :nome_perfil
                  AND ativo = 1
                LIMIT 1";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindParam(':nome_perfil', $nomePerfil);
        $stmt->execute();

        $idPerfil = $stmt->fetchColumn();
        if ($idPerfil === false) {
            return null;
        }

        return (int) $idPerfil;
    }
}
?>