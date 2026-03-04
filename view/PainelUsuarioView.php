<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Usuário</title>
</head>
<body>

    <header>
        <h1>Cidade Limpa & Segura</h1>
        <nav>
            <?php if(isset($_SESSION['usuario_nome'])): ?>
                <span>Olá, <strong><?= $_SESSION['usuario_nome'] ?></strong>!</span>
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

    <main>
        <section class="boas-vindas">
            <h2>Denuncie problemas no seu bairro</h2>
            <p>Ajude a prefeitura a identificar buracos, falta de iluminação e mais.</p>

            <a href="index.php?rota=nova_denuncia">
                <button>Fazer uma Denúncia</button>
            </a>
        </section>

        <section class="ultimas-denuncias">
            <h3>Últimas Denúncias Registradas</h3>
            
            <?php if (count($denuncias) > 0): ?>
                <?php foreach ($denuncias as $d): ?>
                    <div style="border: 1px solid #ccc; margin-bottom: 10px; padding: 10px;">
                        <h4><?= htmlspecialchars($d['titulo']) ?></h4>
                        <p><strong>Localização:</strong> <?= htmlspecialchars($d['localizacao']) ?></p>
                        <p><strong>Status:</strong> <?= $d['status'] ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Nenhuma denúncia registrada ainda. Seja o primeiro!</p>
            <?php endif; ?>
        </section>
    </main>

</body>
</html>