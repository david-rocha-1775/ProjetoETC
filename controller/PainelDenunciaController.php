<?php
// Controller de cadastro, edição e exclusão de denúncias.

require_once "controller/PainelBaseController.php";
require_once "model/dto/DenunciaDTO.php";

class PainelDenunciaController extends PainelBaseController
{
    public function cadastrarDenuncia()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $tituloPagina = "Nova Denúncia";
            $usuarioNome = $_SESSION['usuario_nome'] ?? 'Usuário';
            $categorias = $this->categoriaDAO->listarTodas();
            include "view/painel/nova_denuncia.php";
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $titulo = trim($_POST['titulo'] ?? '');
                $descricao = trim($_POST['descricao'] ?? '');
                $localizacao = trim($_POST['localizacao'] ?? '');
                $idCategoria = trim($_POST['id_categoria'] ?? '');
                $latitude = trim($_POST['latitude'] ?? '');
                $longitude = trim($_POST['longitude'] ?? '');

                if (empty($titulo) || empty($descricao) || empty($localizacao) || empty($idCategoria)) {
                    throw new Exception("Os campos título, descrição, localização e categoria são obrigatórios.");
                }

                if (mb_strlen($titulo) > 150) {
                    throw new Exception('O título deve ter no máximo 150 caracteres.');
                }

                if (mb_strlen($descricao) > 5000) {
                    throw new Exception('A descrição deve ter no máximo 5000 caracteres.');
                }

                if (mb_strlen($localizacao) > 255) {
                    throw new Exception('A localização deve ter no máximo 255 caracteres.');
                }

                if (filter_var($idCategoria, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) === false) {
                    throw new Exception('Categoria inválida.');
                }

                if (!empty($latitude) && !empty($longitude)) {
                    $coordsValidas = $this->validarCoordenadas($latitude, $longitude);
                    if (!$coordsValidas['valida']) {
                        throw new Exception($coordsValidas['mensagem']);
                    }
                    $latitude = (float) $latitude;
                    $longitude = (float) $longitude;
                } else {
                    $latitude = null;
                    $longitude = null;
                }

                $fotoPath = null;
                if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                    $fotoPath = $this->processarUploadImagem($_FILES['foto']);
                }

                $denuncia = new DenunciaDTO();
                $denuncia->setTitulo($titulo);
                $denuncia->setDescricao($descricao);
                $denuncia->setLocalizacao($localizacao);
                $denuncia->setFotoPath($fotoPath);
                $denuncia->setIdCategoria($idCategoria);
                $denuncia->setStatus('Aberto');
                $denuncia->setIdUsuario($_SESSION['usuario_id'] ?? null);

                if ($latitude !== null && $longitude !== null) {
                    $denuncia->setLatitude($latitude);
                    $denuncia->setLongitude($longitude);
                }

                $salvou = $this->denunciaDAO->cadastrar($denuncia);
                if (!$salvou) {
                    throw new Exception("Não foi possível cadastrar a denúncia.");
                }

                $_SESSION['mensagem'] = "Denúncia cadastrada com sucesso!";
                $_SESSION['tipo_mensagem'] = "sucesso";

            } catch (Exception $e) {
                $_SESSION['mensagem'] = "Erro: " . $e->getMessage();
                $_SESSION['tipo_mensagem'] = "erro";
            }

            header("Location: index.php?" . $this->montarUrlRetornoPosAcao());
            exit();
        }
    }

    public function atualizarDenuncia()
    {
        $this->exigirMetodoPost('painel');

        try {
            $idDenuncia = (int) ($_POST['id_denuncia'] ?? 0);
            $titulo = trim($_POST['titulo'] ?? '');
            $descricao = trim($_POST['descricao'] ?? '');
            $localizacao = trim($_POST['localizacao'] ?? '');
            $idCategoria = (int) ($_POST['id_categoria'] ?? 0);
            $status = trim($_POST['status'] ?? 'Aberto');
            $latitude = trim($_POST['latitude'] ?? '');
            $longitude = trim($_POST['longitude'] ?? '');

            if ($idDenuncia <= 0 || $idCategoria <= 0 || empty($titulo) || empty($descricao) || empty($localizacao)) {
                throw new Exception("Dados obrigatórios da denúncia não informados corretamente.");
            }

            if (mb_strlen($titulo) > 150) {
                throw new Exception('O título deve ter no máximo 150 caracteres.');
            }

            if (mb_strlen($descricao) > 5000) {
                throw new Exception('A descrição deve ter no máximo 5000 caracteres.');
            }

            if (mb_strlen($localizacao) > 255) {
                throw new Exception('A localização deve ter no máximo 255 caracteres.');
            }

            $denunciaAtual = $this->denunciaDAO->buscarPorId($idDenuncia);
            if ($denunciaAtual === null) {
                throw new Exception("Denúncia não encontrada.");
            }

            $this->validarPermissaoDenuncia($denunciaAtual->getIdUsuario());

            if (!empty($latitude) && !empty($longitude)) {
                $coordsValidas = $this->validarCoordenadas($latitude, $longitude);
                if (!$coordsValidas['valida']) {
                    throw new Exception($coordsValidas['mensagem']);
                }
                $latitude = (float) $latitude;
                $longitude = (float) $longitude;
            } else {
                $latitude = $denunciaAtual->getLatitude();
                $longitude = $denunciaAtual->getLongitude();
            }

            $statusValido = ['Aberto', 'Em Andamento', 'Resolvido'];
            if (!in_array($status, $statusValido, true)) {
                $status = 'Aberto';
            }

            $fotoPath = $denunciaAtual->getFotoPath();
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $fotoPath = $this->processarUploadImagem($_FILES['foto']);
            }

            $denuncia = new DenunciaDTO();
            $denuncia->setId($idDenuncia);
            $denuncia->setTitulo($titulo);
            $denuncia->setDescricao($descricao);
            $denuncia->setLocalizacao($localizacao);
            $denuncia->setFotoPath($fotoPath);
            $denuncia->setStatus($status);
            $denuncia->setIdUsuario($denunciaAtual->getIdUsuario());
            $denuncia->setIdCategoria($idCategoria);
            $denuncia->setLatitude($latitude);
            $denuncia->setLongitude($longitude);

            $atualizou = $this->denunciaDAO->atualizar($denuncia);
            if (!$atualizou) {
                throw new Exception("Não foi possível atualizar a denúncia.");
            }

            $_SESSION['mensagem'] = "Denúncia atualizada com sucesso!";
            $_SESSION['tipo_mensagem'] = "sucesso";

        } catch (Exception $e) {
            $_SESSION['mensagem'] = "Erro: " . $e->getMessage();
            $_SESSION['tipo_mensagem'] = "erro";
        }

        header("Location: index.php?" . $this->montarUrlRetornoPosAcao($idDenuncia ?? 0));
        exit();
    }

    public function excluirDenuncia()
    {
        $this->exigirMetodoPost('painel');

        try {
            $idDenuncia = (int) ($_POST['id_denuncia'] ?? 0);
            if ($idDenuncia <= 0) {
                throw new Exception("ID da denúncia inválido.");
            }

            $denunciaAtual = $this->denunciaDAO->buscarPorId($idDenuncia);
            if ($denunciaAtual === null) {
                throw new Exception("Denúncia não encontrada.");
            }

            $this->validarPermissaoDenuncia($denunciaAtual->getIdUsuario());

            $excluiu = $this->denunciaDAO->excluirPorId($idDenuncia);
            if (!$excluiu) {
                throw new Exception("Não foi possível excluir a denúncia.");
            }

            $_SESSION['mensagem'] = "Denúncia excluída com sucesso!";
            $_SESSION['tipo_mensagem'] = "sucesso";

        } catch (Exception $e) {
            $_SESSION['mensagem'] = "Erro: " . $e->getMessage();
            $_SESSION['tipo_mensagem'] = "erro";
        }

        header("Location: index.php?" . $this->montarUrlRetornoPosAcao($idDenuncia ?? 0));
        exit();
    }
}
