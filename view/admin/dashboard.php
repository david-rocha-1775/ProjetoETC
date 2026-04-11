<?php include "view/templates/header.php"; ?>

<section class="container py-4">
    <h2 class="mb-3">Dashboard Administrativo</h2>

    <div class="row g-3 mb-3">
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h3 class="h6 text-muted">Denúncias Ativas</h3>
                    <p class="display-6 mb-0"><?= htmlspecialchars((string) $denunciasAtivas, ENT_QUOTES, 'UTF-8') ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h3 class="h6 text-muted">Usuários Ativos</h3>
                    <p class="display-6 mb-0"><?= htmlspecialchars((string) $usuariosAtivos, ENT_QUOTES, 'UTF-8') ?></p>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h3 class="h6 text-muted">Comentários Ativos</h3>
                    <p class="display-6 mb-0"><?= htmlspecialchars((string) $comentariosAtivos, ENT_QUOTES, 'UTF-8') ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h3 class="h6 text-muted">Denúncias Resolvidas</h3>
                    <p class="display-6 mb-0">
                        <?= htmlspecialchars((string) ($denunciasPorStatus['Resolvido'] ?? 0), ENT_QUOTES, 'UTF-8') ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <h3 class="h5 mb-3">Distribuição por Status</h3>
            <div class="table-responsive">
                <table class="table table-striped table-bordered align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Aberto</td>
                            <td><?= htmlspecialchars((string) ($denunciasPorStatus['Aberto'] ?? 0), ENT_QUOTES, 'UTF-8') ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Em Andamento</td>
                            <td><?= htmlspecialchars((string) ($denunciasPorStatus['Em Andamento'] ?? 0), ENT_QUOTES, 'UTF-8') ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Resolvido</td>
                            <td><?= htmlspecialchars((string) ($denunciasPorStatus['Resolvido'] ?? 0), ENT_QUOTES, 'UTF-8') ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<?php include "view/templates/footer.php"; ?>