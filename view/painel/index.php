<?php
// View do Painel do Usuário (Dashboard)
?>
<?php include "view/templates/header.php"; ?>
<div class="container py-4">
    <div id="painel-feedback" class="mb-3" aria-live="polite"></div>

    <section class="mb-4">
        <h2 class="mb-1">Denuncie problemas no seu bairro</h2>
        <p class="text-muted mb-3">Ajude a prefeitura a identificar buracos, falta de iluminação e mais.</p>

        <a href="index.php?rota=nova_denuncia" class="btn btn-primary">Fazer uma Denúncia</a>
    </section>

    <section>
        <h3 class="mb-3">Últimas Denúncias Registradas</h3>

        <?php if (count($denuncias) > 0): ?>
            <?php foreach ($denuncias as $d): ?>
                <div id="denuncia-<?= htmlspecialchars($d->getId()) ?>" data-denuncia-id="<?= htmlspecialchars($d->getId()) ?>"
                    class="card shadow-sm mb-3">
                    <div class="card-body">
                        <h4 class="card-title mb-3"><?= htmlspecialchars($d->getTitulo()) ?></h4>

                        <p class="mb-1"><strong>Localização:</strong> <?= htmlspecialchars($d->getLocalizacao()) ?></p>

                        <p class="mb-2"><strong>Status:</strong> <?= htmlspecialchars($d->getStatus()) ?></p>

                        <?php
                        $interacaoDenuncia = $interacoes[(int) $d->getId()] ?? [
                            'comentarios' => [],
                            'totalCurtidas' => 0,
                            'usuarioCurtiu' => false,
                        ];
                        ?>

                        <div class="d-inline-flex align-items-center gap-2 mb-3 mt-1">
                            <form action="index.php?rota=processar_curtida_denuncia" method="POST" class="js-curtir-denuncia">
                                <input type="hidden" name="id_denuncia" value="<?= htmlspecialchars($d->getId()) ?>">
                                <button type="submit" class="btn btn-outline-primary btn-sm" data-botao-curtir-denuncia>
                                    <?= $interacaoDenuncia['usuarioCurtiu'] ? 'Descurtir' : 'Curtir' ?>
                                </button>
                            </form>
                            <span>Curtidas:
                                <strong
                                    id="total-curtidas-denuncia-<?= htmlspecialchars($d->getId()) ?>"><?= htmlspecialchars($interacaoDenuncia['totalCurtidas']) ?></strong></span>
                        </div>

                        <?php if ($d->getFotoPath()): ?>

                            <img src="<?= htmlspecialchars($d->getFotoPath()) ?>" alt="Foto da denúncia"
                                class="img-fluid rounded border mb-3" style="max-width: 300px; max-height: 200px;">

                        <?php endif; ?>

                        <?php
                        $usuarioLogadoId = (int) ($_SESSION['usuario_id'] ?? 0);
                        $usuarioAdmin = isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] === 'admin';
                        $podeGerenciar = $usuarioAdmin || $usuarioLogadoId === (int) $d->getIdUsuario();
                        ?>

                        <?php if ($podeGerenciar): ?>
                            <details class="mt-3">
                                <summary><strong>Editar denúncia</strong></summary>
                                <form action="index.php?rota=processar_edicao_denuncia" method="POST" enctype="multipart/form-data"
                                    class="mt-3">
                                    <input type="hidden" name="id_denuncia" value="<?= htmlspecialchars($d->getId()) ?>">

                                    <div class="mb-3">
                                        <label class="form-label">Título</label>
                                        <input type="text" name="titulo" class="form-control"
                                            value="<?= htmlspecialchars($d->getTitulo()) ?>" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Descrição</label>
                                        <textarea name="descricao" rows="3" class="form-control"
                                            required><?= htmlspecialchars($d->getDescricao()) ?></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Localização</label>
                                        <input type="text" name="localizacao" class="form-control"
                                            value="<?= htmlspecialchars($d->getLocalizacao()) ?>" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Categoria</label>
                                        <select name="id_categoria" class="form-select" required>
                                            <?php if (isset($categorias) && is_array($categorias)): ?>
                                                <?php foreach ($categorias as $categoria): ?>
                                                    <option value="<?= htmlspecialchars($categoria->getId()) ?>" <?= ((int) $categoria->getId() === (int) $d->getIdCategoria()) ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($categoria->getNomeCategoria()) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Status</label>
                                        <select name="status" class="form-select" required>
                                            <?php $statusAtual = $d->getStatus(); ?>
                                            <option value="Aberto" <?= ($statusAtual === 'Aberto') ? 'selected' : '' ?>>Aberto</option>
                                            <option value="Em Andamento" <?= ($statusAtual === 'Em Andamento') ? 'selected' : '' ?>>Em
                                                Andamento</option>
                                            <option value="Resolvido" <?= ($statusAtual === 'Resolvido') ? 'selected' : '' ?>>Resolvido
                                            </option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Nova foto (opcional)</label>
                                        <input type="file" name="foto" class="form-control" accept="image/*">
                                    </div>

                                    <button type="submit" class="btn btn-success btn-sm">Salvar edição</button>
                                </form>
                            </details>

                            <form action="index.php?rota=processar_exclusao_denuncia" method="POST" class="mt-3"
                                onsubmit="return confirm('Tem certeza que deseja excluir esta denúncia?');">
                                <input type="hidden" name="id_denuncia" value="<?= htmlspecialchars($d->getId()) ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Excluir denúncia</button>
                            </form>
                        <?php endif; ?>

                        <div class="mt-4 pt-3 border-top">
                            <h5 class="mb-3">Comentários</h5>

                            <form action="index.php?rota=processar_comentario" method="POST" class="js-comentar-denuncia mb-3"
                                data-lista-comentarios="comentarios-denuncia-<?= htmlspecialchars($d->getId()) ?>">
                                <input type="hidden" name="id_denuncia" value="<?= htmlspecialchars($d->getId()) ?>">
                                <textarea name="texto" rows="3" placeholder="Escreva um comentário..." required
                                    class="form-control"></textarea>
                                <button type="submit" class="btn btn-primary btn-sm mt-2">Comentar</button>
                            </form>

                            <div id="comentarios-denuncia-<?= htmlspecialchars($d->getId()) ?>">
                                <?php if (!empty($interacaoDenuncia['comentarios'])): ?>
                                    <?php foreach ($interacaoDenuncia['comentarios'] as $itemComentario): ?>
                                        <?php $comentario = $itemComentario['comentario']; ?>
                                        <div id="comentario-<?= htmlspecialchars($comentario->getId()) ?>"
                                            data-comentario-id="<?= htmlspecialchars($comentario->getId()) ?>" class="card mb-2">
                                            <div class="card-body py-2 px-3">
                                                <p class="mb-1">
                                                    <strong><?= htmlspecialchars($comentario->getNomeUsuario() ?? 'Usuário') ?></strong>
                                                </p>
                                                <p class="mb-1"><?= htmlspecialchars($comentario->getTexto()) ?></p>
                                                <small
                                                    class="text-muted"><?= htmlspecialchars($comentario->getDataComentario()) ?></small>

                                                <form action="index.php?rota=processar_curtida_comentario" method="POST"
                                                    class="js-curtir-comentario mt-2 d-inline-block">
                                                    <input type="hidden" name="id_comentario"
                                                        value="<?= htmlspecialchars($comentario->getId()) ?>">
                                                    <button type="submit" class="btn btn-outline-secondary btn-sm"
                                                        data-botao-curtir-comentario>
                                                        <?= $itemComentario['usuarioCurtiu'] ? 'Descurtir comentário' : 'Curtir comentário' ?>
                                                    </button>
                                                </form>

                                                <span class="ms-2">Curtidas:
                                                    <strong
                                                        id="total-curtidas-comentario-<?= htmlspecialchars($comentario->getId()) ?>"><?= htmlspecialchars($itemComentario['totalCurtidas']) ?></strong></span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="text-muted mb-0" data-sem-comentarios="<?= htmlspecialchars($d->getId()) ?>">Nenhum
                                        comentário ainda.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

            <?php endforeach; ?>
        <?php else: ?>
            <div class="alert alert-info">Nenhuma denúncia registrada ainda. Seja o primeiro!</div>
        <?php endif; ?>
    </section>
</div>

<?php include "view/templates/footer.php"; ?>