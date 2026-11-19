<?php
declare(strict_types=1);

require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../helpers/rbac.php';

final class AuditLogController
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        rbacEnsureSchema($this->pdo);
    }

    public function list(array $query): void
    {
        $user = rbacRequireUser($this->pdo);
        if (!rbacCanAccessPage($this->pdo, $user, 'audit_log')) {
            sendResponse(403, 'Anda tidak memiliki akses ke Audit Log.');
        }

        $jakartaTimezone = new DateTimeZone('Asia/Jakarta');
        $today = (new DateTimeImmutable('now', $jakartaTimezone))->setTime(0, 0);
        $defaultFrom = $today->modify('-29 days')->format('Y-m-d');
        $dateFrom = $this->dateFilter($query, 'date_from', $defaultFrom);
        $dateTo = $this->dateFilter($query, 'date_to', $today->format('Y-m-d'));
        if ($dateFrom > $dateTo) {
            sendResponse(422, 'Tanggal awal tidak boleh melewati tanggal akhir.');
        }

        $employeeId = trim((string)($query['employee_id'] ?? ''));
        if (strlen($employeeId) > 50) {
            sendResponse(422, 'ID pegawai terlalu panjang.');
        }

        $menuKey = trim((string)($query['menu_key'] ?? ''));
        $menuKeys = array_column(rbacFeatureCatalog(), 'feature_key');
        if ($menuKey !== '' && !in_array($menuKey, $menuKeys, true)) {
            sendResponse(422, 'Menu tidak dikenali.');
        }

        $accessResult = strtolower(trim((string)($query['access_result'] ?? '')));
        if ($accessResult !== '' && !in_array($accessResult, ['allowed', 'denied'], true)) {
            sendResponse(422, 'Status akses tidak dikenali.');
        }

        $actionType = strtolower(trim((string)($query['action_type'] ?? '')));
        if ($actionType !== '' && !in_array($actionType, ['menu_view', 'detail', 'download', 'update'], true)) {
            sendResponse(422, 'Jenis aktivitas tidak dikenali.');
        }

        $page = filter_var($query['page'] ?? 1, FILTER_VALIDATE_INT);
        $page = max(1, (int)($page ?: 1));
        $limit = filter_var($query['limit'] ?? 50, FILTER_VALIDATE_INT);
        $limit = min(100, max(10, (int)($limit ?: 50)));

        $where = ['l.accessed_at >= :date_from', 'l.accessed_at < :date_to'];
        $utcTimezone = new DateTimeZone('UTC');
        $params = [
            'date_from' => (new DateTimeImmutable($dateFrom . ' 00:00:00', $jakartaTimezone))
                ->setTimezone($utcTimezone)->format('Y-m-d H:i:s'),
            'date_to' => (new DateTimeImmutable($dateTo . ' 00:00:00', $jakartaTimezone))
                ->modify('+1 day')->setTimezone($utcTimezone)->format('Y-m-d H:i:s'),
        ];
        if ($employeeId !== '') {
            $where[] = 'l.employee_id = :employee_id';
            $params['employee_id'] = $employeeId;
        }
        if ($menuKey !== '') {
            $where[] = 'l.feature_key = :menu_key';
            $params['menu_key'] = $menuKey;
        }
        if ($accessResult !== '') {
            $where[] = 'l.access_result = :access_result';
            $params['access_result'] = $accessResult;
        }
        if ($actionType !== '') {
            $where[] = 'l.action_type = :action_type';
            $params['action_type'] = $actionType;
        }
        $whereSql = implode(' AND ', $where);

        $count = $this->pdo->prepare("SELECT COUNT(*) FROM rbac_menu_access_log l WHERE {$whereSql}");
        $count->execute($params);
        $total = (int)$count->fetchColumn();
        $totalPages = max(1, (int)ceil($total / $limit));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $limit;

        $stmt = $this->pdo->prepare("SELECT l.id, l.employee_id, l.employee_name, l.feature_key,
                COALESCE(m.feature_label, l.feature_key) AS menu_label,
                l.request_path, l.request_method, l.http_status, l.ip_address,
                l.action_type, l.action_label, l.access_result, l.accessed_at
            FROM rbac_menu_access_log l
            LEFT JOIN rbac_menu m ON m.feature_key = l.feature_key
            WHERE {$whereSql}
            ORDER BY l.accessed_at DESC, l.id DESC
            LIMIT :limit OFFSET :offset");
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value, PDO::PARAM_STR);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $this->fillMissingEmployeeNames($stmt->fetchAll(PDO::FETCH_ASSOC));

        $menus = $this->pdo->query("SELECT feature_key, feature_label
            FROM rbac_menu
            WHERE is_active = 1 AND feature_key LIKE 'page:%'
            ORDER BY sort_order, feature_label")->fetchAll(PDO::FETCH_ASSOC);

        sendResponse(200, 'Audit log berhasil dimuat.', [
            'rows' => $rows,
            'menus' => $menus,
            'filters' => [
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'employee_id' => $employeeId,
                'menu_key' => $menuKey,
                'access_result' => $accessResult,
                'action_type' => $actionType,
            ],
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'total_pages' => $totalPages,
            ],
        ]);
    }

    public function recordEvent(array $input): void
    {
        $user = rbacRequireUser($this->pdo);
        $pageSlug = strtolower(trim((string)($input['page_slug'] ?? '')));
        $featureKey = 'page:' . $pageSlug;
        $knownKeys = array_column(rbacFeatureCatalog(), 'feature_key');
        if ($pageSlug === '' || !in_array($featureKey, $knownKeys, true)) {
            sendResponse(422, 'Menu tidak dikenali.');
        }
        if (!rbacCanAccessPage($this->pdo, $user, $pageSlug)) {
            sendResponse(403, 'Anda tidak memiliki akses ke menu ini.');
        }

        $actionType = strtolower(trim((string)($input['action_type'] ?? '')));
        if (!in_array($actionType, ['detail', 'download'], true)) {
            sendResponse(422, 'Jenis aktivitas tidak dikenali.');
        }

        $actionLabel = $this->normalizeActionLabel((string)($input['action_label'] ?? ''));
        $employeeName = $this->normalizeActionLabel((string)($user['full_name'] ?? ''));
        $stmt = $this->pdo->prepare("INSERT INTO rbac_menu_access_log
            (employee_id, employee_name, feature_key, request_path, request_method, http_status, ip_address, action_type, action_label, access_result, accessed_at)
            VALUES (:employee_id, :employee_name, :feature_key, :request_path, :request_method, 200, :ip_address, :action_type, :action_label, 'allowed', :accessed_at)");
        $stmt->execute([
            'employee_id' => (string)$user['employee_id'],
            'employee_name' => $employeeName !== '' ? $employeeName : null,
            'feature_key' => $featureKey,
            'request_path' => $this->eventRequestPath($pageSlug),
            'request_method' => rbacAuditRequestMethod((string)($_SERVER['REQUEST_METHOD'] ?? 'POST')),
            'ip_address' => rbacAuditClientIpAddress(),
            'action_type' => $actionType,
            'action_label' => $actionLabel !== '' ? $actionLabel : null,
            'accessed_at' => gmdate('Y-m-d H:i:s'),
        ]);

        sendResponse(200, 'Aktivitas berhasil dicatat.');
    }

    private function eventRequestPath(string $pageSlug): string
    {
        $requestPath = parse_url((string)($_SERVER['REQUEST_URI'] ?? ''), PHP_URL_PATH);
        $requestPath = is_string($requestPath) ? $requestPath : '';
        $requestPath = preg_replace('/[\x00-\x1F\x7F]+/u', '', $requestPath) ?? '';
        $requestPath = '/' . ltrim(trim($requestPath), '/');
        if ($requestPath === '/') {
            $requestPath = '/api/index.php';
        }
        if (function_exists('mb_substr')) {
            $requestPath = mb_substr($requestPath, 0, 190, 'UTF-8');
        } else {
            $requestPath = substr($requestPath, 0, 190);
        }
        return $requestPath . ' [page=' . $pageSlug . ']';
    }

    private function fillMissingEmployeeNames(array $rows): array
    {
        $missingIds = [];
        foreach ($rows as $row) {
            if (trim((string)($row['employee_name'] ?? '')) === '') {
                $missingIds[] = (string)($row['employee_id'] ?? '');
            }
        }
        $missingIds = array_values(array_unique(array_filter($missingIds, static fn(string $id): bool => $id !== '')));
        if (!$missingIds) {
            return $rows;
        }

        try {
            $simpeg = rbacSimpegPdo();
            $placeholders = [];
            $params = [];
            foreach ($missingIds as $index => $employeeId) {
                $placeholder = ':employee_' . $index;
                $placeholders[] = $placeholder;
                $params[$placeholder] = $employeeId;
            }
            $nameQuery = $simpeg->prepare("SELECT CAST(id_peg AS CHAR) AS employee_id, nama AS full_name
                FROM tb_pegawai WHERE id_peg IN (" . implode(', ', $placeholders) . ')');
            foreach ($params as $placeholder => $employeeId) {
                $nameQuery->bindValue($placeholder, $employeeId, PDO::PARAM_STR);
            }
            $nameQuery->execute();
            $names = [];
            foreach ($nameQuery->fetchAll(PDO::FETCH_ASSOC) as $person) {
                $names[(string)$person['employee_id']] = (string)$person['full_name'];
            }
            foreach ($rows as &$row) {
                if (trim((string)($row['employee_name'] ?? '')) === '') {
                    $row['employee_name'] = $names[(string)($row['employee_id'] ?? '')] ?? '';
                }
            }
            unset($row);
        } catch (Throwable $error) {
            error_log('Audit log employee name lookup failed: ' . $error->getMessage());
        }
        return $rows;
    }

    private function normalizeActionLabel(string $label): string
    {
        $label = trim(strip_tags($label));
        $label = preg_replace('/[\x00-\x1F\x7F]+/u', ' ', $label) ?? '';
        $label = preg_replace('/\s+/u', ' ', $label) ?? trim($label);
        if (function_exists('mb_substr')) {
            return mb_substr($label, 0, 180, 'UTF-8');
        }
        $characters = preg_split('//u', $label, -1, PREG_SPLIT_NO_EMPTY);
        return implode('', array_slice(is_array($characters) ? $characters : [], 0, 180));
    }

    private function dateFilter(array $query, string $key, string $fallback): string
    {
        $value = trim((string)($query[$key] ?? ''));
        if ($value === '') {
            return $fallback;
        }
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            sendResponse(422, 'Format tanggal tidak valid.');
        }
        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value);
        if (!$date || $date->format('Y-m-d') !== $value) {
            sendResponse(422, 'Tanggal tidak valid.');
        }
        return $value;
    }
}
