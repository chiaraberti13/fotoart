<?php

class FotoartpuzzleAjaxModuleFrontController extends ModuleFrontController
{
    /**
     * @var FAPPuzzleRepository
     */
    protected $repository;

    /**
     * @var FAPQualityService
     */
    protected $qualityService;

    /**
     * @var FAPSessionService
     */
    protected $sessionService;

    public function __construct()
    {
        parent::__construct();
        $this->ajax = true;
        $this->repository = new FAPPuzzleRepository();
        $this->qualityService = new FAPQualityService();
        $this->sessionService = new FAPSessionService();
    }

    public function initContent()
    {
        parent::initContent();
        $action = Tools::getValue('action');
        $payload = $this->getRequestPayload();

        switch ($action) {
            case 'config':
                $this->sendJsonResponse([
                    'success' => true,
                    'config' => FAPConfiguration::getFrontConfig(),
                    'fonts' => (new FAPFontManager())->getAvailableFonts(),
                ]);
                return;
            case 'getPuzzles':
                $this->handleGetPuzzles($payload);
                return;
            case 'getBoxes':
                $this->sendJsonResponse([
                    'success' => true,
                    'boxes' => $this->repository->getBoxes(true),
                ]);
                return;
            case 'manageSession':
                $this->handleManageSession($payload);
                return;
            case 'getRestore':
                $this->handleRestore($payload);
                return;
            case 'updateSession':
                $this->handleUpdateSession($payload);
                return;
            case 'getQuality':
                $this->handleGetQuality($payload);
                return;
            case 'getOrderDesc':
                $this->handleOrderDescription($payload);
                return;
            case 'mgrGetBoxes':
                $this->sendJsonResponse([
                    'success' => true,
                    'boxes' => $this->repository->getBoxes(false),
                ]);
                return;
            case 'mgrGetBoxesList':
                $this->sendJsonResponse([
                    'success' => true,
                    'boxList' => $this->repository->getBoxes(false),
                ]);
                return;
            case 'mgrDrawBox':
                $this->handleDrawBox($payload);
                return;
            default:
                $this->sendJsonResponse([
                    'success' => false,
                    'message' => $this->module->l('Unknown action'),
                ]);
        }
    }

    /**
     * Handle puzzle listing optionally enriched with quality data.
     *
     * @param array $payload
     */
    protected function handleGetPuzzles(array $payload)
    {
        $puzzles = $this->repository->getFormats(true);
        $imageWidth = isset($payload['imageWidth']) ? (int) $payload['imageWidth'] : null;
        $imageHeight = isset($payload['imageHeight']) ? (int) $payload['imageHeight'] : null;

        if ($imageWidth && $imageHeight) {
            foreach ($puzzles as &$puzzle) {
                $evaluation = $this->qualityService->evaluateFormat($imageWidth, $imageHeight, $puzzle);
                if ($evaluation) {
                    $puzzle = array_merge($puzzle, [
                        'quality' => isset($evaluation['quality']) ? $evaluation['quality'] : null,
                        'coordinates' => isset($evaluation['coordinates']) ? $evaluation['coordinates'] : [],
                    ]);
                }
            }
        }

        $this->sendJsonResponse([
            'success' => true,
            'puzzles' => $puzzles,
        ]);
    }

    /**
     * Manage incremental session payload.
     *
     * @param array $payload
     */
    protected function handleManageSession(array $payload)
    {
        try {
            $sessionPayload = isset($payload['data']) ? $this->normaliseInput($payload['data']) : $payload;
            $result = $this->sessionService->manage($sessionPayload);
            $this->sendJsonResponse(['success' => true, 'session' => $result]);
        } catch (Exception $exception) {
            $this->sendJsonResponse(['success' => false, 'message' => $exception->getMessage()]);
        }
    }

    /**
     * Restore previous session state.
     *
     * @param array $payload
     */
    protected function handleRestore(array $payload)
    {
        if (empty($payload['session_id'])) {
            $this->sendJsonResponse(['success' => false, 'message' => 'Missing session identifier']);
            return;
        }

        $data = $this->sessionService->restore($payload['session_id']);
        $this->sendJsonResponse([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Persist incremental session updates.
     *
     * @param array $payload
     */
    protected function handleUpdateSession(array $payload)
    {
        if (empty($payload['session_id'])) {
            $this->sendJsonResponse(['success' => false, 'message' => 'Missing session identifier']);
            return;
        }

        try {
            $dataPayload = isset($payload['data']) ? $this->normaliseInput($payload['data']) : [];
            $data = $this->sessionService->update($payload['session_id'], $dataPayload);
            $this->sendJsonResponse(['success' => true, 'data' => $data]);
        } catch (Exception $exception) {
            $this->sendJsonResponse(['success' => false, 'message' => $exception->getMessage()]);
        }
    }

    /**
     * Calculate quality for a given format.
     *
     * @param array $payload
     */
    protected function handleGetQuality(array $payload)
    {
        if (empty($payload['format_id']) || empty($payload['imageWidth']) || empty($payload['imageHeight'])) {
            $this->sendJsonResponse(['success' => false, 'message' => 'Missing parameters']);
            return;
        }

        $format = $this->repository->getFormatById((int) $payload['format_id']);
        if (!$format) {
            $this->sendJsonResponse(['success' => false, 'message' => 'Format not found']);
            return;
        }

        $evaluation = $this->qualityService->evaluateFormat((int) $payload['imageWidth'], (int) $payload['imageHeight'], $format);
        $this->sendJsonResponse([
            'success' => true,
            'quality' => isset($evaluation['quality']) ? $evaluation['quality'] : null,
            'coordinates' => isset($evaluation['coordinates']) ? $evaluation['coordinates'] : [],
        ]);
    }

    /**
     * Provide order description summary akin to the legacy flow.
     *
     * @param array $payload
     */
    protected function handleOrderDescription(array $payload)
    {
        $formatId = !empty($payload['optionId']) ? (int) $payload['optionId'] : null;
        $boxId = !empty($payload['optionIdValue']) ? (int) $payload['optionIdValue'] : null;

        $format = $formatId ? $this->repository->getFormatById($formatId) : null;
        $box = $boxId ? $this->repository->getBoxById($boxId) : null;

        $description = [];
        if ($format) {
            $description['format'] = $format;
        }
        if ($box) {
            $description['box'] = $box;
        }

        $this->sendJsonResponse([
            'success' => true,
            'description' => $description,
        ]);
    }

    /**
     * Render manager box preview and return path.
     *
     * @param array $payload
     */
    protected function handleDrawBox(array $payload)
    {
        if (empty($payload['image'])) {
            $this->sendJsonResponse(['success' => false, 'message' => 'Missing image path']);
            return;
        }

        $destinationDir = rtrim(FAPPathBuilder::getBoxesPath(), '/\\') . '/renders';
        if (!is_dir($destinationDir)) {
            @mkdir($destinationDir, 0750, true);
        }

        $destination = $destinationDir . '/' . sha1($payload['image'] . microtime(true)) . '.png';
        $renderer = new FAPBoxRenderer();

        try {
            $renderer->renderFromImage($payload['image'], $destination, [
                'text' => isset($payload['text']) ? $payload['text'] : '',
                'color' => isset($payload['color']) ? $payload['color'] : null,
                'font' => isset($payload['font']) ? $payload['font'] : null,
                'template' => isset($payload['template']) ? $payload['template'] : null,
            ]);
        } catch (Exception $exception) {
            $this->sendJsonResponse(['success' => false, 'message' => $exception->getMessage()]);
            return;
        }

        $this->sendJsonResponse([
            'success' => true,
            'box' => [
                'path' => $destination,
                'filename' => basename($destination),
            ],
        ]);
    }

    /**
     * Output JSON payload and terminate execution.
     *
     * @param array $payload
     */
    protected function sendJsonResponse(array $payload)
    {
        header('Content-Type: application/json; charset=utf-8');
        $this->ajaxDie(json_encode($payload));
    }

    /**
     * Decode request payload from JSON body or POST parameters.
     *
     * @return array
     */
    protected function getRequestPayload()
    {
        $raw = Tools::file_get_contents('php://input');
        if ($raw) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        $payload = [];
        foreach ($_POST as $key => $value) {
            if ($key === 'action') {
                continue;
            }
            $payload[$key] = Tools::getValue($key);
        }

        return $payload;
    }

    /**
     * Normalize payload that might be provided as JSON encoded string.
     *
     * @param mixed $value
     *
     * @return array
     */
    protected function normaliseInput($value)
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return [];
    }
}
