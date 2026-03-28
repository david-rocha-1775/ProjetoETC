<?php
// View do Painel do Usuário (Dashboard)
?>
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

                <?php if ($d->getFotoPath()): ?>

                    <img src="<?= htmlspecialchars($d->getFotoPath()) ?>" alt="Foto da denúncia"
                        style="max-width: 300px; max-height: 200px; display: block; margin-top: 8px;">

                <?php endif; ?>

                <?php
                $usuarioLogadoId = (int) ($_SESSION['usuario_id'] ?? 0);
                $usuarioAdmin = isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] === 'admin';
                $podeGerenciar = $usuarioAdmin || $usuarioLogadoId === (int) $d->getIdUsuario();
                ?>

                <?php if ($podeGerenciar): ?>
                    <details style="margin-top: 10px;">
                        <summary><strong>Editar denúncia</strong></summary>
                        <form action="index.php?rota=processar_edicao_denuncia" method="POST" enctype="multipart/form-data"
                            style="margin-top: 10px;">
                            <input type="hidden" name="id_denuncia" value="<?= htmlspecialchars($d->getId()) ?>">

                            <div style="margin-bottom: 8px;">
                                <label>Título</label><br>
                                <input type="text" name="titulo" value="<?= htmlspecialchars($d->getTitulo()) ?>" required>
                            </div>

                            <div style="margin-bottom: 8px;">
                                <label>Descrição</label><br>
                                <textarea name="descricao" rows="3" required><?= htmlspecialchars($d->getDescricao()) ?></textarea>
                            </div>

                            <div style="margin-bottom: 8px;">
                                <label>Localização</label><br>
                                <input type="text" name="localizacao" value="<?= htmlspecialchars($d->getLocalizacao()) ?>"
                                    required>
                            </div>

                            <div style="margin-bottom: 8px;">
                                <label>Categoria</label><br>
                                <select name="id_categoria" required>
                                    <?php if (isset($categorias) && is_array($categorias)): ?>
                                        <?php foreach ($categorias as $categoria): ?>
                                            <option value="<?= htmlspecialchars($categoria->getId()) ?>" <?= ((int) $categoria->getId() === (int) $d->getIdCategoria()) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($categoria->getNomeCategoria()) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <div style="margin-bottom: 8px;">
                                <label>Status</label><br>
                                <select name="status" required>
                                    <?php $statusAtual = $d->getStatus(); ?>
                                    <option value="Aberto" <?= ($statusAtual === 'Aberto') ? 'selected' : '' ?>>Aberto</option>
                                    <option value="Em Andamento" <?= ($statusAtual === 'Em Andamento') ? 'selected' : '' ?>>Em
                                        Andamento</option>
                                    <option value="Resolvido" <?= ($statusAtual === 'Resolvido') ? 'selected' : '' ?>>Resolvido
                                    </option>
                                </select>
                            </div>

                            <div style="margin-bottom: 8px;">
                                <label>Nova foto (opcional)</label><br>
                                <input type="file" name="foto" accept="image/*">
                            </div>

                            <button type="submit">Salvar edição</button>
                        </form>
                    </details>

                    <form action="index.php?rota=processar_exclusao_denuncia" method="POST" style="margin-top: 10px;"
                        onsubmit="return confirm('Tem certeza que deseja excluir esta denúncia?');">
                        <input type="hidden" name="id_denuncia" value="<?= htmlspecialchars($d->getId()) ?>">
                        <button type="submit">Excluir denúncia</button>
                    </form>
                <?php endif; ?>
            </div>

        <?php endforeach; ?>
    <?php else: ?>
        <p>Nenhuma denúncia registrada ainda. Seja o primeiro!</p>
    <?php endif; ?>
</section>

<?php include "view/templates/footer.php"; ?>