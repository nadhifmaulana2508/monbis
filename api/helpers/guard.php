<?php
require_once __DIR__ . '/sso_guard.php';

function requireAuth(): array {
  return requireAppAuth();
}
