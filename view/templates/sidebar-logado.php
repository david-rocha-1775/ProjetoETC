<?php
$rotaAtualSidebar = isset($_GET['rota']) ? (string) $_GET['rota'] : '';
$ehAdminSidebar = isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] === 'admin';

$classeItemSidebar = static function (array $rotasAtivas) use ($rotaAtualSidebar): string {
    return in_array($rotaAtualSidebar, $rotasAtivas, true)
        ? 'btn btn-primary w-100 text-start d-inline-flex align-items-center gap-2'
        : 'btn btn-outline-primary w-100 text-start d-inline-flex align-items-center gap-2';
};
?>

<button
    class="btn btn-outline-primary btn-sm d-inline-flex align-items-center gap-2 d-lg-none painel-sidebar-mobile-toggle"
    type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarLogadoMobile" aria-controls="sidebarLogadoMobile">
    <img src="assets/fonts/material-symbols/menu.svg" alt="menu" class="nav-icon" width="18" height="18">
    Menu
</button>

<div class="offcanvas offcanvas-start" tabindex="-1" id="sidebarLogadoMobile"
    aria-labelledby="sidebarLogadoMobileLabel">
    <div class="offcanvas-header border-bottom">
        <h3 class="offcanvas-title h5 mb-0" id="sidebarLogadoMobileLabel">Navegacao</h3>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Fechar"></button>
    </div>
    <div class="offcanvas-body d-flex flex-column gap-3">
        <a href="index.php?rota=painel" class="<?= $classeItemSidebar(['painel']) ?>">
            <img src="assets/fonts/material-symbols/description.svg" alt="minhas denuncias" class="nav-icon" width="18"
                height="18">
            Minhas denuncias
        </a>
        <a href="index.php?rota=nova_denuncia" class="<?= $classeItemSidebar(['nova_denuncia']) ?>">
            <img src="assets/fonts/material-symbols/add_circle.svg" alt="nova denuncia" class="nav-icon" width="18"
                height="18">
            Nova denuncia
        </a>
        <a href="index.php?rota=mapa" class="<?= $classeItemSidebar(['mapa']) ?>">
            <img src="assets/fonts/material-symbols/map_search.svg" alt="mapa" class="nav-icon" width="18" height="18">
            Mapa
        </a>
        <a href="index.php?rota=perfil_usuario" class="<?= $classeItemSidebar(['perfil_usuario']) ?>">
            <img src="assets/fonts/material-symbols/badge.svg" alt="meu perfil" class="nav-icon" width="18" height="18">
            Meu perfil
        </a>
        <?php if ($ehAdminSidebar): ?>
            <a href="index.php?rota=admin_dashboard"
                class="<?= $classeItemSidebar(['admin_dashboard', 'admin_denuncias', 'listar_usuarios', 'listar_categorias_admin']) ?>">
                <img src="assets/fonts/material-symbols/group.svg" alt="administracao" class="nav-icon" width="18"
                    height="18">
                Administracao
            </a>
        <?php endif; ?>

        <div class="mt-auto pt-2 d-grid gap-2">
            <form action="index.php?rota=sair" method="POST" class="m-0">
                <?= csrfField() ?>
                <button type="submit" class="painel-sidebar-logout-link">
                    <img src="assets/fonts/material-symbols/exit_to_app.svg" alt="sair" class="nav-icon" width="18"
                        height="18">
                    Sair
                </button>
            </form>
        </div>
    </div>
</div>

<aside class="d-none d-lg-block painel-sidebar-col" aria-label="Menu lateral do usuario">
    <div class="card shadow-sm painel-sidebar-card h-100">
        <div class="card-body painel-sidebar-body h-100">
            <div class="painel-sidebar-main d-flex flex-column gap-3">
                <div>
                    <h3 class="h5 mb-1">Painel do Cidadao</h3>
                    <p class="text-muted small mb-0">Acompanhe solicitacoes e interacoes.</p>
                </div>

                <div class="d-grid gap-2">
                    <a href="index.php?rota=painel" class="<?= $classeItemSidebar(['painel']) ?>">
                        <img src="assets/fonts/material-symbols/description.svg" alt="minhas denuncias" class="nav-icon"
                            width="18" height="18">
                        Minhas denuncias
                    </a>
                    <a href="index.php?rota=nova_denuncia" class="<?= $classeItemSidebar(['nova_denuncia']) ?>">
                        <img src="assets/fonts/material-symbols/add_circle.svg" alt="nova denuncia" class="nav-icon"
                            width="18" height="18">
                        Nova denuncia
                    </a>
                    <a href="index.php?rota=mapa" class="<?= $classeItemSidebar(['mapa']) ?>">
                        <img src="assets/fonts/material-symbols/map_search.svg" alt="mapa" class="nav-icon" width="18"
                            height="18">
                        Mapa
                    </a>
                    <a href="index.php?rota=perfil_usuario" class="<?= $classeItemSidebar(['perfil_usuario']) ?>">
                        <img src="assets/fonts/material-symbols/badge.svg" alt="meu perfil" class="nav-icon" width="18"
                            height="18">
                        Meu perfil
                    </a>
                    <?php if ($ehAdminSidebar): ?>
                        <a href="index.php?rota=admin_dashboard"
                            class="<?= $classeItemSidebar(['admin_dashboard', 'admin_denuncias', 'listar_usuarios', 'listar_categorias_admin']) ?>">
                            <img src="assets/fonts/material-symbols/group.svg" alt="administracao" class="nav-icon"
                                width="18" height="18">
                            Administracao
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="painel-sidebar-exit">
                <form action="index.php?rota=sair" method="POST" class="m-0">
                    <?= csrfField() ?>
                    <button type="submit" class="painel-sidebar-logout-link">
                        <img src="assets/fonts/material-symbols/exit_to_app.svg" alt="sair" class="nav-icon" width="18"
                            height="18">
                        Sair
                    </button>
                </form>
            </div>
        </div>
    </div>
</aside>