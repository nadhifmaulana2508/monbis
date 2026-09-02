<?php
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../controllers/config/database.php';
require_once __DIR__ . '/../controllers/KpiController.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') sendResponse(405,'Gunakan metode POST.');
$body=json_decode(file_get_contents('php://input'),true); if(!is_array($body))$body=[];
$user=[];
$controller=new KpiController($pdo);
switch(strtolower(trim((string)($body['type']??'bootstrap')))){
  case 'directory':$controller->directory($body,$user);break;
  case 'ao_list':$controller->aoList($body,$user);break;
  case 'bootstrap':$controller->bootstrap($body,$user);break;
  case 'evaluation':$controller->evaluation($body,$user);break;
  case 'detail':$controller->detail($body,$user);break;
  case 'quarterly':$controller->quarterly($body,$user);break;
  case 'annual':$controller->annual($body,$user);break;
  case 'calculate':$controller->calculate($body,$user);break;
  case 'setting':$controller->setting($body,$user);break;
  case 'setting_scores':$controller->settingScores($body,$user);break;
  case 'save_indicator':$controller->saveIndicator($body,$user);break;
  case 'save_target_default':$controller->saveTargetDefault($body,$user);break;
  case 'save_score':$controller->saveScoreParameter($body,$user);break;
  case 'save_risk':$controller->saveRiskGate($body,$user);break;
  default:sendResponse(400,'Type KPI tidak dikenali.');
}
