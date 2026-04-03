<?php
// View da Página Inicial Pública
$tituloPagina = "Início - Projeto ETC";
?>
<?php include "view/templates/header.php"; ?>

<div class="container py-4">
    <section class="mb-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="card-title mb-3">Bem-vindo ao Cidade Atenta</h2>
                <p class="mb-2">Uma plataforma para denunciar problemas no seu bairro: buracos, falta de iluminação,
                    lixo irregular e mais.</p>
                <p class="mb-0">Ajude a prefeitura a melhorar a sua comunidade!</p>
            </div>
        </div>
    </section>

    <section>
        <div class="card shadow-sm">
            <div class="card-body">
                <h3 class="card-title mb-3">Como Funciona?</h3>
                <ol class="mb-0 ps-3">
                    <li class="mb-2"><strong>Cadastre-se</strong> - Crie sua conta gratuita</li>
                    <li class="mb-2"><strong>Denuncie</strong> - Registre o problema com foto e localização</li>
                    <li><strong>Acompanhe</strong> - Veja o status da sua denúncia</li>
                </ol>
            </div>
        </div>
    </section>
</div>

<?php include "view/templates/footer.php"; ?>