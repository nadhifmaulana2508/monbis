<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * =========================
 * BASE APP (AUTO)
 * =========================
 * Local   : http://localhost/report-dpk
 * Server  : https://domain.com
 */
define(
    'BASE_APP',
    (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' .
    $_SERVER['HTTP_HOST'] .
    (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false ? '/report-dpk' : '')
);

// =========================
// AMBIL URL DARI REWRITE
// =========================
$url = $_GET['url'] ?? '';
$url = trim($url, '/');

// =========================
// JANGAN LEWATKAN API KE ROUTER HALAMAN
// =========================
if (strpos($url, 'api/') === 0) {
    $apiPath = __DIR__ . '/' . $url . '.php';

    if (is_file($apiPath)) {
        require $apiPath;
    } else {
        http_response_code(404);

        header('Content-Type: application/json; charset=utf-8');

        echo json_encode([
            'status'  => false,
            'message' => 'API endpoint not found'
        ]);
    }

    exit;
}

// =========================
// CEK STATUS LOGIN
// =========================
$isLoggedIn = isset($_COOKIE['sso_token']) &&
              !empty($_COOKIE['sso_token']);

// =========================
// ROUTING DEFAULT
// =========================
if ($url === '') {
    $url = $isLoggedIn ? 'dashboard' : 'login';
}

// Page / parameter
[$page, $param] = array_pad(
    explode('/', $url, 2),
    2,
    null
);

// =========================
// PROTEKSI HALAMAN
// =========================
if (
    !$isLoggedIn &&
    $page !== 'login' &&
    $page !== 'tv'
) {
    header('Location: ' . BASE_APP . '/login');
    exit;
}

if ($isLoggedIn && $page === 'login') {
    header('Location: ' . BASE_APP . '/dashboard');
    exit;
}

$baseDir = __DIR__;

// =========================
// HEADER
// =========================
include $baseDir . '/views/header.php';

// =========================
// NAVBAR
// =========================
if ($page !== 'login' && $page !== 'tv') {
    include $baseDir . '/views/navbar.php';
}

// =========================
// LOAD PAGE
// =========================
$path = $baseDir . "/pages/{$page}.php";

if (is_file($path)) {
    if ($param !== null) {
        $_GET['id'] = $param;
    }

    include $path;
} else {
    http_response_code(404);

    echo '
        <div style="
            padding:40px;
            font-family:Arial,sans-serif;
            text-align:center;
        ">
            <h1>404 - Halaman tidak ditemukan</h1>
        </div>
    ';
}

// =========================
// MODAL PRANK KING YOYOK
// Hanya dirender pada halaman login-protected
// =========================
if (
    $isLoggedIn &&
    $page !== 'login' &&
    $page !== 'tv'
) {
    ?>
    <style>
        #kingYoyokModal {
            position: fixed;
            inset: 0;
            z-index: 999999;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 16px;
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
        }

        #kingYoyokModal.king-yoyok-show {
            display: flex;
        }

        .king-yoyok-card {
            position: relative;
            width: min(430px, 100%);
            overflow: hidden;
            border: 1px solid #fed7aa;
            border-radius: 22px;
            background: #ffffff;
            box-shadow:
                0 30px 80px rgba(15, 23, 42, 0.35),
                0 0 0 1px rgba(255, 255, 255, 0.5);
            animation: kingYoyokScaleUp 0.28s ease-out;
        }

        .king-yoyok-header {
            position: relative;
            overflow: hidden;
            padding: 26px 24px 22px;
            text-align: center;
            background:
                radial-gradient(
                    circle at top right,
                    rgba(251, 191, 36, 0.35),
                    transparent 42%
                ),
                linear-gradient(
                    135deg,
                    #fff7ed,
                    #fffbeb
                );
            border-bottom: 1px solid #fed7aa;
        }

        .king-yoyok-header::before,
        .king-yoyok-header::after {
            content: "";
            position: absolute;
            border-radius: 999px;
            background: rgba(249, 115, 22, 0.11);
        }

        .king-yoyok-header::before {
            width: 110px;
            height: 110px;
            top: -60px;
            left: -35px;
        }

        .king-yoyok-header::after {
            width: 80px;
            height: 80px;
            right: -25px;
            bottom: -45px;
        }

        .king-yoyok-close {
            position: absolute;
            top: 12px;
            right: 12px;
            z-index: 3;
            width: 34px;
            height: 34px;
            border: 0;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.85);
            color: #64748b;
            font-size: 21px;
            line-height: 1;
            cursor: pointer;
            transition: 0.18s ease;
        }

        .king-yoyok-close:hover {
            background: #fee2e2;
            color: #dc2626;
            transform: rotate(8deg);
        }

        .king-yoyok-emoji {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 82px;
            height: 82px;
            margin: 0 auto 13px;
            border: 3px solid #fdba74;
            border-radius: 999px;
            background: #ffffff;
            font-size: 43px;
            box-shadow: 0 12px 25px rgba(249, 115, 22, 0.18);
            animation: kingYoyokCook 1.1s ease-in-out infinite alternate;
        }

        .king-yoyok-label {
            position: relative;
            z-index: 2;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 23px;
            padding: 0 9px;
            border: 1px solid #fed7aa;
            border-radius: 999px;
            background: #ffffff;
            color: #c2410c;
            font-size: 10px;
            font-weight: 900;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .king-yoyok-title {
            position: relative;
            z-index: 2;
            margin: 12px 0 0;
            color: #7c2d12;
            font-size: 23px;
            line-height: 1.2;
            font-weight: 900;
        }

        .king-yoyok-body {
            padding: 22px 24px 24px;
            text-align: center;
        }

        .king-yoyok-message {
            margin: 0;
            color: #334155;
            font-size: 15px;
            line-height: 1.6;
            font-weight: 700;
        }

        .king-yoyok-highlight {
            display: inline-block;
            margin-top: 4px;
            color: #ea580c;
            font-size: 18px;
            font-weight: 900;
        }

        .king-yoyok-progress {
            height: 8px;
            margin-top: 20px;
            overflow: hidden;
            border-radius: 999px;
            background: #ffedd5;
        }

        .king-yoyok-progress-bar {
            width: 42%;
            height: 100%;
            border-radius: inherit;
            background: linear-gradient(
                90deg,
                #f97316,
                #facc15,
                #f97316
            );
            animation: kingYoyokProgress 1.5s ease-in-out infinite;
        }

        .king-yoyok-note {
            margin-top: 9px;
            color: #94a3b8;
            font-size: 11px;
            font-weight: 700;
        }

        .king-yoyok-button {
            width: 100%;
            height: 43px;
            margin-top: 20px;
            border: 0;
            border-radius: 12px;
            background: linear-gradient(
                135deg,
                #f97316,
                #ea580c
            );
            color: #ffffff;
            font-size: 13px;
            font-weight: 900;
            cursor: pointer;
            box-shadow: 0 8px 18px rgba(234, 88, 12, 0.22);
            transition: 0.18s ease;
        }

        .king-yoyok-button:hover {
            transform: translateY(-1px);
            box-shadow: 0 12px 23px rgba(234, 88, 12, 0.28);
        }

        @keyframes kingYoyokScaleUp {
            from {
                opacity: 0;
                transform: scale(0.9) translateY(12px);
            }

            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        @keyframes kingYoyokCook {
            from {
                transform: rotate(-4deg) scale(1);
            }

            to {
                transform: rotate(4deg) scale(1.05);
            }
        }

        @keyframes kingYoyokProgress {
            0% {
                transform: translateX(-110%);
            }

            100% {
                transform: translateX(260%);
            }
        }

        @media (max-width: 480px) {
            .king-yoyok-card {
                border-radius: 18px;
            }

            .king-yoyok-header {
                padding: 22px 18px 18px;
            }

            .king-yoyok-emoji {
                width: 70px;
                height: 70px;
                font-size: 36px;
            }

            .king-yoyok-title {
                font-size: 19px;
            }

            .king-yoyok-body {
                padding: 18px;
            }

            .king-yoyok-message {
                font-size: 13px;
            }

            .king-yoyok-highlight {
                font-size: 16px;
            }
        }
    </style>

    <div
        id="kingYoyokModal"
        role="dialog"
        aria-modal="true"
        aria-labelledby="kingYoyokTitle"
    >
        <div class="king-yoyok-card">
            <div class="king-yoyok-header">
                <button
                    type="button"
                    class="king-yoyok-close"
                    onclick="closeKingYoyokModal()"
                    aria-label="Tutup"
                    title="Tutup"
                >
                    &times;
                </button>

                <div class="king-yoyok-emoji">🍗</div>

                <div class="king-yoyok-label">
                    Informasi Kerajaan
                </div>

                <h2
                    id="kingYoyokTitle"
                    class="king-yoyok-title"
                >
                    Pengumuman Penting!
                </h2>
            </div>

            <div class="king-yoyok-body">
                <p class="king-yoyok-message">
                    Mohon bersabar sebentar karena
                    <br>

                    <span class="king-yoyok-highlight">
                        King Yoyok sedang memasak ayam goreng
                    </span>

                    <br>
                    untuk seluruh rakyat Monbis.
                </p>

                <div class="king-yoyok-progress">
                    <div class="king-yoyok-progress-bar"></div>
                </div>

                <div class="king-yoyok-note">
                    Status: ayam sedang digoreng sampai crispy...
                </div>

                <button
                    type="button"
                    class="king-yoyok-button"
                    onclick="closeKingYoyokModal()"
                >
                    Siap, Saya Tunggu Ayamnya
                </button>
            </div>
        </div>
    </div>
    <?php
}

// =========================
// GLOBAL SCRIPT
// =========================
include $baseDir . '/views/script.php';

// =========================
// SCRIPT PRANK
// =========================
if (
    $isLoggedIn &&
    $page !== 'login' &&
    $page !== 'tv'
) {
    ?>
    <script>
        (() => {
            'use strict';

            const PRANK_TARGET_ID = '102-128';
            const PRANK_STORAGE_KEY =
                `king_yoyok_prank_shown_${PRANK_TARGET_ID}`;

            let checkAttempts = 0;
            const maxCheckAttempts = 40;

            function getLoggedInEmployeeId() {
                try {
                    if (typeof window.getUser !== 'function') {
                        return '';
                    }

                    const user = window.getUser() || {};

                    return String(
                        user.id_peg ??
                        user.employee_id ??
                        user.data?.id_peg ??
                        ''
                    ).trim();
                } catch (error) {
                    console.warn(
                        'Tidak dapat membaca data user:',
                        error
                    );

                    return '';
                }
            }

            window.openKingYoyokModal = function () {
                const modal = document.getElementById(
                    'kingYoyokModal'
                );

                if (!modal) {
                    return;
                }

                modal.classList.add('king-yoyok-show');
                document.body.style.overflow = 'hidden';
            };

            window.closeKingYoyokModal = function () {
                const modal = document.getElementById(
                    'kingYoyokModal'
                );

                if (!modal) {
                    return;
                }

                modal.classList.remove('king-yoyok-show');
                document.body.style.overflow = '';
            };

            function checkKingYoyokTarget() {
                checkAttempts++;

                const employeeId = getLoggedInEmployeeId();

                if (!employeeId) {
                    if (checkAttempts < maxCheckAttempts) {
                        setTimeout(
                            checkKingYoyokTarget,
                            150
                        );
                    }

                    return;
                }

                if (employeeId !== PRANK_TARGET_ID) {
                    return;
                }

                /*
                 * Supaya modal tidak muncul terus setiap pindah halaman
                 * atau refresh selama tab browser masih sama.
                 */
                if (
                    sessionStorage.getItem(
                        PRANK_STORAGE_KEY
                    ) === '1'
                ) {
                    return;
                }

                sessionStorage.setItem(
                    PRANK_STORAGE_KEY,
                    '1'
                );

                setTimeout(() => {
                    window.openKingYoyokModal();
                }, 650);
            }

            document.addEventListener(
                'keydown',
                event => {
                    if (event.key === 'Escape') {
                        window.closeKingYoyokModal();
                    }
                }
            );

            document.addEventListener(
                'DOMContentLoaded',
                checkKingYoyokTarget
            );
        })();
    </script>
    <?php
}

// =========================
// FOOTER
// =========================
include $baseDir . '/views/footer.php';