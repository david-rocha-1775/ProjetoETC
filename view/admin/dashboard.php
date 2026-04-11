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
                            <p class="text-uppercase small text-muted mb-1">Denuncias ativas</p>
                            <p class="display-6 mb-0 fw-semibold">
                                <?= htmlspecialchars((string) $denunciasAtivas, ENT_QUOTES, 'UTF-8') ?></p>
                        </div>
                        <img src="assets/fonts/material-symbols/pending.svg" alt="denuncias ativas" width="24"
                            height="24" class="painel-metrica-icone">
                    </div>
                </article>
            </div>

            <div class="col-12 col-md-6 col-xl-3">
                <article class="card shadow-sm h-100 painel-metrica-card">
                    <div class="card-body d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-uppercase small text-muted mb-1">Usuarios ativos</p>
                            <p class="display-6 mb-0 fw-semibold">
                                <?= htmlspecialchars((string) $usuariosAtivos, ENT_QUOTES, 'UTF-8') ?></p>
                        </div>
                        <img src="assets/fonts/material-symbols/group.svg" alt="usuarios ativos" width="24" height="24"
                            class="painel-metrica-icone">
                    </div>
                </article>
            </div>

            <div class="col-12 col-md-6 col-xl-3">
                <article class="card shadow-sm h-100 painel-metrica-card">
                    <div class="card-body d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-uppercase small text-muted mb-1">Comentarios ativos</p>
                            <p class="display-6 mb-0 fw-semibold">
                                <?= htmlspecialchars((string) $comentariosAtivos, ENT_QUOTES, 'UTF-8') ?></p>
                        </div>
                        <img src="assets/fonts/material-symbols/chat_bubble.svg" alt="comentarios ativos" width="24"
                            height="24" class="painel-metrica-icone">
                    </div>
                </article>
            </div>

            <div class="col-12 col-md-6 col-xl-3">
                <article class="card shadow-sm h-100 painel-metrica-card">
                    <div class="card-body d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-uppercase small text-muted mb-1">Denuncias resolvidas</p>
                            <p class="display-6 mb-0 fw-semibold">
                                <?= htmlspecialchars((string) ($denunciasPorStatus['Resolvido'] ?? 0), ENT_QUOTES, 'UTF-8') ?>
                            </p>
                        </div>
                        <img src="assets/fonts/material-symbols/task_alt_.svg" alt="denuncias resolvidas" width="24"
                            height="24" class="painel-metrica-icone">
                    </div>
                </article>
            </div>
        </div>

        <section class="mb-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <h3 class="mb-0">Distribuicao por status</h3>
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