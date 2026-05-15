<?php
// View da Página Inicial Pública
$tituloPagina = "Início - Cidade Atenta";
?>
<?php include "view/templates/header.php"; ?>

<main class="flex-grow-1">
    <!-- ============ HERO SECTION COM IMAGEM ============ -->
    <section class="home-hero d-flex align-items-center justify-content-center overflow-hidden">
        <div class="home-hero-overlay position-absolute top-0 start-0 w-100 h-100"></div>
        <div class="container position-relative z-1">
            <div class="home-hero-content text-center mx-auto">
                <h1 class="home-hero-title fw-bold mb-4">Sua voz
                    constrói o futuro da nossa cidade.</h1>
                <div class="d-flex gap-3 justify-content-center flex-wrap">
                    <button class="btn btn-primary" onclick="window.location.href='index.php?rota=cadastrar'">Começar
                        Agora</button>
                    <button class="btn btn-outline-primary"
                        onclick="document.querySelector('.como-funciona-section').scrollIntoView({behavior: 'smooth'});">Como
                        Funciona</button>
                </div>
            </div>
        </div>
    </section>

    <!-- ============ FUNCIONALIDADES / CIDADANIA EM AÇÃO ============ -->
    <section class="py-5 home-cidadania-section">
        <div class="container">
            <div class="mb-5">
                <h2 class="home-section-title fw-bold mb-0">Cidadania em Ação</h2>
            </div>

            <!-- Bento Grid -->
            <div class="d-grid gap-4">
                <!-- Linha 1: 2fr 1fr split -->
                <div class="row g-4">
                    <!-- Card Cuidado Coletivo (8 cols) -->
                    <div class="col-12 col-xl-8">
                        <div class="custom-card glass h-100 d-flex flex-column justify-content-between p-4 p-lg-5">
                            <div class="d-flex gap-3 align-items-start flex-wrap flex-sm-nowrap">
                                <img src="assets/fonts/material-symbols/group.svg" alt="group"
                                    class="home-feature-icon flex-shrink-0">
                                <div>
                                    <h3 class="home-card-title h4 mt-0 mb-3">Cuidado Coletivo
                                    </h3>
                                    <p class="home-card-copy mb-0">Unimos a inteligência
                                        dos moradores para cuidar de cada bairro, transformando problemas em soluções
                                        rápidas.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card Reporte Fácil (4 cols) -->
                    <div class="col-12 col-xl-4">
                        <div
                            class="custom-card glass h-100 d-flex flex-column justify-content-center align-items-center text-center p-4 p-lg-5">
                            <div>
                                <img src="assets/fonts/material-symbols/description.svg" alt="description"
                                    class="home-feature-icon d-block mb-3 mx-auto">
                                <h3 class="home-card-title h4 mt-0 mb-3">Reporte Fácil</h3>
                                <p class="home-card-copy mb-0">Identifique algo? Envie
                                    fotos e localização em segundos pelo nosso portal institucional.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Linha 2: 1fr 2fr split -->
                <div class="row g-4">
                    <!-- Card Acompanhamento (4 cols) -->
                    <div class="col-12 col-xl-4">
                        <div
                            class="custom-card glass h-100 d-flex flex-column justify-content-center align-items-center text-center p-4 p-lg-5">
                            <div>
                                <img src="assets/fonts/material-symbols/schedule.svg" alt="schedule"
                                    class="home-feature-icon d-block mb-3 mx-auto">
                                <h3 class="home-card-title h4 mt-0 mb-3">Acompanhamento</h3>
                                <p class="home-card-copy mb-0">Transparência total. Receba
                                    notificações em tempo real sobre o status das suas solicitações.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Card Mapa Interativo (8 cols) -->
                    <div class="col-12 col-xl-8">
                        <div
                            class="custom-card glass h-100 d-flex flex-column justify-content-center align-items-center text-center position-relative overflow-hidden p-4 p-lg-5">
                            <div class="position-relative z-1 d-flex flex-column align-items-center">
                                <img src="assets/fonts/material-symbols/map.svg" alt="map" class="home-map-icon mb-3">
                                <h3 class="home-card-title h4 mt-0 mb-2 text-center">
                                    Mapa Interativo</h3>
                                <p class="home-card-copy text-center mb-0 home-map-copy">
                                    Visualize os pontos de atenção da cidade e as melhores já realizadas num dashboard
                                    georeferenciado.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Linha 3: Full Width - Impacto Social -->
                <div class="row g-4">
                    <div class="col-12">
                        <div class="custom-card glass h-100 p-4 p-lg-5">
                            <div class="row g-4 g-lg-5 align-items-center">
                                <div class="col-12 col-lg-6">
                                    <img src="assets/fonts/material-symbols/trending_up.svg" alt="trending_up"
                                        class="home-impact-icon mb-3 d-block">
                                    <h2 class="home-impact-title h2 mt-0 mb-3 fw-bold">
                                        Impacto Social</h2>
                                    <p class="home-impact-copy mb-0">Transformando cada cidadão
                                        em um agente de mudança. Juntos, facilitamos o diálogo com o setor público para
                                        garantir que cada problema encontrado seja um problema ouvido.</p>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <div class="row g-3 g-md-4">
                                        <div class="col-12 col-md-6 text-center">
                                            <div class="home-stat home-stat-primary mb-2">
                                                Voz Ativa</div>

                                        </div>
                                        <div class="col-12 col-md-6 text-center">
                                            <div class="home-stat home-stat-secondary mb-2">
                                                Transparência Total</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============ COMO FUNCIONA ============ -->
    <section class="como-funciona-section py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="home-section-title fw-bold mb-3 mt-0">Pronto para
                    transformar sua vizinhança?</h2>
                <p class="home-final-copy mx-auto mb-0">Junte-se a milhares de cidadãos ativos
                    que já estão fazendo a diferença na Cidade Atenta.</p>
            </div>
            <div class="text-center">
                <button class="btn btn-primary" onclick="window.location.href='index.php?rota=cadastrar'">Criar minha
                    conta</button>
            </div>
        </div>
    </section>
</main>

<?php include "view/templates/footer.php"; ?>