<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= isset($tituloPagina) ? $tituloPagina : 'Projeto ETC' ?>
    </title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/estilo.css">
    <!-- Bootstrap JS Bundle -->
    <script src="assets/js/bootstrap.bundle.min.js" defer></script>
    <link rel="icon" href="assets/images/favicon.ico">
</head>

<body class="d-flex flex-column min-vh-100">

    <header class="border-bottom sticky-top w-100 bg-body">
        <div class="container">
            <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-between py-3">

                <h1><a href="index.php?rota=inicio" style="text-decoration:none; color:inherit;">Cidade Atenta</a></h1>

                <ul class="nav col-12 col-md-auto mb-2 justify-content-center mb-md-0">

                    <li><a href="index.php?rota=inicio" class="nav-link px-2">Inicio</a></li>
                    <li><a href="#" class="nav-link px-2">Mapa?</a></li>
                    <li><a href="index.php?rota=nova_denuncia" class="nav-link px-2">Nova Denuncia</a></li>

                </ul>

                <div class="col-md-3 text-end">
                    <?php if (isset($_SESSION['logado']) && $_SESSION['logado'] === true): ?>
                        <span class="me-3">Olá, <strong>
                                <?= $_SESSION['usuario_nome'] ?>
                            </strong>!</span>
                        <a href="index.php?rota=painel" class="btn btn-primary me-1 ">Meu Painel</a>

                        <a href="index.php?rota=sair" class="btn btn-outline-primary">Sair</a>
                    <?php else: ?>

                        <a href="index.php?rota=login" class="btn btn-outline-primary me-1">Entrar</a>

                        <a href="index.php?rota=cadastrar" class="btn btn-primary">Cadastrar-se</a>

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