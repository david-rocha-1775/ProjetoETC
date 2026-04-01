</main>

<footer class="py-2 bottom-0 start-0 end-0 w-100">
    <div class="dropdown position-fixed bottom-0 end-0 mb-3 me-3">
        <button class="btn btn-outline-secondary dropdown-toggle" id="bd-theme" type="button" aria-expanded="false"
            data-bs-toggle="dropdown" aria-label="Toggle theme">
            <i class="bi bi-circle-half"></i> Tema
        </button>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="bd-theme">
            <li>
                <button type="button" class="dropdown-item" onclick="setTema('light')">
                    ☀️ Claro
                </button>
            </li>
            <li>
                <button type="button" class="dropdown-item" onclick="setTema('dark')">
                    🌙 Escuro
                </button>
            </li>
        </ul>
    </div>

    <p class="text-center text-body-secondary mb-0">&copy;
        <?= date('Y') ?> Cidade Atenta — Projeto ETC
    </p>
</footer>

<script src="assets/js/painel-interacoes.js" defer></script>
<script>
    function setTema(tema) {
        if (tema === 'light' || tema === 'dark') {
            document.documentElement.setAttribute('data-bs-theme', tema);
            localStorage.setItem('tema_preferido', tema);
            return;
        }

        document.documentElement.removeAttribute('data-bs-theme');
        localStorage.removeItem('tema_preferido');
    }
</script>

</body>

</html>