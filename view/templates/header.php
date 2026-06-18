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
                            Início
                        </a></li>
                    <?php if (isset($_SESSION['logado']) && $_SESSION['logado'] === true): ?>
                        <li><a href="index.php?rota=mapa" class="nav-link px-2 d-flex align-items-center gap-2">
                                <img src="assets/fonts/material-symbols/map_search.svg" alt="mapa" class="nav-icon"
                                    width="20" height="20">
                                Mapa
                            </a></li>
                        <li><a href="index.php?rota=nova_denuncia" class="nav-link px-2 d-flex align-items-center gap-2">
                                <img src="assets/fonts/material-symbols/add_circle.svg" alt="nova denúncia" class="nav-icon"
                                    width="20" height="20">
                                Nova Denúncia
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

    <?php
    $mensagemSessao = isset($_SESSION['mensagem']) ? (string) $_SESSION['mensagem'] : '';
    $tipoMensagemSessao = isset($_SESSION['tipo_mensagem']) ? (string) $_SESSION['tipo_mensagem'] : 'info';
    $configuracoesToast = [
        'sucesso' => [
            'classe' => 'success',
            'titulo' => 'Sucesso',
            'fechar' => 'btn-close-white',
            'delay' => 5500,
        ],
        'erro' => [
            'classe' => 'danger',
            'titulo' => 'Erro',
            'fechar' => 'btn-close-white',
            'delay' => 8500,
        ],
        'aviso' => [
            'classe' => 'warning',
            'titulo' => 'Atenção',
            'fechar' => '',
            'delay' => 7000,
        ],
        'info' => [
            'classe' => 'info',
            'titulo' => 'Informação',
            'fechar' => '',
            'delay' => 6000,
        ],
    ];
    $toastConfiguracao = $configuracoesToast[$tipoMensagemSessao] ?? $configuracoesToast['info'];
    $toastTextoClasse = $toastConfiguracao['classe'] === 'warning' ? 'dark' : 'white';
    ?>

    <?php if ($mensagemSessao !== ''): ?>
        <div class="toast-container position-fixed site-toast-container p-3">
            <div class="toast show site-toast bg-<?= htmlspecialchars($toastConfiguracao['classe'], ENT_QUOTES, 'UTF-8') ?> text-<?= htmlspecialchars($toastTextoClasse, ENT_QUOTES, 'UTF-8') ?> border-0 shadow-lg"
                role="alert" aria-live="assertive" aria-atomic="true" data-feedback-toast="1"
                data-toast-delay="<?= htmlspecialchars((string) $toastConfiguracao['delay'], ENT_QUOTES, 'UTF-8') ?>">
                <div class="d-flex align-items-start">
                    <div class="toast-body flex-grow-1 pe-2">
                        <div class="fw-semibold mb-1"><?= htmlspecialchars($toastConfiguracao['titulo'], ENT_QUOTES, 'UTF-8') ?></div>
                        <div><?= htmlspecialchars($mensagemSessao, ENT_QUOTES, 'UTF-8') ?></div>
                    </div>
                    <button type="button" class="btn-close <?= htmlspecialchars($toastConfiguracao['fechar'], ENT_QUOTES, 'UTF-8') ?> me-3 mt-3"
                        aria-label="Fechar" data-feedback-toast-close="1"></button>
                </div>
            </div>
        </div>
        <?php
        unset($_SESSION['mensagem']);
        unset($_SESSION['tipo_mensagem']);
        ?>
    <?php endif; ?>

    <main class="flex-grow-1<?= $usuarioLogadoLayout ? ' main-com-sidebar' : '' ?>">

        <?php if ($usuarioLogadoLayout): ?>
            <?php include "view/templates/sidebar-logado.php"; ?>
        <?php endif; ?>