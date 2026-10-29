<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script>
    window.MONBIS_CONFIG = Object.freeze({
      env: <?= json_encode(defined('APP_ENV') ? APP_ENV : 'custom', JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>,
      host: <?= json_encode(defined('APP_HOST') ? APP_HOST : ($_SERVER['HTTP_HOST'] ?? ''), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>,
      scheme: <?= json_encode(defined('APP_SCHEME') ? APP_SCHEME : (($_SERVER['HTTPS'] ?? '') !== 'off' ? 'https' : 'http'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>,
      baseUrl: <?= json_encode(defined('BASE_APP') ? BASE_APP : '', JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>,
      ssoApiBase: <?= json_encode(function_exists('getSsoApiBase') ? getSsoApiBase() : 'https://apisso.bkkjateng.co.id', JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>
    });
    window.BASE_APP = window.MONBIS_CONFIG.baseUrl;
    try { localStorage.removeItem('dpk_token'); } catch (error) {}
  </script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <!-- Swiper CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
  
  <script src="https://cdn.tailwindcss.com"></script>

<link rel="icon" type="image/webp" sizes="32x32" href="./img/monbis-icon.webp?v=1">
<link rel="icon" type="image/webp" sizes="16x16" href="./img/monbis-icon.webp?v=1">

<link rel="apple-touch-icon" sizes="180x180" href="./img/monbis-icon.webp?v=1">



  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
  <?php if (($page ?? '') === 'login'): ?>
    <link rel="stylesheet" href="./assets/css/login.css?v=3">
    <script src="./assets/js/login-showcase.js?v=1" defer></script>
  <?php endif; ?>
  <title>MONBIS</title>
</head>
<body class="bg-sky-50">
  <!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
