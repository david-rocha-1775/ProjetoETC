<?php include "view/templates/header.php"; ?>

<div class="container py-4 painel-shell">
    <section class="painel-conteudo-col">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
            <div>
                <h2 class="mb-1">Dashboard Administrativo</h2>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-12 col-md-6 col-xl-3">
                <article class="card shadow-sm h-100 painel-metrica-card">
                    <div class="card-body d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-uppercase small text-muted mb-1">Denúncias ativas</p>
                            <p class="display-6 mb-0 fw-semibold">
                                <?= htmlspecialchars((string) $denunciasAtivas, ENT_QUOTES, 'UTF-8') ?>
                            </p>
                        </div>
                        <img src="assets/fonts/material-symbols/pending.svg" alt="denúncias ativas" width="24"
                            height="24" class="painel-metrica-icone">
                    </div>
                </article>
            </div>

            <div class="col-12 col-md-6 col-xl-3">
                <article class="card shadow-sm h-100 painel-metrica-card">
                    <div class="card-body d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-uppercase small text-muted mb-1">Usuários ativos</p>
                            <p class="display-6 mb-0 fw-semibold">
                                <?= htmlspecialchars((string) $usuariosAtivos, ENT_QUOTES, 'UTF-8') ?>
                            </p>
                        </div>
                        <img src="assets/fonts/material-symbols/group.svg" alt="usuários ativos" width="24" height="24"
                            class="painel-metrica-icone">
                    </div>
                </article>
            </div>

            <div class="col-12 col-md-6 col-xl-3">
                <article class="card shadow-sm h-100 painel-metrica-card">
                    <div class="card-body d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-uppercase small text-muted mb-1">Comentários ativos</p>
                            <p class="display-6 mb-0 fw-semibold">
                                <?= htmlspecialchars((string) $comentariosAtivos, ENT_QUOTES, 'UTF-8') ?>
                            </p>
                        </div>
                        <img src="assets/fonts/material-symbols/chat_bubble.svg" alt="comentários ativos" width="24"
                            height="24" class="painel-metrica-icone">
                    </div>
                </article>
            </div>

            <div class="col-12 col-md-6 col-xl-3">
                <article class="card shadow-sm h-100 painel-metrica-card">
                    <div class="card-body d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-uppercase small text-muted mb-1">Denúncias resolvidas</p>
                            <p class="display-6 mb-0 fw-semibold">
                                <?= htmlspecialchars((string) ($denunciasPorStatus['Resolvido'] ?? 0), ENT_QUOTES, 'UTF-8') ?>
                            </p>
                        </div>
                        <img src="assets/fonts/material-symbols/task_alt_.svg" alt="denúncias resolvidas" width="24"
                            height="24" class="painel-metrica-icone">
                    </div>
                </article>
            </div>
        </div>

        <section class="mb-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <h3 class="mb-0">Distribuição por status</h3>
            </div>

            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <article class="card shadow-sm h-100 painel-metrica-card">
                        <div class="card-body">
                            <p class="text-uppercase small text-muted mb-1">Aberto</p>
                            <p class="display-6 mb-0 fw-semibold">
                                <?= htmlspecialchars((string) ($denunciasPorStatus['Aberto'] ?? 0), ENT_QUOTES, 'UTF-8') ?>
                            </p>
                        </div>
                    </article>
                </div>

                <div class="col-12 col-md-4">
                    <article class="card shadow-sm h-100 painel-metrica-card">
                        <div class="card-body">
                            <p class="text-uppercase small text-muted mb-1">Em andamento</p>
                            <p class="display-6 mb-0 fw-semibold">
                                <?= htmlspecialchars((string) ($denunciasPorStatus['Em Andamento'] ?? 0), ENT_QUOTES, 'UTF-8') ?>
                            </p>
                        </div>
                    </article>
                </div>

                <div class="col-12 col-md-4">
                    <article class="card shadow-sm h-100 painel-metrica-card">
                        <div class="card-body">
                            <p class="text-uppercase small text-muted mb-1">Resolvido</p>
                            <p class="display-6 mb-0 fw-semibold">
                                <?= htmlspecialchars((string) ($denunciasPorStatus['Resolvido'] ?? 0), ENT_QUOTES, 'UTF-8') ?>
                            </p>
                        </div>
                    </article>
                </div>
            </div>
        </section>
    </section>
</div>

<?php include "view/templates/footer.php"; ?>