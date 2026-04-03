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
        <?= isset($tituloPagina) ? $tituloPagina : 'Projeto ETC' ?>
    </title>
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

    <header class="border-bottom sticky-top w-100 bg-body">
        <div class="container">
            <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-between py-3">

                <h1>
                    <a href="index.php?rota=<?= (isset($_SESSION['logado']) && $_SESSION['logado'] === true) ? 'painel' : 'inicio' ?>"
                        style="text-decoration:none; color:inherit;">
                        Cidade Atenta
                    </a>
                </h1>

                <ul class="nav col-12 col-md-auto mb-2 justify-content-center mb-md-0">

                    <li><a href="index.php?rota=<?= (isset($_SESSION['logado']) && $_SESSION['logado'] === true) ? 'painel' : 'inicio' ?>"
                            class="nav-link px-2">Inicio</a></li>
                    <li><a href="index.php?rota=mapa" class="nav-link px-2">Mapa</a></li>
                    <li><a href="index.php?rota=nova_denuncia" class="nav-link px-2">Nova Denuncia</a></li>

                </ul>

                <div class="col-md-auto text-end">
                    <?php if (isset($_SESSION['logado']) && $_SESSION['logado'] === true): ?>
                        <div
                            class="d-flex flex-column flex-md-row align-items-stretch align-items-md-center justify-content-end gap-1">
                            <span class="text-center text-md-end me-md-2 mb-1 mb-md-0">Olá, <strong>
                                    <?= $_SESSION['usuario_nome'] ?>
                                </strong>!</span>

                            <?php if (isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] === 'admin'): ?>
                                <a href="index.php?rota=listar_usuarios"
                                    class="btn btn-secondary flex-fill text-nowrap">Admin</a>
                            <?php endif; ?>

                            <a href="index.php?rota=perfil_usuario" class="btn btn-primary flex-fill text-nowrap">Meu
                                Perfil</a>

                            <a href="index.php?rota=sair" class="btn btn-outline-primary flex-fill text-nowrap">Sair</a>
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

    <main class="flex-grow-1">

        <!-- Exibe mensagens de sucesso/erro vindas do Controller via $_SESSION -->
        <?php if (isset($_SESSION['mensagem'])): ?>
            <div class="mensagem <?= $_SESSION['tipo_mensagem'] ?? '' ?>">
                <p>
                    <?= $_SESSION['mensagem'] ?>
                </p>
            </div>
            <?php
            // Limpa a mensagem para não aparecer de novo
            unset($_SESSION['mensagem']);
            unset($_SESSION['tipo_mensagem']);
            ?>
        <?php endif; ?>