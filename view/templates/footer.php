</main>

<footer class="py-2 bottom-0 start-0 end-0 w-100">
    <div class="position-fixed bottom-0 end-0 mb-3 me-3">
        <button class="btn btn-outline-secondary" id="bd-theme" type="button" onclick="alternarTema()"
            aria-label="Toggle theme" style="color: inherit; display: flex; align-items: center; gap: 0.5rem;">
            <img id="theme-icon" src="assets/fonts/material-symbols/dark_mode.svg" alt="theme" style="width: 24px; height: 24px;"></button>
    </div>

    <p class="text-center text-body-secondary mb-0">&copy;
        <?= date('Y') ?> Cidade Atenta — Projeto ETC
    </p>
</footer>

<script src="assets/js/painel-interacoes.js" defer></script>
<script>
    function alternarTema() {
        const tema = document.documentElement.getAttribute('data-bs-theme') || 'dark';
        const novoTema = tema === 'dark' ? 'light' : 'dark';
        
        document.documentElement.setAttribute('data-bs-theme', novoTema);
        localStorage.setItem('tema_preferido', novoTema);
        atualizarIconeTema();
    }

    function atualizarIconeTema() {
        const tema = document.documentElement.getAttribute('data-bs-theme') || 'dark';
        const icone = document.getElementById('theme-icon');
        
        if (tema === 'dark') {
            icone.src = 'assets/fonts/material-symbols/dark_mode.svg';
        } else {
            icone.src = 'assets/fonts/material-symbols/light_mode.svg';
        }
    }

    // Inicializar ícone ao carregar a página
    document.addEventListener('DOMContentLoaded', atualizarIconeTema);
</script>

</body>

</html>