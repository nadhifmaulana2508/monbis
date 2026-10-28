<?php
require_once __DIR__ . '/../helpers/sso_guard.php';

function requireAuth(): array {
    return requireAppAuth();
}
