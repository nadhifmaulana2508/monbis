<?php

require_once __DIR__ . '/../helpers/response.php';

class AnggotaController
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getRekapAnggota(array $input): void
    {
        $kodeKantor = trim((string)($input['kode_kantor'] ?? ''));
        $korwil = strtoupper(trim((string)($input['korwil'] ?? '')));
        $asOf = trim((string)($input['as_of'] ?? date('Y-m-d')));

        if ($kodeKantor === '' && $korwil === '') {
            sendResponse(400, "Parameter 'kode_kantor' atau 'korwil' wajib diisi.");
        }

        $korwilRanges = [
            'SEMARANG' => ['001', '007'],
            'SOLO' => ['008', '014'],
            'BANYUMAS' => ['015', '021'],
            'PEKALONGAN' => ['022', '028'],
        ];
        if ($korwil !== '' && !isset($korwilRanges[$korwil])) {
            sendResponse(400, 'Korwil tidak dikenali.');
        }

        $date = DateTime::createFromFormat('!Y-m-d', $asOf);
        if (!$date || $date->format('Y-m-d') !== $asOf) {
            $asOf = date('Y-m-d');
        }

        $summaryOnly = filter_var($input['summary_only'] ?? false, FILTER_VALIDATE_BOOLEAN)
            || $korwil !== ''
            || $kodeKantor === '000';
        $summarySelect = "p.tgl_lhr,
                    p.jk AS kelamin,
                    k.nama_kantor AS branch_name,
                    mj.group_jabatan,
                    mj.nama_jabatan AS job_position";
        $detailSelect = "k.kode_cabang AS kode,
                    j.id_peg AS employee_id,
                    p.nama AS full_name,
                    p.tgl_lhr,
                    p.jk AS kelamin,
                    p.status_kepeg,
                    p.tmt_kerja AS mulai_bekerja,
                    k.nama_kantor AS branch_name,
                    mj.nama_unit_kerja AS unit_kerja,
                    mj.nama_jabatan AS job_position,
                    mj.level,
                    mj.group_jabatan";
        $sql = "SELECT " . ($summaryOnly ? $summarySelect : $detailSelect) . "
                FROM tb_jabatan j
                INNER JOIN tb_pegawai p
                    ON j.id_peg = p.id_peg
                INNER JOIN tb_master_jabatan mj
                    ON CAST(j.kode_jabatan AS CHAR) = CAST(mj.kode_jabatan AS CHAR)
                LEFT JOIN tb_kantor k
                    ON j.unit_kerja = k.kode_kantor_detail
                WHERE j.status_jab = 'Aktif'";

        $params = [];
        if ($korwil !== '') {
            $sql .= " AND k.kode_cabang BETWEEN :kode_awal AND :kode_akhir";
            $params[':kode_awal'] = $korwilRanges[$korwil][0];
            $params[':kode_akhir'] = $korwilRanges[$korwil][1];
        } elseif ($kodeKantor !== '000') {
            $sql .= " AND k.kode_cabang = :kode_cabang";
            $params[':kode_cabang'] = $kodeKantor;
        }

        if (!$summaryOnly) {
            $sql .= " ORDER BY CASE UPPER(TRIM(COALESCE(mj.group_jabatan, '')))
                           WHEN 'PE' THEN 1
                           WHEN 'PS' THEN 2
                           WHEN 'STAF' THEN 3
                           WHEN 'NON STAF' THEN 4
                           ELSE 5
                         END,
                         p.nama ASC,
                         j.id_peg ASC";
        }

        try {
            $stmt = $this->pdo->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            sendResponse(500, 'Gagal mengambil rekap anggota: ' . $e->getMessage());
        }

        $male = 0;
        $female = 0;
        $unknownGender = 0;
        $ageTotal = 0;
        $ageCount = 0;
        $ageDistribution = ['under_30' => 0, '30_39' => 0, '40_49' => 0, '50_plus' => 0, 'unknown' => 0];
        $groupCounts = ['PE' => 0, 'PS' => 0, 'STAF' => 0, 'NON STAF' => 0, 'LAINNYA' => 0];
        $positionCounts = ['PE' => [], 'PS' => [], 'STAF' => []];

        foreach ($rows as &$row) {
            $row['age'] = $this->calculateAge($row['tgl_lhr'] ?? null, $asOf);
            $gender = strtoupper(trim((string)($row['kelamin'] ?? '')));
            if (in_array($gender, ['L', 'LAKI-LAKI', 'LAKI LAKI', 'PRIA', 'M'], true)) {
                $male++;
            } elseif (in_array($gender, ['P', 'PEREMPUAN', 'WANITA', 'W'], true)) {
                $female++;
            } else {
                $unknownGender++;
            }
            if ($row['age'] !== null) {
                $ageTotal += $row['age'];
                $ageCount++;
                if ($row['age'] < 30) $ageDistribution['under_30']++;
                elseif ($row['age'] < 40) $ageDistribution['30_39']++;
                elseif ($row['age'] < 50) $ageDistribution['40_49']++;
                else $ageDistribution['50_plus']++;
            } else {
                $ageDistribution['unknown']++;
            }
            $group = strtoupper(trim((string)($row['group_jabatan'] ?? '')));
            if (in_array($group, ['PE', 'PS', 'STAF', 'NON STAF'], true)) $groupCounts[$group]++;
            else $groupCounts['LAINNYA']++;

            if (isset($positionCounts[$group])) {
                $position = trim((string)($row['job_position'] ?? ''));
                if ($position === '') $position = 'Jabatan belum diisi';
                $positionKey = strtoupper($position);
                if (!isset($positionCounts[$group][$positionKey])) {
                    $positionCounts[$group][$positionKey] = ['jabatan' => $position, 'jumlah' => 0];
                }
                $positionCounts[$group][$positionKey]['jumlah']++;
            }
        }
        unset($row);

        $groupOrder = ['PE' => 1, 'PS' => 2, 'STAF' => 3];
        $positions = [];
        foreach ($positionCounts as $group => $items) {
            foreach ($items as $item) {
                $positions[] = ['group_jabatan' => $group, 'jabatan' => $item['jabatan'], 'jumlah' => $item['jumlah']];
            }
        }
        usort($positions, static function (array $a, array $b) use ($groupOrder): int {
            $groupCompare = $groupOrder[$a['group_jabatan']] <=> $groupOrder[$b['group_jabatan']];
            return $groupCompare !== 0 ? $groupCompare : strnatcasecmp($a['jabatan'], $b['jabatan']);
        });

        $branchName = $korwil !== ''
            ? 'Korwil ' . ucfirst(strtolower($korwil))
            : ($kodeKantor === '000' ? 'Pusat' : ($rows[0]['branch_name'] ?? 'Cabang ' . $kodeKantor));

        sendResponse(200, 'Sukses', [
            'meta' => [
                'kode_cabang' => $kodeKantor,
                'korwil' => $korwil !== '' ? $korwil : null,
                'scope_label' => $branchName,
                'branch_name' => $branchName,
                'as_of' => $asOf,
                'total' => count($rows),
                'male' => $male,
                'female' => $female,
                'unknown_gender' => $unknownGender,
                'average_age' => $ageCount > 0 ? round($ageTotal / $ageCount, 1) : null,
                'age_distribution' => $ageDistribution,
                'group_jabatan' => $groupCounts,
                'positions' => $positions,
            ],
            'data' => $summaryOnly ? [] : $rows,
        ]);
    }

    private function calculateAge($birthDate, string $asOf): ?int
    {
        if (!$birthDate || str_starts_with((string)$birthDate, '0000-00-00')) {
            return null;
        }

        try {
            $birth = new DateTime((string)$birthDate);
            $reference = new DateTime($asOf);
            if ($birth > $reference) {
                return null;
            }
            return (int)$birth->diff($reference)->y;
        } catch (Throwable $e) {
            return null;
        }
    }
}
