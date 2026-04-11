<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
    $paginaCssExtra = (isset($paginaCssExtra) && is_array($paginaCssExtra)) ? $paginaCssExtra : [];
    $paginaJsHeadExtra = (isset($paginaJsHeadExtra) && is_array($paginaJsHeadExtra)) ? $paginaJsHeadExtra : [];
    ?>
    <script>
        (function () {
            try {
                var temaSalvo = localStorage.getItem('tema_preferido');
                if (temaSalvo === 'light' || temaSalvo === 'dark') {
                    document.documentElement.setAttribute('data-bs-theme', temaSalvo);
                } else {
                    document.documentElement.removeAttribute('data-bs-theme');
                }
            } catch (e) {
                document.documentElement.removeAttribute('data-bs-theme');
            }
        })();
    </script>
    <title>
        <?= htmlspecialchars(isset($tituloPagina) ? (string) $tituloPagina : 'Projeto ETC', ENT_QUOTES, 'UTF-8') ?>
    </title>
    <meta name="csrf-token" content="<?= htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8') ?>">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/estilo.css">
    <?php foreach ($paginaCssExtra as $cssExtra): ?>
        <link rel="stylesheet" href="<?= htmlspecialchars((string) $cssExtra) ?>">
    <?php endforeach; ?>
    <!-- Bootstrap JS Bundle -->
    <script src="assets/js/bootstrap.bundle.min.js" defer></script>
    <?php foreach ($paginaJsHeadExtra as $jsHeadExtra): ?>
        <script src="<?= htmlspecialchars((string) $jsHeadExtra) ?>" defer></script>
    <?php endforeach; ?>
    <link rel="icon" href="assets/images/favicon.ico">
</head>

<body class="d-flex flex-column min-vh-100">

    <?php $usuarioLogadoLayout = isset($_SESSION['logado']) && $_SESSION['logado'] === true; ?>

    <header class="border-bottom sticky-top w-100">
        <div class="container">
            <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-between py-3">
                <?php
                $logoSvg = file_get_contents(__DIR__ . '/../../assets/images/logo.svg');
                $logoSvg = preg_replace('/^<\?xml.*?\?>\s*/s', '', $logoSvg);
                $logoSvg = preg_replace('/^<!DOCTYPE.*?>\s*/s', '', $logoSvg);
                $logoSvg = str_replace('fill="#000000"', 'fill="currentColor"', $logoSvg);
                ?>

                <h1 class="site-brand mb-0">
                    <a href="index.php?rota=<?= (isset($_SESSION['logado']) && $_SESSION['logado'] === true) ? 'painel' : 'inicio' ?>"
                        class="site-brand-link">
                        <span class="site-brand-mark" aria-hidden="true">
                            <?= $logoSvg ?>
                        </span>
                        <span>Cidade Atenta</span>
                    </a>
                </h1>

                <ul class="nav col-12 col-md-auto mb-2 justify-content-center mb-md-0">

                    <li><a href="index.php?rota=<?= (isset($_SESSION['logado']) && $_SESSION['logado'] === true) ? 'painel' : 'inicio' ?>"
                            class="nav-link px-2 d-flex align-items-center gap-2">
                            <img src="assets/fonts/material-symbols/home.svg" alt="home" class="nav-icon" width="20"
                                height="20">
                            Inicio
                        </a></li>
                    <?php if (isset($_SESSION['logado']) && $_SESSION['logado'] === true): ?>
                        <li><a href="index.php?rota=mapa" class="nav-link px-2 d-flex align-items-center gap-2">
                                <img src="assets/fonts/material-symbols/map_search.svg" alt="mapa" class="nav-icon"
                                    width="20" height="20">
                                Mapa
                            </a></li>
                        <li><a href="index.php?rota=nova_denuncia" class="nav-link px-2 d-flex align-items-center gap-2">
                                <img src="assets/fonts/material-symbols/add_circle.svg" alt="nova denuncia" class="nav-icon"
                                    width="20" height="20">
                                Nova Denuncia
                            </a></li>
                    <?php endif; ?>

                </ul>

                <div class="col-md-auto text-end">
                    <?php if (isset($_SESSION['logado']) && $_SESSION['logado'] === true): ?>
                        <div class="d-flex align-items-center justify-content-end gap-2">
                            <form action="index.php?rota=sair" method="POST" class="m-0 d-inline-flex">
                                <?= csrfField() ?>
                                <button type="submit" class="btn btn-link p-1 text-reset" aria-label="Sair">
                                    <img src="assets/fonts/material-symbols/exit_to_app.svg" alt="sair" class="nav-icon"
                                        width="24" height="24">
                                </button>
                            </form>
                        </div>
                    <?php else: ?>

                        <div class="d-flex flex-column flex-md-row align-items-stretch justify-content-end gap-1">
                            <a href="index.php?rota=login" class="btn btn-outline-primary flex-fill text-nowrap">Entrar</a>
                            <a href="index.php?rota=cadastrar"
                                class="btn btn-primary flex-fill text-nowrap">Cadastrar-se</a>
                        </div>

                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <main class="flex-grow-1<?= $usuarioLogadoLayout ? ' main-com-sidebar' : '' ?>">

        <!-- Exibe mensagens de sucesso/erro vindas do Controller via $_SESSION -->
        <?php if (isset($_SESSION['mensagem'])): ?>
            <div class="mensagem <?= $_SESSION['tipo_mensagem'] ?? '' ?>">
                <p>
                    <?= htmlspecialchars((string) $_SESSION['mensagem'], ENT_QUOTES, 'UTF-8') ?>
                </p>
            </div>
            <?php
            // Limpa a mensagem para não aparecer de novo
            unset($_SESSION['mensagem']);
            unset($_SESSION['tipo_mensagem']);
            ?>
        <?php endif; ?>

        <?php if ($usuarioLogadoLayout): ?>
            <?php include "view/templates/sidebar-logado.php"; ?>
        <?php endif; ?>