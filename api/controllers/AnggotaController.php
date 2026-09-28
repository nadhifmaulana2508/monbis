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
        $asOf = trim((string)($input['as_of'] ?? date('Y-m-d')));

        if ($kodeKantor === '') {
            sendResponse(400, "Parameter 'kode_kantor' wajib diisi.");
        }

        $date = DateTime::createFromFormat('!Y-m-d', $asOf);
        if (!$date || $date->format('Y-m-d') !== $asOf) {
            $asOf = date('Y-m-d');
        }

        $sql = "SELECT
                    k.kode_cabang AS kode,
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
                    mj.group_jabatan
                FROM tb_jabatan j
                INNER JOIN tb_pegawai p
                    ON j.id_peg = p.id_peg
                INNER JOIN tb_master_jabatan mj
                    ON CAST(j.kode_jabatan AS CHAR) = CAST(mj.kode_jabatan AS CHAR)
                LEFT JOIN tb_kantor k
                    ON j.unit_kerja = k.kode_kantor_detail
                WHERE j.status_jab = 'Aktif'
                  AND k.kode_cabang = :kode_cabang
                ORDER BY CASE UPPER(TRIM(COALESCE(mj.group_jabatan, '')))
                           WHEN 'PE' THEN 1
                           WHEN 'PS' THEN 2
                           WHEN 'STAF' THEN 3
                           WHEN 'NON STAF' THEN 4
                           ELSE 5
                         END,
                         p.nama ASC,
                         j.id_peg ASC";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':kode_cabang', $kodeKantor);
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

        foreach ($rows as &$row) {
            $row['age'] = $this->calculateAge($row['tgl_lhr'] ?? null, $asOf);
            $gender = strtoupper(trim((string)($row['kelamin'] ?? '')));
            if (in_array($gender, ['L', 'LAKI-LAKI', 'LAKI LAKI', 'M'], true)) {
                $male++;
            } elseif (in_array($gender, ['P', 'PEREMPUAN', 'W'], true)) {
                $female++;
            } else {
                $unknownGender++;
            }
            if ($row['age'] !== null) {
                $ageTotal += $row['age'];
                $ageCount++;
            }
        }
        unset($row);

        $branchName = $rows[0]['branch_name'] ?? ('Cabang ' . $kodeKantor);

        sendResponse(200, 'Sukses', [
            'meta' => [
                'kode_cabang' => $kodeKantor,
                'branch_name' => $branchName,
                'as_of' => $asOf,
                'total' => count($rows),
                'male' => $male,
                'female' => $female,
                'unknown_gender' => $unknownGender,
                'average_age' => $ageCount > 0 ? round($ageTotal / $ageCount, 1) : null,
            ],
            'data' => $rows,
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
