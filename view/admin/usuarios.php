<?php
// View de listagem administrativa de usuários
$usuariosLista = is_array($usuarios ?? null) ? $usuarios : [];
$filtrosUsuariosAtuais = is_array($filtrosUsuariosAtuais ?? null) ? $filtrosUsuariosAtuais : [
    'busca' => '',
    'papel' => '',
    'ativo' => 1,
];
$totalUsuarios = count($usuariosLista);
$totalAdmins = 0;
$totalCidadaos = 0;
$totalGestores = 0;
$ehAdminSessao = isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] === 'admin';

foreach ($usuariosLista as $usuarioItem) {
    $tipoNormalizado = strtolower(trim((string) $usuarioItem->getTipo()));

    if ($tipoNormalizado === 'admin') {
        $totalAdmins++;
    } elseif ($tipoNormalizado === 'cidadao') {
        $totalCidadaos++;
    } elseif ($tipoNormalizado === 'gestor') {
        $totalGestores++;
    }
}

$classePapel = static function (string $tipo): string {
    $tipoNormalizado = strtolower(trim($tipo));

    if ($tipoNormalizado === 'admin') {
        return 'admin-usuarios-papel admin-usuarios-papel-admin';
    }

    if ($tipoNormalizado === 'gestor') {
        return 'admin-usuarios-papel admin-usuarios-papel-admin';
    }

    return 'admin-usuarios-papel admin-usuarios-papel-cidadao';
};
?>
<?php include "view/templates/header.php"; ?>

<section class="container py-4 admin-usuarios-layout">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <p class="admin-usuarios-eyebrow mb-2">Painel Administrativo</p>
            <h2 class="admin-usuarios-titulo mb-2">Gerenciar Usuários</h2>
            <p class="text-muted mb-0">Controle de acessos, permissões e moderação de perfis da comunidade Cidade
                Atenta.</p>
        </div>
        <a href="index.php?rota=cadastrar"
            class="btn btn-primary d-inline-flex align-items-center gap-2 admin-usuarios-convidar painel-sidebar-item-ativo">
            <img src="assets/fonts/material-symbols/how_to_reg.svg" alt="adicionar usuario" class="nav-icon" width="18"
                height="18">
            Adicionar Usuário
        </a>
    </div>

    <div class="card shadow-sm admin-usuarios-filtros-card mb-4">
        <div class="card-body p-3 p-lg-4">
            <form class="row g-2" method="GET" action="index.php">
                <input type="hidden" name="rota" value="listar_usuarios">

                <div class="col-12 col-lg-6">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0">
                            <img src="assets/fonts/material-symbols/search.svg" alt="buscar" class="nav-icon" width="18"
                                height="18">
                        </span>
                        <input type="text" class="form-control border-start-0" name="busca"
                            placeholder="Buscar por nome ou e-mail..."
                            value="<?= htmlspecialchars((string) ($filtrosUsuariosAtuais['busca'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                            maxlength="120">
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-3">
                    <select class="form-select" name="papel">
                        <option value="">Todos os Papéis</option>
                        <option value="admin" <?= (($filtrosUsuariosAtuais['papel'] ?? '') === 'admin') ? 'selected' : '' ?>>Administrador
                        </option>
                        <option value="cidadao" <?= (($filtrosUsuariosAtuais['papel'] ?? '') === 'cidadao') ? 'selected' : '' ?>>Cidadão
                        </option>
                        <option value="gestor" <?= (($filtrosUsuariosAtuais['papel'] ?? '') === 'gestor') ? 'selected' : '' ?>>Gestor
                        </option>
                    </select>
                </div>

                <div class="col-12 col-md-6 col-lg-3">
                    <select class="form-select" name="ativo">
                        <option value="">Todos os Status</option>
                        <option value="1" <?= (string) ($filtrosUsuariosAtuais['ativo'] ?? 1) === '1' ? 'selected' : '' ?>>
                            Ativos
                        </option>
                        <option value="0" <?= (string) ($filtrosUsuariosAtuais['ativo'] ?? 1) === '0' ? 'selected' : '' ?>>
                            Inativos
                        </option>
                    </select>
                </div>

                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                    <a href="index.php?rota=listar_usuarios" class="btn btn-outline-secondary">Limpar filtros</a>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-4 align-items-start">
        <div class="col-12 col-xl-8">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="h6 mb-0 text-uppercase admin-usuarios-subtitulo">Usuários Recentes</h3>
                <span class="small text-muted">Exibindo
                    <?= htmlspecialchars((string) $totalUsuarios, ENT_QUOTES, 'UTF-8') ?> usuários</span>
            </div>

            <?php if (!empty($usuariosLista)): ?>
                <div class="d-grid gap-2">
                    <?php foreach ($usuariosLista as $usuario): ?>
                        <?php
                        $idUsuario = (int) $usuario->getId();
                        $nomeUsuario = trim((string) $usuario->getNome());
                        $tipoUsuario = (string) $usuario->getTipo();
                        $tipoUsuarioNormalizado = strtolower(trim($tipoUsuario));
                        $usuarioAtivo = (int) $usuario->getAtivo() === 1;
                        $ehContaAtual = $idUsuario === (int) ($idUsuarioSessao ?? 0);
                        ?>
                        <article class="card shadow-sm admin-usuarios-item">
                            <div class="card-body py-3 px-3 px-lg-4">
                                <div class="d-flex flex-wrap align-items-center gap-3">
                                    <div class="flex-grow-1 admin-usuarios-identificacao">
                                        <h4 class="h6 mb-1 text-truncate">
                                            <?= htmlspecialchars($nomeUsuario, ENT_QUOTES, 'UTF-8') ?>
                                        </h4>
                                        <p class="small text-muted mb-0 text-truncate">
                                            <?= htmlspecialchars((string) $usuario->getEmail(), ENT_QUOTES, 'UTF-8') ?>
                                        </p>
                                    </div>

                                    <div>
                                        <p class="small text-muted text-uppercase mb-1">Papel</p>
                                        <span class="<?= $classePapel($tipoUsuario) ?>">
                                            <?= htmlspecialchars(ucfirst($tipoUsuario), ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                    </div>

                                    <div>
                                        <p class="small text-muted text-uppercase mb-1">Status</p>
                                        <span class="admin-usuarios-status">
                                            <span class="admin-usuarios-status-dot" aria-hidden="true"></span>
                                            <?= $usuarioAtivo ? 'Ativo' : 'Inativo' ?>
                                        </span>
                                    </div>

                                    <div class="ms-auto d-flex gap-2 align-items-center">
                                        <?php if ($ehContaAtual): ?>
                                            <span class="badge text-bg-secondary">Conta Atual</span>
                                        <?php else: ?>
                                            <?php if ($usuarioAtivo && $ehAdminSessao && $tipoUsuarioNormalizado !== 'admin'): ?>
                                                <form action="index.php?rota=processar_promocao_usuario_admin" method="POST"
                                                    onsubmit="return confirm('Promover este usuário para administrador?');">
                                                    <?= csrfField() ?>
                                                    <input type="hidden" name="id_usuario"
                                                        value="<?= htmlspecialchars((string) $usuario->getId(), ENT_QUOTES, 'UTF-8') ?>">
                                                    <button type="submit" class="btn btn-outline-primary btn-sm">Promover a
                                                        Admin</button>
                                                </form>
                                            <?php endif; ?>

                                            <?php if ($usuarioAtivo && ($ehAdminSessao || $tipoUsuarioNormalizado !== 'admin')): ?>
                                                <form action="index.php?rota=processar_exclusao_usuario_admin" method="POST"
                                                    onsubmit="return confirm('Tem certeza que deseja desativar este usuário?');">
                                                    <?= csrfField() ?>
                                                    <input type="hidden" name="id_usuario"
                                                        value="<?= htmlspecialchars((string) $usuario->getId(), ENT_QUOTES, 'UTF-8') ?>">
                                                    <button type="submit" class="btn btn-outline-danger btn-sm">Desativar</button>
                                                </form>
                                            <?php endif; ?>

                                            <?php if (!$usuarioAtivo): ?>
                                                <span class="badge text-bg-light border">Sem ações para contas inativas</span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info mb-0" role="alert">
                    Nenhum usuário encontrado.
                </div>
            <?php endif; ?>
        </div>

        <div class="col-12 col-xl-4">
            <div class="card shadow-sm admin-usuarios-resumo-card mb-3">
                <div class="card-body">
                    <h3 class="h6 mb-3 text-uppercase admin-usuarios-subtitulo">Visão Geral</h3>
                    <ul class="list-unstyled d-grid gap-3 mb-0">
                        <li class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Usuários Totais</span>
                            <strong><?= htmlspecialchars((string) $totalUsuarios, ENT_QUOTES, 'UTF-8') ?></strong>
                        </li>
                        <li class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Administradores</span>
                            <strong><?= htmlspecialchars((string) $totalAdmins, ENT_QUOTES, 'UTF-8') ?></strong>
                        </li>
                        <li class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Cidadãos</span>
                            <strong><?= htmlspecialchars((string) $totalCidadaos, ENT_QUOTES, 'UTF-8') ?></strong>
                        </li>
                        <li class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Gestores</span>
                            <strong><?= htmlspecialchars((string) $totalGestores, ENT_QUOTES, 'UTF-8') ?></strong>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card shadow-sm admin-usuarios-dica-card mb-3">
                <div class="card-body">
                    <h3 class="h6 mb-2">Dica de Gestão</h3>
                    <p class="small text-muted mb-0">Revise periodicamente permissões elevadas e mantenha o menor nível
                        de privilégio possível.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include "view/templates/footer.php"; ?>