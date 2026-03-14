</main>

<footer class="py-2 bottom-0 start-0 end-0 w-100">
<div class="dropdown position-fixed bottom-0 end-0 mb-3 me-3">
    <button class="btn btn-outline-secondary dropdown-toggle" id="bd-theme" type="button" aria-expanded="false" data-bs-toggle="dropdown" aria-label="Toggle theme">
        <i class="bi bi-circle-half"></i> Tema
    </button>
    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="bd-theme">
        <li>
            <button type="button" class="dropdown-item" onclick="document.documentElement.setAttribute('data-bs-theme', 'light')">
                ☀️ Claro
            </button>
        </li>
        <li>
            <button type="button" class="dropdown-item" onclick="document.documentElement.setAttribute('data-bs-theme', 'dark')">
                🌙 Escuro
            </button>
        </li>
        <li>
            <button type="button" class="dropdown-item" onclick="document.documentElement.removeAttribute('data-bs-theme')">
                ⚙️ Automático
            </button>
        </li>
    </ul>
</div>

    <p class="text-center text-body-secondary">&copy;
        <?= date('Y') ?> Cidade Atenta — Projeto ETC
    </p>
</footer>

</body>

</html>