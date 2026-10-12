-- Master AO Dana.
-- Kode grup tabungan/deposito tetap disimpan untuk kebutuhan join transaksi,
-- sedangkan id_peg menjadi identitas AO yang dipakai aplikasi.

CREATE TABLE IF NOT EXISTS ao_dana (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  kode_kantor VARCHAR(10) NOT NULL,
  id_peg VARCHAR(30) NOT NULL,
  nama VARCHAR(150) NOT NULL,
  kode_group2_tab VARCHAR(30) NULL,
  kode_group2_dep VARCHAR(30) NULL,
  status TINYINT(1) NOT NULL DEFAULT 1,
  created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_ao_dana_pegawai (kode_kantor,id_peg),
  KEY idx_ao_dana_kantor_status (kode_kantor,status),
  KEY idx_ao_dana_group_tab (kode_group2_tab),
  KEY idx_ao_dana_group_dep (kode_group2_dep)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed dari master kode grup yang sudah ada dan master users.
-- Nama sumber tidak selalu memiliki gelar, sehingga pencocokan dilakukan
-- terhadap bagian nama sebelum koma dan tanpa spasi.
INSERT INTO ao_dana
  (kode_kantor,id_peg,nama,kode_group2_tab,kode_group2_dep,status,created)
SELECT u.kode,
       u.employee_id,
       TRIM(u.full_name),
       MAX(t.kode_group2),
       MAX(d.kode_group2),
       1,
       NOW()
FROM users u
LEFT JOIN kode_ao_tab t
  ON t.kode_kantor=u.kode
 AND REPLACE(UPPER(t.deskripsi_group2),' ','') =
     REPLACE(UPPER(SUBSTRING_INDEX(u.full_name,',',1)),' ','')
LEFT JOIN kode_ao_dep d
  ON d.kode_kantor=u.kode
 AND REPLACE(UPPER(d.deskripsi_group2),' ','') =
     REPLACE(UPPER(SUBSTRING_INDEX(u.full_name,',',1)),' ','')
WHERE u.employee_id IS NOT NULL
  AND TRIM(u.employee_id) <> ''
  AND LPAD(CAST(u.kode AS CHAR),3,'0') BETWEEN '001' AND '028'
  AND (t.kode_group2 IS NOT NULL OR d.kode_group2 IS NOT NULL)
GROUP BY u.kode,u.employee_id,u.full_name
ON DUPLICATE KEY UPDATE
  nama=VALUES(nama),
  kode_group2_tab=VALUES(kode_group2_tab),
  kode_group2_dep=VALUES(kode_group2_dep),
  status=1;
