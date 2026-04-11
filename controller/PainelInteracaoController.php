<?php
// Controller de comentários e curtidas no painel.

require_once "controller/PainelBaseController.php";

class PainelInteracaoController extends PainelBaseController
{
    public function comentarDenuncia()
    {
        $this->exigirMetodoPost('painel');

        try {
            $idDenuncia = (int) ($_POST['id_denuncia'] ?? 0);
            $texto = trim($_POST['texto'] ?? '');

            if ($idDenuncia <= 0 || $texto === '') {
                throw new Exception('Informe a denúncia e o texto do comentário.');
            }

            if (mb_strlen($texto) > 2000) {
                throw new Exception('O comentário deve ter no máximo 2000 caracteres.');
            }

            if ($this->denunciaDAO->buscarPorId($idDenuncia) === null) {
                throw new Exception('Denúncia não encontrada.');
            }

            $comentario = new ComentarioDTO();
            $comentario->setTexto($texto);
            $comentario->setIdDenuncia($idDenuncia);
            $comentario->setIdUsuario((int) ($_SESSION['usuario_id'] ?? 0));

            $salvou = $this->comentarioDAO->cadastrar($comentario);
            if (!$salvou) {
                throw new Exception('Não foi possível cadastrar o comentário.');
            }

            $comentarioCriado = $this->comentarioDAO->buscarPorId((int) $salvou);
            if ($comentarioCriado === null) {
                throw new Exception('Não foi possível recuperar o comentário cadastrado.');
            }

            if ($this->requisicaoAceitaJson()) {
                $this->responderJson([
                    'success' => true,
                    'message' => 'Comentário publicado com sucesso!',
                    'id_denuncia' => $idDenuncia,
                    'comentario' => $this->comentarioParaResposta($comentarioCriado),
                ], 201);
            }

            $_SESSION['mensagem'] = 'Comentário publicado com sucesso!';
            $_SESSION['tipo_mensagem'] = 'sucesso';

        } catch (Exception $e) {
            if ($this->requisicaoAceitaJson()) {
                $this->responderJson([
                    'success' => false,
                    'message' => 'Erro: ' . $e->getMessage(),
                ], 400);
            }

            $_SESSION['mensagem'] = 'Erro: ' . $e->getMessage();
            $_SESSION['tipo_mensagem'] = 'erro';
        }

        header("Location: index.php?" . $this->montarUrlRetornoPosAcao($idDenuncia ?? 0));
        exit();
    }

    public function curtirDenuncia()
    {
        $this->exigirMetodoPost('painel');

        try {
            $idDenuncia = (int) ($_POST['id_denuncia'] ?? 0);
            $idUsuario = (int) ($_SESSION['usuario_id'] ?? 0);

            if ($idDenuncia <= 0 || $idUsuario <= 0) {
                throw new Exception('Não foi possível processar a curtida.');
            }

            if ($this->denunciaDAO->buscarPorId($idDenuncia) === null) {
                throw new Exception('Denúncia não encontrada.');
            }

            if ($this->curtidaDenunciaDAO->usuarioCurtiu($idUsuario, $idDenuncia)) {
                $this->curtidaDenunciaDAO->removerCurtida($idUsuario, $idDenuncia);
                $mensagem = 'Curtida removida.';
            } else {
                $curtida = new CurtidaDenunciaDTO();
                $curtida->setIdUsuario($idUsuario);
                $curtida->setIdDenuncia($idDenuncia);
                $this->curtidaDenunciaDAO->curtir($curtida);
                $mensagem = 'Denúncia curtida com sucesso!';
            }

            $totalCurtidas = $this->curtidaDenunciaDAO->contarCurtidas($idDenuncia);
            $usuarioCurtiu = $this->curtidaDenunciaDAO->usuarioCurtiu($idUsuario, $idDenuncia);

            if ($this->requisicaoAceitaJson()) {
                $this->responderJson([
                    'success' => true,
                    'message' => $mensagem,
                    'id_denuncia' => $idDenuncia,
                    'total_curtidas' => $totalCurtidas,
                    'usuario_curtiu' => $usuarioCurtiu,
                ]);
            }

            unset($_SESSION['mensagem'], $_SESSION['tipo_mensagem']);

        } catch (Exception $e) {
            if ($this->requisicaoAceitaJson()) {
                $this->responderJson([
                    'success' => false,
                    'message' => 'Erro: ' . $e->getMessage(),
                ], 400);
            }

            unset($_SESSION['mensagem'], $_SESSION['tipo_mensagem']);
        }

        header("Location: index.php?" . $this->montarUrlRetornoPosAcao($idDenuncia ?? 0));
        exit();
    }

    public function curtirComentario()
    {
        $this->exigirMetodoPost('painel');

        try {
            $idComentario = (int) ($_POST['id_comentario'] ?? 0);
            $idUsuario = (int) ($_SESSION['usuario_id'] ?? 0);

            if ($idComentario <= 0 || $idUsuario <= 0) {
                throw new Exception('Não foi possível processar a curtida.');
            }

            if ($this->comentarioDAO->buscarPorId($idComentario) === null) {
                throw new Exception('Comentário não encontrado.');
            }

            if ($this->curtidaComentarioDAO->usuarioCurtiu($idUsuario, $idComentario)) {
                $this->curtidaComentarioDAO->removerCurtida($idUsuario, $idComentario);
                $mensagem = 'Curtida removida.';
            } else {
                $curtida = new CurtidaComentarioDTO();
                $curtida->setIdUsuario($idUsuario);
                $curtida->setIdComentario($idComentario);
                $this->curtidaComentarioDAO->curtir($curtida);
                $mensagem = 'Comentário curtido com sucesso!';
            }

            $totalCurtidas = $this->curtidaComentarioDAO->contarCurtidas($idComentario);
            $usuarioCurtiu = $this->curtidaComentarioDAO->usuarioCurtiu($idUsuario, $idComentario);

            if ($this->requisicaoAceitaJson()) {
                $this->responderJson([
                    'success' => true,
                    'message' => $mensagem,
                    'id_comentario' => $idComentario,
                    'total_curtidas' => $totalCurtidas,
                    'usuario_curtiu' => $usuarioCurtiu,
                ]);
            }

            $_SESSION['mensagem'] = $mensagem;
            $_SESSION['tipo_mensagem'] = 'sucesso';

        } catch (Exception $e) {
            if ($this->requisicaoAceitaJson()) {
                $this->responderJson([
                    'success' => false,
                    'message' => 'Erro: ' . $e->getMessage(),
                ], 400);
            }

            $_SESSION['mensagem'] = 'Erro: ' . $e->getMessage();
            $_SESSION['tipo_mensagem'] = 'erro';
        }

        header("Location: index.php?" . $this->montarUrlRetornoPosAcao());
        exit();
    }
}
