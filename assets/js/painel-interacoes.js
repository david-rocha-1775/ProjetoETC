document.addEventListener('DOMContentLoaded', function () {
    const feedbackContainer = document.getElementById('painel-feedback');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    function escapeHtml(valor) {
        return String(valor ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function mostrarFeedback(tipo, mensagem) {
        if (!feedbackContainer) {
            return;
        }

        const tipoNormalizado = ['success', 'danger', 'warning', 'info'].includes(tipo) ? tipo : 'info';
        feedbackContainer.textContent = '';

        const alerta = document.createElement('div');
        alerta.className = 'alert alert-' + tipoNormalizado + ' alert-dismissible fade show';
        alerta.setAttribute('role', 'alert');
        alerta.textContent = String(mensagem || 'Operação concluída.');

        const botaoFechar = document.createElement('button');
        botaoFechar.type = 'button';
        botaoFechar.className = 'btn-close';
        botaoFechar.setAttribute('data-bs-dismiss', 'alert');
        botaoFechar.setAttribute('aria-label', 'Fechar');
        alerta.appendChild(botaoFechar);

        feedbackContainer.appendChild(alerta);

        window.clearTimeout(window.__painelFeedbackTimeout);
        window.__painelFeedbackTimeout = window.setTimeout(function () {
            if (feedbackContainer) {
                feedbackContainer.innerHTML = '';
            }
        }, 4000);
    }

    async function enviarFormulario(formulario) {
        const resposta = await fetch(formulario.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: new FormData(formulario)
        });

        const dados = await resposta.json();

        if (!resposta.ok || !dados.success) {
            throw new Error(dados.message || 'Não foi possível concluir a ação.');
        }

        return dados;
    }

    function atualizarCurtidaDenuncia(formulario, dados) {
        const cardDenuncia = formulario.closest('[data-denuncia-id]');
        if (!cardDenuncia) {
            return;
        }

        const idDenuncia = cardDenuncia.getAttribute('data-denuncia-id');
        const botao = formulario.querySelector('[data-botao-curtir-denuncia]');
        const contador = document.getElementById('total-curtidas-denuncia-' + idDenuncia);

        if (botao) {
            const ehBotaoIcone = botao.hasAttribute('data-curtir-icone');

            if (ehBotaoIcone) {
                const label = dados.usuario_curtiu ? 'Remover curtida' : 'Curtir';
                botao.setAttribute('aria-label', label);
                botao.setAttribute('title', label);
                botao.classList.toggle('is-active', Boolean(dados.usuario_curtiu));
            } else {
                botao.textContent = dados.usuario_curtiu ? 'Descurtir' : 'Curtir';
            }
        }

        if (contador) {
            contador.textContent = dados.total_curtidas;
        }
    }

    function criarBlocoComentario(comentario) {
        const wrapper = document.createElement('div');
        wrapper.id = 'comentario-' + comentario.id;
        wrapper.setAttribute('data-comentario-id', comentario.id);
        wrapper.style.border = '1px solid #e0e0e0';
        wrapper.style.padding = '8px';
        wrapper.style.marginBottom = '8px';

        const autor = document.createElement('p');
        autor.style.marginBottom = '4px';
        const autorStrong = document.createElement('strong');
        autorStrong.textContent = comentario.nome_usuario || 'Usuário';
        autor.appendChild(autorStrong);

        const texto = document.createElement('p');
        texto.style.marginBottom = '4px';
        texto.textContent = comentario.texto || '';

        const data = document.createElement('small');
        data.textContent = comentario.data_comentario || '';

        const formCurtida = document.createElement('form');
        formCurtida.action = 'index.php?rota=processar_curtida_comentario';
        formCurtida.method = 'POST';
        formCurtida.className = 'js-curtir-comentario';
        formCurtida.style.marginTop = '6px';
        formCurtida.style.display = 'inline';

        const inputId = document.createElement('input');
        inputId.type = 'hidden';
        inputId.name = 'id_comentario';
        inputId.value = comentario.id;

        const inputRetornoRota = document.createElement('input');
        inputRetornoRota.type = 'hidden';
        inputRetornoRota.name = 'retorno_rota';
        inputRetornoRota.value = 'detalhe_denuncia';

        const inputRetornoId = document.createElement('input');
        inputRetornoId.type = 'hidden';
        inputRetornoId.name = 'retorno_id';
        inputRetornoId.value = comentario.id_denuncia || '';

        if (csrfToken) {
            const inputCsrf = document.createElement('input');
            inputCsrf.type = 'hidden';
            inputCsrf.name = '_csrf_token';
            inputCsrf.value = csrfToken;
            formCurtida.appendChild(inputCsrf);
        }

        const botao = document.createElement('button');
        botao.type = 'submit';
        botao.setAttribute('data-botao-curtir-comentario', '1');
        botao.textContent = 'Curtir comentário';

        const curtidas = document.createElement('span');
        curtidas.style.marginLeft = '8px';
        const comentarioIdSeguro = parseInt(comentario.id, 10) || 0;
        const totalCurtidasSeguro = parseInt(comentario.total_curtidas, 10) || 0;
        curtidas.appendChild(document.createTextNode('Curtidas: '));
        const strongCurtidas = document.createElement('strong');
        strongCurtidas.id = 'total-curtidas-comentario-' + comentarioIdSeguro;
        strongCurtidas.textContent = String(totalCurtidasSeguro);
        curtidas.appendChild(strongCurtidas);

        formCurtida.appendChild(inputId);
        formCurtida.appendChild(inputRetornoRota);
        formCurtida.appendChild(inputRetornoId);
        formCurtida.appendChild(botao);

        wrapper.appendChild(autor);
        wrapper.appendChild(texto);
        wrapper.appendChild(data);
        wrapper.appendChild(document.createElement('br'));
        wrapper.appendChild(formCurtida);
        wrapper.appendChild(curtidas);

        return wrapper;
    }

    function atualizarListaComentarios(formulario, dados) {
        const listaId = formulario.getAttribute('data-lista-comentarios');
        const lista = document.getElementById(listaId);
        if (!lista) {
            return;
        }

        const placeholder = lista.querySelector('[data-sem-comentarios]');
        if (placeholder) {
            placeholder.remove();
        }

        const comentarioElemento = criarBlocoComentario(dados.comentario);
        lista.appendChild(comentarioElemento);
        comentarioElemento.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

        const textarea = formulario.querySelector('textarea[name="texto"]');
        if (textarea) {
            textarea.value = '';
        }
    }

    async function tratarEnvio(evento) {
        const formulario = evento.target;
        if (!(formulario instanceof HTMLFormElement)) {
            return;
        }

        if (!formulario.matches('.js-curtir-denuncia, .js-comentar-denuncia, .js-curtir-comentario')) {
            return;
        }

        evento.preventDefault();

        const botao = formulario.querySelector('button[type="submit"]');
        const ehBotaoIcone = botao ? botao.hasAttribute('data-curtir-icone') : false;
        const textoOriginal = botao ? botao.textContent : '';

        try {
            if (botao) {
                botao.disabled = true;

                if (!ehBotaoIcone) {
                    botao.textContent = 'Processando...';
                }
            }

            const dados = await enviarFormulario(formulario);

            if (formulario.classList.contains('js-curtir-denuncia')) {
                atualizarCurtidaDenuncia(formulario, dados);
            } else if (formulario.classList.contains('js-curtir-comentario')) {
                const comentarioId = formulario.querySelector('input[name="id_comentario"]')?.value;
                const contador = document.getElementById('total-curtidas-comentario-' + comentarioId);
                const botaoCurtida = formulario.querySelector('[data-botao-curtir-comentario]');

                if (contador) {
                    contador.textContent = dados.total_curtidas;
                }

                if (botaoCurtida) {
                    botaoCurtida.textContent = dados.usuario_curtiu ? 'Descurtir comentário' : 'Curtir comentário';
                }
            } else if (formulario.classList.contains('js-comentar-denuncia')) {
                atualizarListaComentarios(formulario, dados);
            }

            mostrarFeedback('success', dados.message || 'Operação concluída com sucesso.');
        } catch (erro) {
            mostrarFeedback('danger', erro.message || 'Não foi possível concluir a ação.');
        } finally {
            if (botao) {
                botao.disabled = false;

                if (!ehBotaoIcone) {
                    botao.textContent = textoOriginal || botao.textContent;
                }
            }
        }
    }

    document.addEventListener('submit', tratarEnvio);
});