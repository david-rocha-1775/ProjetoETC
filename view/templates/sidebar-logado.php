<?php
$rotaAtualSidebar = isset($_GET['rota']) ? (string) $_GET['rota'] : '';
$ehAdminSidebar = isset($_SESSION['usuario_tipo']) && in_array($_SESSION['usuario_tipo'], ['admin', 'gestor'], true);

$classeItemSidebar = static function (array $rotasAtivas) use ($rotaAtualSidebar): string {
    return in_array($rotaAtualSidebar, $rotasAtivas, true)
        ? 'btn btn-primary w-100 text-start d-inline-flex align-items-center gap-2 painel-sidebar-item-ativo'
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
        <h3 class="offcanvas-title h5 mb-0" id="sidebarLogadoMobileLabel">Navegação</h3>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Fechar"></button>
    </div>
    <div class="offcanvas-body d-flex flex-column gap-3">
        <a href="index.php?rota=painel" class="<?= $classeItemSidebar(['painel']) ?>">
            <img src="assets/fonts/material-symbols/home.svg" alt="início" class="nav-icon" width="18" height="18">
            Início
        </a>
        <a href="index.php?rota=nova_denuncia" class="<?= $classeItemSidebar(['nova_denuncia']) ?>">
            <img src="assets/fonts/material-symbols/add_circle.svg" alt="nova denúncia" class="nav-icon" width="18"
                height="18">
            Nova denúncia
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
            <div class="pt-2 border-top">
                <p class="small text-muted mb-2">Administração</p>
                <div class="d-grid gap-2">
                    <a href="index.php?rota=admin_dashboard" class="<?= $classeItemSidebar(['admin_dashboard']) ?>">
                        <img src="assets/fonts/material-symbols/analytics4.svg" alt="dashboard admin" class="nav-icon"
                            width="18" height="18">
                        Dashboard Admin
                    </a>
                    <a href="index.php?rota=admin_denuncias" class="<?= $classeItemSidebar(['admin_denuncias']) ?>">
                        <img src="assets/fonts/material-symbols/description.svg" alt="gerenciar denúncias" class="nav-icon"
                            width="18" height="18">
                        Gerenciar Denúncias
                    </a>
                    <a href="index.php?rota=listar_usuarios" class="<?= $classeItemSidebar(['listar_usuarios']) ?>">
                        <img src="assets/fonts/material-symbols/group.svg" alt="gerenciar usuários" class="nav-icon"
                            width="18" height="18">
                        Gerenciar Usuários
                    </a>
                    <a href="index.php?rota=listar_categorias_admin"
                        class="<?= $classeItemSidebar(['listar_categorias_admin']) ?>">
                        <img src="assets/fonts/material-symbols/tune.svg" alt="gerenciar categorias" class="nav-icon"
                            width="18" height="18">
                        Gerenciar Categorias
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<aside class="d-none d-lg-block painel-sidebar-col" aria-label="Menu lateral do usuário">
    <div class="card shadow-sm painel-sidebar-card h-100">
        <div class="card-body painel-sidebar-body h-100">
            <div class="painel-sidebar-main d-flex flex-column gap-3">
                <div>
                    <h3 class="h5 mb-1">Navegação</h3>
                    <p class="text-muted small mb-0">Acesse rapidamente suas principais acoes.</p>
                </div>

                <div class="d-grid gap-2">
                    <a href="index.php?rota=painel" class="<?= $classeItemSidebar(['painel']) ?>">
                        <img src="assets/fonts/material-symbols/home.svg" alt="início" class="nav-icon" width="18"
                            height="18">
                        Início
                    </a>
                    <a href="index.php?rota=nova_denuncia" class="<?= $classeItemSidebar(['nova_denuncia']) ?>">
                        <img src="assets/fonts/material-symbols/add_circle.svg" alt="nova denúncia" class="nav-icon"
                            width="18" height="18">
                        Nova denúncia
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
                        <div class="pt-2 border-top">
                            <p class="small text-muted mb-2">Administração</p>
                            <div class="d-grid gap-2">
                                <a href="index.php?rota=admin_dashboard"
                                    class="<?= $classeItemSidebar(['admin_dashboard']) ?>">
                                    <img src="assets/fonts/material-symbols/analytics4.svg" alt="dashboard admin"
                                        class="nav-icon" width="18" height="18">
                                    Dashboard Admin
                                </a>
                                <a href="index.php?rota=admin_denuncias"
                                    class="<?= $classeItemSidebar(['admin_denuncias']) ?>">
                                    <img src="assets/fonts/material-symbols/description.svg" alt="gerenciar denúncias"
                                        class="nav-icon" width="18" height="18">
                                    Gerenciar Denúncias
                                </a>
                                <a href="index.php?rota=listar_usuarios"
                                    class="<?= $classeItemSidebar(['listar_usuarios']) ?>">
                                    <img src="assets/fonts/material-symbols/group.svg" alt="gerenciar usuários"
                                        class="nav-icon" width="18" height="18">
                                    Gerenciar Usuários
                                </a>
                                <a href="index.php?rota=listar_categorias_admin"
                                    class="<?= $classeItemSidebar(['listar_categorias_admin']) ?>">
                                    <img src="assets/fonts/material-symbols/tune.svg" alt="gerenciar categorias"
                                        class="nav-icon" width="18" height="18">
                                    Gerenciar Categorias
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</aside>