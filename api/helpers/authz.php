<?php
// api/helpers/authz.php

function userKC(array $user): ?string {
  $kc = $user['kode'] ?? ($user['kode_kantor'] ?? null);
  return $kc ? str_pad((string)$kc, 3, '0', STR_PAD_LEFT) : null;
}

function isHQ(array $user): bool {
  return userKC($user) === '000';
}

function isBranchVerifier(array $user): bool {
  $pos = strtolower((string)($user['job_position'] ?? ''));
  $kc  = userKC($user);
  if (!$kc || $kc === '000') return false;
  return (
    strpos($pos, 'kepala cabang') !== false ||
    strpos($pos, 'kabid pemasaran') !== false ||
    strpos($pos, 'kasubid remedial') !== false
  );
}

function canViewBranch(array $user, string $recordKC): bool {
  if (isHQ($user)) return true;
  return userKC($user) === $recordKC;
}

function canVerifyKunjungan(array $user, string $recordKC): bool {
  if (isHQ($user)) return false;             // pusat read-only
  if (!isBranchVerifier($user)) return false;
  return userKC($user) === $recordKC;
}
