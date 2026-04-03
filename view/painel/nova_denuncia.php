<?php include "view/templates/header.php"; ?>

<section class="container py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-1"><?php echo isset($tituloPagina) ? htmlspecialchars($tituloPagina) : 'Nova Denúncia'; ?>
            </h2>
            <p class="text-muted mb-0">Bem-vindo,
                <?php echo isset($usuarioNome) ? htmlspecialchars($usuarioNome) : 'Usuário'; ?></p>
        </div>
    </div>

    <?php if (isset($_SESSION['mensagem'])): ?>
        <?php $tipoMensagem = $_SESSION['tipo_mensagem'] ?? 'erro'; ?>
        <div class="alert <?php echo $tipoMensagem === 'sucesso' ? 'alert-success' : 'alert-danger'; ?> mb-4" role="alert">
            <p class="mb-0"><strong>Aviso:</strong>
                <?php echo htmlspecialchars($_SESSION['mensagem']); ?>
            </p>
        </div>
        <?php
        unset($_SESSION['mensagem']);
        unset($_SESSION['tipo_mensagem']);
        ?>
    <?php endif; ?>

    <div class="row">
        <div class="col-12 col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="index.php?rota=nova_denuncia" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="titulo" class="form-label">Título da Denúncia:</label>
                            <input type="text" id="titulo" name="titulo" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="descricao" class="form-label">Descrição (Detalhes):</label>
                            <textarea id="descricao" name="descricao" rows="5" class="form-control" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="localizacao" class="form-label">Localização (Bairro, Rua, Referência):</label>
                            <input type="text" id="localizacao" name="localizacao" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="id_categoria" class="form-label">Categoria da Denúncia:</label>
                            <select id="id_categoria" name="id_categoria" class="form-select" required>
                                <option value="">Selecione uma categoria...</option>
                                <?php if (isset($categorias) && is_array($categorias)): ?>
                                    <?php foreach ($categorias as $categoria): ?>
                                        <option value="<?php echo htmlspecialchars($categoria->getId()); ?>">
                                            <?php echo htmlspecialchars($categoria->getNomeCategoria()); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="">Nenhuma categoria carregada</option>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="foto" class="form-label">Foto (Opcional):</label>
                            <input type="file" id="foto" name="foto" class="form-control" accept="image/*">
                        </div>

                        <div class="d-flex gap-2 flex-wrap">
                            <button type="submit" class="btn btn-primary">Enviar Denúncia</button>
                            <a href="index.php?rota=painel" class="btn btn-outline-secondary">Cancelar e Voltar ao
                                Painel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include "view/templates/footer.php"; ?>