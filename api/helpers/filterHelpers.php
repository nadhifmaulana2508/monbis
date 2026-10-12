<?php
// helpers/FilterHelper.php

/**
 * Meracik query filter untuk Korwil, Cabang, Kankas, dan AO
 * Mendukung prefix alias (misal: 'n.' untuk nominatif n)
 */
function buildBankFilters($input, $prefix = '') {
    $sql = "";
    $params = [];
    $pfx = $prefix ? $prefix . '.' : '';

    $kode_kantor = $input['kode_kantor'] ?? null;
    $korwil      = strtoupper($input['korwil'] ?? '');
    $kankas      = $input['kode_kankas'] ?? null; // kode_group1
    $ao          = $input['kode_ao'] ?? null;     // kode_group2

    // 1. Filter Korwil / Cabang (Di tabel nominatif pakai 'kode_cabang')
    if (!empty($kode_kantor) && $kode_kantor !== '000') {
        $kode_kantor = str_pad((string)$kode_kantor, 3, '0', STR_PAD_LEFT);
        $sql .= " AND {$pfx}kode_cabang = :kode_kantor";
        $params[':kode_kantor'] = $kode_kantor;
    } elseif (!empty($korwil)) {
        if ($korwil === 'SEMARANG') {
            $sql .= " AND {$pfx}kode_cabang BETWEEN '001' AND '007'";
        } elseif ($korwil === 'SOLO') {
            $sql .= " AND {$pfx}kode_cabang BETWEEN '008' AND '014'";
        } elseif ($korwil === 'BANYUMAS') {
            $sql .= " AND {$pfx}kode_cabang BETWEEN '015' AND '021'";
        } elseif ($korwil === 'PEKALONGAN') {
            $sql .= " AND {$pfx}kode_cabang BETWEEN '022' AND '028'";
        }
    }

    // 2. Filter Kankas (Group 1)
    if (!empty($kankas)) {
        $sql .= " AND {$pfx}kode_group1 = :kankas";
        $params[':kankas'] = $kankas;
    }

    // 3. Filter AO (Group 2)
    if (!empty($ao)) {
        $sql .= " AND {$pfx}kode_group2 = :ao";
        $params[':ao'] = $ao;
    }

    return [
        'sql' => $sql,
        'params' => $params
    ];
}

/**
 * Mengambil list Kankas & AO berdasarkan Cabang/Korwil (Membaca Master Tabel)
 */
function getDropdownKankasAo($pdo, $input) {
    $kode_kantor = $input['kode_kantor'] ?? null;
    $korwil      = strtoupper($input['korwil'] ?? '');

    $sqlFilter = "";
    $params = [];

    // Filter Area Khusus Master Tabel (Pakai kolom 'kode_kantor' sesuai DB)
    if (!empty($kode_kantor) && $kode_kantor !== '000') {
        $kode_kantor = str_pad((string)$kode_kantor, 3, '0', STR_PAD_LEFT);
        $sqlFilter .= " AND kode_kantor = :kode_kantor_master";
        $params[':kode_kantor_master'] = $kode_kantor;
    } elseif (!empty($korwil)) {
        if ($korwil === 'SEMARANG') {
            $sqlFilter .= " AND kode_kantor BETWEEN '001' AND '007'";
        } elseif ($korwil === 'SOLO') {
            $sqlFilter .= " AND kode_kantor BETWEEN '008' AND '014'";
        } elseif ($korwil === 'BANYUMAS') {
            $sqlFilter .= " AND kode_kantor BETWEEN '015' AND '021'";
        } elseif ($korwil === 'PEKALONGAN') {
            $sqlFilter .= " AND kode_kantor BETWEEN '022' AND '028'";
        }
    }

    try {
        // Eksekusi Master Kankas (mengacu ke gambar ke-2)
        $sqlKankas = "SELECT kode_group1 as kode, deskripsi_group1 as nama 
                      FROM kankas 
                      WHERE 1=1 {$sqlFilter} 
                      ORDER BY kode_kantor ASC, kode_group1 ASC";
        
        $stmtK = $pdo->prepare($sqlKankas);
        foreach ($params as $key => $val) { $stmtK->bindValue($key, $val); }
        $stmtK->execute();
        $listKankas = $stmtK->fetchAll(PDO::FETCH_ASSOC);

        // Eksekusi Master AO (mengacu ke gambar ke-1)
        $sqlAo = "SELECT kode_group2 as kode, nama_ao as nama 
                  FROM ao_kredit 
                  WHERE 1=1 {$sqlFilter} 
                  ORDER BY kode_kantor ASC, nama_ao ASC";
        
        $stmtA = $pdo->prepare($sqlAo);
        foreach ($params as $key => $val) { $stmtA->bindValue($key, $val); }
        $stmtA->execute();
        $listAo = $stmtA->fetchAll(PDO::FETCH_ASSOC);

        return [
            'list_kankas' => $listKankas,
            'list_ao'     => $listAo
        ];
    } catch (PDOException $e) {
        error_log("Error Helper Master Dropdown: " . $e->getMessage());
        return ['list_kankas' => [], 'list_ao' => []];
    }
}