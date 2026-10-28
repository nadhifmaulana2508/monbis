<?php
require_once __DIR__ . '/../helpers/sso_guard.php';

function verifyTokenOrDie($token) {
    $normalized = trim((string)preg_replace('/^Bearer\\s+/i', '', (string)$token));
    $user = $normalized !== '' ? ssoWhoami($normalized) : null;
    if (!$user) sendResponse(401, 'Token SSO tidak valid atau sudah kedaluwarsa.');
    return $user;
}
