<?php // Esta View é chamada pelo PainelUsuarioController.php que já passou os dados ?>
<?php include "view/templates/header.php"; ?>

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
                <h4><?= htmlspecialchars($d->getTitulo()) ?></h4>
                <p><strong>Localização:</strong> <?= htmlspecialchars($d->getLocalizacao()) ?></p>
                <p><strong>Status:</strong> <?= htmlspecialchars($d->getStatus()) ?></p>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Nenhuma denúncia registrada ainda. Seja o primeiro!</p>
    <?php endif; ?>
</section>

<?php include "view/templates/footer.php"; ?>