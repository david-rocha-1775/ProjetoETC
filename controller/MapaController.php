<?php
// Controller de contexto do Mapa (ações geoespaciais autenticadas)

require_once "model/dao/DenunciaDAO.php";

class MapaController
{
    private $denunciaDAO;

    private const RAIO_PADRAO_KM = 7.5;
    private const LIMITE_PADRAO = 50;

    // Fallback padrão quando não há geolocalização do navegador (Brasília/DF).
    private const FALLBACK_LATITUDE = -15.793889;
    private const FALLBACK_LONGITUDE = -47.882778;

    public function __construct()
    {
        if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
            $this->responderJson([
                'success' => false,
                'message' => 'Usuário não autenticado.'
            ], 401);
        }

        $this->denunciaDAO = new DenunciaDAO();
    }

    /**
     * Lista denúncias por proximidade para renderização no mapa.
     */
    public function listarDenunciasMapa()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $this->responderJson([
                'success' => false,
                'message' => 'Método não permitido.'
            ], 405);
        }

        try {
            $coordenadas = $this->resolverCoordenadasCentro();

            $denuncias = $this->denunciaDAO->buscarPorProximidade(
                $coordenadas['latitude'],
                $coordenadas['longitude'],
                self::RAIO_PADRAO_KM,
                self::LIMITE_PADRAO
            );

            $respostaDenuncias = [];
            foreach ($denuncias as $denuncia) {
                $latitude = $denuncia->getLatitude();
                $longitude = $denuncia->getLongitude();

                if (!$this->coordenadaValida($latitude, $longitude)) {
                    continue;
                }

                $respostaDenuncias[] = [
                    'id' => (int) $denuncia->getId(),
                    'titulo' => $denuncia->getTitulo(),
                    'descricao' => $denuncia->getDescricao(),
                    'localizacao' => $denuncia->getLocalizacao(),
                    'status' => $denuncia->getStatus(),
                    'id_categoria' => (int) $denuncia->getIdCategoria(),
                    'latitude' => (float) $latitude,
                    'longitude' => (float) $longitude,
                    'data_criacao' => $denuncia->getDataCriacao(),
                    'foto_path' => $denuncia->getFotoPath(),
                ];
            }

            $this->responderJson([
                'success' => true,
                'message' => 'Denúncias carregadas com sucesso.',
                'centro_usado' => [
                    'latitude' => (float) $coordenadas['latitude'],
                    'longitude' => (float) $coordenadas['longitude'],
                    'origem' => $coordenadas['origem'],
                ],
                'raio_km' => self::RAIO_PADRAO_KM,
                'limite' => self::LIMITE_PADRAO,
                'denuncias' => $respostaDenuncias,
                'total' => count($respostaDenuncias),
            ]);
        } catch (Exception $e) {
            $this->responderJson([
                'success' => false,
                'message' => 'Erro: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Resolve as coordenadas da consulta com fallback padrão.
     */
    private function resolverCoordenadasCentro()
    {
        $latInformada = array_key_exists('lat', $_GET);
        $lonInformada = array_key_exists('lon', $_GET);

        if ($latInformada xor $lonInformada) {
            throw new Exception('Informe latitude e longitude juntas.');
        }

        if ($latInformada && $lonInformada) {
            $latitude = filter_var($_GET['lat'], FILTER_VALIDATE_FLOAT);
            $longitude = filter_var($_GET['lon'], FILTER_VALIDATE_FLOAT);

            if ($latitude === false || $longitude === false) {
                throw new Exception('Latitude/longitude inválidas.');
            }

            if (!$this->coordenadaValida($latitude, $longitude)) {
                throw new Exception('Coordenadas fora da faixa permitida.');
            }

            return [
                'latitude' => (float) $latitude,
                'longitude' => (float) $longitude,
                'origem' => 'usuario'
            ];
        }

        return [
            'latitude' => self::FALLBACK_LATITUDE,
            'longitude' => self::FALLBACK_LONGITUDE,
            'origem' => 'fallback_padrao'
        ];
    }

    /**
     * Valida faixa de coordenadas geográficas.
     */
    private function coordenadaValida($latitude, $longitude)
    {
        return is_numeric($latitude)
            && is_numeric($longitude)
            && $latitude >= -90
            && $latitude <= 90
            && $longitude >= -180
            && $longitude <= 180;
    }

    /**
     * Envia resposta JSON e encerra execução.
     */
    private function responderJson(array $dados, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($dados, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit();
    }
}
?>