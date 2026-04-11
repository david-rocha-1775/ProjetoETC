<?php
// View da Página Inicial Pública
$tituloPagina = "Início - Cidade Atenta";
?>
<?php include "view/templates/header.php"; ?>

<main class="flex-grow-1">
    <!-- ============ HERO SECTION COM IMAGEM ============ -->
    <section
        style="position: relative; min-height: 600px; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, rgba(11, 19, 38, 0.85) 0%, rgba(15, 20, 38, 0.75) 100%), url('assets/images/fundo_home.png') center/cover; overflow: hidden;">
        <div
            style="position: absolute; inset: 0; background: radial-gradient(circle at 40% 60%, rgba(156, 202, 255, 0.08) 0%, transparent 50%); pointer-events: none;">
        </div>
        <div class="container" style="position: relative; z-index: 1;">
            <div style="text-align: center; max-width: 800px; margin: 0 auto;">
                <h1 style="font-size: 3.5rem; font-weight: 700; margin-bottom: 1.5rem; line-height: 1.2;">Sua voz
                    constrói o futuro da nossa cidade.</h1>
                <div style="display: flex; gap: 1.5rem; justify-content: center; flex-wrap: wrap;">
                    <button class="btn btn-primary" onclick="window.location.href='index.php?rota=cadastrar'"
                        style="font-size: 1rem;">Começar Agora</button>
                    <button class="btn btn-outline-primary"
                        onclick="document.querySelector('.como-funciona-section').scrollIntoView({behavior: 'smooth'});"
                        style="font-size: 1rem;">Como Funciona</button>
                </div>
            </div>
        </div>
    </section>

    <!-- ============ FUNCIONALIDADES / CIDADANIA EM AÇÃO ============ -->
    <section style="padding: 4rem 0; background-color: var(--color-dark-bg);">
        <div class="container">
            <div style="margin-bottom: 3rem;">
                <h2 style="font-size: 2.5rem; font-weight: 700; margin: 0;">Cidadania em Ação</h2>
            </div>

            <!-- Bento Grid -->
            <div style="display: grid; gap: 1.5rem;">
                <!-- Linha 1: 2fr 1fr split -->
                <div style="display: grid; grid-template-columns: repeat(12, 1fr); gap: 1.5rem;">
                    <!-- Card Cuidado Coletivo (8 cols) -->
                    <div style="grid-column: span 8;">
                        <div class="custom-card glass home-card-flex">
                            <div style="display: flex; gap: 1.5rem; align-items: flex-start;">
                                <img src="assets/fonts/material-symbols/group.svg" alt="group"
                                    style="width: 48px; height: 48px; flex-shrink: 0;">
                                <div>
                                    <h3 style="font-size: 1.5rem; margin-bottom: 1rem; margin-top: 0;">Cuidado Coletivo
                                    </h3>
                                    <p style="font-size: 0.95rem; line-height: 1.6; margin: 0;">Únimos a inteligência
                                        dos moradores para cuidar de cada bairro, transformando problemas em soluções
                                        rápidas.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card Reporte Fácil (4 cols) -->
                    <div style="grid-column: span 4;">
                        <div class="custom-card glass home-card-flex home-card-centered">
                            <div>
                                <img src="assets/fonts/material-symbols/description.svg" alt="description"
                                    style="width: 48px; height: 48px; display: block; margin-bottom: 1rem;">
                                <h3 style="font-size: 1.5rem; margin-bottom: 1rem; margin-top: 0;">Reporte Fácil</h3>
                                <p style="font-size: 0.95rem; line-height: 1.6; margin: 0;">Identifique algo? Envie
                                    fotos e localização em segundos pelo nosso portal institucional.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Linha 2: 1fr 2fr split -->
                <div style="display: grid; grid-template-columns: repeat(12, 1fr); gap: 1.5rem;">
                    <!-- Card Acompanhamento (4 cols) -->
                    <div style="grid-column: span 4;">
                        <div class="custom-card glass home-card-flex home-card-centered">
                            <div>
                                <img src="assets/fonts/material-symbols/schedule.svg" alt="schedule"
                                    style="width: 48px; height: 48px; display: block; margin-bottom: 1rem;">
                                <h3 style="font-size: 1.5rem; margin-bottom: 1rem; margin-top: 0;">Acompanhamento</h3>
                                <p style="font-size: 0.95rem; line-height: 1.6; margin: 0;">Transparência total. Receba
                                    notificações em tempo real sobre o status das suas solicitações.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Card Mapa Interativo (8 cols) -->
                    <div style="grid-column: span 8;">
                        <div class="custom-card glass home-card-flex-center">
                            <div
                                style="position: relative; z-index: 1; display: flex; flex-direction: column; align-items: center;">
                                <img src="assets/fonts/material-symbols/map.svg" alt="map"
                                    style="width: 56px; height: 56px; margin-bottom: 1rem;">
                                <h3
                                    style="font-size: 1.5rem; margin-bottom: 0.5rem; text-align: center; margin-top: 0;">
                                    Mapa Interativo</h3>
                                <p style="font-size: 0.95rem; text-align: center; max-width: 250px; margin: 0;">
                                    Visualize os pontos de atenção da cidade e as melhores já realizadas num dashboard
                                    georeferenciado.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Linha 3: Full Width - Impacto Social -->
                <div style="display: grid; grid-template-columns: repeat(12, 1fr); gap: 1.5rem;">
                    <div style="grid-column: span 12;">
                        <div class="custom-card glass home-card-large">
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; align-items: center;">
                                <div>
                                    <img src="assets/fonts/material-symbols/trending_up.svg" alt="trending_up"
                                        style="width: 64px; height: 64px; margin-bottom: 1rem; display: block;">
                                    <h2 style="font-size: 2rem; margin-bottom: 1rem; font-weight: 700; margin-top: 0;">
                                        Impacto Social</h2>
                                    <p style="font-size: 1rem; line-height: 1.7; margin: 0;">Transformando cada cidadão
                                        em um agente de mudança. Juntos, facilitamos o diálogo com o setor público para
                                        garantir que cada problema encontrado seja um problema ouvido.</p>
                                </div>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                                    <div style="text-align: center;">
                                        <div
                                            style="font-size: 2.5rem; font-weight: 700; color: var(--color-primary); margin-bottom: 0.5rem; letter-spacing: 1px;">
                                            Voz Ativa</div>

                                    </div>
                                    <div style="text-align: center;">
                                        <div
                                            style="font-size: 2.5rem; font-weight: 700; color: var(--color-secondary); margin-bottom: 0.5rem;">
                                            Transparência Total</div>
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
    <section class="como-funciona-section" style="padding: 4rem 0;">
        <div class="container">
            <div style="text-align: center; margin-bottom: 3rem;">
                <h2 style="font-size: 2.5rem; font-weight: 700; margin-bottom: 1rem; margin-top: 0;">Pronto para
                    transformar sua vizinhança?</h2>
                <p style="font-size: 1.15rem; max-width: 600px; margin: 0 auto;">Junte-se a milhares de cidadãos ativos
                    que já estão fazendo a diferença na Cidade Atenta.</p>
            </div>
            <div style="text-align: center;">
                <button class="btn btn-primary" onclick="window.location.href='index.php?rota=cadastrar'"
                    style="font-size: 1rem;">Criar minha conta</button>
            </div>
        </div>
    </section>
</main>

<?php include "view/templates/footer.php"; ?>