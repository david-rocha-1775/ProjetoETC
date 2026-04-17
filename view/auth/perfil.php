<?php
// View de gerenciamento do perfil do usuário autenticado

$totalMinhas = isset($totalMinhasDenuncias) ? (int) $totalMinhasDenuncias : 0;
$totalEmAnalise = isset($totaisMinhasDenunciasStatus['em_analise'])
    ? (int) $totaisMinhasDenunciasStatus['em_analise']
    : 0;
$totalResolvidas = isset($totaisMinhasDenunciasStatus['resolvido'])
    ? (int) $totaisMinhasDenunciasStatus['resolvido']
    : 0;
?>
<?php include "view/templates/header.php"; ?>

<section class="container py-4 perfil-page">
    <div class="perfil-header mb-4 animate-in">
        <div>
            <h2 class="mb-1">Meu Perfil</h2>
            <p class="text-muted mb-0">Gerencie seus dados de identidade e acompanhe suas solicitações mais recentes.
            </p>
        </div>
    </div>

    <form id="form-perfil-usuario" action="index.php?rota=processar_edicao_usuario" method="POST">
        <?= csrfField() ?>
        <div class="row g-4 align-items-stretch perfil-main-grid">
            <div class="col-12 col-xl-7">
                <article class="card perfil-card-form animate-in h-100">
                    <div class="card-body">
                        <h3 class="perfil-section-title mb-3">Identificação e contato</h3>

                        <div class="row g-3">
                            <div class="col-12">
                                <label for="nome" class="form-label">Nome completo *</label>
                                <input type="text" class="form-control" id="nome" name="nome"
                                    value="<?= htmlspecialchars($usuario->getNome()) ?>" required>
                            </div>

                            <div class="col-12">
                                <label for="email" class="form-label">E-mail *</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="<?= htmlspecialchars($usuario->getEmail()) ?>" required>
                            </div>
                        </div>
                    </div>
                </article>
            </div>

            <div class="col-12 col-xl-5">
                <article class="card perfil-card-form animate-in h-100">
                    <div class="card-body">
                        <h3 class="perfil-section-title mb-3">Troca de senha</h3>
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="senha_atual" class="form-label">Senha atual</label>
                                <div class="position-relative auth-password-field">
                                    <input type="password" class="form-control" id="senha_atual" name="senha_atual"
                                        autocomplete="current-password">
                                    <button type="button" class="btn btn-sm auth-password-toggle"
                                        data-toggle-password="senha_atual" aria-label="Mostrar senha"
                                        title="Mostrar senha">
                                        <img src="assets/fonts/material-symbols/visibility.svg" alt="" width="18"
                                            height="18" aria-hidden="true">
                                        <span class="visually-hidden">Mostrar senha</span>
                                    </button>
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="nova_senha" class="form-label">Nova senha</label>
                                <div class="position-relative auth-password-field">
                                    <input type="password" class="form-control" id="nova_senha" name="nova_senha"
                                        autocomplete="new-password">
                                    <button type="button" class="btn btn-sm auth-password-toggle"
                                        data-toggle-password="nova_senha" aria-label="Mostrar senha"
                                        title="Mostrar senha">
                                        <img src="assets/fonts/material-symbols/visibility.svg" alt="" width="18"
                                            height="18" aria-hidden="true">
                                        <span class="visually-hidden">Mostrar senha</span>
                                    </button>
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="confirmacao_senha" class="form-label">Confirmar nova senha</label>
                                <div class="position-relative auth-password-field">
                                    <input type="password" class="form-control" id="confirmacao_senha"
                                        name="confirmacao_senha" autocomplete="new-password">
                                    <button type="button" class="btn btn-sm auth-password-toggle"
                                        data-toggle-password="confirmacao_senha"
                                        aria-label="Mostrar confirmação de senha" title="Mostrar senha">
                                        <img src="assets/fonts/material-symbols/visibility.svg" alt="" width="18"
                                            height="18" aria-hidden="true">
                                        <span class="visually-hidden">Mostrar confirmação de senha</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>
            </div>
        </div>

        <div class="d-flex flex-wrap gap-2 mt-3 mb-4 animate-in">
            <button type="submit" class="btn btn-primary">Salvar alterações</button>
            <a href="index.php?rota=painel" class="btn btn-outline-secondary">Voltar ao painel</a>
        </div>
    </form>

    <article class="card border-danger perfil-card-perigo animate-in">
        <div class="card-body">
            <h3 class="h5 text-danger mb-2">Desativar conta</h3>
            <p class="mb-3">Esta ação encerra seu acesso e não pode ser desfeita.</p>

            <form action="index.php?rota=processar_exclusao_usuario" method="POST"
                onsubmit="return confirm('Tem certeza que deseja excluir sua conta? Essa ação não pode ser desfeita.');">
                <?= csrfField() ?>
                <div class="mb-3">
                    <label for="senha_confirmacao" class="form-label">Senha de confirmação *</label>
                    <input type="password" class="form-control" id="senha_confirmacao" name="senha_confirmacao"
                        required>
                </div>

                <button type="submit" class="btn btn-danger">Excluir minha conta</button>
            </form>
        </div>
    </article>

    <section class="perfil-denuncias mt-4">
        <div class="d-flex justify-content-between align-items-start gap-2 flex-wrap mb-3">
            <div>
                <h3 class="mb-1">Minhas Denúncias</h3>
                <p class="text-muted mb-0">Acompanhe o progresso e o histórico das suas solicitações.</p>
            </div>
            <a href="index.php?rota=nova_denuncia" class="btn btn-outline-primary btn-sm">Nova denúncia</a>
        </div>

        <?php if ($totalMinhas > 0): ?>
            <div class="row g-3 mb-4">
                <div class="col-12 col-md-4">
                    <article class="card shadow-sm h-100 painel-metrica-card">
                        <div class="card-body d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-uppercase small text-muted mb-1">Total de relatos</p>
                                <p class="display-6 mb-0 fw-semibold"><?= htmlspecialchars((string) $totalMinhas) ?></p>
                            </div>
                            <img src="assets/fonts/material-symbols/analytics4.svg" alt="total" width="24" height="24"
                                class="painel-metrica-icone">
                        </div>
                    </article>
                </div>
                <div class="col-12 col-md-4">
                    <article class="card shadow-sm h-100 painel-metrica-card">
                        <div class="card-body d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-uppercase small text-muted mb-1">Em análise</p>
                                <p class="display-6 mb-0 fw-semibold"><?= htmlspecialchars((string) $totalEmAnalise) ?></p>
                                <span class="small text-muted">total</span>
                            </div>
                            <img src="assets/fonts/material-symbols/pending.svg" alt="em análise" width="24" height="24"
                                class="painel-metrica-icone">
                        </div>
                    </article>
                </div>
                <div class="col-12 col-md-4">
                    <article class="card shadow-sm h-100 painel-metrica-card">
                        <div class="card-body d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-uppercase small text-muted mb-1">Resolvidos</p>
                                <p class="display-6 mb-0 fw-semibold"><?= htmlspecialchars((string) $totalResolvidas) ?></p>
                                <span class="small text-muted">total</span>
                            </div>
                            <img src="assets/fonts/material-symbols/task_alt_.svg" alt="resolvidos" width="24" height="24"
                                class="painel-metrica-icone">
                        </div>
                    </article>
                </div>
            </div>

            <div class="painel-denuncias-lista mb-3">
                <?php foreach ($minhasDenuncias as $denuncia): ?>
                    <?php
                    $idDenuncia = (int) $denuncia->getId();
                    $categoriaNome = $categoriasPorId[(int) $denuncia->getIdCategoria()] ?? 'Sem categoria';
                    $statusDenuncia = (string) $denuncia->getStatus();
                    $dataCriacaoRaw = (string) $denuncia->getDataCriacao();
                    $timestampCriacao = strtotime($dataCriacaoRaw);
                    $dataCriacaoFormatada = $timestampCriacao !== false ? date('d/m/y', $timestampCriacao) : $dataCriacaoRaw;
                    $descricaoResumida = htmlspecialchars(mb_strimwidth((string) $denuncia->getDescricao(), 0, 180, '...'));
                    $urlDetalhe = 'index.php?rota=detalhe_denuncia&id=' . rawurlencode((string) $idDenuncia);
                    ?>

                    <article class="card shadow-sm painel-denuncia-card animate-in">
                        <div class="painel-denuncia-thumb-wrap">
                            <?php if ($denuncia->getFotoPath()): ?>
                                <img src="<?= htmlspecialchars((string) $denuncia->getFotoPath()) ?>" alt="Foto da denúncia"
                                    class="painel-denuncia-thumb">
                            <?php else: ?>
                                <div class="painel-denuncia-thumb painel-denuncia-thumb-placeholder" aria-hidden="true"></div>
                            <?php endif; ?>
                        </div>

                        <div class="card-body d-flex flex-column painel-denuncia-conteudo">
                            <div
                                class="d-flex justify-content-between align-items-start gap-2 mb-2 flex-wrap painel-denuncia-cabecalho">
                                <h4 class="card-title mb-0 painel-denuncia-titulo">
                                    <?= htmlspecialchars((string) $denuncia->getTitulo()) ?>
                                </h4>
                                <div class="d-flex gap-2 align-items-center flex-wrap">
                                    <span class="badge text-bg-secondary"><?= htmlspecialchars($categoriaNome) ?></span>
                                    <span class="badge text-bg-dark"><?= htmlspecialchars($statusDenuncia) ?></span>
                                </div>
                            </div>

                            <p class="mb-1 painel-denuncia-meta"><strong>Localização:</strong>
                                <?= htmlspecialchars((string) $denuncia->getLocalizacao()) ?></p>
                            <p class="mb-2 painel-denuncia-meta"><strong>Criada em:</strong>
                                <?= htmlspecialchars($dataCriacaoFormatada) ?></p>
                            <p class="mb-3 painel-denuncia-resumo"><?= $descricaoResumida ?></p>

                            <div
                                class="d-flex align-items-center flex-wrap gap-2 mt-auto pt-2 border-top painel-denuncia-rodape">
                                <span class="small text-muted">ID #<?= htmlspecialchars((string) $idDenuncia) ?></span>
                                <a href="<?= htmlspecialchars($urlDetalhe) ?>" class="btn btn-primary btn-sm ms-md-auto">Ver
                                    detalhes</a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <?php if (($totalPaginasMinhasDenuncias ?? 0) > 1): ?>
                <?php
                $paginaAtualPerfil = isset($paginaDenuncias) ? (int) $paginaDenuncias : 1;
                $totalPaginasPerfil = (int) $totalPaginasMinhasDenuncias;
                $baseQueryPerfil = 'rota=perfil_usuario';
                $inicioPaginacaoPerfil = max(1, $paginaAtualPerfil - 2);
                $fimPaginacaoPerfil = min($totalPaginasPerfil, $paginaAtualPerfil + 2);
                ?>
                <div class="d-flex flex-column gap-3 mt-4">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="text-muted small">Total encontrado:
                            <strong><?= htmlspecialchars((string) $totalMinhas) ?></strong></span>
                        <span class="text-muted small">Página <?= htmlspecialchars((string) $paginaAtualPerfil) ?> de
                            <?= htmlspecialchars((string) $totalPaginasPerfil) ?></span>
                    </div>

                    <nav class="painel-paginacao-nav" aria-label="Paginação das minhas denúncias">
                        <ul class="pagination painel-paginacao flex-wrap mb-0">
                            <?php if ($paginaAtualPerfil > 1): ?>
                                <li class="page-item">
                                    <a class="page-link"
                                        href="index.php?<?= htmlspecialchars($baseQueryPerfil . '&minhas_pagina=' . ($paginaAtualPerfil - 1)) ?>">Anterior</a>
                                </li>
                            <?php endif; ?>

                            <?php if ($inicioPaginacaoPerfil > 1): ?>
                                <li class="page-item">
                                    <a class="page-link"
                                        href="index.php?<?= htmlspecialchars($baseQueryPerfil . '&minhas_pagina=1') ?>">1</a>
                                </li>
                                <?php if ($inicioPaginacaoPerfil > 2): ?>
                                    <li class="page-item disabled painel-paginacao-ellipsis"><span class="page-link">...</span></li>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php for ($pagina = $inicioPaginacaoPerfil; $pagina <= $fimPaginacaoPerfil; $pagina++): ?>
                                <li class="page-item <?= ($pagina === $paginaAtualPerfil) ? 'active' : '' ?>">
                                    <a class="page-link"
                                        href="index.php?<?= htmlspecialchars($baseQueryPerfil . '&minhas_pagina=' . $pagina) ?>"><?= htmlspecialchars((string) $pagina) ?></a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($fimPaginacaoPerfil < $totalPaginasPerfil): ?>
                                <?php if ($fimPaginacaoPerfil < $totalPaginasPerfil - 1): ?>
                                    <li class="page-item disabled painel-paginacao-ellipsis"><span class="page-link">...</span></li>
                                <?php endif; ?>
                                <li class="page-item">
                                    <a class="page-link"
                                        href="index.php?<?= htmlspecialchars($baseQueryPerfil . '&minhas_pagina=' . $totalPaginasPerfil) ?>"><?= htmlspecialchars((string) $totalPaginasPerfil) ?></a>
                                </li>
                            <?php endif; ?>

                            <?php if ($paginaAtualPerfil < $totalPaginasPerfil): ?>
                                <li class="page-item">
                                    <a class="page-link"
                                        href="index.php?<?= htmlspecialchars($baseQueryPerfil . '&minhas_pagina=' . ($paginaAtualPerfil + 1)) ?>">Próxima</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="card perfil-denuncia-vazia animate-in">
                <div class="card-body">
                    <p class="mb-3">Você ainda não registrou nenhuma denúncia.</p>
                    <a href="index.php?rota=nova_denuncia" class="btn btn-primary btn-sm">Criar primeira denúncia</a>
                </div>
            </div>
        <?php endif; ?>
    </section>

</section>

<script src="assets/js/auth-cadastro.js"></script>

<?php include "view/templates/footer.php"; ?>