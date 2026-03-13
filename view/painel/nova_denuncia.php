<?php include "view/templates/header.php"; ?>

<section class="nova-denuncia">
    <h2><?php echo isset($tituloPagina) ? htmlspecialchars($tituloPagina) : 'Nova Denúncia'; ?></h2>
    <p>Bem-vindo, <?php echo isset($usuarioNome) ? htmlspecialchars($usuarioNome) : 'Usuário'; ?></p>

    <?php if (isset($_SESSION['mensagem'])): ?>
        <div style="border: 1px solid #ccc; padding: 10px; margin-bottom: 20px;">
            <p><strong>Aviso:</strong>
                <?php echo htmlspecialchars($_SESSION['mensagem']); ?>
            </p>
        </div>
        <?php
        unset($_SESSION['mensagem']);
        unset($_SESSION['tipo_mensagem']);
        ?>
    <?php endif; ?>

    <form action="index.php?rota=nova_denuncia" method="POST" enctype="multipart/form-data">
        <div style="margin-bottom: 15px;">
            <label for="titulo">Título da Denúncia:</label><br>
            <input type="text" id="titulo" name="titulo" required>
        </div>

        <div style="margin-bottom: 15px;">
            <label for="descricao">Descrição (Detalhes):</label><br>
            <textarea id="descricao" name="descricao" rows="5" required></textarea>
        </div>

        <div style="margin-bottom: 15px;">
            <label for="localizacao">Localização (Bairro, Rua, Referência):</label><br>
            <input type="text" id="localizacao" name="localizacao" required>
        </div>

        <div style="margin-bottom: 15px;">
            <label for="id_categoria">Categoria da Denúncia:</label><br>
            <select id="id_categoria" name="id_categoria" required>
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

        <div style="margin-bottom: 15px;">
            <label for="foto">Foto (Opcional):</label><br>
            <input type="file" id="foto" name="foto" accept="image/*">
        </div>

        <div style="margin-bottom: 15px;">
            <button type="submit">Enviar Denúncia</button>
            <a href="index.php?rota=painel">Cancelar e Voltar ao Painel</a>
        </div>
    </form>
</section>

<?php include "view/templates/footer.php"; ?>