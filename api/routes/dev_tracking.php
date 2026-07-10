<?php

require_once __DIR__ . '/../controllers/DevTrackingController.php';
require_once __DIR__ . '/../controllers/config/database.php';

$controller = new DevTrackingController($pdo);

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents("php://input"), true) ?? [];

switch ($method) {
    case 'POST':
        $type = $input['type'] ?? '';

        switch ($type) {
            // === SUMMARY ===
            case 'summary':
                $controller->getSummary();
                break;

            // === MODULE ===
            case 'get_modules':
                $controller->getModules();
                break;
            case 'create_module':
                $controller->createModule($input);
                break;
            case 'update_module':
                $controller->updateModule($input);
                break;
            case 'delete_module':
                $controller->deleteModule($input);
                break;

            // === FEATURE ===
            case 'get_features':
                $controller->getFeatures($input);
                break;
            case 'get_feature_detail':
                $controller->getFeatureDetail($input);
                break;
            case 'create_feature':
                $controller->createFeature($input);
                break;
            case 'update_feature':
                $controller->updateFeature($input);
                break;
            case 'delete_feature':
                $controller->deleteFeature($input);
                break;

            // === PROGRESS LOG ===
            case 'get_logs':
                $controller->getProgressLogs($input);
                break;
            case 'create_log':
                $controller->createProgressLog($input);
                break;

            // === BACKLOG IDEAS ===
            case 'get_ideas':
                $controller->getBacklogIdeas($input);
                break;
            case 'create_idea':
                $controller->createBacklogIdea($input);
                break;
            case 'update_idea':
                $controller->updateBacklogIdea($input);
                break;
            case 'delete_idea':
                $controller->deleteBacklogIdea($input);
                break;

            default:
                sendResponse(400, "Type tidak dikenali. Available: summary, get_modules, create_module, update_module, delete_module, get_features, get_feature_detail, create_feature, update_feature, delete_feature, get_logs, create_log, get_ideas, create_idea, update_idea, delete_idea");
                break;
        }
        break;

    default:
        sendResponse(405, "Method not allowed");
        break;
}

