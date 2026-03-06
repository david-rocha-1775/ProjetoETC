<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= isset($tituloPagina) ? $tituloPagina : 'Projeto ETC' ?>
    </title>
    <link rel="stylesheet" href="assets/css/estilo.css">
</head>

<body>

    <header>
        <h1><a href="index.php?rota=inicio" style="text-decoration:none; color:inherit;">Cidade Limpa & Segura</a></h1>
        <nav>
            <?php if (isset($_SESSION['logado']) && $_SESSION['logado'] === true): ?>
                <span>Olá, <strong>
                        <?= $_SESSION['usuario_nome'] ?>
                    </strong>!</span>
                <a href="index.php?rota=painel">
                    <button>Meu Painel</button>
                </a>
                <a href="index.php?rota=sair">
                    <button>Sair</button>
                </a>
            <?php else: ?>
                <a href="index.php?rota=login">
                    <button>Entrar</button>
                </a>
                <a href="index.php?rota=cadastrar">
                    <button>Cadastrar-se</button>
                </a>
            <?php endif; ?>
        </nav>
    </header>

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

    <main>